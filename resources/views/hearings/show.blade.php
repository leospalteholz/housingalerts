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
                                {{ \Carbon\Carbon::parse($hearing->start_date)->format('F j, Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Start Time</p>
                        <p class="text-gray-900">
                                {{ \Carbon\Carbon::parse($hearing->start_time)->format('g:i A') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">End Time</p>
                        <p class="text-gray-900">
                                {{ \Carbon\Carbon::parse($hearing->end_time)->format('g:i A') }}
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

        <!-- Notification Statistics (Admin/Superuser Only) -->
        @if(auth()->user()->is_admin || auth()->user()->is_superuser)
            <div class="bg-white rounded shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Notification Information</h3>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h2V4a2 2 0 012-2h4a2 2 0 012 2v4z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">
                            Subscribed Users
                        </p>
                        <p class="text-2xl font-bold text-blue-900">
                            {{ $subscribedUsersCount }}
                        </p>
                    </div>
                </div>
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
