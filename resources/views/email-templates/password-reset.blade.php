@component('mail::message')
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ asset('path/to/your/logo.png') }}" alt="{{ config('app.name') }}" style="max-width: 200px;">
</div>

<h1 style="color: #333; font-size: 24px; text-align: center; margin-bottom: 20px;">Password Reset Request</h1>

<p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
    Hello,
</p>

<p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
    We received a request to reset the password for your account. If you made this request, please click the button below to reset your password:
</p>

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Reset Your Password
@endcomponent

<p style="color: #555; font-size: 16px; line-height: 1.5; margin-top: 20px;">
    If you didn't request a password reset, please ignore this email or <a href="{{ route('contact.support') }}" style="color: #3869D4;">contact our support team</a> if you have concerns about your account's security.
</p>

<p style="color: #555; font-size: 16px; line-height: 1.5; margin-top: 20px;">
    This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes.
</p>

<div style="border-top: 1px solid #e8e5ef; margin-top: 30px; padding-top: 20px; text-align: center; color: #777; font-size: 14px;">
    <p>If you're having trouble clicking the "Reset Your Password" button, copy and paste the URL below into your web browser:</p>
    <p style="word-break: break-all;">{{ $url }}</p>
</div>

<div style="margin-top: 30px; text-align: center; color: #777; font-size: 14px;">
    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</div>
@endcomponent