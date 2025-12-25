# Tin tức PHP cho shared hosting (dataonline.vn)

## Cấu trúc thư mục
- Upload toàn bộ thư mục `public_html` lên hosting (đặt đúng tên public_html).
- `app/config/config.php`: sửa thông tin DB, BASE_URL, SESSION_SALT, đường dẫn cache, token Telegram.
- `sql/database.sql`: import vào MySQL (có dữ liệu mẫu + tài khoản admin/admin123 thay đổi ngay).

## Cài đặt
1. Tạo database MySQL, import `sql/database.sql`.
2. Mở `app/config/config.php` và điền DB_HOST/DB_NAME/DB_USER/DB_PASS + BASE_URL.
3. Đảm bảo thư mục `app/cache` có quyền ghi.
4. Bật mod_rewrite trên Apache (dataonline.vn bật sẵn). `.htaccess` đã cấu hình slug `/slug` → `post.php?slug=...` và bỏ qua `/admin|/api|/bots|/assets|/includes|/app|/sql`.

## Đăng nhập admin
- URL: `BASE_URL/admin/login.php`
- Tài khoản mẫu: admin / admin123 (hãy đổi trong DB hoặc cập nhật mật khẩu bằng `password_hash`).

## Kiểm thử bắt buộc
- **Slug**: `/tin-nong-hom-nay` mở đúng bài.
- **Quảng cáo Shopee** (ads_enabled=1, ad_link có):
  - Click bài từ trang chủ hoặc truy cập trực tiếp `/slug` → overlay hiện.
  - Click overlay hoặc nút X: tab hiện tại chuyển ngay đến ad_link, đồng thời mở tab mới `/slug?ad=0` không quảng cáo.
- **Tắt quảng cáo**: ads_enabled=0 hoặc ad_link rỗng → không overlay.
- **Chống click ảo**: gửi nhiều lần `/api/track.php` trong 30s không tăng vô hạn (rate limit + token + hash IP/UA).
- **Theme**: đổi theme 1/2/3 trong admin/settings, frontend đổi CSS.
- **Telegram**: điền TELEGRAM_BOT_TOKEN + TELEGRAM_ADMIN_CHAT_ID rồi publish bài mới (status=public) hoặc có ad_click → Telegram nhận thông báo (gộp click, gửi tối thiểu mỗi 5 phút).
- **Responsive**: kiểm tra mobile/laptop, hamburger 3D mở menu.

## Lưu ý
- Không dùng .env, không cần composer. Thuần PHP/PDO.
- Cache file: settings (5 phút), home (60s), bài viết (60s).
- Assets đã thêm lazy-load (iframe/JS), nên đặt header cache-control dài hạn trên hosting nếu muốn.
- Nếu cần webhook Telegram, trỏ `bots/telegram/webhook.php` và bật HTTPS.
