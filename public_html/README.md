# Báo điện tử PHP đơn giản

## Yêu cầu
- PHP 7.4+
- MySQL
- Hosting shared (không cần NodeJS, Docker)

## Cài đặt
1. Upload toàn bộ thư mục `public_html` lên hosting (gốc public của domain).
2. Tạo database MySQL và import file `sql/database.sql`.
3. Mở `app/config/config.php` và cập nhật:
   - Thông tin database (host, name, user, pass)
   - `base_url` theo domain
   - `session_secret`, `session_salt`
   - Token bot Telegram, danh sách admin ID
   - Cấu hình cache (bật/tắt, TTL)
4. Đảm bảo thư mục `app/cache` có quyền ghi.

## Chạy
- Trang chủ: `https://your-domain/`
- Trang bài: `https://your-domain/a/{id}-{slug}`

## Quản trị
- `https://your-domain/admin/`
- Tài khoản mặc định: `admin / admin123` (hãy đổi ngay mật khẩu trong database).
- Chức năng: thêm/sửa/xoá/copy bài, bật/tắt public, đổi logo/banner/màu, cấu hình quảng cáo Shopee, xem/reset thống kê.

## API tracking
- `api/track.php`: ghi nhận `article_view`, `ad_forced_redirect`, `ad_close_click` (giới hạn theo IP + token session).
- `api/stats.php`: trả về thống kê JSON.

## Bot Telegram
- Folder `telegram-bot/` (độc lập).
- Thiết lập webhook: `https://api.telegram.org/bot<token>/setWebhook?url=https://your-domain/telegram-bot/webhook.php`
- Lệnh hỗ trợ: `/stats`, `/reset`, `/add title|desc|body|media`, `/ad link|title|body`.
- Inline keyboard mẫu: `telegram-bot/keyboard.php`.

## Checklist kiểm thử quảng cáo Shopee
- Khi `ad_link` rỗng → không hiện popup.
- Khi có `ad_link`:
  1. Popup che bài.
  2. Click bất kỳ: tab hiện tại chuyển ngay đến Shopee (`ad_link`).
  3. Đồng thời mở tab mới `article_url?ad=0` và tab này không có quảng cáo.
  4. Sự kiện tracking `ad_forced_redirect` được ghi.

## Tối ưu hiệu năng
- Cache file tại `app/cache` (TTL mặc định 300s).
- Index DB: cột `published_at`, `is_public`, `event_type`, `ip_address`, `created_at`.
- Phân trang trên trang chủ.
- Lazy load hình ảnh (thuộc tính `loading="lazy"`).
