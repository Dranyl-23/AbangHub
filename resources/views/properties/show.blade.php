<x-app-layout>
    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="w-full">
            
            <!-- Breadcrumbs & Actions -->
            <div class="flex items-center justify-between mb-6">
                <nav class="flex text-sm font-medium text-slate-500 dark:text-slate-400">
                    <a href="{{ route('properties.index') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Properties</a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-900 dark:text-white truncate max-w-xs">{{ $property->title }}</span>
                </nav>
                <div class="flex gap-3">
                    @php
                        $isFavorited = auth()->check() && \App\Models\Favorite::where('user_id', auth()->id())->where('property_id', $property->id)->exists();
                    @endphp
                    <form action="{{ route('properties.favorite', $property) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:text-rose-500 transition-colors border border-slate-200 dark:border-slate-700 {{ $isFavorited ? 'text-rose-500' : 'text-slate-500' }}" title="{{ $isFavorited ? 'Remove from favorites' : 'Save to favorites' }}">
                            <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </button>
                    </form>
                    <button onclick="if(navigator.share) { navigator.share({title: '{{ addslashes($property->title) }}', url: window.location.href}); } else { navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!'); }" class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:text-blue-500 transition-colors border border-slate-200 dark:border-slate-700" title="Share">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Image Gallery -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-[50vh] md:h-[60vh] rounded-3xl overflow-hidden mb-10">
                @if($property->images->count() > 0)
                    <!-- Main Image -->
                    <div class="md:col-span-2 md:row-span-2 relative group cursor-pointer">
                        <img src="{{ Storage::url($property->images[0]->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
                    </div>
                    <!-- Other Images -->
                    @foreach($property->images->skip(1)->take(4) as $image)
                        <div class="hidden md:block relative group cursor-pointer overflow-hidden">
                            <img src="{{ Storage::url($image->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-4 bg-slate-200 dark:bg-slate-700 flex flex-col items-center justify-center text-slate-400">
                        <svg class="w-20 h-20 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p>No photos available</p>
                    </div>
                @endif
                
                @if($property->images->count() > 5)
                    <button class="absolute bottom-6 right-6 px-4 py-2 bg-white/90 backdrop-blur-sm rounded-lg shadow-md font-semibold text-sm text-slate-900 border border-slate-200 hover:bg-white transition-colors">
                        Show all {{ $property->images->count() }} photos
                    </button>
                @endif
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative">
                
                <!-- Left Side: Details -->
                <div class="lg:col-span-2 space-y-10">
                    
                    <!-- Header Info -->
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-xs font-bold uppercase tracking-wider">
                                {{ str_replace('_', ' ', $property->property_type) }}
                            </span>
                            @if($property->furnishing_status !== 'unfurnished')
                                <span class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $property->furnishing_status) }}
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-4">
                            {{ $property->title }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-4 text-lg text-slate-600 dark:text-slate-400 mb-2">
                            <p class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $property->address }}, {{ $property->barangay ? $property->barangay . ', ' : '' }}{{ $property->city }}, {{ $property->province }}
                            </p>
                            @if($property->review_count > 0)
                            <div class="flex items-center gap-1.5 font-bold text-slate-900 dark:text-white">
                                <span>·</span>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <span>{{ number_format($property->average_rating, 1) }}</span>
                                <a href="#reviews" class="text-base font-normal underline hover:text-rose-600 ml-1">({{ $property->review_count }} reviews)</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Key Features -->
                    <div class="flex flex-wrap gap-8 py-6 border-y border-slate-200 dark:border-slate-700/50">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $property->bedrooms }}</p>
                                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Bedrooms</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $property->bathrooms }}</p>
                                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Bathrooms</p>
                            </div>
                        </div>
                        @if($property->floor_area)
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $property->floor_area }}</p>
                                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Square Meters</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">About this property</h2>
                        <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400">
                            {!! nl2br(e($property->description)) !!}
                        </div>
                    </div>

                    <!-- Amenities -->
                    @if($property->amenities->count() > 0)
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">What this place offers</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($property->amenities as $amenity)
                                @php
                                    $name = strtolower($amenity->amenity_name);
                                @endphp
                                <div class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                                    @if(str_contains($name, 'wifi') || str_contains($name, 'internet'))
                                        <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                                        </svg>
                                    @elseif(str_contains($name, 'air') || str_contains($name, 'ac'))
                                        <svg class="w-6 h-6 text-sky-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15m10.5 3l-3-3 3-3m-10.5 0l3 3-3 3" />
                                        </svg>
                                    @elseif(str_contains($name, 'heater') || str_contains($name, 'shower') || str_contains($name, 'bath'))
                                        <svg class="w-6 h-6 text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                        </svg>
                                    @elseif(str_contains($name, 'park') || str_contains($name, 'garage'))
                                        <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                        </svg>
                                    @elseif(str_contains($name, 'pool'))
                                        <svg class="w-6 h-6 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                    <span class="text-lg font-medium">{{ $amenity->amenity_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Reviews Section -->
                    <div id="reviews" class="pt-10 border-t border-slate-200 dark:border-slate-700/50">
                        <div class="flex items-center gap-3 mb-8">
                            <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                                {{ $property->review_count > 0 ? number_format($property->average_rating, 1) . ' · ' . $property->review_count . ' reviews' : 'No reviews yet' }}
                            </h2>
                        </div>
                        
                        <!-- Review Grid -->
                        @if($property->review_count > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10 mb-10">
                                @foreach($property->reviews()->latest()->get() as $review)
                                    <div>
                                        <div class="flex items-center gap-4 mb-3">
                                            @if($review->tenant->profile_image)
                                                <img src="{{ Storage::url($review->tenant->profile_image) }}" class="w-12 h-12 rounded-full object-cover">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->tenant->full_name ?? $review->tenant->username) }}&background=f43f5e&color=fff" class="w-12 h-12 rounded-full object-cover">
                                            @endif
                                            <div>
                                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $review->tenant->full_name ?? $review->tenant->username }}</h4>
                                                <p class="text-sm text-slate-500">{{ $review->created_at->format('F Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex text-yellow-400 mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-300 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            @endfor
                                        </div>
                                        @if($review->comment)
                                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Write Review Form -->
                        @php
                            $canReview = false;
                            if(auth()->check() && auth()->user()->user_type === 'tenant') {
                                $hasRented = auth()->user()->leases()->where('property_id', $property->id)->whereIn('status', ['approved', 'active', 'completed'])->exists();
                                $hasReviewed = $property->reviews()->where('tenant_id', auth()->id())->exists();
                                $canReview = $hasRented && !$hasReviewed;
                            }
                        @endphp

                        @if($canReview)
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Rate your stay</h3>
                                <form action="{{ route('properties.reviews.store', $property) }}" method="POST" x-data="{ rating: 0, hoverRating: 0 }">
                                    @csrf
                                    <input type="hidden" name="rating" x-model="rating">
                                    
                                    <div class="flex items-center mb-4">
                                        <template x-for="i in 5">
                                            <svg @click="rating = i" @mouseenter="hoverRating = i" @mouseleave="hoverRating = 0" class="w-8 h-8 cursor-pointer transition-colors" :class="(hoverRating >= i || rating >= i) ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600'" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        </template>
                                    </div>
                                    @error('rating')
                                        <p class="text-rose-500 text-sm mb-4">{{ $message }}</p>
                                    @enderror

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Write a public review (Optional)</label>
                                        <textarea name="comment" rows="3" placeholder="Tell others about your experience..." class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500"></textarea>
                                        @error('comment')
                                            <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" :disabled="rating === 0" class="px-6 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-200 text-white dark:text-slate-900 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl font-bold transition-colors">
                                        Submit Review
                                    </button>
                                </form>
                            </div>
                        @elseif(auth()->check() && auth()->user()->user_type === 'tenant' && isset($hasReviewed) && $hasReviewed)
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-medium">You have already reviewed this property. Thank you!</p>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Right Side: Sticky Action Card -->
                <div class="relative">
                    <div class="sticky top-12">
                        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-2xl rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700">
                            
                            <!-- Price -->
                            <div class="mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                                <span class="text-4xl font-extrabold text-slate-900 dark:text-white">₱{{ number_format($property->monthly_rent, 0) }}</span>
                                <span class="text-lg text-slate-500 dark:text-slate-400 font-medium"> / month</span>
                                
                                @if($property->security_deposit)
                                <p class="text-sm text-slate-500 mt-2">
                                    + ₱{{ number_format($property->security_deposit, 0) }} security deposit
                                </p>
                                @endif
                            </div>

                            <!-- Landlord Info -->
                            <div class="mb-8">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Listed By</h3>
                                <a href="{{ route('host.show', $property->owner) }}" class="flex items-center gap-4 group cursor-pointer block">
                                    @if($property->owner->profile_image)
                                        <img src="{{ Storage::url($property->owner->profile_image) }}" alt="{{ $property->owner->full_name }}" class="w-14 h-14 rounded-full border-2 border-white shadow-sm group-hover:scale-105 transition-transform object-cover">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($property->owner->full_name) }}&background=10b981&color=fff" alt="{{ $property->owner->full_name }}" class="w-14 h-14 rounded-full border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                    @endif
                                    <div>
                                        <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-rose-600 transition-colors">{{ $property->owner->full_name }}</h4>
                                        <p class="text-sm text-slate-500">Joined {{ $property->owner->created_at ? $property->owner->created_at->format('M Y') : 'recently' }}</p>
                                    </div>
                                </a>
                            </div>

                            <!-- Action Buttons / Booking Form -->
                            <div class="space-y-4">
                                @if(auth()->check() && auth()->id() === $property->owner_id)
                                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl text-center border border-slate-200 dark:border-slate-600">
                                        <p class="text-slate-600 dark:text-slate-400 font-medium">This is your property.</p>
                                        <a href="{{ route('properties.edit', $property) }}" class="mt-2 inline-block text-rose-600 hover:text-rose-700 font-bold">Edit Property</a>
                                    </div>
                                @else
                                    <form action="{{ route('applications.store', $property) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Move-in Date (Optional)</label>
                                            <input type="date" name="move_in_date" min="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                        </div>
                                        <div>
                                            @php
                                                $ownerFirstName = explode(' ', trim($property->owner->full_name ?? $property->owner->username))[0];
                                            @endphp
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Message to {{ $ownerFirstName }} (Optional)</label>
                                            <textarea name="message" rows="2" placeholder="Hi! I'm interested in renting..." class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500"></textarea>
                                        </div>
                                        <button type="submit" class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-lg shadow-lg shadow-rose-600/30 transition-all hover:-translate-y-0.5 active:translate-y-0">
                                            Request to Book
                                        </button>
                                    </form>
                                    <a href="{{ route('messages.show', $property->owner) }}" class="w-full flex justify-center py-4 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-900 dark:text-white rounded-xl font-bold text-lg border border-slate-300 dark:border-slate-600 transition-colors">
                                        Message {{ $ownerFirstName }}
                                    </a>
                                @endif
                            </div>

                            <p class="text-center text-xs text-slate-500 mt-6 flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Secure platform application
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
