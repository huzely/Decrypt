CREATE DATABASE IF NOT EXISTS video_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE video_site;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS videos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(500) NOT NULL,
  video_url VARCHAR(500) NOT NULL,
  type ENUM('mp4','embed') NOT NULL DEFAULT 'mp4',
  views INT NOT NULL DEFAULT 0,
  tags TEXT,
  category VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  video_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_video FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS view_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  video_id INT NOT NULL,
  ip VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_view_logs_video FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(500) NOT NULL,
  link VARCHAR(500) NOT NULL,
  position ENUM('popup','header','middle','footer') NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  site_name VARCHAR(255) NOT NULL,
  logo VARCHAR(500) DEFAULT '',
  primary_color VARCHAR(20) NOT NULL DEFAULT '#e50914',
  popup_ads_enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO categories (id, name, description) VALUES
(1, 'Trending', 'Most watched videos'),
(2, 'Featured', 'Editor picks'),
(3, 'Action', 'Fast-paced picks'),
(4, 'Drama', 'Story-rich uploads'),
(5, 'Music', 'Concert and clip content');

INSERT IGNORE INTO tags (id, name) VALUES
(1, 'hot'), (2, 'hd'), (3, 'viral'), (4, 'exclusive'), (5, 'night'), (6, 'cinema'), (7, 'trending');

INSERT IGNORE INTO videos (id, title, description, thumbnail, video_url, type, views, tags, category, created_at) VALUES
(1, 'Night Drop 1', 'Sample featured upload for the PHP version.', 'https://picsum.photos/seed/php-1/640/360', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'mp4', 1450, 'hot,hd,trending', 'Trending', NOW() - INTERVAL 1 DAY),
(2, 'Night Drop 2', 'Embed demo content.', 'https://picsum.photos/seed/php-2/640/360', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'embed', 1280, 'viral,exclusive', 'Featured', NOW() - INTERVAL 2 DAY),
(3, 'Night Drop 3', 'Action collection sample.', 'https://picsum.photos/seed/php-3/640/360', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'mp4', 980, 'night,cinema', 'Action', NOW() - INTERVAL 3 DAY),
(4, 'Night Drop 4', 'Drama sample video.', 'https://picsum.photos/seed/php-4/640/360', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'embed', 1660, 'hot,viral', 'Drama', NOW() - INTERVAL 4 DAY),
(5, 'Night Drop 5', 'Music sample video.', 'https://picsum.photos/seed/php-5/640/360', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'mp4', 2015, 'hd,cinema', 'Music', NOW() - INTERVAL 5 DAY),
(6, 'Night Drop 6', 'Another featured upload.', 'https://picsum.photos/seed/php-6/640/360', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'mp4', 1111, 'exclusive,trending', 'Featured', NOW() - INTERVAL 6 DAY),
(7, 'Night Drop 7', 'Action demo.', 'https://picsum.photos/seed/php-7/640/360', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'embed', 890, 'night,hot', 'Action', NOW() - INTERVAL 7 DAY),
(8, 'Night Drop 8', 'Trending clip.', 'https://picsum.photos/seed/php-8/640/360', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'mp4', 3200, 'viral,hd', 'Trending', NOW() - INTERVAL 8 DAY),
(9, 'Night Drop 9', 'Drama embed clip.', 'https://picsum.photos/seed/php-9/640/360', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'embed', 734, 'exclusive,cinema', 'Drama', NOW() - INTERVAL 9 DAY),
(10, 'Night Drop 10', 'Music upload demo.', 'https://picsum.photos/seed/php-10/640/360', 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4', 'mp4', 1744, 'trending,night', 'Music', NOW() - INTERVAL 10 DAY);

INSERT IGNORE INTO ads (id, image, link, position, active) VALUES
(1, 'https://picsum.photos/seed/php-header/1200/180', 'https://example.com/header', 'header', 1),
(2, 'https://picsum.photos/seed/php-middle/1200/180', 'https://example.com/middle', 'middle', 1),
(3, 'https://picsum.photos/seed/php-footer/1200/180', 'https://example.com/footer', 'footer', 1),
(4, 'https://picsum.photos/seed/php-popup/1280/720', 'https://example.com/popup', 'popup', 1);

INSERT IGNORE INTO announcements (id, title, content, active) VALUES
(1, 'Featured Release Tonight', 'New clips and trending videos are now live. Explore the latest uploads and editor picks.', 1);

INSERT IGNORE INTO settings (id, site_name, logo, primary_color, popup_ads_enabled) VALUES
(1, 'NightFlix PHP', 'https://picsum.photos/seed/php-logo/120/40', '#e50914', 1);

INSERT IGNORE INTO admin (id, username, password) VALUES
(1, 'admin', '$2y$12$6oyCc.4n3JyP8/fu9NhP3Oitk1ljqOs/qRgFcTZJagOULa8AMe1Nu');

INSERT INTO view_logs (video_id, ip, created_at) VALUES
(1, '127.0.0.1', NOW() - INTERVAL 1 DAY),
(1, '127.0.0.1', NOW() - INTERVAL 1 DAY),
(2, '127.0.0.1', NOW() - INTERVAL 2 DAY),
(3, '127.0.0.1', NOW() - INTERVAL 2 DAY),
(3, '127.0.0.1', NOW() - INTERVAL 3 DAY),
(4, '127.0.0.1', NOW() - INTERVAL 4 DAY),
(5, '127.0.0.1', NOW() - INTERVAL 15 DAY),
(6, '127.0.0.1', NOW() - INTERVAL 33 DAY),
(7, '127.0.0.1', NOW() - INTERVAL 34 DAY),
(8, '127.0.0.1', NOW() - INTERVAL 60 DAY);
