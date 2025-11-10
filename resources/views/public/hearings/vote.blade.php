<x-guest-layout>
    <div class="max-w-4xl mx-auto py-12 px-6">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Add vote results for {{ $hearing->display_title }}</h1>
            <p class="mt-2 text-gray-600">Thanks for sharing this hearing. If council has already voted, record the results below so we can include them.</p>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-10">
            @if ($errors->any())
                <div class="mb-6 rounded border-l-4 border-red-500 bg-red-50 p-4 text-red-800">
                    <p class="font-semibold">Please fix the following issues:</p>
                    <ul class="mt-2 list-disc list-inside space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50 border border-blue-100 rounded-lg p-6 text-sm text-blue-900">
                <div>
                    <p class="font-semibold text-blue-800">Hearing details</p>
                    <p class="mt-1">Region: {{ $hearing->region->name }}</p>
                    <p>Scheduled: {{ $hearing->start_datetime?->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-blue-800">Need a change?</p>
                    <p class="mt-1">If these details look wrong, please go back and submit a new hearing with corrections.</p>
                </div>
            </div>

            <form method="POST" action="{{ request()->fullUrl() }}" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vote_date" class="block text-sm font-medium text-gray-700">Vote date<span class="text-red-500"> *</span></label>
                        <input type="date" id="vote_date" name="vote_date" value="{{ old('vote_date', $hearing->start_datetime?->format('Y-m-d')) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Vote result<span class="text-red-500"> *</span></span>
                        <div class="mt-3 space-y-3">
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="passed" value="1" {{ old('passed') === '1' ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700">Approved / passed</span>
                            </label>
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="passed" value="0" {{ old('passed') === '0' ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-700">Rejected / failed</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('notes') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Optional context about the vote (public sentiment, conditions, etc.).</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Councillor votes</h2>
                    @if ($councillors->isEmpty())
                        <div class="rounded border border-yellow-300 bg-yellow-50 p-4 text-yellow-900 text-sm">
                            We don't have councillors on file for this region. Feel free to skip this section.
                        </div>
                    @else
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">Councillor</th>
                                        <th class="px-4 py-3 text-center font-semibold text-green-700">For</th>
                                        <th class="px-4 py-3 text-center font-semibold text-red-700">Against</th>
                                        <th class="px-4 py-3 text-center font-semibold text-amber-600">Abstain</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Absent</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    @foreach ($councillors as $councillor)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $councillor->name }}</td>
                                            @foreach (['for' => 'text-green-600', 'against' => 'text-red-600', 'abstain' => 'text-amber-600', 'absent' => 'text-gray-600'] as $value => $colour)
                                                <td class="px-4 py-3 text-center">
                                                    <input type="radio" name="vote_{{ $councillor->id }}" value="{{ $value }}" {{ old('vote_'.$councillor->id) === $value ? 'checked' : '' }} class="h-4 w-4 {{ $colour }} focus:ring-purple-500">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">If you're unsure about any councillor, you can leave their row blank.</p>
                    @endif
                </div>

                <div class="text-right">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow">
                        Save vote details
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
