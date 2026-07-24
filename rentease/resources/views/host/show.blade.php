<x-app-layout>
    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Left Sidebar: Host Info -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700/50 flex flex-col items-center text-center">
                        
                        <div class="relative mb-6">
                            @if($user->profile_image)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-32 h-32 rounded-full border-4 border-white dark:border-slate-800 shadow-md object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=10b981&color=fff&size=200" alt="{{ $user->full_name }}" class="w-32 h-32 rounded-full border-4 border-white dark:border-slate-800 shadow-md">
                            @endif
                            
                            @if($user->documents && $user->documents->count() > 0)
                                <!-- Verified Badge -->
                                <div class="absolute bottom-0 right-0 bg-white dark:bg-slate-800 rounded-full p-1 shadow-sm" title="Verified Business">
                                    <div class="bg-blue-500 rounded-full p-1.5 text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">{{ $user->full_name }}</h1>
                        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-6">Host / Landlord</p>
                        
                        <div class="w-full space-y-4 text-left border-t border-slate-100 dark:border-slate-700/50 pt-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Joined</span>
                                <span class="font-medium text-slate-900 dark:text-white">{{ $user->created_at ? $user->created_at->format('Y') : 'Recently' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Identity</span>
                                @if($user->documents && $user->documents->count() > 0)
                                    <span class="font-medium text-blue-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944a11.954 11.954 0 007.834 3.055 11.924 11.924 0 00.382 3.016 11.922 11.922 0 01-8.216 10.972 11.922 11.922 0 01-8.216-10.972 11.924 11.924 0 00.382-3.016zM10 12l-3-3 1.4-1.4 1.6 1.6 3.6-3.6 1.4 1.4-5 5z" clip-rule="evenodd"></path></svg>
                                        Business Verified
                                    </span>
                                @else
                                    <span class="font-medium text-slate-500 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                        Unverified
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400">Response rate</span>
                                <span class="font-medium text-slate-900 dark:text-white">100%</span>
                            </div>
                        </div>

                        <div class="w-full mt-8">
                            <a href="{{ route('messages.show', $user) }}" class="w-full flex justify-center items-center gap-2 py-3.5 bg-slate-900 hover:bg-slate-800 dark:bg-rose-600 dark:hover:bg-rose-700 text-white rounded-xl font-bold text-sm shadow-lg transition-transform hover:-translate-y-0.5 active:translate-y-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                Contact Host
                            </a>
                        </div>
                        
                    </div>
                </div>

                <!-- Right Side: Content & Properties -->
                <div class="lg:col-span-2">
                    
                    <div class="mb-12">
                        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-6">Hi, I'm {{ explode(' ', $user->full_name)[0] }}</h2>
                        <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-400 text-lg leading-relaxed">
                            <p>Welcome to my profile! I take pride in providing clean, safe, and comfortable spaces for my tenants. Whether you're looking for a cozy room or a spacious apartment, I'm committed to ensuring you have a great stay.</p>
                        </div>
                    </div>

                    <div class="pt-10 border-t border-slate-200 dark:border-slate-700/50">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">{{ $user->full_name }}'s Listings</h3>
                        
                        @if($user->properties->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($user->properties as $property)
                                    <div class="group bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100 dark:border-slate-700/50 flex flex-col">
                                        <!-- Image -->
                                        <div class="relative aspect-[4/3] bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            @if($property->primaryImage)
                                                <img src="{{ asset($property->primaryImage->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                                </div>
                                            @endif
                                            
                                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg text-xs font-bold text-slate-900 shadow-sm uppercase tracking-wider">
                                                {{ str_replace('_', ' ', $property->property_type) }}
                                            </div>
                                        </div>

                                        <!-- Details -->
                                        <div class="p-5 flex-1 flex flex-col">
                                            <a href="{{ route('properties.show', $property) }}" class="block flex-1">
                                                <h4 class="text-lg font-bold text-slate-900 dark:text-white truncate group-hover:text-rose-600 transition-colors">{{ $property->title }}</h4>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $property->city }}, {{ $property->province }}</p>
                                                <div class="mt-4 flex items-center justify-between">
                                                    <div class="flex text-sm text-slate-500 gap-3">
                                                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg> {{ $property->bedrooms }}</span>
                                                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> {{ $property->bathrooms }}</span>
                                                    </div>
                                                    <div class="text-lg font-bold text-slate-900 dark:text-white">
                                                        ₱{{ number_format($property->monthly_rent, 0) }}<span class="text-xs font-normal text-slate-500">/mo</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-200 dark:border-slate-700 p-10 text-center">
                                <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <p class="text-slate-500 dark:text-slate-400">This host doesn't have any available properties at the moment.</p>
                            </div>
                        @endif
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
