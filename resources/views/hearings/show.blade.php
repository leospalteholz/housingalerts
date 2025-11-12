<x-app-layout>
    <x-slot name="title">{{ $hearing->title }} - {{ config('app.name') }}</x-slot>
    
    <x-slot name="meta">
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="article">
        <meta property="og:title" content="{{ $hearing->title }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($hearing->description), 150) }}">
    <meta property="og:url" content="{{ route('hearings.show', ['hearing' => $hearing]) }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        @if($hearing->image_url)
            <meta property="og:image" content="{{ $hearing->image_url }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
        @endif
        <meta property="article:published_time" content="{{ $hearing->created_at->toISOString() }}">
        <meta property="article:modified_time" content="{{ $hearing->updated_at->toISOString() }}">
        
        <!-- Twitter -->
        <meta name="twitter:card" content="{{ $hearing->image_url ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $hearing->title }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($hearing->description), 150) }}">
        @if($hearing->image_url)
            <meta name="twitter:image" content="{{ $hearing->image_url }}">
        @endif
        
        <!-- Additional meta -->
        <meta name="description" content="{{ Str::limit(strip_tags($hearing->description), 160) }}">
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $hearing->title }}
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded shadow">
            <!-- Header Image (if exists) -->
            @if($hearing->image_url)
                <div class="w-full h-48 md:h-64 overflow-hidden rounded-t">
                    <img src="{{ $hearing->image_url }}" alt="Header image for {{ $hearing->display_title }}" class="w-full h-full object-cover">
                </div>
            @endif
            
            <!-- Development Info Bar (for development hearings only) -->
            @if($hearing->isDevelopment())
                <div class="bg-gray-50 border-b px-6 py-4">
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-2 text-sm">
                        <!-- at a glance info -->
                        <div class="flex-shrink-0">
                            <span class="text-gray-900">{{ $hearing->units }} new {{ $hearing->rental ? 'rental' : 'ownership' }} homes at {{ $hearing->street_address }}, {{ $hearing->region->name }}</span>
                        </div>
                        @if($hearing->below_market_units > 0)
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $hearing->below_market_units }} below market units
                                </span>
                            </div>
                        @endif
                        @if($hearing->replaced_units > 0)
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Replacing {{ $hearing->replaced_units }} existing units
                                </span>
                            </div>
                        @endif
                        @if($hearing->subject_to_vote)
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Subject to vote
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="p-6">
                <!-- Float the date/time and calendar button to the right -->
                <div class="float-right ml-6 mb-4 bg-gray-50 rounded-lg p-4 border">
                    <div class="flex items-start justify-between">
                        <div class="text-left">
                            <p class="text-gray-900 text-base font-medium">
                                {{ $hearing->start_datetime->format('M j, Y') }}
                            </p>
                            <p class="text-gray-700 text-sm">
                                {{ $hearing->start_datetime->format('g:i A') }} - {{ $hearing->end_datetime->format('g:i A') }}
                            </p>
                        </div>
                        <div class="ml-3">
                            <x-calendar-button :hearing="$hearing" compact="true" />
                        </div>
                    </div>
                </div>
                
                <!-- Hearing Description that wraps around the floated element -->
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $hearing->description }}</p>
                    </div>
                </div>
                
                <!-- Additional details in a separate section after clearing the float -->
                <div class="clear-both mt-6 space-y-4">
                    
                    <!-- More Information -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">More Information</p>
                        <p class="text-gray-900">
                            <a href="{{ $hearing->more_info_url }}" class="text-blue-600 hover:underline" target="_blank">
                                {{ $hearing->more_info_url }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Clear float before full-width sections -->
            <div class="clear-both"></div>

            <!-- Full-width sections -->
            <div class="space-y-6">
                <!-- How to Help Section -->
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-6 border border-orange-200">
                    <h3 class="text-xl font-semibold text-orange-900 mb-4 flex items-center">
                        <x-icon name="megaphone" class="w-6 h-6 mr-2" />
                        How to Help
                    </h3>
                    
                    <!-- Comments Email - Prominent -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4 border-l-4 border-orange-500">
                            <h4 class="font-semibold text-orange-900 mb-2 flex items-center">
                                <x-icon name="mail" class="w-5 h-5 mr-2" />
                                Submit Written Comments
                            </h4>
                            <p class="text-gray-600 mb-2 text-sm">Send your support via email:</p>
                            <a href="mailto:{{ $hearing->comments_email }}" class="inline-flex items-center bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-3 rounded-lg transition duration-200 text-sm">
                                {{ $hearing->comments_email }}
                            </a>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                            <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                                <x-icon name="share" class="w-5 h-5 mr-2" />
                                Share this hearing
                            </h4>
                            <p class="text-gray-600 mb-2 text-sm">Help spread the word:</p>
                            <div class="flex gap-2 justify-center">
                                <!-- BlueSky -->
                                <a href="https://bsky.app/intent/compose?text={{ urlencode($hearing->title . ' - ' . request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-full transition duration-200"
                                   title="Share on BlueSky">
                                    <x-icon name="bluesky" class="w-4 h-4" />
                                </a>
                                
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-full transition duration-200"
                                   title="Share on Facebook">
                                    <x-icon name="facebook" class="w-4 h-4" />
                                </a>
                                
                                <!-- X (Twitter) -->
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($hearing->title) }}&url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-black hover:bg-gray-800 text-white rounded-full transition duration-200"
                                   title="Share on X">
                                    <x-icon name="x-twitter" class="w-4 h-4" />
                                </a>
                                
                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-blue-700 hover:bg-blue-800 text-white rounded-full transition duration-200"
                                   title="Share on LinkedIn">
                                    <x-icon name="linkedin" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participation Instructions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                                <x-icon name="computer" class="w-5 h-5 mr-2" />
                                Join Virtually
                            </h4>
                            <div class="text-gray-700 text-sm whitespace-pre-wrap">{!! $hearing->remote_instructions_linked !!}</div>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                                <x-icon name="building" class="w-5 h-5 mr-2" />
                                Attend In Person
                            </h4>
                            <div class="text-gray-700 text-sm whitespace-pre-wrap">{!! $hearing->inperson_instructions_linked !!}</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-orange-100 rounded-lg">
                        <p class="text-sm text-orange-800">
                            <strong>💡 Pro tip:</strong> Your voice matters! Participating in housing hearings helps shape your community's future.
                        </p>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action for Non-Authenticated Users -->
        @if(!auth()->check())
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow p-6 mt-6">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-white mb-2">Want to stay informed about hearings like this?</h3>
                    <p class="text-blue-100 mb-4">Sign up to receive email notifications about upcoming housing hearings in your area.</p>
                    <div class="space-x-4">
                        <a href="{{ route('signup') }}" class="bg-white text-blue-600 font-semibold py-2 px-6 rounded-lg hover:bg-gray-100 transition duration-200">
                            Sign Up for Notifications
                        </a>
                        <a href="{{ route('login') }}" class="border border-white text-white font-semibold py-2 px-6 rounded-lg hover:bg-white hover:bg-opacity-10 transition duration-200">
                            Login
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Notification Statistics (Admin/Superuser Only) -->
        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->is_superuser))
            <div class="bg-white rounded shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Notification Information</h3>
            
            <!-- Subscribed Users Table -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-md font-semibold text-gray-900">Subscribed Users</h4>
                    <span class="text-sm text-gray-500">{{ $subscribedUsers->count() }} users subscribed</span>
                </div>

                @if($subscribedUsers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notification Types
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Regions
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email Verified
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subscribedUsers as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-blue-700">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->is_superuser)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    Superuser
                                                </span>
                                            @elseif($user->is_admin)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    User
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                @if($user->notificationSettings && $user->notificationSettings->notify_development_hearings)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Development
                                                    </span>
                                                @endif
                                                @if($user->notificationSettings && $user->notificationSettings->notify_policy_hearings)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Policy
                                                    </span>
                                                @endif
                                                @if($user->notificationSettings && $user->notificationSettings->notify_day_of_hearing)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                        Day-of Reminders
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($user->regions->count() > 0)
                                                <div class="flex flex-col">
                                                    @foreach($user->regions->take(2) as $region)
                                                        <span class="text-sm">{{ $region->name }}</span>
                                                    @endforeach
                                                    @if($user->regions->count() > 2)
                                                        <span class="text-xs text-gray-500">+{{ $user->regions->count() - 2 }} more</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-500">No regions</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->email_verified_at)
                                                <span class="text-green-600 text-sm">✓ Verified</span>
                                            @else
                                                <span class="text-red-600 text-sm">✗ Unverified</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-icon name="user" class="mx-auto h-12 w-12 text-gray-400" />
                        <p class="text-gray-500 text-sm">No users are currently subscribed to notifications for this region/hearing type.</p>
                    </div>
                @endif
            </div>

            <!-- Email Notifications Table -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-md font-semibold text-gray-900">Email Notifications Sent</h4>
                    <span class="text-sm text-gray-500">{{ $emailNotifications->count() }} total notifications</span>
                </div>

                @if($emailNotifications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Recipient
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sent At
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Opted In
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($emailNotifications as $notification)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($notification->email_address, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $notification->user->name ?? 'Unknown User' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $notification->email_address }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $notification->notification_type === 'hearing_created' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                                {{ $notification->notification_type === 'hearing_created' ? 'New Hearing' : 'Day-of Reminder' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($notification->status === 'sent')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ✓ Sent
                                                </span>
                                            @elseif($notification->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    ✗ Failed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    ⏳ Queued
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($notification->sent_at)
                                                <div>{{ $notification->sent_at->format('M j, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $notification->sent_at->format('g:i A') }}</div>
                                            @elseif($notification->status === 'queued')
                                                <div class="text-gray-500">Pending</div>
                                                <div class="text-xs text-gray-400">Queued {{ $notification->created_at->diffForHumans() }}</div>
                                            @else
                                                <span class="text-gray-500">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($notification->opted_in)
                                                <span class="text-green-600 text-sm">✓ Yes</span>
                                            @else
                                                <span class="text-red-600 text-sm">✗ No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-icon name="mail" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications sent</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            No email notifications have been sent for this hearing yet.
                        </p>
                    </div>
                @endif
            </div>
            </div>
        </div>
        @endif
    </div>

    <script src="{{ asset('js/calendar-button.js') }}"></script>
</x-app-layout>
