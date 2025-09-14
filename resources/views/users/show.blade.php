<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white rounded shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">User Information</h3>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Role</p>
                        @if($user->is_superuser)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Superuser
                            </span>
                        @elseif($user->is_admin)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Admin
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                User
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email Verification</p>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Verified on {{ $user->email_verified_at->format('M j, Y') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Unverified
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Organization</p>
                        @if($user->organization)
                            <p class="text-gray-900">{{ $user->organization->name }}</p>
                        @else
                            <span class="text-gray-500">No organization assigned</span>
                        @endif
                    </div>
                </div>

                <!-- Subscription Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Subscriptions</h3>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Subscribed Regions</p>
                        @if($user->regions && $user->regions->count() > 0)
                            <div class="space-y-2">
                                @foreach($user->regions as $region)
                                    <div class="bg-gray-50 rounded p-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $region->name }}</p>
                                                @if($region->organization)
                                                    <p class="text-sm text-gray-600">{{ $region->organization->name }}</p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded p-4 text-center">
                                <p class="text-gray-500">No region subscriptions</p>
                                <p class="text-sm text-gray-400">User will not receive hearing notifications</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex space-x-4">
            <a href="{{ route('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                Back to Users
            </a>
            <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                Edit User
            </a>
        </div>
    </div>
</x-app-layout>
