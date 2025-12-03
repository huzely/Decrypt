# Link Wrapper PHP (Shopee → Telegram)

Ứng dụng PHP/MySQL một file deploy cho hosting phổ thông, cho phép bọc link Shopee và tự chuyển sang Telegram sau 5 giây, có trang quản trị, thống kê click, fake meta tag và thông báo Telegram chỉ với user thật.

## Tính năng chính
- Trang chủ (`index.php`) liệt kê toàn bộ link đã bọc, nút sao chép link rút gọn dạng `domain/slug`.
- Trang admin (`/admin`) đăng nhập/đăng xuất (mặc định `admin/123456`), thêm/sửa link, reset lượt click từng link hoặc toàn bộ, chỉnh màu sắc và tiêu đề giao diện (áp dụng cho cả trang chủ và trang admin), thêm tài khoản admin mới.
- Redirect: mở Shopee ngay khi vào slug, sau 5 giây tự chuyển Telegram; hỗ trợ meta title/description/ảnh để giả lập preview đẹp.
- Thống kê tổng click, click theo ngày và tháng.
- Logging chỉ dành cho người dùng thật: bot Facebook/Telegram/Zalo có thể preview nhưng không ghi DB và không gửi thông báo Telegram. Việc ghi log chỉ chạy khi trình duyệt thực thi JavaScript gọi `/link.php?track=1`.
- Thông báo Telegram khi tạo link mới và khi có click mới (gồm IP, số lần IP truy cập, trình duyệt, hệ điều hành).

## Cấu trúc thư mục
- `index.php`: Trang chính + router slug.
- `link.php`: Trang redirect Shopee→Telegram và endpoint ghi nhận click người thật.
- `admin/` gồm `dashboard.php`, `login.php`, `logout.php`, `link_edit.php`, `settings.php`, `register.php`.
- `config.php`: thông số kết nối DB, token Telegram, tên site, mật khẩu admin mặc định (hash 123456).
- `db.php`, `helpers.php`, `bootstrap.php`: kết nối DB, khởi tạo bảng, hàm tiện ích dùng chung.
- `schema.sql` / `database.sql`: schema MySQL.
- `static/css/style.css`: giao diện đáp ứng cho mobile và laptop.
- `uploads/`: lưu ảnh meta (đính kèm `.gitkeep`).
- `.htaccess`: rewrite mọi slug về `index.php` để hỗ trợ đường dẫn `domain/slug`.

## Cài đặt
1. Tạo database trống (ví dụ `decrypt`).
2. Cập nhật thông tin trong `config.php` nếu cần: DB host/user/pass, token Telegram, chat id, tên site.
3. Import `schema.sql` hoặc `database.sql` vào MySQL. Nếu không import, ứng dụng sẽ tự tạo bảng khi truy cập lần đầu.
4. Đảm bảo thư mục `uploads/` có quyền ghi để lưu ảnh meta.
5. Upload toàn bộ mã nguồn lên hosting hỗ trợ PHP 7.4+ và mod_rewrite (để `.htaccess` hoạt động).

## Sử dụng
- Truy cập `/admin/login.php` để đăng nhập (admin/123456) và quản lý link.
- Nhấn "Tạo link mới" để thêm slug, link Shopee, link Telegram, meta title/description/ảnh.
- Sao chép link rút gọn tại bảng danh sách hoặc tại trang chủ.
- Khi chia sẻ `https://your-domain/slug`, bot preview không ghi log; người dùng thật mở sẽ bị chuyển Shopee rồi Telegram sau 5s, đồng thời ghi log và gửi thông báo Telegram.

## Yêu cầu môi trường
- PHP 7.4+ với PDO MySQL, session, file upload.
- MySQL/MariaDB.
- `file_get_contents` mở ra ngoài nếu muốn gửi Telegram.
