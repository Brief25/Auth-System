DB
1.
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
    password_hash VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    otp_code VARCHAR(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
    otp_expiry DATETIME DEFAULT NULL,
    role ENUM('user', 'admin') COLLATE utf8mb4_general_ci DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    failed_attempts INT(11) DEFAULT 0,
    last_failed_at DATETIME DEFAULT NULL,
    locked_until DATETIME DEFAULT NULL,
    status ENUM('active', 'blocked') COLLATE utf8mb4_general_ci DEFAULT 'active',
    deleted_at DATETIME DEFAULT NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY email_unique (email),
    INDEX (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
