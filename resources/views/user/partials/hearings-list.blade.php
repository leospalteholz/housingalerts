@if($upcomingHearings->count() > 0)
    <div class="divide-y divide-gray-200">
        @foreach($upcomingHearings->take(5) as $hearing)
            <div class="px-6 py-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            {{ $hearing->display_title }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-2">
                            @if($hearing->start_date)
                                <div class="flex items-center">
                                    <x-icon name="calendar" class="w-4 h-4 mr-1" />
                                    {{ \Carbon\Carbon::parse($hearing->start_date)->format('M j, Y \a\t g:i A') }}
                                </div>
                            @endif
                            @if($hearing->region)
                                <div class="flex items-center">
                                    <x-icon name="location" class="w-4 h-4 mr-1" />
                                    {{ $hearing->region->name }}
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->isDevelopment() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $hearing->isDevelopment() ? 'Development' : 'Policy' }}
                            </span>
                            @if($hearing->isDevelopment() && $hearing->units)
                                <span class="text-sm text-gray-600">{{ $hearing->units }} units</span>
                            @endif
                            @if($hearing->isDevelopment() && $hearing->rental !== null)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $hearing->rental ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $hearing->rental ? 'Rental' : 'Condo' }}
                                </span>
                            @endif
                            @if($hearing->isDevelopment() && $hearing->below_market_units > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                    {{ $hearing->below_market_units }} BMR
                                </span>
                            @endif
                            @if($hearing->isDevelopment() && $hearing->replaced_units > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                                    {{ $hearing->replaced_units }} replaced
                                </span>
                            @endif
                            @if($hearing->isDevelopment() && $hearing->subject_to_vote)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                    Vote
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4 flex gap-2">
                        <a href="{{ orgRoute('hearings.show', ['hearing' => $hearing]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                            View Details
                        </a>
                        
                        <!-- Add to Calendar Dropdown -->
                        <x-calendar-button :hearing="$hearing" compact="true" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if($upcomingHearings->count() > 5)
        <div class="px-6 py-4 bg-gray-50 text-center">
            <a href="{{ orgRoute('hearings.index') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium">
                View all {{ $upcomingHearings->count() }} upcoming hearings →
            </a>
        </div>
    @endif
@else
    <div class="px-6 py-12 text-center">
        <x-icon name="calendar" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming hearings</h3>
        @if($upcomingHearings->count() === 0)
            <p class="text-gray-600 mb-4">Subscribe to some regions above to see upcoming hearings here.</p>
        @else
            <p class="text-gray-600 mb-4">There are no upcoming hearings in your monitored regions.</p>
        @endif
    </div>
@endif
