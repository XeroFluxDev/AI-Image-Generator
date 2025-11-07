<div class="editor-container">
    <div class="container-fluid">
        <div class="editor-layout">
            <!-- Left Sidebar - Controls -->
            <aside class="editor-sidebar">
                <div class="sidebar-section">
                    <h3><i class="fas fa-magic"></i> Generate</h3>
                    <form id="generate-form">
                        <div class="form-group">
                            <label for="prompt">Prompt</label>
                            <textarea
                                id="prompt"
                                name="prompt"
                                rows="3"
                                class="form-control"
                                placeholder="Describe the image you want to create..."
                                required
                            ></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="width">Width</label>
                                <select id="width" name="width" class="form-control">
                                    <option value="512">512px</option>
                                    <option value="768">768px</option>
                                    <option value="1024" selected>1024px</option>
                                    <option value="1280">1280px</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="height">Height</label>
                                <select id="height" name="height" class="form-control">
                                    <option value="512">512px</option>
                                    <option value="768">768px</option>
                                    <option value="1024" selected>1024px</option>
                                    <option value="1280">1280px</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="generate-btn">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            Generate Image
                        </button>
                    </form>
                </div>

                <div class="sidebar-section">
                    <h3><i class="fas fa-sync-alt"></i> Refine</h3>
                    <form id="refine-form">
                        <div class="form-group">
                            <label for="refinement-prompt">Refinement Prompt</label>
                            <textarea
                                id="refinement-prompt"
                                name="refinement_prompt"
                                rows="3"
                                class="form-control"
                                placeholder="Describe the changes you want..."
                                disabled
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary btn-block" id="refine-btn" disabled>
                            <i class="fas fa-sync-alt"></i>
                            Refine Image
                        </button>
                    </form>
                </div>

                <div class="sidebar-section">
                    <h3><i class="fas fa-history"></i> Version History</h3>
                    <div id="version-history" class="version-list">
                        <p class="text-muted">No versions yet</p>
                    </div>
                </div>
            </aside>

            <!-- Main Content - Canvas -->
            <main class="editor-main">
                <div class="editor-toolbar">
                    <div class="toolbar-group">
                        <button class="btn btn-tool" id="tool-select" title="Select">
                            <i class="fas fa-mouse-pointer"></i>
                        </button>
                        <button class="btn btn-tool" id="tool-draw" title="Draw">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-tool" id="tool-text" title="Add Text">
                            <i class="fas fa-font"></i>
                        </button>
                        <button class="btn btn-tool" id="tool-shapes" title="Shapes">
                            <i class="fas fa-shapes"></i>
                        </button>
                    </div>

                    <div class="toolbar-group">
                        <button class="btn btn-tool" id="tool-undo" title="Undo">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="btn btn-tool" id="tool-redo" title="Redo">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button class="btn btn-tool" id="tool-clear" title="Clear Canvas">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>

                    <div class="toolbar-group">
                        <button class="btn btn-success" id="save-btn" disabled>
                            <i class="fas fa-save"></i>
                            Save
                        </button>
                        <button class="btn btn-primary" id="export-btn" disabled>
                            <i class="fas fa-download"></i>
                            Export
                        </button>
                    </div>
                </div>

                <div class="canvas-container">
                    <canvas id="image-canvas"></canvas>
                    <div id="canvas-placeholder" class="canvas-placeholder">
                        <i class="fas fa-image fa-3x"></i>
                        <p>Generate an image to get started</p>
                    </div>
                    <div id="canvas-loading" class="canvas-loading" style="display: none;">
                        <div class="spinner"></div>
                        <p>Generating image...</p>
                    </div>
                </div>
            </main>

            <!-- Right Sidebar - Properties -->
            <aside class="editor-properties">
                <div class="sidebar-section">
                    <h3><i class="fas fa-sliders-h"></i> Properties</h3>

                    <div class="form-group">
                        <label for="brush-size">Brush Size</label>
                        <input type="range" id="brush-size" min="1" max="50" value="5" class="form-range">
                        <span id="brush-size-value">5px</span>
                    </div>

                    <div class="form-group">
                        <label for="brush-color">Color</label>
                        <input type="color" id="brush-color" value="#000000" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="opacity">Opacity</label>
                        <input type="range" id="opacity" min="0" max="100" value="100" class="form-range">
                        <span id="opacity-value">100%</span>
                    </div>
                </div>

                <div class="sidebar-section">
                    <h3><i class="fas fa-adjust"></i> Filters</h3>
                    <button class="btn btn-sm btn-block" id="filter-brightness">
                        <i class="fas fa-sun"></i> Brightness
                    </button>
                    <button class="btn btn-sm btn-block" id="filter-contrast">
                        <i class="fas fa-adjust"></i> Contrast
                    </button>
                    <button class="btn btn-sm btn-block" id="filter-saturation">
                        <i class="fas fa-palette"></i> Saturation
                    </button>
                    <button class="btn btn-sm btn-block" id="filter-blur">
                        <i class="fas fa-circle-notch"></i> Blur
                    </button>
                </div>
            </aside>
        </div>
    </div>
</div>
