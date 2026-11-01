# KFS Portal — Project Overview & Stages

## Project Goal
Merge three separate systems (Archive, Meetings, Follow-up) into one unified portal —
**KFS Royal Egyptian** — for Kafr El-Sheikh Governorate: one login, one user table, one
permission set, one design system, with every system still fully functional and keeping its
original ideas and features.

## Tech Stack
- Laravel 12
- Filament 3 (panels for Archive, Archive Workspace, Meetings)
- Blade + Tailwind (compiled theme) for the Follow-up module
- nwidart/laravel-modules (Modules/Archive, Modules/Meetings, Modules/Followup)
- spatie/laravel-permission + bezhansalleh/filament-shield (role/permission UI)
- saade/filament-fullcalendar (Meetings calendar)

## Architecture Decisions
1. **One Laravel app** — no separate apps: one `users` table, one `web` guard, one Spatie
   permission set shared by all three systems.
2. **`/portal` is a custom Blade landing/hub page** (not a Filament panel) showing the three
   system cards, gated by permissions.
3. **Filament panels remain** for `/archive`, `/archive/workspace`, `/meetings`; shared
   defaults live in `app/Filament/Concerns/KfsPanelDefaults.php` (middleware, auth middleware,
   brand, design render hooks).
4. **Follow-up stays Blade** under `/followup` on its own compiled Tailwind theme, re-pointed
   to the unified palette via `public/css/kfs-followup.css` (loaded after `@vite`, no rebuild
   needed), forced light theme, theme toggle removed.
5. **`Gate::before`** lets `super_admin` bypass all checks. Registration creates an *inactive*
   `staff` user with no permissions (pending-approval flow only).
6. **Design system** (`public/css/kfs-royal.css`): RTL Arabic, light-first, Egyptian green
   primary (#144536 family), gold accents (#C9A227 family), red only for critical alerts,
   off-white surfaces (no pure #000/#FFF), flat geometric pharaonic motifs, no neon/SaaS
   gradients/emoji in official UI. The same identity is applied to landing, auth, and all
   three systems.
7. **Filament assets are published automatically** — `filament:assets` was added to composer
   `post-autoload-dump` / `post-update-cmd` because missing `public/js/filament/**` and
   `public/css/filament/**` silently 404 and break the sidebar/header/interactions.

## Routes
| Route | System | Tech |
|---|---|---|
| `/` | Landing page | Blade |
| `/login`, `/register`, `/forgot-password`, `/reset-password`, `/pending` | Auth | Blade |
| `/portal`, `/portal/profile` | Unified hub + profile | Blade |
| `/archive`, `/archive/workspace` (+ resources) | Archive | Filament panel |
| `/meetings` (+ resources, `/meetings/shield/roles`) | Meetings | Filament panel |
| `/followup/dashboard`, `/followup/tasks`, … | Follow-up | Blade module |

## Permission Matrix
| Role / Account | Portal | Archive | Meetings | Follow-up |
|---|---|---|---|---|
| `super.admin`, `portal.admin` | ✔ | ✔ | ✔ | ✔ |
| `archive.admin` / `.manager` / `.staff` | ✔ | ✔ | ✖ | ✖ |
| `meetings.admin` / `.manager` / `.staff` | ✔ | ✖ | ✔ | ✖ |
| `followup.manager` / `.supervisor` / `.staff` | ✔ | ✖ | ✖ | ✔ |
| `pending.user` (inactive) | login blocked | — | — | — |
| Guest | → `/login` (302) | 302 | 302 | 302 |

Full English account list with passwords: **`TEST_ACCOUNTS.md`**.
Seeded by `database/seeders/UserSeeder.php`.

## All Stages
| Stage | Name | Status |
|---|---|---|
| 1 | Core Setup & Archive System Migration | ✔ Completed |
| 2 | Meetings System Migration | ✔ Completed |
| 3 | Follow-up System Migration | ✔ Completed |
| 4 | Central Permissions & Portal Integration | ✔ Completed |
| 5 | Testing & Final Integration | ✔ Completed |
| 6 | Design Unification & Interaction Fixes | ✔ Completed |

## What Was Done (Stages 1–6)
- [x] Modular structure: `Modules/Archive`, `Modules/Meetings`, `Modules/Followup` (namespaces updated)
- [x] Unified `User` model with `FilamentUser` + `HasRoles`
- [x] Portal hub at `/portal` with permission-aware system cards + back-links everywhere
- [x] Full auth flows: login, register → `/pending`, forgot/reset password (verified end-to-end)
- [x] Spatie roles/permissions + Shield role screens (`/meetings/shield/roles` — Shield plugin registered on the Meetings panel)
- [x] Shared panel defaults: full session/auth middleware so `Filament::auth()->user()` resolves (fixed resource 403s)
- [x] Published Filament JS/CSS assets (fixed dead sidebar/header) + automated via composer scripts
- [x] Design unification: `kfs-royal.css` on landing/auth/portal/Archive/Meetings; `kfs-followup.css` override on Follow-up (green/gold, light-only, official `logo.png`, Arabic branding, portal back-links in header + sidebar footer)
- [x] Per-role test accounts seeded and access matrix verified (200/403 as expected)
- [x] Admin sweep of all major screens in the three systems — all HTTP 200
- [x] `optimize:clear` + `view:cache` + final smoke test with `admin` and one demo account per system

## Current Stage
**All stages completed.** The portal is merged, designed, permissioned, and smoke-tested.

## Remaining / Nice-to-Have
- Visual browser pass (screenshots) of Archive/Meetings/Follow-up to confirm rendering, not just HTTP 200.
- Production `.env` (APP_URL, DB, mail) and HTTPS deployment.
- Remove/keep legacy `archive.demo` / `meetings.demo` / `followup.demo` accounts deliberately.
- Optional: rebuild Follow-up Tailwind theme natively to green/gold instead of the CSS override.

## Update Log
- **Oct 28, 2026** — Stages 1–3 completed (module migrations)
- **Oct 28, 2026** — Stage 4 completed (permissions + portal)
- **Oct 28, 2026** — Stage 5 completed (initial testing)
- **Oct 31, 2026** — Stage 6 completed: Filament assets republished (sidebar/header fix), panel middleware fix, Shield plugin registered, Follow-up design unified to green/gold light theme, test accounts + TEST_ACCOUNTS.md, full access-matrix and smoke tests passed.
