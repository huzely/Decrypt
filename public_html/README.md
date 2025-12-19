# Báo điện tử PHP thuần (shared hosting)

## Công nghệ
- PHP 7.4+, MySQL, HTML/CSS/JS thuần
- PDO + prepared statements, không .env, không Node/Docker

## Triển khai
1. Upload toàn bộ thư mục `public_html` lên root hosting (DataOnline.vn hoặc tương tự).
2. Import database: `sql/database.sql` vào MySQL.
3. Cập nhật cấu hình tại `app/config/config.php`:
   - DB_HOST, DB_NAME, DB_USER, DB_PASS
   - BASE_URL (vd: `https://your-domain.com`)
   - SESSION_SALT (chuỗi ngẫu nhiên dài)
   - TELEGRAM_BOT_TOKEN, TELEGRAM_ADMIN_IDS
   - CACHE_PATH, CACHE_TTL
4. Đảm bảo thư mục `app/cache` có quyền ghi.
5. Bật mod_rewrite (Apache). `.htaccess` đã cấu hình: `/slug` -> `post.php?slug=...` và không ảnh hưởng `/admin`, `/api`, `/bots`.

## Chạy
- Trang chủ: `https://your-domain/`
- Bài viết: `https://your-domain/{slug}`
- Admin: `https://your-domain/admin/login.php` (mặc định: admin / admin123, hãy đổi mật khẩu trong DB)

## Quảng cáo Shopee (flow bắt buộc)
- Cấu hình trong Admin > Cài đặt: ad_link, ad_title, ad_body.
- Nếu `ad_link` rỗng: không hiện quảng cáo.
- Nếu có `ad_link`:
  - Click bài từ trang chủ: chặn mặc định, hiện overlay; click overlay hoặc nút X →
    - Ghi tracking `ad_forced_redirect` bằng sendBeacon
    - `window.open('/{slug}?ad=0', '_blank')`
    - `window.location.href = ad_link` (ngay, không delay)
  - Truy cập trực tiếp bài không có `?ad=0`: hiển thị overlay và hành vi tương tự; tab mới `?ad=0` không có quảng cáo.

## Tracking & chống click ảo
- Endpoint: `/api/track.php` nhận `event`, `slug`, `token` (session token).
- Sự kiện: `article_view`, `ad_forced_redirect`, `ad_close_click` (nếu cần).
- Giới hạn IP hash + User-Agent mỗi 60s; lưu hash với `SESSION_SALT`.
- Dùng `navigator.sendBeacon` (fallback fetch keepalive) để không chậm redirect.

## Telegram bot
- Folder: `bots/telegram/` với `webhook.php`, `bot.php`, `keyboard.php`.
- Thiết lập webhook: `https://api.telegram.org/bot<token>/setWebhook?url=https://your-domain/bots/telegram/webhook.php`
- Lệnh: `/stats`, `/reset`, `/add title|excerpt|content|media`, `/ad link|title|body`.
- Whitelist theo TELEGRAM_ADMIN_IDS.

## Hiệu năng
- Cache file: settings 5 phút, home 45s, article 45s tại `app/cache`.
- DB index: slug unique, is_public, published_at, click_events.event_type/slug/created_at.
- Lazy load ảnh, defer JS.

## Checklist kiểm thử
- Rewrite `/slug` hoạt động, `/admin` không bị rewrite.
- `ad_link` rỗng → không hiện overlay.
- `ad_link` có giá trị → overlay + redirect Shopee + tab mới `?ad=0` không có quảng cáo.
- Tracking ghi `article_view` và `ad_forced_redirect`.
- Admin CRUD, copy, publish/unpublish, cài đặt, reset thống kê.
- Telegram bot `/stats`, `/reset`, `/ad`, `/add` hoạt động với admin ID whitelisted.
