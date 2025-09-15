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
                            @if($hearing->start_date)
                                {{ \Carbon\Carbon::parse($hearing->start_date)->format('F j, Y') }}
                            @else
                                Not scheduled
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Start Time</p>
                        <p class="text-gray-900">{{ $hearing->start_time ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">End Time</p>
                        <p class="text-gray-900">{{ $hearing->end_time ?: 'Not specified' }}</p>
                    </div>
                    
                    <!-- Add to Calendar Button -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Add to Calendar</p>
                        <div class="relative inline-block text-left">
                            <div>
                                <button type="button" class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="calendar-menu-button" aria-expanded="true" aria-haspopup="true">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Add to Calendar
                                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="calendar-dropdown origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden z-10" role="menu" aria-orientation="vertical" aria-labelledby="calendar-menu-button" tabindex="-1">
                                <div class="py-1" role="none">
                                    <a href="{{ route('hearings.calendar', [$hearing, 'google']) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <svg class="w-4 h-4 mr-3" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                        </svg>
                                        Google Calendar
                                    </a>
                                    <a href="{{ route('hearings.calendar', [$hearing, 'outlook']) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <svg class="w-4 h-4 mr-3" fill="#0078d4" viewBox="0 0 24 24">
                                            <path d="M24,12A12,12,0,0,1,12,24H4a4,4,0,0,1-4-4V12A12,12,0,0,1,24,12ZM12.5,7.5V16h2V14h1.5a2.5,2.5,0,0,0,0-5Zm2,2v1h1.5a.5.5,0,0,0,0-1ZM8.5,7.5a3,3,0,1,0,3,3,3,3,0,0,0-3-3Zm0,4.5a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,8.5,12Z"/>
                                        </svg>
                                        Outlook Calendar
                                    </a>
                                    <a href="{{ route('hearings.calendar', [$hearing, 'yahoo']) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <svg class="w-4 h-4 mr-3" fill="#6001d2" viewBox="0 0 24 24">
                                            <path d="M12 0C5.383 0 0 5.383 0 12s5.383 12 12 12 12-5.383 12-12S18.617 0 12 0zm5.5 18h-3l-2.5-6.5L9.5 18h-3L11 6h2l4.5 12z"/>
                                        </svg>
                                        Yahoo Calendar
                                    </a>
                                    <hr class="my-1">
                                    <a href="{{ route('hearings.ics', $hearing) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download .ics file
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Comments Email</p>
                        <p class="text-gray-900">
                            <a href="mailto:{{ $hearing->comments_email }}" class="text-blue-600 hover:underline">
                                {{ $hearing->comments_email }}
                            </a>
                        </p>
                    </div>
                    @if($hearing->more_info_url)
                        <div>
                            <p class="text-sm font-medium text-gray-500">More Information</p>
                            <p class="text-gray-900">
                                <a href="{{ $hearing->more_info_url }}" class="text-blue-600 hover:underline" target="_blank">
                                    {{ $hearing->more_info_url }}
                                </a>
                            </p>
                        </div>
                    @endif
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarButton = document.getElementById('calendar-menu-button');
            const calendarDropdown = document.querySelector('.calendar-dropdown');
            
            if (calendarButton && calendarDropdown) {
                calendarButton.addEventListener('click', function() {
                    calendarDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!calendarButton.contains(event.target) && !calendarDropdown.contains(event.target)) {
                        calendarDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>
