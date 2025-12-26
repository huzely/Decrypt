# Tin tức PHP cho shared hosting (dataonline.vn)

## Cấu trúc & upload
- Giữ nguyên thư mục `public_html/` và upload thẳng vào hosting dataonline.vn (đúng tên public_html).
- File cấu hình duy nhất: `public_html/config.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS, BASE_URL, SESSION_SALT, CACHE_PATH, CACHE_TTL, TELEGRAM_BOT_TOKEN, TELEGRAM_ADMIN_CHAT_ID).
- Import `sql/database.sql` vào MySQL (có seed admin + bài mẫu).
- Đảm bảo `cache/` có quyền ghi (777 nếu cần).
- Apache mod_rewrite bật sẵn: `.htaccess` map `/slug` → `post.php?slug=...` và bỏ qua `/admin|/api|/bots|/assets|/includes|/lib|/cache|/sql`.

## Thiết lập nhanh
1) Import DB: `sql/database.sql`.
2) Sửa `public_html/config.php` với thông tin DB + BASE_URL + SESSION_SALT (tùy chọn đổi CACHE_TTL).
3) (Tuỳ chọn) Điền TELEGRAM_BOT_TOKEN + TELEGRAM_ADMIN_CHAT_ID để bật notify.
4) Kiểm tra quyền ghi `cache/` và thư mục `assets/img` (upload logo/banner).

## Đăng nhập admin
- URL: `BASE_URL/admin/login.php`
- Tài khoản mẫu: `admin / admin567` (đổi ngay sau khi cài bằng UPDATE DB hoặc `password_hash` PHP).

## Acceptance test checklist
1) Slug: mở `/tin-nong-hom-nay` đúng bài.
2) Quảng cáo Shopee (ads_enabled=1, ad_link có):
   - Từ trang chủ hoặc truy cập trực tiếp `/slug`: overlay hiện ngay.
   - Click overlay/nút X/anywhere: tab hiện tại chuyển NGAY đến ad_link, đồng thời mở tab mới `/slug?ad=0` không quảng cáo, không delay.
3) Ads tắt: ads_enabled=0 hoặc ad_link rỗng → không overlay.
4) Chống click ảo: spam `/api/track.php` trong 30s không tăng vô hạn (rate limit + hash IP/UA + token phiên).
5) Theme: đổi theme 1/2/3 trong admin/settings, frontend thay CSS tương ứng.
6) Telegram notify: publish bài public → nhận tin “Bài mới”; ad_click tích lũy → gửi gộp tối thiểu mỗi 5 phút.
7) Responsive: kiểm tra mobile/laptop, menu hamburger 3D, admin/login cân đối.

## Lưu ý hiệu năng
- Cache file: settings (5 phút), home (60s), bài (60s, key theo slug+updated_at).
- Có thể đặt cache-control dài cho assets tĩnh.
- Không dùng .env, không cần composer, thuần PHP 7.4+/PDO.
