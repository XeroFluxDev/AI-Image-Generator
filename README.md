# 🎨 AI Image Generator & Editor

A powerful vanilla PHP application for AI-powered image generation and iterative editing using the OpenRouter API. Generate images from text prompts, refine them with built-in editing tools, and iterate until perfect.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-purple.svg)
![Status](https://img.shields.io/badge/status-in%20development-yellow.svg)

## ✨ Features

- **AI-Powered Generation**: Create images from text descriptions using OpenRouter API
- **Visual Editor**: Edit images with Fabric.js canvas (drawing, shapes, text, filters)
- **Iterative Refinement**: Continuously improve images through AI regeneration
- **Version History**: Track all iterations with compare and revert capabilities
- **Project Management**: Save, load, and organize your image projects
- **Smart Caching**: Reduce API costs with intelligent request caching
- **Export Options**: Save in multiple formats (PNG, JPEG, WebP)

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- GD Library (usually included with PHP)
- OpenRouter API key ([Get one here](https://openrouter.ai/))

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/AI-Image-Generator.git
cd AI-Image-Generator

# Install PHP dependencies
composer install

# Copy environment configuration
cp .env.example .env

# Edit .env and add your OpenRouter API key
nano .env

# Set up permissions (Linux/Mac)
chmod -R 755 storage/
chmod -R 755 public/uploads/

# Start development server
php -S localhost:8000 -t public
```

Visit `http://localhost:8000` in your browser.

## 🎯 How It Works

### The Iterative Workflow

```
1. Enter Prompt → 2. Generate Image → 3. Review Result
                                            ↓
                        ← 5. Refine Again ← 4. Edit with Tools
                                            ↓
                                       6. Save/Export
```

### Example Use Case

1. **Initial Generation**: "A serene mountain landscape at sunset"
2. **AI Creates**: Base image generated
3. **Edit**: Add text overlay "Adventure Awaits", adjust brightness
4. **Refine**: "Make the sunset colors more dramatic"
5. **AI Refines**: Enhanced version created
6. **Save**: Export final image as PNG

## 📖 Usage Guide

### Generating Your First Image

1. Open the application
2. Enter a descriptive prompt in the text field
3. Select size and style preferences
4. Click "Generate"
5. Wait for the AI to create your image

### Editing Generated Images

**Available Tools**:
- **Brush**: Draw freehand on the canvas
- **Shapes**: Add rectangles, circles, lines, arrows
- **Text**: Add text overlays with custom fonts
- **Crop**: Trim image to desired dimensions
- **Rotate**: Rotate 90°, 180°, 270°, or free angle
- **Filters**: Adjust brightness, contrast, saturation, blur

### Refining with AI

1. After editing, enter a refinement prompt describing desired changes
2. Click "Refine" to send the edited image back to AI
3. The AI will generate a new version based on your edits and prompt
4. Compare versions in the history panel
5. Continue iterating until satisfied

### Managing Projects

- **Save**: Preserve your work with all version history
- **Load**: Resume previous projects
- **Gallery**: Browse all your creations
- **Export**: Download final images in your preferred format

## 🛠️ Configuration

### Environment Variables

Edit `.env` file:

```env
# OpenRouter API Configuration
OPENROUTER_API_KEY=your_api_key_here
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1

# Default Settings
DEFAULT_MODEL=black-forest-labs/flux-1-schnell-free
DEFAULT_IMAGE_WIDTH=1024
DEFAULT_IMAGE_HEIGHT=1024
DEFAULT_QUALITY=90

# Application Settings
APP_NAME="AI Image Generator"
APP_ENV=development
APP_DEBUG=true

# Storage Paths
STORAGE_PATH=storage
UPLOAD_PATH=public/uploads
MAX_UPLOAD_SIZE=10485760
```

### Recommended Models

**Free Options**:
- `black-forest-labs/flux-1-schnell-free` - Fast, good quality
- `google/gemini-2.0-flash-thinking-exp-01-21:free` - Experimental

**Paid Options** (Better Quality):
- `stability-ai/stable-diffusion-xl-base-1.0` - High quality, slower
- `openai/dall-e-3` - Premium quality
- `midjourney/v6` - Artistic style

## 📁 Project Structure

```
AI-Image-Generator/
├── public/              # Web-accessible files
│   ├── index.php       # Entry point
│   ├── assets/         # CSS, JS, images
│   └── uploads/        # Temporary uploads
├── src/                # Application code
│   ├── Controllers/    # Request handlers
│   ├── Models/         # Data models
│   ├── Services/       # Business logic
│   ├── Views/          # HTML templates
│   └── Core/           # Framework components
├── storage/            # Data persistence
│   ├── database/       # SQLite database
│   ├── images/         # Generated images
│   └── cache/          # API cache
├── vendor/             # Composer dependencies
└── composer.json       # PHP dependencies
```

See [FEATURE_PLAN.md](FEATURE_PLAN.md) for detailed architecture documentation.

## 🔧 Development

### Running Tests

```bash
# Run PHP unit tests
composer test

# Run with coverage
composer test -- --coverage-html coverage/
```

### Code Style

```bash
# Check code style
composer phpcs

# Fix code style issues
composer phpcbf
```

### Building for Production

```bash
# Optimize autoloader
composer install --no-dev --optimize-autoloader

# Minify assets (requires Node.js)
npm run build

# Set production environment
# Edit .env: APP_ENV=production, APP_DEBUG=false
```

## 🔒 Security

- **API Keys**: Never commit `.env` file or expose API keys
- **File Uploads**: Only images allowed, validated on server
- **SQL Injection**: All queries use prepared statements
- **XSS Protection**: All outputs are escaped
- **CSRF**: Protected forms with tokens

## 🐛 Troubleshooting

### "API Key Invalid" Error
- Verify your OpenRouter API key in `.env`
- Ensure no extra spaces or quotes

### "GD Library Not Found"
```bash
# Ubuntu/Debian
sudo apt-get install php-gd

# macOS (Homebrew)
brew install php@8.1
```

### Canvas Not Loading
- Check browser console for JavaScript errors
- Ensure Fabric.js CDN is accessible
- Try clearing browser cache

### Images Not Saving
```bash
# Check permissions
chmod -R 755 storage/
chown -R www-data:www-data storage/
```

## 📊 Performance Tips

1. **Use Free Models**: Start with free-tier models for testing
2. **Enable Caching**: Reduces duplicate API calls
3. **Optimize Images**: Use WebP format for storage
4. **Batch Edits**: Make multiple edits before refinement
5. **Progressive Loading**: Enable lazy loading in gallery

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Intervention Image](https://image.intervention.io/) - PHP image processing
- [Fabric.js](http://fabricjs.com/) - Canvas manipulation
- [OpenRouter](https://openrouter.ai/) - AI model API
- [Feather Icons](https://feathericons.com/) - Beautiful icons

## 📞 Support

- **Documentation**: [FEATURE_PLAN.md](FEATURE_PLAN.md)
- **Issues**: [GitHub Issues](https://github.com/yourusername/AI-Image-Generator/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yourusername/AI-Image-Generator/discussions)

## 🗺️ Roadmap

- [x] Core generation functionality
- [x] Canvas editor with basic tools
- [x] Iterative refinement system
- [ ] Advanced masking/inpainting
- [ ] Multi-user support
- [ ] Real-time collaboration
- [ ] Mobile app (PWA)
- [ ] Plugin system

See [FEATURE_PLAN.md](FEATURE_PLAN.md) for detailed implementation phases.

---

**Made with ❤️ using Vanilla PHP and AI**
