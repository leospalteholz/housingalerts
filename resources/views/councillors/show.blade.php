<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $councillor->name }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <!-- Councillor Details -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Councillor Information</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Name:</span>
                                    <p class="text-gray-900">{{ $councillor->name }}</p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Region:</span>
                                    <p class="text-gray-900">{{ $councillor->region->name }}</p>
                                </div>
                                
                                @if(auth()->user()->is_superuser)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Organization:</span>
                                    <p class="text-gray-900">{{ $councillor->region->organization->name }}</p>
                                </div>
                                @endif
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Term:</span>
                                    <p class="text-gray-900">
                                        {{ $councillor->elected_start->format('F j, Y') }} - 
                                        {{ $councillor->elected_end ? $councillor->elected_end->format('F j, Y') : 'Present' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Status:</span>
                                    <p>
                                        @if($councillor->isCurrentlyServing())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Currently Serving
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Past Councillor
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Voting Statistics</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Total Votes:</span>
                                    <p class="text-2xl font-bold text-gray-900">{{ $votingStats['total'] }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <span class="text-sm font-medium text-green-700">For</span>
                                        <p class="text-xl font-bold text-green-900">{{ $votingStats['for'] }}</p>
                                    </div>
                                    
                                    <div class="bg-red-50 rounded-lg p-3">
                                        <span class="text-sm font-medium text-red-700">Against</span>
                                        <p class="text-xl font-bold text-red-900">{{ $votingStats['against'] }}</p>
                                    </div>
                                    
                                    <div class="bg-yellow-50 rounded-lg p-3">
                                        <span class="text-sm font-medium text-yellow-700">Abstain</span>
                                        <p class="text-xl font-bold text-yellow-900">{{ $votingStats['abstain'] }}</p>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <span class="text-sm font-medium text-gray-700">Absent</span>
                                        <p class="text-xl font-bold text-gray-900">{{ $votingStats['absent'] }}</p>
                                    </div>
                                </div>
                                
                                @if($votingStats['total'] > 0)
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-gray-500">Support Rate:</span>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ round(($votingStats['for'] / max($votingStats['for'] + $votingStats['against'], 1)) * 100) }}%
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex space-x-2">
                        <a href="{{ orgRoute('councillors.edit', ['councillor' => $councillor]) }}" 
                           class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-2 px-4 rounded">
                            Edit Councillor
                        </a>
                        <a href="{{ orgRoute('councillors.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
                
                <!-- Voting History -->
                @if($councillor->councillorVotes->count() > 0)
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Voting History</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hearing</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vote Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vote</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($councillor->councillorVotes as $vote)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <a href="{{ orgRoute('hearings.show', ['hearing' => $vote->hearingVote->hearing]) }}" 
                                               class="text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ $vote->hearingVote->hearing->display_title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $vote->hearingVote->vote_date ? $vote->hearingVote->vote_date->format('M j, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $vote->vote_color === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $vote->vote_color === 'red' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $vote->vote_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $vote->vote_color === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ $vote->vote_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($vote->hearingVote->passed !== null)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $vote->hearingVote->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $vote->hearingVote->vote_result }}
                                                </span>
                                            @else
                                                <span class="text-gray-500 text-sm">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="p-6 text-center text-gray-500">
                    <p>No voting history available for this councillor.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
