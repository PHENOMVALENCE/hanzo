# HANZO - B2B Trade Platform

Laravel 12 B2B trade platform with controlled buyer/factory data privacy.

## Requirements

- PHP 8.2+
- MySQL (XAMPP)
- Composer

## XAMPP MySQL Setup

1. Start XAMPP and ensure MySQL is running
2. Create database: `CREATE DATABASE hanzo;`
3. Default XAMPP MySQL: host `127.0.0.1`, port `3306`, user `root`, password `` (empty)

## Installation

```bash
cd C:\xampp\htdocs\hanzo

# Copy env and configure MySQL
copy .env.example .env

# Edit .env - set:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=hanzo
# DB_USERNAME=root
# DB_PASSWORD=

php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## Seeded Login Credentials

| Role   | Email            | Password | Status   |
|--------|------------------|----------|----------|
| Admin  | admin@hanzo.com  | password | approved |
| Buyer  | buyer@hanzo.com  | password | pending  |
| Factory| factory@hanzo.com| password | pending  |

**Admin** can log in immediately and approve buyers/factories. **Buyer** and **Factory** will see "Pending Approval" until admin approves them.

## Routes to Test

After login as admin:

- `/admin/dashboard`
- `/admin/approvals/buyers`
- `/admin/approvals/factories`
- `/admin/rfqs`
- `/admin/freight-rates`
- `/admin/orders`
- `/admin/payments`
- `/admin/documents`

As approved buyer: `/buyer/dashboard`, `/buyer/rfqs`, `/buyer/quotes`, `/buyer/orders`

As approved factory: `/factory/dashboard`, `/factory/rfqs`, `/factory/orders`

## API Estimate

```
GET /api/estimate?category=electronics&qty=1000&method=sea&destination=LA
```

Returns JSON estimate from freight rates.

## Data Privacy

- **Buyers** never see factory identity/contact
- **Factories** never see buyer email/phone/company_name
- **Admin** sees everything
