<!DOCTYPE html>
<html>
<head>
    <title>Your Temporary Password</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Your temporary password is: <strong>{{ $tempPassword }}</strong></p>
    <p>Please use this temporary password to log in and change your password immediately.</p>
    <p>Thank you!</p>
</body>
</html>
