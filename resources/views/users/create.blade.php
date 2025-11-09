<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create User') }}
        </h2>
    </x-slot>
    <div class="max-w-md mx-auto py-8">
        <form method="POST" action="{{ orgRoute('users.store') }}" class="bg-white rounded shadow p-6">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="is_admin" class="block text-gray-700 font-semibold mb-2">Role</label>
                <select id="is_admin" name="is_admin" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="0" {{ old('is_admin') == '0' ? 'selected' : '' }}>Regular User</option>
                    <option value="1" {{ old('is_admin') == '1' ? 'selected' : '' }}>Administrator</option>
                </select>
                @error('is_admin')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            @if(auth()->user()->is_superuser)
                <div class="mb-4">
                    <label for="organization_id" class="block text-gray-700 font-semibold mb-2">Organization</label>
                    <select id="organization_id" name="organization_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select an organization...</option>
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Region Subscriptions</label>
                <p class="text-sm text-gray-600 mb-3">Select regions this user should receive notifications for:</p>
                <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded p-3">
                    @forelse($regions as $region)
                        <label class="flex items-center">
                            <input type="checkbox" name="regions[]" value="{{ $region->id }}" 
                                {{ in_array($region->id, old('regions', [])) ? 'checked' : '' }}
                                class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">
                                {{ $region->name }}
                                @if(auth()->user()->is_superuser && $region->organization)
                                    <span class="text-gray-500">({{ $region->organization->name }})</span>
                                @endif
                            </span>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm">No regions available for subscription.</p>
                    @endforelse
                </div>
                @error('regions')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-between items-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Create User</button>
                <a href="{{ orgRoute('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
