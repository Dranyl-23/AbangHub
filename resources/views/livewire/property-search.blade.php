<div class="flex flex-col lg:flex-row bg-white dark:bg-slate-900 w-full"
     x-data="propertyMap({{ $initialMapData }})"
     @update-map-markers.window="updateMarkers($event.detail.markers)">
    
    <!-- Include Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Left Column: Search & List (60% width on Desktop) -->
    <div class="w-full lg:w-[60%] flex flex-col p-4 sm:p-6 lg:p-8">
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Find your next home in Digos City</h1>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Browse through the best boarding houses, apartments, and rooms for rent.</p>
        </div>

        <!-- Top Filter Bar -->
        <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-2xl p-5 mb-8">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <!-- Search Keyword -->
                <div class="flex-grow w-full md:w-auto">
                    <label for="search" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Search Location</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" class="pl-10 block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 sm:text-sm h-11" placeholder="City, Barangay...">
                    </div>
                </div>

                <!-- Property Type -->
                <div class="w-full md:w-36">
                    <label for="type" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Type</label>
                    <select wire:model.live="type" id="type" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 sm:text-sm h-11">
                        <option value="">All Types</option>
                        <option value="apartment">Apartment</option>
                        <option value="boarding_house">Boarding House</option>
                        <option value="room">Room</option>
                        <option value="house">House</option>
                        <option value="condo">Condo</option>
                    </select>
                </div>

                <!-- Price & Beds (Grouped for compactness) -->
                <div class="flex gap-3 w-full md:w-auto">
                    <div class="w-1/3 md:w-24">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Min ₱</label>
                        <input wire:model.live.debounce.500ms="min_price" type="number" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 sm:text-sm h-11" placeholder="0">
                    </div>
                    <div class="w-1/3 md:w-24">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Max ₱</label>
                        <input wire:model.live.debounce.500ms="max_price" type="number" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 sm:text-sm h-11" placeholder="Any">
                    </div>
                    <div class="w-1/3 md:w-24">
                        <label for="bedrooms" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Beds</label>
                        <select wire:model.live="bedrooms" id="bedrooms" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 sm:text-sm h-11">
                            <option value="">Any</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4+">4+</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div wire:loading.flex class="w-full justify-center my-4">
            <svg class="animate-spin h-8 w-8 text-rose-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-8" wire:loading.class="opacity-50 transition-opacity duration-200">
            @forelse($properties as $property)
                <div @click="window.location.href = '{{ route('properties.show', $property) }}'" class="block group bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-all duration-300 flex flex-col cursor-pointer"
                     @mouseenter="highlightMarker({{ $property->id }})"
                     @mouseleave="resetMarker({{ $property->id }})">
                    
                    <div class="relative overflow-hidden bg-slate-200 dark:bg-slate-700" style="height: 200px;">
                        @if(Auth::check() && Auth::user()->user_type === 'tenant')
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
                                class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm shadow-md flex items-center justify-center transition-transform hover:scale-110 focus:outline-none"
                                x-data="{ isFav: {{ Auth::user()->favorites->contains($property->id) ? 'true' : 'false' }} }">
                                <svg class="w-4 h-4 transition-colors" :class="isFav ? 'text-rose-500 fill-current' : 'text-slate-400 hover:text-rose-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        @else
                            <button type="button" @click.stop.prevent="window.location.href = '{{ route('login') }}'" class="absolute top-4 right-4 z-10 text-white hover:scale-110 transition-transform focus:outline-none">
                                <svg class="w-8 h-8 drop-shadow-md fill-black/50 stroke-white" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </button>
                        @endif

                        @if($property->images->count() > 0)
                            <img src="{{ Storage::url($property->images->first()->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                        @else
                            <img src="https://picsum.photos/seed/{{ $property->id }}/800/600" alt="{{ $property->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                        @endif
                        
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-lg bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-xs font-bold text-slate-800 dark:text-slate-200 shadow-sm capitalize tracking-wide">
                                {{ str_replace('_', ' ', $property->property_type) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow">
                        <div class="flex items-center justify-between mb-1">
                            <div class="font-medium text-[15px] text-slate-900 dark:text-slate-400 truncate pr-2">
                                {{ $property->barangay ? $property->barangay . ', ' : '' }}{{ $property->city }}
                            </div>
                            @if($property->review_count > 0)
                                <div class="flex items-center gap-1 text-[15px] text-slate-900 dark:text-white shrink-0">
                                    <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <span>{{ number_format($property->average_rating, 1) }}</span>
                                </div>
                            @else
                                <div class="text-[14px] text-slate-500 shrink-0 font-medium">New</div>
                            @endif
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1 line-clamp-1 group-hover:text-rose-600 transition-colors">{{ $property->title }}</h4>
                        
                        <div class="text-rose-600 dark:text-rose-400 font-bold text-lg mb-3">
                            ₱{{ number_format($property->monthly_rent, 0) }} <span class="text-sm font-normal text-slate-500">/ month</span>
                        </div>

                        <div class="mt-auto pt-3 flex items-center gap-4 border-t border-slate-100 dark:border-slate-700/50 text-sm text-slate-600 dark:text-slate-400">
                            <div class="flex items-center" title="Bedrooms">
                                <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                {{ $property->bedrooms }}
                            </div>
                            <div class="flex items-center" title="Bathrooms">
                                <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                {{ $property->bathrooms }}
                            </div>
                            <div class="flex items-center" title="Floor Area">
                                <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                {{ $property->floor_area ?? '--' }} sqm
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white/50 dark:bg-slate-800/50 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4 text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No properties found</h3>
                    <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Try adjusting your filters or search terms to find what you're looking for.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-auto pb-4">
            {{ $properties->links() }}
        </div>
    </div>

    <!-- Right Column: Interactive Map (40% width on Desktop) -->
    <div class="hidden lg:block w-[40%] h-[calc(100vh-120px)] sticky top-[88px] z-10 border-l border-slate-200 dark:border-slate-800" wire:ignore>
        <div id="leaflet-map" class="w-full h-full"></div>
    </div>

    <!-- Alpine Component for Map Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('propertyMap', (initialData) => ({
                map: null,
                markersData: initialData,
                markerInstances: {}, // Store marker references by property ID
                activeMarkerId: null,

                init() {
                    // Initialize Leaflet map (Wait for DOM)
                    setTimeout(() => {
                        this.initMap();
                    }, 100);
                },

                initMap() {
                    // Center roughly around Digos City
                    this.map = L.map('leaflet-map', {
                        zoomControl: false // We can add custom zoom controls if needed
                    }).setView([6.7486, 125.3556], 14);

                    // Add a premium looking tile layer (CARTO Voyager is very clean/light)
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                        subdomains: 'abcd',
                        maxZoom: 20
                    }).addTo(this.map);
                    
                    // Add Zoom Control at bottom right
                    L.control.zoom({
                        position: 'bottomright'
                    }).addTo(this.map);

                    this.renderMarkers();
                },

                renderMarkers() {
                    // Clear existing markers
                    Object.values(this.markerInstances).forEach(marker => {
                        this.map.removeLayer(marker);
                    });
                    this.markerInstances = {};

                    if (this.markersData.length === 0) return;

                    const bounds = L.latLngBounds();

                    this.markersData.forEach(property => {
                        if (property.latitude && property.longitude) {
                            // Create a custom divIcon that looks like Airbnb price pins
                            const priceFormatted = new Intl.NumberFormat('en-PH', { maximumSignificantDigits: 3 }).format(property.monthly_rent);
                            
                            const defaultHtml = `
                                <div class="bg-white px-3 py-1.5 rounded-full shadow-md border border-slate-200 text-slate-800 font-bold text-sm text-center whitespace-nowrap hover:bg-slate-900 hover:text-white transition-colors hover:scale-105 transform origin-bottom cursor-pointer relative"
                                     style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                                    ₱${priceFormatted}
                                </div>
                            `;

                            const customIcon = L.divIcon({
                                className: 'custom-price-marker',
                                html: defaultHtml,
                                iconSize: null, // Let CSS determine size
                                iconAnchor: [35, 15] // Center roughly
                            });

                            const marker = L.marker([property.latitude, property.longitude], {
                                icon: customIcon,
                                title: property.title
                            }).addTo(this.map);

                            // Add click event to redirect to property
                            marker.on('click', () => {
                                window.location.href = property.url;
                            });

                            // Store reference
                            this.markerInstances[property.id] = marker;
                            bounds.extend([property.latitude, property.longitude]);
                        }
                    });

                    // Auto zoom/pan to fit markers if there are any
                    if (Object.keys(this.markerInstances).length > 0) {
                        this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
                    }
                },

                updateMarkers(newData) {
                    this.markersData = newData;
                    this.renderMarkers();
                },

                // Highlight marker when hovering over card
                highlightMarker(id) {
                    const marker = this.markerInstances[id];
                    if (marker) {
                        const priceFormatted = new Intl.NumberFormat('en-PH', { maximumSignificantDigits: 3 }).format(this.markersData.find(p => p.id === id).monthly_rent);
                        
                        const activeHtml = `
                            <div class="bg-slate-900 px-3 py-1.5 rounded-full shadow-lg border border-slate-900 text-white font-bold text-sm text-center whitespace-nowrap scale-110 transform origin-bottom cursor-pointer relative z-50">
                                ₱${priceFormatted}
                            </div>
                        `;
                        marker.setIcon(L.divIcon({
                            className: 'custom-price-marker',
                            html: activeHtml,
                            iconSize: null,
                            iconAnchor: [35, 15]
                        }));
                    }
                },

                // Reset marker when leaving card
                resetMarker(id) {
                    const marker = this.markerInstances[id];
                    if (marker) {
                        const priceFormatted = new Intl.NumberFormat('en-PH', { maximumSignificantDigits: 3 }).format(this.markersData.find(p => p.id === id).monthly_rent);
                        
                        const defaultHtml = `
                            <div class="bg-white px-3 py-1.5 rounded-full shadow-md border border-slate-200 text-slate-800 font-bold text-sm text-center whitespace-nowrap hover:bg-slate-900 hover:text-white transition-colors hover:scale-105 transform origin-bottom cursor-pointer relative"
                                 style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                                ₱${priceFormatted}
                            </div>
                        `;
                        marker.setIcon(L.divIcon({
                            className: 'custom-price-marker',
                            html: defaultHtml,
                            iconSize: null,
                            iconAnchor: [35, 15]
                        }));
                    }
                }
            }));
        });
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: #94a3b8; 
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155; 
        }
        .dark .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: #475569; 
        }
        /* Fix leaflet z-index issues with sticky nav */
        .leaflet-container {
            z-index: 1 !important;
        }
    </style>
</div>
