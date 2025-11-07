<?php

namespace App\Core;

/**
 * Session Management Class
 *
 * Provides secure session handling with flash messages support
 */
class Session
{
    private static bool $started = false;

    /**
     * Start the session
     *
     * @return void
     */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Configure session security
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_samesite', 'Lax');

        // Set session name from environment
        $sessionName = $_ENV['SESSION_NAME'] ?? 'ai_image_gen_session';
        session_name($sessionName);

        // Set session lifetime from environment
        $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);
        ini_set('session.gc_maxlifetime', (string)$lifetime);

        // Start session
        session_start();

        self::$started = true;

        // Process flash messages
        self::processFlashMessages();
    }

    /**
     * Set a session variable
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable
     *
     * @param string $key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session variable exists
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Delete a session variable
     *
     * @param string $key
     * @return void
     */
    public static function delete(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Get all session data
     *
     * @return array
     */
    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    /**
     * Clear all session data
     *
     * @return void
     */
    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
    }

    /**
     * Destroy the session
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Regenerate session ID
     *
     * @param bool $deleteOldSession
     * @return void
     */
    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::start();
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Set a flash message
     *
     * Flash messages are available for one request only
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function flash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION["_flash_{$key}"] = $value;
        $_SESSION["_flash_{$key}_new"] = true;
    }

    /**
     * Get a flash message
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION["_flash_{$key}"] ?? $default;
    }

    /**
     * Check if flash message exists
     *
     * @param string $key
     * @return bool
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION["_flash_{$key}"]);
    }

    /**
     * Process flash messages
     *
     * Marks new flash messages as old and removes old ones
     *
     * @return void
     */
    private static function processFlashMessages(): void
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, '_flash_') === 0 && !str_ends_with($key, '_new')) {
                // Check if this is a new flash message
                $newKey = "{$key}_new";
                if (isset($_SESSION[$newKey])) {
                    // Mark as old
                    unset($_SESSION[$newKey]);
                } else {
                    // Remove old flash message
                    unset($_SESSION[$key]);
                }
            }
        }
    }

    /**
     * Store current project in session
     *
     * @param int $projectId
     * @return void
     */
    public static function setCurrentProject(int $projectId): void
    {
        self::set('current_project_id', $projectId);
    }

    /**
     * Get current project from session
     *
     * @return int|null
     */
    public static function getCurrentProject(): ?int
    {
        return self::get('current_project_id');
    }

    /**
     * Store editor state in session
     *
     * @param array $state
     * @return void
     */
    public static function setEditorState(array $state): void
    {
        self::set('editor_state', $state);
    }

    /**
     * Get editor state from session
     *
     * @return array|null
     */
    public static function getEditorState(): ?array
    {
        return self::get('editor_state');
    }

    /**
     * Clear editor state
     *
     * @return void
     */
    public static function clearEditorState(): void
    {
        self::delete('editor_state');
    }
}
