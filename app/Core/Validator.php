<?php
/**
 * Input Validator
 * Validates form data and inputs
 */

class Validator {
    private array $errors = [];

    /**
     * Validate required field
     */
    public function required(string $field, string $value, string $label = null): bool {
        if (empty(trim($value))) {
            $this->errors[$field] = ($label ?? $field) . ' is required';
            return false;
        }
        return true;
    }

    /**
     * Validate email format
     */
    public function email(string $field, string $value, string $label = null): bool {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = ($label ?? $field) . ' must be a valid email address';
            return false;
        }
        return true;
    }

    /**
     * Validate minimum length
     */
    public function min(string $field, string $value, int $min, string $label = null): bool {
        if (strlen($value) < $min) {
            $this->errors[$field] = ($label ?? $field) . ' must be at least ' . $min . ' characters';
            return false;
        }
        return true;
    }

    /**
     * Validate maximum length
     */
    public function max(string $field, string $value, int $max, string $label = null): bool {
        if (strlen($value) > $max) {
            $this->errors[$field] = ($label ?? $field) . ' must not exceed ' . $max . ' characters';
            return false;
        }
        return true;
    }

    /**
     * Validate string matches pattern
     */
    public function pattern(string $field, string $value, string $pattern, string $label = null): bool {
        if (!preg_match($pattern, $value)) {
            $this->errors[$field] = ($label ?? $field) . ' format is invalid';
            return false;
        }
        return true;
    }

    /**
     * Validate integer
     */
    public function integer(string $field, string $value, string $label = null): bool {
        if (!is_numeric($value) || (int)$value != $value) {
            $this->errors[$field] = ($label ?? $field) . ' must be a valid number';
            return false;
        }
        return true;
    }

    /**
     * Validate date
     */
    public function date(string $field, string $value, string $label = null): bool {
        if (!strtotime($value)) {
            $this->errors[$field] = ($label ?? $field) . ' must be a valid date';
            return false;
        }
        return true;
    }

    /**
     * Validate in allowed values
     */
    public function in(string $field, string $value, array $allowed, string $label = null): bool {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = ($label ?? $field) . ' must be one of: ' . implode(', ', $allowed);
            return false;
        }
        return true;
    }

    /**
     * Get all errors
     */
    public function errors(): array {
        return $this->errors;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool {
        return empty($this->errors);
    }

    /**
     * Reset errors
     */
    public function reset(): void {
        $this->errors = [];
    }
}
