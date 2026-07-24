<div>
    <!-- Search and Filter Bar -->
    <div class="mb-6 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row gap-4 justify-between items-center">
        
        <!-- Search Input -->
        <div class="relative w-full lg:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl leading-5 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 sm:text-sm transition-colors" placeholder="Search by title or location...">
        </div>

        <!-- Status Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap hidden sm:block">Filter by Status:</span>
            <div class="relative w-full sm:w-48">
                <select wire:model.live="statusFilter" class="block w-full pl-3 pr-10 py-2.5 text-base border-slate-300 dark:border-slate-600 focus:outline-none focus:ring-rose-500 focus:border-rose-500 sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white cursor-pointer appearance-none shadow-sm">
                    <option value="" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-white">All Properties</option>
                    <option value="available" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-white">Available</option>
                    <option value="rented" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-white">Rented</option>
                    <option value="maintenance" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-white">Maintenance</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-rose-50 dark:bg-rose-900/30 p-4 border border-rose-200 dark:border-rose-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-rose-800 dark:text-rose-300">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Property Grid -->
    <div class="relative min-h-[400px]">
        <!-- Loading overlay -->
        <div wire:loading.flex class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 items-center justify-center rounded-2xl">
            <svg class="animate-spin h-8 w-8 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($properties as $property)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                        
                        <!-- Top Image Section -->
                        <div class="relative aspect-video bg-slate-200 dark:bg-slate-700 overflow-hidden">
                            @if($property->images->count() > 0)
                                <img src="{{ Storage::url($property->images->first()->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                </div>
                            @endif

                            <!-- Property Type Badge -->
                            <div class="absolute top-4 left-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-900 dark:text-white shadow-sm">
                                {{ ucfirst(str_replace('_', ' ', $property->property_type)) }}
                            </div>

                            <!-- Status Dropdown (Top Right inside image) -->
                            <div class="absolute top-4 right-4 z-20">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm backdrop-blur-md border border-white/20 transition-all {{ 
                                        $property->status === 'available' ? 'bg-emerald-500/90 text-white hover:bg-emerald-600' : 
                                        ($property->status === 'rented' ? 'bg-blue-500/90 text-white hover:bg-blue-600' : 
                                        'bg-amber-500/90 text-white hover:bg-amber-600') 
                                    }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        {{ ucfirst($property->status) }}
                                        <svg class="h-3.5 w-3.5 ml-0.5 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    
                                    <!-- Status Menu -->
                                    <div x-show="open" x-transition class="absolute right-0 mt-2 w-36 rounded-xl bg-white dark:bg-slate-800 shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden origin-top-right">
                                        <div class="py-1">
                                            @if($property->status !== 'available')
                                                <button wire:click="toggleStatus({{ $property->id }}, 'available')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-700 font-medium">Available</button>
                                            @endif
                                            @if($property->status !== 'rented')
                                                <button wire:click="toggleStatus({{ $property->id }}, 'rented')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 font-medium">Rented</button>
                                            @endif
                                            @if($property->status !== 'maintenance')
                                                <button wire:click="toggleStatus({{ $property->id }}, 'maintenance')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-slate-700 font-medium">Maintenance</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col">
                            
                            <!-- Title & Price Row -->
                            <div class="flex justify-between items-start gap-4 mb-2">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('properties.show', $property) }}" class="text-lg font-bold text-slate-900 dark:text-white truncate hover:text-rose-600 transition-colors block">
                                        {{ $property->title }}
                                    </a>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 truncate flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $property->barangay ? $property->barangay . ', ' : '' }}{{ $property->city }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-lg font-extrabold text-rose-600 dark:text-rose-500">₱{{ number_format($property->monthly_rent, 0) }}</div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">/ Month</div>
                                </div>
                            </div>

                            <!-- Insights / Badges -->
                            <div class="mt-4 flex flex-wrap gap-2 mb-6">
                                @if($property->applications_count > 0)
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 text-[13px] font-semibold text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-700/50 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        {{ $property->applications_count }} Pending Apps
                                    </a>
                                @endif
                                
                                @if($property->active_tenants_count > 0)
                                    <span class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 text-[13px] font-semibold text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-700/50">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        {{ $property->active_tenants_count }} Active Tenants
                                    </span>
                                @endif

                                @if($property->applications_count == 0 && $property->active_tenants_count == 0)
                                    <span class="inline-flex items-center rounded-lg bg-slate-50 dark:bg-slate-800 px-2.5 py-1 text-[13px] font-medium text-slate-500 border border-slate-200 dark:border-slate-700">
                                        No recent activity
                                    </span>
                                @endif
                            </div>

                            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-center">
                                <span class="text-xs text-slate-400 font-medium">Added {{ $property->created_at->diffForHumans() }}</span>
                                
                                <!-- Action Menu (3 dots) -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.away="open = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors focus:outline-none">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                    </button>

                                    <!-- Dropdown -->
                                    <div x-show="open" x-transition class="absolute right-0 bottom-full mb-2 w-48 rounded-xl bg-white dark:bg-slate-800 shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden z-30 origin-bottom-right" style="display: none;">
                                        <div class="py-1">
                                            <a href="{{ route('properties.edit', $property) }}" class="group flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium">
                                                <svg class="mr-3 h-4 w-4 text-slate-400 group-hover:text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                Edit Details
                                            </a>
                                            <a href="{{ route('properties.show', $property) }}" class="group flex items-center px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-medium">
                                                <svg class="mr-3 h-4 w-4 text-slate-400 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                View Listing
                                            </a>
                                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                            <button onclick="confirm('Are you sure you want to delete this property? This cannot be undone.') || event.stopImmediatePropagation()" wire:click="deleteProperty({{ $property->id }})" class="group flex w-full items-center px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 font-medium">
                                                <svg class="mr-3 h-4 w-4 text-rose-400 group-hover:text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Delete Property
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 py-16 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="h-20 w-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mb-5 border border-slate-100 dark:border-slate-700">
                        <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">No properties found</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Get started by creating a new listing or adjust your search filters to find what you're looking for.</p>
                    <a href="{{ route('properties.create') }}" class="mt-8 inline-flex items-center rounded-xl bg-rose-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition-all hover:scale-105 shadow-rose-600/20">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                        Add New Property
                    </a>
                </div>
            </div>
        @endif
        
        @if($properties->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>
