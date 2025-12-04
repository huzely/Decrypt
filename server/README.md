# BO Trading Platform Backend (MVP)

Backend API for a binary options trading platform covering account auth, wallet funding, trading, and admin controls. Tech stack: **Node.js + Express**, **Sequelize (MySQL/SQLite)**, **Socket.io** for real-time quotes.

## Features
- JWT authentication: register, login, reset token stub, change password.
- Wallets: balance lookup, deposit orders, withdrawal requests, history.
- Trading: asset list, real-time price feed via Socket.io, place CALL/PUT orders with expirations, open & historical trades.
- Admin: manage users, approve/reject deposits & withdrawals, asset configuration (enable/disable, payout, bias), simple financial report.
- Protections: rate limits for login and trade placement, request logging with morgan.

## Getting Started
1. Install dependencies (use `npm config set registry https://registry.npmjs.org/` if needed):
   ```bash
   cd server
   npm install
   cp .env.example .env
   ```
2. Adjust `.env` for your MySQL instance (`DB_DIALECT=mysql`, `DB_HOST`, `DB_USER`, `DB_PASSWORD`). SQLite is the default for quick start.
3. Run the API:
   ```bash
   npm run dev
   ```
4. Socket.io price feed available at `ws://localhost:4000` with `price` events `{ symbol, price }`.

## API Outline
- `POST /api/auth/register` — email/username/password signup.
- `POST /api/auth/login` — JWT login (rate limited).
- `POST /api/auth/forgot` — generate reset token (stub).
- `POST /api/auth/reset` — reset password using token.
- `GET /api/auth/profile` — profile for logged-in user.
- `POST /api/auth/change-password` — change password.
- `GET /api/wallet/balance` — wallet balance.
- `POST /api/wallet/deposit` — create deposit order.
- `POST /api/wallet/withdraw` — submit withdrawal request.
- `GET /api/wallet/history` — deposits/withdrawals history.
- `GET /api/trade/assets` — enabled assets & payouts.
- `POST /api/trade/place` — place CALL/PUT trade with expiry seconds.
- `GET /api/trade/open` — open trades.
- `GET /api/trade/history` — trade history.
- `GET /api/admin/users` — list users (admin).
- `PUT /api/admin/users/:userId` — edit user (admin).
- `GET /api/admin/transactions` — list deposits/withdrawals (admin).
- `POST /api/admin/transactions/:id/decision` — approve/reject transaction; approve deposits credit wallet.
- `POST /api/admin/assets` — enable/disable assets, set payout or bias (admin).
- `GET /api/admin/report` — totals for deposits, withdrawals, and trading volume.

## Notes
- Price feed is simulated; extend `priceFeed` service to integrate with real market APIs.
- `bias` on assets lets admins tilt settlement up/down for testing or automation scenarios.
- Trade settlement is handled by the in-process scheduler; production setups should move this to a queue/worker.
