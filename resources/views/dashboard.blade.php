<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(auth()->user() && auth()->user()->is_admin)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <a href="{{ route('users.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
                                <h4 class="text-lg font-semibold mb-2">Manage Users</h4>
                                <p class="text-gray-600">View, add, edit, or remove users.</p>
                            </a>
                            <a href="{{ route('regions.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
                                <h4 class="text-lg font-semibold mb-2">Manage Regions</h4>
                                <p class="text-gray-600">View, add, edit, or remove regions.</p>
                            </a>
                            <a href="{{ route('hearings.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
                                <h4 class="text-lg font-semibold mb-2">Manage Hearings</h4>
                                <p class="text-gray-600">View, add, edit, or remove hearings.</p>
                            </a>
                        </div>
                    @else
                        <h3 class="text-2xl font-bold mb-4">User Dashboard</h3>
                        <p>Welcome! You are logged in as a regular user.</p>
                        <!-- Add more user-specific content here -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
