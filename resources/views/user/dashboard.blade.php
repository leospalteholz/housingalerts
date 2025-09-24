<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Welcome Section -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">
                    Welcome {{ auth()->user()->name }}
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Thank you for signing up to help support housing in your community!
                </p>
            </div>

            <!-- Success Messages -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="check-circle" class="h-5 w-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Email Verification Status -->
            @if(!auth()->user()->hasVerifiedEmail())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="exclamation-triangle" class="h-5 w-5 text-yellow-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>You won't receive any hearing notifications until you verify your email.</strong></br> 
                                Check your inbox and click the link to verify your account.
                            </p>
                            <div class="mt-2">
                                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm bg-yellow-100 text-yellow-800 px-3 py-1 rounded hover:bg-yellow-200 transition">
                                        Resend Verification Email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Unsubscribed Status -->
            @if(auth()->user()->unsubscribed_at)
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-icon name="exclamation-triangle" class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <strong>You have unsubscribed from all notifications.</strong><br> 
                                You won't receive any hearing notifications until you resubscribe.
                            </p>
                            <div class="mt-2">
                                <form method="POST" action="{{ route('user.resubscribe') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm bg-red-100 text-red-800 px-3 py-1 rounded hover:bg-red-200 transition">
                                        Resubscribe to Notifications
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Region Subscription Management -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Select the regions you're interested in</h2>
                            <p class="text-sm text-gray-600 mt-1">You'll be notified about housing hearings in these regions.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Success/Error Messages -->
                <div id="subscription-message" class="hidden mx-6 mt-4 p-4 rounded-md"></div>
                
                <div class="px-6 py-4">
                    @if($allRegions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($allRegions as $region)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       class="region-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                                       data-region-id="{{ $region->id }}"
                                                       data-region-name="{{ $region->name }}"
                                                       {{ $region->is_monitored ? 'checked' : '' }}>
                                                <span class="sr-only">Subscribe to {{ $region->name }}</span>
                                            </label>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-medium text-gray-900">{{ $region->name }}</h3>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $region->hearings->count() }} hearings
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">{{ $region->organization->name }}</p>
                                            @if($region->description)
                                                <p class="text-xs text-gray-600 mt-2">{{ Str::limit($region->description, 120) }}</p>
                                            @endif
                                            
                                            <!-- Subscription Status -->
                                            <div class="mt-2">
                                                <span class="subscription-status inline-flex items-center text-xs" data-region-id="{{ $region->id }}">
                                                    @if($region->is_monitored)
                                                        <x-icon name="check-circle" class="w-3 h-3 mr-1 text-green-500" />
                                                        <span class="text-green-700 font-medium">Subscribed</span>
                                                    @else
                                                        <x-icon name="plus" class="w-3 h-3 mr-1 text-gray-400" />
                                                        <span class="text-gray-500">Not subscribed</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-icon name="location" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No regions available</h3>
                            <p class="text-gray-600">There are no regions available for subscription in your organization yet.</p>
                        </div>
                    @endif

                    <!-- Notification Preferences (always shown) -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Notification Preferences</h2>
                            <p class="text-sm text-gray-600">Choose which types of hearings you want to be notified about</p>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="notify_development_hearings"
                                       class="notification-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                       data-setting="notify_development_hearings"
                                       {{ $notificationSettings->notify_development_hearings ? 'checked' : '' }}>
                                <label for="notify_development_hearings" class="ml-3 text-sm text-gray-700">
                                    <span class="font-medium">New developments</span>
                                    <p class="text-xs text-gray-500">Get notified about new housing development proposals</p>
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="notify_policy_hearings"
                                       class="notification-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                       data-setting="notify_policy_hearings"
                                       {{ $notificationSettings->notify_policy_hearings ? 'checked' : '' }}>
                                <label for="notify_policy_hearings" class="ml-3 text-sm text-gray-700">
                                    <span class="font-medium">Important housing policy</span>
                                    <p class="text-xs text-gray-500">Get notified about housing policy changes and discussions</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Hearings Section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Hearings in Your Regions</h2>
                        <p class="text-sm text-gray-600 mt-1">Housing development and policy hearings you should know about</p>
                    </div>
                </div>
                <div id="hearings-content">
                    @include('user.partials.hearings-list')
                </div>
            </div>

            
            <div class="text-center">
                <p class="text-sm text-gray-600">Hearing tracking is provided by:</p>
            </div>
            <!-- Organization Information Card -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-6">
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                        {{ auth()->user()->organization->name }}
                    </h2>
                    
                    @if(auth()->user()->organization->about)
                        <div class="text-gray-700 leading-relaxed mb-6 text-center">
                            {{ auth()->user()->organization->about }}
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                        <span class="text-sm text-gray-600">Want to learn more?</span>
                        <div class="flex flex-wrap justify-center gap-4">
                            @if(auth()->user()->organization->contact_email)
                                <a href="mailto:{{ auth()->user()->organization->contact_email }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                                    <x-icon name="mail" class="w-4 h-4 mr-2" />
                                    Contact Us
                                </a>
                            @endif
                            @if(auth()->user()->organization->website_url)
                                <a href="{{ auth()->user()->organization->website_url }}" target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                                    <x-icon name="external-link" class="w-4 h-4 mr-2" />
                                    Visit Website
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for handling region subscriptions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.region-checkbox');
            const messageDiv = document.getElementById('subscription-message');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const regionId = this.dataset.regionId;
                    const regionName = this.dataset.regionName;
                    const isChecked = this.checked;
                    
                    // Disable checkbox during request
                    this.disabled = true;
                    
                    const baseUrl = '{{ url('/') }}';
                    const url = isChecked 
                        ? `${baseUrl}/regions/${regionId}/subscribe`
                        : `${baseUrl}/regions/${regionId}/unsubscribe`;
                    
                    const method = 'POST'; // Use POST for both subscribe and unsubscribe
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update status indicator
                            updateSubscriptionStatus(regionId, isChecked);
                            
                            // Show success message
                            showMessage(data.message, 'success');
                            
                            // Reload hearings section
                            reloadHearingsSection();
                        } else {
                            // Revert checkbox if failed
                            this.checked = !isChecked;
                            showMessage(data.error || 'An error occurred', 'error');
                        }
                    })
                    .catch(error => {
                        // Revert checkbox if failed
                        this.checked = !isChecked;
                        showMessage(`Error: ${error.message}`, 'error');
                    })
                    .finally(() => {
                        // Re-enable checkbox
                        this.disabled = false;
                    });
                });
            });
            
            // Handle notification preferences
            const notificationCheckboxes = document.querySelectorAll('.notification-checkbox');
            
            notificationCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const setting = this.dataset.setting;
                    const isChecked = this.checked;
                    
                    // Disable checkbox during request
                    this.disabled = true;
                    
                    const baseUrl = '{{ url('/') }}';
                    const url = `${baseUrl}/user/notification-preferences`;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    
                    // Add both settings to maintain their current state
                    const developmentCheckbox = document.querySelector('[data-setting="notify_development_hearings"]');
                    const policyCheckbox = document.querySelector('[data-setting="notify_policy_hearings"]');
                    
                    if (developmentCheckbox && developmentCheckbox.checked) {
                        formData.append('notify_development_hearings', '1');
                    }
                    if (policyCheckbox && policyCheckbox.checked) {
                        formData.append('notify_policy_hearings', '1');
                    }
                    
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showMessage(data.message, 'success');
                        } else {
                            // Revert checkbox if failed
                            this.checked = !isChecked;
                            showMessage(data.error || 'An error occurred updating preferences', 'error');
                        }
                    })
                    .catch(error => {
                        // Revert checkbox if failed
                        this.checked = !isChecked;
                        showMessage(`Error: ${error.message}`, 'error');
                    })
                    .finally(() => {
                        // Re-enable checkbox
                        this.disabled = false;
                    });
                });
            });
            
            function updateSubscriptionStatus(regionId, isSubscribed) {
                const statusElement = document.querySelector(`[data-region-id="${regionId}"].subscription-status`);
                
                if (isSubscribed) {
                    statusElement.innerHTML = `
                        <x-icon name="check-circle" class="w-3 h-3 mr-1 text-green-500" />
                        <span class="text-green-700 font-medium">Subscribed</span>
                    `;
                } else {
                    statusElement.innerHTML = `
                        <x-icon name="plus" class="w-3 h-3 mr-1 text-gray-400" />
                        <span class="text-gray-500">Not subscribed</span>
                    `;
                }
            }
            
            function reloadHearingsSection() {
                const hearingsContent = document.getElementById('hearings-content');
                
                if (hearingsContent) {
                    // Show loading state
                    hearingsContent.innerHTML = `
                        <div class="px-6 py-12 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Updating hearings...</p>
                        </div>
                    `;
                    
                    // Fetch updated hearings
                    const baseUrl = '{{ url('/') }}';
                    fetch(`${baseUrl}/user/hearings`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        hearingsContent.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error reloading hearings:', error);
                        hearingsContent.innerHTML = `
                            <div class="px-6 py-12 text-center">
                                <p class="text-red-600">Error loading hearings. Please refresh the page.</p>
                            </div>
                        `;
                    });
                }
            }
            
            function showMessage(message, type) {
                const bgClass = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                const textClass = type === 'success' ? 'text-green-800' : 'text-red-800';
                const iconClass = type === 'success' ? 'text-green-400' : 'text-red-400';
                
                const icon = type === 'success' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>';
                
                messageDiv.className = `border rounded-md p-4 ${bgClass}`;
                messageDiv.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 ${iconClass}" viewBox="0 0 20 20" fill="currentColor">
                                ${icon}
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm ${textClass}">${message}</p>
                        </div>
                    </div>
                `;
                messageDiv.classList.remove('hidden');
                
                // Auto-hide success messages after 3 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        messageDiv.classList.add('hidden');
                    }, 3000);
                }
            }
        });
    </script>
    <script src="{{ asset('js/calendar-button.js') }}"></script>
</x-app-layout>
