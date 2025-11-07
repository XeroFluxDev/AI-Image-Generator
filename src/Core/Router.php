<?php

namespace App\Core;

/**
 * Router Class
 *
 * Handles HTTP routing and dispatches requests to appropriate controllers
 */
class Router
{
    private array $routes = [];
    private array $params = [];

    /**
     * Register a GET route
     *
     * @param string $path
     * @param array $handler [ControllerClass::class, 'methodName']
     * @return void
     */
    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route
     *
     * @param string $path
     * @param array $handler [ControllerClass::class, 'methodName']
     * @return void
     */
    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Register a PUT route
     *
     * @param string $path
     * @param array $handler [ControllerClass::class, 'methodName']
     * @return void
     */
    public function put(string $path, array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Register a DELETE route
     *
     * @param string $path
     * @param array $handler [ControllerClass::class, 'methodName']
     * @return void
     */
    public function delete(string $path, array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add a route to the routes array
     *
     * @param string $method
     * @param string $path
     * @param array $handler
     * @return void
     */
    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $handler[0],
            'action' => $handler[1]
        ];
    }

    /**
     * Dispatch the current request
     *
     * @return void
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $this->getRequestUri();

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestUri)) {
                $this->callController($route);
                return;
            }
        }

        // 404 Not Found
        $this->handleNotFound();
    }

    /**
     * Get the clean request URI
     *
     * @return string
     */
    private function getRequestUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove trailing slash
        $uri = rtrim($uri, '/');

        // Handle root path
        if ($uri === '') {
            $uri = '/';
        }

        return $uri;
    }

    /**
     * Match a route against the request
     *
     * @param array $route
     * @param string $method
     * @param string $uri
     * @return bool
     */
    private function matchRoute(array $route, string $method, string $uri): bool
    {
        // Check HTTP method
        if ($route['method'] !== $method) {
            return false;
        }

        // Convert route path to regex pattern
        $pattern = $this->convertToRegex($route['path']);

        // Match URI against pattern
        if (preg_match($pattern, $uri, $matches)) {
            // Extract named parameters
            array_shift($matches); // Remove full match
            $this->params = $matches;
            return true;
        }

        return false;
    }

    /**
     * Convert route path with placeholders to regex
     *
     * @param string $path
     * @return string
     */
    private function convertToRegex(string $path): string
    {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);

        // Replace {param} with named capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^\/]+)', $pattern);

        // Return full regex pattern
        return '/^' . $pattern . '$/';
    }

    /**
     * Call the controller action
     *
     * @param array $route
     * @return void
     */
    private function callController(array $route): void
    {
        $controllerClass = $route['controller'];
        $action = $route['action'];

        // Instantiate controller
        $controller = new $controllerClass();

        // Call action with parameters
        call_user_func_array([$controller, $action], $this->params);
    }

    /**
     * Handle 404 Not Found
     *
     * @return void
     */
    private function handleNotFound(): void
    {
        http_response_code(404);

        // Check if it's an API request
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Endpoint not found'
            ]);
        } else {
            // Return simple HTML 404 page
            echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: system-ui; text-align: center; padding: 50px; background: #f5f5f5; }
        h1 { font-size: 72px; margin: 0; color: #333; }
        p { font-size: 18px; color: #666; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Page not found</p>
    <p><a href="/">Return to Home</a></p>
</body>
</html>';
        }

        exit;
    }

    /**
     * Get route parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
