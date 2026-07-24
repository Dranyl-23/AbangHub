<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">My Favorites</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Properties you have saved for later.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($favorites->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($favorites as $property)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200 dark:border-slate-700 group flex flex-col h-full relative" x-data="{ isFavorite: true }">
                            
                            <!-- Favorite Toggle Button -->
                            <button type="button" @click="
                                fetch('{{ route('properties.favorite', $property) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                }).then(res => res.json()).then(data => {
                                    isFavorite = (data.status === 'added');
                                })"
                                class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm shadow-md flex items-center justify-center transition-transform hover:scale-110 focus:outline-none">
                                <svg class="w-6 h-6 transition-colors" :class="isFavorite ? 'text-rose-500 fill-current' : 'text-slate-400 hover:text-rose-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>

                            <!-- Image Container -->
                            <a href="{{ route('properties.show', $property) }}" class="relative block h-56 overflow-hidden bg-slate-100 dark:bg-slate-700 shrink-0">
                                @if($property->primaryImage)
                                    <img src="{{ Storage::url($property->primaryImage->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        <span class="text-sm font-medium">No image</span>
                                    </div>
                                @endif
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-slate-900/0 to-slate-900/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <!-- Status Badge -->
                                <div class="absolute bottom-4 left-4 flex gap-2">
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-slate-900 rounded-full text-xs font-bold tracking-wide uppercase shadow-sm">
                                        {{ str_replace('_', ' ', $property->property_type) }}
                                    </span>
                                    @if($property->status === 'available')
                                        <span class="px-3 py-1 bg-emerald-500/90 backdrop-blur-md text-white rounded-full text-xs font-bold tracking-wide uppercase shadow-sm flex items-center">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 animate-pulse"></span>
                                            Available
                                        </span>
                                    @endif
                                </div>
                            </a>

                            <!-- Content -->
                            <div class="p-6 flex flex-col flex-1">
                                <!-- Price and Title -->
                                <div class="mb-4">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1 line-clamp-1 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">
                                        <a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                    </h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center line-clamp-1">
                                        <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        {{ $property->address }}, {{ $property->city }}
                                    </p>
                                </div>

                                <div class="mt-auto">
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold mb-1">Monthly Rent</p>
                                            <p class="text-2xl font-black text-slate-900 dark:text-white">₱{{ number_format($property->monthly_rent, 0) }}</p>
                                        </div>
                                        <a href="{{ route('properties.show', $property) }}" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-700 shadow-sm max-w-2xl mx-auto">
                    <div class="mx-auto w-20 h-20 bg-rose-50 dark:bg-slate-700 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-rose-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Your wishlist is empty</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">You haven't saved any properties yet. Click the heart icon on any property to save it here for later.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Explore Properties
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
