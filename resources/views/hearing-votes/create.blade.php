<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Hearing Vote') }}
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
                                <span class="text-gray-900">{{ $hearing->street_address }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Region:</span>
                                <span class="text-gray-900">{{ $hearing->region->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Hearing Date:</span>
                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($hearing->start_datetime)->format('M d, Y g:i A') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Applicant:</span>
                                <span class="text-gray-900">{{ $hearing->applicant ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('hearing-votes.store') }}">
                        @csrf
                        <input type="hidden" name="hearing_id" value="{{ $hearing->id }}">

                        <!-- Vote Date -->
                        <div class="mb-4">
                            <label for="vote_date" class="block text-sm font-medium text-gray-700">
                                Vote Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="vote_date" 
                                   id="vote_date" 
                                   value="{{ old('vote_date', \Carbon\Carbon::parse($hearing->start_datetime)->format('Y-m-d')) }}"
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
                                Vote Result <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center mr-6">
                                    <input type="radio" 
                                           name="passed" 
                                           value="1" 
                                           {{ old('passed') == '1' ? 'checked' : '' }}
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
                                           {{ old('passed') == '0' ? 'checked' : '' }}
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
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
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
                                    <p class="text-sm mt-1">There are no councillors in {{ $hearing->region->name }} who were serving at the time of this hearing.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Votes For -->
                                    <div>
                                        <label class="block text-sm font-medium text-green-700 mb-2">
                                            <x-icon name="check" class="w-4 h-4 inline" /> Voted For
                                        </label>
                                        <div class="space-y-2 border border-green-200 rounded-lg p-4 bg-green-50">
                                            @foreach ($councillors as $councillor)
                                                <label class="flex items-center">
                                                    <input type="checkbox" 
                                                           name="votes_for[]" 
                                                           value="{{ $councillor->id }}"
                                                           {{ in_array($councillor->id, old('votes_for', [])) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $councillor->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('votes_for')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Votes Against -->
                                    <div>
                                        <label class="block text-sm font-medium text-red-700 mb-2">
                                            <x-icon name="x" class="w-4 h-4 inline" /> Voted Against
                                        </label>
                                        <div class="space-y-2 border border-red-200 rounded-lg p-4 bg-red-50">
                                            @foreach ($councillors as $councillor)
                                                <label class="flex items-center">
                                                    <input type="checkbox" 
                                                           name="votes_against[]" 
                                                           value="{{ $councillor->id }}"
                                                           {{ in_array($councillor->id, old('votes_against', [])) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $councillor->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('votes_against')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Select which councillors voted for or against this hearing. Councillors shown were serving in {{ $hearing->region->name }} at the time of this hearing.</p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('hearing-votes.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <x-icon name="check" class="w-4 h-4 mr-2" />
                                Create Vote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
