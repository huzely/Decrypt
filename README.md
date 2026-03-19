# NightFlix PHP Video Website

A shared-hosting-friendly video website built with pure PHP, MySQL, HTML, CSS, and JavaScript. It is structured for cPanel deployment and does not require Node.js.

## Folder structure

- `public_html/` - public pages (`index.php`, `video.php`, `upload.php`, `search.php`)
- `admin/` - session-based admin dashboard and CRUD pages
- `assets/` - CSS and JavaScript assets
- `includes/` - configuration, helpers, shared header, shared footer
- `uploads/` - reserved folder for future local file uploads
- `database.sql` - MySQL schema and seed data

## Features

### Public site
- Responsive dark mode homepage with YouTube-style video grid
- Search by title or tags
- Category and tag filters
- Video detail page with MP4 or iframe embed support
- View counter and related videos
- Comment system
- Upload form
- Popup ad (once per session), banner ads, and announcement area

### Admin panel
- Session-based login
- Dashboard with total videos, total views, and daily/monthly statistics tables
- Video CRUD
- Category CRUD
- Tag CRUD
- Ads CRUD with enable/disable
- Announcement edit/enable toggle
- Site settings for logo, primary color, and popup ads

## Default admin account

- Username: `admin`
- Password: `admin123`

## Deploy on cPanel / shared hosting

1. Create a MySQL database and user in cPanel.
2. Import `database.sql` using phpMyAdmin.
3. Upload the project files so that these folders exist in your hosting account:
   - `public_html/`
   - `admin/`
   - `assets/`
   - `includes/`
   - `uploads/`
4. Edit `includes/config.php` and update:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   - `BASE_URL` if your site is installed in a subdirectory
5. Open `public_html/index.php` in the browser.
6. Open `admin/login.php` to access the admin dashboard.

## Notes

- This project uses PDO and PHP sessions, which are supported on standard cPanel hosting.
- The popup ad is controlled by JavaScript and `sessionStorage`.
- `uploads/` is included for future file upload storage if you later switch from URL-based media to hosted files.
