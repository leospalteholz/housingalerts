<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hearings') }}
        </h2>
    </x-slot>
    <div class="max-w-3xl mx-auto py-8">
        <a href="{{ route('hearings.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400">+ Create Hearing</a>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($hearings as $hearing)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $hearing->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($hearing->start_date)
                                    {{ \Carbon\Carbon::parse($hearing->start_date)->format('M j, Y') }}
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                <a href="{{ route('hearings.show', $hearing) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                <a href="{{ route('hearings.edit', $hearing) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                <form action="{{ route('hearings.destroy', $hearing) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this hearing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-gray-500">No hearings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
