# Link wrapper Shopee → Telegram (PHP + MySQL)

Ứng dụng web thuần PHP giúp bọc link: người dùng được đưa vào Shopee trước, sau 5 giây sẽ tự mở Telegram. Hệ thống có trang chính liệt kê link, khu vực quản trị với đăng nhập/đăng xuất cố định, thống kê click theo ngày & tháng, thông báo Telegram và tuỳ chỉnh meta tag + giao diện. Giao diện được dựng trực tiếp từ các file PHP (không dùng template `.html`).

## Thành phần & yêu cầu
- PHP 8.x có extension `pdo_mysql`
- MySQL hoặc MariaDB
- Web server hỗ trợ PHP (Apache/Nginx/LiteSpeed, hoặc host share hỗ trợ PHP)

## Cài đặt cơ sở dữ liệu
1. Tạo database, cập nhật thông tin kết nối trong `config.php`.
2. Chạy file `schema.sql` trên database để tạo bảng (`users`, `links`, `clicks`, `settings`).

## Cấu hình ứng dụng
Chỉnh trong `config.php`:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`: thông tin MySQL.
- `APP_URL`: URL gốc để tạo link đầy đủ cho người dùng copy.
- `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`: dùng để gửi thông báo khi tạo link mới và khi có click mới (gồm trình duyệt, IP, hệ điều hành, số lần truy cập của IP đó).

## Chạy trên hosting
1. Upload toàn bộ mã nguồn lên host PHP + MySQL.
2. Đảm bảo thư mục `uploads/` được phép ghi để lưu ảnh meta.
3. Trỏ domain về thư mục chứa `index.php`. Bật rewrite (.htaccess đã kèm) để link rút gọn hiển thị dạng `domain/slug` thay vì query `link.php?slug=...`. Các trang admin nằm tại `/admin/...`. Không cần thêm file `.html` riêng vì toàn bộ giao diện render từ PHP.
4. Đăng nhập tài khoản mặc định tại `/admin/login.php` với **admin / 123456** (tự động tạo hoặc khôi phục nếu chưa đúng trong DB).

## Tính năng chính
- **Trang chính**: hiển thị tất cả link đã bọc và số click, copy link nhanh theo định dạng `domain/slug`.
- **Chuyển hướng kép**: mở Shopee ngay, tự chuyển sang Telegram sau 5 giây; có nút mở lại thủ công.
- **Quản trị**: đăng nhập/đăng xuất với tài khoản cố định admin/123456 (tự động được tạo và đồng bộ khi chạy), tạo/sửa/xoá link, upload ảnh meta, tuỳ chỉnh meta title/description và nội dung hiển thị.
- **Thống kê**: bảng điều khiển tổng click, click theo ngày và tháng, top link, click mới nhất.
- **Thông báo Telegram**: gửi khi tạo link mới và khi có click mới kèm IP, trình duyệt, hệ điều hành, số lần IP đó truy cập; tự động bỏ qua bot/crawler để chỉ lấy người dùng thật.
- **Tuỳ chỉnh giao diện**: thay đổi tiêu đề trang, hero title/subtitle, màu sắc chủ đạo và ghi chú admin cho cả trang chính và admin.

## Cấu trúc chính
- `index.php`: trang công khai liệt kê link.
- `link.php`: trang chuyển hướng Shopee → Telegram, giả lập meta tag.
- `admin/`: đăng nhập, dashboard, quản lí link, tuỳ chỉnh giao diện.
- `uploads/`: lưu ảnh meta người quản trị tải lên.

## Ghi chú bảo mật
- Thiết lập quyền ghi đúng cho `uploads/`.
- Đặt mật khẩu mạnh khi tạo tài khoản quản trị.
- Cấu hình HTTPS trên hosting để tránh lộ thông tin đăng nhập.
