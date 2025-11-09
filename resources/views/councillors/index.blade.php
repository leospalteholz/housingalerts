<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Councillors') }}
            </h2>
            <a href="{{ orgRoute('councillors.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Add New Councillor
            </a>
        </div>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="space-y-6">
                <!-- Current Councillors -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-green-800">Current Councillors</h3>
                                <p class="text-sm text-green-600">{{ $currentCouncillors->count() }} currently serving</p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                                    @if(auth()->user()->is_superuser)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($currentCouncillors as $councillor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $councillor->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $councillor->region->name }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->is_superuser)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $councillor->region->organization->name ?? 'N/A' }}
                                        </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $councillor->elected_start->format('M Y') }} - 
                                            {{ $councillor->elected_end ? $councillor->elected_end->format('M Y') : 'Present' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                            <a href="{{ orgRoute('councillors.show', ['councillor' => $councillor]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                            <a href="{{ orgRoute('councillors.edit', ['councillor' => $councillor]) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->is_superuser ? '5' : '4' }}" class="px-6 py-4 text-gray-500 text-center">No current councillors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Past Councillors -->
                @if($pastCouncillors->count() > 0)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Past Councillors</h3>
                            <p class="text-sm text-gray-600">{{ $pastCouncillors->count() }} past councillors</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
                                    @if(auth()->user()->is_superuser)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pastCouncillors as $councillor)
                                    <tr class="opacity-75">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $councillor->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $councillor->region->name }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->is_superuser)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $councillor->region->organization->name ?? 'N/A' }}
                                        </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $councillor->elected_start->format('M Y') }} - 
                                            {{ $councillor->elected_end ? $councillor->elected_end->format('M Y') : 'Present' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                            <a href="{{ orgRoute('councillors.show', ['councillor' => $councillor]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm">View</a>
                                            <a href="{{ orgRoute('councillors.edit', ['councillor' => $councillor]) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-1 px-3 rounded text-sm">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
