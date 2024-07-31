<!DOCTYPE html>
<html>
<head>
    <title>Your MFA Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            text-align: center;
            margin-bottom: 20px;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #3869D4;
            margin: 20px 0;
        }
        .footer {
            border-top: 1px solid #e8e5ef;
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #333; font-size: 24px; margin-bottom: 20px;">Your MFA Code</h1>
        
        <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
            Dear person
        </p>
        
        <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
            Your MFA code is:
        </p>
        
        <p class="code">
            {{ $code }}
        </p>

        <p style="color: #555; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
            This code will expire in 10 minutes.
        </p>
    </div>

    <div class="footer">
        <p>If you did not request this MFA code, please ignore this email or contact support if you have any concerns.</p>
        <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</body>
</html>
