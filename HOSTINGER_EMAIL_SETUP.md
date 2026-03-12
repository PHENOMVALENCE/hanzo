# Hostinger Email Setup for HANZO

This guide configures the email automation system so admins can invite/register users and they receive welcome emails on Hostinger shared hosting.

## 1. Create an Email Account in Hostinger

1. Log in to **Hostinger hPanel** → **Emails** → **Email Accounts**
2. Create a new account (e.g. `noreply@hanzo.promaafrica.com` or `noreply@promaafrica.com`)
3. Set a strong password and note it
4. Ensure the domain is linked (e.g. `hanzo.promaafrica.com` or your main domain)

## 2. Add Mail Variables to `.env`

Edit your `.env` file on the server (inside the `hanzo` folder or wherever your app lives) and add:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@promaafrica.com
MAIL_PASSWORD=your_email_password_here
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@promaafrica.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Important:** Replace `noreply@promaafrica.com` with the actual email you created, and `your_email_password_here` with its password.

**Alternative (port 587 with TLS):** If 465/SSL fails, try:
```env
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

## 3. Clear Config Cache

After updating `.env`, run (via SSH or Hostinger’s PHP CLI if available):

```bash
php artisan config:clear
php artisan cache:clear
```

If you only have File Manager, delete `bootstrap/cache/config.php` if it exists.

## 4. How It Works

- **Admin creates a user** → The system sends a welcome/invite email with login credentials (when “Send welcome email with login credentials” is checked).
- **Admin invites new users** → Use **User Management** → **Create User**; the welcome email is sent automatically.
- **Existing users** → They already have credentials; no invite is sent.

## 5. Shared Hosting Notes (Hostinger)

- **Queues:** On shared hosting, background queues may not run. Important emails (welcome, order notifications) are sent immediately, not queued.
- **Timeout:** Sending can take a few seconds; Hostinger usually allows it.
- **Logs:** Check `storage/logs/laravel.log` if emails fail.
- **SPF/DKIM:** Configure these for your domain in Hostinger to improve deliverability.

## 6. Test Email

1. Create a test user: **Admin** → **User Management** → **Create User**
2. Enable **“Send welcome email with login credentials”**
3. Submit the form
4. The new user should receive an email at the address you entered
