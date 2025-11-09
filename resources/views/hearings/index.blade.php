<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hearings') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <!-- Upcoming Hearings -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-green-800">Upcoming Hearings</h3>
                        <p class="text-sm text-green-600">{{ $upcomingHearings->count() }} hearings scheduled</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('hearings.export') }}"
                           class="inline-flex items-center px-4 py-2 border border-green-600 rounded-md font-semibold text-xs text-green-700 uppercase tracking-widest bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Export CSV
                        </a>
                        <a href="{{ route('hearings.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add New Hearing
                        </a>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title/Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($upcomingHearings as $hearing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $hearing->display_title }}
                                    </div>
                                    @unless($hearing->approved)
                                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            Pending Approval
                                        </span>
                                    @endunless
                                    @if($hearing->isDevelopment() && $hearing->postal_code)
                                        <div class="text-sm text-gray-500">{{ $hearing->postal_code }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->isDevelopment() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $hearing->isDevelopment() ? 'Development' : 'Policy' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hearing->isDevelopment())
                                        <div class="text-sm text-gray-900">
                                            @if($hearing->rental !== null)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $hearing->rental ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $hearing->rental ? 'Rental' : 'Condo' }}
                                                </span>
                                            @endif
                                            @if($hearing->units)
                                                <span class="ml-2 text-sm text-gray-600">{{ $hearing->units }} units</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($hearing->below_market_units > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700 mr-1">
                                                    {{ $hearing->below_market_units }} BMR
                                                </span>
                                            @endif
                                            @if($hearing->replaced_units > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700 mr-1">
                                                    {{ $hearing->replaced_units }} replaced
                                                </span>
                                            @endif
                                            @if($hearing->subject_to_vote)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-700">
                                                    Vote
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-600">Policy Hearing</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hearing->region)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $hearing->region->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">No region</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hearing->start_date)
                                        {{ \Carbon\Carbon::parse($hearing->start_date)->format('M j, Y') }}
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="{{ route('hearings.show', $hearing) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                    <a href="{{ route('hearings.edit', $hearing) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-gray-500 text-center">No upcoming hearings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

                <!-- Past Hearings -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Past Hearings</h3>
                <p class="text-sm text-gray-600">{{ $pastHearings->count() }} hearings completed</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title/Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pastHearings as $hearing)
                            <tr class="opacity-75">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $hearing->display_title }}
                                    </div>
                                    @unless($hearing->approved)
                                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            Pending Approval
                                        </span>
                                    @endunless
                                    @if($hearing->isDevelopment() && $hearing->postal_code)
                                        <div class="text-sm text-gray-500">{{ $hearing->postal_code }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->isDevelopment() ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                        {{ $hearing->isDevelopment() ? 'Development' : 'Policy' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hearing->isDevelopment())
                                        <div class="text-sm text-gray-900">
                                            @if($hearing->rental !== null)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $hearing->rental ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600' }}">
                                                    {{ $hearing->rental ? 'Rental' : 'Condo' }}
                                                </span>
                                            @endif
                                            @if($hearing->units)
                                                <span class="ml-2 text-sm text-gray-600">{{ $hearing->units }} units</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if($hearing->below_market_units > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-600 mr-1">
                                                    {{ $hearing->below_market_units }} BMR
                                                </span>
                                            @endif
                                            @if($hearing->replaced_units > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-600 mr-1">
                                                    {{ $hearing->replaced_units }} replaced
                                                </span>
                                            @endif
                                            @if($hearing->subject_to_vote)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-600">
                                                    Vote
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-600">Policy Hearing</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hearing->region)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $hearing->region->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">No region</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($hearing->start_date)
                                        {{ \Carbon\Carbon::parse($hearing->start_date)->format('M j, Y') }}
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                    <a href="{{ route('hearings.show', $hearing) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                    <a href="{{ route('hearings.edit', $hearing) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-gray-500 text-center">No past hearings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
            </div>
        </div>
    </div>
</x-app-layout>
