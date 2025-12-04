# BO Platform

Prototype codebase for a binary options (BO) trading website with Node.js/Express backend, Socket.io real-time pricing, and MySQL/Sequelize persistence. This repository currently contains the backend API in `server/` for authentication, wallet management, trading, and admin backoffice flows.

## Structure
- `server/` — Express API with JWT auth, wallet deposits/withdrawals, BO trade placement & settlement, and admin endpoints for approvals, assets, and reporting.

## Quick start
See [`server/README.md`](server/README.md) for setup, environment variables, and API usage.
