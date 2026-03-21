# HANZO Platform – Agent Reference

## What HANZO Is

HANZO is a **controlled B2B trade platform**, not an open marketplace. It connects:

- **Verified Chinese factories** (approved by HANZO)
- **Buyers in Tanzania / East Africa**
- **Shipping partners**
- **HANZO admin team**

All communication and transactions flow through HANZO. No direct contact between buyers and factories outside the platform.

---

## Main Goals

- Manage product requests (RFQs)
- Generate estimated pricing
- Send official quotations
- Track orders and shipping
- Manage payments
- **Protect factory information**
- Standardize trade operations

---

## User Types

| Role | Access |
|------|--------|
| **Buyer** | Browse products, request quotes, place orders, messaging via platform |
| **Factory** | Manage products, respond to RFQs, fulfill orders (approved only) |
| **HANZO Admin** | Full platform control, freight rates, clearing, verification, quote builder |

---

## Critical Rules (Never Violate)

### 1. Factory Visibility

**Buyers must NOT see factory direct contact details.**

- Use `FactoryPrivacy::forBuyer($factory)` before passing any factory to buyer views
- Buyer-facing text: "HANZO Verified Factory" and general production info only
- Never expose: `phone`, `email`, `wechat`, `whatsapp`, `contact_*`, `direct_contact`
- All contact goes through platform messaging (`buyer.messages.index`)

### 2. MOQ (Minimum Order Quantity)

- Every product MUST display MOQ in buyer views
- Product model: `moq` field; use `{{ $product->moq ?? '—' }}`

### 3. Freight & Clearing

- **Freight rates**: Admin inputs via `/admin/freight-rates` (Sea per CBM, Air per KG, per container)
- **Clearing**: `config/clearing_rates.php` + Estimate Defaults (customs_min, customs_max)
- Used in: Quote Builder, EstimatorService, RFQ cost estimates

---

## Key Paths

| Area | Path |
|------|------|
| Factory privacy | `app/Services/FactoryPrivacy.php` |
| Freight model | `app/Models/FreightRate.php` |
| Clearing config | `config/clearing_rates.php` |
| Quote / estimate | `app/Services/QuoteService.php`, `app/Services/EstimatorService.php` |
| Buyer product views | `resources/views/buyer/products/` |

---

## Documentation

- `WORKFLOW.md` – Order flow, milestones, admin workflows
- `HANZO_BUYER_SPEC.md` – Buyer UI design system
- `.cursor/rules/hanzo-platform.mdc` – Platform rules for AI
