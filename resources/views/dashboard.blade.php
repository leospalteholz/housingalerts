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
                    <h3 class="text-2xl font-bold mb-4">{{ auth()->user()->organization->name }}</h3>
                    
                    <!-- Superusers can manage organizations -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        @if(auth()->user() && auth()->user()->is_superuser)
                            <a href="{{ route('organizations.index') }}" class="block p-6 bg-white rounded-lg shadow hover:bg-gray-100 transition">
                                <h4 class="text-lg font-semibold mb-2">Manage Organizations</h4>
                                <p class="text-gray-600">View, add, edit, or remove organizations.</p>
                            </a>
                        @endif
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

                    @if(isset($stats))
                    <div class="mt-8 mb-8">
                        <h4 class="text-lg font-semibold mb-3">
                            @if(auth()->user()->is_superuser)
                                System-wide Statistics
                            @else
                                {{ auth()->user()->organization->name }} Statistics
                            @endif
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-{{ auth()->user()->is_superuser ? '4' : '3' }} gap-4">
                            @if(auth()->user()->is_superuser)
                            <div class="p-4 bg-blue-50 rounded shadow">
                                <span class="text-3xl font-bold text-blue-700">{{ $stats['organizations'] }}</span>
                                <p class="text-gray-600">Organizations</p>
                            </div>
                            @endif
                            <div class="p-4 bg-green-50 rounded shadow">
                                <span class="text-3xl font-bold text-green-700">{{ $stats['totalUsers'] }}</span>
                                <p class="text-gray-600">Users</p>
                            </div>
                            <div class="p-4 bg-purple-50 rounded shadow">
                                <span class="text-3xl font-bold text-purple-700">{{ $stats['totalRegions'] }}</span>
                                <p class="text-gray-600">Regions</p>
                            </div>
                            <div class="p-4 bg-amber-50 rounded shadow">
                                <span class="text-3xl font-bold text-amber-700">{{ $stats['totalHearings'] }}</span>
                                <p class="text-gray-600">Hearings</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(auth()->user() && !auth()->user()->is_admin && !auth()->user()->is_superuser)
                        <h3 class="text-2xl font-bold mb-4">User Dashboard</h3>
                        <p>Welcome! You are logged in as a regular user.</p>
                        <!-- Add more user-specific content here -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
