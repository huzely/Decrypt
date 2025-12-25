# Tin tức PHP + MySQL

Website báo tin tức (frontend + admin) viết thuần PHP 8.x, chạy tốt trên shared hosting Apache + MySQL. Không dùng `.env`; mọi cấu hình nằm trong `config.php`.

## Tính năng chính
- 3 theme giao diện (A/B/C) đổi nhanh trong trang Admin → Cài đặt.
- Bài viết hỗ trợ text/ảnh/iframe Telegram embed.
- Interstitial Shopee hợp lệ: nút chính đi Shopee (target `_blank`), nút phụ bỏ qua đọc bài; có tần suất hiển thị theo session/giờ/số bài.
- Tracking pageview, click Shopee (lọc bot/UA), thống kê ngày/tháng, export CSV.
- Admin: đăng nhập (bcrypt), chống brute-force, CSRF token, CRUD bài viết, cài đặt hệ thống, upload logo/banner.
- Telegram bot: gửi khi publish bài hoặc khi có click Shopee (tùy chọn).
- Setup wizard: nhập thông số DB → tạo bảng + admin → sinh `config.php`.

## Cấu trúc thư mục
- `public/`: Front controller `index.php`, asset CSS/JS.
- `app/Core`: Router, DB, bảo mật, view.
- `app/Controllers`: Controller frontend & admin.
- `app/Models`: Model truy vấn PDO.
- `app/Views`: Template PHP (frontend/admin + partials).
- `database/schema.sql`: Schema MySQL.
- `setup.php`: Trang cài đặt nhanh.
- `docs/DEPLOYMENT.md`: Hướng dẫn deploy Apache/shared hosting.

## Cài đặt nhanh (local hoặc shared hosting)
1. Upload toàn bộ mã nguồn lên hosting (document root chứa `.htaccess` và thư mục `public`).
2. Truy cập `https://domain/setup.php`, nhập thông tin DB, base URL, tài khoản admin.
3. Sau khi thấy thông báo thành công, xóa `setup.php` trên hosting.
4. Đăng nhập Admin tại `https://domain/admin`.

## Chạy thủ công (không dùng setup wizard)
1. Copy `config.example.php` → `config.php`, điền thông tin DB/base URL.
2. Import `database/schema.sql` vào MySQL.
3. Tạo tài khoản admin: `INSERT INTO users (name,email,password) VALUES ('Admin','admin@example.com', PASSWORD_HASH_HERE);` (dùng `password_hash` PHP).
4. Đặt DocumentRoot trỏ vào thư mục gốc repo, bật mod_rewrite (đã có `.htaccess`).

## Ghi chú bảo mật
- Luôn thay `security.csrf_key` trong `config.php`.
- Upload logo/banner chỉ chấp nhận PNG/JPG/WEBP < 2MB.
- Luôn xóa `setup.php` sau khi cài đặt.

## License
MIT
