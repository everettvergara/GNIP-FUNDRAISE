# Implementation Plan

Good Neighbors Philippines Fundraising Platform.

**Design:** [Canva prototype](https://raevergara.com/gnip-fundraising-home/fundraise-page)

---

## Goals

1. Match the Canva design exactly (same copy, same images)
2. Let campaign users create, manage, and share fundraising campaigns
3. Accept donations via Xendit (GCash, Maya, banks, cards)
4. Provide admin backend to manage content, campaigns, and users
5. Use standard Laravel only — no clever abstractions

---

## Tech stack

| Layer | Choice |
|-------|--------|
| Backend | Latest Laravel |
| Public / campaign UI | Blade + Tailwind + Alpine.js |
| Campaign auth | Laravel Breeze |
| Admin | Filament v5 |
| Database | MariaDB (local) / MySQL (production) |
| Payments | Xendit PHP SDK |

---

## Three application areas

| Area | Login | Layout |
|------|-------|--------|
| Public site | None | Marketing header + footer |
| Campaign user portal | `/login` | Simple top menu |
| Admin backend | `/admin/login` | Left sidebar + right content + avatar menu |

See [SITEMAP.md](SITEMAP.md) for full route trees.

---

## Brand colors (from Canva)

| Token | Hex |
|-------|-----|
| Primary text | `#685b55` |
| Secondary text | `#7a726f` |
| Accent green | `#8aa330` |
| Brand orange (Donate / fundraise CTA) | `#f17025` |
| Danger / CTA red | `#ff3131` |
| Warning yellow | `#ffde59` |
| Background | `#ffffff` |

Admin panel uses Calibri small fonts — see [ADMIN-UI.md](ADMIN-UI.md).

---

## Implementation phases

### Phase 0 — Documentation ✅

- [x] README.md
- [x] docs/PLAN.md, SITEMAP.md, SETUP.md, DATABASE.md
- [x] docs/CODING-STANDARDS.md, AUTH-WORKFLOWS.md
- [x] docs/ADMIN-SECURITY.md, ADMIN-UI.md

### Phase 1 — Foundation ✅

- [x] Laravel 13 installed
- [x] MariaDB `gnip_fundraise`
- [x] `.env` with port 8222
- [x] Breeze + Filament 5
- [x] Migrations + seeders

### Phase 2 — Public site ✅ (core)

- [ ] Extract Canva images to `public/images/design/` (pending)
- [x] Home, FAQ, legal, sectors, partners
- [x] Shared public layout

### Phase 3 — Campaign user portal ✅

- [x] Breeze auth (register/login/forgot/verify)
- [x] Dashboard, profile, change password (separate)
- [x] My campaigns, create/edit/share

### Phase 4 — Admin backend ✅ (core)

- [x] Filament `/admin` with GN theme
- [x] CMS, campaigns, users, donations, admins, activity logs

### Phase 5 — Donations (stub)

- [x] Donate form + pending donation record
- [ ] Xendit live integration (needs API keys)

### Phase 6 — BMS extensions

- [ ] Donor masterfile, sponsored children, matching
- [ ] Advanced reports and analytics

---

## Key workflows

### Fundraiser flow

Register → verify email → dashboard → create campaign → share → receive donations

### Donor flow

Browse campaigns → campaign page → Donate Now → Xendit checkout → receipt

### Admin flow

Login at `/admin/login` → manage content / campaigns / users from sidebar

---

## Coding rules

See [CODING-STANDARDS.md](CODING-STANDARDS.md).

- Standard Laravel MVC only
- Form Requests for validation
- Policies for authorization
- No repository layers or custom frameworks
- Profile and password on separate pages

---

## Related documents

| Doc | Content |
|-----|---------|
| [SITEMAP.md](SITEMAP.md) | All routes |
| [SETUP.md](SETUP.md) | Local dev setup |
| [DATABASE.md](DATABASE.md) | Schema and seeders |
| [AUTH-WORKFLOWS.md](AUTH-WORKFLOWS.md) | Login, register, reset |
| [ADMIN-SECURITY.md](ADMIN-SECURITY.md) | Admin modules and roles |
| [ADMIN-UI.md](ADMIN-UI.md) | Admin layout and theme |

---

## Out of scope for v1

- Video transcoding
- Native mobile apps
- Full BMS parity on day one (Phase 6)
