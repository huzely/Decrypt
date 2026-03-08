# GENZMOVIE

GENZMOVIE is a PHP + MySQL movie streaming website with OPhim auto crawler, admin dashboard, ad monetization, user accounts, watchlist/history, SEO routes, and responsive UI.

## Project Structure

```text
genzmovie/
├── config/
├── controllers/
├── models/
├── views/
├── assets/
├── uploads/
├── admin/
├── crawler/
├── api/
├── includes/
├── database/
├── index.php
└── .htaccess
```

## Features

- Netflix-style homepage with featured slider and category blocks.
- Search + autocomplete + filter by genre/year/country/quality.
- Movie details with metadata, episodes, and related movies.
- Player supports iframe embed and direct MP4/HLS source.
- Multiple server episode links.
- User register/login/logout with dashboard (favorites + watch history).
- Ad monetization: header/sidebar/popup/footer/video pre-roll placements.
- Admin dashboard for crawler trigger, ad management, overview of movies/users/comments.
- OPhim crawler imports movie metadata + episodes + links.
- SEO: friendly route `/phim/{slug}` and XML sitemap endpoint.
- Security basics: password hashing, prepared statements, CSRF token, output escaping.

## Installation Guide

1. **Copy files** into web root (Apache/Nginx + PHP 8+).
2. **Create database**:
   ```bash
   mysql -u root -p < genzmovie/database/schema.sql
   ```
3. **Update DB credentials** in `genzmovie/config/config.php`.
4. **Set document root** to project root or `/genzmovie` and enable mod_rewrite.
5. **Open website**:
   - Frontend: `http://localhost/genzmovie/`
   - Admin: `http://localhost/genzmovie/admin/login.php`
6. **Default admin account**:
   - Email: `admin@genzmovie.local`
   - Password: `admin123`

## Crawler

- Manual import: use **Import từ OPhim** button in `/admin`.
- Cronjob:
  ```bash
  */30 * * * * php /path/to/genzmovie/crawler/ophim_crawler.php
  ```

## Sitemap

- XML sitemap endpoint: `http://localhost/genzmovie/api/sitemap.php`

## Notes

- Add your Google AdSense / ad network code at admin ads form.
- For production, configure HTTPS, strict CSP, and hardened session settings.
