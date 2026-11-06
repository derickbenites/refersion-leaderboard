# AI usage

Disclosure for reviewers. AI assistance is allowed. This log names the harness, the model, and the request. It does not include Client ID, Client Secret, access tokens, or hidden system instructions.

| When | Harness | Model | Prompt | Outcome |
|------|---------|-------|--------|---------|
| 2026-11-06 | Cursor | Grok 4.7 | Scaffold Laravel 13 with the given Docker Composer flow. Do not migrate again and do not commit yet. | `backend/` with Laravel Framework 13.40.0 |
| 2026-11-06 | Cursor | Grok 4.7 | Smoke-check Artisan, keep Refersion credentials out of git, and commit only the backend scaffold. | `dfc6a25` |
| 2026-11-06 | Cursor | Grok 4.7 | Say whether `CONTEXT.md`, `AGENT.md`, and `PLANNING.md` are enough to finish the challenge. | Listed the missing response fields. The Refersion authentication guide and OpenAPI schema were read afterward. |
| 2026-11-06 | Cursor | Grok 4.7 | Store the supplied Refersion client credentials locally and keep them out of git. | Values written only to `backend/.env` |
| 2026-11-06 | Cursor | Grok 4.7 | Read the linked Refersion docs and report the affiliate and custom-field JSON. | Confirmed `data`, `page`, `limit`, `firstName`, `lastName`, `performance.total.commission`, and custom-field `label` / `value` |
| 2026-11-06 | Cursor | Grok 4.7 | Scaffold and commit the separate React, Vite, TypeScript, and Tailwind app. | `ff7838d` |
| 2026-11-06 | Cursor | Grok 4.7 | Add the root README quick start. | `e5bd6bf` |
| 2026-11-06 | Cursor | Grok 4.7 | Add the empty AI usage template. | `f068a4e` |
| 2026-11-06 | Cursor | Grok 4.7 | Add Refersion placeholders to `backend/.env.example`. | `627cbf5` |
| 2026-11-06 | Cursor | Grok 4.7 | Exchange the OAuth client credentials for a bearer token and reuse it until expiry. | `52563a9` |
| 2026-11-06 | Cursor | Grok 4.7 | List affiliates from page 1 and stop when a page is shorter than `limit`. | `a045450` |
| 2026-11-06 | Cursor | Grok 4.7 | Finish Phase 2: custom fields, the exact `TRUE` filter, the top five, `GET /api/leaderboard`, and the eligibility test. | `56e9867`, `ebbc981`, `c5b7bb5`, `f2f37c7`, `67e34d2`. A later pass removed a leading space that was not in the published challenge. |
| 2026-11-06 | Cursor | Grok 4.7 | Build Phase 3: the Vite proxy, the leaderboard table, and the loading and error states. | `e9865b4`, `4f19031`, `1d6ce10` |
| 2026-11-06 | Cursor | Grok 4.7 | Record the live run, complete this log, note unfinished work, and prepare the public repository. | This file, the README notes, and the Phase 4 commits |
