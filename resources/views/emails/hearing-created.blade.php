<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Hearing Notification</title>
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
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e2e8f0;
        }
        .hearing-details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #2563eb;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #6b7280;
        }
        .unsubscribe {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Housing Hearing Posted</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>A new {{ strtolower($hearing->type) }} hearing has been posted in {{ $hearing->region->name }} that may interest you:</p>
        
        <div class="hearing-details">
            <h2>{{ $hearing->title }}</h2>
            
            <p><strong>Type:</strong> {{ $hearing->type }}</p>
            <p><strong>Date & Time:</strong> {{ $hearing->hearing_date->format('l, F j, Y \a\t g:i A') }}</p>
            <p><strong>Region:</strong> {{ $hearing->region->name }}</p>
            
            @if($hearing->location)
                <p><strong>Location:</strong> {{ $hearing->location }}</p>
            @endif
            
            @if($hearing->description)
                <p><strong>Description:</strong></p>
                <p>{{ $hearing->description }}</p>
            @endif
            
            @if($hearing->meeting_url)
                <p><strong>Meeting Link:</strong> <a href="{{ $hearing->meeting_url }}">{{ $hearing->meeting_url }}</a></p>
            @endif
        </div>
        
        <p>
            <a href="{{ route('hearings.show', $hearing) }}" class="button">
                View Full Details
            </a>
        </p>
        
        <div class="footer">
            <p>You're receiving this email because you've subscribed to hearing notifications for {{ $hearing->region->name }}.</p>
            
            <p>You can manage your notification preferences in your <a href="{{ route('user.dashboard') }}">dashboard</a>.</p>
            
            <div class="unsubscribe">
                <p>If you no longer wish to receive these notifications, you can <a href="{{ route('user.dashboard') }}">update your preferences</a> or unsubscribe from this region.</p>
            </div>
        </div>
    </div>
</body>
</html>
