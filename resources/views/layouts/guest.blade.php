<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-12 lg:py-16">
                @isset($header)
                    <div class="space-y-10">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                                <div class="flex items-start gap-4">
                                    <a href="{{ url('/') }}" class="inline-flex shrink-0">
                                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                                    </a>
                                    <div class="flex-1 text-gray-900">
                                        {{ $header }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{ $slot }}
                    </div>
                @else
                    <div class="flex justify-center">
                        <a href="{{ url('/') }}" class="inline-flex">
                            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                        </a>
                    </div>

                    <div class="mt-10">
                        {{ $slot }}
                    </div>
                @endisset
            </div>
        </div>
    </body>
</html>
