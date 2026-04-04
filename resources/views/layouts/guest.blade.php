<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php
            $faviconPath = \App\Models\SiteSetting::get('favicon_path', '');
        @endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @if (!empty($faviconPath))
            <link rel="icon" href="{{ \Illuminate\Support\Facades\Storage::url($faviconPath) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $immersiveAuthPage = request()->routeIs('register') || request()->routeIs('login');
    @endphp

    <body class="font-sans text-gray-900 antialiased bg-slate-50">
        @if ($immersiveAuthPage)
            {{ $slot }}
        @else
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
                <div class="mb-6">
                    <a href="/" wire:navigate>
                        <x-application-logo class="h-16 w-auto" />
                    </a>
                </div>

                <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
