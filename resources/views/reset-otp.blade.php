<!DOCTYPE html>
<html>
<head>
    <style>
        .otp-container {
            background-color: #f4f4f4;
            border: 2px dashed #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #1e3a8a;
            margin: 0;
        }
        .footer-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 20px;
        }
    </style>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">

    <span style="display:none; visibility:hidden; color:transparent; max-height:0; max-width:0; opacity:0; overflow:hidden;">
        Your code is: {{ $token }}. Use this to reset your password.
    </span>

    <p>Hello,</p>
    
    <p>You requested to reset the password for <strong>{{ $email }}</strong>. Use the code below to complete the process:</p>

    <div class="otp-container">
        <p style="margin-top: 0; color: #666; font-size: 14px;">YOUR ONE-TIME PASSWORD</p>
        <h1 class="otp-code">{{ $token }}</h1>
    </div>

    <p>This code is valid for <strong>10 minutes</strong>.</p>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

    <p class="footer-text">
        If you did not request a password reset, you can safely ignore this email. 
        For security, never share this code with anyone.
    </p>

</body>
</html>