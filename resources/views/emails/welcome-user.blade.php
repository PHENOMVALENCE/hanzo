<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <title>Welcome to {{ config('app.name') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; height: 100% !important; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

        /* Core styles — Hanzo brand palette */
        body {
            background-color: #f8fafc;
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Responsive */
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; margin: 0 auto !important; }
            .fluid { max-width: 100% !important; height: auto !important; }
            .stack-column { display: block !important; width: 100% !important; max-width: 100% !important; }
            .body-content { padding: 28px 24px !important; }
            .header-content { padding: 28px 24px !important; }
            .footer-content { padding: 20px 24px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc;">

    <!-- Preheader (hidden preview text) -->
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all;">
        Welcome to {{ config('app.name') }}! Your account is ready — here are your login details.
    </div>

    <!-- Outer wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 40px 16px;">

                <!-- Email container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="580" class="email-container" style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(15,23,42,0.06), 0 2px 4px -2px rgba(15,23,42,0.04);">

                    <!-- ========== HEADER ========== -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td class="header-content" style="padding: 36px 40px; text-align: center;">
                                        <!-- Brand logo -->
                                        <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" width="160" style="height: auto; max-height: 48px; display: block; margin: 0 auto;">
                                        <p style="color: #94a3b8; font-size: 13px; margin: 10px 0 0; letter-spacing: 0.5px;">
                                            B2B Trade Platform
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ========== ACCENT BAR ========== -->
                    <tr>
                        <td style="background: linear-gradient(90deg, #f59e0b, #fcd34d); height: 4px; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>

                    <!-- ========== BODY ========== -->
                    <tr>
                        <td class="body-content" style="padding: 40px 44px; background: #ffffff;">

                            <!-- Greeting -->
                            <h1 style="margin: 0 0 6px; font-size: 24px; font-weight: 700; color: #0f172a; font-family: 'Public Sans', sans-serif;">
                                Welcome aboard, {{ $user->name }}!
                            </h1>
                            <p style="margin: 0 0 28px; font-size: 15px; color: #64748b; line-height: 1.6;">
                                An account has been created for you on <strong style="color: #0f172a;">{{ config('app.name') }}</strong>. You're all set to start using the platform.
                            </p>

                            <!-- Credentials card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 0;">
                                        <!-- Card header -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 14px 20px; background: #0f172a; color: #f59e0b; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; font-family: 'Public Sans', sans-serif;">
                                                    Your Login Credentials
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Card body -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 20px;">
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                        <!-- Email -->
                                                        <tr>
                                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                    <tr>
                                                                        <td width="100" style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Public Sans', sans-serif; vertical-align: top; padding-top: 2px;">
                                                                            Email
                                                                        </td>
                                                                        <td style="font-size: 15px; font-weight: 600; color: #0f172a; font-family: 'Public Sans', sans-serif;">
                                                                            {{ $user->email }}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <!-- Password -->
                                                        <tr>
                                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                    <tr>
                                                                        <td width="100" style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Public Sans', sans-serif; vertical-align: top; padding-top: 2px;">
                                                                            Password
                                                                        </td>
                                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 15px; font-weight: 700; color: #0f172a; background: #ffffff; padding: 4px 10px; border-radius: 4px; border: 1px dashed #cbd5e1;">
                                                                            {{ $plainPassword }}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <!-- Role -->
                                                        <tr>
                                                            <td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                    <tr>
                                                                        <td width="100" style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Public Sans', sans-serif; vertical-align: top; padding-top: 2px;">
                                                                            Role
                                                                        </td>
                                                                        <td>
                                                                            <span style="display: inline-block; font-size: 13px; font-weight: 600; color: #0f172a; background: #fef3c7; padding: 3px 12px; border-radius: 20px; font-family: 'Public Sans', sans-serif;">
                                                                                {{ ucfirst($user->roles->first()?->name ?? 'user') }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <!-- Status -->
                                                        <tr>
                                                            <td style="padding: 8px 0;">
                                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                    <tr>
                                                                        <td width="100" style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Public Sans', sans-serif; vertical-align: top; padding-top: 2px;">
                                                                            Status
                                                                        </td>
                                                                        <td>
                                                                            @if($user->status === 'approved')
                                                                                <span style="display: inline-block; font-size: 13px; font-weight: 600; color: #059669; background: #ecfdf5; padding: 3px 12px; border-radius: 20px; font-family: 'Public Sans', sans-serif;">
                                                                                    &#10003; Approved
                                                                                </span>
                                                                            @elseif($user->status === 'pending')
                                                                                <span style="display: inline-block; font-size: 13px; font-weight: 600; color: #d97706; background: #fffbeb; padding: 3px 12px; border-radius: 20px; font-family: 'Public Sans', sans-serif;">
                                                                                    &#9679; Pending Review
                                                                                </span>
                                                                            @else
                                                                                <span style="display: inline-block; font-size: 13px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 3px 12px; border-radius: 20px; font-family: 'Public Sans', sans-serif;">
                                                                                    {{ ucfirst($user->status) }}
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 32px;">
                                <tr>
                                    <td align="center">
                                        <!--[if mso]>
                                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ rtrim(config('app.url'), '/') }}/login" style="height:48px;v-text-anchor:middle;width:220px;" arcsize="15%" strokecolor="#0f172a" fillcolor="#0f172a">
                                        <w:anchorlock/>
                                        <center style="color:#ffffff;font-family:'Public Sans',sans-serif;font-size:15px;font-weight:bold;">Log In Now &rarr;</center>
                                        </v:roundrect>
                                        <![endif]-->
                                        <!--[if !mso]><!-->
                                        <a href="{{ rtrim(config('app.url'), '/') }}/login" style="display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-weight: 700; font-size: 15px; font-family: 'Public Sans', sans-serif; letter-spacing: 0.3px; mso-padding-alt: 0; text-align: center;">
                                            Log In Now &rarr;
                                        </a>
                                        <!--<![endif]-->
                                    </td>
                                </tr>
                            </table>

                            <!-- Security notice -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 28px;">
                                <tr>
                                    <td style="background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 6px 6px 0; padding: 14px 18px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="24" style="vertical-align: top; padding-top: 1px; font-size: 16px;">
                                                    &#x1F512;
                                                </td>
                                                <td style="padding-left: 8px; font-size: 13px; color: #92400e; line-height: 1.5; font-family: 'Public Sans', sans-serif;">
                                                    <strong>Security notice:</strong> Please change your password after your first login by visiting your <strong>Profile</strong> page.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($user->status === 'pending')
                            <!-- Pending notice -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 12px;">
                                <tr>
                                    <td style="background: #eff6ff; border-left: 4px solid #0284c7; border-radius: 0 6px 6px 0; padding: 14px 18px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="24" style="vertical-align: top; padding-top: 1px; font-size: 16px;">
                                                    &#x2139;&#xFE0F;
                                                </td>
                                                <td style="padding-left: 8px; font-size: 13px; color: #1e40af; line-height: 1.5; font-family: 'Public Sans', sans-serif;">
                                                    <strong>Note:</strong> Your account is pending approval. An administrator will review and activate your account shortly.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                        </td>
                    </tr>

                    <!-- ========== HELP SECTION ========== -->
                    <tr>
                        <td style="padding: 0 44px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 24px 0; text-align: center;">
                                        <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.6; font-family: 'Public Sans', sans-serif;">
                                            Questions? Reply to this email or contact our support team.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- ========== FOOTER ========== -->
                    <tr>
                        <td style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 0;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td class="footer-content" style="padding: 24px 44px; text-align: center;">
                                        <!-- Mini brand logo -->
                                        <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" width="90" style="height: auto; max-height: 28px; display: block; margin: 0 auto 12px;">
                                        <p style="margin: 0 0 4px; font-size: 12px; color: #94a3b8; font-family: 'Public Sans', sans-serif;">
                                            Structured Access to Global Manufacturing
                                        </p>
                                        <p style="margin: 0; font-size: 11px; color: #cbd5e1; font-family: 'Public Sans', sans-serif;">
                                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!-- /Email container -->

            </td>
        </tr>
    </table>

</body>
</html>
