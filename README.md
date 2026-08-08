# GNIP Fundraising

Good Neighbors Philippines fundraising platform — user-created campaigns, donations via Xendit, and admin content management.

**Design reference:** [Canva prototype](https://raevergara.com/gnip-fundraising-home/fundraise-page)

## Overview

This project has three separate application areas:

| Area | URL | Who |
|------|-----|-----|
| **Public site** | `/`, `/campaigns`, `/faq`, etc. | Visitors and donors |
| **Campaign user portal** | `/dashboard`, `/profile`, `/my-campaigns` | Fundraisers (Breeze auth at `/login`) |
| **Admin backend** | `/admin` | GNIP staff (Filament auth at `/admin/login`) |

Admin login and campaign user login are **completely separate** — different guards, tables, and URLs.

## Tech stack

- **Laravel** (latest stable)
- **Blade + Tailwind CSS + Alpine.js** (public site and campaign user portal)
- **Laravel Breeze** (campaign user auth)
- **Filament v3** (admin panel with left sidebar)
- **MariaDB / MySQL** (local dev uses MariaDB)
- **Xendit** (GCash, Maya, banks, credit cards)

## Documentation

| Document | Description |
|----------|-------------|
| [docs/PLAN.md](docs/PLAN.md) | Implementation plan and phases |
| [docs/SITEMAP.md](docs/SITEMAP.md) | Public, campaign user, and admin routes |
| [docs/SETUP.md](docs/SETUP.md) | Local development setup |
| [docs/DATABASE.md](docs/DATABASE.md) | Tables, relationships, seeders |
| [docs/CODING-STANDARDS.md](docs/CODING-STANDARDS.md) | Laravel conventions for this project |
| [docs/AUTH-WORKFLOWS.md](docs/AUTH-WORKFLOWS.md) | Registration, login, password reset flows |
| [docs/ADMIN-SECURITY.md](docs/ADMIN-SECURITY.md) | Admin modules, roles, security |
| [docs/ADMIN-UI.md](docs/ADMIN-UI.md) | Admin layout, GN theme, Calibri fonts |

## Quick start (after Laravel is installed)

See [docs/SETUP.md](docs/SETUP.md) for full instructions.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve --port=8222 --port=8222
```

## Implementation status

| Phase | Status |
|-------|--------|
| Phase 0 — Documentation | Complete |
| Phase 1 — Foundation (Laravel, DB, Filament) | Complete |
| Phase 2 — Public site | Complete (core pages) |
| Phase 3 — Campaign user auth + dashboard | Complete |
| Phase 4 — Admin backend | Complete (core modules) |
| Phase 5 — Donations (Xendit) | Stub (pending Xendit keys) |
| Phase 6 — BMS extensions | Partial |

## Default seeded accounts (after Phase 1)

Set during `php artisan migrate --seed`:

| Account | Email | Default password | Login URL |
|---------|-------|------------------|-----------|
| Admin | `admin@goodneighbors.ph` | `password` | `/admin/login` |
| Demo fundraiser | `fundraiser@example.com` | `password` | `/login` |

**Change these passwords immediately after first login on live.** See [docs/SETUP.md](docs/SETUP.md#post-seed-security-required) for the full production bootstrap checklist.

## License

Proprietary — Good Neighbors Philippines.
