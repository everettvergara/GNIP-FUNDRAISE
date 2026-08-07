# Authentication Workflows

Campaign user auth uses **Laravel Breeze** (standard scaffolds). Admin auth uses **Filament** at `/admin/login`. These are completely separate.

See also: [ADMIN-SECURITY.md](ADMIN-SECURITY.md) for admin login.

---

## Campaign user — registration

**Trigger:** User clicks Register or "I want to fundraise" while logged out.

| Step | Route | Action |
|------|-------|--------|
| 1 | `GET /register` | Show form: First name, Last name, Email, Password, Confirm password |
| 2 | `POST /register` | Validate, create `users` row, send verification email |
| 3 | `GET /email-confirmation` | Show: "A verification email has been sent…" |
| 4 | `GET /verify-email/{id}/{hash}` | Mark email verified (signed URL) |
| 5 | — | Redirect to `/dashboard` |

**Rules:**

- Email must be unique
- Password minimum 8 characters
- `verified` middleware required before creating campaigns
- Resend verification link on notice page

---

## Campaign user — login

**Trigger:** User clicks Login.

| Step | Route | Action |
|------|-------|--------|
| 1 | `GET /login` | Show email + password form, Forgot password link, Register link |
| 2 | `POST /login` | `Auth::attempt()` with rate limiting |
| 3a | Success + verified + active | Redirect to `/dashboard` |
| 3b | Success + unverified | Redirect to `/email-confirmation` |
| 3c | Success + suspended (`is_active = false`) | Show suspended message |
| 3d | Failure | Show validation error |

**Rules:**

- Remember me checkbox (Breeze default)
- Login throttling (Laravel default)
- Suspended users cannot log in

---

## Campaign user — forgot password

**Trigger:** User clicks "Forgot password?" on login page.

| Step | Route | Action |
|------|-------|--------|
| 1 | `GET /forgot-password` | Show email field |
| 2 | `POST /forgot-password` | `Password::sendResetLink()` |
| 3 | — | Show generic success message (do not reveal if email exists) |
| 4 | `GET /reset-password/{token}` | Show new password + confirm |
| 5 | `POST /reset-password` | Update password, invalidate sessions |
| 6 | — | Redirect to `/login` with success message |

**Rules:**

- Reset link expires in 60 minutes (Laravel default)
- User must log in again after reset

---

## Campaign user — logout

| Route | Action |
|-------|--------|
| `POST /logout` | Invalidate session, redirect to `/` |

---

## Campaign user — profile vs change password

These are **separate pages**. Do not combine.

| Page | Route | Fields |
|------|-------|--------|
| Profile | `/profile` | First name, last name, email, avatar |
| Change password | `/account/change-password` | Current password, new password, confirm |
| Account settings | `/account/settings` | Notifications, privacy; link to change password |

---

## Admin — login

**URL:** `/admin/login` only. Campaign users cannot use this URL.

| Step | Action |
|------|--------|
| 1 | Admin enters email + password |
| 2 | Filament authenticates against `admins` table |
| 3a | Valid + `is_active` | Redirect to `/admin` dashboard |
| 3b | Valid + inactive | Show deactivated message |
| 3c | Invalid | Show error |

**Logout:** `POST /admin/logout` → redirect to `/admin/login`

---

## Admin — profile vs change password

Separate from campaign user flows. Separate pages.

| Page | Route | Fields |
|------|-------|--------|
| Admin profile | `/admin/profile` | Name, email, avatar only |
| Change password | `/admin/change-password` | Current, new, confirm only |

Top-right avatar dropdown links to Profile, Change Password, and Logout separately.

Creating a new admin user (with initial password) is done in `/admin/admins` — that is user management, not the change-password page.
