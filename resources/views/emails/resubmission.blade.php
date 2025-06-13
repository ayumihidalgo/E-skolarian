<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Document Requires Resubmission</title>
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
                                        <h1 style="font-size: 18px; font-weight: bold; color: #7A1212;">Document Requires Resubmission</h1>
                                        <p style="font-weight: 600; padding-top: 20px; color:#000000;">
                                            Your document <strong>{{ $document->title }}</strong> requires changes and resubmission.
                                        </p>
                                        <div style="background-color: #f8f9fa; border-left: 4px solid #7A1212; padding: 15px; margin: 20px 0;">
                                            <h2 style="font-size: 16px; margin: 0 0 10px 0; color: #7A1212;">Feedback from Admin:</h2>
                                            <p style="margin: 0; color: #333333;">{{ $message }}</p>
                                        </div>
                                        <a href="{{ route('guest.document.view', [
                                            'id' => $document->id, 
                                            'email_hash' => hash('sha256', $document->guest_webmail),
                                            'timestamp' => time()
                                        ]) }}" 
                                        style="display: block; white-space: normal; text-decoration: none; text-transform: uppercase; font-weight: bold; font-size: 25px; margin: 40px auto; text-align: center; max-width: 280px; padding: 20px; font-family: 'Verdana', sans-serif; border-radius: 10px; background-color: #F5C518; color: #2C2C2C;">
                                            View Document
                                        </a>
                                        <p style="color: #000000; font-weight: 600;">
                                            Please make the necessary changes and resubmit your document.
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
                                            <strong>Document Management System</strong> <br>
                                            To help keep your account secure, please do not forward this email. <br>
                                            <strong>Contact No:</strong> 0961 802 3780<br>
                                            <strong>Email:</strong> starosa@pup.edu.ph
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