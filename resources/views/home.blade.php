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
                            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline-none focus:underline">Log in</a>
                            <a href="{{ route('signup') }}" class="ml-4 font-semibold bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Sign up</a>
                        @endauth
                    </div>
                </div>
            </header>
            
            <main class="flex-grow">
                <div class="container mx-auto px-4 py-12">
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">Stay Informed About Housing Hearings</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">It's hard for your councillors to support housing when all they hear is voices against.</p>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">But we know with life and work it's nearly impossible to keep up with public hearings or opportunities to support housing in your community.</p>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Housing Alerts allows you to receive timely notifications about new homes and housing issues in your area so you can take action, whether it's sending a quick supportive email (great!) or speaking in support at a council meeting (incredible!).</p>
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
                    
                    <div class="text-center">
                        <a href="{{ route('signup') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg">Sign Up Now</a>
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
