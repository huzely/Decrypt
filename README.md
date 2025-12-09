# Cổng thông tin PHP (Admin & Frontend)

Dự án mẫu cổng thông tin PHP chạy được trên hosting shared (cPanel/DirectAdmin) với trang quản trị và trang người dùng.

## Cấu trúc thư mục
- `admin/`
  - `login.php`, `logout.php`, `dashboard.php`, `posts.php`, `post_add.php`, `post_edit.php`, `post_delete.php`
  - `includes/` (header, footer, auth_check)
- `includes/` (header/footer frontend)
- `assets/css/style.css`
- `uploads/` (thư mục lưu ảnh upload)
- `config.php`, `functions.php`, `upload_handler.php`
- `index.php`, `post.php`
- `database.sql` (tạo bảng + seed data)

## Cài đặt nhanh trên hosting
1. Tạo database MySQL mới và import file `database.sql`.
2. Sửa thông tin kết nối DB trong `config.php` (`$db`, `$user`, `$pass`).
3. Upload toàn bộ mã nguồn lên public_html (hoặc thư mục web root). Đảm bảo thư mục `uploads/` có quyền ghi.
4. Đăng nhập trang admin tại `/admin/login.php` với tài khoản mặc định `admin / admin123` (sau khi import SQL) và đổi mật khẩu ngay.
5. Tạo/sửa bài viết trong admin, chúng sẽ hiển thị ở trang chủ `/` và chi tiết `/post.php?id=ID`.

## Ghi chú bảo mật
- Đã sử dụng `password_hash` và `password_verify` cho tài khoản admin.
- Kiểm tra MIME khi upload, escape output chống XSS.
- Có thể chuyển `post_delete.php` sang xoá mềm bằng cách thay DELETE thành cập nhật status.

## Seed data mẫu
File `database.sql` có 3 bài viết tiếng Việt và 1 tài khoản admin mẫu để thử ngay.
