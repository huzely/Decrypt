# Báo điện tử PHP thuần (shared hosting)

## Công nghệ & Kiến trúc
- PHP 8.x + MySQL, HTML/CSS/JS thuần, PDO prepared statements.
- Cấu hình qua `app/config/config.php` (không .env), cache file tại `app/cache`.
- SEO slug rewrite bằng `.htaccess` (không ảnh hưởng `/admin`, `/api`, `/bots`, `/uploads`).
- 3 theme (A/B/C) đổi nhanh trong admin, mobile-first, hiệu ứng 3D nhẹ.

## Cài đặt nhanh trên shared hosting (DataOnline.vn)
1. Upload toàn bộ `public_html` lên root hosting (Apache + mod_rewrite).
2. Tạo database MySQL, import `sql/database.sql`.
3. Tạo cấu hình: truy cập `https://your-domain/install.php` để nhập DB/BASE_URL (xong nên xóa file này). Hoặc sửa tay `app/config/config.php`.
4. Đảm bảo `app/cache` và `uploads` có quyền ghi (chmod 755/775 tùy hosting).
5. Đăng nhập admin: `https://your-domain/admin/login.php` (mặc định `admin / admin123`, hãy đổi mật khẩu trong DB), cấu hình tên site, logo/banner, theme, slug mode.

## Chạy & URL
- Trang chủ: `/`
- Bài viết: `/slug` hoặc `/slug-id` (chọn trong Cài đặt > Slug mode).
- Admin: `/admin/`
- API tracking: `/api/track.php`
- Bot webhook: `/bots/telegram/webhook.php`

## Tính năng frontend
- Trang chủ: banner, logo (ẩn nếu trống), tìm kiếm, lọc danh mục, phân trang.
- Trang bài: breadcrumbs, chia sẻ, meta/OG tiếng Việt, schema NewsArticle, media Telegram (ảnh/video link), bài liên quan.

## Quảng cáo Shopee (interstitial hợp lệ)
- Cấu hình trong Admin > Cài đặt: bật/tắt, link Shopee, title/body, tần suất (một lần/phiên, mỗi N giờ, mỗi N bài).
- Overlay có 2 nút: “Đi tới Shopee (Ưu đãi)” mở tab mới Shopee (noopener) + tracking, “Vào đọc bài (Bỏ qua)” đóng overlay đọc ngay. Không hijack click toàn trang.

## Tracking & chống click ảo
- Event: `article_view`, `ad_forced_redirect`, `ad_close_click`.
- IP+UA hash (SESSION_SALT) + rate limit cấu hình (mặc định 5 sự kiện/60s) + chặn UA bot regex.
- Gửi bằng `sendBeacon`/fetch keepalive nên không cản redirect.

## Admin
- Đăng nhập bảo vệ CSRF + khóa tạm khi sai quá số lần (cấu hình).
- CRUD bài (title, slug, meta, tags, category, media Telegram, draft/publish, copy), ping Telegram khi publish.
- Cài đặt: theme A/B/C, slug mode, logo/banner upload an toàn (image mime), màu sắc, hero text, Shopee ad, rate limit, UA bot regex, Telegram token/chat id.
- Thống kê: view/click, CTR, reset (log), export CSV.

## Telegram bot
- Thư mục `bots/telegram/` (bot.php, webhook.php, keyboard.php).
- Webhook: `https://api.telegram.org/bot<token>/setWebhook?url=https://your-domain/bots/telegram/webhook.php`
- Lệnh: `/stats`, `/reset`, `/add title|excerpt|content|media`, `/ad link|title|body`.

## Rewrite & bảo trì
- `.htaccess` đã chặn rewrite thư mục hệ thống (`admin|api|bots|assets|sql|app|includes|uploads`).
- Backup/restore: export DB, sao lưu `app/config/config.php`, `uploads/`, `app/cache` có thể xóa để rebuild.

## Checklist kiểm thử nhanh
- Cài DB bằng install.php, xóa file sau khi hoàn tất.
- Slug rewrite hoạt động cho /slug và /slug-id.
- Quảng cáo: rỗng link => không hiển thị; có link => overlay với 2 nút, click “Shopee” mở tab mới + tracking.
- Tracking ghi `article_view` và `ad_forced_redirect`, CTR hiển thị ở dashboard.
- Admin CRUD + copy, publish/draft, đổi theme/slug mode, upload logo/banner.
- Bot Telegram nhận `/stats`, `/reset`, `/ad`, `/add` với admin ID whitelist.
