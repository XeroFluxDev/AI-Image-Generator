<div class="gallery-header">
    <div class="container">
        <h1><i class="fas fa-images"></i> Gallery</h1>
        <p>Browse all your AI-generated projects</p>
    </div>
</div>

<div class="gallery-container">
    <div class="container">
        <div class="gallery-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Search projects..." class="form-control">
            </div>

            <div class="filter-controls">
                <select id="sort-select" class="form-control">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name">Name (A-Z)</option>
                </select>
            </div>

            <a href="/editor" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                New Project
            </a>
        </div>

        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open fa-4x"></i>
                <h2>No projects yet</h2>
                <p>Create your first AI-generated image to get started</p>
                <a href="/editor" class="btn btn-primary btn-lg">
                    <i class="fas fa-magic"></i>
                    Create Your First Project
                </a>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="gallery-item">
                        <div class="gallery-item-image">
                            <?php if (!empty($project['latest_image'])): ?>
                                <img src="<?= htmlspecialchars($project['latest_image']) ?>"
                                     alt="<?= htmlspecialchars($project['name']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="placeholder-image">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            <?php endif; ?>

                            <div class="gallery-item-overlay">
                                <a href="/editor?project=<?= $project['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-sm btn-danger delete-project" data-id="<?= $project['id'] ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>

                        <div class="gallery-item-info">
                            <h3><?= htmlspecialchars($project['name']) ?></h3>
                            <p class="gallery-item-prompt">
                                <?= htmlspecialchars(substr($project['initial_prompt'], 0, 100)) ?>
                                <?= strlen($project['initial_prompt']) > 100 ? '...' : '' ?>
                            </p>
                            <div class="gallery-item-meta">
                                <span><i class="fas fa-layer-group"></i> <?= $project['image_count'] ?> versions</span>
                                <span><i class="fas fa-clock"></i> <?= date('M j, Y', strtotime($project['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Gallery functionality
document.getElementById('search-input')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.gallery-item').forEach(item => {
        const name = item.querySelector('h3').textContent.toLowerCase();
        const prompt = item.querySelector('.gallery-item-prompt').textContent.toLowerCase();
        item.style.display = (name.includes(searchTerm) || prompt.includes(searchTerm)) ? 'block' : 'none';
    });
});

document.getElementById('sort-select')?.addEventListener('change', function(e) {
    // Sort functionality to be implemented
    console.log('Sort by:', e.target.value);
});

document.querySelectorAll('.delete-project').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this project?')) {
            const projectId = this.dataset.id;
            // Delete functionality to be implemented
            console.log('Delete project:', projectId);
        }
    });
});
</script>
