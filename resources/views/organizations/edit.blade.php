<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Organization') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <form method="POST" action="{{ auth()->user()->is_superuser ? route('organizations.update', $organization) : route('organizations.update-own') }}" class="bg-white rounded shadow p-6">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $organization->name) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="slug" class="block text-gray-700 font-semibold mb-2">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $organization->slug) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="contact_email" class="block text-gray-700 font-semibold mb-2">Contact Email</label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $organization->contact_email) }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="website_url" class="block text-gray-700 font-semibold mb-2">Website URL</label>
                <input type="url" id="website_url" name="website_url" value="{{ old('website_url', $organization->website_url) }}" placeholder="https://example.com" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="areas_active" class="block text-gray-700 font-semibold mb-2">Areas Active</label>
                <input type="text" id="areas_active" name="areas_active" value="{{ old('areas_active', $organization->areas_active) }}" placeholder="e.g., Greater Victoria, BC" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-600 mt-1">Geographic areas where your organization is active</p>
            </div>
            <div class="mb-4">
                <label for="about" class="block text-gray-700 font-semibold mb-2">About</label>
                <textarea id="about" name="about" rows="4" placeholder="Tell us about your organization..." class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('about', $organization->about) }}</textarea>
            </div>
            @if(auth()->user()->is_superuser)
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="user_visible" value="1" {{ old('user_visible', $organization->user_visible) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Visible to users during signup</span>
                </label>
                <p class="text-sm text-gray-600 mt-1">If checked, users can select this organization when signing up</p>
            </div>
            @endif
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Update</button>
            <a href="{{ auth()->user()->is_superuser ? route('organizations.index') : route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
        </form>
    </div>
</x-app-layout>
