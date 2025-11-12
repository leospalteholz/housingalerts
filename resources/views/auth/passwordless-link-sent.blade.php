<x-guest-layout>
    <div class="max-w-lg mx-auto py-12">
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">Check your inbox</h1>
            @if($emailDispatched)
                <p class="mt-4 text-gray-700">We just sent a dashboard link to <strong>{{ $email }}</strong>. Open the email and click the button inside to access your Housing Alerts dashboard.</p>
            @else
                <p class="mt-4 text-gray-700">We tried to send a dashboard link to <strong>{{ $email }}</strong>, but something went wrong. Please try again in a few minutes or contact support.</p>
            @endif
            <a href="/" class="inline-flex mt-8 items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">Return to Housing Alerts</a>
        </div>
    </div>
</x-guest-layout>
