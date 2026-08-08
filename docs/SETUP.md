# Local Development Setup

## Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+ and npm
- MariaDB (local) or MySQL
- Git

## Database

Create the database before running migrations:

```sql
CREATE DATABASE IF NOT EXISTS gnip_fundraise
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

## Environment

Copy `.env.example` to `.env` after Laravel is installed, then set:

```env
APP_NAME="GNIP Fundraising"
APP_URL=http://localhost:8222
APP_PORT=8222

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gnip_fundraise
DB_USERNAME=root
DB_PASSWORD=your_local_password

MAIL_MAILER=log
```

Local MariaDB (development only — never commit `.env`):

| Setting | Value |
|---------|-------|
| Database | `gnip_fundraise` |
| Username | `root` |
| Password | Set in local `.env` only |

## Install (Phase 1 — after Laravel bootstrap)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan db:show
php artisan migrate --seed
npm install
npm run dev
```

## Run development server

Default port is **8222** (set `APP_PORT=8222` in `.env`).

```bash
php artisan serve --port=8222
```

Or, if `APP_PORT` is set in `.env`:

```bash
php artisan serve --port=%APP_PORT%
```

On Linux/macOS: `php artisan serve --port=$APP_PORT`

| Area | URL |
|------|-----|
| Public site | http://localhost:8222 |
| Campaign user login | http://localhost:8222/login |
| Admin panel | http://localhost:8222/admin/login |

## Google reCAPTCHA v3

reCAPTCHA is **optional**. Forms work normally until both keys are set in `.env`.

1. Create a reCAPTCHA v3 site at [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin).
2. Add your domains (e.g. `localhost` for local dev).
3. Set in `.env`:

```env
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
RECAPTCHA_MIN_SCORE=0.5
```

Both `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` must be present for verification to run. Leave them empty to disable reCAPTCHA entirely.

`VITE_RECAPTCHA_SITE_KEY` is derived from `RECAPTCHA_SITE_KEY` in `.env.example`. Rebuild frontend assets after changing keys: `npm run build` or `npm run dev`.

Tests skip reCAPTCHA verification via `RECAPTCHA_SKIP_VERIFY=true` in `phpunit.xml`.

## Xendit (Phase 5)

```env
XENDIT_SECRET_KEY=your_secret_key
XENDIT_WEBHOOK_TOKEN=your_webhook_verification_token
```

## Mail

Use `MAIL_MAILER=log` locally. Emails are written to `storage/logs/laravel.log`.

## Common commands

```bash
php artisan migrate:fresh --seed
php artisan route:list
npm run build
php artisan storage:link
php scripts/verify-seed.php
```

## Production bootstrap

After deploying and configuring `.env` on the server:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci && npm run build   # if frontend assets are not pre-built in deploy
php scripts/verify-seed.php
```

### Post-seed security (required)

The seeders create default accounts for demo and admin access. **Change these immediately after first login on live:**

| Account | Email | Default password | Where to log in |
|---------|-------|------------------|-----------------|
| Admin | `admin@goodneighbors.ph` | `password` | `/admin/login` |
| Demo fundraiser | `fundraiser@example.com` | `password` | `/login` |

Optionally deactivate or change the demo fundraiser account if you do not want a public demo login.

### What gets seeded

- Roles, admin account, campaign categories, sectors, CMS pages, partners (names only), email templates, site settings
- 5 demo campaigns with cover images copied into `storage/app/public/campaigns/`
- 2 sample confirmed donations per demo campaign

Static images for the hero banner, header/footer design, sector cards, and footer partner logos are already committed under `public/images/` and do not require seeding.

## Troubleshooting

**Database connection refused** — confirm MariaDB is running and `.env` credentials are correct.

**Storage permissions** — `chmod -R 775 storage bootstrap/cache`
