# AI Image Generator & Editor - Complete Feature Plan

## 📋 Executive Summary

A vanilla PHP application for AI-powered image generation and iterative editing using OpenRouter API. The app enables users to generate images from text prompts, apply basic edits using client-side tools, and refine results through multiple AI iterations until satisfied.

**Core Workflow**: `Prompt → AI Generate → Edit with Tools → AI Refine → Repeat/Save`

---

## 🎯 Project Goals

- **Minimal Complexity**: Clean vanilla PHP with MVC architecture, minimal files
- **Iterative Workflow**: Seamless prompt → generate → edit → regenerate loop
- **Free Tools**: Use open-source libraries for image manipulation
- **Modern UX**: Responsive, intuitive interface with real-time feedback
- **API Efficiency**: Smart caching and optimized OpenRouter API usage

---

## 🏗️ Technical Stack

### Backend
- **Language**: Vanilla PHP 8.1+
- **Architecture**: Lightweight MVC pattern
- **Image Processing**: GD Library (built-in) + Intervention Image v3
- **API Integration**: cURL for OpenRouter API calls
- **Session Management**: PHP Sessions for workflow state
- **Database**: SQLite (single file, zero config)

### Frontend
- **UI Framework**: Vanilla JavaScript (ES6+)
- **Canvas Library**: Fabric.js v5+ (powerful, SVG export, active development)
- **Styling**: CSS3 with CSS Variables (dark/light theme ready)
- **Icons**: Feather Icons (lightweight SVG icons)

### Third-Party Services
- **AI Provider**: OpenRouter API (multi-model support)
- **Recommended Models**:
  - Generation: `black-forest-labs/flux-1-schnell-free`
  - Refinement: `stability-ai/stable-diffusion-xl-base-1.0`

---

## 📊 Core Features

### 1. Image Generation Module
- Text-to-image generation via OpenRouter
- Prompt enhancement suggestions
- Multiple aspect ratio presets (1:1, 16:9, 9:16, 4:3)
- Style presets (realistic, artistic, minimalist, etc.)
- Seed control for reproducible results
- Batch generation (1-4 images)

### 2. Canvas Editor Module
- **Drawing Tools**:
  - Brush (variable size, opacity, color)
  - Eraser
  - Shape tools (rectangle, circle, line, arrow)
  - Text overlay (fonts, sizes, colors)

- **Image Adjustments**:
  - Crop with predefined ratios
  - Rotate (90°, 180°, 270°, free rotation)
  - Flip horizontal/vertical
  - Filters (brightness, contrast, saturation, blur)

- **Layer Management**:
  - Add/remove annotation layers
  - Preserve base AI-generated image
  - Export layers separately or merged

### 3. Iterative Refinement System
- **Refinement Prompt**: Describe desired changes
- **Edit Modes**:
  - Text-based (pure prompt refinement)
  - Visual + Text (canvas edits + prompt)
  - Region-specific (mask editing areas)

- **History Management**:
  - Version history (up to 10 iterations)
  - Compare side-by-side
  - Revert to any previous version
  - Branch from earlier iterations

### 4. Project Management
- Save/load projects (SQLite storage)
- Export images (PNG, JPEG, WebP)
- Project metadata (prompts, settings, timestamps)
- Gallery view of all generations
- Search and filter projects

### 5. Settings & Configuration
- OpenRouter API key management
- Default image dimensions
- Model selection preferences
- UI theme toggle
- Auto-save interval
- Export quality settings

---

## 🔄 Process Flow

### Primary Workflow

```
┌─────────────────────────────────────────────────────────────────┐
│                        START APPLICATION                        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
                ┌────────────────────┐
                │  Enter Prompt +    │
                │  Select Settings   │
                └─────────┬──────────┘
                          │
                          ▼
                ┌────────────────────┐
                │  Send to OpenRouter│◄──────┐
                │  (Text-to-Image)   │       │
                └─────────┬──────────┘       │
                          │                  │
                          ▼                  │
                ┌────────────────────┐       │
                │  Receive Generated │       │
                │  Image (Base64)    │       │
                └─────────┬──────────┘       │
                          │                  │
                          ▼                  │
                ┌────────────────────┐       │
                │  Display in Canvas │       │
                │  Editor            │       │
                └─────────┬──────────┘       │
                          │                  │
                          ▼                  │
                    ┌──────────┐             │
                    │ Satisfied?│             │
                    └─────┬────┘             │
                          │                  │
               No ────────┼─────── Yes       │
               │          │          │       │
               ▼          ▼          ▼       │
        ┌──────────┐  Save/     ┌────────┐  │
        │ Use Tools│  Export    │  END   │  │
        │ to Edit  │            └────────┘  │
        └────┬─────┘                         │
             │                               │
             ▼                               │
        ┌────────────────────┐               │
        │ Add Refinement     │               │
        │ Prompt (optional)  │               │
        └────────┬───────────┘               │
                 │                           │
                 ▼                           │
        ┌────────────────────┐               │
        │ Generate Composite │               │
        │ (Canvas + Edits)   │               │
        └────────┬───────────┘               │
                 │                           │
                 └───────────────────────────┘
                   (Loop back to OpenRouter)
```

### Detailed API Interaction Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                     CLIENT (Browser)                            │
│                                                                 │
│  ┌───────────┐     ┌──────────────┐     ┌─────────────────┐  │
│  │  Prompt   │────►│ Canvas Editor│────►│ Refinement Form │  │
│  │  Form     │     │  (Fabric.js) │     │                 │  │
│  └───────────┘     └──────────────┘     └─────────────────┘  │
│        │                  │                       │            │
│        │ (1) Generate     │ (3) Edit Image        │ (4) Refine│
│        ▼                  ▼                       ▼            │
│   [AJAX Request]     [Canvas Data]        [Image + Prompt]    │
└────────┼──────────────────┼───────────────────────┼───────────┘
         │                  │                       │
         │                  │                       │
┌────────┼──────────────────┼───────────────────────┼───────────┐
│        │          PHP BACKEND (MVC)               │           │
│        ▼                  │                       ▼           │
│  ┌──────────┐             │              ┌────────────────┐  │
│  │  Router  │             │              │   Controller   │  │
│  └────┬─────┘             │              └────────┬───────┘  │
│       │                   │                       │           │
│       ▼                   │                       ▼           │
│  ┌──────────────┐         │              ┌────────────────┐  │
│  │  Controller  │         │              │  Image Service │  │
│  └────┬─────────┘         │              └────────┬───────┘  │
│       │                   │                       │           │
│       ▼                   ▼                       ▼           │
│  ┌────────────────────────────────────────────────────────┐  │
│  │              Image Processing Service                  │  │
│  │  • Apply filters (GD/Intervention)                     │  │
│  │  • Merge canvas layers                                 │  │
│  │  • Prepare composite for AI                            │  │
│  └────────────────────────┬───────────────────────────────┘  │
│                           │                                  │
│                           ▼                                  │
│                  ┌─────────────────┐                         │
│                  │  OpenRouter     │                         │
│                  │  API Service    │                         │
│                  └────────┬────────┘                         │
│                           │                                  │
│                           │ (HTTP POST)                      │
└───────────────────────────┼──────────────────────────────────┘
                            │
                            ▼
┌───────────────────────────────────────────────────────────────┐
│                   OPENROUTER API                              │
│                                                               │
│  POST /api/v1/chat/completions                               │
│  {                                                            │
│    "model": "black-forest-labs/flux-1-schnell-free",         │
│    "messages": [...],                                        │
│    "modalities": ["image", "text"]                           │
│  }                                                            │
│                           │                                   │
│                           ▼                                   │
│                  ┌─────────────────┐                          │
│                  │  AI Model       │                          │
│                  │  Processing     │                          │
│                  └────────┬────────┘                          │
│                           │                                   │
│                           ▼                                   │
│                  { "images": [                                │
│                      "data:image/png;base64,..."              │
│                    ]                                          │
│                  }                                            │
└───────────────────────────┬───────────────────────────────────┘
                            │
                            │ (Response)
                            ▼
┌───────────────────────────────────────────────────────────────┐
│                    PHP BACKEND                                │
│                           │                                   │
│                           ▼                                   │
│                  ┌─────────────────┐                          │
│                  │  Save to DB     │                          │
│                  │  • Image data   │                          │
│                  │  • Metadata     │                          │
│                  │  • Version info │                          │
│                  └────────┬────────┘                          │
│                           │                                   │
│                           ▼                                   │
│                  [JSON Response]                              │
└───────────────────────────┬───────────────────────────────────┘
                            │
                            ▼
┌───────────────────────────────────────────────────────────────┐
│                     CLIENT (Browser)                          │
│                                                               │
│                  Display new image in canvas                  │
│                  Update version history                       │
│                  Enable further editing                       │
└───────────────────────────────────────────────────────────────┘
```

---

## 📁 File Structure

```
AI-Image-Generator/
│
├── public/                          # Public web root
│   ├── index.php                    # Front controller (entry point)
│   ├── .htaccess                    # URL rewriting rules
│   │
│   ├── assets/                      # Static assets
│   │   ├── css/
│   │   │   └── style.css           # Main stylesheet (~500 lines)
│   │   │
│   │   ├── js/
│   │   │   ├── app.js              # Main application logic
│   │   │   ├── canvas-editor.js    # Fabric.js canvas wrapper
│   │   │   ├── api-client.js       # AJAX API calls
│   │   │   └── utils.js            # Helper functions
│   │   │
│   │   └── icons/                  # Feather icons (optional local copy)
│   │
│   └── uploads/                     # Temporary file storage
│       └── .htaccess               # Deny direct access
│
├── src/                             # Application source code
│   ├── Config/
│   │   └── config.php              # Configuration constants
│   │
│   ├── Controllers/
│   │   ├── HomeController.php      # Main page controller
│   │   ├── ImageController.php     # Image generation/editing
│   │   └── ProjectController.php   # Project management
│   │
│   ├── Models/
│   │   ├── Image.php               # Image data model
│   │   └── Project.php             # Project data model
│   │
│   ├── Services/
│   │   ├── OpenRouterService.php   # OpenRouter API integration
│   │   ├── ImageService.php        # Image processing (GD/Intervention)
│   │   └── DatabaseService.php     # SQLite database operations
│   │
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── header.php          # Common header
│   │   │   └── footer.php          # Common footer
│   │   │
│   │   ├── home.php                # Landing/dashboard view
│   │   ├── editor.php              # Canvas editor view
│   │   └── gallery.php             # Project gallery view
│   │
│   └── Core/
│       ├── Router.php              # Simple routing system
│       ├── Controller.php          # Base controller class
│       ├── Database.php            # PDO wrapper
│       └── Session.php             # Session management
│
├── storage/
│   ├── database/
│   │   └── app.db                  # SQLite database file
│   │
│   ├── images/                     # Saved images
│   │   ├── originals/              # AI-generated originals
│   │   ├── edited/                 # Edited versions
│   │   └── exports/                # Final exports
│   │
│   └── cache/                      # API response cache
│       └── .htaccess               # Deny direct access
│
├── vendor/                          # Composer dependencies
│   └── intervention/image/         # Image manipulation library
│
├── composer.json                    # PHP dependencies
├── composer.lock
├── .env.example                     # Environment variables template
├── .env                            # Actual config (gitignored)
├── .gitignore
├── README.md                        # Project documentation
└── FEATURE_PLAN.md                 # This document

```

**Total Files**: ~30 core files (excluding vendor and generated files)

---

## 🎨 UI/UX Design

### Main Interface Sections

```
┌─────────────────────────────────────────────────────────────────┐
│  AI Image Generator                    [Settings] [Gallery] [?] │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │                                                           │  │
│  │                    CANVAS EDITOR                          │  │
│  │                  (Fabric.js Area)                        │  │
│  │                                                           │  │
│  │              [Generated/Edited Image Here]               │  │
│  │                                                           │  │
│  │                                                           │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌─── Toolbar ──────────────────────────────────────────────┐  │
│  │ [↶] [↷] [✂] [T] [✏] [⬜] [○] [→] [🎨] [Save] [Export]   │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌─── Generation Panel ────────────────────────────────────┐  │
│  │                                                           │  │
│  │  Prompt: [________________________________]  [Generate]  │  │
│  │                                                           │  │
│  │  Refinement: [____________________________] [Refine]     │  │
│  │                                                           │  │
│  │  ⚙ Size: [1024x1024 ▼] Style: [Realistic ▼]            │  │
│  │                                                           │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌─── Version History ─────────────────────────────────────┐  │
│  │  [v1] [v2] [v3] [v4] ... [Compare] [Revert]             │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Key UI Features

1. **Responsive Design**: Mobile-friendly, adapts to tablet/desktop
2. **Real-time Preview**: Canvas updates instantly with edits
3. **Loading States**: Progress indicators for API calls
4. **Keyboard Shortcuts**: Power user productivity
5. **Tooltips**: Contextual help for all tools
6. **Error Handling**: User-friendly error messages

---

## 🔧 Implementation Details

### 1. OpenRouter API Integration

**Endpoint**: `https://openrouter.ai/api/v1/chat/completions`

**Request Structure**:
```json
{
  "model": "black-forest-labs/flux-1-schnell-free",
  "messages": [
    {
      "role": "user",
      "content": [
        {
          "type": "text",
          "text": "A serene mountain landscape at sunset"
        }
      ]
    }
  ],
  "modalities": ["image", "text"],
  "max_tokens": 1024
}
```

**Response Structure**:
```json
{
  "id": "gen-...",
  "choices": [
    {
      "message": {
        "content": "Generated image based on prompt",
        "images": [
          "data:image/png;base64,iVBORw0KG..."
        ]
      }
    }
  ],
  "usage": {
    "prompt_tokens": 15,
    "completion_tokens": 0,
    "total_tokens": 15
  }
}
```

**For Refinement** (Image-to-Image):
```json
{
  "model": "stability-ai/stable-diffusion-xl-base-1.0",
  "messages": [
    {
      "role": "user",
      "content": [
        {
          "type": "image_url",
          "image_url": {
            "url": "data:image/png;base64,..."
          }
        },
        {
          "type": "text",
          "text": "Make the sky more dramatic with storm clouds"
        }
      ]
    }
  ],
  "modalities": ["image", "text"]
}
```

### 2. Image Processing Pipeline

**PHP (Backend)**:
```php
// Using Intervention Image v3
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());

// Apply basic edits before sending to AI
$image = $manager->read($base64Image);
$image->brightness(10);
$image->contrast(5);
$image->crop(800, 600, 100, 100);

// Convert to base64 for API
$encoded = $image->toWebp(85)->toDataUri();
```

**JavaScript (Frontend)**:
```javascript
// Using Fabric.js for canvas editing
const canvas = new fabric.Canvas('image-canvas');

// Add drawing layer
canvas.isDrawingMode = true;
canvas.freeDrawingBrush.width = 5;
canvas.freeDrawingBrush.color = '#ff0000';

// Export merged image
const dataURL = canvas.toDataURL({
  format: 'png',
  quality: 0.9
});
```

### 3. Database Schema

```sql
-- Projects table
CREATE TABLE projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    initial_prompt TEXT NOT NULL,
    settings JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Images table (version history)
CREATE TABLE images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id INTEGER NOT NULL,
    version INTEGER NOT NULL,
    image_path TEXT NOT NULL,
    thumbnail_path TEXT,
    prompt TEXT,
    refinement_prompt TEXT,
    metadata JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- API cache table
CREATE TABLE api_cache (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cache_key TEXT UNIQUE NOT NULL,
    response_data TEXT NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Settings table
CREATE TABLE settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### 4. Key Algorithms

**Smart Caching Strategy**:
```php
// Cache identical prompts to save API costs
function getCacheKey($prompt, $settings) {
    return md5(json_encode([
        'prompt' => $prompt,
        'model' => $settings['model'],
        'size' => $settings['size'],
        'style' => $settings['style']
    ]));
}
```

**Version Comparison**:
```javascript
// Side-by-side comparison utility
function compareVersions(versionA, versionB) {
    const container = document.getElementById('comparison-view');
    container.innerHTML = `
        <div class="comparison-grid">
            <div class="version-panel">
                <h3>Version ${versionA.number}</h3>
                <img src="${versionA.url}" />
                <p>${versionA.prompt}</p>
            </div>
            <div class="version-panel">
                <h3>Version ${versionB.number}</h3>
                <img src="${versionB.url}" />
                <p>${versionB.prompt}</p>
            </div>
        </div>
    `;
}
```

---

## 🚀 Implementation Phases

### Phase 1: Foundation (Week 1)
- [ ] Set up project structure
- [ ] Implement MVC core (Router, Controller, Database)
- [ ] Create basic UI layout with HTML/CSS
- [ ] Set up Composer and install Intervention Image
- [ ] Configure SQLite database with schema

### Phase 2: Core Generation (Week 2)
- [ ] Implement OpenRouter API service
- [ ] Build image generation controller
- [ ] Create prompt form with settings
- [ ] Implement image storage system
- [ ] Add loading states and error handling

### Phase 3: Canvas Editor (Week 3)
- [ ] Integrate Fabric.js canvas
- [ ] Implement drawing tools (brush, shapes, text)
- [ ] Add image adjustment features (crop, rotate, filters)
- [ ] Build toolbar UI with all controls
- [ ] Implement layer management

### Phase 4: Iterative Refinement (Week 4)
- [ ] Build refinement prompt system
- [ ] Implement image-to-image API calls
- [ ] Create version history UI
- [ ] Add compare and revert functionality
- [ ] Build visual diff/comparison tool

### Phase 5: Project Management (Week 5)
- [ ] Implement save/load project features
- [ ] Create gallery view with thumbnails
- [ ] Add search and filter functionality
- [ ] Build export system (PNG, JPEG, WebP)
- [ ] Implement project metadata management

### Phase 6: Polish & Optimization (Week 6)
- [ ] Add keyboard shortcuts
- [ ] Implement smart caching
- [ ] Optimize API call efficiency
- [ ] Improve responsive design
- [ ] Add comprehensive error handling
- [ ] Write documentation

### Phase 7: Testing & Deployment (Week 7)
- [ ] Unit testing for services
- [ ] Integration testing for API
- [ ] UI/UX testing
- [ ] Performance optimization
- [ ] Security audit
- [ ] Deployment preparation

---

## 🔒 Security Considerations

1. **API Key Protection**:
   - Store in `.env` file (never commit)
   - Server-side only, never expose to client
   - Implement rate limiting

2. **File Upload Security**:
   - Validate file types (images only)
   - Limit file sizes (max 10MB)
   - Sanitize filenames
   - Store outside public directory

3. **Input Validation**:
   - Sanitize all user inputs
   - Use prepared statements for SQL
   - Validate JSON payloads
   - Implement CSRF protection

4. **Session Management**:
   - Secure session configuration
   - Session timeout after inactivity
   - Regenerate session IDs

5. **Error Handling**:
   - Never expose sensitive errors to users
   - Log errors securely
   - Generic error messages for production

---

## 📈 Performance Optimization

1. **Image Optimization**:
   - Compress images before storage (WebP format)
   - Generate thumbnails for gallery view
   - Lazy load images in gallery
   - Use progressive image loading

2. **API Efficiency**:
   - Cache identical requests (1 hour TTL)
   - Batch multiple small edits before API call
   - Implement request debouncing
   - Use fastest available models

3. **Frontend Performance**:
   - Minify CSS/JS for production
   - Use CDN for libraries (fallback to local)
   - Implement service worker for offline support
   - Optimize canvas rendering

4. **Database Optimization**:
   - Index frequently queried columns
   - Paginate gallery results
   - Archive old projects
   - Regular VACUUM for SQLite

---

## 🧪 Testing Strategy

### Unit Tests
- OpenRouter API service
- Image processing functions
- Database operations
- Utility functions

### Integration Tests
- Full generation workflow
- Refinement loop
- Save/load projects
- Export functionality

### UI Tests
- Canvas tool interactions
- Form submissions
- Navigation flows
- Responsive behavior

### Performance Tests
- API response times
- Image processing speed
- Database query performance
- Page load times

---

## 📚 Dependencies

### PHP (via Composer)
```json
{
  "require": {
    "php": ">=8.1",
    "intervention/image": "^3.0",
    "vlucas/phpdotenv": "^5.5"
  },
  "require-dev": {
    "phpunit/phpunit": "^10.0"
  }
}
```

### JavaScript (via CDN/NPM)
- Fabric.js v5.3+ (Canvas manipulation)
- Feather Icons v4.29+ (UI icons)
- Optional: Alpine.js v3.x (Reactive UI components)

---

## 🎓 Learning Resources

### For Developers
- [Intervention Image v3 Docs](https://image.intervention.io/v3)
- [Fabric.js Documentation](http://fabricjs.com/docs/)
- [OpenRouter API Docs](https://openrouter.ai/docs)
- [PHP MVC Pattern Guide](https://medium.com/@dilankayasuru/budling-a-minimal-mvc-application-in-vanilla-php-a-step-by-step-guide-75c185604c65)

### For Users
- User guide for prompt engineering
- Tutorial videos for editor tools
- Best practices for iterative refinement
- API cost optimization tips

---

## 🔮 Future Enhancements

### Phase 8+ (Post-MVP)
- [ ] Multi-user support with authentication
- [ ] Collaborative editing (real-time)
- [ ] Advanced masking/inpainting tools
- [ ] Style transfer between images
- [ ] Batch processing multiple images
- [ ] Plugin system for custom tools
- [ ] Desktop app (Electron wrapper)
- [ ] Mobile app (Progressive Web App)
- [ ] Integration with cloud storage (S3, GCS)
- [ ] AI prompt suggestions based on history
- [ ] Template library for common use cases
- [ ] Advanced analytics dashboard

---

## 💰 Cost Considerations

### OpenRouter API Pricing
- **Free Tier**: Limited to certain models (e.g., `flux-1-schnell-free`)
- **Paid Models**: ~$0.01-0.10 per image generation
- **Strategy**: Cache aggressively, use free models for testing

### Infrastructure
- **Hosting**: ~$5-20/month (shared hosting, VPS)
- **Storage**: ~10GB for SQLite + images (expandable)
- **CDN**: Optional (~$1-5/month for static assets)

**Estimated Monthly Cost for 1000 Generations**: $10-50 (depending on model selection)

---

## 📝 Conclusion

This feature plan provides a comprehensive roadmap for building a production-ready AI Image Generator and Editor using vanilla PHP. The architecture emphasizes:

✅ **Simplicity**: Minimal files, clean MVC structure
✅ **Functionality**: Complete iterative editing workflow
✅ **Performance**: Smart caching, optimized processing
✅ **Scalability**: Easy to extend with new features
✅ **User Experience**: Intuitive interface, real-time feedback

**Next Steps**:
1. Review and approve this plan
2. Set up development environment
3. Begin Phase 1 implementation
4. Iterate based on testing and feedback

---

**Document Version**: 1.0
**Last Updated**: November 7, 2025
**Author**: AI Architecture Team
**Status**: Ready for Implementation
