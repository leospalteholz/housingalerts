<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ auth()->user()->organization->name }} Dashboard
            </h2>
            <div class="flex items-center text-sm text-gray-600">
                <x-icon name="user" class="h-4 w-4 mr-1" />
                {{ auth()->user()->is_superuser ? 'Superuser' : 'Administrator' }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if(auth()->user()->is_superuser)
                <!-- Superuser Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="building" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-blue-100 truncate">Organizations</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['organizations'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="user" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-green-100 truncate">Total Users</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['totalUsers'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="location" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-purple-100 truncate">Total Regions</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['totalRegions'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="calendar" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-orange-100 truncate">Total Hearings</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['totalHearings'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Admin Statistics for Organization -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="user" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-green-100 truncate">Organization Users</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['totalUsers'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="location" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-purple-100 truncate">Regions Managed</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['totalRegions'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-icon name="calendar" class="h-6 w-6 text-white" />
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-orange-100 truncate">Hearings Posted</dt>
                                        <dd class="text-lg font-medium text-white">{{ $stats['totalHearings'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
                <!-- Management Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Management Tools</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if(auth()->user()->is_superuser)
                            <a href="{{ route('users.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                                        <x-icon name="organization" class="h-6 w-6 text-black" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Manage Organizations
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">View, add, edit, and manage organizations in your system.</p>
                                </div>
                            </a>
                            @else 
                            <a href="{{ route('organizations.edit-own') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-gray-50 text-gray-700 ring-4 ring-white">
                                        <x-icon name="settings" class="h-6 w-6" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Organization Settings
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">Configure organization preferences and settings.</p>
                                </div>
                            </a>
                            @endif

                            <a href="{{ route('users.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                                        <x-icon name="user" class="h-6 w-6" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Manage Users
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">View, add, edit, and manage users in your organization.</p>
                                </div>
                            </a>

                            <a href="{{ route('regions.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
                                        <x-icon name="location" class="h-6 w-6" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Manage Regions
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">Add, edit, and configure notification regions and templates.</p>
                                </div>
                            </a>

                            <a href="{{ route('hearings.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-orange-50 text-orange-700 ring-4 ring-white">
                                        <x-icon name="calendar" class="h-6 w-6" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Manage Hearings
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">Post and manage development and policy hearings.</p>
                                </div>
                            </a>

                            <a href="{{ route('councillors.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-teal-50 text-teal-700 ring-4 ring-white">
                                        <x-icon name="user" class="h-6 w-6" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Manage Councillors
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">Track councillors and their voting records.</p>
                                </div>
                            </a>

                            <a href="{{ route('hearing-votes.index') }}" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-indigo-50 text-indigo-700 ring-4 ring-white">
                                        <x-icon name="check" class="h-6 w-6" />
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-700">
                                        Hearing Votes
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">Record and track council votes on hearings.</p>
                                </div>
                            </a>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
