<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hearing Details') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <div class="bg-white rounded shadow p-4">
            <p><strong>Title:</strong> {{ $hearing->title }}</p>
            <p><strong>Details:</strong> {{ $hearing->body }}</p>
            <p><strong>Start Date:</strong> {{ $hearing->start_date }}</p>
            <p><strong>Start Time:</strong> {{ $hearing->start_time }}</p>
            <p><strong>More Info URL:</strong> <a href="{{ $hearing->more_info_url }}" class="text-blue-600 hover:underline" target="_blank">{{ $hearing->more_info_url }}</a></p>
        </div>
        <a href="{{ route('hearings.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to Hearings</a>
    </div>
</x-app-layout>
