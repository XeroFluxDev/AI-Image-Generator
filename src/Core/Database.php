<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Class
 *
 * Manages SQLite database connection and schema initialization
 */
class Database
{
    private static ?PDO $connection = null;

    /**
     * Get database connection (Singleton pattern)
     *
     * @return PDO
     */
    public static function connect(): ?PDO
    {
        if (self::$connection === null) {
            $dbPath = __DIR__ . '/../../' . $_ENV['DB_PATH'];
            $dbDir = dirname($dbPath);

            // Create database directory if it doesn't exist
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            try {
                // Check if PDO SQLite driver is available
                if (!extension_loaded('pdo_sqlite')) {
                    error_log("SQLite PDO driver not available. Database features disabled.");
                    return null;
                }

                self::$connection = new PDO("sqlite:$dbPath");
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Enable foreign keys
                self::$connection->exec('PRAGMA foreign_keys = ON');

                // Initialize schema if needed
                self::initializeSchema();
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                return null;
            }
        }

        return self::$connection;
    }

    /**
     * Initialize database schema
     *
     * @return void
     */
    private static function initializeSchema(): void
    {
        $db = self::$connection;

        // Projects table
        $db->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                initial_prompt TEXT NOT NULL,
                settings TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Images table (version history)
        $db->exec("
            CREATE TABLE IF NOT EXISTS images (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                version INTEGER NOT NULL DEFAULT 1,
                image_path TEXT NOT NULL,
                thumbnail_path TEXT,
                prompt TEXT,
                refinement_prompt TEXT,
                metadata TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
            )
        ");

        // API cache table
        $db->exec("
            CREATE TABLE IF NOT EXISTS api_cache (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cache_key TEXT UNIQUE NOT NULL,
                response_data TEXT NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Settings table
        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                key TEXT PRIMARY KEY,
                value TEXT NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Create indexes for better performance
        $db->exec("CREATE INDEX IF NOT EXISTS idx_projects_created ON projects(created_at DESC)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_images_project ON images(project_id, version)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_cache_key ON api_cache(cache_key)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_cache_expires ON api_cache(expires_at)");
    }

    /**
     * Clean expired cache entries
     *
     * @return int Number of deleted entries
     */
    public static function cleanExpiredCache(): int
    {
        $db = self::connect();
        $stmt = $db->prepare("DELETE FROM api_cache WHERE expires_at < datetime('now')");
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Get database statistics
     *
     * @return array
     */
    public static function getStats(): array
    {
        $db = self::connect();

        if ($db === null) {
            return [
                'projects' => 0,
                'images' => 0,
                'cached_requests' => 0
            ];
        }

        $projectCount = $db->query("SELECT COUNT(*) as count FROM projects")->fetch()['count'];
        $imageCount = $db->query("SELECT COUNT(*) as count FROM images")->fetch()['count'];
        $cacheCount = $db->query("SELECT COUNT(*) as count FROM api_cache WHERE expires_at > datetime('now')")->fetch()['count'];

        return [
            'projects' => $projectCount,
            'images' => $imageCount,
            'cached_requests' => $cacheCount
        ];
    }

    /**
     * Close database connection
     *
     * @return void
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
