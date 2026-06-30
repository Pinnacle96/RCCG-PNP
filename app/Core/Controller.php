<?php
/**
 * Base Controller
 * Parent class for all controllers. Provides view rendering, auth helpers,
 * redirect, CSRF, JSON response, flash messages.
 */

class Controller {
    protected View $view;
    protected ?array $user = null;
    protected ?int $userId = null;
    protected ?int $memberId = null;
    protected ?string $userRole = null;
    protected array $data = [];

    public function __construct() {
        $this->view = new View();
        $this->user     = Auth::user();
        $this->userId   = Auth::userId();
        $this->userRole = Auth::role();
        $this->memberId = $this->user['member_id'] ?? null;
    }

    /**
     * Render a view with optional layout, passing $data
     *
     * @param string $view   dot-notation view path (e.g. "admin.members.index")
     * @param array  $data   variables exposed to the view
     * @param string|null $layout  layout file name without .php (e.g. "admin")
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void {
        // merge controller-level data
        $data = array_merge($this->data, $data);

        // Always provide $user, $csrf to views
        $data['currentUser'] = $this->user;
        $data['csrf']        = Auth::csrf();
        $data['flash']       = $this->getFlash();
        $data['settings']    = Settings::all();
        $data['siteName']    = Settings::get('site_name', SITE_NAME);
        $data['siteTagline'] = Settings::get('site_tagline', SITE_TAGLINE);
        $data['baseUrl']     = BASE_URL;

        $this->view->render($view, $data, $layout);
    }

    /**
     * Redirect to a URL (default BASE_URL)
     */
    protected function redirect(string $url = '/', int $statusCode = 302): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Location: ' . $url);
        } else {
            echo '<script>window.location.href="' . htmlspecialchars($url) . '";</script>';
        }
        exit;
    }

    /**
     * Send JSON response
     */
    protected function json($data, int $statusCode = 200): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Require authentication. If not logged in, redirect to login.
     */
    protected function requireAuth(string $redirectUrl = '/login'): void {
        if (!Auth::check()) {
            $this->setFlash('error', 'Please sign in to continue.');
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Require a specific role. Pass an array for multiple allowed roles.
     */
    protected function requireRole(string|array $roles): void {
        $this->requireAuth();
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($this->userRole, $allowed, true)) {
            http_response_code(403);
            $this->view('frontend/403');
            exit;
        }
    }

    /**
     * Verify CSRF token on POST; throw 403 on mismatch
     */
    protected function verifyCsrf(): void {
        if (!Auth::verifyCsrf()) {
            http_response_code(403);
            $this->json(['success' => false, 'message' => 'Invalid CSRF token. Please refresh the page and try again.'], 403);
        }
    }

    /**
     * Get cleaned POST input (key or all)
     */
    protected function input(?string $key = null, $default = null): mixed {
        $source = array_merge($_GET, $_POST);
        $cleaned = [];
        foreach ($source as $k => $v) {
            $cleaned[$k] = is_string($v) ? trim($v) : $v;
        }
        if ($key === null) return $cleaned;
        return $cleaned[$key] ?? $default;
    }

    /**
     * Send flash message stored in session
     */
    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear all flash messages
     */
    protected function getFlash(): array {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }
}
