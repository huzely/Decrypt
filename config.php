<?php
// Cấu hình kết nối MySQL và Telegram
const DB_HOST = 'localhost';
const DB_NAME = 'link_wrapper';
const DB_USER = 'root';
const DB_PASS = '';

// URL gốc của ứng dụng (không có dấu gạch chéo cuối)
const APP_URL = 'http://localhost/link-wrapper';

// Thông báo Telegram
const TELEGRAM_BOT_TOKEN = '';
const TELEGRAM_CHAT_ID = '';

// Thư mục upload ảnh meta
const UPLOAD_DIR = __DIR__ . '/uploads';
const UPLOAD_BASE_URL = APP_URL . '/uploads';

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0775, true);
}
