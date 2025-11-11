<x-guest-layout>
    <div class="max-w-lg mx-auto py-12">
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">This dashboard link has expired</h1>
            @if($emailDispatched)
                <p class="mt-4 text-gray-700">No worries&mdash;we just sent a fresh dashboard link to <strong>{{ $email }}</strong>. Check your inbox (and spam folder) to pick up where you left off.</p>
            @else
                <p class="mt-4 text-gray-700">We tried to send a new dashboard link to <strong>{{ $email }}</strong>, but something went wrong. Please request another email from the homepage or contact support.</p>
            @endif
            <p class="mt-6 text-sm text-gray-500">Once you have the new email, use the button inside to access your Housing Alerts dashboard.</p>
            <a href="/" class="inline-flex mt-8 items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">Return to Housing Alerts</a>
        </div>
    </div>
</x-guest-layout>
