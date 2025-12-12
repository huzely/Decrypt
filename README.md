# Cổng tin tức + bot Telegram

Triển khai website PHP/MySQL với quảng cáo Shopee và bot Telegram quản trị chạy trên Fly.io.

## Cấu trúc
- `.htaccess`: rewrite slug/id
- `config.php`, `functions.php`, `track_view.php`, `issue_token.php`, `shopee_redirect.php`
- `index.php`, `post.php`, `includes/`
- `assets/css`, `assets/js`
- `admin/`: đăng nhập, dashboard, CRUD, thống kê, cài đặt, giao diện
- `telegram_bot/`: bot Python Flask + Dockerfile + fly.toml

## Cài database
```sql
SOURCE database.sql;
```
Sửa `config.php` với thông tin MySQL và TELEGRAM_BOT_TOKEN.

## Deploy web
Upload toàn bộ mã nguồn lên hosting PHP 7.4+ hoặc 8.x, tạo thư mục `uploads` writable. Bật mod_rewrite (htaccess).

## Bot Telegram trên Fly.io
```bash
cd telegram_bot
fly launch --no-deploy
fly deploy
```
Cấu hình biến môi trường trên Fly:
```
fly secrets set DB_HOST=... DB_NAME=... DB_USER=... DB_PASS=... TELEGRAM_BOT_TOKEN=...
```
Đặt webhook Telegram:
```
https://api.telegram.org/bot<token>/setWebhook?url=https://<app>.fly.dev/webhook
```

## Sử dụng
- Truy cập `/admin/login.php` (admin/ Admin@12345)
- Cập nhật cài đặt, logo/banner, CSS trong admin.
- Khi người dùng click bài/video: hiển thị overlay quảng cáo, mở tab bài với `skip_ad=1` và tab hiện tại đi Shopee qua `shopee_redirect.php` (kiểm tra token, chống bot, log click, gửi Telegram).
- Dashboard admin hiển thị PV, unique, Shopee click, biểu đồ 7 ngày.

## Bảo mật & chống bot
- PDO prepared statements
- Token quảng cáo lưu bảng `ad_tokens` với delay 2s, kiểm tra IP/UA, rate-limit 3 click/10 phút, lọc UA bot.
- click_logs ghi valid/invalid + reason.
