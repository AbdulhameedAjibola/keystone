<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Received</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; }
        .content { padding: 20px; }
        .footer { background-color: #f4f4f4; padding: 10px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Job Application Received</h1>
        </div>
        <div class="content">
            <p>Dear Hiring Team,</p>
            <p>A new job application has been submitted. Here are the details:</p>
            <ul>
                <li><strong>Name:</strong> {{ $name }}</li>
                <li><strong>Email:</strong> {{ $email }}</li>
                <li><strong>Phone Number:</strong> {{ $phoneNumber }}</li>
                <li><strong>Job Title:</strong> {{ $jobTitle }}</li>
                <li><strong>Message:</strong> {{ $applicantMessage }}</li>
            </ul>
            <p>Please review the application and follow up as necessary.</p>
            <p>Best regards,<br>Your Application System</p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
