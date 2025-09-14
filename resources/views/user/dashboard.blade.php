<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Welcome Section -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">
                    Welcome {{ auth()->user()->name }}!
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Thank you for signing up to help support housing in your community. Hearing tracking is provided by:
                </p>
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
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Contact Us
                                </a>
                            @endif
                            @if(auth()->user()->organization->website_url)
                                <a href="{{ auth()->user()->organization->website_url }}" target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Visit Website
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Email Verification Status -->
            @if(!auth()->user()->hasVerifiedEmail())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Please verify your email address.</strong> 
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

            <!-- Upcoming Hearings Section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Upcoming Hearings in Your Regions</h2>
                    <p class="text-sm text-gray-600 mt-1">Housing development and policy hearings you should know about</p>
                </div>
                @if($upcomingHearings->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($upcomingHearings->take(5) as $hearing)
                            <div class="px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                                            {{ $hearing->display_title }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-2">
                                            @if($hearing->start_date)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($hearing->start_date)->format('M j, Y \a\t g:i A') }}
                                                </div>
                                            @endif
                                            @if($hearing->region)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    </svg>
                                                    {{ $hearing->region->name }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->isDevelopment() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $hearing->isDevelopment() ? 'Development' : 'Policy' }}
                                            </span>
                                            @if($hearing->isDevelopment() && $hearing->units)
                                                <span class="text-sm text-gray-600">{{ $hearing->units }} units</span>
                                            @endif
                                            @if($hearing->isDevelopment() && $hearing->rental !== null)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $hearing->rental ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ $hearing->rental ? 'Rental' : 'Condo' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('hearings.show', $hearing) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($upcomingHearings->count() > 5)
                        <div class="px-6 py-4 bg-gray-50 text-center">
                            <a href="{{ route('hearings.index') }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                View all {{ $upcomingHearings->count() }} upcoming hearings →
                            </a>
                        </div>
                    @endif
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming hearings</h3>
                        @if($monitoredRegions->count() > 0)
                            <p class="text-gray-600 mb-4">There are no upcoming hearings in your monitored regions.</p>
                        @else
                            <p class="text-gray-600 mb-4">Start monitoring regions to see upcoming hearings here.</p>
                            <a href="{{ route('regions.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                                Browse Regions
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Region Subscription Management -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Manage Your Region Subscriptions</h2>
                            <p class="text-sm text-gray-600 mt-1">Select regions to monitor for housing developments and policy changes</p>
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
                                                        <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-green-700 font-medium">Subscribed</span>
                                                    @else
                                                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
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
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No regions available</h3>
                            <p class="text-gray-600">There are no regions available for subscription in your organization yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 text-center">What would you like to do?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('hearings.index') }}" 
                       class="group bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center">
                        <div class="text-blue-600 mb-2">
                            <svg class="h-8 w-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900 mb-1">View All Hearings</h3>
                        <p class="text-sm text-gray-600">See all development and policy hearings</p>
                    </a>

                    <a href="{{ route('regions.index') }}" 
                       class="group bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center">
                        <div class="text-green-600 mb-2">
                            <svg class="h-8 w-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900 mb-1">Manage Regions</h3>
                        <p class="text-sm text-gray-600">Add or remove monitored regions</p>
                    </a>

                    <a href="{{ route('profile.edit') }}" 
                       class="group bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition text-center">
                        <div class="text-purple-600 mb-2">
                            <svg class="h-8 w-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900 mb-1">Update Profile</h3>
                        <p class="text-sm text-gray-600">Manage your account settings</p>
                    </a>
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
                    
                    const url = isChecked 
                        ? `/regions/${regionId}/subscribe`
                        : `/regions/${regionId}/unsubscribe`;
                    
                    const method = isChecked ? 'POST' : 'DELETE';
                    
                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update status indicator
                            updateSubscriptionStatus(regionId, isChecked);
                            
                            // Show success message
                            showMessage(data.message, 'success');
                        } else {
                            // Revert checkbox if failed
                            this.checked = !isChecked;
                            showMessage(data.error || 'An error occurred', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert checkbox if failed
                        this.checked = !isChecked;
                        showMessage('An error occurred while updating your subscription', 'error');
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
                        <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-green-700 font-medium">Subscribed</span>
                    `;
                } else {
                    statusElement.innerHTML = `
                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-gray-500">Not subscribed</span>
                    `;
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
</x-app-layout>
