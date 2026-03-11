# HANZO B2B Trade Platform — System Workflow & Documentation

## 1. System Overview

HANZO is a B2B trade platform connecting **Buyers**, **Factories**, and **Admins** for sourcing and delivering products from China. The platform manages the full lifecycle from product request (RFQ) to delivery.

### 1.1 User Roles

| Role | Description | Access |
|------|-------------|--------|
| **Admin** | Platform operator; approves users, assigns factories, builds quotes, manages orders, verifies payments | Full operational control |
| **Buyer** | Importer; submits product requests, receives quotes, accepts quotes, makes payments, tracks orders | RFQs, Quotes, Orders, Payments |
| **Factory** | Supplier; receives assigned RFQs, submits pricing, fulfills orders | Assigned RFQs, Orders |

### 1.2 User Approval Flow

- New users (buyers and factories) register and land on a **Pending Approval** page.
- Admin approves or rejects users from **Approvals → Buyers** and **Approvals → Factories**.
- Until approved, users cannot access their dashboards.

---

## 2. Ordering Process — End-to-End Workflow

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                        BUYER CREATES PRODUCT REQUEST                              │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  1. BUYER: Request a Quote (RFQ)                                                  │
│     • Category, description, quantity, target price, timeline                     │
│     • Delivery city/country                                                       │
│     • Optional attachments (images, PDFs)                                         │
│     Status: RFQ = new                                                            │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  2. ADMIN: Assign Factory                                                         │
│     • View RFQ in Product Requests                                                │
│     • Assign a factory to the RFQ                                                 │
│     Status: RFQ = assigned                                                       │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  3. FACTORY: Submit Price (optional)                                              │
│     • View assigned RFQ, submit unit price and MOQ                                │
│     Status: RFQ = pricing_received (optional)                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  4. ADMIN: Quote Builder                                                          │
│     • Enter product cost, shipping method (sea/air)                               │
│     • Freight cost (from freight rates or transport defaults)                     │
│     • China local shipping, export handling, insurance, clearing, delivery        │
│     • HANZO margin (hidden from buyer)                                            │
│     • Save draft or Send to Buyer                                                 │
│     Status: Quotation = sent | RFQ = quoted                                      │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  5. BUYER: Accept or Reject Quote                                                 │
│     • View quotation in Quotes                                                    │
│     • Accept → Order created; Admin + Factory notified                            │
│     • Reject → Quotation status = rejected                                        │
│     Status: Order = deposit_pending | Quotation = accepted | RFQ = in_production  │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  6. BUYER: Pay Deposit                                                            │
│     • From Order page, click Pay Deposit                                          │
│     • Upload payment proof (bank transfer, etc.)                                  │
│     Status: Payment = pending (until admin verifies)                              │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  7. ADMIN: Verify Payment                                                         │
│     • View payment in Payments list                                               │
│     • Verify or Reject (with reason)                                              │
│     • Update Order milestone to deposit_paid                                      │
│     Status: Payment = verified | Order = deposit_paid                             │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  8. FACTORY + ADMIN: Production                                                   │
│     • Factory submits production updates (optional)                               │
│     • Admin updates Order milestone to in_production                              │
│     Status: Order = in_production                                                │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  9. ADMIN: Mark Shipped                                                           │
│     • Update milestone to shipped                                                 │
│     • Optional: Add tracking number, estimated arrival                            │
│     Status: Order = shipped                                                      │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│  10. ADMIN: Mark Delivered                                                        │
│     • Update milestone to delivered                                               │
│     Status: Order = delivered                                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Status Reference

### 3.1 RFQ (Request for Quote) Statuses

| Status | Meaning |
|--------|---------|
| `new` | RFQ created; awaiting admin assignment |
| `assigned` | Admin has assigned a factory |
| `pricing_received` | Factory has submitted unit price and MOQ |
| `quoted` | Admin has sent a quotation to the buyer |
| `accepted` | Buyer accepted the quote; order created |
| `in_production` | Order in progress |
| `shipped` | Order shipped |
| `delivered` | Order delivered |
| `cancelled` | RFQ cancelled |

### 3.2 Quotation Statuses

| Status | Meaning |
|--------|---------|
| `draft` | Quote built; not yet sent |
| `sent` | Sent to buyer; awaiting accept/reject |
| `accepted` | Buyer accepted; order created |
| `rejected` | Buyer rejected |

### 3.3 Order Milestone Statuses (5 steps)

| Milestone | Meaning |
|-----------|---------|
| `deposit_pending` | Order created; deposit payment expected |
| `deposit_paid` | Deposit verified by admin |
| `in_production` | Manufacturing (includes quality control) |
| `shipped` | Shipped / in transit (includes customs) |
| `delivered` | Delivered to buyer |

### 3.4 Payment Statuses

| Status | Meaning |
|--------|---------|
| `pending` | Buyer submitted proof; awaiting admin verification |
| `verified` | Admin verified payment |
| `rejected` | Admin rejected (buyer may resubmit) |

### 3.5 Payment Types

- **Deposit** — Initial deposit (typically ~30%)
- **Balance** — Remaining amount
- **Full Payment** — Full amount in one payment

---

## 4. System Functionality by Role

### 4.1 Admin Functions

| Section | Functions |
|---------|-----------|
| **Dashboard** | Overview of pending approvals, open RFQs, payments pending, orders by status, recent activity |
| **Approvals** | Approve/reject buyers and factories |
| **Product Requests (RFQs)** | List RFQs, view details, assign factory |
| **Quote Builder** | Build quotations (product cost, freight, clearing, etc.), save draft or send to buyer. Access via RFQ or Product Requests. |
| **Freight Rates** | CRUD freight rates by method (sea/air), destination, rate type (per CBM, per KG, per container) |
| **Transport Defaults** | Set initial/default transport costs (base + per-unit) for sea and air when no specific rate matches |
| **Orders** | List orders, view details, update milestone, add tracking number and estimated arrival |
| **Payments** | List payments, view proof, verify or reject |
| **Documents** | List documents, upload (invoice, packing list, BL/AWB, customs, delivery note), delete |
| **User Management** | CRUD users (admin, buyer, factory) |
| **Notifications** | Bell icon shows new order notifications; mark as read |

### 4.2 Buyer Functions

| Section | Functions |
|---------|-----------|
| **Dashboard** | Counts: RFQs, Quotes, Orders; Order summary (in production, in transit, delivered); Recent quotes; My Account card |
| **RFQs** | Create RFQ, list RFQs, view RFQ details |
| **Quotes** | List quotations, view quotation, accept or reject |
| **Orders** | List orders (code, name, status), view order tracking (5-step milestone), pay deposit/balance, view documents |
| **Profile** | Edit profile info, avatar, password; delete account |

Order tracking shows: order code, order name (category + description), current milestone, tracking number, estimated arrival, total, payment history.

### 4.3 Factory Functions

| Section | Functions |
|---------|-----------|
| **Dashboard** | Assigned RFQs, orders in production, shipped, delivered |
| **Assigned Product Requests** | List assigned RFQs, view details, submit unit price and MOQ |
| **Orders** | List orders assigned to the factory, view details, submit production updates |
| **Notifications** | Bell icon shows new order notifications; mark as read |

---

## 5. Key Features

### 5.1 Transport Costs

- **Freight Rates**: Admin configures specific rates by method (sea/air), destination port/city, and rate type.
- **Transport Defaults**: Admin sets default costs (base + per-unit) for sea and air. Used when no matching freight rate exists (Quote Builder and Estimator API).

### 5.2 Documents

Document types: Commercial Invoice, Packing List, Bill of Lading/AWB, Customs Documents, Delivery Note.  
Admin uploads documents to orders; buyers can download from the order documents page.

### 5.3 Notifications

- **New Order**: When a buyer accepts a quote, Admin and the assigned Factory receive a notification (in-app bell and email).
- **Notification bell** (Admin & Factory): Shows recent notifications, links to order, mark as read.

### 5.4 Multi-Language

- Languages: English, Kiswahili, 中文 (Chinese).
- Language switcher available for all roles in the top navbar.

### 5.5 Order Display Name

- Orders display both order code (e.g. `ORD-XXXXXXXX`) and an order name derived from RFQ category and description (e.g. "Electronics: LED strips 5m").
- Shown in order lists and order tracking to identify orders at a glance.

---

## 6. Public Pages

| Route | Page |
|-------|------|
| `/` | Landing (logged-out) or role dashboard (logged-in) |
| `/how-it-works` | How it works |
| `/about` | About |
| `/login`, `/register` | Authentication |

---

## 7. Technical Notes

- **Document Root**: Web server must point to `public/` (e.g. `public/index.php`).
- **Queue**: Notifications use `ShouldQueue`; ensure queue worker runs for email/database notifications.
- **Storage**: Ensure `storage/` and `bootstrap/cache/` are writable; run `php artisan storage:link` for public uploads.
- **Clearing Costs**: Suggest ranges by destination (e.g. Dar es Salaam, Nairobi) from `config/clearing_rates.php`.
