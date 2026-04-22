<!DOCTYPE html>
<html>
<head>
    <title>Meeting Request Received</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .tracking-id { font-weight: bold; color: #4F46E5; }
        .footer { font-size: 12px; text-align: center; color: #777; margin-top: 30px; }
    </style>
</head>
<body>
    <div className="container">
        <div className="header">
            <h2>Meeting Request Received</h2>
        </div>
        <p>Dear {{ $meeting->full_name }},</p>
        <p>Thank you for your interest in meeting with us. We have received your request and our team will review it shortly.</p>
        
        <div className="details">
            <p><strong>Tracking ID:</strong> <span className="tracking-id">{{ $meeting->tracking_id }}</span></p>
            <p><strong>Meeting Type:</strong> {{ $meeting->meeting_type }}</p>
            <p><strong>Requested Date:</strong> {{ \Carbon\Carbon::parse($meeting->scheduled_at)->format('F j, Y, g:i a') }} ({{ $meeting->timezone }})</p>
            <p><strong>Duration:</strong> {{ $meeting->duration }} minutes</p>
        </div>

        <p>You will receive another email once your meeting request has been reviewed and updated by our team.</p>
        
        <p>Best regards,<br>The Care & Support Team</p>
        
        <div className="footer">
            &copy; {{ date('Y') }} Care & Support Web. All rights reserved.
        </div>
    </div>
</body>
</html>