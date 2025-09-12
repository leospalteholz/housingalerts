<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regions') }}
        </h2>
    </x-slot>
    <div class="max-w-2xl mx-auto py-8 bg-white">
        <a href="{{ route('regions.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow border border-blue-700 transition duration-150 mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">+ Create Region</a>
        <ul class="bg-white rounded shadow divide-y divide-gray-200">
            @forelse ($regions as $region)
                <li class="p-4 flex items-center justify-between">
                    <span>{{ $region->name }}</span>
                    <span class="flex space-x-2">
                        <a href="{{ route('regions.edit', $region) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                        <form action="{{ route('regions.destroy', $region) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this region?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded text-sm">Delete</button>
                        </form>
                    </span>
                </li>
            @empty
                <li class="p-4 text-gray-500">No regions found.</li>
            @endforelse
        </ul>
    </div>
</x-app-layout>
