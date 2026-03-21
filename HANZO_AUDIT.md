# HANZO Platform Audit – Factory Visibility, MOQ, Freight & Clearing

*Last run: 2025-03-20*

## 1. Factory Visibility Rule ✅

**Requirement:** Buyers must NOT see factory direct contact. Only "HANZO Verified Factory" and general production info.

### Implementation

| Area | Status | Notes |
|------|--------|-------|
| `FactoryPrivacy::forBuyer()` | ✅ | `app/Services/FactoryPrivacy.php` – strips phone, email, wechat |
| `SupplierController` | ✅ | Uses `FactoryPrivacy::forBuyer()` for index & show |
| Buyer product views | ✅ | Product controller no longer loads `factory` relation (hardening) |
| Buyer product index/show | ✅ | Only product data shown; no factory contact |
| Supplier directory | ✅ | Shows "HANZO Verified Factory", location "China", platform messaging |

### Hardening applied

- Removed `factory` relation from buyer `ProductController` index/show queries so no factory data is loaded for buyer product views.

---

## 2. MOQ Display ✅

**Requirement:** Each product displays Minimum Order Quantity.

### Implementation

| View | Status |
|------|--------|
| `buyer/products/index.blade.php` | ✅ `{{ $product->moq ?? '—' }}` |
| `buyer/products/show.blade.php` | ✅ MOQ in stat box |
| `buyer/dashboard.blade.php` | ✅ `{{ $p->moq ?? '—' }}` on product cards |
| `buyer/rfqs/create.blade.php` | ✅ MOQ in product summary and quantity hint |
| Factory product forms | ✅ MOQ field required |
| Admin product forms | ✅ MOQ field |

---

## 3. Freight Rate Tables ✅

**Requirement:** Admin inputs freight rates (Sea per CBM, Air per KG, Clearing brackets).

### Implementation

| Area | Status | Notes |
|------|--------|-------|
| Freight rates CRUD | ✅ | `/admin/freight-rates` – method (sea/air), rate_type (per_cbm, per_kg, per_container) |
| `FreightRate` model | ✅ | `method`, `rate_type`, `rate_value`, `destination_*` |
| Quote Builder | ✅ | Uses freight rates for cost estimation |
| EstimatorService | ✅ | Uses freight rates for RFQ estimates |
| Transport defaults | ✅ | Fallback when no matching freight rate |
| Freight index UX | ✅ | Links to Transport Defaults and Estimate Defaults |

### Clearing brackets

- **Estimate Defaults** (`/admin/estimate-defaults`): customs_min, customs_max (clearing brackets)
- **config/clearing_rates.php**: Destination-based clearing (Dar, Nairobi, Kampala, etc.) for Quote Builder suggestions
- Linked from Freight Rates index for easier admin access

---

## Summary

- **Factory visibility:** Enforced via `FactoryPrivacy`; buyer product views do not load factory.
- **MOQ:** Shown on all buyer product and RFQ views.
- **Freight:** Admin CRUD for sea/air, per CBM/per KG/per container; integrated in Quote Builder and Estimator.
- **Clearing:** Admin via Estimate Defaults; destination-based rates in config used for suggestions.
