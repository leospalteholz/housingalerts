<x-guest-layout>
    <div class="max-w-5xl mx-auto py-12 px-6">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Submit a hearing for {{ $organization->name }}</h1>
            <p class="mt-3 text-gray-600 text-lg">Share the details of an upcoming public hearing so our team can review and publish it.</p>
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

            <form method="POST" action="{{ route('public.hearings.submit.store', ['organization' => $organization->slug]) }}" class="space-y-8">
                @csrf

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Hearing type</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="development" class="sr-only" {{ old('type', 'development') === 'development' ? 'checked' : '' }} onchange="toggleHearingType()">
                            <div id="development-option" class="h-full border-2 rounded-xl p-5 text-center transition hover:shadow focus-within:ring-2 focus-within:ring-blue-500">
                                <div class="text-lg font-medium text-gray-900">Development</div>
                                <p class="text-sm text-gray-600 mt-1">Use for project-specific housing approvals.</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="policy" class="sr-only" {{ old('type') === 'policy' ? 'checked' : '' }} onchange="toggleHearingType()">
                            <div id="policy-option" class="h-full border-2 rounded-xl p-5 text-center transition hover:shadow focus-within:ring-2 focus-within:ring-blue-500">
                                <div class="text-lg font-medium text-gray-900">Policy</div>
                                <p class="text-sm text-gray-600 mt-1">Use for official plan updates, bylaw changes, or non-project hearings.</p>
                            </div>
                        </label>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Location &amp; basics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="region_id" class="block text-sm font-medium text-gray-700">Region<span class="text-red-500"> *</span></label>
                            <select id="region_id" name="region_id" required class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select a region…</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}" {{ (int) old('region_id') === $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="title-field" class="hidden">
                            <label for="title" class="block text-sm font-medium text-gray-700">Hearing title<span class="text-red-500"> *</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., Official Community Plan Update">
                        </div>
                    </div>

                    <div id="development-fields" class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="street_address" class="block text-sm font-medium text-gray-700">Street address<span class="text-red-500"> *</span></label>
                                <input type="text" id="street_address" name="street_address" value="{{ old('street_address') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="123 Main St">
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal code<span class="text-red-500"> *</span></label>
                                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="A1A 1A1">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="rental" class="block text-sm font-medium text-gray-700">Property type<span class="text-red-500"> *</span></label>
                                <select id="rental" name="rental" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select…</option>
                                    <option value="1" {{ old('rental') === '1' ? 'selected' : '' }}>Rental</option>
                                    <option value="0" {{ old('rental') === '0' ? 'selected' : '' }}>Ownership</option>
                                </select>
                            </div>
                            <div>
                                <label for="units" class="block text-sm font-medium text-gray-700">Total homes<span class="text-red-500"> *</span></label>
                                <input type="number" id="units" name="units" value="{{ old('units') }}" min="1" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="below_market_units" class="block text-sm font-medium text-gray-700">Below-market homes<span class="text-red-500"> *</span></label>
                                <input type="number" id="below_market_units" name="below_market_units" value="{{ old('below_market_units', 0) }}" min="0" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-6">
                            <label for="replaced_units" class="block text-sm font-medium text-gray-700">Replaced homes</label>
                            <input type="number" id="replaced_units" name="replaced_units" value="{{ old('replaced_units') }}" min="0" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Schedule</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Hearing date<span class="text-red-500"> *</span></label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start time<span class="text-red-500"> *</span></label>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End time<span class="text-red-500"> *</span></label>
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="subject_to_vote" class="block text-sm font-medium text-gray-700">Will council vote on this hearing?<span class="text-red-500"> *</span></label>
                        <select id="subject_to_vote" name="subject_to_vote" required class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="0" {{ old('subject_to_vote', '0') === '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('subject_to_vote') === '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-500">If you select "Yes" we'll ask for vote results on the next step.</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Details</h2>
                    <div class="space-y-6">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Describe the proposal<span class="text-red-500"> *</span></label>
                            <textarea id="description" name="description" rows="5" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label for="more_info_url" class="block text-sm font-medium text-gray-700">Link to more information</label>
                            <input type="url" id="more_info_url" name="more_info_url" value="{{ old('more_info_url') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="comments_email" class="block text-sm font-medium text-gray-700">Public comments email<span class="text-red-500"> *</span></label>
                                <input type="email" id="comments_email" name="comments_email" value="{{ old('comments_email') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="council@example.ca">
                            </div>
                            <div>
                                <label for="remote_instructions" class="block text-sm font-medium text-gray-700">Remote participation details<span class="text-red-500"> *</span></label>
                                <textarea id="remote_instructions" name="remote_instructions" rows="3" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('remote_instructions') }}</textarea>
                            </div>
                        </div>
                        <div>
                            <label for="inperson_instructions" class="block text-sm font-medium text-gray-700">In-person participation details<span class="text-red-500"> *</span></label>
                            <textarea id="inperson_instructions" name="inperson_instructions" rows="3" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('inperson_instructions') }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="text-right">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow">
                        Save hearing
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleHearingType() {
            const type = document.querySelector('input[name="type"]:checked')?.value;
            const titleField = document.getElementById('title-field');
            const developmentFields = document.getElementById('development-fields');

            if (type === 'policy') {
                titleField.classList.remove('hidden');
                document.getElementById('title').required = true;

                developmentFields.classList.add('hidden');
                document.getElementById('street_address').required = false;
                document.getElementById('postal_code').required = false;
                document.getElementById('rental').required = false;
                document.getElementById('units').required = false;
                document.getElementById('below_market_units').required = false;
            } else {
                titleField.classList.add('hidden');
                document.getElementById('title').required = false;

                developmentFields.classList.remove('hidden');
                document.getElementById('street_address').required = true;
                document.getElementById('postal_code').required = true;
                document.getElementById('rental').required = true;
                document.getElementById('units').required = true;
                document.getElementById('below_market_units').required = true;
            }
        }

        document.addEventListener('DOMContentLoaded', toggleHearingType);
    </script>
</x-guest-layout>
