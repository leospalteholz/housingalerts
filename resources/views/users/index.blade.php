<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>
    <div class="max-w-3xl mx-auto py-8">
    <!-- Removed duplicate page title for cleaner UI -->
        <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add User</a>
        <ul class="bg-white rounded shadow divide-y divide-gray-200">
            @forelse ($users as $user)
                <li class="p-4 flex items-center justify-between">
                    <span>{{ $user->name }} ({{ $user->email }})</span>
                    <span class="flex space-x-2">
                        <a href="{{ route('users.show', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                        <a href="{{ route('users.edit', $user) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded text-sm">Delete</button>
                        </form>
                    </span>
                </li>
            @empty
                <li class="p-4 text-gray-500">No users found.</li>
            @endforelse
        </ul>
    </div>
</x-app-layout>
