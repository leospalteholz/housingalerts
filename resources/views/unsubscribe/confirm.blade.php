<x-guest-layout>
    <div class="max-w-md mx-auto">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Unsubscribe from All Notifications</h1>
            
            <div class="mb-6">
                <p class="text-gray-600 mb-2">You are about to unsubscribe:</p>
                <p class="font-semibold text-gray-900">{{ $user->email }}</p>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            This will unsubscribe you from ALL email notifications
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>You will no longer receive:</p>
                            <ul class="list-disc pl-5 mt-1">
                                <li>New hearing notifications</li>
                                <li>Day-of reminders</li>
                                <li>All other housing alerts</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ request()->fullUrl() }}" class="space-y-4">
                @csrf
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Yes, Unsubscribe Me
                    </button>
                    
                    <a href="{{ url('/') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md text-center transition duration-150 ease-in-out">
                        Cancel
                    </a>
                </div>
            </form>
            
            <div class="mt-6 text-sm text-gray-500">
                <p>You can always re-subscribe by <a href="{{ route('signup') }}" class="text-blue-600 hover:text-blue-800">signing up again</a>.</p>
            </div>
        </div>
    </div>
</x-guest-layout>