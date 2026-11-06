# AGENT.md

Guidance for coding agents and contributors working in this repository
(Format inspired by https://github.com/agentmd/agent.md and https://agents.md/).

## Mission

Implement the Refersion leaderboard described in `CONTEXT.md` **exactly** as specified. Prefer correctness and clarity over features.

## Before writing code

1. Read `CONTEXT.md` and the active section of `PLANNING.md`.
2. Work **one** `PLANNING.md` task at a time.
3. Do not start a task marked blocked or not yet ready.
4. Wait for human approval of each commit before starting the next task.

## Spec-driven rules (SDD)

- Requirements live in `CONTEXT.md`.
- Execution order lives in `PLANNING.md`.
- If the brief and an assumption conflict, **brief wins**.
- If you need a decision not covered by CONTEXT/PLANNING, stop and ask the human — do not invent requirements.
- After finishing a task: mark it done in `PLANNING.md`, then propose a **semantic commit** matching the task id/title.

## Project structure (target)

```
/
  CONTEXT.md
  AGENT.md
  PLANNING.md
  README.md
  AI_USAGE.md
  backend/          # Laravel
  frontend/         # React + Vite + TypeScript + Tailwind
  .gitignore
```

## Commands (update after scaffold)

Backend:

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
php artisan test
```

Frontend:

```bash
cd frontend
npm install
npm run dev
npm run build
```

## Code style

- Clean Code + SOLID **without** unnecessary layers (no pattern for its own sake)
- PHP: PSR-12, typed properties/params/returns where practical, thin controllers, focused services for Refersion I/O
- React/TS: TypeScript strict; Tailwind; small presentational table; no unnecessary state libs
- Naming: clear domain words (`Affiliate`, `CustomField`, `Leaderboard`)
- No secrets in source, tests, or commit history

## Architecture patterns

- `RefersionClient` (or equivalent) owns HTTP + auth token lifecycle
- Pure function/service for eligibility + ranking (easy to unit test)
- Frontend only renders API results; business rules stay on backend

## Testing

- Mandatory: unit/feature test that eligibility requires exact `" TRUE"` (leading space)
- Nice: pagination stop condition; ranking order
- Prefer fast tests; mock HTTP when testing ranking/filter

## Security

- Never commit Client ID/Secret or access tokens
- Do not log full tokens or full PII dumps in README
- Public repo: scrub sample fixtures of unnecessary personal data

## Git / commits

- Follow `PLANNING.md` commit map
- Conventional Commits: `feat:`, `fix:`, `docs:`, `test:`, `chore:`
- One logical task per commit when possible

## Out of scope

- Changing eligibility rules without human approval
- Adding unrelated products/features
- Committing secrets
