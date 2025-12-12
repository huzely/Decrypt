CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) UNIQUE NULL,
    title VARCHAR(255),
    content LONGTEXT,
    thumbnail VARCHAR(500),
    telegram_image_url VARCHAR(500),
    telegram_video_url VARCHAR(500),
    shopee_link VARCHAR(500),
    external_url VARCHAR(500),
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    meta_image VARCHAR(500),
    type ENUM('article','announcement','video') DEFAULT 'article',
    status ENUM('draft','published') DEFAULT 'draft',
    shopee_click_count INT DEFAULT 0,
    last_shopee_click_at DATETIME NULL,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS click_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    clicked_at DATETIME,
    ip_address VARCHAR(100),
    user_agent VARCHAR(255),
    is_valid TINYINT(1) DEFAULT 1,
    reason VARCHAR(255) NULL
);

CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255),
    logo_url VARCHAR(500) NULL,
    banner_url VARCHAR(500) NULL,
    primary_color VARCHAR(50) NULL,
    footer_text VARCHAR(500) NULL,
    ad_title VARCHAR(255) NULL,
    ad_body TEXT NULL,
    updated_at DATETIME
);

CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_date DATE UNIQUE,
    pageviews INT DEFAULT 0,
    unique_visitors INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS visitor_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_date DATE,
    ip_address VARCHAR(100),
    user_agent VARCHAR(255),
    first_seen_at DATETIME,
    UNIQUE KEY uniq_visit (visit_date, ip_address)
);

CREATE TABLE IF NOT EXISTS telegram_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telegram_chat_id BIGINT UNIQUE,
    name VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS ad_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) UNIQUE,
    post_id INT,
    ip_address VARCHAR(100),
    user_agent VARCHAR(255),
    created_at DATETIME,
    valid_after DATETIME,
    used_at DATETIME NULL,
    is_used TINYINT(1) DEFAULT 0
);

INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$xx0g4VQnPjdLaEiERCQnhO4hgqP2.VShSsIyYQnWUj2Z5WDXPOt1S');

INSERT INTO site_settings (id, site_name, logo_url, banner_url, primary_color, footer_text, ad_title, ad_body, updated_at) VALUES
(1, 'Cổng tin tức Telegram', NULL, NULL, '#ff5722', '© 2024 Cổng tin tức Telegram', 'Ưu đãi Shopee', 'Giảm giá sốc khi mua ngay trên Shopee!', NOW());

INSERT INTO telegram_admins (telegram_chat_id, name, is_active) VALUES
(123456789, 'Admin Demo', 1);

INSERT INTO posts (slug, title, content, thumbnail, telegram_image_url, telegram_video_url, shopee_link, external_url, meta_title, meta_description, meta_keywords, meta_image, type, status, shopee_click_count, created_at, updated_at) VALUES
('khuyen-mai-lon', 'Khuyến mãi cực lớn', '<p>Đừng bỏ lỡ khuyến mãi cực lớn tuần này!</p>', '/uploads/demo1.jpg', 'https://telegram.org/img/t_logo.png', NULL, 'https://shopee.vn/demo', 'https://example.com', 'Khuyến mãi cực lớn', 'Săn sale hấp dẫn trên Shopee.', 'khuyen mai, giam gia', '/uploads/meta1.jpg', 'article', 'published', 0, NOW(), NOW()),
('video-mo-hop', 'Video mở hộp sản phẩm', '<p>Xem video mở hộp sản phẩm hot nhất.</p>', '/uploads/demo2.jpg', 'https://telegram.org/img/t_logo.png', 'https://your-video-link.mp4', 'https://shopee.vn/video', NULL, 'Video mở hộp', 'Video unbox cực chất.', 'video, unbox', '/uploads/meta2.jpg', 'video', 'published', 0, NOW(), NOW()),
('thong-bao-cap-nhat', 'Thông báo cập nhật', '<p>Cập nhật mới nhất từ kênh Telegram.</p>', '/uploads/demo3.jpg', 'https://telegram.org/img/t_logo.png', NULL, NULL, NULL, 'Thông báo cập nhật', 'Tin tức mới nhất.', 'thong bao, cap nhat', '/uploads/meta3.jpg', 'announcement', 'published', 0, NOW(), NOW());
