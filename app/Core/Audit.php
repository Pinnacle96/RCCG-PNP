<?php
/**
 * Audit Log helper.
 *
 * Records privileged admin actions into the `audit_log` table (SSOT §5.22).
 * Failures are swallowed so logging can never break a user-facing action.
 */
class Audit {
    public static function log(string $action, ?string $module = null, ?string $description = null): void {
        try {
            Database::insert('audit_log', [
                'user_id' => Auth::userId(),
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (\Throwable $e) {
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }
}
