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
        <a href="{{ route('hearings.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to Hearings</a>
    </div>
</x-app-layout>
