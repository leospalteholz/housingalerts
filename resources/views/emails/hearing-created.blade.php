<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Hearing Notification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        
        .email-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        
        .header .subtitle {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .content {
            padding: 32px 24px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 24px;
            color: #374151;
        }
        
        .intro-text {
            font-size: 16px;
            margin-bottom: 32px;
            color: #4b5563;
            line-height: 1.7;
        }
        
        .hearing-card {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
            margin: 32px 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .hearing-image {
            width: 100%;
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .hearing-title {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 16px 0;
            line-height: 1.3;
        }
        
        .hearing-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        
        .meta-label {
            font-weight: 600;
            color: #374151;
            min-width: 80px;
            flex-shrink: 0;
        }
        
        .meta-value {
            color: #4b5563;
            flex: 1;
        }
        
        .meta-value a {
            color: #2563eb;
            text-decoration: none;
        }
        
        .meta-value a:hover {
            text-decoration: underline;
        }
        
        .description {
            background-color: white;
            padding: 16px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            margin-top: 16px;
            color: #4b5563;
            line-height: 1.6;
        }
        
        .action-section {
            margin: 32px 0;
        }
        
        .action-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .action-cards {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .action-card {
            background-color: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
        }
        
        .action-card.good {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #f0fdf4 100%);
        }
        
        .action-card.amazing {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #fffbeb 100%);
        }
        
        .action-card.cherry {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, #faf5ff 0%, #faf5ff 100%);
        }
        
        .action-level {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        
        .action-card.good .action-level {
            background-color: #10b981;
            color: white;
        }
        
        .action-card.amazing .action-level {
            background-color: #f59e0b;
            color: white;
        }
        
        .action-card.cherry .action-level {
            background-color: #8b5cf6;
            color: white;
        }
        
        .action-text {
            font-size: 16px;
            color: #374151;
            line-height: 1.5;
            margin: 0;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            margin: 32px 0;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .footer {
            background-color: #f9fafb;
            padding: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .unsubscribe {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .content {
                padding: 24px 16px;
            }
            
            .hearing-card {
                padding: 16px;
            }
            
            .header {
                padding: 24px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏠 Housing Alert</h1>
            <p class="subtitle">Your voice matters for housing in {{ $hearing->region->name }}</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $user->name }},</p>

            @if($hearing->type === 'development')
                <p class="intro-text">Housing is being proposed in {{ $hearing->region->name }} and it needs your help! Your support can make the difference in getting more homes built in your community.</p>
            @else
                <p class="intro-text">An important housing policy hearing is coming up in {{ $hearing->region->name }} and it needs your help to pass! This policy could have a significant impact on housing in your area.</p>
            @endif
            
            <div class="hearing-card">
                @if($hearing->image_url)
                    <img src="{{ $hearing->image_url }}" alt="Hearing image" class="hearing-image">
                @endif
                
                <h2 class="hearing-title">{{ $hearing->title }}</h2>
                
                <div class="hearing-meta">
                    <div class="meta-item">
                        <span class="meta-label">📅 Date:</span>
                        <span class="meta-value">{{ $hearing->start_datetime->format('l, F j, Y \a\t g:i A') }}</span>
                    </div>
                </div>
                
                @if($hearing->description)
                    <div class="description">
                        {{ $hearing->description }}
                    </div>
                @endif
            </div>

            <div class="action-section">
                <h2 class="action-title">Here's How You Can Help</h2>
                
                <div class="action-cards">
                    <div class="action-card good">
                        <div class="action-level">Good</div>
                        <p class="action-text">Send your support via email to <a href="mailto:{{ $hearing->comments_email }}">{{ $hearing->comments_email }}</a>. Even a simple message saying you support more housing makes a difference!</p>
                    </div>
                    
                    <div class="action-card amazing">
                        <div class="action-level">Amazing</div>
                        <p class="action-text">Add this hearing to your calendar and plan to speak in support at this hearing. You can participate over the phone or in person. Your personal story about housing matters!</p>
                    </div>
                    
                    <div class="action-card cherry">
                        <div class="action-level">Cherry on Top</div>
                        <p class="action-text">Share this hearing with your network and encourage others to participate. The more voices supporting housing, the better!</p>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('hearings.show', $hearing) }}" class="cta-button">
                    View hearing details
                </a>
            </div>
        </div>
        
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
