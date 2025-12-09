# Cổng thông tin + Bot Telegram

Triển khai portal PHP/MySQL hiển thị bài viết (ảnh, video Telegram) và bot Telegram quản trị.

## Cấu trúc thư mục
```
assets/
  css/style.css
  js/main.js
includes/
  header.php
  footer.php
admin/
  login.php
  logout.php
  dashboard.php
  posts.php
  post_add.php
  post_edit.php
  post_delete.php
  stats.php
  includes/{header.php,footer.php,auth_check.php}
uploads/
config.php
functions.php
index.php
post.php
shopee_redirect.php
telegram_webhook.php
Dockerfile
fly.toml
database.sql
README.md
```

## Cài đặt trên hosting shared
1. Tạo database MySQL, cập nhật thông tin trong `config.php` hoặc biến môi trường (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `BASE_URL`, `TELEGRAM_BOT_TOKEN`).
2. Import `database.sql` để tạo bảng và seed tài khoản admin (user: `admin`, pass: `password`).
3. Upload toàn bộ mã nguồn lên hosting, đảm bảo thư mục `uploads` cho phép ghi.
4. Trỏ webhook bot Telegram tới `https://yourdomain.com/telegram_webhook.php` bằng `https://api.telegram.org/botTOKEN/setWebhook?url=...`.
5. Đăng nhập admin tại `/admin/login.php` để quản lý bài viết.

## Triển khai bot Telegram trên Fly.io
- Sửa `Dockerfile` và `fly.toml` với domain/webhook phù hợp.
- Build & deploy:
```
flyctl launch --no-deploy
flyctl secrets set DB_HOST=... DB_NAME=... DB_USER=... DB_PASS=... TELEGRAM_BOT_TOKEN=...
flyctl deploy
```

## Yêu cầu popup Shopee
- `post.php` hiển thị nút "Xem video" khi không có `play=1`.
- Khi nhấn, `main.js` mở tab mới `post.php?id=ID&play=1` (autoplay video Telegram) và chuyển tab hiện tại tới `shopee_redirect.php?post_id=ID` để tăng thống kê + redirect Shopee.

## Seed data
- `database.sql` bao gồm 1 bài viết mẫu tiếng Việt, 1 admin và 1 admin Telegram.
