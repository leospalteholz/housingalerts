<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Subscribers') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Subscriber Directory</h3>
                            <p class="text-sm text-gray-600">
                                {{ $subscribers->count() }} subscriber{{ $subscribers->count() === 1 ? '' : 's' }}
                                {{ $isSuperuser ? 'across all organizations.' : 'subscribed to your regions.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name & Email</th>
                                @if($isSuperuser)
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Organizations</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subscribed Regions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($subscribers as $subscriber)
                                @php
                                    $regions = $subscriber->regions;
                                    $organizations = $regions->pluck('organization')->filter()->unique('id');
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $subscriber->name ?? '—' }}</div>
                                        <div class="text-sm text-gray-500">{{ $subscriber->email }}</div>
                                    </td>
                                    @if($isSuperuser)
                                        <td class="px-6 py-4 align-top text-sm text-gray-700">
                                            @if($organizations->isNotEmpty())
                                                <ul class="list-inside list-disc space-y-1 text-gray-600">
                                                    @foreach($organizations as $organizationItem)
                                                        <li>{{ $organizationItem->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-gray-400">No organizations</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 align-top text-sm text-gray-700">
                                        @if($regions->isNotEmpty())
                                            <ul class="list-inside list-disc space-y-1 text-gray-600">
                                                @foreach($regions as $region)
                                                    <li>{{ $region->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-gray-400">No subscriptions</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="inline-block h-2 w-2 rounded-full {{ $subscriber->unsubscribed_at ? 'bg-red-400' : 'bg-green-400' }}"></span>
                                            {{ $subscriber->unsubscribed_at ? 'Unsubscribed' : 'Subscribed' }}
                                        </span>
                                        @if(!$subscriber->hasVerifiedEmail())
                                            <p class="mt-1 text-xs text-amber-600">Email not verified</p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <form method="POST" action="{{ orgRoute('subscribers.destroy', ['subscriber' => $subscriber]) }}" onsubmit="return confirm('Delete this subscriber? This removes their region subscriptions.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isSuperuser ? '5' : '4' }}" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No subscribers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
