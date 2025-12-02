# Link wrapper Shopee → Telegram

Ứng dụng web mini xây dựng bằng Flask cho phép bọc link chuyển hướng qua Shopee trước khi tự động mở Telegram sau 5 giây. Hệ thống bao gồm trang chính hiển thị mọi link đã bọc và trang quản trị có đăng nhập/đăng kí, thống kê click, meta tag và tuỳ chỉnh giao diện.

## Tính năng
- **Trang chính**: liệt kê toàn bộ link đã bọc và tổng số click.
- **Quản trị**: đăng kí/đăng nhập/đăng xuất, bảng điều khiển thống kê click theo ngày/tháng, danh sách link và lịch sử click mới nhất.
- **Tạo & chỉnh sửa link**: cấu hình URL Shopee/Telegram, slug tùy chọn, nội dung hiển thị, meta title/description, tải ảnh meta để giả lập thẻ chia sẻ.
- **Chuyển hướng kép**: khi người dùng mở link sẽ nhảy vào Shopee, sau 5 giây tự chuyển tới Telegram.
- **Thông báo Telegram**: gửi thông báo khi tạo link mới và khi có click (gồm trình duyệt, IP, hệ điều hành, số lần truy cập của IP đó).
- **Tuỳ biến giao diện**: chỉnh màu sắc và nội dung hero cho trang chính/trang quản trị.

## Cài đặt nhanh
1. Cài đặt thư viện:
   ```bash
   pip install -r requirements.txt
   ```
2. Thiết lập biến môi trường (tuỳ chọn) cho Telegram:
   ```bash
   export TELEGRAM_BOT_TOKEN="<token>"
   export TELEGRAM_CHAT_ID="<chat_id>"
   export SECRET_KEY="change-me"
   ```
3. Chạy server:
   ```bash
   python app.py
   ```
4. Truy cập `http://localhost:5000/admin/register` để tạo tài khoản đầu tiên.

Uploads ảnh meta sẽ được lưu trong `static/uploads/`.
