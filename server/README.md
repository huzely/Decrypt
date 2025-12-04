# BO Trading Platform (Node.js + Prisma + EJS)

Monolithic SSR app (user + admin) for binary options trading using internal CREDIT balance. Stack: Express, Prisma/PostgreSQL, Socket.io, EJS.

## Features
- User auth (register/login/logout), password reset token.
- Wallet with deposit/withdraw requests, history.
- BO trading with real-time prices, payouts, auto/manual payout rules, settlement engine.
- Admin backoffice: user management, approve deposits/withdrawals, asset CRUD + payout config, dashboard stats.
- Security: bcrypt passwords, JWT in httpOnly cookie, rate limits, helmet, input validation at server level.

## Setup
1. Install dependencies
```bash
npm install
```
2. Configure environment
```bash
cp .env.example .env
# edit DATABASE_URL, JWT_SECRET, SESSION_SECRET
```
3. Run Prisma
```bash
npx prisma generate
npx prisma migrate dev --name init
npm run seed
```
4. Development
```bash
npm run dev
```
5. Production (build then start)
```bash
npm run build
npm start
```

## VPS / HTTPS notes
- Use reverse proxy (Nginx) with SSL to terminate HTTPS and forward to `PORT`.
- Set `HTTPS_ENABLED=true` if you enable secure cookies.

## Default accounts
- Admin: `admin@example.com` / `Admin@123`
- Trader: `trader@example.com` / `User@123`
