<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HANZO - Registration Received</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;">
    <tr>
      <td align="center" style="padding:40px 20px;">
        <table width="560" cellpadding="0" cellspacing="0" style="max-width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.05);">
          <tr>
            <td style="background:linear-gradient(135deg,#0f172a,#1e293b);padding:32px;text-align:center;">
              <p style="color:#f1f5f9;font-size:24px;font-weight:700;margin:0;">HANZO</p>
              <p style="color:#94a3b8;font-size:13px;margin:8px 0 0;">B2B Trade Platform</p>
            </td>
          </tr>
          <tr>
            <td style="padding:40px;">
              <h1 style="margin:0 0 16px;font-size:20px;color:#0f172a;">Thank you for registering, {{ $user->name }}!</h1>
              <p style="margin:0 0 12px;font-size:15px;color:#475569;line-height:1.6;">Your account is under review. We typically respond within 24–48 hours.</p>
              <p style="margin:0 0 12px;font-size:15px;color:#475569;line-height:1.6;">Once approved, you'll receive a welcome email and full access to the platform.</p>
              <p style="margin:0;font-size:15px;color:#475569;line-height:1.6;">While you wait, you can log in and complete your profile or upload additional documents.</p>
            </td>
          </tr>
          <tr>
            <td style="padding:0 40px 40px;border-top:1px solid #e2e8f0;">
              <p style="margin:20px 0 0;font-size:13px;color:#94a3b8;">Questions? Reply to this email or contact support via your account.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
