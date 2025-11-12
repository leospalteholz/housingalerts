@props(['hearing', 'compact' => false])

<div class="relative inline-block text-left">
    @if($compact)
        <!-- Compact Version -->
        <button type="button" class="calendar-menu-button inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition" data-hearing-id="{{ $hearing->id }}">
            <x-icon name="calendar" class="w-4 h-4" />
            <x-icon name="chevron-down" class="ml-1 h-4 w-4" />
        </button>
    @else
        <!-- Full Version -->
        <button type="button" class="calendar-menu-button inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-hearing-id="{{ $hearing->id }}">
            <x-icon name="calendar" class="w-4 h-4 mr-2" />
            Add to Calendar
            <x-icon name="chevron-down" class="-mr-1 ml-2 h-5 w-5" />
        </button>
    @endif

    <!-- Dropdown Menu (same for both versions) -->
    <div class="calendar-dropdown origin-top-right absolute right-0 mt-2 {{ $compact ? 'w-48' : 'w-56' }} rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden z-10" role="menu" aria-orientation="vertical" tabindex="-1" data-hearing-id="{{ $hearing->id }}">
        <div class="py-1" role="none">
            <a href="{{ route('hearings.calendar', ['hearing' => $hearing, 'provider' => 'google']) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                <svg class="w-4 h-4 mr-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Google Calendar
            </a>
            <a href="{{ route('hearings.calendar', ['hearing' => $hearing, 'provider' => 'outlook']) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="#0078d4" viewBox="0 0 24 24">
                    <path d="M24,12A12,12,0,0,1,12,24H4a4,4,0,0,1-4-4V12A12,12,0,0,1,24,12ZM12.5,7.5V16h2V14h1.5a2.5,2.5,0,0,0,0-5Zm2,2v1h1.5a.5.5,0,0,0,0-1ZM8.5,7.5a3,3,0,1,0,3,3,3,3,0,0,0-3-3Zm0,4.5a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,8.5,12Z"/>
                </svg>
                Outlook Calendar
            </a>
            <a href="{{ route('hearings.calendar', ['hearing' => $hearing, 'provider' => 'yahoo']) }}" target="_blank" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                <svg class="w-4 h-4 mr-3" fill="#6001d2" viewBox="0 0 24 24">
                    <path d="M12 0C5.383 0 0 5.383 0 12s5.383 12 12 12 12-5.383 12-12S18.617 0 12 0zm5.5 18h-3l-2.5-6.5L9.5 18h-3L11 6h2l4.5 12z"/>
                </svg>
                Yahoo Calendar
            </a>
            <hr class="my-1">
            <a href="{{ route('hearings.ics', ['hearing' => $hearing]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                <x-icon name="download" class="w-4 h-4 mr-3" />
                Download ICS File
            </a>
        </div>
    </div>
</div>
