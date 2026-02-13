# Anonymous Matchmaking Chat Bot (FastAPI)

Production-oriented anonymous matchmaking backend with JWT auth, Redis queue matchmaking, trust/reporting logic, ad delivery, premium controls, and admin APIs.

## 1) Project Structure

```text
.
├── app
│   ├── api
│   │   ├── admin.py
│   │   ├── auth.py
│   │   ├── match.py
│   │   ├── user.py
│   │   └── ws.py
│   ├── core
│   │   ├── config.py
│   │   ├── deps.py
│   │   ├── redis_client.py
│   │   └── security.py
│   ├── db
│   │   ├── base.py
│   │   └── session.py
│   ├── models
│   │   └── models.py
│   ├── schemas
│   │   ├── auth.py
│   │   └── common.py
│   ├── services
│   │   ├── ads.py
│   │   ├── maintenance.py
│   │   ├── matchmaking.py
│   │   └── trust.py
│   ├── websocket
│   │   └── manager.py
│   └── main.py
├── alembic
│   ├── env.py
│   └── versions
│       └── 0001_initial.py
├── scripts
│   └── maintenance.py
├── .env.example
├── Dockerfile
├── docker-compose.yml
├── requirements.txt
└── alembic.ini
```

## 2) Core Features Included

- Daily verification via admin-managed token (`/admin/daily-token`, `/user/verify`)
- User profile fields: gender, preferred gender, trust score, premium, account status
- Matchmaking using Redis waiting queue with trust/gender/block checks
- Anonymous WebSocket chat per room (`/ws/chat/{room_id}?token=JWT`)
- Report flow that stores reports, penalizes trust, blocks pairing
- Ads served after session close for non-premium users
- Premium activation endpoint (`/user/premium/activate`)
- Admin APIs for stats, reports, trust distribution, ad CRUD, blocked pairs
- Rate limiting per IP via Redis
- JWT authentication + admin RBAC

## 3) Database Models

Implemented SQLAlchemy models:
- `Users`
- `ChatRooms`
- `Messages`
- `Reports`
- `BlockedPairs`
- `Ads`
- `DailyVerificationToken`
- `UserVerification`

See: `app/models/models.py`.

## 4) Configuration Reference

All key business rules are configurable in `app/core/config.py` (or via env vars):

- **Trust thresholds / penalties / regen**
  - `TRUST_COOLDOWN_THRESHOLD`
  - `TRUST_SUSPEND_THRESHOLD`
  - `TRUST_PENALTY_ON_REPORT`
  - `TRUST_REGEN_DAYS`
  - `TRUST_REGEN_AMOUNT`
- **Daily token behavior**
  - Token value set each day by admin endpoint: `POST /admin/daily-token`
  - User verification recorded in `user_verifications` table (`/user/verify`)
- **Ad logic**
  - Selection & impression update in `app/services/ads.py`
  - Delivery after chat close in `POST /match/rooms/{room_id}/close`
- **Premium benefits**
  - `PREMIUM_MATCHMAKING_BOOST` (priority queue)
  - `PREMIUM_SESSION_MINUTES` (longer sessions)
  - No ads for premium users in room close handler

## 5) Local Run Instructions

### Prerequisites
- Python 3.11+
- PostgreSQL
- Redis

### Setup

```bash
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
```

### Migrate DB

```bash
alembic upgrade head
```

### Start API

```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

Health check: `GET /health`

## 6) Docker Deployment Instructions

### Build & Run

```bash
docker compose up --build
```

### Run migrations in API container

```bash
docker compose exec api alembic upgrade head
```

App available at `http://localhost:8000`.

## 7) API Highlights

- `POST /auth/register`
- `POST /auth/login`
- `POST /user/verify`
- `POST /match/find`
- `POST /match/rooms/{room_id}/report`
- `POST /match/rooms/{room_id}/close`
- `GET /admin/stats/users`
- `GET /admin/stats/chats`
- `GET /admin/reports`
- `POST /admin/ads`, `PATCH /admin/ads/{ad_id}`, `DELETE /admin/ads/{ad_id}`
- `POST /admin/daily-token`
- `WS /ws/chat/{room_id}?token=<JWT>`

## 8) Operational Notes

- Schedule `python scripts/maintenance.py` every minute (cron/Celery/worker) to:
  - Auto-close expired rooms
  - Release completed cooldown/suspension windows
  - Apply trust regeneration checks
- Use HTTPS and rotate `SECRET_KEY` in production.
- Add centralized logging, tracing, and audit logs before going live.
