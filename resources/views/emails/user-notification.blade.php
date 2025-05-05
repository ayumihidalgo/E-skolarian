{{-- user-notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isNewUser ? 'Welcome to Our Platform' : 'Your Account Has Been Updated' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4338ca;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            background-color: #4338ca;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $isNewUser ? 'Welcome to Our Platform!' : 'Account Update Notification' }}</h1>
    </div>

    <div class="content">
        <p>Hello {{ $user->username }},</p>

        @if($isNewUser)
            <p>Your account has been created successfully.</p>
            <p>Here are your account details:</p>
        @else
            <p>Your account information has been updated.</p>
            <p>Here are your updated account details:</p>
        @endif

        <div class="info">
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role_name }}</p>
        </div>

        @if($isNewUser)
            <p>A temporary password has been set for your account. For security reasons, we recommend changing your password after your first login.</p>

            <p style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Login to Your Account</a>
            </p>
        @endif

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Thank you,<br>The Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message, please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</body>
</html>