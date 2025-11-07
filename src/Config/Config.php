<?php

namespace App\Config;

/**
 * Configuration Helper Class
 *
 * Provides easy access to environment variables with type casting
 */
class Config
{
    /**
     * Get configuration value
     *
     * @param string $key Configuration key (environment variable name)
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $default;

        // Type casting for common values
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }
        if ($value === 'null') {
            return null;
        }

        return $value;
    }

    /**
     * Get string configuration value
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function getString(string $key, string $default = ''): string
    {
        return (string) self::get($key, $default);
    }

    /**
     * Get integer configuration value
     *
     * @param string $key
     * @param int $default
     * @return int
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    /**
     * Get boolean configuration value
     *
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get float configuration value
     *
     * @param string $key
     * @param float $default
     * @return float
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) self::get($key, $default);
    }

    /**
     * Check if configuration key exists
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_ENV[$key]);
    }

    /**
     * Get application name
     *
     * @return string
     */
    public static function appName(): string
    {
        return self::getString('APP_NAME', 'AI Image Generator');
    }

    /**
     * Get application environment
     *
     * @return string
     */
    public static function appEnv(): string
    {
        return self::getString('APP_ENV', 'production');
    }

    /**
     * Check if application is in debug mode
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return self::getBool('APP_DEBUG', false);
    }

    /**
     * Get application URL
     *
     * @return string
     */
    public static function appUrl(): string
    {
        return self::getString('APP_URL', 'http://localhost:8000');
    }

    /**
     * Get database path
     *
     * @return string
     */
    public static function dbPath(): string
    {
        return self::getString('DB_PATH', 'storage/database/app.db');
    }

    /**
     * Get OpenRouter API key
     *
     * @return string
     */
    public static function openRouterApiKey(): string
    {
        return self::getString('OPENROUTER_API_KEY', '');
    }

    /**
     * Get OpenRouter base URL
     *
     * @return string
     */
    public static function openRouterBaseUrl(): string
    {
        return self::getString('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
    }

    /**
     * Get default AI model
     *
     * @return string
     */
    public static function defaultModel(): string
    {
        return self::getString('DEFAULT_MODEL', 'black-forest-labs/flux-1-schnell-free');
    }

    /**
     * Get refinement AI model
     *
     * @return string
     */
    public static function refinementModel(): string
    {
        return self::getString('REFINEMENT_MODEL', 'stability-ai/stable-diffusion-xl-base-1.0');
    }

    /**
     * Get default image width
     *
     * @return int
     */
    public static function defaultWidth(): int
    {
        return self::getInt('DEFAULT_WIDTH', 1024);
    }

    /**
     * Get default image height
     *
     * @return int
     */
    public static function defaultHeight(): int
    {
        return self::getInt('DEFAULT_HEIGHT', 1024);
    }

    /**
     * Get default image quality
     *
     * @return int
     */
    public static function defaultQuality(): int
    {
        return self::getInt('DEFAULT_QUALITY', 90);
    }

    /**
     * Get maximum upload size in bytes
     *
     * @return int
     */
    public static function maxUploadSize(): int
    {
        return self::getInt('MAX_UPLOAD_SIZE', 10485760); // 10MB default
    }

    /**
     * Get storage path
     *
     * @return string
     */
    public static function storagePath(): string
    {
        return self::getString('STORAGE_PATH', 'storage');
    }

    /**
     * Get upload path
     *
     * @return string
     */
    public static function uploadPath(): string
    {
        return self::getString('UPLOAD_PATH', 'public/uploads');
    }

    /**
     * Check if caching is enabled
     *
     * @return bool
     */
    public static function cacheEnabled(): bool
    {
        return self::getBool('CACHE_ENABLED', true);
    }

    /**
     * Get cache TTL in seconds
     *
     * @return int
     */
    public static function cacheTTL(): int
    {
        return self::getInt('CACHE_TTL', 3600); // 1 hour default
    }

    /**
     * Get all configuration as array
     *
     * @return array
     */
    public static function all(): array
    {
        return $_ENV;
    }
}
