<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badilisha Nenosiri - Wazabiashara</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f5f7;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#0D9488,#065F46);padding:32px 40px;text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="Wazabiashara" style="width:56px;height:56px;margin-bottom:12px;">
                            <h1 style="color:#ffffff;font-size:22px;font-weight:800;margin:0;">Wazabiashara</h1>
                            <p style="color:#a7f3d0;font-size:13px;margin:4px 0 0;">Biashara Yako, Mkononi Mwako</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 40px;">
                            <h2 style="color:#1f2937;font-size:20px;font-weight:700;margin:0 0 16px;">Habari {{ $user->name }},</h2>
                            <p style="color:#4b5563;font-size:15px;line-height:1.6;margin:0 0 20px;">
                                Umeomba kubadilisha nenosiri lako la akaunti ya Wazabiashara. Bonyeza kitufe kilicho chini kubadilisha nenosiri lako.
                            </p>
                            <div style="text-align:center;margin:28px 0;">
                                <a href="{{ url('/reset-password?token=' . $token) }}" style="display:inline-block;background:#0D9488;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 36px;border-radius:10px;">
                                    Badilisha Nenosiri
                                </a>
                            </div>
                            <p style="color:#6b7280;font-size:13px;line-height:1.5;margin:0 0 12px;">
                                Kiungo hiki kitaisha muda wa dakika 60. Kama hukuomba kubadilisha nenosiri, tafadhali puuza barua pepe hii.
                            </p>
                            <p style="color:#9ca3af;font-size:12px;line-height:1.5;margin:0;">
                                Au nakili kiungo hiki: {{ url('/reset-password?token=' . $token) }}
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb;padding:20px 40px;border-top:1px solid #f3f4f6;">
                            <p style="color:#9ca3af;font-size:12px;text-align:center;margin:0;">
                                &copy; {{ date('Y') }} Wazabiashara. Haki zote zimehifadhiwa.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
