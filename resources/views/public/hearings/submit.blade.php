<x-guest-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Submit a hearing for {{ $organization->name }}</h1>
                <p class="mt-4 text-gray-600 text-lg max-w-3xl">
                    Share the details of an upcoming public hearing. Submissions will be reviewed before they show on the website.
                </p>
            </div>
            <div class="hidden lg:block text-sm text-gray-500 max-w-xs border-l-2 border-gray-200 pl-4">
                <p class="font-semibold text-gray-700">Tips</p>
                <p class="mt-1">Have the hearing notice or agenda handy. Include remote and in-person participation details if available.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 lg:py-10 px-0 lg:px-10">
        <div class="bg-white shadow-xl rounded-none sm:rounded-2xl py-8 px-6 sm:p-10 lg:p-14">
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

            <form method="POST" action="{{ route('public.hearings.submit.store', ['organization' => $organization->slug]) }}" class="space-y-12">
                @csrf

                <section class="rounded-xl border border-gray-200 bg-slate-50/60 p-6 lg:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Hearing type</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="development" class="sr-only" {{ old('type', 'development') === 'development' ? 'checked' : '' }} onchange="toggleHearingType()">
                            <div id="development-option" class="h-full border-2 border-gray-200 bg-white rounded-xl p-6 text-center transition hover:shadow focus-within:ring-2 focus-within:ring-blue-500">
                                <div class="text-lg font-medium text-gray-900">Development</div>
                                <p class="text-sm text-gray-600 mt-1">Use for project-specific housing approvals.</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="policy" class="sr-only" {{ old('type') === 'policy' ? 'checked' : '' }} onchange="toggleHearingType()">
                            <div id="policy-option" class="h-full border-2 border-gray-200 bg-white rounded-xl p-6 text-center transition hover:shadow focus-within:ring-2 focus-within:ring-blue-500">
                                <div class="text-lg font-medium text-gray-900">Policy</div>
                                <p class="text-sm text-gray-600 mt-1">Use for official plan updates, bylaw changes, or non-project hearings.</p>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-slate-50/60 p-6 lg:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Location &amp; basics</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div id="street-address-wrapper">
                            <label for="street_address" class="block text-sm font-medium text-gray-700">Street address</label>
                            <input type="text" id="street_address" name="street_address" value="{{ old('street_address') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="123 Main St">
                        </div>
                        <div id="postal-code-wrapper">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal code</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="A1A 1A1">
                        </div>
                        <div>
                            <label for="region_id" class="block text-sm font-medium text-gray-700">Region</label>
                            <select id="region_id" name="region_id" required class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select a region…</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}" {{ (int) old('region_id') === $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="title-field" class="hidden mt-6">
                        <label for="title" class="block text-sm font-medium text-gray-700">Hearing title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., Official Community Plan Update">
                    </div>

                    <div id="development-fields" class="mt-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="rental" class="block text-sm font-medium text-gray-700">Property type</label>
                                <select id="rental" name="rental" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select…</option>
                                    <option value="1" {{ old('rental') === '1' ? 'selected' : '' }}>Rental</option>
                                    <option value="0" {{ old('rental') === '0' ? 'selected' : '' }}>Ownership</option>
                                </select>
                            </div>
                            <div>
                                <label for="units" class="block text-sm font-medium text-gray-700">Total homes</label>
                                <input type="number" id="units" name="units" value="{{ old('units') }}" min="1" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="below_market_units" class="block text-sm font-medium text-gray-700">Below-market homes</label>
                                <input type="number" id="below_market_units" name="below_market_units" value="{{ old('below_market_units', 0) }}" min="0" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="replaced_units" class="block text-sm font-medium text-gray-700">Replaced homes</label>
                                <input type="number" id="replaced_units" name="replaced_units" value="{{ old('replaced_units') }}" min="0" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-slate-50/60 p-6 lg:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Details</h2>
                    <p class="mt-2 text-xs text-gray-500">You can leave these fields blank if you are recording a hearing in the past</p>
                    <div class="space-y-6 lg:space-y-0 lg:grid lg:grid-cols-2 lg:gap-8">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Describe the proposal</label>
                            <textarea id="description" name="description" rows="5" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label for="more_info_url" class="block text-sm font-medium text-gray-700">Link to more information</label>
                                <input type="url" id="more_info_url" name="more_info_url" value="{{ old('more_info_url') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://">
                            </div>
                            <div>
                                <label for="comments_email" class="block text-sm font-medium text-gray-700">Public comments email</label>
                                <input type="email" id="comments_email" name="comments_email" value="{{ old('comments_email') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="council@example.ca">
                            </div>
                        </div>
                        <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="remote_instructions" class="block text-sm font-medium text-gray-700">Remote participation details</label>
                                <textarea id="remote_instructions" name="remote_instructions" rows="4" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('remote_instructions') }}</textarea>
                            </div>
                            <div>
                                <label for="inperson_instructions" class="block text-sm font-medium text-gray-700">In-person participation details</label>
                                <textarea id="inperson_instructions" name="inperson_instructions" rows="4" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('inperson_instructions') }}</textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-slate-50/60 p-6 lg:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Schedule</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Hearing date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start time</label>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End time</label>
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-6 lg:max-w-md">
                        <label for="subject_to_vote" class="block text-sm font-medium text-gray-700">Will council vote on this hearing?</label>
                        <select id="subject_to_vote" name="subject_to_vote" required class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="0" {{ old('subject_to_vote', '0') === '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('subject_to_vote') === '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-500">If the hearing has already happened you can record the vote below.</p>
                    </div>
                </section>

                <section id="vote-section" class="rounded-xl border border-gray-200 bg-slate-50/60 p-6 lg:p-8 hidden">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Vote results</h2>
                            <p class="mt-1 text-sm text-gray-600">Share what happened at council so we can surface it alongside the hearing.</p>
                        </div>
                        <div id="vote-message" class="hidden rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                            We’ll save the hearing now. Vote details can be added after the hearing date.
                        </div>
                    </div>

                    <div id="vote-fields" class="mt-8 space-y-8 hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="rounded-xl border border-gray-200 bg-white p-6">
                                <label for="vote_date" class="block text-sm font-medium text-gray-700">Vote date</label>
                                <input type="date" id="vote_date" name="vote_date" value="{{ old('vote_date') }}" class="mt-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div class="rounded-xl border border-gray-200 bg-white p-6">
                                <span class="block text-sm font-medium text-gray-700">Vote result</span>
                                <div class="mt-4 space-y-4 text-sm text-gray-700">
                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="passed" value="1" {{ old('passed') === '1' ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500">
                                        <span class="font-medium text-green-700">Approved / passed</span>
                                    </label>
                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="passed" value="0" {{ old('passed') === '0' ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500">
                                        <span class="font-medium text-red-700">Rejected / failed</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" name="notes" rows="4" class="mt-3 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('notes') }}</textarea>
                            <p class="mt-2 text-xs text-gray-500">Optional context about the vote (public sentiment, conditions, amendments, etc.).</p>
                        </div>

                        <div id="councillor-votes" class="rounded-xl border border-gray-200 bg-white p-6 hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Councillor votes</h3>
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
                                    <tbody id="councillor-table-body" class="bg-white divide-y divide-gray-200 text-sm"></tbody>
                                </table>
                            </div>
                            <p class="mt-3 text-xs text-gray-500">Leave councillors blank if you're unsure about their vote.</p>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <button type="submit" id="submit-button" class="inline-flex items-center justify-center px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-base font-semibold rounded-lg shadow">
                        <span id="button-text">Save hearing</span>
                        <svg id="button-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $previousVoteSelections = collect(old())->filter(fn ($value, $key) => str_starts_with($key, 'vote_'));
    @endphp

    <script>
        const councillorsByRegion = @json($councillorsByRegion);
        const previousVoteData = {
            vote_date: @json(old('vote_date')),
            passed: @json(old('passed')),
            notes: @json(old('notes')),
            selections: @json($previousVoteSelections),
        };

        function toggleHearingType() {
            const type = document.querySelector('input[name="type"]:checked')?.value;
            const titleField = document.getElementById('title-field');
            const developmentFields = document.getElementById('development-fields');
            const streetWrapper = document.getElementById('street-address-wrapper');
            const postalWrapper = document.getElementById('postal-code-wrapper');
            const developmentCard = document.getElementById('development-option');
            const policyCard = document.getElementById('policy-option');

            const highlightCard = (card, isActive) => {
                if (!card) {
                    return;
                }

                card.classList.toggle('border-blue-500', isActive);
                card.classList.toggle('bg-blue-50', isActive);
                card.classList.toggle('shadow-lg', isActive);
                card.classList.toggle('border-gray-200', !isActive);
                card.classList.toggle('bg-white', !isActive);
            };

            highlightCard(developmentCard, type === 'development');
            highlightCard(policyCard, type === 'policy');

            if (type === 'policy') {
                titleField.classList.remove('hidden');
                const titleInput = document.getElementById('title');
                if (titleInput) {
                    titleInput.required = true;
                }

                developmentFields.classList.add('hidden');
                const devFields = ['street_address', 'postal_code', 'rental', 'units', 'below_market_units'];
                devFields.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.required = false;
                    }
                });
                streetWrapper?.classList.add('hidden');
                postalWrapper?.classList.add('hidden');
            } else {
                titleField.classList.add('hidden');
                const titleInput = document.getElementById('title');
                if (titleInput) {
                    titleInput.required = false;
                }

                developmentFields.classList.remove('hidden');
                const devFields = ['street_address', 'postal_code', 'rental', 'units', 'below_market_units'];
                devFields.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.required = true;
                    }
                });
                streetWrapper?.classList.remove('hidden');
                postalWrapper?.classList.remove('hidden');
            }
        }

        function updateVoteSection() {
            const subjectSelect = document.getElementById('subject_to_vote');
            const voteSection = document.getElementById('vote-section');
            const voteFields = document.getElementById('vote-fields');
            const voteMessage = document.getElementById('vote-message');
            const councillorWrapper = document.getElementById('councillor-votes');
            const regionSelect = document.getElementById('region_id');
            const startDateInput = document.getElementById('start_date');

            if (!subjectSelect || !voteSection) {
                return;
            }

            const wantsVote = subjectSelect.value === '1';
            if (!wantsVote) {
                voteSection.classList.add('hidden');
                voteFields?.classList.add('hidden');
                voteMessage?.classList.add('hidden');
                councillorWrapper?.classList.add('hidden');
                return;
            }

            voteSection.classList.remove('hidden');

            const regionId = regionSelect?.value ?? '';
            const hearingDateValue = startDateInput?.value ?? '';

            if (!hearingDateValue) {
                voteFields?.classList.add('hidden');
                if (voteMessage) {
                    voteMessage.textContent = 'Enter the hearing date to record vote results.';
                    voteMessage.classList.remove('hidden');
                }
                councillorWrapper?.classList.add('hidden');
                return;
            }

            const today = new Date();
            const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const hearingDate = new Date(`${hearingDateValue}T00:00:00`);

            const isPastOrToday = !Number.isNaN(hearingDate.getTime()) && hearingDate <= todayStart;

            if (!isPastOrToday) {
                voteFields?.classList.add('hidden');
                if (voteMessage) {
                    voteMessage.textContent = 'We’ll save the hearing now. Vote details can be added after the hearing date.';
                    voteMessage.classList.remove('hidden');
                }
                councillorWrapper?.classList.add('hidden');
                return;
            }

            voteMessage?.classList.add('hidden');
            voteFields?.classList.remove('hidden');
            renderCouncillorRows(regionId, hearingDateValue);
        }

        function renderCouncillorRows(regionId, hearingDateValue) {
            const councillorWrapper = document.getElementById('councillor-votes');
            const tableBody = document.getElementById('councillor-table-body');

            if (!councillorWrapper || !tableBody) {
                return;
            }

            const existingSelections = {};
            tableBody.querySelectorAll('input[type="radio"]:checked').forEach((input) => {
                existingSelections[input.name] = input.value;
            });

            tableBody.innerHTML = '';

            const councillors = regionId && councillorsByRegion[regionId] ? councillorsByRegion[regionId] : [];

            if (!councillors.length) {
                councillorWrapper.classList.add('hidden');
                return;
            }

            const hearingDate = hearingDateValue ? new Date(`${hearingDateValue}T00:00:00`) : null;

            const filtered = councillors.filter((c) => {
                if (!hearingDate || Number.isNaN(hearingDate.getTime())) {
                    return true;
                }

                if (c.elected_start && new Date(`${c.elected_start}T00:00:00`) > hearingDate) {
                    return false;
                }

                if (c.elected_end && new Date(`${c.elected_end}T00:00:00`) < hearingDate) {
                    return false;
                }

                return true;
            });

            if (!filtered.length) {
                councillorWrapper.classList.add('hidden');
                return;
            }

            const previousSelections = previousVoteData.selections || {};

            filtered.forEach((councillor) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                const voteOptions = ['for', 'against', 'abstain', 'absent'];

                const cells = voteOptions.map((value) => {
                    const inputName = `vote_${councillor.id}`;
                    const colour = value === 'for'
                        ? 'text-green-600'
                        : value === 'against'
                            ? 'text-red-600'
                            : value === 'abstain'
                                ? 'text-amber-600'
                                : 'text-gray-600';

                    const existingValue = existingSelections[inputName] ?? previousSelections[inputName] ?? null;
                    const checked = existingValue === value ? 'checked' : '';

                    return `<td class="px-4 py-3 text-center"><input type="radio" name="${inputName}" value="${value}" class="h-4 w-4 ${colour} focus:ring-purple-500" ${checked}></td>`;
                }).join('');

                row.innerHTML = `
                    <td class="px-6 py-3 font-medium text-gray-900">${councillor.name}</td>
                    ${cells}
                `;

                tableBody.appendChild(row);
            });

            councillorWrapper.classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleHearingType();
            updateVoteSection();

            document.querySelectorAll('input[name="type"]').forEach((input) => {
                input.addEventListener('change', toggleHearingType);
            });

            document.getElementById('subject_to_vote')?.addEventListener('change', updateVoteSection);
            document.getElementById('start_date')?.addEventListener('change', updateVoteSection);
            document.getElementById('region_id')?.addEventListener('change', updateVoteSection);

            // Handle form submission to prevent duplicates
            const form = document.querySelector('form');
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const buttonSpinner = document.getElementById('button-spinner');

            form?.addEventListener('submit', () => {
                if (submitButton && buttonText && buttonSpinner) {
                    submitButton.disabled = true;
                    submitButton.classList.remove('hover:bg-blue-700');
                    submitButton.classList.add('bg-blue-700', 'cursor-not-allowed');
                    buttonText.textContent = 'Saving...';
                    buttonSpinner.classList.remove('hidden');
                }
            });
        });
    </script>
</x-guest-layout>
