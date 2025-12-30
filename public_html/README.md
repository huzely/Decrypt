# Website tin tức đơn giản

Upload toàn bộ thư mục `public_html` lên hosting, import file `sql/database.sql` vào MySQL, chỉnh thông tin DB trong `config.php`.

## Cấu trúc
- `index.php`: Trang chủ với nút liên hệ admin.
- `post.php`: Hiển thị bài viết theo slug và overlay quảng cáo.
- `admin/`: Đăng nhập và trang quản trị một trang.
- `api/track.php`: Ghi nhận lượt xem và click quảng cáo.
- `assets/`: CSS/JS.
- `lib/`: Thư viện chung (DB, auth, csrf, slugify, error handler, track).
- `sql/database.sql`: Tạo bảng và tài khoản admin mặc định (admin/admin567).
- `.htaccess`: Rewrite slug -> post.php.

## Đăng nhập admin
- URL: /admin/login.php
- Tài khoản mặc định: admin / admin567

## Ghi chú
- APP_DEBUG trong `config.php` khi false sẽ ghi log vào `logs/app.log`.
