<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Notification Settings') }}</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage how and when you receive hearing notifications</p>
                </div>

                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('notification-settings.update') }}">
                        @csrf

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Hearing Notifications</h3>
                            <p class="text-gray-600 mb-4">Choose which types of hearings you'd like to receive notifications about.</p>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" 
                                               type="checkbox" 
                                               name="notify_development_hearings" 
                                               id="notify_development_hearings"
                                               value="1"
                                               {{ $settings->notify_development_hearings ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="notify_development_hearings" class="font-medium text-gray-700">
                                            Development Hearings
                                        </label>
                                        <p class="text-gray-500">
                                            Get notified about new housing development hearings in your subscribed regions
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" 
                                               type="checkbox" 
                                               name="notify_policy_hearings" 
                                               id="notify_policy_hearings"
                                               value="1"
                                               {{ $settings->notify_policy_hearings ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="notify_policy_hearings" class="font-medium text-gray-700">
                                            Policy Hearings
                                        </label>
                                        <p class="text-gray-500">
                                            Get notified about housing policy hearings in your subscribed regions
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Reminder Settings</h3>
                            <p class="text-gray-600 mb-4">Configure when you'd like to receive reminders.</p>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" 
                                           type="checkbox" 
                                           name="send_day_of_reminders" 
                                           id="send_day_of_reminders"
                                           value="1"
                                           {{ $settings->send_day_of_reminders ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="send_day_of_reminders" class="font-medium text-gray-700">
                                        Day-of Reminders
                                    </label>
                                    <p class="text-gray-500">
                                        Get reminder emails on the day of hearings you're interested in
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('user.dashboard') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back to Dashboard
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-6 mt-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">About Notifications</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>
                                You'll only receive notifications for hearings in regions you've subscribed to. 
                                You can manage your region subscriptions from your 
                                <a href="{{ route('user.dashboard') }}" class="font-medium underline">dashboard</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
