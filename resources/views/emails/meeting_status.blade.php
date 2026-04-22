<!DOCTYPE html>
<html>
<head>
    <title>Meeting Status Updated</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .status { font-weight: bold; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; font-size: 14px; }
        .status-confirmed { background: #D1FAE5; color: #065F46; }
        .status-rejected { background: #FEE2E2; color: #991B1B; }
        .status-completed { background: #DBEAFE; color: #1E40AF; }
        .status-pending { background: #FEF3C7; color: #92400E; }
        .footer { font-size: 12px; text-align: center; color: #777; margin-top: 30px; }
    </style>
</head>
<body>
    <div className="container">
        <div className="header">
            <h2>Meeting Status Update</h2>
        </div>
        <p>Dear {{ $meeting->full_name }},</p>
        <p>We're writing to inform you that the status of your meeting request ({{ $meeting->tracking_id }}) has been updated.</p>
        
        <div className="details">
            <p><strong>Current Status:</strong> 
                <span className="status status-{{ $meeting->status }}">{{ strtoupper($meeting->status) }}</span>
            </p>
            <p><strong>Meeting Details:</strong></p>
            <ul>
                <li><strong>Meeting Type:</strong> {{ $meeting->meeting_type }}</li>
                <li><strong>Scheduled For:</strong> {{ \Carbon\Carbon::parse($meeting->scheduled_at)->format('F j, Y, g:i a') }} ({{ $meeting->timezone }})</li>
                <li><strong>Duration:</strong> {{ $meeting->duration }} minutes</li>
            </ul>
        </div>

        @if($meeting->internal_notes)
            <p><strong>Message from our team:</strong></p>
            <p className="details">{{ $meeting->internal_notes }}</p>
        @endif

        <p>If you have any questions, please feel free to reach out to us.</p>
        
        <p>Best regards,<br>The Care & Support Team</p>
        
        <div className="footer">
            &copy; {{ date('Y') }} Care & Support Web. All rights reserved.
        </div>
    </div>
</body>
</html>