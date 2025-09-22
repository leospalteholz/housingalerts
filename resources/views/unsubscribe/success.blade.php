<x-guest-layout>
    <div class="max-w-md mx-auto">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">You've Been Unsubscribed</h1>
            
            <div class="mb-6">
                <p class="text-gray-600 mb-2">We've successfully unsubscribed:</p>
                <p class="font-semibold text-gray-900">{{ $user->email }}</p>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <p class="text-sm text-green-800">
                    You will no longer receive housing alert emails. We're sorry to see you go!
                </p>
            </div>
            
            <div class="space-y-4">
                <p class="text-gray-600">
                    If you change your mind, you can always re-subscribe to start receiving housing alerts again.
                </p>
                
                <a href="{{ route('signup') }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                    Re-subscribe to Housing Alerts
                </a>
            </div>
            
            <div class="mt-6">
                <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Return to homepage
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>