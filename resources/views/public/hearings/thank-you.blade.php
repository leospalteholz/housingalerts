<x-guest-layout>
    <div class="max-w-4xl mx-auto py-16 px-0 lg:px-10 text-center">
        <div class="bg-white shadow-xl rounded-none sm:rounded-2xl py-12 px-6 sm:p-10 lg:p-14">
            <div class="mx-auto h-16 w-16 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 class="mt-8 text-3xl lg:text-4xl font-bold text-gray-900">Thank you for supporting {{ $organization->name }}!</h1>
            <p class="mt-5 text-lg text-gray-600 max-w-2xl mx-auto">Your hearing submission has been received. Our team will review the details shortly and publish it once approved.</p>

            @if (session('status'))
                <p class="mt-6 text-base font-medium text-gray-700">{{ session('status') }}</p>
            @endif

            <div class="mt-10">
                <a href="{{ route('public.hearings.submit', ['organization' => $organization->slug]) }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700">
                    Submit another hearing
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
