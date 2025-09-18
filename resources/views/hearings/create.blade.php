<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Hearing') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <form method="POST" action="{{ route('hearings.store') }}" enctype="multipart/form-data" class="bg-white rounded shadow p-6">
            @csrf
            
            <!-- Hearing Type Selection -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2 required-field">Hearing Type</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="development" class="sr-only" {{ old('type', 'development') == 'development' ? 'checked' : '' }} onchange="toggleHearingType()">
                        <div class="border-2 rounded-lg p-4 text-center transition-all duration-200 hover:bg-gray-50" id="development-option">
                            <div class="text-lg font-medium">Development</div>
                            <div class="text-sm text-gray-600">Housing approval hearing</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="policy" class="sr-only" {{ old('type') == 'policy' ? 'checked' : '' }} onchange="toggleHearingType()">
                        <div class="border-2 rounded-lg p-4 text-center transition-all duration-200 hover:bg-gray-50" id="policy-option">
                            <div class="text-lg font-medium">Policy</div>
                            <div class="text-sm text-gray-600">General policy hearing</div>
                        </div>
                    </label>
                </div>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Region (moved to first) -->
            <div class="mb-4">
                <label for="region_id" class="block text-gray-700 font-semibold mb-2">Region</label>
                <select id="region_id" name="region_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a region...</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                            @if(auth()->user()->is_superuser && $region->organization)
                                ({{ $region->organization->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title for Policy Hearings -->
            <div class="mb-4" id="title-field" style="display: none;">
                <label for="title" class="block text-gray-700 font-semibold mb-2">Hearing Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., OCP Update Hearing">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Development-specific fields -->
            <div id="development-fields">
                <!-- Property Address Information -->
                <div class="mb-4">
                    <label for="street_address" class="block text-gray-700 font-semibold mb-2">Street Address</label>
                    <input type="text" id="street_address" name="street_address" value="{{ old('street_address') }}" placeholder="eg. 123 Main St" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('street_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="postal_code" class="block text-gray-700 font-semibold mb-2">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="rental" class="block text-gray-700 font-semibold mb-2">Property Type</label>
                    <select id="rental" name="rental" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select property type...</option>
                        <option value="1" {{ old('rental') == '1' ? 'selected' : '' }}>Rental Property</option>
                        <option value="0" {{ old('rental') == '0' ? 'selected' : '' }}>Condo/Owned Property</option>
                    </select>
                @error('rental')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="units" class="block text-gray-700 font-semibold mb-2">Number of Homes</label>
                <input type="number" id="units" name="units" value="{{ old('units') }}" min="1" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('units')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div> <!-- End development-fields -->
            
            <!-- Hearing Scheduling -->
            <div class="mb-4">
                <label for="start_date" class="block text-gray-700 font-semibold mb-2">Hearing Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="start_time" class="block text-gray-700 font-semibold mb-2">Start Time</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" step="900" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('start_time')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="end_time" class="block text-gray-700 font-semibold mb-2">End Time</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" step="900" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('end_time')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Content Section -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Hearing Description</label>
                <textarea id="description" name="description" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4">{{ old('description') }}</textarea>
                <p class="text-sm text-gray-600 mt-1">Add important details that might entice someone to support this project</p>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Image Upload -->
            <x-image-upload 
                name="image" 
                label="Header Image (Optional)"
                :required="false"
                help-text="Upload a landscape image (recommended: 1200x400px) to make your hearing more visually appealing. Supports JPEG and WebP formats up to 2MB."
            />
            
            <div class="mb-6">
                <label for="more_info_url" class="block text-gray-700 font-semibold mb-2">More Info URL</label>
                <input type="url" id="more_info_url" name="more_info_url" value="{{ old('more_info_url') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-600 mt-1">Link to the municipality's page for this hearing</p>
                @error('more_info_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Call to Action Section -->
            <div class="border-t pt-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Call to Action</h3>
                    <button type="button" id="copyFromRegion" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded" disabled>
                        Copy from Region
                    </button>
                </div>
                <div class="mb-4">
                    <label for="comments_email" class="block text-gray-700 font-semibold mb-2">Comments Email</label>
                    <input type="email" id="comments_email" name="comments_email" value="{{ old('comments_email') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('comments_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="remote_instructions" class="block text-gray-700 font-semibold mb-2">Remote Joining Instructions</label>
                    <textarea id="remote_instructions" name="remote_instructions" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">{{ old('remote_instructions') }}</textarea>
                    <p class="text-sm text-gray-600 mt-1">How to join remotely (phone, video call, etc.)</p>
                    @error('remote_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="inperson_instructions" class="block text-gray-700 font-semibold mb-2">In-Person Instructions</label>
                    <textarea id="inperson_instructions" name="inperson_instructions" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">{{ old('inperson_instructions') }}</textarea>
                    <p class="text-sm text-gray-600 mt-1">Where and how to attend in person</p>
                    @error('inperson_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-4 text-sm text-gray-600">
                <span class="text-red-500">*</span> Required fields
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Create</button>
            <a href="{{ route('hearings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
        </form>
    </div>

    <script>
        // Hearing type toggle functionality
        function toggleHearingType() {
            const developmentRadio = document.querySelector('input[name="type"][value="development"]');
            const policyRadio = document.querySelector('input[name="type"][value="policy"]');
            const developmentOption = document.getElementById('development-option');
            const policyOption = document.getElementById('policy-option');
            const titleField = document.getElementById('title-field');
            const developmentFields = document.getElementById('development-fields');
            
            // Update visual styling for radio buttons
            if (developmentRadio.checked) {
                developmentOption.classList.add('border-blue-500', 'bg-blue-50');
                developmentOption.classList.remove('border-gray-300');
                policyOption.classList.remove('border-blue-500', 'bg-blue-50');
                policyOption.classList.add('border-gray-300');
                
                // Show development fields, hide title field
                developmentFields.style.display = 'block';
                titleField.style.display = 'none';
                
                // Make development fields required
                document.getElementById('street_address').required = true;
                document.getElementById('postal_code').required = true;
                document.getElementById('rental').required = true;
                document.getElementById('units').required = true;
                document.getElementById('title').required = false;
            } else if (policyRadio.checked) {
                policyOption.classList.add('border-blue-500', 'bg-blue-50');
                policyOption.classList.remove('border-gray-300');
                developmentOption.classList.remove('border-blue-500', 'bg-blue-50');
                developmentOption.classList.add('border-gray-300');
                
                // Hide development fields, show title field
                developmentFields.style.display = 'none';
                titleField.style.display = 'block';
                
                // Make title required, development fields not required
                document.getElementById('title').required = true;
                document.getElementById('street_address').required = false;
                document.getElementById('postal_code').required = false;
                document.getElementById('rental').required = false;
                document.getElementById('units').required = false;
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleHearingType();
        });

        // Region data for JavaScript
        const regionData = @json($regions->keyBy('id'));
        
        const regionSelect = document.getElementById('region_id');
        const copyButton = document.getElementById('copyFromRegion');
        const commentsEmailField = document.getElementById('comments_email');
        const remoteInstructionsField = document.getElementById('remote_instructions');
        const inpersonInstructionsField = document.getElementById('inperson_instructions');

        // Enable/disable copy button based on region selection
        regionSelect.addEventListener('change', function() {
            const selectedRegionId = this.value;
            if (selectedRegionId && regionData[selectedRegionId]) {
                copyButton.disabled = false;
            } else {
                copyButton.disabled = true;
            }
        });

        // Copy from region functionality
        copyButton.addEventListener('click', function() {
            const selectedRegionId = regionSelect.value;
            if (selectedRegionId && regionData[selectedRegionId]) {
                const region = regionData[selectedRegionId];
                
                if (region.comments_email) {
                    commentsEmailField.value = region.comments_email;
                }
                if (region.remote_instructions) {
                    remoteInstructionsField.value = region.remote_instructions;
                }
                if (region.inperson_instructions) {
                    inpersonInstructionsField.value = region.inperson_instructions;
                }
                
                // Show success message
                copyButton.textContent = 'Copied!';
                copyButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                copyButton.classList.add('bg-gray-500');
                
                setTimeout(() => {
                    copyButton.textContent = 'Copy from Region';
                    copyButton.classList.remove('bg-gray-500');
                    copyButton.classList.add('bg-green-600', 'hover:bg-green-700');
                }, 2000);
            }
        });
    </script>
</x-app-layout>
