<!DOCTYPE html><html><head><meta charset="utf-8"><title>HANZO</title></head><body style="margin:0;font-family:sans-serif;background:#f8fafc;">
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:40px 20px;">
<table width="560" cellpadding="0" cellspacing="0" style="max-width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.05);">
<tr><td style="background:linear-gradient(135deg,#0f172a,#1e293b);padding:32px;text-align:center;"><p style="color:#f1f5f9;font-size:24px;font-weight:700;margin:0;">HANZO</p></td></tr>
<tr><td style="padding:40px;">
<h1 style="margin:0 0 16px;font-size:20px;color:#0f172a;">Application Not Approved</h1>
<p style="margin:0 0 12px;font-size:15px;color:#475569;line-height:1.6;">Thank you for your interest in HANZO. Unfortunately, we are unable to approve your registration at this time.</p>
@if($user->approval_message)
<p style="margin:0;font-size:15px;color:#475569;line-height:1.6;"><strong>Note:</strong> {{ $user->approval_message }}</p>
@endif
</td></tr></table></td></tr></table></body></html>
