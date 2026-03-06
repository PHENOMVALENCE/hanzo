# HANZO Platform UI Style Guide

## Overview

HANZO is a controlled B2B trade platform connecting verified Chinese factories to buyers in Tanzania/East Africa. The UI must feel **premium**, **structured**, and **logistics-focused**.

---

## 1. Visual Identity

### Brand feel
- Premium, structured, logistics/manufacturing
- Trust and transparency
- No open marketplace aesthetic

### Color Palette

| Token | Hex | Use |
|-------|-----|-----|
| Navy (primary) | `#0f172a` | Headers, sidebar, primary surfaces |
| Navy light | `#1e293b` | Hover states, gradients |
| Gold (primary action) | `#f59e0b` | CTAs, highlights, buyer accent |
| Gold soft | `#fcd34d` | Active sidebar, accents |
| Slate | `#64748b` | Muted text, borders |
| Success | `#059669` | Delivered, approved |
| Warning | `#f59e0b` | Pending, in progress |
| Danger | `#dc2626` | Rejected, errors |

### Typography
- **Font**: DM Sans
- **Scale**: H1 (page title) → H2 (section) → H3 (cards)
- Strong hierarchy; avoid decorative fonts

---

## 2. Layouts

### Authenticated (Admin, Buyer, Factory)
- Top navbar (brand + quick actions)
- Left sidebar (role-based menu, collapsible)
- Main content: `container-xxl container-p-y`
- Footer: minimal

### Public
- Fixed top navbar (transparent overlay on hero)
- Full-width sections
- Footer with links

---

## 3. Components

### Buttons
| Class | Use |
|-------|-----|
| `btn-hanzo-primary` | Primary CTA (gold) |
| `btn-hanzo-secondary` | Secondary (navy outline) |
| `btn-hanzo-muted` | Tertiary, low emphasis |
| `btn-primary` | Admin/navy primary |
| `btn-outline-primary` | Secondary links |

### Cards
| Class | Use |
|-------|-----|
| `hanzo-card` | Base card |
| `hanzo-card-header` | Card header |
| `hanzo-card-body` | Card body |
| `hanzo-stat-card` | Stat/metric card |
| `card-verified` | Gold accent border |

### Metrics
| Class | Use |
|-------|-----|
| `hanzo-metric` | Large number (e.g. 42) |
| `hanzo-metric-label` | Label above metric |
| `hanzo-stat-box` | Compact stat box |

### Status Badges
| Class | Status |
|-------|--------|
| `hanzo-badge-new` | New |
| `hanzo-badge-assigned` | Assigned |
| `hanzo-badge-quoted` | Quoted / pricing_received |
| `hanzo-badge-accepted` | Accepted |
| `hanzo-badge-in_production` | In production |
| `hanzo-badge-shipped` | Shipped |
| `hanzo-badge-delivered` | Delivered |
| `hanzo-badge-pending` | Pending |
| `hanzo-badge-approved` | Approved |
| `hanzo-badge-rejected` | Rejected |
| `hanzo-badge-suspended` | Suspended |

Usage: `<x-status-badge :status="$order->milestone_status" />`

### Page Header
```blade
<x-page-header
  title="Order Tracking"
  :breadcrumbs="[['label' => 'Orders', 'url' => route('buyer.orders.index')], ['label' => 'ORD-001']]"
>
  <x-slot:actions>
    <a href="..." class="btn btn-hanzo-primary btn-sm">New Request</a>
  </x-slot:actions>
</x-page-header>
```

### Empty State
```blade
<x-empty-state
  icon="bx-folder-open"
  title="No orders yet"
  text="Create a request to get started."
  actionLabel="Create Request"
  :actionUrl="route('buyer.rfqs.create')"
/>
```

### Stepper (Order Milestones)
```blade
<x-stepper
  :steps="[
    ['status' => 'deposit_pending', 'label' => 'Deposit Pending'],
    ['status' => 'in_production', 'label' => 'In Production'],
    ['status' => 'shipped', 'label' => 'Shipped'],
    ['status' => 'delivered', 'label' => 'Delivered']
  ]"
  :currentStatus="$order->milestone_status"
/>
```

### Document List
```blade
<x-document-list :documents="$order->documents" :downloadRoute="'buyer.documents.download'" />
```

---

## 4. Forms

### Labels
- Use `hanzo-form-label` or Bootstrap `form-label`
- Helper text: `hanzo-form-helper`
- Validation: `hanzo-form-error`

### File Upload
- Container: `hanzo-file-upload`
- List: `hanzo-file-list`
- Supports drag & drop; shows filenames and size

---

## 5. Tables

- Use Bootstrap `.table` with HANZO theme
- Or `hanzo-table` for custom styling
- Always provide empty state via `<x-empty-state>`

---

## 6. Security / Visibility

- **Buyers** must never see factory phone/WeChat/email
- **Factories** must not see other factories or buyer direct contact
- Strip sensitive fields in views by role before rendering

---

## 7. Responsiveness

- Mobile-first
- Sidebar collapses to offcanvas on small screens
- Forms stack vertically on mobile
- Tables scroll horizontally if needed

---

## 8. JS Modules

| File | Purpose |
|------|---------|
| `hanzo-quote-builder.js` | Live total calculation on cost inputs |
| `hanzo-estimator.js` | Fetch estimate API, render result panel |
| `hanzo-stepper.js` | Highlight milestone step by status |
| `hanzo-upload.js` | Multi-file UI, drag & drop, size display |

---

## 9. Asset Paths

- Theme: `public/assets/hanzo/hanzo-theme.css`
- Design system: `public/assets/hanzo/hanzo.css`
- JS: `public/assets/hanzo/js/*.js`
- Sneat base: `public/assets/sneat/`
