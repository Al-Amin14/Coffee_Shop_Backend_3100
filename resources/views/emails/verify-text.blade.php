<!DOCTYPE html>
<html>
<head>
    <title>Verify your email</title>
</head>
<body>
    <p>Hello {{ $name }},</p>
    <p>Please verify your email by clicking the link below:</p>
    <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
    <p>If you did not register, please ignore this email.</p>
</body>
</html>
