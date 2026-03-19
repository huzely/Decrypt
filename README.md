# NightFlix Fullstack Video Website

A complete fullstack video website built with React (Vite), Node.js/Express, and MongoDB. The project includes a public video browsing experience plus an admin dashboard for content, ads, announcements, analytics, and site settings.

## Project structure

- `backend/` – Express API, MongoDB models, seeding script, analytics endpoints
- `frontend/` – React + Vite client with home page, detail page, upload page, and admin dashboard

## Features

### Public site
- Home page with responsive dark-mode video grid
- Search by title or tags
- Category and tag filters
- Video detail page with HTML5 player or iframe embeds
- Related videos
- Comment system
- Popup ad, header/middle/footer banner ads
- Announcement banner
- Upload page

### Admin panel
- Overview cards for total videos and total views
- Daily and monthly analytics charts
- CRUD for videos, categories, tags, ads, and announcements
- Site settings editor for branding and popup ads toggle

## Run locally

### 1) Backend
```bash
cd backend
cp .env.example .env
npm install
npm run seed
npm run dev
```

Backend runs on `http://localhost:3000`.

### 2) Frontend
```bash
cd frontend
npm install
npm run dev
```

Frontend runs on `http://localhost:5173`.

## MongoDB
Make sure MongoDB is running locally at the URI defined in `backend/.env`.

Default local URI:

```bash
mongodb://127.0.0.1:27017/video_site
```
