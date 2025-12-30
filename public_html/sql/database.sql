CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content TEXT,
  images_json TEXT,
  videos_json TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  id INT PRIMARY KEY,
  ads_enabled TINYINT(1) NOT NULL DEFAULT 0,
  ad_link VARCHAR(500) DEFAULT '',
  ad_title VARCHAR(255) DEFAULT '',
  ad_body TEXT,
  contact_link VARCHAR(500) DEFAULT ''
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('view','ad_click') NOT NULL,
  post_id INT NULL,
  ip_hash CHAR(64) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_type_created (type, created_at),
  INDEX idx_post (post_id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO admins (username, password_hash) VALUES ('admin', '$2y$12$V.97JyiCRI3EUNfz4uojbupFsTqpoZObHXGGy/tFFiV.kFCGAEj7G')
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash);

INSERT INTO settings (id, ads_enabled, ad_link, ad_title, ad_body, contact_link)
VALUES (1, 0, '', '', '', '')
ON DUPLICATE KEY UPDATE ads_enabled = VALUES(ads_enabled);
