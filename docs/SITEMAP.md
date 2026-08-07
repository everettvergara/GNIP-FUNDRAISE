# Sitemap

Good Neighbors Philippines Fundraising Platform — full route inventory.

**Design reference:** [Canva prototype](https://raevergara.com/gnip-fundraising-home/fundraise-page)

Ignore Canva's top navigation bar in the prototype — it is not part of this site.

---

## Three application areas

| Area | Login | Layout |
|------|-------|--------|
| Public site | None | Marketing header + footer (Canva design) |
| Campaign user portal | `/login` (Breeze) | Simple horizontal top menu |
| Admin backend | `/admin/login` (Filament) | Left sidebar menu + right content + top-right avatar |

Campaign users and admins use **separate guards, tables, and login URLs**.

---

## 1. Public sitemap

No authentication required.

```
/
├── /campaigns                          Browse Campaigns
│   └── /campaigns/{slug}               Public campaign page
│       ├── /campaigns/{slug}/donate    Donate → Xendit
│       └── /campaigns/{slug}/share       Share tools (auth required for owner)
├── /announcements                      Announcement and News
├── /our-sectors                        Our Sectors hub
│   ├── /our-sectors/education
│   ├── /our-sectors/health
│   ├── /our-sectors/child-protection
│   ├── /our-sectors/disaster-risk-reduction
│   ├── /our-sectors/economic-empowerment
│   └── /our-sectors/sustainable-environment
├── /partners                           Our Partners
├── /faq                                FAQ
├── /support                            Support Resources
├── /fundraising-tips                   Fundraising Tips and Best Practices
├── /terms-of-use                       Terms of Use
├── /terms-and-conditions               Terms and Conditions
├── /privacy-policy                     Data Privacy Policy
└── /donor-policy                       Donor Policy
```

### Public header menu

- Home
- Browse Campaigns
- FAQ
- Login
- **I want to fundraise** (CTA → register or create campaign)

### Public CMS pages (seeded, editable in admin)

| Slug | Title |
|------|-------|
| `/` | Fund Raising Main Page |
| `/faq` | FAQ |
| `/terms-of-use` | Terms of Use |
| `/terms-and-conditions` | Terms and Conditions |
| `/privacy-policy` | Data Privacy Policy |
| `/donor-policy` | Donor Policy |
| `/support` | Support Resources |
| `/fundraising-tips` | Fundraising Tips and Best Practices |
| `/our-sectors` | Our Sectors |
| `/announcements` | Announcement and News |
| `/partners` | Our Partners |

---

## 2. Campaign user portal

Authentication: Laravel Breeze (`auth`, `verified` middleware).

### Auth routes (guest)

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/register` | Registration form |
| POST | `/register` | Create account, send verification email |
| GET | `/login` | Login form |
| POST | `/login` | Authenticate |
| GET | `/forgot-password` | Request reset link |
| POST | `/forgot-password` | Send reset email |
| GET | `/reset-password/{token}` | New password form |
| POST | `/reset-password` | Save new password |
| GET | `/email-confirmation` | Verification notice |
| GET | `/verify-email` | Redirect to `/email-confirmation` |
| GET | `/verify-email/{id}/{hash}` | Verify email |
| POST | `/email/verification-notification` | Resend verification |
| POST | `/logout` | End session |

### Authenticated routes

| Route | Purpose |
|-------|---------|
| `/dashboard` | Campaign user dashboard (default after login) |
| `/profile` | Profile only — name, email, avatar (NO password) |
| `/account/change-password` | Change password only (NO profile fields) |
| `/account/settings` | Preferences, privacy settings |
| `/my-campaigns` | My Fundraising Pages |
| `/campaigns/create` | Create a Fundraise |
| `/campaigns/{slug}/edit` | Edit campaign |
| `/campaigns/{slug}/share` | Promotional tools |
| `/donations` | Donations received |
| `/donations/{id}/thank-you` | Customize thank-you message |

### Campaign user top menu

- Dashboard
- My Campaigns
- Create Campaign
- Donations
- Profile
- Logout

---

## 3. Admin backend

Authentication: Filament at `/admin/login` — `admins` table, `auth:admin` guard.

### Layout

- **Left:** Admin panel menu (sidebar)
- **Right:** Management pages (tables, forms, dashboard)
- **Top-right:** Avatar dropdown → Admin Profile, Change Password, Logout

### Admin routes

| Route | Purpose |
|-------|---------|
| `/admin/login` | Admin login (GN themed) |
| `/admin` | Admin dashboard |
| `/admin/profile` | Admin profile only — NO password |
| `/admin/change-password` | Change password only — NO profile fields |
| `/admin/logout` | End admin session |

### Left sidebar modules

**Content Management**

- `/admin/cms-pages`
- `/admin/announcements`
- `/admin/partners`
- `/admin/sectors`
- `/admin/email-templates`

**Campaign Management**

- `/admin/campaigns`
- `/admin/campaign-categories`

**User Management**

- `/admin/campaign-users`

**Donations and BMS**

- `/admin/donations`
- `/admin/donors`
- `/admin/sponsored-children`
- `/admin/matching`
- `/admin/recurring-donations`

**Security and Administration**

- `/admin/admins`
- `/admin/roles`
- `/admin/activity-logs`

**Settings**

- `/admin/settings`

---

## 4. Webhooks

| Method | Route | Purpose |
|--------|-------|---------|
| POST | `/webhooks/xendit` | Xendit payment confirmation |

---

## 5. Canva frame mapping

| Canva frame | Area | Route |
|-------------|------|-------|
| Fund Raising Main Page | Public | `/` |
| Browse Campaigns | Public | `/campaigns` |
| Fundraise Page | Public | `/campaigns/{slug}` |
| Login / Registration Page | Campaign user | `/login`, `/register` |
| Forgotten Password Page | Campaign user | `/forgot-password` |
| Email Confirmation | Campaign user | `/email-confirmation` |
| User Dashboard | Campaign user | `/dashboard` |
| Account Page | Campaign user | `/profile`, `/my-campaigns` |
| Create a Fundraise / Fundraise Form | Campaign user | `/campaigns/create`, `/campaigns/{slug}/edit` |
| Promotional Tools | Campaign user | `/campaigns/{slug}/share` |
| FAQ | Public + admin CMS | `/faq`, `/admin/cms-pages` |
| Sample email | Admin | `/admin/email-templates` |
| BMS / Coverage doc | Admin | `/admin/*` |

---

## 6. Email templates (not web pages)

Seeded and editable in admin:

- Account verification
- Password reset
- Campaign published / share-ready
- Donation receipt (donor)
- Donation received (fundraiser)
- Share-by-email sample
