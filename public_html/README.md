# Báo tin tức PHP thuần

Triển khai cho hosting share (PHP 7.4, MySQL) với ba theme, quản trị tiếng Việt và flow quảng cáo Shopee không delay, giữ link affiliate s.shopee.vn.

## Cách cài đặt
1. Upload toàn bộ thư mục `public_html` lên hosting (dataonline.vn hoặc tương tự).
2. Import MySQL file `sql/database.sql` (đã seed admin: `admin / admin567`).
3. Sửa file `public_html/config.php` với thông tin DB, `BASE_URL`, token Telegram, `APP_DEBUG`.
4. Đảm bảo file `.htaccess` hoạt động để rewrite slug: `/slug` -> `post.php?slug=...`.
5. Phân quyền ghi cho thư mục `cache/` và `logs/`.

## Đăng nhập admin
- URL: `/admin/login.php`
- Tài khoản mặc định: **admin / admin567** (mật khẩu đã được hash trong DB).

## Kiểm tra nhanh sau khi deploy
- **Quảng cáo Shopee**: Bật quảng cáo ở `Admin > Cài đặt`, nhập link s.shopee.vn. Truy cập bài viết: overlay xuất hiện, bấm bất kỳ nơi nào hoặc nút X sẽ mở bài ở tab mới với `?ad=0` và tab hiện tại đi Shopee (không delay). Truy cập trực tiếp `/slug` vẫn thấy overlay trừ khi URL có `?ad=0`.
- **Theme**: Vào `Admin > Cài đặt`, đổi giữa Theme 1/2/3, toàn site đổi màu ngay.
- **Chống click ảo**: Tracking dùng token phiên + rate limit IP/UA/slug (30s) trong `/api/track.php`; spam gọi API sẽ nhận `rate_limited`.
- **Log lỗi**: Khi `APP_DEBUG=false`, lỗi được ghi vào `logs/app.log` và hiển thị `error.php` thân thiện (không trắng trang).

## Thư mục chính
- `index.php`: trang chủ, phân trang, menu 3D, trigger quảng cáo trên click bài.
- `post.php`: trang bài, meta SEO/OG, hiển thị media Telegram, overlay quảng cáo nếu bật.
- `assets/css`: `base.css` + `theme1/2/3.css` + `admin.css`.
- `assets/js`: `app.js` (UI + menu), `adflow.js` (overlay Shopee đúng flow), `track.js` (tracking không chậm redirect).
- `admin/`: đăng nhập, CRUD bài, copy link, cài đặt, thống kê (view/ngày/tháng/ad click, reset).
- `api/track.php`: nhận tracking page/post/ad với rate limit và token, gộp thông báo Telegram.
- `lib/`: PDO, auth, cache file, csrf, slugify, rate-limit, telegram, helpers, error handler.

## Lưu ý vận hành
- Ảnh/logo/banner được lưu trong `assets/img/` (nhẹ, phù hợp share hosting).
- Cache file cho trang chủ/bài ~45s; cài đặt cache 5 phút.
- Không sử dụng framework hay CLI/composer.
- Gửi Telegram: cấu hình token/chat_id trong `config.php`; hàng đợi + gộp click trong `telegram_queue` và `telegram_notifications`.
