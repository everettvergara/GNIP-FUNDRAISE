# Admin Security and Modules

Admin backend requirements for GNIP Fundraising.

See also: [ADMIN-UI.md](ADMIN-UI.md) for layout and theme, [AUTH-WORKFLOWS.md](AUTH-WORKFLOWS.md) for login flows.

---

## Admin management modules

| Module | Filament Resource | Capabilities |
|--------|-------------------|--------------|
| Dashboard | Filament Dashboard page | Stats, charts, recent activity |
| CMS Pages | `CmsPageResource` | Edit static pages (home, legal, FAQ) |
| Announcements | `AnnouncementResource` | News items |
| Partners | `PartnerResource` | Partner logos and links |
| Sectors | `SectorResource` | Our Sectors content |
| Email Templates | `EmailTemplateResource` | System email content |
| Campaigns | `CampaignResource` | Approve, reject, feature, edit, delete |
| Campaign Categories | `CampaignCategoryResource` | Category management |
| Campaign Users | `CampaignUserResource` | View, suspend, activate, force password reset |
| Donations | `DonationResource` | List, export, reports |
| Donors | `DonorResource` | Donor masterfile |
| Sponsored Children | `SponsoredChildResource` | BMS masterfile |
| Matching | `DonorSponsorMatchResource` | Donor ↔ child matching |
| Recurring Donations | `RecurringDonationResource` | Subscription management |
| Admin Users | `AdminResource` | Create, edit, deactivate admins |
| Roles | Policy-based | Role assignment on admin records |
| Activity Logs | `ActivityLogResource` | Audit trail |
| Settings | `SettingsPage` | Site config, Xendit keys |

---

## Admin roles

Simple `role` column on `admins` table. Enforced via Laravel Policies.

| Role | Access |
|------|--------|
| `super_admin` | Full access including admin user management |
| `content_manager` | CMS, announcements, partners, sectors |
| `campaign_manager` | Campaigns, campaign users, categories |
| `finance` | Donations, donors, BMS matching, reports |
| `support` | View campaign users, force password reset, view donations |

Implementation: Policy `before()` method checks role. Filament `canViewAny()` delegates to policies.

No third-party RBAC package unless requirements grow beyond this.

---

## Security controls

| Control | Implementation |
|---------|----------------|
| Separate login | `/admin/login`, guard `admin`, table `admins` |
| Session isolation | Admin session independent from campaign user session |
| Password hashing | `Hash::make()` / bcrypt |
| CSRF | Laravel default on all forms |
| Rate limiting | Throttle admin login attempts |
| Account lockout | `is_active = false` on `admins` |
| Authorization | Policies per Filament Resource |
| Audit trail | `activity_logs` table — admin_id, action, model, changes, ip |
| HTTPS | Required in production |
| Secrets | `.env` only — Xendit keys, DB password, mail credentials |
| Mass assignment | `$fillable` on all models |
| File uploads | MIME and size validation |

---

## Campaign user management (admin actions)

From `/admin/campaign-users`:

| Action | Effect |
|--------|--------|
| View | Profile, registration date, campaigns, total raised |
| Suspend | `is_active = false` — cannot log in |
| Activate | Re-enable account |
| Force password reset | Send reset email to user |
| View campaigns | Link to user's campaigns in Campaign Management |

Prefer suspend over delete.

---

## Activity log

Table: `activity_logs`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `admin_id` | FK | Who performed the action |
| `action` | string | e.g. `created`, `updated`, `deleted`, `suspended` |
| `model` | string | Model class name |
| `model_id` | bigint | Record ID |
| `changes` | json | Before/after snapshot (optional) |
| `ip` | string | Request IP |
| `created_at` | timestamp | When |

Log on: campaign approval/rejection, user suspend/activate, admin create/edit, settings change.

---

## Default admin account

Seeded in `AdminSeeder` (Phase 1):

- Email: `admin@goodneighbors.ph`
- Role: `super_admin`
- Password: set in seeder — **change immediately after first login**

---

## What campaign users cannot do

- Access any `/admin/*` route
- Log in at `/admin/login`
- View other users' campaigns in the portal (only their own)

## What admins cannot do (by default)

- Log in at `/login` as a campaign user (separate table)
- Mix profile and password on one screen
