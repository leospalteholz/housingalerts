<x-guest-layout>
    <div class="max-w-md mx-auto">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Already Unsubscribed</h1>
            
            <div class="mb-6">
                <p class="text-gray-600 mb-2">The email address:</p>
                <p class="font-semibold text-gray-900">{{ $subscriber->email }}</p>
                <p class="text-gray-600 mt-2">is already unsubscribed from all housing alerts.</p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                <p class="text-sm text-blue-800">
                    You are not currently receiving any email notifications from Housing Alerts.
                </p>
            </div>
            
            <div class="space-y-4">
                <p class="text-gray-600">
                    If you'd like to start receiving housing alerts again, you can re-subscribe.
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