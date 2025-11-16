{{-- Verify your email address by clicking the link below: --}}
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial; color:#333; line-height:1.5; margin:0; padding:24px; background:#f6f7fb;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff; border-radius:8px; padding:28px; box-shadow:0 4px 18px rgba(0,0,0,0.05);">
          <tr>
            <td style="text-align:left;">
              <h2 style="margin:0 0 8px 0; font-size:20px; color:#111;">Welcome, {{ $user->name }}!</h2>
              <p style="margin:0 0 18px 0; color:#555;">
                Thanks for creating an account with {{ config('app.name', 'Our App') }}. Please confirm your email address to activate your account and get started.
              </p>

              <p style="text-align:center; margin:24px 0;">
                <a href="{{ $url }}" style="display:inline-block; padding:12px 20px; background:#2563eb; color:#fff; text-decoration:none; border-radius:6px; font-weight:600;">
                  Verify Email Address
                </a>
              </p>

              <p style="margin:0 0 12px 0; color:#666; font-size:13px;">
                If the button above doesn't work, copy and paste the link below into your browser:
              </p>
              <p style="word-break:break-all; font-size:13px; color:#2563eb; margin:0 0 20px 0;">
                <a href="{{ $url }}" style="color:#2563eb; text-decoration:none;">{{ $url }}</a>
              </p>

              <p style="margin:0; color:#777; font-size:13px;">
                If you didn't create an account, you can safely ignore this email.
              </p>

              <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

              <p style="margin:0; color:#999; font-size:12px;">
                Sent by {{ config('app.name', 'Our App') }} • {{ url('/') }}
              </p>
            </td>
          </tr>
        </table>

        <p style="margin:16px 0 0 0; font-size:12px; color:#999;">
          This link will expire in 5 minutes.
        </p>
      </td>
    </tr>
  </table>
</body>
</html>