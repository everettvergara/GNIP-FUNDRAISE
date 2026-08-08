# Database Schema

MariaDB / MySQL via Laravel `mysql` driver. Database name: `gnip_fundraise`.

Updated during Phase 1 when migrations are created.

---

## Auth tables (separate — do not merge)

### `users` — campaign users (fundraisers)

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `first_name` | string | |
| `last_name` | string | |
| `email` | string | unique |
| `email_verified_at` | timestamp | nullable |
| `password` | string | hashed |
| `avatar` | string | nullable, path to file |
| `is_active` | boolean | default true; false = suspended |
| `remember_token` | string | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

Guard: `web`. Login: `/login`.

### `admins` — GNIP staff

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `name` | string | |
| `email` | string | unique |
| `password` | string | hashed |
| `role` | string | super_admin, content_manager, etc. |
| `avatar` | string | nullable |
| `is_active` | boolean | default true |
| `remember_token` | string | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

Guard: `admin`. Login: `/admin/login`.

### `activity_logs` — admin audit

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `admin_id` | FK → admins | |
| `action` | string | |
| `model` | string | nullable |
| `model_id` | bigint | nullable |
| `changes` | json | nullable |
| `ip` | string | nullable |
| `created_at` | timestamp | |

---

## Campaign tables

### `campaign_categories`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `name` | string | e.g. Education, Health |
| `slug` | string | unique |
| `description` | text | nullable |
| `sort_order` | int | default 0 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `campaigns`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `user_id` | FK → users | owner |
| `category_id` | FK → campaign_categories | nullable |
| `title` | string | |
| `slug` | string | unique |
| `description` | text | |
| `goal_amount` | decimal(12,2) | |
| `raised_amount` | decimal(12,2) | default 0; auto-calculated from confirmed donations (monthly amount × commitment months for 3/6-month plans) |
| `cover_image` | string | nullable |
| `thank_you_message` | text | nullable |
| `status` | string | draft, pending, active, rejected, ended |
| `is_featured` | boolean | default false |
| `starts_at` | date | nullable |
| `ends_at` | date | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `campaign_media`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `campaign_id` | FK → campaigns | |
| `path` | string | |
| `type` | string | image, video |
| `sort_order` | int | default 0 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## Donation tables

### `donations`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `campaign_id` | FK → campaigns | |
| `donor_name` | string | |
| `donor_email` | string | |
| `amount` | decimal(12,2) | |
| `type` | string | one_time, recurring, recurring_3_months, recurring_6_months |
| `status` | string | pending, cancelled, confirmed_payment |
| `xendit_invoice_id` | string | nullable |
| `paid_at` | timestamp | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `payment_releases`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `campaign_id` | FK → campaigns | |
| `control_number` | string | unique, required |
| `amount_released` | decimal(12,2) | sum of tagged donations |
| `released_at` | date | |
| `released_by` | FK → admins | |
| `remarks` | text | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `donation_payment_release`

| Column | Type | Notes |
|--------|------|-------|
| `payment_release_id` | FK → payment_releases | |
| `donation_id` | FK → donations | unique — each donation released once |

### `recurring_donations`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `donation_id` | FK → donations | initial donation |
| `campaign_id` | FK → campaigns | |
| `xendit_plan_id` | string | |
| `status` | string | active, cancelled |
| `next_payment_at` | date | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## CMS tables

### `cms_pages`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `title` | string | |
| `slug` | string | unique |
| `body` | json | structured content blocks |
| `meta_title` | string | nullable |
| `meta_description` | text | nullable |
| `is_published` | boolean | default true |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `announcements`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `title` | string | |
| `slug` | string | unique |
| `excerpt` | text | nullable |
| `body` | text | |
| `image` | string | nullable |
| `is_published` | boolean | default true |
| `published_at` | timestamp | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `partners`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `name` | string | |
| `logo` | string | nullable |
| `url` | string | nullable |
| `sort_order` | int | default 0 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `sectors`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `name` | string | |
| `slug` | string | unique |
| `description` | text | |
| `image` | string | nullable |
| `sort_order` | int | default 0 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `email_templates`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `key` | string | unique — e.g. `verification`, `donation_receipt` |
| `subject` | string | |
| `body` | text | Blade-compatible |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## BMS tables

### `sponsored_children`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `reference_code` | string | unique |
| `first_name` | string | |
| `last_name` | string | |
| `date_of_birth` | date | nullable |
| `location` | string | nullable |
| `status` | string | active, matched, inactive |
| `notes` | text | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `donor_sponsor_matches`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `donation_id` | FK → donations | nullable |
| `sponsored_child_id` | FK → sponsored_children | |
| `status` | string | pending, approved, rejected |
| `reviewed_by` | FK → admins | nullable |
| `reviewed_at` | timestamp | nullable |
| `notes` | text | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## Relationships summary

```
users 1──* campaigns
campaign_categories 1──* campaigns
campaigns 1──* campaign_media
campaigns 1──* donations
donations 1──1 recurring_donations (optional)
admins 1──* activity_logs
sponsored_children 1──* donor_sponsor_matches
donations 1──* donor_sponsor_matches
```

---

## Seeder order

Run in this order (foreign key dependencies):

1. `RoleSeeder`
2. `AdminSeeder`
3. `CampaignDocumentTypeSeeder`
4. `CampaignCategorySeeder`
5. `SectorSeeder`
6. `CmsPageSeeder`
7. `PartnerSeeder`
8. `EmailTemplateSeeder`
9. `SiteSettingSeeder`
10. `CampaignSeeder` (requires admin, categories, and source images in `public/images/campaigns/`)

Command: `php artisan migrate --seed`

Design images (hero banner, logos, partner logos in the footer) live in `public/images/` and are **not** seeded to the database. Campaign cover images are copied from `public/images/campaigns/` into `storage/app/public/campaigns/` during `CampaignSeeder`. Run `php artisan storage:link` after seeding so campaign covers are web-accessible.
