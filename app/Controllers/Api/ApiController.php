<?php

namespace App\Controllers\Api;

/**
 * Base API Controller
 *
 * Provides JSON body parsing and the standard response envelope used by all
 * AJAX endpoints (SSOT §9.3):
 *   { "success": bool, "message": string, "data": {...}, "errors": {...} }
 *
 * The frontend ajax.js helper sends a JSON request body plus an X-CSRF-Token
 * header, so input is merged from the JSON body, $_POST and $_GET (in that
 * order of precedence). CSRF for POST endpoints is validated via the header.
 */
class ApiController extends \Controller {
    /** Decoded JSON request body */
    protected array $body = [];

    public function __construct() {
        parent::__construct();
        $this->body = $this->parseJsonBody();
    }

    /**
     * Decode a JSON request body if one was sent.
     */
    private function parseJsonBody(): array {
        $raw = file_get_contents('php://input');
        if ($raw === '' || $raw === false) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Read a request parameter from JSON body, POST then GET.
     */
    protected function param(string $key, $default = null): mixed {
        if (array_key_exists($key, $this->body)) {
            $value = $this->body[$key];
            return is_string($value) ? trim($value) : $value;
        }
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Success envelope.
     */
    protected function ok(array $data = [], string $message = 'Operation completed'): void {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => [],
        ]);
    }

    /**
     * Error envelope.
     */
    protected function fail(string $message = 'An error occurred', array $errors = [], int $status = 400): void {
        $this->json([
            'success' => false,
            'message' => $message,
            'data' => [],
            'errors' => $errors,
        ], $status);
    }

    /**
     * Reject anything that is not a POST request.
     */
    protected function requirePost(): void {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->fail('Method not allowed.', [], 405);
        }
    }

    /**
     * CSRF guard for state-changing API calls. Responds with JSON on failure.
     */
    protected function guardCsrf(): void {
        if (!\Auth::verifyCsrf()) {
            $this->fail('Invalid or missing CSRF token. Please refresh and try again.', [], 419);
        }
    }

    /**
     * Authentication guard for member-scoped endpoints (JSON response).
     */
    protected function requireAuthApi(): void {
        if (!\Auth::check()) {
            $this->fail('Authentication required.', [], 401);
        }
    }

    /**
     * Staff/admin guard for admin endpoints (JSON response, never HTML).
     */
    protected function requireAdminApi(): void {
        $this->requireAuthApi();
        $allowed = [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON];
        if (!in_array($this->userRole, $allowed, true)) {
            $this->fail('You do not have permission to perform this action.', [], 403);
        }
    }
}
