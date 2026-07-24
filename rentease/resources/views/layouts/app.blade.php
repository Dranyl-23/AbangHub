<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('darkMode') === 'true', logoutModalOpen: false }" x-init="$watch('dark', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val) })" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'AbangHub') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg?v=4') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-300">
    <div class="min-h-screen flex flex-col">
        <!-- Header / Navigation -->
        <header class="bg-white dark:bg-slate-800 shadow-sm sticky top-0 z-50">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-16 xl:px-24">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                                <x-application-logo class="w-8 h-8 text-rose-600" />
                                <span class="text-xl font-bold text-rose-600">AbangHub</span>
                            </a>
                        </div>


                    </div>

                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button @click="dark = !dark" class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700 transition-colors">
                            <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </button>

                        <!-- Authentication Links -->
                        @auth
                            <!-- Profile Dropdown (Airbnb Style) -->
                            @php
                                $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->unread()->count();
                            @endphp
                            <div x-data="{ open: false }" class="relative z-50">
                                <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2.5 pl-3.5 pr-1.5 py-1.5 rounded-full border border-slate-300 dark:border-slate-600 hover:shadow-md transition-all duration-200 bg-white dark:bg-slate-800 focus:outline-none">
                                    <!-- Hamburger Icon -->
                                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    
                                    <!-- Profile Picture / Avatar -->
                                    <div class="w-7 h-7 rounded-full overflow-hidden bg-slate-200 relative">
                                        @if(Auth::user()->profile_image)
                                            <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name ?? Auth::user()->username) }}&background=f43f5e&color=fff" class="w-full h-full object-cover">
                                        @endif
                                        <!-- Notification dot if needed -->
                                        @if($unreadCount > 0)
                                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-800"></span>
                                        @endif
                                    </div>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-3 w-64 rounded-2xl shadow-xl py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none" style="display: none;">
                                    
                                    <!-- User Info Header (Mobile/Tablet visible, optional on desktop) -->
                                    <a href="{{ Auth::user()->user_type === 'landlord' ? route('host.show', Auth::user()) : route('profile.edit') }}" class="block px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 mb-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ Auth::user()->full_name ?? Auth::user()->username }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ ucfirst(Auth::user()->user_type) }} Account</p>
                                    </a>

                                    <!-- Primary Links -->
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Dashboard</a>
                                    <a href="{{ route('messages.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium flex justify-between items-center">
                                        Messages
                                        @if($unreadCount > 0)
                                            <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }} New</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('properties.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Properties</a>
                                    @if(Auth::user()->user_type === 'tenant')
                                        <a href="{{ route('tenant.applications.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium flex justify-between items-center">
                                            My Applications
                                            @php
                                                $pendingApps = \App\Models\Application::where('user_id', Auth::id())->where('status', 'pending')->count();
                                            @endphp
                                            @if($pendingApps > 0)
                                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingApps }} Pending</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('tenant.invoices.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium flex justify-between items-center">
                                            My Invoices
                                            @php
                                                $pendingInvoices = \App\Models\Invoice::whereHas('lease', function($q) { $q->where('tenant_id', Auth::id()); })->where('status', 'pending')->count();
                                            @endphp
                                            @if($pendingInvoices > 0)
                                                <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingInvoices }} Due</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('tenant.favorites.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">My Favorites</a>
                                        <a href="{{ route('tenant.maintenance.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Maintenance Requests</a>
                                    @endif
                                    @if(Auth::user()->user_type !== 'admin')
                                        <a href="{{ route('transactions.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Transactions</a>
                                    @endif
                                    @if(Auth::user()->user_type === 'landlord')
                                        <a href="{{ route('wallet.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium flex justify-between items-center">
                                            My Wallet
                                            <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-full">₱{{ number_format(Auth::user()->wallet->balance ?? 0, 0) }}</span>
                                        </a>
                                        <a href="{{ route('reports.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Reports</a>
                                        <a href="{{ route('landlord.compliance.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Compliance Hub</a>
                                        <a href="{{ route('landlord.maintenance.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Maintenance Requests</a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin')
                                        <a href="{{ route('admin.users.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Manage Users</a>
                                        <a href="{{ route('admin.properties.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">Moderate Properties</a>
                                        <a href="{{ route('log-viewer.index') }}" target="_blank" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-medium">System Logs</a>
                                    @endif
                                    
                                    <div class="my-2 border-t border-slate-100 dark:border-slate-700/50"></div>
                                    
                                    <!-- Secondary Links -->
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">Account settings</a>
                                    <a href="{{ route('help.index') }}" class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">Help Center</a>
                                    
                                    <div class="my-2 border-t border-slate-100 dark:border-slate-700/50"></div>
                                    
                                    <!-- Logout -->
                                    <button type="button" @click="logoutModalOpen = true; open = false" class="block w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                        Log out
                                    </button>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 mr-4">Log in</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700">Sign up</a>
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden" x-data="{ mobileMenuOpen: false }">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Mobile Navigation -->
                        <div x-show="mobileMenuOpen" @click.outside="mobileMenuOpen = false" class="absolute top-16 left-0 right-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-lg" style="display: none;">
                            <div class="px-2 pt-2 pb-3 space-y-1">
                                <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('properties.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Properties</a>
                                @auth
                                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Dashboard</a>
                                    <a href="{{ route('messages.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('messages.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Messages</a>
                                    @if(Auth::user()->user_type === 'tenant')
                                        <a href="{{ route('tenant.applications.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.applications.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">My Applications</a>
                                        <a href="{{ route('tenant.invoices.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.invoices.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">My Invoices</a>
                                        <a href="{{ route('tenant.favorites.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.favorites.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">My Favorites</a>
                                        <a href="{{ route('tenant.maintenance.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tenant.maintenance.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Maintenance Requests</a>
                                    @endif
                                    @if(Auth::user()->user_type !== 'admin')
                                        <a href="{{ route('transactions.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('transactions.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Transactions</a>
                                    @endif
                                    @if(Auth::user()->user_type === 'landlord')
                                        <a href="{{ route('wallet.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('wallet.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">My Wallet</a>
                                        <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('reports.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Reports</a>
                                        <a href="{{ route('landlord.compliance.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('landlord.compliance.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Compliance Hub</a>
                                        <a href="{{ route('landlord.maintenance.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('landlord.maintenance.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Maintenance Requests</a>
                                    @endif
                                    @if(Auth::user()->user_type === 'admin')
                                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.users.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Manage Users</a>
                                        <a href="{{ route('admin.properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.properties.*') ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400' : 'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700' }}">Moderate Properties</a>
                                        <a href="{{ route('log-viewer.index') }}" target="_blank" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700">System Logs</a>
                                    @endif
                                    <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
                                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700">Profile</a>
                                    <button type="button" @click="logoutModalOpen = true; mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">Log Out</button>
                                @else
                                    <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
                                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700">Log in</a>
                                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700">Sign up</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-grow max-w-[1920px] w-full mx-auto py-6 px-4 sm:px-8 lg:px-16 xl:px-24">
            {{ $slot }}
        </main>

        <!-- Flash Messages / Toasts -->
        @if (session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed bottom-4 right-4 z-50 flex items-center bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="ml-4 text-emerald-100 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed bottom-4 right-4 z-50 flex items-center bg-rose-500 text-white px-6 py-3 rounded-xl shadow-lg">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="ml-4 text-rose-100 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
        
        <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-auto">
            <div class="max-w-[1920px] mx-auto py-4 px-4 sm:px-8 lg:px-16 xl:px-24 flex items-center justify-between">
                <p class="text-sm text-slate-500 dark:text-slate-400">&copy; {{ date('Y') }} AbangHub. All rights reserved.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-sm text-slate-500 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400">Privacy Policy</a>
                    <a href="#" class="text-sm text-slate-500 hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-400">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- Logout Modal -->
    <div x-show="logoutModalOpen" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="logoutModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="logoutModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="logoutModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">
                                Log out of AbangHub
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Are you sure you want to log out? You will need to enter your credentials again to access your account.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200 dark:border-slate-700">
                    <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Yes, log out
                        </button>
                    </form>
                    <button type="button" @click="logoutModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>





