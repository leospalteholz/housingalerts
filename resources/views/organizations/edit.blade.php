<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Organization') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <form method="POST" action="{{ route('organizations.update', $organization) }}" class="bg-white rounded shadow p-6">
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
                <label for="about" class="block text-gray-700 font-semibold mb-2">About</label>
                <textarea id="about" name="about" rows="4" placeholder="Tell us about your organization..." class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('about', $organization->about) }}</textarea>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Update</button>
            <a href="{{ route('organizations.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
        </form>
    </div>
</x-app-layout>
