<x-app-layout>
    <div class="bg-transparent min-h-screen pb-16 text-slate-900 dark:text-white transition-colors duration-300">
        
        <!-- Minimalist Header (Airbnb Style) -->
        <div class="w-full pt-6 pb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">
                        Welcome back, {{ explode(' ', auth()->user()->full_name)[0] }}
                    </h1>
                    <p class="text-base text-slate-500 dark:text-slate-400">
                        Let's find the perfect place for you.
                    </p>
                </div>
                
                <!-- Pill-shaped CTA mimicking Airbnb's search bar -->
                <div class="shrink-0 w-full md:w-auto mt-2 md:mt-0">
                    <a href="{{ route('properties.index') }}" class="flex items-center justify-between w-full md:w-auto px-5 py-3.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-full shadow-sm hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center gap-4 pr-6">
                            <span class="text-sm font-medium">Anywhere</span>
                            <span class="w-px h-6 bg-slate-300 dark:bg-slate-700"></span>
                            <span class="text-sm font-medium">Any price</span>
                            <span class="w-px h-6 bg-slate-300 dark:bg-slate-700"></span>
                            <span class="text-sm text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Find homes</span>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-rose-600 flex items-center justify-center text-white shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path></svg>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        @if($activeLease)
        <!-- Active Lease Banner -->
        <div class="w-full mb-12">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm flex flex-col md:flex-row">
                <div class="w-full md:w-1/3 aspect-[4/3] md:aspect-auto relative bg-slate-200 dark:bg-slate-900">
                    @if($activeLease->property->images->count() > 0)
                        <img src="{{ asset($activeLease->property->images->first()->image_path) }}" alt="Property" class="w-full h-full object-cover">
                    @else
                        <img src="https://picsum.photos/seed/{{ $activeLease->property_id }}/800/600" alt="Property" class="w-full h-full object-cover">
                    @endif
                    <div class="absolute top-4 left-4 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Active Lease
                    </div>
                </div>
                <div class="w-full md:w-2/3 p-6 md:p-10 flex flex-col justify-center">
                    <p class="text-sm font-semibold text-rose-600 dark:text-rose-400 mb-1">YOUR CURRENT HOME</p>
                    <h2 class="text-2xl sm:text-3xl font-semibold mb-4">{{ $activeLease->property->title }}</h2>
                    <div class="flex flex-wrap gap-x-8 gap-y-4 mb-8">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Monthly Rent</p>
                            <p class="text-lg font-semibold">₱{{ number_format($activeLease->monthly_rent, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Next Payment</p>
                            <p class="text-lg font-semibold">{{ $upcomingBills->first() ? $upcomingBills->first()->due_date->format('M d, Y') : 'No pending bills' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Landlord</p>
                            <p class="text-lg font-semibold">{{ $activeLease->property->owner->full_name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4 flex-wrap mt-4">
                        <a href="{{ route('leases.download', $activeLease) }}" class="px-6 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-lg font-medium hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors text-center shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Contract
                        </a>
                        <a href="{{ route('properties.show', $activeLease->property) }}" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium transition-colors text-center">View Property</a>
                        <a href="{{ route('messages.show', $activeLease->property->owner) }}" class="px-6 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-center block">Message Landlord</a>
                        @php
                            $hasReviewed = $activeLease->property->reviews()->where('tenant_id', auth()->id())->exists();
                        @endphp
                        @if(!$hasReviewed)
                            <a href="{{ route('properties.show', $activeLease->property) }}#reviews" class="px-6 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-slate-900 rounded-lg font-medium transition-colors text-center">Rate Property</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Clean, Subtle Stats -->
        <div class="w-full mb-12">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Active Leases</p>
                    <p class="text-2xl font-semibold">{{ $stats['activeLeases'] ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Pending Apps</p>
                    <p class="text-2xl font-semibold">{{ $stats['pendingApplications'] ?? 0 }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Rent Paid</p>
                    <p class="text-2xl font-semibold">₱{{ number_format($stats['totalPaid'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors relative">
                    @if(($stats['unreadMessages'] ?? 0) > 0)
                        <span class="absolute top-5 right-5 w-2.5 h-2.5 bg-rose-600 rounded-full"></span>
                    @endif
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Messages</p>
                    <p class="text-2xl font-semibold">{{ $stats['unreadMessages'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Left Side: Main Content (Takes 2 cols) -->
                <div class="lg:col-span-2">

                    <!-- Saved Properties (Wishlist) -->
                    <div x-data class="mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Saved Homes</h2>
                            <div class="flex items-center gap-4">
                                <!-- Navigation Arrows -->
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="$refs.savedContainer.scrollBy({left: -320, behavior: 'smooth'})" class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <button type="button" @click="$refs.savedContainer.scrollBy({left: 320, behavior: 'smooth'})" class="w-8 h-8 flex items-center justify-center rounded-full bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @if($savedProperties->count() > 0)
                            <!-- Horizontal Scroll Container -->
                            <div x-ref="savedContainer" class="flex overflow-x-auto gap-6 pb-6 snap-x snap-mandatory [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                @foreach($savedProperties as $property)
                                    <a href="{{ route('properties.show', $property) }}" class="block group cursor-pointer shrink-0 w-[280px] sm:w-[320px] snap-start">
                                        
                                        <!-- Image -->
                                        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-200 dark:bg-slate-800 mb-3">
                                            @if($property->images->count() > 0)
                                                <img src="{{ asset($property->images->first()->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="https://picsum.photos/seed/{{ $property->id }}/800/600" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                            @endif
                                            
                                            <!-- Guest Favorite / Type Badge -->
                                            <div class="absolute top-3 left-3 bg-white dark:bg-slate-900 px-2.5 py-1 rounded-full text-[13px] font-semibold shadow-sm">
                                                {{ str_replace('_', ' ', ucfirst($property->property_type)) }}
                                            </div>
                                            
                                            <!-- Heart Icon (Filled because saved) -->
                                            <button type="button" class="absolute top-3 right-3 text-white focus:outline-none hover:scale-110 transition-transform">
                                                <svg class="w-7 h-7 drop-shadow-sm text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="flex justify-between items-start">
                                            <div class="pr-2 flex-1 min-w-0">
                                                <h3 class="text-[15px] font-semibold truncate">{{ $property->city }}</h3>
                                                <p class="text-[15px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $property->title }}</p>
                                                <p class="text-[15px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $property->bedrooms }} beds · {{ $property->bathrooms }} baths</p>
                                                <div class="mt-1.5 flex items-baseline gap-1">
                                                    <span class="text-[15px] font-semibold">₱{{ number_format($property->monthly_rent, 0) }}</span>
                                                    <span class="text-[15px]">night</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 text-[15px]">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                <span>4.9</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 text-center text-slate-500 dark:text-slate-400 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl">
                                You haven't saved any homes yet.
                            </div>
                        @endif
                    </div>

                    <!-- Recommended Properties (Explore) -->
                    <div class="mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Recommended for You</h2>
                            <a href="{{ route('properties.index') }}" class="text-sm font-medium text-rose-600 hover:text-rose-700">See all</a>
                        </div>
                        
                        @if(isset($recommendedProperties) && $recommendedProperties->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @foreach($recommendedProperties as $property)
                                    <a href="{{ route('properties.show', $property) }}" class="block group cursor-pointer">
                                        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-200 dark:bg-slate-800 mb-3">
                                            @if($property->images->count() > 0)
                                                <img src="{{ asset($property->images->first()->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <img src="https://picsum.photos/seed/{{ $property->id }}/800/600" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @endif
                                            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm dark:bg-slate-900/95 px-2.5 py-1 rounded-full text-[13px] font-semibold shadow-sm">
                                                {{ str_replace('_', ' ', ucfirst($property->property_type)) }}
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-start">
                                            <div class="pr-2 flex-1 min-w-0">
                                                <h3 class="text-[15px] font-semibold truncate">{{ $property->city }}</h3>
                                                <p class="text-[15px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $property->title }}</p>
                                                <div class="mt-1.5 flex items-baseline gap-1">
                                                    <span class="text-[15px] font-semibold text-slate-900 dark:text-white">₱{{ number_format($property->monthly_rent, 0) }}</span>
                                                    <span class="text-[15px] text-slate-500 dark:text-slate-400">/ month</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 text-center text-slate-500 dark:text-slate-400 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl">
                                No recommendations available right now.
                            </div>
                        @endif
                    </div>

                    <!-- Application Tracker -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-semibold tracking-tight mb-6">Application Tracker</h2>
                        
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($applications as $app)
                                    <li class="p-5 flex flex-col sm:flex-row sm:items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                        <div class="w-20 h-16 shrink-0 rounded-lg overflow-hidden bg-slate-200 dark:bg-slate-900">
                                            @if($app->property->images->count() > 0)
                                                <img src="{{ asset($app->property->images->first()->image_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="https://picsum.photos/seed/{{ $app->property->id }}/200/150" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('properties.show', $app->property) }}" class="text-[15px] font-semibold hover:underline truncate block">
                                                {{ $app->property->title }}
                                            </a>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 truncate">
                                                Applied on {{ $app->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="shrink-0 flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-4">
                                            @if($app->status === 'approved')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20">Approved</span>
                                            @elseif($app->status === 'rejected')
                                                <span class="inline-flex items-center rounded-full bg-rose-50 dark:bg-rose-900/30 px-2.5 py-1 text-xs font-medium text-rose-700 dark:text-rose-400 ring-1 ring-inset ring-rose-600/20">Rejected</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-600/20">Pending Review</span>
                                            @endif
                                            <a href="#" class="text-sm font-medium underline decoration-1 hover:text-slate-600 dark:hover:text-slate-300">View details</a>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        You haven't submitted any applications yet.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Right Side: Sidebar -->
                <div class="lg:col-span-1 flex flex-col gap-12">
                    
                    <!-- Upcoming Bills -->
                    <div>
                        <h2 class="text-2xl font-semibold tracking-tight mb-6">Upcoming Bills</h2>
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($upcomingBills as $bill)
                                    <li class="p-5">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-[15px] font-semibold">{{ $bill->description }}</h3>
                                            <span class="text-lg font-semibold text-rose-600 dark:text-rose-400">₱{{ number_format($bill->amount, 0) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Due {{ $bill->due_date->format('M d, Y') }}
                                            </p>
                                            <button class="px-4 py-1.5 bg-black dark:bg-white text-white dark:text-black rounded-lg text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">Pay Now</button>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        No upcoming bills.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Maintenance Requests -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Maintenance</h2>
                            <button class="text-sm font-medium underline decoration-1 hover:text-slate-600 dark:hover:text-slate-300">Report Issue</button>
                        </div>
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($maintenanceRequests as $req)
                                    <li class="p-5 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors cursor-pointer group">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="text-[15px] font-semibold group-hover:underline">{{ $req->title }}</h3>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $req->description }}</p>
                                            </div>
                                            @if($req->status === 'resolved')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">Fixed</span>
                                            @elseif($req->status === 'in_progress')
                                                <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400">Working</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700 px-2 py-0.5 text-xs font-medium text-slate-700 dark:text-slate-300">Pending</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-400 mt-3">Reported {{ $req->created_at->diffForHumans() }}</p>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        No active maintenance tickets.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Activity Sidebar -->
                    <div>
                        <h2 class="text-2xl font-semibold tracking-tight mb-6">Recent Activity</h2>
                        
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($recentActivity as $activity)
                                    <li class="p-5 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <!-- Minimalist Icon -->
                                            <div class="shrink-0">
                                                @if($activity->type === 'deposit')
                                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[15px] font-semibold truncate">
                                                    {{ ucfirst($activity->type) }} {{ $activity->status }}
                                                </p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                    {{ $activity->property?->title ?? 'N/A' }}
                                                </p>
                                            </div>
                                            
                                            <!-- Amount -->
                                            <div class="shrink-0 text-right">
                                                <span class="text-[15px] font-semibold">
                                                    ₱{{ number_format($activity->amount, 0) }}
                                                </span>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $activity->created_at->format('M d') }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        No recent activity.
                                    </li>
                                @endforelse
                            </ul>
                            
                            @if(count($recentActivity) > 0)
                                <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 text-center rounded-b-2xl">
                                    <a href="{{ route('transactions.index') }}" class="text-sm font-semibold underline decoration-1 underline-offset-2 hover:text-slate-600 dark:hover:text-slate-300">Show all</a>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>

