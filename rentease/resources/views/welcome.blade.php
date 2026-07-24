<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AbangHub') }} - Find Your Perfect Home</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg?v=4') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 pb-16 md:pb-0" x-data="{ showLoginModal: {{ $errors->any() ? 'true' : 'false' }} }">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/50 dark:border-slate-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <x-application-logo class="w-8 h-8 text-rose-600 transition-transform group-hover:scale-110" />
                        <span class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Abang<span class="text-rose-600">Hub</span></span>
                    </a>
                </div>
                
                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#features" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">Features</a>
                    <a href="#listings" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">Listings</a>
                    
                    <div class="h-6 w-px bg-slate-200"></div>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">Dashboard</a>
                    @else
                        <button @click="showLoginModal = true" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 transition-all hover:shadow-md hover:-translate-y-0.5">
                            Log in
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-rose-50 to-slate-50 dark:from-slate-900 dark:to-slate-800 -z-10"></div>
        
        <!-- Decorative blobs -->
        <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full overflow-hidden -z-10 pointer-events-none">
            <div class="absolute -top-[20%] -right-[10%] w-[70%] h-[70%] rounded-full bg-rose-200/30 blur-3xl opacity-60 mix-blend-multiply"></div>
            <div class="absolute top-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-blue-200/30 blur-3xl opacity-60 mix-blend-multiply"></div>
        </div>

        <div class="w-full relative text-center px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-6">
                Find Your Perfect Home in <span class="text-rose-600 inline-block">Digos City</span>
            </h1>
            <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                The most trusted platform connecting tenants with quality boarding houses and apartments. Transparent, secure, and hassle-free.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="#listings" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-medium rounded-full text-white bg-rose-600 hover:bg-rose-700 shadow-sm hover:shadow-lg transition-all hover:-translate-y-0.5">
                    Browse Properties
                </a>
                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 border-2 border-slate-200 dark:border-slate-700 text-base font-medium rounded-full text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all">
                    List Your Property
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-rose-600 font-semibold tracking-wide uppercase">Why Choose AbangHub</h2>
                <p class="mt-2 text-3xl leading-8 font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                    A better way to rent and manage
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="relative p-8 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-shadow duration-300">
                    <div class="absolute -top-6 left-8 h-12 w-12 flex items-center justify-center rounded-xl bg-rose-600 text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900 dark:text-white mb-3">Easy Search</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Find the perfect boarding house or apartment with our intuitive search and filtering tools. See all details upfront.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="relative p-8 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-shadow duration-300">
                    <div class="absolute -top-6 left-8 h-12 w-12 flex items-center justify-center rounded-xl bg-rose-600 text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900 dark:text-white mb-3">Direct Communication</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Chat directly with landlords or tenants through our secure messaging system. No middleman needed.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="relative p-8 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-shadow duration-300">
                    <div class="absolute -top-6 left-8 h-12 w-12 flex items-center justify-center rounded-xl bg-rose-600 text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900 dark:text-white mb-3">Seamless Management</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Landlords can track leases, handle maintenance requests, and monitor revenue all in one powerful dashboard.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Section -->
    <div id="listings" class="py-16 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(isset($featuredProperties) && $featuredProperties->count() > 0)
                
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Featured Properties</h2>
                        <a href="{{ route('properties.index') }}" class="text-rose-600 font-semibold hover:text-rose-700">View all</a>
                    </div>
                    
                    <div class="relative w-full">
                        <!-- Horizontal Scroll Container -->
                        <div class="flex overflow-x-auto gap-6 pb-4 snap-x snap-mandatory [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                            @foreach($featuredProperties as $property)
                                <div @click="window.location.href = '{{ route('properties.show', $property) }}'" class="block group cursor-pointer shrink-0 snap-start" style="width: 280px; min-width: 280px;">
                                    <!-- Image container -->
                                    <div class="relative overflow-hidden rounded-xl bg-slate-200 mb-3" style="width: 100%; height: 260px;">
                                        @if($property->primaryImage)
                                            <img src="{{ asset($property->primaryImage->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Badge -->
                                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-semibold text-slate-900 shadow-sm">
                                            {{ str_replace('_', ' ', ucfirst($property->property_type)) }}
                                        </div>
                                        
                                        <!-- Heart Icon -->
                                        @if(auth()->check() && auth()->user()->user_type === 'tenant')
                                            <button type="button" @click.stop.prevent="
                                                fetch('{{ route('properties.favorite', $property) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    }
                                                }).then(res => res.json()).then(data => {
                                                    isFav = (data.status === 'added');
                                                })"
                                                class="absolute top-3 right-3 z-10 text-white hover:scale-110 transition-transform focus:outline-none"
                                                x-data="{ isFav: {{ auth()->user()->favorites->contains($property->id) ? 'true' : 'false' }} }">
                                                <svg class="w-7 h-7 drop-shadow-md transition-colors" :class="isFav ? 'fill-rose-500 stroke-rose-500' : 'fill-black/50 stroke-white'" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" @click.stop.prevent="window.location.href = '{{ route('login') }}'" class="absolute top-3 right-3 z-10 text-white hover:scale-110 transition-transform focus:outline-none">
                                                <svg class="w-7 h-7 drop-shadow-md fill-black/50 stroke-white" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Content (Airbnb Style) -->
                                    <div class="flex justify-between items-start mt-3">
                                        <div class="font-medium text-base text-slate-900" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $property->city }}
                                        </div>
                                        @if($property->review_count > 0)
                                            <div class="flex items-center gap-1 text-sm text-slate-900 shrink-0 ml-2">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                <span>{{ number_format($property->average_rating, 1) }}</span>
                                            </div>
                                        @else
                                            <div class="text-sm text-slate-900 shrink-0 font-medium ml-2">New</div>
                                        @endif
                                    </div>
                                    <div class="text-sm text-slate-500 mt-0.5" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $property->title }}
                                    </div>
                                    <div class="text-sm text-slate-500 mt-0.5" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $property->bedrooms }} beds · {{ $property->bathrooms }} baths
                                    </div>
                                    <div class="mt-1.5 flex items-baseline gap-1">
                                        <span class="text-base font-semibold text-slate-900">₱{{ number_format($property->monthly_rent, 0) }}</span>
                                        <span class="text-sm text-slate-900">/ mo</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <h3 class="mt-4 text-sm font-semibold text-slate-900">No properties available yet</h3>
                    <p class="mt-1 text-sm text-slate-500">Landlords are preparing to list their amazing properties.</p>
                    <div class="mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-rose-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Be the first to list a property
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start items-center gap-2 mb-6 md:mb-0">
                    <x-application-logo class="w-6 h-6 text-rose-600" />
                    <span class="text-xl font-bold text-slate-900 dark:text-white">Abang<span class="text-rose-600">Hub</span></span>
                </div>
                <div class="flex justify-center space-x-6 md:order-2">
                    <p class="text-base text-slate-400">
                        &copy; {{ date('Y') }} AbangHub, Inc. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="relative z-[100]" x-cloak x-show="showLoginModal" style="display: none;">
        <!-- Modal Backdrop -->
        <div x-show="showLoginModal" 
             x-transition:enter="transition-opacity ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
             
        <!-- Modal Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <!-- Modal Panel -->
                <div x-show="showLoginModal" @click.away="showLoginModal = false"
                     x-transition:enter="transition-all ease-out duration-300 transform" 
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="transition-all ease-in duration-200 transform" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" 
                     class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl sm:my-8 border border-slate-200 dark:border-slate-700">
                    
                    <!-- Close Button -->
                    <button @click="showLoginModal = false" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors z-20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <x-application-logo class="w-10 h-10 text-rose-600 mx-auto mb-4" />
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Welcome back</h2>
                            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Log in to continue to AbangHub.</p>
                        </div>
                        
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf
                            
                            <!-- Google Login Button -->
                            <div>
                                <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center py-2.5 px-4 border border-slate-300 rounded-lg shadow-sm bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                                    <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                    </svg>
                                    Continue with Google
                                </a>
                            </div>

                            <div class="relative my-6">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-slate-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-slate-500">Or continue with</span>
                                </div>
                            </div>
                            
                            <!-- Username or Email -->
                            <div>
                                <label for="login" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username or Email</label>
                                <div class="mt-1">
                                    <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm">
                                </div>
                                <x-input-error :messages="$errors->get('login')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="flex justify-between items-center">
                                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-rose-600 hover:text-rose-500">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <input id="password" type="password" name="password" required
                                        class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm">
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center">
                                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <label for="remember_me" class="ml-2 block text-sm text-slate-600">Remember me</label>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                                    Sign in
                                </button>
                            </div>
                        </form>
                        
                        <p class="mt-6 text-center text-sm text-slate-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-medium text-rose-600 hover:text-rose-500">Sign up for free</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 flex justify-around items-center h-16 z-40 pb-safe">
        <a href="#listings" class="flex flex-col items-center justify-center w-full h-full text-rose-600">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="text-xs font-medium tracking-wide">Explore</span>
        </a>
        <a href="{{ auth()->check() && auth()->user()->user_type === 'tenant' ? route('tenant.favorites.index') : route('login') }}" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"></path>
            </svg>
            <span class="text-xs font-medium tracking-wide">Wishlists</span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                </svg>
                <span class="text-xs font-medium tracking-wide">Profile</span>
            </a>
        @else
            <button @click="showLoginModal = true" class="flex flex-col items-center justify-center w-full h-full text-slate-500 hover:text-slate-900 transition-colors">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                </svg>
                <span class="text-xs font-medium tracking-wide">Log in</span>
            </button>
        @endauth
    </div>
</body>
</html>





