-- Database schema for news portal
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    failed_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content MEDIUMTEXT NOT NULL,
    media_url VARCHAR(255) DEFAULT NULL,
    category VARCHAR(120) DEFAULT '',
    tags VARCHAR(255) DEFAULT '',
    meta_title VARCHAR(255) DEFAULT '',
    meta_description VARCHAR(255) DEFAULT '',
    meta_keywords VARCHAR(255) DEFAULT '',
    og_image VARCHAR(255) DEFAULT '',
    is_public TINYINT(1) DEFAULT 0,
    published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_public (is_public),
    INDEX idx_published (published_at)
);

CREATE TABLE site_settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT
);

CREATE TABLE click_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    ip_hash CHAR(64) NOT NULL,
    session_token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event (event_type),
    INDEX idx_slug (slug),
    INDEX idx_created (created_at)
);

CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin (admin_id)
);

INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$Hgd6Nq35p3xNmPR9U1FVLemyYI7DiIP9N6byN1Nsx3Rp3XIanFkOu'); -- password: admin123

INSERT INTO site_settings (`key`, `value`) VALUES
('site_name', 'Tin Nóng 24H'),
('logo_url', 'https://telegram.org/img/t_logo.png'),
('banner_url', 'https://telegram.org/img/t_logo.png'),
('hero_text', 'Tin nhanh, chính xác, luôn cập nhật'),
('meta_description', 'Báo điện tử tối ưu cho hosting yếu'),
('primary_color', '#e63946'),
('secondary_color', '#1d3557'),
('theme', 'theme-a'),
('slug_mode', 'slug'),
('ad_enabled', '0'),
('ad_link', ''),
('ad_title', 'Deal hot Shopee'),
('ad_body', 'Mua sắm tiết kiệm nhất hôm nay!'),
('ad_frequency', 'once'),
('ad_interval_hours', '4'),
('ad_every_posts', '3'),
('rate_window_seconds', '60'),
('rate_max_events', '5'),
('bot_list', 'bot|crawl|spider|curl');

INSERT INTO articles (title, slug, excerpt, content, media_url, is_public, published_at) VALUES
('Chào mừng đến với bản demo', 'chao-mung-den-voi-ban-demo', 'Báo điện tử tối ưu cho hosting yếu.', 'Nội dung mẫu cho bài viết đầu tiên.', 'https://telegram.org/img/t_logo.png', 1, NOW()),
('Tin nóng hôm nay', 'tin-nong-hom-nay', 'Cập nhật liên tục các sự kiện nổi bật.', 'Nội dung chi tiết của tin nóng.', 'https://telegram.org/img/t_logo.png', 1, NOW());
