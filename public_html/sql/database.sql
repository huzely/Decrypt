CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(120) UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(190) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT,
  content MEDIUMTEXT,
  telegram_media TEXT,
  meta_title VARCHAR(255),
  meta_description VARCHAR(255),
  meta_keywords VARCHAR(255),
  status ENUM('draft','public') DEFAULT 'draft',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE site_settings (
  id INT PRIMARY KEY,
  site_name VARCHAR(255),
  site_description TEXT,
  logo_path VARCHAR(255),
  banner_path VARCHAR(255),
  theme ENUM('1','2','3') DEFAULT '1',
  ads_enabled TINYINT(1) DEFAULT 0,
  ad_link VARCHAR(255),
  ad_title VARCHAR(255),
  ad_body TEXT,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(190) NULL,
  event_type ENUM('page_view','post_view','ad_click') NOT NULL,
  ip_hash CHAR(64),
  ua_hash CHAR(64),
  token_hash CHAR(64),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX(event_type), INDEX(slug)
);

CREATE TABLE admin_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NULL,
  action VARCHAR(255),
  payload TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE telegram_queue (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50) UNIQUE,
  counter INT DEFAULT 0,
  last_sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password_hash) VALUES ('admin', '$2y$10$3bQ7N06j8VnYX3yvGN28M.dQ21FdWyF/pD2IJVBGLe7.tuMnIKPQe');
INSERT INTO site_settings (id, site_name, site_description, theme, ads_enabled, ad_title, ad_body) VALUES (1, 'Báo điện tử', 'Tin nhanh cho mọi người', '1', 0, 'Mua sắm Shopee', 'Ưu đãi hot chờ bạn!');
INSERT INTO articles (slug, title, excerpt, content, telegram_media, meta_title, meta_description, meta_keywords, status) VALUES
('tin-nong-hom-nay', 'Tin nóng hôm nay', 'Bản tin ngắn gọn', '<p>Nội dung bài mẫu.</p>', 'https://t.me/yourchannel/1', 'Tin nóng', 'Tin nóng hôm nay', 'tin,nong,hom,nay', 'public'),
('the-thao-sang', 'Thể thao sáng', 'Tin thể thao', '<p>Bài thể thao.</p>', 'https://t.me/yourchannel/2', 'Thể thao', 'Bản tin thể thao', 'the thao', 'public');
