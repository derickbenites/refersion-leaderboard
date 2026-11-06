# CONTEXT.md — Refersion affiliate leaderboard

Living project context for humans and agents. Update when facts change. Do not put secrets here.

## 1. Purpose

Small full-stack app that builds an affiliate **leaderboard** from the Refersion API (`v2026-08`).

Built as a timed exercise (~**90 minutes**). Prefer clear decisions, verifiable correctness, a usable table UI, and notes on anything left unfinished.

**Stack (locked):** Laravel (PHP) + React (TypeScript) + Vite + Tailwind · MySQL available for Laravel defaults · public GitHub repo · React app separate from Laravel (`backend/` + `frontend/`).

## 2. Functional requirements (source of truth)

1. Fetch all affiliates via `GET /affiliates` (read `data`, paginate with `page` + `limit`; start at `page=1`; stop when a page returns fewer records than `limit`).
2. For each affiliate, fetch custom fields via `GET /affiliates/{affiliateId}/custom-fields` (read `data`).
3. Display the **top five** eligible affiliates in a **table**, ranked by `performance.total.commission` descending.
4. Eligibility: custom field with label **`Display in leaderboard`** and value **exactly** the string **`TRUE`**.

API:

- Base URL: `https://api.refersion.com/v2026-08`
- Auth: OAuth client credentials → Bearer token (https://refersion.dev/docs/api-reference/authentication/)
- Credentials: Client ID + Client Secret from the challenge materials → local `.env` only (never commit)

AI tools are allowed; document prompts, model, and harness in `AI_USAGE.md`.

Deliverable: **public GitHub repository**.

## 3. Non-goals

- Loosening the `"TRUE"` match (no trim/case-fold for eligibility)
- Inventing API response fields
- Building a full Refersion clone / admin panel
- Deploying to production (local run is enough unless time allows)
- Mixing unrelated exercises into this repository

## 4. Chosen product architecture

| Layer | Choice | Notes |
|-------|--------|-------|
| Backend | Laravel (PHP) | HTTP client for Refersion, leaderboard use-case, JSON API |
| Frontend | React + Vite + TypeScript + Tailwind (**separate** app) | Table UI consuming Laravel API |
| DB | MySQL (Laravel default) | Not required by the API brief for ranking; use for app defaults only — do not invent persistence requirements |
| Monorepo layout | `backend/` + `frontend/` | One public repo, two packages |

## 5. Constraints and quality bar

- Code organized, logical, industry standards
- Handle API responses and errors appropriately
- Secrets only in `.env` / CI secrets; `.env.example` placeholders only
- Semantic commits driven by `PLANNING.md` tasks (one focused task → one commit when practical)
- Spec-driven: do not implement a task until it is listed and marked ready in `PLANNING.md`
- Clean Code + SOLID without unnecessary layers

## 6. Known API quirks (verify during implementation)

- Affiliate `id` may arrive as a **number** — coerce safely for path segments
- Custom-field values may include near-misses (` TRUE`, `true`, etc.); eligibility remains **exact** `"TRUE"`

## 7. Related docs

- `AGENT.md` — how contributors and coding agents must work in this repo
- `PLANNING.md` — ordered tasks and commit map
- `README.md` — human quick start (created when scaffolding starts)
- `AI_USAGE.md` — AI disclosure for reviewers

## 8. Locked decisions

- TypeScript + Tailwind on the frontend
- Semantic commits; human approves each commit before the next task
- Docs stay in the public repo with clear separation and a professional tone
- Hard stop at 90 minutes of implementation once coding starts
