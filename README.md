# AVSTube

Website chia sẻ video dùng **PHP 8 + MySQL + Bootstrap 5**.

## 1) Cấu trúc thư mục

```text
/project (repo root)
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── videos.php
│   ├── ads.php
│   ├── popup.php
│   ├── announcements.php
│   ├── settings.php
│   ├── auth.php
│   └── partials.php
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── uploads/
│   ├── thumbnails/
│   └── videos/
├── sql/avstube.sql
├── config.php
├── index.php
├── video.php
├── search.php
├── comment.php
└── README.md
```

## 2) Cài đặt

1. Tạo virtual host trỏ vào thư mục project.
2. Tạo database và import file SQL:
   ```bash
   mysql -u root -p < sql/avstube.sql
   ```
3. Cập nhật DB trong `config.php` nếu khác mặc định.
4. Cấp quyền ghi cho `uploads/`.
5. Truy cập website tại `/index.php`.

## 3) Tài khoản admin mặc định

- Username: `admin`
- Password: `admin2006`
- URL: `/admin/login.php`

## 4) Tính năng chính

- Giao diện dark theo mẫu HTML yêu cầu (Bootstrap + Font Awesome).
- Trang chủ grid video, hover scale, responsive.
- Xem video hỗ trợ `embed` và `mp4`.
- Bình luận theo video.
- Tìm kiếm video theo tiêu đề.
- Quản trị đầy đủ: dashboard/chart, video CRUD, ads, popup ads, announcements, settings.
- Popup quảng cáo full màn hình, redirect khi click/X, ẩn vĩnh viễn bằng `localStorage`.
- Prepared statements + validate input cơ bản.
