<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'App'))</title>
    <!-- Vite: CSS -->
    @if (file_exists(public_path('mix-manifest.json')))
        <!-- fallback if mix is used -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @else
        @vite(['resources/css/app.css'])
    @endif

    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ sidebarCollapsed: (localStorage.getItem('sidebarCollapsed') === 'true'), mobileOpen: false }"
        x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))"
        class="min-h-screen flex"
        :class="sidebarCollapsed ? 'sidebar-collapsed' : ''">
        {{-- Left sidebar --}}
        @include('layouts.sidebar')

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- single page header will be used below (no extra topbar) --}}

            {{-- Page header (left: title, right: profile dropdown) --}}
            @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <!-- Mobile hamburger next to header title -->
                            <button @click="mobileOpen = true" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 md:hidden hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="flex-1">
                                @yield('header')
                            </div>
                        </div>

                        <div class="ms-4 flex items-center">
                            <!-- Profile dropdown placed in header -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                                        <div class="mr-3 text-right">
                                            <div class="text-xs text-gray-500">{{ Auth::user()->name ?? 'User' }}</div>
                                            <div class="text-xs text-gray-400">{{ Auth::user()->email ?? '' }}</div>
                                        </div>

                                        <div>
                                            @php
                                                $name = Auth::user()->name ?? 'U';
                                                $parts = preg_split('/\s+/', trim($name));
                                                $initials = '';
                                                foreach ($parts as $p) {
                                                    if ($p === '') continue;
                                                    $initials .= mb_strtoupper(mb_substr($p, 0, 1));
                                                    if (mb_strlen($initials) >= 2) break; // limit to 2 chars
                                                }
                                                if ($initials === '') { $initials = 'U'; }
                                            @endphp

                                            @if (!empty(Auth::user()->profile_photo_url))
                                                <img class="h-9 w-9 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ $initials }}" />
                                            @else
                                                <div class="h-9 w-9 rounded-full bg-gray-200 text-sm font-semibold flex items-center justify-center text-gray-700" aria-hidden="false" role="img" aria-label="{{ $initials }}">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="ms-2">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </header>
            @endif

            {{-- Main --}}
            <main class="flex-1">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Vite: JS --}}
    @if (file_exists(public_path('mix-manifest.json')))
        <script src="{{ mix('js/app.js') }}"></script>
    @else
        @vite(['resources/js/app.js'])
    @endif

    {{-- Alpine: simple CDN fallback if Alpine isn't bundled in app.js (keeps mobile toggle working) --}}
    <script>
        (function(){
            if (typeof Alpine === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js';
                s.defer = true;
                document.head.appendChild(s);
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
