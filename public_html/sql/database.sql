CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(200) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT,
  content LONGTEXT,
  meta_title VARCHAR(255),
  meta_description TEXT,
  meta_keywords TEXT,
  telegram_media MEDIUMTEXT,
  status ENUM('draft','published') DEFAULT 'draft',
  created_at DATETIME,
  updated_at DATETIME,
  INDEX idx_status_created (status, created_at)
);

CREATE TABLE site_settings (
  id INT PRIMARY KEY,
  site_name VARCHAR(255),
  logo VARCHAR(500),
  banner VARCHAR(500),
  theme_color VARCHAR(50),
  footer_text VARCHAR(500),
  ad_link VARCHAR(500),
  ad_title VARCHAR(255),
  ad_body TEXT,
  updated_at DATETIME
);

CREATE TABLE click_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(200) NULL,
  event_type VARCHAR(50),
  ip_hash VARCHAR(128),
  ua_hash VARCHAR(128),
  token_hash VARCHAR(128),
  created_at DATETIME,
  INDEX idx_event_created (event_type, created_at),
  INDEX idx_slug_event_created (slug, event_type, created_at)
);

CREATE TABLE admin_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  telegram_user_id BIGINT,
  action VARCHAR(100),
  payload TEXT,
  created_at DATETIME
);

INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$6LLq5yRvCNrI6VsgGAaHoOBuAZj0Kx3p83nvbA18J/nBsvcvZvCL6'); -- Admin@12345

INSERT INTO site_settings (id, site_name, logo, banner, theme_color, footer_text, ad_link, ad_title, ad_body, updated_at) VALUES
(1, 'Báo Điện Tử', '', '', '#0d6efd', '© 2024 Báo Điện Tử', '', '', '', NOW());

INSERT INTO articles (slug, title, excerpt, content, meta_title, meta_description, meta_keywords, telegram_media, status, created_at, updated_at) VALUES
('tin-nong-hom-nay', 'Tin nóng hôm nay', 'Tóm tắt nhanh tin nóng hổi.', '<p>Nội dung bài mẫu lấy từ Telegram.</p>', 'Tin nóng hôm nay', 'Mô tả tin nóng', 'tin,nong', '[{\"type\":\"image\",\"url\":\"https://t.me/sample_image1\"}]', 'published', NOW(), NOW()),
('cong-nghe-24h', 'Công nghệ 24h', 'Bản tin công nghệ cập nhật.', '<p>Nội dung công nghệ.</p>', 'Công nghệ 24h', 'Tin công nghệ', 'cong nghe', '[{\"type\":\"image\",\"url\":\"https://t.me/sample_image2\"}]', 'published', NOW(), NOW()),
('video-demo', 'Video demo Telegram', 'Xem video demo.', '<p>Video demo.</p>', 'Video demo', 'Video telegram', 'video,telegram', '[{\"type\":\"video\",\"url\":\"https://t.me/sample_video.mp4\"}]', 'draft', NOW(), NOW());
