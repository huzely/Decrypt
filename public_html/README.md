# Báo điện tử PHP cho hosting shared (DataOnline.vn)

## Triển khai
1. Upload toàn bộ thư mục `public_html` lên hosting (đặt đúng tên thư mục gốc).
2. Tạo database MySQL và import `sql/database.sql`.
3. Mở `app/config/config.php` và cập nhật:
   - DB_HOST, DB_NAME, DB_USER, DB_PASS.
   - BASE_URL.
   - SESSION_SALT (chuỗi ngẫu nhiên).
   - TELEGRAM_BOT_TOKEN, TELEGRAM_ADMIN_IDS.
   - CACHE_PATH, CACHE_TTL nếu cần.
4. Đảm bảo thư mục `app/cache` có quyền ghi.
5. `.htaccess` đã cấu hình rewrite `/slug` -> `post.php?slug=...`. Không rewrite admin/api/bots.

## Admin
- Đăng nhập: `/admin/login.php` (seed `admin` / `Admin@12345`, nên đổi ngay).
- Chức năng: Dashboard, CRUD/Copy bài, cài đặt giao diện + quảng cáo, thống kê/reset.

## Quảng cáo Shopee (acceptance test)
1. Nếu `ad_link` rỗng: click bài mở thẳng, không overlay.
2. Nếu có `ad_link`:
   - Click bài ở trang chủ: tab mới mở `/{slug}?ad=0` (không quảng cáo), tab hiện tại hiển thị overlay.
   - Click overlay: ghi `ad_forced_redirect`, chuyển ngay tới ad_link (không delay).
   - Bấm ĐÓNG: overlay tắt, không redirect, ghi `ad_close_click`.
3. Trang bài với `?ad=0`: tuyệt đối không hiển thị quảng cáo.

## Tracking & chống spam
- Endpoint `/api/track.php`, event: `article_view`, `ad_forced_redirect`, `ad_close_click`.
- CSRF token theo session, hash IP/UA với SESSION_SALT, rate limit IP+slug+event (30s).
- Dùng `navigator.sendBeacon` / fetch keepalive, không chặn redirect.

## Telegram bot (PHP)
- Folder: `bots/telegram/`.
- Webhook: `https://yourdomain.com/bots/telegram/webhook.php`.
- Set webhook: `https://api.telegram.org/bot<token>/setWebhook?url=...`.
- Kiểm tra whitelist `TELEGRAM_ADMIN_IDS`, nếu không có quyền -> báo lỗi.
- Inline keyboard: quản lý bài, quảng cáo, thống kê, reset; lệnh hỗ trợ: `/start`, `/add`, `/publish`, `/unpublish`, `/adset`.

## Troubleshooting
- 404 slug: kiểm tra `.htaccess` hoặc quyền file.
- Không ghi cache/tracking: cấp quyền ghi `app/cache`.
- Bot không phản hồi: kiểm tra TOKEN, webhook URL, và whitelist ID.
