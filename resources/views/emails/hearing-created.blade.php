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
        
        .checklist {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
        }
        
        .checklist-item {
            display: flex;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .checklist-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .checklist-item:first-child {
            padding-top: 0;
        }
        
        .checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            margin-right: 16px;
            flex-shrink: 0;
            margin-top: 2px;
            position: relative;
            background-color: white;
        }
        
        .checkbox::after {
            content: '';
            position: absolute;
            left: 7px;
            top: 3px;
            width: 6px;
            height: 10px;
            border: solid #10b981;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            opacity: 0;
        }
        
        .checklist-item.completed .checkbox {
            background-color: #10b981;
            border-color: #10b981;
        }
        
        .checklist-item.completed .checkbox::after {
            opacity: 1;
            border-color: white;
        }
        
        .checklist-content {
            flex: 1;
        }
        
        .checklist-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
            font-size: 16px;
        }
        
        .checklist-description {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }
        
        .checklist-description a {
            color: #2563eb;
            text-decoration: none;
        }
        
        .checklist-description a:hover {
            text-decoration: underline;
        }
        
        .cta-button {
            display: inline-block;
            background: #2563eb !important;
            color: #ffffff !important;
            padding: 16px 32px;
            text-decoration: none !important;
            border-radius: 8px;
            margin: 32px 0;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
            border: 2px solid #2563eb;
        }
        
        .cta-button:hover {
            background: #1d4ed8 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            text-decoration: none !important;
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
                <h2 class="action-title">Your Action Checklist</h2>
                
                <div class="checklist">
                    <div class="checklist-item">
                        <div class="checkbox"></div>
                        <div class="checklist-content">
                            <div class="checklist-label">📧 Send an email in support</div>
                            <p class="checklist-description">
                                Email <a href="mailto:{{ $hearing->comments_email }}">{{ $hearing->comments_email }}</a> with your support. Even a simple message saying you support more housing makes a difference!
                            </p>
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="checkbox"></div>
                        <div class="checklist-content">
                            <div class="checklist-label">🗣️ Speak at the hearing</div>
                            <p class="checklist-description">
                                Add this hearing to your calendar and plan to speak in support. You can participate over the phone or in person. Your personal story about housing matters!
                            </p>
                        </div>
                    </div>
                    
                    <div class="checklist-item">
                        <div class="checkbox"></div>
                        <div class="checklist-content">
                            <div class="checklist-label">📢 Share with your network</div>
                            <p class="checklist-description">
                                Forward this email or share details about this hearing with friends, family, and colleagues. The more voices supporting housing, the better!
                            </p>
                        </div>
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
