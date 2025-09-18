<x-app-layout>
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
                    
                    @if($hearing->isDevelopment())
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Property Information</h3>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Street Address</p>
                            <p class="text-gray-900">{{ $hearing->street_address }}</p>
                        </div>
                        @if($hearing->postal_code)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Postal Code</p>
                                <p class="text-gray-900">{{ $hearing->postal_code }}</p>
                            </div>
                        @endif
                        @if($hearing->rental !== null)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Property Type</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->rental ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $hearing->rental ? 'Rental Property' : 'Condo/Owned Property' }}
                                </span>
                            </div>
                        @endif
                        @if($hearing->units)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Number of Units</p>
                                <p class="text-gray-900">{{ $hearing->units }}</p>
                            </div>
                        @endif
                    @else
                    @endif
                </div>
            </div>

            <!-- Clear float before full-width sections -->
            <div class="clear-both"></div>

            <!-- Full-width sections -->
            <div class="space-y-6">
                <!-- How to Help Section -->
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-6 border border-orange-200">
                    <h3 class="text-xl font-semibold text-orange-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                        </svg>
                        How to Help
                    </h3>
                    
                    <!-- Comments Email - Prominent -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4 border-l-4 border-orange-500">
                            <h4 class="font-semibold text-orange-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Submit Written Comments
                            </h4>
                            <p class="text-gray-600 mb-2 text-sm">Send your support via email:</p>
                            <a href="mailto:{{ $hearing->comments_email }}" class="inline-flex items-center bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-3 rounded-lg transition duration-200 text-sm">
                                {{ $hearing->comments_email }}
                            </a>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                            <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                Share this hearing
                            </h4>
                            <p class="text-gray-600 mb-2 text-sm">Help spread the word:</p>
                            <div class="flex gap-2 justify-center">
                                <!-- BlueSky -->
                                <a href="https://bsky.app/intent/compose?text={{ urlencode($hearing->title . ' - ' . request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-full transition duration-200"
                                   title="Share on BlueSky">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.5c-2.4 0-4.8.8-6.7 2.3C3.6 6.1 2.5 8.2 2.5 10.5c0 1.8.6 3.5 1.7 4.9.6.8 1.4 1.4 2.3 1.8.4.2.8.3 1.2.3.2 0 .4 0 .6-.1.8-.3 1.4-1 1.6-1.9.1-.4.1-.8 0-1.2-.2-.8-.7-1.4-1.4-1.7-.3-.1-.6-.2-.9-.2-.2 0-.4.1-.5.2-.1.1-.2.2-.2.4 0 .1.1.2.2.3.4.3.7.7.8 1.2.1.3.1.6 0 .9-.1.3-.3.5-.5.6-.1 0-.2.1-.3.1-.2 0-.4-.1-.6-.2-.5-.3-1-.7-1.3-1.2-.7-.9-1.1-2-1.1-3.1 0-1.7.8-3.3 2.1-4.4C7.8 3.6 9.9 2.5 12 2.5s4.2 1.1 5.8 2.8c1.3 1.1 2.1 2.7 2.1 4.4 0 1.1-.4 2.2-1.1 3.1-.3.5-.8.9-1.3 1.2-.2.1-.4.2-.6.2-.1 0-.2 0-.3-.1-.2-.1-.4-.3-.5-.6-.1-.3-.1-.6 0-.9.1-.5.4-.9.8-1.2.1-.1.2-.2.2-.3 0-.2-.1-.3-.2-.4-.1-.1-.3-.2-.5-.2-.3 0-.6.1-.9.2-.7.3-1.2.9-1.4 1.7-.1.4-.1.8 0 1.2.2.9.8 1.6 1.6 1.9.2.1.4.1.6.1.4 0 .8-.1 1.2-.3.9-.4 1.7-1 2.3-1.8 1.1-1.4 1.7-3.1 1.7-4.9 0-2.3-1.1-4.4-2.8-5.7C16.8 3.3 14.4 2.5 12 2.5z"/>
                                    </svg>
                                </a>
                                
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-full transition duration-200"
                                   title="Share on Facebook">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                
                                <!-- X (Twitter) -->
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($hearing->title) }}&url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-black hover:bg-gray-800 text-white rounded-full transition duration-200"
                                   title="Share on X">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                                
                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" 
                                   class="w-8 h-8 flex items-center justify-center bg-blue-700 hover:bg-blue-800 text-white rounded-full transition duration-200"
                                   title="Share on LinkedIn">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participation Instructions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Join Virtually
                            </h4>
                            <div class="text-gray-700 text-sm whitespace-pre-wrap">{!! $hearing->remote_instructions_linked !!}</div>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4">
                            <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
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
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
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
