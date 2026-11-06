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

## Live run

Checked on 2026-11-06 against `https://api.refersion.com/v2026-08`. On this machine port 8000 was already taken, so Laravel was published on `http://127.0.0.1:8001` and the Vite app on `http://127.0.0.1:5176` proxied `/api` to it.

`GET /api/leaderboard` returned:

```json
{
  "data": [
    { "rank": 1, "id": 8572723, "name": "Vella Braun", "commission": 431289 },
    { "rank": 2, "id": 8572704, "name": "Bradly Kuhic", "commission": 331475 }
  ]
}
```

The React table rendered those same two rows. Only two affiliates had the custom field `Display in leaderboard` with the value exactly ` TRUE`.
