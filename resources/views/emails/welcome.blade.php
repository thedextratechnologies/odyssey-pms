<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1A1A2E; color: #fff; padding: 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 6px 0 0; color: #B8960C; font-size: 13px; }
        .body { padding: 32px; }
        .body p { color: #444; font-size: 14px; line-height: 1.7; }
        .creds { background: #F5E6C8; border-left: 4px solid #B8960C; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .creds p { margin: 4px 0; font-size: 14px; color: #333; }
        .creds strong { color: #1A1A2E; }
        .btn { display: inline-block; background: #B8960C; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: bold; margin-top: 12px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Odyssey Elevators</h1>
        <p>Proposal Management System</p>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Welcome to the Odyssey Elevators Proposal Management System. Your account has been created by the system administrator.</p>
        <p>Your login credentials are:</p>
        <div class="creds">
            <p><strong>Login URL:</strong> {{ config('app.url') }}/login</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Temporary Password:</strong> {{ $password }}</p>
            <p><strong>Role:</strong> {{ $user->role?->display_name }}</p>
        </div>
        <p>⚠️ You will be required to change your password the first time you log in.</p>
        <a href="{{ config('app.url') }}/login" class="btn">Login to Odyssey PMS →</a>
        <p style="margin-top:24px; font-size:13px; color:#888;">If you did not expect this email, please contact your system administrator immediately.</p>
    </div>
    <div class="footer">© {{ date('Y') }} Odyssey Elevators Pvt Ltd · Chennai, Tamil Nadu</div>
</div>
</body>
</html>
