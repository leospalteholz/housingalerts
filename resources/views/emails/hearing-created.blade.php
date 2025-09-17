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
            color: black;
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
    <div class="content">
        <p>Hello {{ $user->name }},</p>

        @if($hearing->type === 'development')
            <p>Housing is being proposed in {{ $hearing->region->name }} and it needs your help!</p>
        @else
            <p>An important housing policy hearing is coming up in {{ $hearing->region->name }} and it needs your help to pass!</p>
        @endif
        
        <div class="hearing-details">
            <h2>{{ $hearing->title }}</h2>
            
            <p><strong>Date & Time:</strong> {{ $hearing->hearing_date->format('l, F j, Y \a\t g:i A') }}</p>
            <p><strong>Description:</strong></p>
            <p>{{ $hearing->description }}</p>
                        
            @if($hearing->meeting_url)
                <p><strong>Meeting Link:</strong> <a href="{{ $hearing->meeting_url }}">{{ $hearing->meeting_url }}</a></p>
            @endif
        </div>

        <div class="call-for-action">
            <h2>What You Can Do</h2>
            <ol>
                <li>Good - Send your support to {{ $hearing->comments_email }}</li>
                <li>Amazing - Speak in support at this hearing.  You can do that over the phone or in person.</li>
                <li>Cherry on top - Share this hearing with your network and encourage others to participate.</li>
            </ol>
        </div>
        
        <p>
            <a href="{{ route('hearings.show', $hearing) }}" class="button">
                View Details
            </a>
        </p>
        
        <div class="footer">
            <p>You're receiving this email because you've subscribed to hearing notifications for {{ $hearing->region->name }}.</p>
            
            <p>You can manage your notification preferences in your <a href="{{ route('dashboard') }}">dashboard</a>.</p>
            
            <div class="unsubscribe">
                <p>If you no longer wish to receive notifications about hearings in {{ $hearing->region->name }}, you can <a href="{{ route('dashboard') }}">unsubscribe</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>
