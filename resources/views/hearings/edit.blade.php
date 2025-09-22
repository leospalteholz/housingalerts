<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hearing') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <form method="POST" action="{{ route('hearings.update', $hearing) }}" enctype="multipart/form-data" class="bg-white rounded shadow p-6">
            @csrf
            @method('PUT')
            
            <!-- Hearing Type (Read-only) -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Hearing Type</label>
                <div class="p-4 bg-gray-50 rounded-lg border">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $hearing->isDevelopment() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $hearing->isDevelopment() ? 'Development Hearing' : 'Policy Hearing' }}
                        </span>
                        <span class="ml-3 text-sm text-gray-600">Type cannot be changed after creation</span>
                    </div>
                </div>
                <!-- Hidden field to maintain type -->
                <input type="hidden" name="type" value="{{ $hearing->type }}">
                <!-- Hidden field to maintain organization -->
                <input type="hidden" name="organization_id" value="{{ $hearing->organization_id }}">
            </div>
            
            <!-- Region -->
            <div class="mb-4">
                <label for="region_id" class="block text-gray-700 font-semibold mb-2">Region</label>
                <select id="region_id" name="region_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a region...</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" 
                            {{ old('region_id', $hearing->region_id) == $region->id ? 'selected' : '' }}>
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

            @if($hearing->isPolicy())
                <!-- Title for Policy Hearings -->
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-semibold mb-2">Hearing Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $hearing->title) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($hearing->isDevelopment())
                <!-- Development-specific fields -->
                <div class="mb-4">
                    <label for="street_address" class="block text-gray-700 font-semibold mb-2">Street Address</label>
                    <input type="text" id="street_address" name="street_address" value="{{ old('street_address', $hearing->street_address) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('street_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="postal_code" class="block text-gray-700 font-semibold mb-2">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $hearing->postal_code) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="rental" class="block text-gray-700 font-semibold mb-2">Property Type</label>
                    <select id="rental" name="rental" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select property type...</option>
                        <option value="1" {{ old('rental', $hearing->rental) == '1' ? 'selected' : '' }}>Rental Property</option>
                        <option value="0" {{ old('rental', $hearing->rental) == '0' ? 'selected' : '' }}>Condo/Owned Property</option>
                    </select>
                    @error('rental')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="units" class="block text-gray-700 font-semibold mb-2">Number of Homes</label>
                    <input type="number" id="units" name="units" value="{{ old('units', $hearing->units) }}" min="1" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('units')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            
            <!-- Common fields for both types -->
            
            <!-- Hearing Scheduling -->
            <div class="mb-4">
                <label for="start_date" class="block text-gray-700 font-semibold mb-2">Hearing Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $hearing->start_date) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="start_time" class="block text-gray-700 font-semibold mb-2">Start Time</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $hearing->start_time) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('start_time')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="end_time" class="block text-gray-700 font-semibold mb-2">End Time</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $hearing->end_time) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('end_time')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Content Section -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Hearing Description</label>
                <p class="text-sm text-gray-600 mb-2">💡 Add important details that might entice someone to support this project</p>
                <textarea id="description" name="description" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4">{{ old('description', $hearing->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Image Upload -->
            <x-image-upload 
                name="image" 
                label="Header Image (Optional)"
                :current-image="$hearing->image_url"
                :required="false"
                help-text="Upload a landscape image (recommended: 1200x400px) to make your hearing more visually appealing. Supports JPEG and WebP formats up to 2MB."
            />
            
            <div class="mb-6">
                <label for="more_info_url" class="block text-gray-700 font-semibold mb-2">More Info URL</label>
                <p class="text-sm text-gray-600 mb-2">� Link to the municipality's meeting details page</p>
                <input type="url" id="more_info_url" name="more_info_url" value="{{ old('more_info_url', $hearing->more_info_url) }}" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <input type="email" id="comments_email" name="comments_email" value="{{ old('comments_email', $hearing->comments_email) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('comments_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="remote_instructions" class="block text-gray-700 font-semibold mb-2">Remote Joining Instructions</label>
                    <p class="text-sm text-gray-600 mb-2">� How to join remotely (phone, video call, etc.)</p>
                    <textarea id="remote_instructions" name="remote_instructions" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">{{ old('remote_instructions', $hearing->remote_instructions) }}</textarea>
                    @error('remote_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="inperson_instructions" class="block text-gray-700 font-semibold mb-2">In-Person Instructions</label>
                    <p class="text-sm text-gray-600 mb-2">🏢 Where and how to attend in person</p>
                    <textarea id="inperson_instructions" name="inperson_instructions" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3">{{ old('inperson_instructions', $hearing->inperson_instructions) }}</textarea>
                    @error('inperson_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex justify-between items-center">
                <div class="space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Update</button>
                    <a href="{{ route('hearings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
                </div>
            </div>
        </form>

        <!-- Delete Button (separate form) -->
        <form action="{{ route('hearings.destroy', $hearing) }}" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to delete this hearing? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded w-full">
                Delete Hearing
            </button>
        </form>
    </div>

    <script>
        // Region data for JavaScript
        const regionData = @json($regions->keyBy('id'));
        
        const regionSelect = document.getElementById('region_id');
        const copyButton = document.getElementById('copyFromRegion');
        const commentsEmailField = document.getElementById('comments_email');
        const remoteInstructionsField = document.getElementById('remote_instructions');
        const inpersonInstructionsField = document.getElementById('inperson_instructions');

        // Enable/disable copy button based on region selection
        function updateCopyButton() {
            const selectedRegionId = regionSelect.value;
            if (selectedRegionId && regionData[selectedRegionId]) {
                copyButton.disabled = false;
            } else {
                copyButton.disabled = true;
            }
        }

        // Initial check on page load
        updateCopyButton();

        regionSelect.addEventListener('change', updateCopyButton);

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
