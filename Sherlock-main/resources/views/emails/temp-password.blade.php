<!DOCTYPE html>
<html>
<head>
    <title>Sherlock - Your Temporary Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Sherlock" style="max-width: 200px;">
    </div>

    <h1 style="color: #333; font-size: 24px; text-align: center; margin-bottom: 20px;">Your Temporary Password</h1>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Hello, {{ $user->name }},
    </p>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Your temporary password is:
    </p>

    <p style="color: #3869D4; font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 20px;">
        {{ $tempPassword }}
    </p>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Please use this temporary password to log in and change your password immediately.
    </p>

    <p style="text-align: center;">
        <a href="{{ route('login') }}" style="background-color: #3869D4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Log in here</a>
    </p>

    <p style="color: #555; font-size: 16px; line-height: 1.5; margin-top: 20px;">
        If you didn't request a temporary password, please contact our support team immediately.
    </p>

    <div style="border-top: 1px solid #e8e5ef; margin-top: 30px; padding-top: 20px; text-align: center; color: #777; font-size: 14px;">
        <p>If you're having trouble clicking the "Log in here" button, copy and paste the URL below into your web browser:</p>
        <p style="word-break: break-all;">{{ route('login') }}</p>
    </div>

    <div style="margin-top: 30px; text-align: center; color: #777; font-size: 14px;">
        &copy; {{ date('Y') }} Sherlock. All rights reserved.
    </div>
</body>
</html>