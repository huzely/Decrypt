CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    telegram_media TEXT,
    meta_title VARCHAR(255),
    meta_description VARCHAR(255),
    meta_keywords VARCHAR(255),
    status ENUM('draft','public') DEFAULT 'draft',
    created_at DATETIME,
    updated_at DATETIME
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255),
    site_description VARCHAR(255),
    theme TINYINT DEFAULT 1,
    logo VARCHAR(255),
    banner VARCHAR(255),
    ads_enabled TINYINT DEFAULT 0,
    ad_link VARCHAR(255),
    ad_title VARCHAR(255),
    ad_body TEXT
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50),
    slug VARCHAR(255),
    ip_hash CHAR(64),
    ua_hash CHAR(64),
    token_hash CHAR(64),
    created_at DATETIME
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE telegram_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT,
    created_at DATETIME
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO admins(username, password_hash) VALUES('admin', '$2y$12$8iZvhwdsREBGjkQc4TQxA.iEPsVSbfr5Tjlyo.7vTn5L3ZfjFsN/G');
INSERT INTO site_settings(site_name, site_description, theme, ads_enabled) VALUES('Báo nhanh', 'Tin nhanh mọi lúc', 1, 0);
