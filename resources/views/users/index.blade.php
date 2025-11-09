<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users') }}
            </h2>
            <a href="{{ orgRoute('users.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase tracking-widest py-2 px-4 rounded shadow transition duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400">
                + Add User
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <!-- Admins Section -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-purple-50 px-6 py-4 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-purple-800">Administrators</h3>
                    <p class="text-sm text-purple-600">{{ $admins->count() }} admin(s) registered</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name & Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($admins as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->is_superuser)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Superuser
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $user->organization->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ orgRoute('users.show', ['user' => $user]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                        <a href="{{ orgRoute('users.edit', ['user' => $user]) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ orgRoute('users.destroy', ['user' => $user]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded text-sm">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-gray-500 text-center">No administrators found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                    </table>
                </div>
            </div>

            <!-- Regular Users Section -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-800">Regular Users</h3>
                    <p class="text-sm text-green-600">{{ $regularUsers->count() }} regular user(s) registered</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name & Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribed Regions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($regularUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->regions && $user->regions->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($user->regions as $region)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $region->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">No subscriptions</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Unverified
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ orgRoute('users.show', ['user' => $user]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                        <a href="{{ orgRoute('users.edit', ['user' => $user]) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ orgRoute('users.destroy', ['user' => $user]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded text-sm">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-gray-500 text-center">No regular users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
