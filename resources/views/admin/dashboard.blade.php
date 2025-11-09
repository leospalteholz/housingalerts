<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ auth()->user()->organization->name }} Dashboard
            </h2>
            @if(!auth()->user()->is_superuser)
                <a href="{{ orgRoute('organizations.edit-own') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-icon name="settings" class="h-4 w-4 mr-2" />
                    Organization Settings
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Management Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Management Tools</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Hearings -->
                            <a href="{{ orgRoute('hearings.index') }}" class="group relative bg-white p-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="rounded-lg inline-flex p-2 bg-orange-50 text-orange-700 ring-4 ring-white flex-shrink-0">
                                            <x-icon name="calendar" class="h-5 w-5" />
                                        </span>
                                        <div class="ml-4">
                                            <h3 class="text-base font-medium text-gray-900 group-hover:text-gray-700">
                                                Manage Hearings
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Post and manage development hearings.</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-400">{{ $stats['totalHearings'] }}</span>
                                </div>
                            </a>

                            <!-- Votes -->
                            <a href="{{ orgRoute('hearing-votes.index') }}" class="group relative bg-white p-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="rounded-lg inline-flex p-2 bg-indigo-50 text-indigo-700 ring-4 ring-white flex-shrink-0">
                                            <x-icon name="check" class="h-5 w-5" />
                                        </span>
                                        <div class="ml-4">
                                            <h3 class="text-base font-medium text-gray-900 group-hover:text-gray-700">
                                                Manage Votes
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Record and track council votes.</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-400">{{ $stats['totalVotes'] ?? 0 }}</span>
                                </div>
                            </a>

                            <!-- Users -->
                            <a href="{{ orgRoute('users.index') }}" class="group relative bg-white p-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="rounded-lg inline-flex p-2 bg-green-50 text-green-700 ring-4 ring-white flex-shrink-0">
                                            <x-icon name="user" class="h-5 w-5" />
                                        </span>
                                        <div class="ml-4">
                                            <h3 class="text-base font-medium text-gray-900 group-hover:text-gray-700">
                                                Manage Users
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Manage users in your organization.</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-400">{{ $stats['totalUsers'] }}</span>
                                </div>
                            </a>

                            <!-- Regions -->
                            <a href="{{ orgRoute('regions.index') }}" class="group relative bg-white p-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="rounded-lg inline-flex p-2 bg-purple-50 text-purple-700 ring-4 ring-white flex-shrink-0">
                                            <x-icon name="location" class="h-5 w-5" />
                                        </span>
                                        <div class="ml-4">
                                            <h3 class="text-base font-medium text-gray-900 group-hover:text-gray-700">
                                                Manage Regions
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Configure notification regions.</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-400">{{ $stats['totalRegions'] }}</span>
                                </div>
                            </a>

                            <!-- Councillors -->
                            <a href="{{ orgRoute('councillors.index') }}" class="group relative bg-white p-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="rounded-lg inline-flex p-2 bg-teal-50 text-teal-700 ring-4 ring-white flex-shrink-0">
                                            <x-icon name="user" class="h-5 w-5" />
                                        </span>
                                        <div class="ml-4">
                                            <h3 class="text-base font-medium text-gray-900 group-hover:text-gray-700">
                                                Manage Councillors
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Manage councillors and see voting records.</p>    
                                        </div>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-400">{{ $stats['totalCouncillors'] ?? 0 }}</span>
                                </div>
                            </a>

                            <!-- Organizations (Superuser only) -->
                            @if(auth()->user()->is_superuser)
                            <a href="{{ route('users.index') }}" class="group relative bg-white p-4 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="rounded-lg inline-flex p-2 bg-gray-50 text-gray-700 ring-4 ring-white flex-shrink-0">
                                            <x-icon name="organization" class="h-5 w-5 text-black" />
                                        </span>
                                        <div class="ml-4">
                                            <h3 class="text-base font-medium text-gray-900 group-hover:text-gray-700">
                                                Manage Organizations
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500">Manage organizations in the system.</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-400">{{ $stats['organizations'] }}</span>
                                </div>
                            </a>
                            @endif

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
