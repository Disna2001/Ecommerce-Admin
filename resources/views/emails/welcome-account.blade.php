<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:640px; margin:0 auto; padding:32px 20px;">
        <div style="background:linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%); border-radius:24px 24px 0 0; padding:32px 28px; color:#ffffff;">
            <div style="font-size:12px; letter-spacing:1.5px; text-transform:uppercase; opacity:0.8;">
                {{ config('app.name') }}
            </div>
            <h1 style="margin:12px 0 0; font-size:30px; line-height:1.2;">
                Account created successfully
            </h1>
            <p style="margin:12px 0 0; font-size:16px; line-height:1.7; opacity:0.92;">
                Hi {{ $user->name }}, your account is ready and you can start using the platform right away.
            </p>
        </div>

        <div style="background:#ffffff; border:1px solid #dbe4f0; border-top:none; border-radius:0 0 24px 24px; padding:28px;">
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:18px; padding:18px 20px; margin-bottom:20px;">
                <p style="margin:0 0 8px; font-size:14px; color:#475569;">Account email</p>
                <p style="margin:0; font-size:18px; font-weight:700; color:#0f172a;">{{ $user->email }}</p>
            </div>

            @if ($isFirstUser)
                <p style="margin:0 0 16px; font-size:15px; line-height:1.8;">
                    You were registered as the first user, so your account was given administrator access automatically.
                </p>
            @elseif ($isMerchant)
                <p style="margin:0 0 16px; font-size:15px; line-height:1.8;">
                    Your merchant application and business documents were received successfully. Our team will review them and contact you once verification is complete.
                </p>
            @else
                <p style="margin:0 0 16px; font-size:15px; line-height:1.8;">
                    Your customer account is active. You can now browse products, manage your profile, and place orders more easily.
                </p>
            @endif

            <p style="margin:0; font-size:15px; line-height:1.8;">
                If you did not create this account, please contact support as soon as possible.
            </p>

            <div style="margin-top:24px; padding-top:18px; border-top:1px solid #e5e7eb; font-size:13px; color:#64748b;">
                This is an automated message from {{ config('app.name') }}.
            </div>
        </div>
    </div>
</body>
</html>
