{{-- user-notification.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>
    @if($isNewUser)
      Welcome to E-skolarian - Account Created
    @elseif($isDeactivated)
      E-skolarian - Account Deactivated
    @else
      E-skolarian - Account Update Notification
    @endif
  </title>
  <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font: 14px / 1.2 "Montserrat", "Helvetica", sans-serif;
    }

    body {
        background-color: #7A1212;
    }

    .card-container  {
        width: 100%;
        max-width: 700px;
        padding: 20px 20px 20px 20px;
        height: 100%;
        margin: auto;
        background-color: #ffffff;
    }

    .header-card {
        text-align: center;
        height: 90px;
    }

    .logo {
        width: 240px;
    }

    .body-card {
        padding: 30px 0 15px;
        margin: auto;
        width: 90%;
    }

    .body-card h1 {
        font-size: 18px;
        font-weight: bold;
        color: #7A1212;
    }

    .body-card p{
        font-weight: 600;
    }

    .body-card .first-p {
        padding-top: 20px;
    }

    .action-button {
        display: block;
        white-space: normal;
        text-decoration: none;
        text-transform: uppercase;
        font-weight: bold;
        font-size: 25px;
        margin: 40px auto;
        text-align: center;
        max-width: 300px;
        padding: 20px;
        font-family: 'Verdana', sans-serif;
        border-radius: 10px;
        background-color: #F5C518;
        color: #2C2C2C;
    }

    .body-card .last-p {
        color: #AFADAD;
        font-style: italic;
    }

    .login-url {
        display: block;
        text-align: center;
        color: #7A1212;
        font-size: 12px;
        margin-top: -30px;
        margin-bottom: 30px;
        text-decoration: none;
    }

    .info-box {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
        border: 1px solid #eaeaea;
    }

    .info-box p {
        margin: 8px 0;
    }

    .footer-card {
        padding: 15px;
        margin: auto;
        width: 90%;
        color: #A6A6A6;
        text-align: center;
    }

    .footer-card .first-p {
        font-weight: 600;
    }

    hr {
        margin: 0 auto;
        width: 90%;
    }

    .deactivated-notice {
        color: #7A1212;
        font-weight: bold;
    }
  </style>
</head>
<body>
    <div class="card-container">
        <div class="header-card">
            <img class="logo" src="{{ asset('images/e-skolarianLogo.svg') }}" alt="E-skolarian Logo">
        </div>
            <div class="body-card">
        <h1>Hello {{ $user->username }},</h1>

        @if($isNewUser)
            <p class="first-p">
                We are pleased to inform you that an account has been successfully created for you in the E-Skolarian: Document Management System of PUP Santa Rosa.
            </p>
            <p>Here are your account details:</p>
        @elseif($isDeactivated)
            <p class="first-p deactivated-notice">
                Your account in the E-Skolarian system has been deactivated.
            </p>
            <p>This means you will no longer be able to access the system using these credentials:</p>
        @elseif($isReactivated)
            <p class="first-p">
                Your account in the E-Skolarian system has been reactivated. You can now access the system again using your existing credentials.
            </p>
            <p>Here are your account details:</p>
        @else
            <p class="first-p">
                Your account information has been updated in the E-Skolarian system.
            </p>
            <p>Here are your updated account details:</p>
        @endif

        <div class="info-box">
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role_name }}</p>
            @if($isNewUser || $isReactivated)
                <p><strong>Default Password:</strong> {{ $password }}</p>
            @endif
        </div>

        @if($isNewUser || $isReactivated)
            <a class="action-button" href="{{ url('/login') }}">Login to Account</a>
            <a href="{{ url('/login') }}" class="login-url">{{ url('/login') }}</a>
        @endif

        @if($isNewUser || $isReactivated)
            <p>A temporary password has been provided above for your initial login. For security purposes, we recommend changing your password immediately after your first login.</p>
        @elseif($isDeactivated)
            <p>If you believe this action was taken in error or if you need to reactivate your account, please contact the system administrator.</p>
        @elseif($isReactivated)
            <p class="last-p">
                If you have any questions or need assistance, please don't hesitate to contact our support team.
            </p>
        @endif
    </div>
        <hr>
        <div class="footer-card">
            <p class="first-p">© E-skolarian - Document Management System</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p class="contact-info">
                <strong>Contact No:</strong> 0961 802 3780<br>
                <strong>Email:</strong> starosa@pup.edu.ph
            </p>
        </div>
    </div>
</body>
</html>