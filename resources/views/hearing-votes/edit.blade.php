<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hearing Vote') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Hearing Details -->
                    <div class="mb-6 p-4 bg-purple-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-900 mb-2">Hearing Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Address:</span>
                                <span class="text-gray-900">{{ $hearingVote->hearing->street_address }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Region:</span>
                                <span class="text-gray-900">{{ $hearingVote->hearing->region->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Hearing Date:</span>
                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($hearingVote->hearing->start_datetime)->format('M d, Y g:i A') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Applicant:</span>
                                <span class="text-gray-900">{{ $hearingVote->hearing->applicant ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ orgRoute('hearing-votes.update', ['hearing_vote' => $hearingVote]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Vote Date -->
                        <div class="mb-4">
                            <label for="vote_date" class="block text-sm font-medium text-gray-700">
                                Vote Date
                            </label>
                            <input type="date" 
                                   name="vote_date" 
                                   id="vote_date" 
                                   value="{{ old('vote_date', \Carbon\Carbon::parse($hearingVote->vote_date)->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('vote_date') border-red-500 @enderror" 
                                   required>
                            @error('vote_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">The date when the council voted on this hearing.</p>
                        </div>

                        <!-- Passed -->
                        <div class="mb-4">
                            <label for="passed" class="block text-sm font-medium text-gray-700 mb-2">
                                Vote Result
                            </label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center mr-6">
                                    <input type="radio" 
                                           name="passed" 
                                           value="1" 
                                           {{ old('passed', $hearingVote->passed) == '1' ? 'checked' : '' }}
                                           class="rounded-full border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" 
                                           required>
                                    <span class="ml-2 text-sm text-gray-700">
                                        <span class="font-semibold text-green-700">Passed</span> - The hearing was approved
                                    </span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="radio" 
                                           name="passed" 
                                           value="0" 
                                           {{ old('passed', $hearingVote->passed) == '0' ? 'checked' : '' }}
                                           class="rounded-full border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50" 
                                           required>
                                    <span class="ml-2 text-sm text-gray-700">
                                        <span class="font-semibold text-red-700">Failed</span> - The hearing was rejected
                                    </span>
                                </label>
                            </div>
                            @error('passed')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('notes') border-red-500 @enderror">{{ old('notes', $hearingVote->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Optional notes about the vote or meeting.</p>
                        </div>

                        <!-- Councillor Votes -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Councillor Votes</h4>
                            
                            @if ($councillors->isEmpty())
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
                                    <p class="font-medium">No councillors available</p>
                                    <p class="text-sm mt-1">There are no councillors in {{ $hearingVote->hearing->region->name }} who were serving at the time of this hearing.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Councillor</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-green-700 uppercase tracking-wider">
                                                    <x-icon name="check" class="w-4 h-4 inline" /> For
                                                </th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-red-700 uppercase tracking-wider">
                                                    <x-icon name="x" class="w-4 h-4 inline" /> Against
                                                </th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-yellow-700 uppercase tracking-wider">
                                                    <x-icon name="minus" class="w-4 h-4 inline" /> Abstain
                                                </th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                                    <x-icon name="user-x" class="w-4 h-4 inline" /> Absent
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($councillors as $councillor)
                                                @php
                                                    $existingVote = $hearingVote->councillorVotes->where('councillor_id', $councillor->id)->first();
                                                    $currentVote = old('vote_' . $councillor->id, $existingVote?->vote);
                                                @endphp
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $councillor->name }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <input type="radio" 
                                                               name="vote_{{ $councillor->id }}" 
                                                               value="for"
                                                               {{ $currentVote == 'for' ? 'checked' : '' }}
                                                               class="rounded-full border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <input type="radio" 
                                                               name="vote_{{ $councillor->id }}" 
                                                               value="against"
                                                               {{ $currentVote == 'against' ? 'checked' : '' }}
                                                               class="rounded-full border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <input type="radio" 
                                                               name="vote_{{ $councillor->id }}" 
                                                               value="abstain"
                                                               {{ $currentVote == 'abstain' ? 'checked' : '' }}
                                                               class="rounded-full border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <input type="radio" 
                                                               name="vote_{{ $councillor->id }}" 
                                                               value="absent"
                                                               {{ $currentVote == 'absent' ? 'checked' : '' }}
                                                               class="rounded-full border-gray-300 text-gray-600 shadow-sm focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Select how each councillor voted. You can leave blank if the vote is unknown.</p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <a href="{{ orgRoute('hearing-votes.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <x-icon name="check" class="w-4 h-4 mr-2" />
                                Update Vote
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form method="POST" action="{{ orgRoute('hearing-votes.destroy', ['hearing_vote' => $hearingVote]) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this vote? This action cannot be undone.');" 
                          class="mt-6 pt-6 border-t border-gray-200">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <x-icon name="trash" class="w-4 h-4 mr-2" />
                            Delete Vote
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
