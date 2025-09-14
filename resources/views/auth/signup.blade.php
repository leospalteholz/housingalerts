<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Register for housing alerts') }}
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('signup.process') }}" id="signupForm">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Organization -->
        <div class="mt-4">
            <x-input-label for="organization_id" :value="__('Organization')" />
            <select id="organization_id" name="organization_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">Select organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                        {{ $organization->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('organization_id')" class="mt-2" />
        </div>

        <!-- Regions (populated via JavaScript) -->
        <div class="mt-4" id="regions-container" style="display: none;">
            <x-input-label for="regions" :value="__('Select regions you want to receive notifications about.')" />
            <div class="mt-2 p-2 border rounded-md max-h-60 overflow-y-auto" id="regions-list">
                <!-- Regions will be populated here via JavaScript -->
                <div class="text-gray-500 text-sm p-2">Please select an organization first.</div>
            </div>
            <x-input-error :messages="$errors->get('regions')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-4">
                {{ __('Sign Up') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const organizationSelect = document.getElementById('organization_id');
            const regionsContainer = document.getElementById('regions-container');
            const regionsList = document.getElementById('regions-list');
            
            organizationSelect.addEventListener('change', function() {
                const organizationId = this.value;
                
                if (!organizationId) {
                    regionsContainer.style.display = 'none';
                    return;
                }
                
                // Show loading state
                regionsList.innerHTML = '<div class="text-gray-500 text-sm p-2">Loading regions...</div>';
                regionsContainer.style.display = 'block';
                
                // Fetch regions for selected organization
                const url = `{{ route('signup.regions') }}?organization_id=${organizationId}`;
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(regions => {
                        if (regions.length === 0) {
                            regionsList.innerHTML = '<div class="text-gray-500 text-sm p-2">No regions available for this organization.</div>';
                            return;
                        }
                        
                        // Populate regions checkboxes
                        let html = '';
                        regions.forEach(region => {
                            html += `
                                <div class="p-2 hover:bg-gray-100">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="regions[]" value="${region.id}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2">${region.name}</span>
                                    </label>
                                </div>
                            `;
                        });
                        regionsList.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching regions:', error);
                        regionsList.innerHTML = `<div class="text-red-500 text-sm p-2">Error loading regions: ${error.message}. Please try again.</div>`;
                    });
            });
        });
    </script>
</x-guest-layout>
