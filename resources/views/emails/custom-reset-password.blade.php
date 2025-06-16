<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; box-sizing: border-box; background-color: #7A1212; font-family: 'Montserrat', 'Helvetica', sans-serif; font-size: 14px; line-height: 1.2;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #7A1212; margin: 0; padding: 0;">
    <tr>
      <td align="center">
        <table width="700" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 700px; background-color: #ffffff; padding: 20px;">
          <tr>
            <td>
              <!-- Header -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="text-align: center; height: 90px;">
                    <img src="{{ asset('images/e-skolarianLogo.png') }}" alt="E-skolarian Logo" style="width: 100%; max-width: 240px;">
                  </td>
                </tr>
              </table>
              <!-- Body -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding: 30px 0 15px; margin: auto; width: 90%;">
                    <h1 style="font-size: 18px; font-weight: bold; color: #7A1212;">Hello {{ $displayRole }},</h1>
                    <p style="font-weight: 600; padding-top: 20px; color:#000000;">
                      You are receiving this email because we received a password reset request for your E-skolarian account.
                      Please click the button below to securely create a new password.
                    </p>
                    <a href="{{ $url }}" style="display: block; white-space: normal; text-decoration: none; text-transform: uppercase; font-weight: bold; font-size: 25px; margin: 40px auto; text-align: center; max-width: 280px; padding: 20px; font-family: 'Verdana', sans-serif; border-radius: 10px; background-color: #F5C518; color: #2C2C2C;">
                      Reset Password
                    </a>
                    <p style="color: #AFADAD; font-style: italic; font-weight: 600;">
                      This password reset link will expire in 15 minutes. If you did not request a password reset, no further action is required.
                    </p>
                  </td>
                </tr>
              </table>
              <!-- Divider -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td>
                    <hr style="margin: 0 auto; width: 90%; border: none; border-top: 1px solid #ccc;">
                  </td>
                </tr>
              </table>
              <!-- Footer -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding: 15px; margin: auto; width: 90%; color: #A6A6A6; text-align: center;">
                    <p>
                      <strong>© E-skolarian - Document Management System</strong> <br>
                      To help keep your account secure, please do not forward this email. <br>
                      <strong>Contact No:</strong> 0961 802 3780<br>
                      <strong>Email:</strong> eskolarian@gmail.com
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
