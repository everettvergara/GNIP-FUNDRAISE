# Coding Standards

Standard Laravel only. Do not be clever.

## Principles

1. Use Laravel the way the official docs and Breeze/Filament scaffolds do it.
2. Thin controllers — business logic stays in models or Form Requests where appropriate.
3. No custom architecture layers unless Laravel docs recommend them.
4. Match existing code style in the file you are editing.

## Do use

| Pattern | Where |
|---------|-------|
| Controllers | `app/Http/Controllers/` |
| Form Requests | `app/Http/Requests/` — all validation |
| Eloquent Models | `app/Models/` with relationships |
| Policies | `app/Policies/` — authorization |
| Middleware | `auth`, `verified`, route groups |
| Blade views | `resources/views/` |
| Blade components | `resources/views/components/` |
| Mailables | `app/Mail/` |
| Migrations / Seeders | `database/migrations/`, `database/seeders/` |
| Filament Resources | `app/Filament/Resources/` — admin CRUD |
| Config / `.env` | Secrets and environment-specific values |

## Do not use

- Repository pattern or service layers for every operation
- Abstract base controllers or models “for flexibility”
- Action classes, DTO frameworks, or event sourcing
- Custom auth instead of Breeze / Filament
- Inline validation in controllers
- Hardcoded credentials or API keys
- Clever metaprogramming or non-obvious abstractions

## File structure

```
app/
  Http/Controllers/              # Public site
  Http/Controllers/CampaignUser/ # Dashboard, profile, my-campaigns
  Http/Requests/
  Models/
  Policies/
  Mail/
  Filament/Resources/
  Filament/Pages/
resources/views/
  layouts/public.blade.php
  layouts/campaign-user.blade.php
  campaigns/
  dashboard/
database/migrations/
database/seeders/
routes/web.php
routes/webhooks.php
```

## Routing

- Use named routes: `route('campaigns.show', $campaign)`
- Group authenticated campaign user routes:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
});
```

- Admin routes are handled by Filament at `/admin` — do not mix with `web.php` unless necessary.

## Auth separation

| Guard | Table | Login URL |
|-------|-------|-----------|
| `web` | `users` | `/login` |
| `admin` | `admins` | `/admin/login` |

Never use one guard for both user types.

## Profile vs password

Keep these on **separate pages** for both campaign users and admins:

| Page | Fields |
|------|--------|
| Profile | Name, email, avatar only |
| Change password | Current password, new password, confirm only |

## Database

- Use migrations for all schema changes
- Foreign keys with `constrained()` and `cascadeOnDelete()` where appropriate
- `$fillable` on all models — no `$guarded = []`
- Seeders for initial CMS content and default admin account

## When in doubt

1. Check [Laravel documentation](https://laravel.com/docs)
2. Copy the Breeze or Filament example
3. Adapt minimally
