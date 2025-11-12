<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .password-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello,</p>

            <p>Your password has been successfully reset. Below is your new password:</p>

            <div class="password-box">
                {{ $password }}
            </div>

            <p><strong>Important:</strong></p>
            <ul>
                <li>Please change this password after your first login for security reasons</li>
                <li>Do not share this password with anyone</li>
                <li>If you didn't request this password reset, please contact support immediately</li>
            </ul>

            <p>Your email: <strong>{{ $email }}</strong></p>

            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>
