<?php
/**
 * URL Router
 * Maps URLs to controllers and actions. Supports {param} placeholders.
 */

class Router {
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    private string $currentMethod = 'GET';
    private string $currentRoute = '/';
    private string $notFoundView = 'frontend/404';

    /**
     * Register a GET route
     */
    public function get(string $route, $action): void {
        $this->routes['GET'][$route] = $action;
    }

    /**
     * Register a POST route
     */
    public function post(string $route, $action): void {
        $this->routes['POST'][$route] = $action;
    }

    /**
     * Register a route for any HTTP method
     */
    public function any(string $route, $action): void {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m) {
            $this->routes[$m][$route] = $action;
        }
    }

    /**
     * Register a route group (prefix + optional auth middleware)
     *
     * @param string   $prefix
     * @param callable $callback receives Router, prefix, middleware
     * @param array    $middleware e.g. ['admin'] or ['member']
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void {
        $callback($this, $prefix, $middleware);
    }

    /**
     * Set a custom 404 view path
     */
    public function setNotFound(string $view): void {
        $this->notFoundView = $view;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(): void {
        $this->currentMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->currentRoute  = $this->getRoute();

        // Maintenance mode check (if enabled)
        if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE) {
            $this->renderMaintenance();
            return;
        }

        $action = null;
        $method = $this->currentMethod;
        if (!isset($this->routes[$method])) {
            $this->notFound();
            return;
        }
        $action = $this->matchRoute($this->routes[$method], $this->currentRoute);

        if ($action === null) {
            // try wildcard catch-all per method
            if (isset($this->routes[$method]['/{path}'])) {
                $action = $this->routes[$method]['/{path}'];
            }
        }

        if ($action === null) {
            $this->notFound();
            return;
        }

        $this->executeAction($action);
    }

    /**
     * Resolve current route from REQUEST_URI, stripping base path
     */
    private function getRoute(): string {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        // strip query string
        if (($p = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $p);
        }
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $basePath = str_replace('/index.php', '', $scriptName);
        $basePath = rtrim($basePath, '/');
        if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        if ($requestUri === '' || $requestUri === false) {
            $requestUri = '/';
        }
        return $requestUri;
    }

    /**
     * Match URI against registered routes (with {param} support)
     */
    private function matchRoute(array $routeMap, string $uri): mixed {
        // Static route match wins
        if (isset($routeMap[$uri])) {
            return $routeMap[$uri];
        }
        foreach ($routeMap as $route => $action) {
            $pattern = $this->convertToRegex($route);
            if (@preg_match($pattern, $uri, $matches)) {
                // Extract named params
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $_GET[$key] = $value;
                        $_REQUEST[$key] = $value;
                    }
                }
                return $action;
            }
        }
        return null;
    }

    /**
     * Convert /path/{slug}/sub/{id} → #^/path/(?P<slug>[^/]+)/sub/(?P<id>[^/]+)$#
     */
    private function convertToRegex(string $route): string {
        $route = '/' . ltrim($route, '/');
        $parts = preg_split('/(\{[^}]+\})/', $route, -1, PREG_SPLIT_DELIM_CAPTURE);
        $pattern = '';
        foreach ($parts as $part) {
            if (preg_match('/^\{([^}]+)\}$/', $part, $m)) {
                $pattern .= '(?P<' . $m[1] . '>[^/]+)';
            } else {
                $pattern .= preg_quote($part, '#');
            }
        }
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute Controller@method or closure
     */
    private function executeAction(mixed $action): void {
        try {
            if (is_string($action) && strpos($action, '@') !== false) {
                [$controllerShort, $method] = explode('@', $action);
                $controllerClass = 'App\\Controllers\\' . str_replace('/', '\\', $controllerShort);

                if (!class_exists($controllerClass)) {
                    throw new RuntimeException("Controller class not found: {$controllerClass}");
                }
                $instance = new $controllerClass();
                if (!method_exists($instance, $method)) {
                    throw new RuntimeException("Method {$method} not found on {$controllerClass}");
                }
                $instance->$method();
                return;
            }
            if (is_callable($action)) {
                $action();
                return;
            }
            throw new RuntimeException('Invalid route action');
        } catch (\Throwable $e) {
            error_log("Router error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            $this->renderError($e);
        }
    }

    /**
     * Display 404 view
     */
    private function notFound(): void {
        http_response_code(404);
        try {
            $view = new View();
            $view->render($this->notFoundView, ['requestRoute' => $this->currentRoute]);
        } catch (\Throwable $e) {
            echo '<h1>404</h1><p>Page Not Found</p>';
        }
    }

    /**
     * Render the maintenance page
     */
    private function renderMaintenance(): void {
        http_response_code(503);
        try {
            $view = new View();
            $view->render('frontend/maintenance');
        } catch (\Throwable $e) {
            echo 'Site under maintenance. Please check back soon.';
        }
    }

    /**
     * Render error page
     */
    private function renderError(\Throwable $e): void {
        if (defined('DEBUG') && DEBUG) {
            echo "<h2>Error</h2>";
            echo "<p><strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
            echo "<p>File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            http_response_code(500);
            echo 'An error occurred. Please try again later.';
        }
    }
}
