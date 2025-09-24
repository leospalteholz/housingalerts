<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Housing Alerts</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100">
            <header class="bg-white shadow">
                <div class="container mx-auto px-4 py-6 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <!-- Housing Alerts Logo -->
                        <img src="{{ asset('images/housing-alerts-logo.svg') }}" alt="Housing Alerts Logo" class="w-10 h-10 text-blue-600">
                        <div class="text-2xl font-bold text-gray-800">Housing Alerts</div>
                    </div>
                    <div>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline-none focus:underline">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline-none focus:underline">Admin Login</a>
                        @endauth
                    </div>
                </div>
            </header>
            
            <main class="flex-grow">
                <div class="container mx-auto px-4 py-12">
                    <div class="text-center mb-8">
                        <h1 class="text-5xl font-bold text-gray-900 mb-6">Help Advocate for More Housing</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-2">It's hard for your councillors to support housing when all they hear is negative voices, but we know it's nearly impossible to keep up with opportunities to support housing in your community.</p>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-6">Housing Alerts sends you emails about hearings and tells you exactly how you can make your voice heard for more housing.</p>
                    </div>

                    <!-- Simple Email Signup -->
                    <div class="max-w-2xl mx-auto mb-12">
                        <form action="{{ route('signup.passwordless') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <span class="block text-3xl font-bold text-gray-800 mb-2">Sign me up!</span>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       required 
                                       placeholder="Enter your email address"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg whitespace-nowrap transition duration-200">
                                Subscribe
                            </button>
                        </form>
                        
                        @if(session('success'))
                            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="bg-white shadow-md rounded-lg p-8 mb-12">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <div class="bg-blue-100 rounded-full p-4 w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="organization" class="h-10 w-10 text-blue-500" />
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Choose Your Organization</h3>
                                <p class="text-gray-600">Select the organization relevant to your community.</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-green-100 rounded-full p-4 w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="location" class="h-10 w-10 text-green-500" />
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Select Regions</h3>
                                <p class="text-gray-600">Pick the specific regions you want to monitor for housing hearings.</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-amber-100 rounded-full p-4 w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="notification" class="h-10 w-10 text-amber-500" />
                                </div>
                                <h3 class="text-xl font-semibold mb-2">Get Notifications</h3>
                                <p class="text-gray-600">Receive timely emails when new homes could use your support!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <footer class="bg-white shadow-inner py-8">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <p class="text-gray-600">&copy; {{ date('Y') }} Housing Alerts. All rights reserved.</p>
                        </div>
                        <div>
                            <a href="#" class="text-gray-600 hover:text-gray-900 mr-4">Privacy Policy</a>
                            <a href="#" class="text-gray-600 hover:text-gray-900">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
