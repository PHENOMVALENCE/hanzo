# HANZO – Deployment Guide (Hostinger)

## Fix: Table 'products' doesn't exist

The error occurs because **database migrations have not been run** on the production server. Follow these steps:

### 1. Connect to Hostinger

Use one of these options:

- **SSH** (if enabled): `ssh u145584795@hanzo.promaafrica.com`
- **Hostinger hPanel** → **Advanced** → **SSH Access** or **Terminal**
- **Hostinger File Manager** → Upload/run scripts if no SSH

### 2. Run migrations via SSH

```bash
# Navigate to your project root (adjust path if different)
cd ~/domains/hanzo.promaafrica.com/public_html

# Or if Laravel is in a subfolder (e.g. app lives above public_html):
# cd ~/domains/hanzo.promaafrica.com
# The public_html typically points to /public, so your artisan may be at:
# cd ~/domains/hanzo.promaafrica.com/public_html/..
# or
# cd /home/u145584795/domains/hanzo.promaafrica.com/public_html
# php ../artisan migrate --force  # if artisan is one level up

# Run migrations (--force skips confirmation in production)
php artisan migrate --force
```

### 3. Optional: Seed initial data

If you want demo/admin accounts:

```bash
php artisan db:seed --force
```

> **Note:** Seeding creates `admin@hanzo.com` / `buyer@hanzo.com` / `factory@hanzo.com`. Skip if you already have users.

### 4. Clear caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Initial deployment checklist

1. **Environment**
   - Copy `.env.example` to `.env`
   - Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://hanzo.promaafrica.com`
   - Configure DB: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (Hostinger MySQL credentials)

2. **Application key**
   ```bash
   php artisan key:generate
   ```

3. **Migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Permissions**
   - `storage/` and `bootstrap/cache/` writable (755 or 775)

5. **Document root**
   - Point to `public/` (Hostinger usually uses `public_html` = `public`)

6. **Optional**
   - `php artisan storage:link` if using local storage for uploads

---

## Hostinger database setup

1. In hPanel → **Databases** → create MySQL database (e.g. `u145584795_hanzo`)
2. Create user and grant all privileges
3. Update `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u145584795_hanzo
   DB_USERNAME=u145584795_hanzo
   DB_PASSWORD=your_password
   ```

---

## Gmail SMTP (authentication failed)

If you see `535 authentication failed` with Gmail:

1. **Use an App Password**, not your normal Gmail password:
   - Google Account → Security → 2-Step Verification (enable if needed)
   - App passwords → Generate for "Mail" / "Other (Custom name)"
   - Use the 16-character password in `.env` as `MAIL_PASSWORD`

2. **Correct `.env` settings:**
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-16-char-app-password
   MAIL_ENCRYPTION=tls
   ```

3. **Temporary workaround (choose one):**
   - Set `MAIL_MAILER=log` to log emails instead of sending.
   - Or set `NOTIFICATIONS_MAIL_ENABLED=false` so notifications use only the database channel (no email). Order, RFQ, and other flows will work; users will see in-app notifications only.

---

## If you can't use SSH

1. Use **Hostinger File Manager** or FTP.
2. Create `run-migrate.php` inside the `public` folder (same level as `index.php`):

```php
<?php
// run-migrate.php - DELETE immediately after use
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
echo \Illuminate\Support\Facades\Artisan::output();
```

3. Open in browser: `https://hanzo.promaafrica.com/run-migrate.php`
4. **Delete the file right away** for security.
