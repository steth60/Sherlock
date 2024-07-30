<!-- resources/views/emails/login-notification.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Login Notification</title>
</head>
<body>
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" style="max-width: 200px;">
    </div>

    <h1 style="color: #333; font-size: 24px; text-align: center; margin-bottom: 20px;">New Login Detected</h1>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Hello {{ $user->name }},
    </p>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        We noticed a new login to your account from a new device. Here are the details:
    </p>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        <strong>IP Address:</strong> {{ $ipAddress }}<br>
        <strong>Time:</strong> {{ $loginTime }}
    </p>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-top: 20px;">
        If you didn't recognize this login, please secure your account by changing your password immediately.
    </p>

    <div style="border-top: 1px solid #e8e5ef; margin-top: 30px; padding-top: 20px; text-align: center; color: #777; font-size: 14px;">
        <p>If you're having trouble clicking the "Secure Your Account" button, copy and paste the URL below into your web browser:</p>
        <p style="word-break: break-all;">{{ $secureAccountUrl }}</p>
    </div>

    <div style="margin-top: 30px; text-align: center; color: #777; font-size: 14px;">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>
