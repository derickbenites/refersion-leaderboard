# Refersion affiliate leaderboard

Small full-stack app that ranks affiliates from the Refersion API (`v2026-08`). The React app is separate from Laravel.

```
backend/     Laravel 13 JSON API
frontend/    React + Vite + TypeScript + Tailwind
```

Refersion Client ID and Client Secret belong in `backend/.env` only. Do not commit them.

## Prerequisites

- PHP 8.4.1+ and Composer, or Docker
- Node.js 22+ and npm

The locked dependencies refuse PHP 8.3. The scaffold database is SQLite, so MySQL is not required to boot the app.

## Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API: http://localhost:8000

Without PHP on the host, run Artisan from `backend/` in Docker:

```bash
cd backend
docker run --rm -it -v "${PWD}:/app" -w /app -p 8000:8000 php:8.4-cli php artisan serve --host=0.0.0.0 --port=8000
```

On Git Bash, prefix that command with `MSYS_NO_PATHCONV=1` so `/app` is not rewritten.

## Frontend

```bash
cd frontend
npm install
npm run dev
```

App: http://localhost:5173

`npm run build` typechecks and produces `frontend/dist/`.
