<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Your Password</title>
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

    .reset__link {
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
</style>
</head>
<body>
    <div class="card-container">
        <div class="header-card">
            <img class="logo" src="{{ asset('images/e-skolarianLogo.svg') }}" alt="E-skolarian Logo">
        </div>
        <div class="body-card">
            <h1>Hello {{ $displayRole }},</h1>
            <p class="first-p">
                You are receiving this email because we received a password reset request for your E-skolarian account.
                Please click the button below to securely create a new password.
            </p>
            <a class="reset__link" href="{{ $url }}">Reset Password</a>
            <p class="last-p">
                This password reset link will expire in 15 minutes. If you did not request a password reset, no further action is required.
            </p>
        </div>
        <hr>
        <div class="footer-card">
            <p class="first-p">© E-skolarian - Document Management System</p>
            <p>To help keep your account secure, please do not forward this email.</p>
            <p class="contact-info">
                <strong>Contact No:</strong> 0961 802 3780<br>
                <strong>Email:</strong> starosa@pup.edu.ph
            </p>
        </div>
    </div>
</body>
</html>
