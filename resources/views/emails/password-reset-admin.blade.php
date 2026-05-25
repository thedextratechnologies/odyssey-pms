<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 12px; overflow: hidden; }
        .header { background: #1A1A2E; color: #fff; padding: 32px; text-align: center; }
        .body { padding: 32px; }
        .body p { color: #444; font-size: 14px; line-height: 1.7; }
        .creds { background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .creds p { margin: 4px 0; font-size: 14px; }
        .btn { display: inline-block; background: #B8960C; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: bold; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
<div class="card">
    <div class="header"><h1>Password Reset — Odyssey PMS</h1></div>
    <div class="body">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Your password has been reset by a system administrator. Use the temporary password below to log in and set a new password.</p>
        <div class="creds">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Temporary Password:</strong> {{ $password }}</p>
        </div>
        <a href="{{ config('app.url') }}/login" class="btn">Login Now →</a>
    </div>
    <div class="footer">© {{ date('Y') }} Odyssey Elevators Pvt Ltd</div>
</div>
</body>
</html>
