<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hearing Vote Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Action Bar -->
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('hearing-votes.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Back to Votes
                </a>
                <a href="{{ route('hearing-votes.edit', $hearingVote) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-icon name="edit" class="w-4 h-4 mr-2" />
                    Edit Vote
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Hearing Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-purple-800 mb-4">Hearing Information</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Address</span>
                                    <p class="text-base text-gray-900">{{ $hearingVote->hearing->street_address }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Region</span>
                                        <p class="text-base text-gray-900">{{ $hearingVote->hearing->region->name }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Hearing Date</span>
                                        <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($hearingVote->hearing->start_datetime)->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>

                                @if ($hearingVote->hearing->applicant)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Applicant</span>
                                        <p class="text-base text-gray-900">{{ $hearingVote->hearing->applicant }}</p>
                                    </div>
                                @endif

                                @if ($hearingVote->hearing->description)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Description</span>
                                        <p class="text-base text-gray-900">{{ $hearingVote->hearing->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Vote Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-indigo-800 mb-4">Vote Details</h3>
                            
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Vote Date</span>
                                        <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($hearingVote->vote_date)->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Result</span>
                                        <p class="text-base">
                                            @if ($hearingVote->passed === null)
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Pending
                                                </span>
                                            @elseif ($hearingVote->passed)
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Passed
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Failed
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if ($hearingVote->notes)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Notes</span>
                                        <p class="text-base text-gray-900">{{ $hearingVote->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Individual Votes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Individual Councillor Votes</h3>
                            
                            @if ($hearingVote->councillorVotes->isEmpty())
                                <p class="text-gray-500 italic">No individual votes recorded.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Councillor</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vote</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($hearingVote->councillorVotes->sortBy('councillor.name') as $councillorVote)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <a href="{{ route('councillors.show', $councillorVote->councillor) }}" 
                                                           class="text-blue-600 hover:text-blue-900 hover:underline">
                                                            {{ $councillorVote->councillor->name }}
                                                        </a>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $councillorVote->voteColor }}-100 text-{{ $councillorVote->voteColor }}-800">
                                                            {{ $councillorVote->voteLabel }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Vote Tally -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Vote Tally</h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-icon name="check" class="w-5 h-5 text-green-600 mr-2" />
                                        <span class="text-sm font-medium text-green-900">For</span>
                                    </div>
                                    <span class="text-2xl font-bold text-green-700">{{ $tallies['for'] }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-icon name="x" class="w-5 h-5 text-red-600 mr-2" />
                                        <span class="text-sm font-medium text-red-900">Against</span>
                                    </div>
                                    <span class="text-2xl font-bold text-red-700">{{ $tallies['against'] }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-icon name="minus" class="w-5 h-5 text-yellow-600 mr-2" />
                                        <span class="text-sm font-medium text-yellow-900">Abstain</span>
                                    </div>
                                    <span class="text-2xl font-bold text-yellow-700">{{ $tallies['abstain'] }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-icon name="user-x" class="w-5 h-5 text-gray-600 mr-2" />
                                        <span class="text-sm font-medium text-gray-900">Absent</span>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-700">{{ $tallies['absent'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    @if (auth()->user()->is_superuser)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Organization</h3>
                                <p class="text-base text-gray-900">{{ $hearingVote->hearing->region->organization->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
