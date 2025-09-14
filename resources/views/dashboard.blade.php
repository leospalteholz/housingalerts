<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user() && !auth()->user()->is_admin && !auth()->user()->is_superuser)
                {{-- Regular User Dashboard --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        {{-- Welcome Header --}}
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-3xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h3>
                            
                            {{-- Email Verification Status --}}
                            <div class="flex items-center">
                                @if(auth()->user()->hasVerifiedEmail())
                                    <div class="flex items-center text-green-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">Email Verified</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-amber-600">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium">Email Not Verified</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Organization Info --}}
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-semibold text-lg text-blue-800 mb-2">Organization</h4>
                            <p class="text-blue-700">{{ auth()->user()->organization->name }}</p>
                        </div>

                        {{-- Monitored Regions --}}
                        <div class="mb-6">
                            <h4 class="font-semibold text-lg text-gray-800 mb-4">You are monitoring these regions:</h4>
                            @if($monitoredRegions && $monitoredRegions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($monitoredRegions as $region)
                                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                                            <h5 class="font-medium text-green-800">{{ $region->name }}</h5>
                                            <p class="text-sm text-green-600">{{ $region->organization->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-600">You are not currently monitoring any regions. Contact your administrator to be assigned to regions.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Upcoming Hearings --}}
                        <div class="mb-6">
                            <h4 class="font-semibold text-lg text-gray-800 mb-4">Upcoming hearings in your regions:</h4>
                            @if($upcomingHearings && $upcomingHearings->count() > 0)
                                <div class="space-y-4">
                                    @foreach($upcomingHearings as $hearing)
                                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h5 class="font-semibold text-gray-800 mb-2">{{ $hearing->title }}</h5>
                                                    <p class="text-gray-600 mb-2">{{ $hearing->description }}</p>
                                                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span>
                                                                @if($hearing->start_date)
                                                                    {{ \Carbon\Carbon::parse($hearing->start_date)->format('M j, Y') }}
                                                                    @if($hearing->start_time)
                                                                        at {{ \Carbon\Carbon::parse($hearing->start_time)->format('g:i A') }}
                                                                    @endif
                                                                @else
                                                                    Date TBD
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span>{{ $hearing->region->name }}</span>
                                                        </div>
                                                    </div>
                                                    @if($hearing->location)
                                                        <p class="text-sm text-gray-500 mt-1">Location: {{ $hearing->location }}</p>
                                                    @endif
                                                    @if($hearing->image_url)
                                                        <p class="text-sm text-blue-600 mt-1">
                                                            <a href="{{ $hearing->image_url }}" target="_blank" class="hover:underline">View attachment</a>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-gray-600">No upcoming hearings scheduled in your monitored regions.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- Admin/Superuser Dashboard --}}
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
                                {{ auth()->user()->organization->name }} Statistics
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
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
