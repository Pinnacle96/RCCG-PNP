<?php
/**
 * View Renderer
 * Loads and renders template files with optional layout.
 *
 * View path syntax: "frontend.home" → app/Views/frontend/home.php
 */

class View {
    private string $viewPath;
    private ?string $layout = null;

    public function __construct(string $viewPath = '') {
        $this->viewPath = $viewPath ?: VIEW_PATH;
    }

    /**
     * Render a view file (with optional layout)
     */
    public function render(string $view, array $data = [], ?string $layout = null): void {
        $file = $this->resolve($view);
        if (!is_file($file)) {
            throw new RuntimeException("View not found: {$view} ({$file})");
        }

        // Extract data into local scope
        extract($data, EXTR_SKIP);

        // Buffer the inner view
        ob_start();
        require $file;
        $content = ob_get_clean();

        // Render with layout if specified
        if ($layout) {
            $layoutFile = LAYOUT_PATH . $layout . '.php';
            if (!is_file($layoutFile)) {
                throw new RuntimeException("Layout not found: {$layout}");
            }
            require $layoutFile;
            return;
        }

        echo $content;
    }

    /**
     * Render a partial (no layout)
     */
    public function partial(string $view, array $data = []): void {
        $this->render($view, $data, null);
    }

    /**
     * Resolve dot-notation view name to file path
     */
    private function resolve(string $view): string {
        $file = str_replace('.', '/', $view) . '.php';
        return VIEW_PATH . $file;
    }
}
