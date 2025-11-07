# Implementation Roadmap
## AI Image Generator & Editor - Step-by-Step Development Guide

This document provides a detailed, actionable roadmap for implementing the AI Image Generator & Editor application.

---

## 🎯 Prerequisites Setup

### Step 0.1: Development Environment
```bash
# Verify PHP version
php -v  # Should be 8.1+

# Verify GD extension
php -m | grep -i gd

# Install Composer if not present
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verify installation
composer --version
```

### Step 0.2: OpenRouter API Account
1. Visit https://openrouter.ai/
2. Sign up for free account
3. Navigate to API Keys section
4. Generate new API key
5. Save securely (will add to .env later)

### Step 0.3: Test API Access
```bash
curl -X POST https://openrouter.ai/api/v1/chat/completions \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "black-forest-labs/flux-1-schnell-free",
    "messages": [{"role": "user", "content": "Hello"}]
  }'
```

---

## 📦 Phase 1: Foundation (Days 1-3)

### Day 1: Project Initialization

#### Task 1.1: Create Directory Structure
```bash
mkdir -p AI-Image-Generator/{public/{assets/{css,js,icons},uploads},src/{Config,Controllers,Models,Services,Views/{layouts},Core},storage/{database,images/{originals,edited,exports},cache}}

cd AI-Image-Generator
```

#### Task 1.2: Initialize Composer
```bash
composer init
```

**composer.json**:
```json
{
    "name": "yourname/ai-image-generator",
    "description": "AI-powered image generation and editing application",
    "type": "project",
    "require": {
        "php": ">=8.1",
        "intervention/image": "^3.0",
        "vlucas/phpdotenv": "^5.5"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

```bash
composer install
```

#### Task 1.3: Environment Configuration
```bash
# Create .env file
cat > .env << 'EOL'
# OpenRouter API
OPENROUTER_API_KEY=your_key_here
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1

# Models
DEFAULT_MODEL=black-forest-labs/flux-1-schnell-free
REFINEMENT_MODEL=stability-ai/stable-diffusion-xl-base-1.0

# App Settings
APP_NAME="AI Image Generator"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_PATH=storage/database/app.db

# Storage
STORAGE_PATH=storage
UPLOAD_PATH=public/uploads
MAX_UPLOAD_SIZE=10485760

# Image Settings
DEFAULT_WIDTH=1024
DEFAULT_HEIGHT=1024
DEFAULT_QUALITY=90
EOL

# Create .env.example (without actual keys)
cp .env .env.example
sed -i 's/your_key_here/sk-or-v1-xxxxx/' .env.example
```

#### Task 1.4: Create .gitignore
```bash
cat > .gitignore << 'EOL'
# Environment
.env

# Dependencies
/vendor/

# Storage
storage/database/*.db
storage/images/*
storage/cache/*
public/uploads/*

# Keep directory structure
!storage/database/.gitkeep
!storage/images/.gitkeep
!storage/cache/.gitkeep
!public/uploads/.gitkeep

# IDE
.vscode/
.idea/
*.sublime-*

# OS
.DS_Store
Thumbs.db

# Logs
*.log
EOL
```

#### Task 1.5: Create .htaccess Files
**public/.htaccess**:
```apache
# Disable directory browsing
Options -Indexes

# Enable URL rewriting
RewriteEngine On

# Redirect to index.php if not a file or directory
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

**public/uploads/.htaccess**:
```apache
# Deny direct access to uploaded files
Order Deny,Allow
Deny from all

# Allow PHP to access
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Allow from all
</FilesMatch>
```

**storage/.htaccess**:
```apache
# Deny all direct access
Deny from all
```

### Day 2: Core MVC Framework

#### Task 2.1: Database Class (src/Core/Database.php)
```php
<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $dbPath = __DIR__ . '/../../' . $_ENV['DB_PATH'];
            $dbDir = dirname($dbPath);

            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            try {
                self::$connection = new PDO("sqlite:$dbPath");
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Initialize schema if needed
                self::initializeSchema();
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

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

        // Images table
        $db->exec("
            CREATE TABLE IF NOT EXISTS images (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                version INTEGER NOT NULL,
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

        // Create indexes
        $db->exec("CREATE INDEX IF NOT EXISTS idx_projects_created ON projects(created_at DESC)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_images_project ON images(project_id, version)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_cache_expires ON api_cache(expires_at)");
    }
}
```

#### Task 2.2: Router Class (src/Core/Router.php)
```php
<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $handler[0],
            'action' => $handler[1]
        ];
    }

    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove trailing slash
        $requestUri = rtrim($requestUri, '/');
        if ($requestUri === '') {
            $requestUri = '/';
        }

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestUri)) {
                $this->callController($route);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }

    private function matchRoute(array $route, string $method, string $uri): bool
    {
        if ($route['method'] !== $method) {
            return false;
        }

        // Simple pattern matching (can be enhanced for parameters)
        $pattern = preg_replace('/\{[^\}]+\}/', '([^/]+)', $route['path']);
        $pattern = "#^" . $pattern . "$#";

        return preg_match($pattern, $uri);
    }

    private function callController(array $route): void
    {
        $controllerClass = $route['controller'];
        $action = $route['action'];

        $controller = new $controllerClass();
        $controller->$action();
    }
}
```

#### Task 2.3: Base Controller (src/Core/Controller.php)
```php
<?php

namespace App\Core;

class Controller
{
    protected function view(string $viewName, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . "/../Views/{$viewName}.php";

        if (!file_exists($viewPath)) {
            die("View not found: {$viewName}");
        }

        require_once __DIR__ . '/../Views/layouts/header.php';
        require_once $viewPath;
        require_once __DIR__ . '/../Views/layouts/footer.php';
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }
}
```

#### Task 2.4: Session Management (src/Core/Session.php)
```php
<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        self::set($key, $value);
        self::set("flash_{$key}", true);
    }

    public static function getFlash(string $key): mixed
    {
        if (self::has("flash_{$key}")) {
            $value = self::get($key);
            self::delete($key);
            self::delete("flash_{$key}");
            return $value;
        }
        return null;
    }
}
```

### Day 3: Entry Point and Basic Views

#### Task 3.1: Front Controller (public/index.php)
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Session;
use App\Core\Database;
use App\Controllers\HomeController;
use App\Controllers\ImageController;
use App\Controllers\ProjectController;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Start session
Session::start();

// Initialize database
Database::connect();

// Create router
$router = new Router();

// Define routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/editor', [HomeController::class, 'editor']);
$router->get('/gallery', [HomeController::class, 'gallery']);

$router->post('/api/generate', [ImageController::class, 'generate']);
$router->post('/api/refine', [ImageController::class, 'refine']);
$router->post('/api/save', [ImageController::class, 'save']);

$router->get('/api/projects', [ProjectController::class, 'list']);
$router->get('/api/projects/{id}', [ProjectController::class, 'show']);
$router->post('/api/projects', [ProjectController::class, 'create']);
$router->post('/api/projects/{id}/delete', [ProjectController::class, 'delete']);

// Dispatch request
$router->dispatch();
```

#### Task 3.2: Layout Header (src/Views/layouts/header.php)
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AI Image Generator' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <i class="fas fa-magic"></i>
                AI Image Generator
            </a>
            <ul class="nav-menu">
                <li><a href="/">Home</a></li>
                <li><a href="/editor">Editor</a></li>
                <li><a href="/gallery">Gallery</a></li>
                <li><a href="#" id="settings-btn"><i class="fas fa-cog"></i></a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">
```

#### Task 3.3: Layout Footer (src/Views/layouts/footer.php)
```php
    </main>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> AI Image Generator. Powered by OpenRouter.</p>
        </div>
    </footer>

    <!-- Load Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>

    <!-- Load application scripts -->
    <script src="/assets/js/utils.js"></script>
    <script src="/assets/js/api-client.js"></script>
    <script src="/assets/js/canvas-editor.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
```

---

## 🎨 Phase 2: Core Generation (Days 4-7)

### Day 4: OpenRouter Service

#### Task 4.1: OpenRouter Service (src/Services/OpenRouterService.php)
```php
<?php

namespace App\Services;

class OpenRouterService
{
    private string $apiKey;
    private string $baseUrl;
    private string $defaultModel;

    public function __construct()
    {
        $this->apiKey = $_ENV['OPENROUTER_API_KEY'];
        $this->baseUrl = $_ENV['OPENROUTER_BASE_URL'];
        $this->defaultModel = $_ENV['DEFAULT_MODEL'];
    }

    public function generateImage(string $prompt, array $options = []): array
    {
        $cacheKey = $this->getCacheKey($prompt, $options);

        // Check cache first
        if ($cached = $this->getFromCache($cacheKey)) {
            return $cached;
        }

        $model = $options['model'] ?? $this->defaultModel;
        $width = $options['width'] ?? (int)$_ENV['DEFAULT_WIDTH'];
        $height = $options['height'] ?? (int)$_ENV['DEFAULT_HEIGHT'];

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            'modalities' => ['image', 'text'],
            'max_tokens' => 1024
        ];

        $response = $this->makeRequest('/chat/completions', $payload);

        if (isset($response['choices'][0]['message']['images'][0])) {
            $result = [
                'success' => true,
                'image' => $response['choices'][0]['message']['images'][0],
                'model' => $model,
                'usage' => $response['usage'] ?? []
            ];

            // Cache successful response
            $this->saveToCache($cacheKey, $result);

            return $result;
        }

        return [
            'success' => false,
            'error' => 'No image generated'
        ];
    }

    public function refineImage(string $imageData, string $prompt, array $options = []): array
    {
        $model = $options['model'] ?? $_ENV['REFINEMENT_MODEL'];

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageData
                            ]
                        ],
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            'modalities' => ['image', 'text']
        ];

        $response = $this->makeRequest('/chat/completions', $payload);

        if (isset($response['choices'][0]['message']['images'][0])) {
            return [
                'success' => true,
                'image' => $response['choices'][0]['message']['images'][0],
                'model' => $model,
                'usage' => $response['usage'] ?? []
            ];
        }

        return [
            'success' => false,
            'error' => 'Refinement failed'
        ];
    }

    private function makeRequest(string $endpoint, array $payload): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'HTTP-Referer: ' . $_ENV['APP_URL'],
                'X-Title: ' . $_ENV['APP_NAME']
            ],
            CURLOPT_TIMEOUT => 60
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL error: {$error}");
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("API request failed with status {$httpCode}");
        }

        return json_decode($response, true);
    }

    private function getCacheKey(string $prompt, array $options): string
    {
        return md5(json_encode([
            'prompt' => $prompt,
            'model' => $options['model'] ?? $this->defaultModel,
            'width' => $options['width'] ?? $_ENV['DEFAULT_WIDTH'],
            'height' => $options['height'] ?? $_ENV['DEFAULT_HEIGHT']
        ]));
    }

    private function getFromCache(string $key): ?array
    {
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("
            SELECT response_data
            FROM api_cache
            WHERE cache_key = ? AND expires_at > datetime('now')
        ");
        $stmt->execute([$key]);
        $result = $stmt->fetch();

        return $result ? json_decode($result['response_data'], true) : null;
    }

    private function saveToCache(string $key, array $data): void
    {
        $db = \App\Core\Database::connect();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $db->prepare("
            INSERT OR REPLACE INTO api_cache (cache_key, response_data, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$key, json_encode($data), $expiresAt]);
    }
}
```

---

## ⏭️ Continued in Next Phases...

This roadmap continues with:
- **Phase 3**: Canvas Editor Implementation (Days 8-12)
- **Phase 4**: Iterative Refinement System (Days 13-16)
- **Phase 5**: Project Management (Days 17-20)
- **Phase 6**: Polish & Optimization (Days 21-24)
- **Phase 7**: Testing & Deployment (Days 25-28)

**Next Steps**:
1. Complete Phase 1 implementation
2. Test each component thoroughly
3. Proceed to Phase 2
4. Refer to FEATURE_PLAN.md for detailed specifications

---

**Document Version**: 1.0
**Last Updated**: November 7, 2025
**Status**: Phase 1-2 Detailed, Phases 3-7 in FEATURE_PLAN.md
