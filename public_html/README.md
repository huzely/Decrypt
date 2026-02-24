# Website tin tức đơn giản

Upload toàn bộ thư mục `public_html` lên hosting (DataOnline.vn), import `sql/database.sql`, cập nhật thông tin DB trong `config.php`. Sau khi import có sẵn tài khoản admin/admin567.

## Đường dẫn
- Trang chủ: `/`
- Trang bài: `/slug`
- Đăng nhập: `/admin/login.php`
- Trang quản trị một trang: `/admin/panel.php`

## Lưu ý
- Bật/tắt quảng cáo, link Shopee, link liên hệ nằm trong tab Cài đặt của panel.
- Khi quảng cáo bật và người dùng mở trực tiếp `/slug`, overlay sẽ xuất hiện ngay; đóng overlay sẽ mở tab mới `/slug?ad=0` và tab hiện tại chuyển sang link Shopee.
