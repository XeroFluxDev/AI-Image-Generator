    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>
                        <i class="fas fa-wand-magic-sparkles"></i>
                        AI Image Generator
                    </h3>
                    <p>Create and refine images using AI-powered generation</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/editor">Editor</a></li>
                        <li><a href="/gallery">Gallery</a></li>
                        <li><a href="/about">About</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="https://openrouter.ai/" target="_blank" rel="noopener">OpenRouter</a></li>
                        <li><a href="https://github.com/yourusername/ai-image-generator" target="_blank" rel="noopener">GitHub</a></li>
                        <li><a href="https://image.intervention.io/" target="_blank" rel="noopener">Intervention Image</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Connect</h4>
                    <div class="social-links">
                        <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> AI Image Generator. Powered by OpenRouter.</p>
                <p>Built with <i class="fas fa-heart" style="color: #e74c3c;"></i> using Vanilla PHP</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Fabric.js for canvas editing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js" integrity="sha512-CeIsOAsgJnmevfCi2C7Zsyy6bQKi43utIjdA87Q0ZY84oDqnI0uwfM9+bKiIkI75lUeI00WG/+uJzOmuHlesMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Application scripts -->
    <script src="/assets/js/utils.js"></script>
    <script src="/assets/js/api-client.js"></script>
    <script src="/assets/js/canvas-editor.js"></script>
    <script src="/assets/js/app.js"></script>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
            document.getElementById('nav-menu')?.classList.toggle('active');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
