<x-guest-layout>
    <div class="max-w-lg mx-auto py-12">
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">You already have an account</h1>
            @if($emailDispatched)
                <p class="mt-4 text-gray-700">We sent a fresh dashboard link to <strong>{{ $email }}</strong>. Check your inbox (and spam folder) to continue configuring your housing alerts.</p>
            @else
                <p class="mt-4 text-gray-700">We tried to send a dashboard link to <strong>{{ $email }}</strong>, but something went wrong. Please try again in a few minutes or contact support.</p>
            @endif
            <p class="mt-6 text-sm text-gray-500">Once you have the email, click the button inside to access your dashboard and manage your subscriptions.</p>
            <a href="/" class="inline-flex mt-8 items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">Return to Housing Alerts</a>
        </div>
    </div>
</x-guest-layout>
