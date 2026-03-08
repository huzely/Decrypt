CREATE DATABASE IF NOT EXISTS genzmovie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE genzmovie;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  status ENUM('active','banned') DEFAULT 'active',
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  original_title VARCHAR(255) DEFAULT '',
  description TEXT,
  poster VARCHAR(255),
  year INT,
  country VARCHAR(120),
  director VARCHAR(255),
  actors TEXT,
  quality VARCHAR(20) DEFAULT 'HD',
  language VARCHAR(30) DEFAULT 'Vietsub',
  tags VARCHAR(255),
  type VARCHAR(50) DEFAULT 'single',
  is_featured TINYINT(1) DEFAULT 0,
  meta_title VARCHAR(255),
  meta_description VARCHAR(255),
  meta_keywords VARCHAR(255),
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) UNIQUE NOT NULL,
  slug VARCHAR(80) UNIQUE NOT NULL
);

CREATE TABLE movie_genres (
  movie_id INT NOT NULL,
  genre_id INT NOT NULL,
  PRIMARY KEY (movie_id, genre_id),
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
  FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

CREATE TABLE episodes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  server_name VARCHAR(80) DEFAULT 'Server 1',
  episode_number VARCHAR(20) NOT NULL,
  embed_link TEXT,
  mp4_link TEXT,
  subtitle_link VARCHAR(255),
  created_at DATETIME,
  updated_at DATETIME,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

CREATE TABLE watch_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  movie_id INT NOT NULL,
  episode_id INT NULL,
  watched_at DATETIME,
  INDEX idx_user_watched (user_id, watched_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  movie_id INT NOT NULL,
  created_at DATETIME,
  UNIQUE KEY uniq_fav (user_id, movie_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  movie_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

CREATE TABLE ads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  position ENUM('header','sidebar','popup','video_preroll','footer') NOT NULL,
  ad_type ENUM('adsense','custom_html','script') NOT NULL,
  ad_code TEXT NOT NULL,
  status TINYINT(1) DEFAULT 1,
  created_at DATETIME,
  updated_at DATETIME
);

CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) UNIQUE NOT NULL,
  setting_value TEXT
);

INSERT INTO admins (name, email, password, created_at, updated_at)
VALUES ('Administrator', 'admin@genzmovie.local', '$2y$10$3M5v0HcObv7pwzR4zuI3d.8kd8V6VgP9SN5w2em4fS5s95x80nA4.', NOW(), NOW());
