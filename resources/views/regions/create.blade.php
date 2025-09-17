<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Region') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Create New Region</h3>
                    </div>

                    <form method="POST" action="{{ route('regions.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Region Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Region Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">Name of the jurisdiction (e.g., Victoria, Langley, etc.)</p>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <p class="mt-1 text-sm text-gray-500">The following details for engaging with hearings over email, remotely, or in person can be customized when creating new hearings in this region.</p>

                        <!-- Comments Email -->
                        <div>
                            <label for="comments_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Comments Email
                            </label>
                            <input type="email" 
                                   id="comments_email" 
                                   name="comments_email" 
                                   value="{{ old('comments_email') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g., council@city.com">
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
                                      placeholder="Instructions for joining hearings remotely (e.g., Zoom links, phone numbers, etc.)">{{ old('remote_instructions') }}</textarea>
                            
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
                                      placeholder="Instructions for attending hearings in person (e.g., address, parking, entrance details, etc.)">{{ old('inperson_instructions') }}</textarea>
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
                                Create Region
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
