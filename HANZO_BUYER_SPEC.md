# HANZO Buyer Interface — Comprehensive Spec

> **Adapted for Laravel.** Original spec assumed plain PHP MVC. This document maps to the existing Laravel structure:
> - Views: `resources/views/buyer/`, `resources/views/partials/`
> - Layout: `resources/views/layouts/buyer.blade.php` (extends `layouts.sneat.base`)
> - Assets: `public/assets/hanzo/`, `public/assets/css/`, `public/assets/js/`
> - Controllers: `app/Http/Controllers/Buyer/`
> - Routes: `routes/web.php` (prefix `buyer`, name `buyer.`)

---

## Design System & Brand

**Tagline:** "East Africa × China. Sourced Right."

### Color Palette (CSS variables in `/public/assets/css/buyer.css` or `hanzo-buyer-b2b.css`)

| Token | Hex | Use |
|-------|-----|-----|
| `--hz-navy` | #0B1120 | Primary background |
| `--hz-navy-2` | #141D2E | Card/surface background |
| `--hz-navy-3` | #1C2840 | Input/secondary surface |
| `--hz-gold` | #E8A020 | Primary accent, prices, CTAs |
| `--hz-gold-light` | #F5B940 | Hover for gold |
| `--hz-verified` | #1DB87A | Verified badge, success |
| `--hz-blue` | #4A9FE0 | Shipped/info states |
| `--hz-muted` | #8A96A8 | Secondary text |
| `--hz-border` | rgba(255,255,255,0.08) | Borders on dark bg |
| `--hz-danger` | #E05050 | Error, dispute |
| `--hz-white` | #F2F4F8 | Primary text on dark |

### Typography

- Font: `Plus Jakarta Sans`, `DM Sans`, sans-serif
- Base: 14px, line-height 1.6
- Headings: font-weight 600; Labels: 500, 11–12px

### Component Rules

- **Cards:** `background: var(--hz-navy-2)`, `border: 1px solid var(--hz-border)`, `border-radius: 12px`
- **Inputs:** `background: var(--hz-navy-3)`, `border: 1px solid var(--hz-border)`, `color: var(--hz-white)`
- **Primary button:** `background: var(--hz-gold)`, `color: #0B1120`, `font-weight: 600`
- **Ghost button:** transparent bg, `border: 1px solid var(--hz-gold)`, `color: var(--hz-gold)`
- No box shadows; use borders for depth
- Border radius: 8px inputs, 12px cards, 20px pills

---

## Layout

- **Top navbar:** fixed 60px, HANZO logo (gold), search, notifications, RFQ cart, avatar
- **Sidebar:** 220px (collapsible to 60px), dark bg, nav items with icons + badges
- **Main content:** padding 24px, background `#0D1525`

### Sidebar Nav Items

| Label | Icon | Badge |
|-------|------|-------|
| Dashboard | grid | — |
| Browse Products | search | — |
| My RFQs | document | open count |
| Quotation Inbox | inbox | unread count |
| My Orders | package | active count |
| Saved Products | bookmark | — |
| Suppliers | factory | — |
| Messages | chat | unread count |
| Account Settings | gear | — |

Active: left border 3px gold, `background: rgba(232,160,32,0.08)`, text gold

---

## Page Specs (Laravel View Paths)

| Page | View Path | Notes |
|------|-----------|-------|
| Dashboard | `buyer/dashboard.blade.php` | KPIs, recently viewed, open RFQs, orders needing attention, recommended, announcements |
| Catalog | `buyer/catalog/index.blade.php` | Category sidebar, filter chips, product grid, pagination |
| Product Detail | `buyer/catalog/show.blade.php` or new `buyer/products/show.blade.php` | Gallery, tabs, price tiers, factory mini-card |
| RFQ Center | `buyer/rfqs/index.blade.php` + create | Tabs: My RFQs, Post New RFQ, Quotation Inbox |
| Orders | `buyer/orders/index.blade.php` | Table, progress tracker, filters |
| Order Detail | `buyer/orders/show.blade.php` | Progress, line items, documents, payment summary |
| Suppliers | `buyer/suppliers/index.blade.php` | Search, filters, supplier cards grid |
| Supplier Profile | `buyer/suppliers/show.blade.php` | Hero, tabs: Overview, Products, Certifications |
| Messages | `buyer/messages/index.blade.php` | Two-panel: conversation list + thread |
| Saved | `buyer/saved/index.blade.php` | Tabs: All Saved, Collections, Compare |
| Settings | `buyer/settings` → profile or new `buyer/settings.blade.php` | Profile, Company, Team, Notifications, Documents, Security |

---

## Security Rules (CRITICAL)

1. **Factory contact data:** NEVER render `phone`, `email`, `wechat`, `whatsapp`, `direct_contact` in buyer views. Sanitize via `FactoryPrivacy::sanitize($factory)` in controller.
2. **hanzo_margin:** NEVER expose in any buyer-facing view or API response.
3. **Quotation data:** Use `QuotationModel::getSafeForBuyer()` before passing to views.
4. All forms: `@csrf`
5. All user input: `{{ old('field', $value) }}` or `{{ $variable }}` (Blade escapes by default).

---

## Responsive

- Mobile < 768px: sidebar → bottom tab bar, single-column grid
- Tablet 768–1023px: sidebar icon-only 60px, 2-col product grid
- Desktop ≥ 1024px: full sidebar 220px, auto-fill product grid

---

## Build Order

1. `layouts/buyer.blade.php` + layout structure
2. `public/assets/css/hanzo-buyer.css` (or update `hanzo-buyer-b2b.css`) — design system
3. `partials/sidebar-buyer-b2b.blade.php`, `topbar-buyer-b2b.blade.php`
4. `buyer/dashboard.blade.php`
5. `buyer/catalog/index.blade.php`, `catalog/show.blade.php`
6. `buyer/orders/index.blade.php`, `orders/show.blade.php`
7. `buyer/rfqs/index.blade.php`, `rfqs/create.blade.php`
8. `buyer/suppliers/index.blade.php`, `suppliers/show.blade.php`
9. `buyer/messages/index.blade.php`
10. `buyer/saved/index.blade.php`
11. Settings (new or extend profile)
12. `buyer.js`, page-specific JS for interactivity

---

## Product Card (Catalog)

- Image 200×200, aspect-ratio 1:1
- Verified badge: "✓ Verified" green
- Save button (heart) top-right
- Price in gold, MOQ muted
- Factory name with CN flag (no contact info)
- "Request Quote" ghost button

---

## RFQ Table Columns

RFQ ID | Product Requested | Qty | Target Price | Quotes | Expires | Status | Actions

Status pills: Open (blue), Quoted (gold), Negotiating (purple), Closed (green), Expired (gray)

---

## Order Progress Tracker (5 steps)

Confirmed → Production → QC Check → Shipped → Delivered

● = completed (green), ◐ = active (gold), ○ = pending (gray)

---

*Full page-level wireframes and component details are in the original Cursor prompt. Use this doc as the source of truth for design tokens, security, and structure.*
