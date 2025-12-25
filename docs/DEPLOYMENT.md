# Hướng dẫn deploy trên shared hosting (Apache + PHP 8.x)

## Yêu cầu
- PHP 8.x (enable PDO MySQL, fileinfo).
- MySQL 5.7+.
- Apache mod_rewrite bật sẵn.

## Các bước
1. **Upload code**: giải nén toàn bộ repo vào thư mục gốc website. Giữ nguyên `.htaccess`.
2. **Tạo database**: tạo database trống, user có quyền SELECT/INSERT/UPDATE/DELETE/CREATE.
3. **Chạy setup wizard**: vào `https://domain/setup.php`, nhập host/db/user/pass, base URL, tài khoản admin. Wizard sẽ import `database/schema.sql` và sinh `config.php`.
4. **Xóa file cài đặt**: sau khi thấy thông báo thành công, xóa `setup.php` để tránh bị lạm dụng.
5. **Kiểm tra rewrite**: truy cập `https://domain/slug-bat-ky` để chắc chắn mod_rewrite chuyển về `public/index.php`. `.htaccess` đã cấu hình:
   ```
   RewriteEngine On
   RewriteBase /
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ public/index.php [QSA,L]
   ```
6. **Phân quyền thư mục**: đảm bảo hosting cho phép ghi thư mục `public/uploads/` để lưu logo/banner.

## Backup/Restore
- **Backup DB**: `mysqldump -uUSER -p DBNAME > backup.sql`.
- **Backup code**: tải toàn bộ mã nguồn và `config.php`.
- **Restore**: import lại `backup.sql`, upload mã nguồn, cập nhật `config.php` nếu đổi thông tin DB.

## Cấu hình thêm
- **Đổi theme**: Admin → Cài đặt → chọn Theme A/B/C.
- **CSP gợi ý**: thêm vào Apache config nếu cần: `Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; frame-src https://t.me https://telegram.org;`.
- **Cron cache (tùy chọn)**: có thể cài cron xóa cache file tự xây nếu bổ sung.
