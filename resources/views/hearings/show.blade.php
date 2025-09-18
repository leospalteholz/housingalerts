<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hearing Details') }}
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hearing Details (Dynamic based on type) -->
                <div class="space-y-4">
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
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Hearing Details</h3>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Hearing Title</p>
                            <p class="text-gray-900 text-lg">{{ $hearing->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Hearing Type</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Policy Hearing
                            </span>
                        </div>
                    @endif
                    
                    <!-- Region (shown for both types) -->
                    <div>
                        <p class="text-sm font-medium text-gray-500">Region</p>
                        @if($hearing->region)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $hearing->region->name }}
                            </span>
                        @else
                            <span class="text-gray-500">No region assigned</span>
                        @endif
                    </div>
                </div>

                <!-- Hearing Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Hearing Information</h3>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Start Date</p>
                        <p class="text-gray-900">
                                {{ $hearing->start_datetime ? $hearing->start_datetime->format('F j, Y') : 'Not set' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Start Time</p>
                        <p class="text-gray-900">
                                {{ $hearing->start_datetime ? $hearing->start_datetime->format('g:i A') : 'Not set' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">End Time</p>
                        <p class="text-gray-900">
                                {{ $hearing->end_datetime ? $hearing->end_datetime->format('g:i A') : 'Not set' }}
                        </p>
                    </div>
                    
                    <!-- Add to Calendar Button -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Add to Calendar</p>
                        <x-calendar-button :hearing="$hearing" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Comments Email</p>
                        <p class="text-gray-900">
                            <a href="mailto:{{ $hearing->comments_email }}" class="text-blue-600 hover:underline">
                                {{ $hearing->comments_email }}
                            </a>
                        </p>
                    </div>
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

            <!-- Full-width sections -->
            <div class="mt-6 space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Hearing Description</p>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $hearing->description }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Remote Joining Instructions</p>
                        <div class="bg-blue-50 rounded p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $hearing->remote_instructions }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">In-Person Instructions</p>
                        <div class="bg-green-50 rounded p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $hearing->inperson_instructions }}</p>
                        </div>
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
        @endif
    </div>

    <script src="{{ asset('js/calendar-button.js') }}"></script>
</x-app-layout>
