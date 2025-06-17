<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $announcement->title }}</title>
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

        .announcement-message {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #eaeaea;
        }

        .deadline-info {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #ffeaa7;
            color: #856404;
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

        .view-url {
            display: block;
            text-align: center;
            color: #7A1212;
            font-size: 12px;
            margin-top: -30px;
            margin-bottom: 30px;
            text-decoration: none;
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
            <h1>New Announcement</h1>
            
            <p class="first-p">
                You have received a new announcement from the E-Skolarian system.
            </p>
            
            <div class="announcement-message" style="overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
                <span style="font-weight: 500;"><strong>{{ $announcement->title }}</strong></span><br><br>
                {{ $announcement->content }}
            </div>
            
            @if($announcement->deadline)
                <div class="deadline-info">
                    <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($announcement->deadline)->format('F j, Y \a\t g:i A') }}
                </div>
            @endif
            
            <a class="action-button" href="{{ route('student.dashboard') }}">View Dashboard</a>
            <a href="{{ route('student.dashboard') }}" class="view-url">{{ route('student.dashboard') }}</a>
            
            <p class="last-p">
                This announcement was posted on {{ $announcement->created_at->format('F j, Y \a\t g:i A') }}.
            </p>
        </div>
        <hr>
        <div class="footer-card">
            <p class="first-p">© E-skolarian - Document Management System</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p class="contact-info">
                <strong>Contact No:</strong> 0961 802 3780<br>
                <strong>Email:</strong> eskolarian@gmail.com
            </p>
        </div>
    </div>
</body>
</html>