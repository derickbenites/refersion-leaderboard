# PLANNING.md — Refersion leaderboard

Ordered backlog. **One task at a time.** Each completed task should become one semantic commit (unless noted).

Status legend: `todo` · `doing` · `done` · `blocked`

---

## Phase 0 — Spec lock

| ID | Status | Task | Suggested commit |
|----|--------|------|------------------|
| P0.1 | done | Capture CONTEXT / AGENT / PLANNING for SDD | `docs: add CONTEXT AGENT and PLANNING` |
| P0.2 | done | Lock stack and workflow decisions | `docs: lock open decisions` |
| P0.3 | done | Init git root with docs in D:\repositories\refersion-leaderboard | `docs: add CONTEXT AGENT PLANNING and lock stack decisions` |

### Locked decisions

- [x] Laravel + PHP 8.2+ (current stable at scaffold time)
- [x] React + TypeScript + Tailwind
- [x] MySQL for Laravel defaults (no invented persistence for the leaderboard)
- [x] Implementer prepares work; **human approves each commit**
- [x] Local: `artisan serve` + Vite proxy (Docker OK if PHP missing on host)
- [x] Hard stop **90 minutes** once coding starts

---

## Phase 1 — Repository skeleton

| ID | Status | Task | Suggested commit |
|----|--------|------|------------------|
| P1.1 | done | Root `.gitignore` | `chore: add root gitignore` |
| P1.2 | done | Scaffold Laravel app in `backend/` | `chore: scaffold laravel backend` |
| P1.3 | done | Scaffold React+Vite+TS+Tailwind in `frontend/` | `chore: scaffold react vite frontend` |
| P1.4 | done | Root README quick start | `docs: add root README` |
| P1.5 | done | `AI_USAGE.md` template | `docs: add AI usage template` |

---

## Phase 2 — Backend (Laravel)

| ID | Status | Task | Suggested commit |
|----|--------|------|------------------|
| P2.1 | done | Env keys + `.env.example` for Refersion OAuth/API | `chore: add refersion env placeholders` |
| P2.2 | done | Refersion OAuth token service | `feat: exchange refersion oauth token` |
| P2.3 | done | List affiliates with pagination stop rule | `feat: paginate refersion affiliates` |
| P2.4 | done | Fetch affiliate custom fields | `feat: fetch affiliate custom fields` |
| P2.5 | done | Eligibility filter exact `Display in leaderboard` / ` TRUE` | `feat: filter leaderboard eligibility` |
| P2.6 | done | Rank by `performance.total.commission` and take top 5 | `feat: rank top five affiliates` |
| P2.7 | done | `GET /api/leaderboard` JSON endpoint + error handling | `feat: expose leaderboard api` |
| P2.8 | todo | Feature/unit test for exact `" TRUE"` eligibility | `test: eligibility requires leading-space TRUE` |

---

## Phase 3 — Frontend (React + Vite)

| ID | Status | Task | Suggested commit |
|----|--------|------|------------------|
| P3.1 | todo | API client pointing at Laravel (dev proxy) | `feat: add frontend api client` |
| P3.2 | todo | Leaderboard table UI (rank, id, name, commission) | `feat: render leaderboard table` |
| P3.3 | todo | Loading and error states | `feat: handle leaderboard loading and errors` |

---

## Phase 4 — Hardening and delivery

| ID | Status | Task | Suggested commit |
|----|--------|------|------------------|
| P4.1 | todo | End-to-end manual run against live API; note results in README | `docs: record live leaderboard run` |
| P4.2 | todo | Fill `AI_USAGE.md` honestly | `docs: complete AI usage log` |
| P4.3 | todo | Document unfinished work / next steps | `docs: note unfinished work` |
| P4.4 | todo | Push public repo (verify no secrets in history) | `chore: prepare public release` |

---

## Explicitly deferred (only if time remains after P4.1)

- Extra Tailwind polish / accessibility pass
- Artisan command mirror of the HTTP endpoint
- Retry/`Retry-After` for HTTP 429
- Caching affiliates between requests

---

## Definition of done

1. `GET /api/leaderboard` returns eligible top ≤5 sorted correctly
2. React table displays the same data
3. Eligibility test passes for exact `" TRUE"`
4. Public GitHub repo without secrets
5. README + AI_USAGE filled; unfinished work listed if any
