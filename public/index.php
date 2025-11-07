<?php

/**
 * Front Controller
 *
 * Entry point for all requests
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\Database;
use App\Controllers\HomeController;
use App\Controllers\ImageController;
use App\Controllers\ProjectController;
use Dotenv\Dotenv;

// Error reporting (based on environment)
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    // Load environment variables
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    // Set error display based on environment
    if ($_ENV['APP_DEBUG'] === 'false' || $_ENV['APP_ENV'] === 'production') {
        ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    }

    // Start session
    Session::start();

    // Initialize database connection
    Database::connect();

    // Create router instance
    $router = new Router();

    // ===================================
    // Define Routes
    // ===================================

    // --- Web Routes ---
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/editor', [HomeController::class, 'editor']);
    $router->get('/gallery', [HomeController::class, 'gallery']);
    $router->get('/about', [HomeController::class, 'about']);

    // --- API Routes - Image Generation ---
    $router->post('/api/generate', [ImageController::class, 'generate']);
    $router->post('/api/refine', [ImageController::class, 'refine']);
    $router->post('/api/save-image', [ImageController::class, 'saveImage']);
    $router->post('/api/upload', [ImageController::class, 'upload']);

    // --- API Routes - Project Management ---
    $router->get('/api/projects', [ProjectController::class, 'list']);
    $router->get('/api/projects/{id}', [ProjectController::class, 'show']);
    $router->post('/api/projects', [ProjectController::class, 'create']);
    $router->post('/api/projects/{id}/update', [ProjectController::class, 'update']);
    $router->post('/api/projects/{id}/delete', [ProjectController::class, 'delete']);

    // --- API Routes - History/Versions ---
    $router->get('/api/projects/{id}/history', [ProjectController::class, 'history']);
    $router->post('/api/projects/{id}/revert/{version}', [ProjectController::class, 'revert']);

    // ===================================
    // Dispatch Request
    // ===================================
    $router->dispatch();

} catch (Exception $e) {
    // Log error
    error_log("Application Error: " . $e->getMessage());

    // Show user-friendly error
    if ($_ENV['APP_DEBUG'] === 'true' && $_ENV['APP_ENV'] !== 'production') {
        // Development: Show detailed error
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Application Error</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; padding: 40px; background: #f5f5f5; }
        .error-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto; }
        h1 { color: #dc3545; margin: 0 0 20px 0; }
        .error-message { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545; }
        .error-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; overflow-x: auto; }
        pre { margin: 0; white-space: pre-wrap; }
        .stack-trace { font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class='error-container'>
        <h1>⚠️ Application Error</h1>
        <div class='error-message'>
            <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "
        </div>
        <div class='error-details'>
            <strong>File:</strong> " . htmlspecialchars($e->getFile()) . " <br>
            <strong>Line:</strong> " . $e->getLine() . "
        </div>
        <details>
            <summary style='cursor: pointer; padding: 10px; background: #e9ecef; border-radius: 5px; margin: 10px 0;'>
                <strong>Stack Trace</strong>
            </summary>
            <div class='error-details stack-trace'>
                <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
            </div>
        </details>
    </div>
</body>
</html>";
    } else {
        // Production: Show generic error
        http_response_code(500);
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Server Error</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        h1 { font-size: 72px; margin: 0; color: #dc3545; }
        p { font-size: 18px; color: #666; margin: 20px 0; }
        a { color: #007bff; text-decoration: none; padding: 10px 20px; background: white; border-radius: 5px; display: inline-block; }
        a:hover { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>500</h1>
    <p>Something went wrong on our end.</p>
    <p>We're working to fix it. Please try again later.</p>
    <a href='/'>Return to Home</a>
</body>
</html>";
    }
}
