CREATE DATABASE IF NOT EXISTS avstube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE avstube;

CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    thumbnail VARCHAR(255) NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    type ENUM('embed', 'mp4') NOT NULL DEFAULT 'embed',
    duration VARCHAR(20) DEFAULT '00:00',
    views INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    username VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'admin'
);

CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('banner', 'google', 'popup') NOT NULL,
    image TEXT,
    link TEXT,
    status TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT
);

CREATE TABLE IF NOT EXISTS video_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    view_date DATE NOT NULL,
    daily_views INT NOT NULL DEFAULT 0,
    UNIQUE KEY uniq_video_date (video_id, view_date),
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, role)
VALUES ('admin', '$2y$12$GuDy3X/fduWUNM2SbMvp9uVCG4kT35GJx/mEQj3q0BuWftU7dN7pq', 'admin')
ON DUPLICATE KEY UPDATE username = VALUES(username);

INSERT INTO videos (title, description, thumbnail, video_url, type, duration, views) VALUES
('Cảnh quay đẹp chất lượng cao', 'Video demo cho AVSTube', 'https://via.placeholder.com/300x160/111/ccc?text=Video+1', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'embed', '14:32', 1800000),
('Nội dung hấp dẫn full HD', 'Video demo cho AVSTube', 'https://via.placeholder.com/300x160/111/ccc?text=Video+2', 'https://www.youtube.com/embed/ysz5S6PUM-U', 'embed', '22:10', 950000),
('Khoảnh khắc đáng nhớ', 'Video demo cho AVSTube', 'https://via.placeholder.com/300x160/111/ccc?text=Video+3', 'https://www.youtube.com/embed/tgbNymZ7vqY', 'embed', '18:45', 720000),
('Trải nghiệm thú vị', 'Video demo cho AVSTube', 'https://via.placeholder.com/300x160/111/ccc?text=Video+4', 'https://www.youtube.com/embed/oUFJJNQGwhk', 'embed', '09:58', 1200000);
