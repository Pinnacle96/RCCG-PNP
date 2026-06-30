CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(150) UNIQUE NOT NULL,
    name         VARCHAR(160) NULL,
    is_active    TINYINT(1) DEFAULT 1,
    ip_address   VARCHAR(45) NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
