<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI-powered image generation and editing using OpenRouter API">
    <title><?= htmlspecialchars($title ?? 'AI Image Generator') ?></title>

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Favicon (placeholder) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎨</text></svg>">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <i class="fas fa-wand-magic-sparkles"></i>
                <span>AI Image Generator</span>
            </a>

            <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-menu" id="nav-menu">
                <li><a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' ? 'active' : '') ?>">
                    <i class="fas fa-home"></i> Home
                </a></li>
                <li><a href="/editor" class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/editor') ? 'active' : '') ?>">
                    <i class="fas fa-palette"></i> Editor
                </a></li>
                <li><a href="/gallery" class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/gallery') ? 'active' : '') ?>">
                    <i class="fas fa-images"></i> Gallery
                </a></li>
                <li><a href="/about" class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/about') ? 'active' : '') ?>">
                    <i class="fas fa-info-circle"></i> About
                </a></li>
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php
    use App\Core\Session;

    if (Session::hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars(Session::getFlash('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-error" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars(Session::getFlash('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('info')): ?>
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i>
            <?= htmlspecialchars(Session::getFlash('info')) ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
