<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

/**
 * Project Controller
 *
 * Handles project management operations
 */
class ProjectController extends Controller
{
    /**
     * List all projects
     *
     * @return void
     */
    public function list(): void
    {
        $db = Database::connect();

        $stmt = $db->query("
            SELECT
                p.*,
                COUNT(i.id) as image_count,
                MAX(i.created_at) as last_generated
            FROM projects p
            LEFT JOIN images i ON p.id = i.project_id
            GROUP BY p.id
            ORDER BY p.updated_at DESC
        ");

        $projects = $stmt->fetchAll();

        $this->jsonSuccess($projects);
    }

    /**
     * Show single project with all versions
     *
     * @param int $id Project ID
     * @return void
     */
    public function show(int $id): void
    {
        $db = Database::connect();

        // Get project details
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if (!$project) {
            $this->jsonError('Project not found', 404);
            return;
        }

        // Get all images for this project
        $stmt = $db->prepare("
            SELECT * FROM images
            WHERE project_id = ?
            ORDER BY version ASC
        ");
        $stmt->execute([$id]);
        $images = $stmt->fetchAll();

        $project['images'] = $images;

        $this->jsonSuccess($project);
    }

    /**
     * Create new project
     *
     * @return void
     */
    public function create(): void
    {
        // Validate required fields
        $missing = $this->validate(['name', 'initial_prompt']);
        if (!empty($missing)) {
            $this->jsonError('Missing required fields: ' . implode(', ', $missing), 400);
            return;
        }

        $name = $this->sanitize($this->input('name'));
        $initialPrompt = $this->sanitize($this->input('initial_prompt'));
        $settings = $this->input('settings', '{}');

        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO projects (name, initial_prompt, settings)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$name, $initialPrompt, $settings]);
        $projectId = $db->lastInsertId();

        $this->jsonSuccess([
            'id' => $projectId,
            'name' => $name,
            'initial_prompt' => $initialPrompt
        ], 'Project created successfully');
    }

    /**
     * Update project
     *
     * @param int $id Project ID
     * @return void
     */
    public function update(int $id): void
    {
        $db = Database::connect();

        // Check if project exists
        $stmt = $db->prepare("SELECT id FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            $this->jsonError('Project not found', 404);
            return;
        }

        $name = $this->input('name');
        $settings = $this->input('settings');

        $updates = [];
        $params = [];

        if ($name !== null) {
            $updates[] = "name = ?";
            $params[] = $this->sanitize($name);
        }

        if ($settings !== null) {
            $updates[] = "settings = ?";
            $params[] = $settings;
        }

        if (empty($updates)) {
            $this->jsonError('No fields to update', 400);
            return;
        }

        $updates[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $sql = "UPDATE projects SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $this->jsonSuccess(['id' => $id], 'Project updated successfully');
    }

    /**
     * Delete project
     *
     * @param int $id Project ID
     * @return void
     */
    public function delete(int $id): void
    {
        $db = Database::connect();

        // Check if project exists
        $stmt = $db->prepare("SELECT id FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            $this->jsonError('Project not found', 404);
            return;
        }

        // Delete project (cascades to images)
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);

        // TODO: Delete physical image files in Phase 5

        $this->jsonSuccess(['id' => $id], 'Project deleted successfully');
    }

    /**
     * Get project version history
     *
     * @param int $id Project ID
     * @return void
     */
    public function history(int $id): void
    {
        $db = Database::connect();

        // Check if project exists
        $stmt = $db->prepare("SELECT id, name FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if (!$project) {
            $this->jsonError('Project not found', 404);
            return;
        }

        // Get all versions
        $stmt = $db->prepare("
            SELECT * FROM images
            WHERE project_id = ?
            ORDER BY version DESC
        ");
        $stmt->execute([$id]);
        $history = $stmt->fetchAll();

        $this->jsonSuccess([
            'project' => $project,
            'history' => $history
        ]);
    }

    /**
     * Revert to a previous version
     *
     * @param int $id Project ID
     * @param int $version Version number to revert to
     * @return void
     */
    public function revert(int $id, int $version): void
    {
        $db = Database::connect();

        // Check if version exists
        $stmt = $db->prepare("
            SELECT * FROM images
            WHERE project_id = ? AND version = ?
        ");
        $stmt->execute([$id, $version]);
        $image = $stmt->fetch();

        if (!$image) {
            $this->jsonError('Version not found', 404);
            return;
        }

        // Get max version number
        $stmt = $db->prepare("
            SELECT MAX(version) as max_version
            FROM images
            WHERE project_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $newVersion = ($result['max_version'] ?? 0) + 1;

        // Create new version with reverted data
        $stmt = $db->prepare("
            INSERT INTO images (project_id, version, image_path, thumbnail_path, prompt, refinement_prompt, metadata)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $id,
            $newVersion,
            $image['image_path'],
            $image['thumbnail_path'],
            $image['prompt'],
            'Reverted to version ' . $version,
            $image['metadata']
        ]);

        $this->jsonSuccess([
            'project_id' => $id,
            'old_version' => $version,
            'new_version' => $newVersion
        ], 'Successfully reverted to version ' . $version);
    }
}
