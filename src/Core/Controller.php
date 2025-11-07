<?php

namespace App\Core;

/**
 * Base Controller Class
 *
 * Provides common functionality for all controllers
 */
class Controller
{
    /**
     * Render a view with data
     *
     * @param string $viewName View file name (without .php extension)
     * @param array $data Data to pass to the view
     * @param bool $useLayout Whether to include header/footer layout
     * @return void
     */
    protected function view(string $viewName, array $data = [], bool $useLayout = true): void
    {
        // Extract data array to individual variables
        extract($data);

        // Set default title if not provided
        if (!isset($title)) {
            $title = $_ENV['APP_NAME'] ?? 'AI Image Generator';
        }

        $viewPath = __DIR__ . "/../Views/{$viewName}.php";

        if (!file_exists($viewPath)) {
            $this->handleError("View not found: {$viewName}");
            return;
        }

        // Include layout header if needed
        if ($useLayout) {
            require_once __DIR__ . '/../Views/layouts/header.php';
        }

        // Include the view
        require_once $viewPath;

        // Include layout footer if needed
        if ($useLayout) {
            require_once __DIR__ . '/../Views/layouts/footer.php';
        }
    }

    /**
     * Return JSON response
     *
     * @param mixed $data Data to encode as JSON
     * @param int $status HTTP status code
     * @return void
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Return success JSON response
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @return void
     */
    protected function jsonSuccess(mixed $data = null, string $message = 'Success'): void
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->json($response);
    }

    /**
     * Return error JSON response
     *
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param mixed $errors Additional error details
     * @return void
     */
    protected function jsonError(string $message, int $status = 400, mixed $errors = null): void
    {
        $response = [
            'success' => false,
            'error' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        $this->json($response, $status);
    }

    /**
     * Redirect to another URL
     *
     * @param string $path Path to redirect to
     * @param int $status HTTP status code for redirect
     * @return void
     */
    protected function redirect(string $path, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$path}");
        exit;
    }

    /**
     * Get request input data
     *
     * @param string|null $key Specific key to retrieve, or null for all data
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    protected function input(?string $key = null, mixed $default = null): mixed
    {
        $data = [];

        // Parse JSON body for API requests
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?? [];
        } else {
            // Merge GET and POST data
            $data = array_merge($_GET, $_POST);
        }

        // Return specific key or all data
        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    /**
     * Get uploaded file
     *
     * @param string $key File input name
     * @return array|null File data or null if not found
     */
    protected function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Validate required fields
     *
     * @param array $fields Array of required field names
     * @param array $data Data to validate
     * @return array Empty array if valid, or array of missing fields
     */
    protected function validate(array $fields, ?array $data = null): array
    {
        $data = $data ?? $this->input();
        $missing = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    /**
     * Sanitize string input
     *
     * @param string $input
     * @return string
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Check if request is AJAX/API call
     *
     * @return bool
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Handle error (for development)
     *
     * @param string $message
     * @return void
     */
    private function handleError(string $message): void
    {
        if ($_ENV['APP_DEBUG'] === 'true') {
            echo "<pre style='background: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
            echo "<strong>Error:</strong> " . htmlspecialchars($message);
            echo "</pre>";
        } else {
            echo "An error occurred. Please contact support.";
        }
        exit;
    }
}
