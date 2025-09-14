<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Organization Details') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <div class="bg-white rounded shadow p-6">
            <div class="mb-4">
                <strong>Name:</strong> {{ $organization->name }}
            </div>
            <div class="mb-4">
                <strong>Slug:</strong> {{ $organization->slug }}
            </div>
            <div class="mb-4">
                <strong>Contact Email:</strong> {{ $organization->{'contact_email'} ?: 'Not provided' }}
            </div>
            <div class="mb-4">
                <strong>Website:</strong> 
                @if($organization->website_url)
                    <a href="{{ $organization->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">{{ $organization->website_url }}</a>
                @else
                    Not provided
                @endif
            </div>
            <div class="mb-4">
                <strong>About:</strong>
                <div class="mt-2 text-gray-700">
                    {{ $organization->about ?: 'No description provided' }}
                </div>
            </div>
            <a href="{{ route('organizations.edit', $organization) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-2 px-4 rounded">Edit</a>
            <a href="{{ route('organizations.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Back</a>
        </div>
    </div>
</x-app-layout>
