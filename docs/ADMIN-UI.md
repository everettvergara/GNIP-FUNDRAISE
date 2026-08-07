# Admin UI Specification

Filament admin panel at `/admin` — Good Neighbors branding.

---

## Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│  [GN Logo]                              [Admin Avatar ▼]            │
├──────────────┬──────────────────────────────────────────────────────┤
│              │                                                      │
│  LEFT        │  RIGHT — management pages                            │
│  Admin       │  (dashboard, tables, forms)                        │
│  Panel Menu  │                                                      │
│              │                                                      │
└──────────────┴──────────────────────────────────────────────────────┘
```

| Zone | Purpose |
|------|---------|
| **Left sidebar** | All module navigation (Filament `navigation` groups) |
| **Right content** | Dashboard widgets, Filament Resource pages |
| **Top bar** | GN logo (left), admin avatar menu (right) |

Sidebar is hidden on `/admin/login` until authenticated.

---

## Top-right avatar menu

Implemented via Filament `userMenuItems()` in the Panel provider.

| Item | Route | Content |
|------|-------|---------|
| Admin Profile | `/admin/profile` | Name, email, avatar — **no password fields** |
| Change Password | `/admin/change-password` | Password fields only — **no profile fields** |
| Logout | `POST /admin/logout` | End session |

---

## Typography

| Setting | Value |
|---------|-------|
| Font family | Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif |
| Body size | 13px |
| Table text | 12px |
| Headings | Scaled down proportionally |

Configure in Filament `AdminPanelProvider` theme — standard CSS variable overrides.

---

## Color palette (Good Neighbors)

| Token | Hex | Usage |
|-------|-----|-------|
| `gn-primary` | `#685b55` | Sidebar text, headings |
| `gn-primary-dark` | `#675a54` | Sidebar hover/active text |
| `gn-secondary` | `#7a726f` | Labels, muted text |
| `gn-accent` | `#8aa330` | Active nav item, primary buttons |
| `gn-accent-light` | `#94a240` | Hover accents, badges |
| `gn-danger` | `#ff3131` | Delete, errors, logout emphasis |
| `gn-warning` | `#ffde59` | Warnings |
| `gn-background` | `#ffffff` | Content area |
| `gn-border` | `#c2c0bf` | Borders, dividers |

### Application

- Sidebar: white or light warm gray background, primary text color, accent green on active item
- Top bar: white, GN logo left
- Content: white background, compact table/form spacing
- Primary buttons: accent green
- Danger buttons: red

---

## Login page

`/admin/login` — centered card, GN colors and Calibri font. No sidebar until logged in.

---

## Left sidebar navigation groups

1. Dashboard
2. Content Management
3. Campaign Management
4. User Management
5. Donations and BMS
6. Security and Administration
7. Settings

See [SITEMAP.md](SITEMAP.md) for full module list.

---

## Implementation notes

- Use Filament v3 panel theming — no custom admin framework
- Custom CSS in `resources/css/filament/admin/theme.css` if needed
- Register theme in `AdminPanelProvider::panel()`
