<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

/**
 * Home Controller
 *
 * Handles main web pages
 */
class HomeController extends Controller
{
    /**
     * Home/Landing page
     *
     * @return void
     */
    public function index(): void
    {
        $stats = Database::getStats();

        $this->view('home', [
            'title' => 'AI Image Generator - Home',
            'stats' => $stats
        ]);
    }

    /**
     * Image Editor page
     *
     * @return void
     */
    public function editor(): void
    {
        $this->view('editor', [
            'title' => 'Image Editor - AI Image Generator'
        ]);
    }

    /**
     * Gallery page
     *
     * @return void
     */
    public function gallery(): void
    {
        $db = Database::connect();
        $projects = [];

        if ($db !== null) {
            // Get all projects with their latest images
            $stmt = $db->query("
                SELECT
                    p.*,
                    i.image_path as latest_image,
                    i.thumbnail_path,
                    (SELECT COUNT(*) FROM images WHERE project_id = p.id) as image_count
                FROM projects p
                LEFT JOIN images i ON p.id = i.project_id
                WHERE i.version = (
                    SELECT MAX(version) FROM images WHERE project_id = p.id
                )
                ORDER BY p.updated_at DESC
            ");

            $projects = $stmt->fetchAll();
        }

        $this->view('gallery', [
            'title' => 'Gallery - AI Image Generator',
            'projects' => $projects
        ]);
    }

    /**
     * About page
     *
     * @return void
     */
    public function about(): void
    {
        $this->view('about', [
            'title' => 'About - AI Image Generator'
        ]);
    }
}
