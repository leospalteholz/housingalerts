<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Region') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Edit Region</h3>
                    </div>

                    <form method="POST" action="{{ route('regions.update', $region) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Region Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Region Name
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $region->name) }}" 
                                   required 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Comments Email -->
                        <div>
                            <label for="comments_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Comments Email
                            </label>
                            <input type="email" 
                                   id="comments_email" 
                                   name="comments_email" 
                                   value="{{ old('comments_email', $region->comments_email) }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g., comments@example.com">
                            <p class="mt-1 text-sm text-gray-500">Default email address for receiving public comments on hearings in this region.</p>
                            @error('comments_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remote Instructions -->
                        <div>
                            <label for="remote_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Remote Instructions
                            </label>
                            <textarea id="remote_instructions" 
                                      name="remote_instructions" 
                                      rows="4" 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Default instructions for joining hearings remotely (e.g., Zoom links, phone numbers, etc.)">{{ old('remote_instructions', $region->remote_instructions) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">These instructions will be used as defaults when creating new hearings in this region.</p>
                            @error('remote_instructions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- In-Person Instructions -->
                        <div>
                            <label for="inperson_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                Default In-Person Instructions
                            </label>
                            <textarea id="inperson_instructions" 
                                      name="inperson_instructions" 
                                      rows="4" 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Default instructions for attending hearings in person (e.g., address, parking, entrance details, etc.)">{{ old('inperson_instructions', $region->inperson_instructions) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">These instructions will be used as defaults when creating new hearings in this region.</p>
                            @error('inperson_instructions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between pt-6">
                            <a href="{{ route('regions.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update Region
                            </button>
                        </div>
                    </form>

                    <!-- Delete Region Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-red-800 mb-2">Danger Zone</h4>
                            <p class="text-sm text-red-600 mb-3">
                                Delete this region. Note: Regions with existing hearings cannot be deleted.
                            </p>
                            <form action="{{ route('regions.destroy', $region) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this region?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Delete Region
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
