<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <div class="bg-white rounded shadow p-4">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Admin:</strong> {{ $user->is_admin ? 'Yes' : 'No' }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to Users</a>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <div class="bg-white rounded shadow p-4">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Admin:</strong> {{ $user->is_admin ? 'Yes' : 'No' }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="mt-4 inline-block text-blue-600 hover:underline">Back to Users</a>
    </div>
</x-app-layout>
