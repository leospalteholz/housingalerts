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

                @foreach($regionGroups as $group)
                    @php($region = $group['region'])
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 {{ $group['current']->count() ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                    <x-icon name="location" class="w-5 h-5 text-indigo-500" />
                                    {{ $region->name }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    {{ $group['current']->count() }} current, {{ $group['past']->count() }} past councillors
                                </p>
                                @if(auth()->user()->is_superuser)
                                    <p class="text-xs text-gray-500">Organization: {{ $region->organization->name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
                                    Current
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <span class="inline-block w-2 h-2 rounded-full bg-gray-400"></span>
                                    Past
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-5 space-y-6">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Current Councillors</h4>
                                @if($group['current']->isNotEmpty())
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($group['current'] as $councillor)
                                                    <tr>
                                                        <td class="px-5 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $councillor->name }}
                                                        </td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600">
                                                            {{ $councillor->elected_start?->format('M Y') }} - {{ $councillor->elected_end ? $councillor->elected_end->format('M Y') : 'Present' }}
                                                        </td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-sm">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ orgRoute('councillors.show', ['councillor' => $councillor]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-blue-600 text-white hover:bg-blue-700">View</a>
                                                                <a href="{{ orgRoute('councillors.edit', ['councillor' => $councillor]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-yellow-500 text-white hover:bg-yellow-600">Edit</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No current councillors assigned to this region.</p>
                                @endif
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Past Councillors</h4>
                                @if($group['past']->isNotEmpty())
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($group['past'] as $councillor)
                                                    <tr class="opacity-80">
                                                        <td class="px-5 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $councillor->name }}
                                                        </td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600">
                                                            {{ $councillor->elected_start?->format('M Y') }} - {{ $councillor->elected_end?->format('M Y') }}
                                                        </td>
                                                        <td class="px-5 py-3 whitespace-nowrap text-sm">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ orgRoute('councillors.show', ['councillor' => $councillor]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-blue-600 text-white hover:bg-blue-700">View</a>
                                                                <a href="{{ orgRoute('councillors.edit', ['councillor' => $councillor]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-yellow-500 text-white hover:bg-yellow-600">Edit</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No past councillors recorded for this region yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
