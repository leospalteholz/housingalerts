<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hearing Reminder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #fef2f2;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #fecaca;
        }
        .hearing-details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #dc2626;
        }
        .urgent-notice {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
            color: #dc2626;
        }
        .button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .calendar-button {
            display: inline-block;
            background-color: #059669;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 10px 10px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #fecaca;
            font-size: 14px;
            color: #6b7280;
        }
        .unsubscribe {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }
        .time-highlight {
            background-color: #fef3c7;
            padding: 10px;
            border-radius: 4px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Hearing Reminder - TODAY</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <div class="urgent-notice">
            ⏰ REMINDER: There's a {{ strtolower($hearing->type) }} hearing happening TODAY!
        </div>
        
        <div class="hearing-details">
            <h2>{{ $hearing->title }}</h2>
            
            <div class="time-highlight">
                📅 TODAY - {{ $hearing->hearing_date->format('g:i A') }}
            </div>
            
            <p><strong>Type:</strong> {{ $hearing->type }}</p>
            <p><strong>Region:</strong> {{ $hearing->region->name }}</p>
            
            @if($hearing->location)
                <p><strong>Location:</strong> {{ $hearing->location }}</p>
            @endif
            
            @if($hearing->meeting_url)
                <p><strong>📹 Join Online:</strong> <a href="{{ $hearing->meeting_url }}" style="color: #2563eb;">{{ $hearing->meeting_url }}</a></p>
            @endif
            
            @if($hearing->description)
                <p><strong>What's being discussed:</strong></p>
                <p>{{ $hearing->description }}</p>
            @endif
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('hearings.show', $hearing) }}" class="button">
                View Full Details
            </a>
            
            @if($hearing->meeting_url)
                <br>
                <a href="{{ $hearing->meeting_url }}" class="calendar-button">
                    Join Meeting Now
                </a>
            @endif
        </div>
        
        <div style="margin: 20px 0; padding: 15px; background-color: #f0f9ff; border-radius: 6px; border-left: 4px solid #0ea5e9;">
            <h3 style="margin-top: 0; color: #0ea5e9;">💡 How to Participate:</h3>
            <ul style="margin-bottom: 0;">
                <li>Review the agenda and materials beforehand</li>
                <li>Prepare your questions or comments</li>
                <li>Join a few minutes early to test your connection</li>
                <li>Unmute only when speaking (if online)</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>You requested day-of reminders for hearings in {{ $hearing->region->name }}.</p>
            
            <p>Your voice matters! Participating in these hearings helps shape housing policy in your community.</p>
            
            <p>You can manage your notification preferences in your <a href="{{ route('user.dashboard') }}">dashboard</a>.</p>
            
            <div class="unsubscribe">
                <p>To stop receiving day-of reminders, <a href="{{ route('notification-settings') }}">update your notification settings</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>
