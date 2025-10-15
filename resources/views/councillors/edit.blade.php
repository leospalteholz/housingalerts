<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Councillor') }}
        </h2>
    </x-slot>
    
    <div class="max-w-md mx-auto py-8">
        <form method="POST" action="{{ route('councillors.update', $councillor) }}" class="bg-white rounded shadow p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="region_id" class="block text-gray-700 font-semibold mb-2">Region</label>
                <select id="region_id" name="region_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a region...</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id', $councillor->region_id) == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                            @if(auth()->user()->is_superuser && $region->organization)
                                ({{ $region->organization->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $councillor->name) }}" 
                       required 
                       placeholder="e.g., Mayor Jane Smith"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="elected_start" class="block text-gray-700 font-semibold mb-2">Elected Start Date</label>
                <input type="date" 
                       id="elected_start" 
                       name="elected_start" 
                       value="{{ old('elected_start', $councillor->elected_start->format('Y-m-d')) }}" 
                       required 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-600 mt-1">The date this councillor's term began</p>
                @error('elected_start')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="elected_end" class="block text-gray-700 font-semibold mb-2">Elected End Date (Optional)</label>
                <input type="date" 
                       id="elected_end" 
                       name="elected_end" 
                       value="{{ old('elected_end', $councillor->elected_end?->format('Y-m-d')) }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-600 mt-1">Leave blank if currently serving</p>
                @error('elected_end')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Update Councillor
                </button>
                <a href="{{ route('councillors.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                    Cancel
                </a>
                
                <form method="POST" action="{{ route('councillors.destroy', $councillor) }}" onsubmit="return confirm('Are you sure you want to delete this councillor? This action cannot be undone.');" class="ml-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </div>
</x-app-layout>
