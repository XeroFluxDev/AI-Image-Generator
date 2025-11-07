<div class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-magic"></i>
                Create Amazing Images with AI
            </h1>
            <p class="hero-subtitle">
                Generate, edit, and refine images using powerful AI models. Iterate until perfect.
            </p>
            <div class="hero-actions">
                <a href="/editor" class="btn btn-primary btn-lg">
                    <i class="fas fa-palette"></i>
                    Start Creating
                </a>
                <a href="/gallery" class="btn btn-secondary btn-lg">
                    <i class="fas fa-images"></i>
                    View Gallery
                </a>
            </div>
        </div>
    </div>
</div>

<section class="features">
    <div class="container">
        <h2 class="section-title">Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </div>
                <h3>AI-Powered Generation</h3>
                <p>Create images from text descriptions using state-of-the-art AI models via OpenRouter</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h3>Visual Editor</h3>
                <p>Edit generated images with intuitive canvas tools - draw, add text, apply filters</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3>Iterative Refinement</h3>
                <p>Refine your images through multiple AI iterations until they're exactly what you want</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Version History</h3>
                <p>Track all iterations with complete history. Compare, revert, and branch from any version</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3>Layer Management</h3>
                <p>Work with multiple layers to preserve your original while experimenting</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-download"></i>
                </div>
                <h3>Export Options</h3>
                <p>Save your creations in multiple formats - PNG, JPEG, WebP with quality control</p>
            </div>
        </div>
    </div>
</section>

<section class="workflow">
    <div class="container">
        <h2 class="section-title">How It Works</h2>
        <div class="workflow-steps">
            <div class="workflow-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3><i class="fas fa-keyboard"></i> Enter Prompt</h3>
                    <p>Describe the image you want to create</p>
                </div>
            </div>

            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>

            <div class="workflow-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3><i class="fas fa-robot"></i> AI Generates</h3>
                    <p>OpenRouter creates your image</p>
                </div>
            </div>

            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>

            <div class="workflow-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3><i class="fas fa-edit"></i> Edit & Refine</h3>
                    <p>Make adjustments with visual tools</p>
                </div>
            </div>

            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>

            <div class="workflow-step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3><i class="fas fa-check-circle"></i> Save Result</h3>
                    <p>Download or continue iterating</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['projects'] ?? 0) ?></div>
                <div class="stat-label">Projects Created</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['images'] ?? 0) ?></div>
                <div class="stat-label">Images Generated</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['cached_requests'] ?? 0) ?></div>
                <div class="stat-label">Cached Requests</div>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Create?</h2>
            <p>Start generating amazing images with AI today</p>
            <a href="/editor" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket"></i>
                Get Started Now
            </a>
        </div>
    </div>
</section>
