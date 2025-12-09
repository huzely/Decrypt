CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
    username VARCHAR(100),
    password_hash VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS click_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    clicked_at DATETIME,
    ip_address VARCHAR(100),
    user_agent VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS telegram_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telegram_chat_id BIGINT,
    name VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO admin_users (username, password_hash) VALUES ('admin', '$2y$10$gDnHZyjdrg0dp2PbgM3N6uHc0QKkU5C0uE8rcNwIcGVmU2EwHDYNO');
INSERT INTO telegram_admins (telegram_chat_id, name, is_active) VALUES (123456789, 'Admin', 1);
INSERT INTO posts (title, content, thumbnail, telegram_image_url, telegram_video_url, shopee_link, external_url, meta_title, meta_description, meta_keywords, meta_image, type, status, shopee_click_count, created_at, updated_at)
VALUES
('Giới thiệu sản phẩm mới', 'Nội dung bài viết demo với hình ảnh Telegram.', '/uploads/demo.jpg', 'https://telegram.org/img/t_logo.png', 'https://your-video-link.mp4', 'https://shopee.vn/demo', 'https://example.com', 'Meta tiêu đề demo', 'Mô tả meta ngắn gọn bằng tiếng Việt.', 'sản phẩm, khuyến mãi', '/uploads/meta.jpg', 'article', 'published', 0, NOW(), NOW());
