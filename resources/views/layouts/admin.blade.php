<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen bg-purple-100 flex">

            {{-- Sidebar --}}
            <aside
                class="fixed inset-y-0 left-0 z-30 w-64 bg-indigo-900 text-white flex flex-col transform transition-transform duration-200 ease-in-out md:relative md:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                {{-- Overlay for mobile --}}
                <div
                    class="fixed inset-0 bg-black/50 md:hidden"
                    x-show="sidebarOpen"
                    x-transition.opacity
                    @click="sidebarOpen = false"
                ></div>

                {{-- Logo --}}
                <div class="h-16 flex items-center px-6 border-b border-indigo-700">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-white" />
                        <span class="text-lg font-bold">CleanSwift</span>
                    </a>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1" x-data="{ unread: 0 }" x-init="
                    let url = '{{ route('admin.notifications.index') }}';
                    let fetchCount = () => {
                        let headers = new Headers({ 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' });
                        fetch(url + '?count=1', { headers })
                            .then(r => r.json())
                            .then(d => { if (d.count !== undefined) unread = d.count; })
                            .catch(() => {});
                    };
                    fetchCount();
                    setInterval(fetchCount, 30000);
                ">
                    <x-admin-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">
                        Bookings
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        Users
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.staff-applications.index')" :active="request()->routeIs('admin.staff-applications.*')">
                        Staff apps
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">
                        Services
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions.*')">
                        Transactions
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')">
                        Reviews
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                        Reports
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.notifications.index')" :active="request()->routeIs('admin.notifications.*')">
                        <span class="flex items-center gap-2">
                            Notifications
                            <span x-show="unread > 0" style="display: none" class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full min-w-[1.25rem]" x-text="unread"></span>
                        </span>
                    </x-admin-nav-link>
                </nav>

                {{-- User footer --}}
                <div class="border-t border-indigo-700 p-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-semibold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-indigo-300 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-indigo-300 hover:text-white transition-colors" title="Log out">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('profile.edit') }}" class="text-xs text-indigo-300 hover:text-white transition-colors">Profile</a>
                    </div>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex-1 flex flex-col min-w-0">
                {{-- Top bar (mobile hamburger + user dropdown) --}}
                <header class="bg-white shadow-sm h-16 flex items-center px-4 lg:px-6">
                    <button class="md:hidden mr-3 text-gray-500 hover:text-gray-700" @click="sidebarOpen = !sidebarOpen">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ Auth::user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">{{ __('Log Out') }}</button>
                        </form>
                    </div>
                </header>

                {{-- Page Heading --}}
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
