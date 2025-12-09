CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    thumbnail VARCHAR(255),
    video_url VARCHAR(255),
    shopee_link VARCHAR(255),
    external_url VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    meta_image VARCHAR(255),
    type ENUM('article','announcement','video') DEFAULT 'article',
    status ENUM('draft','published') DEFAULT 'draft',
    created_at DATETIME,
    updated_at DATETIME
);

-- Seed admin user: mật khẩu mặc định là 'admin123', hãy đổi sau khi cài đặt.
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$zVzzmN6D7P52gQxHqyV7IuKtQJR1uWoExKbq9j2zKfnHxSCaPUOOa');

-- Seed posts mẫu (tiếng Việt)
INSERT INTO posts (title, content, thumbnail, video_url, shopee_link, external_url, meta_title, meta_description, meta_keywords, meta_image, type, status, created_at, updated_at) VALUES
('Mẹo mua sắm cuối tuần', '<p>Danh sách ưu đãi hấp dẫn cho bạn đọc.</p>', NULL, NULL, 'https://shopee.vn/unlock-deeplink', 'https://example.com/uu-dai', 'Mẹo săn sale cuối tuần', 'Tổng hợp các deal hot cuối tuần dành cho bạn.', 'khuyến mãi, mua sắm, deal', NULL, 'article', 'published', NOW(), NOW()),
('Thông báo bảo trì hệ thống', '<p>Trang web sẽ bảo trì lúc 23h đêm nay.</p>', NULL, NULL, NULL, NULL, 'Thông báo bảo trì', 'Thời gian bảo trì hệ thống 23h - 01h.', 'thông báo, bảo trì', NULL, 'announcement', 'published', NOW(), NOW()),
('Video giới thiệu sản phẩm', '<p>Xem video hướng dẫn sử dụng sản phẩm mới.</p>', NULL, 'https://shopee.vn/unlock-deeplink-video', 'https://shopee.vn/unlock-deeplink-video', 'https://example.com/video', 'Video demo sản phẩm mới', 'Video giới thiệu tính năng sản phẩm.', 'video, sản phẩm', NULL, 'video', 'published', NOW(), NOW());
