<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Maintenance Requests</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Manage and track repair requests from your tenants.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($requests->isEmpty())
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7M5 13l4-4M19 7l-4 4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No maintenance requests</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto">None of your tenants have reported any issues yet. Great job keeping your properties in good condition!</p>
                </div>
            @else
                <!-- Kanban/List View -->
                <div class="space-y-6">
                    @foreach($requests as $request)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col md:flex-row">
                            
                            <!-- Image Section -->
                            <div class="md:w-64 md:flex-shrink-0 relative">
                                @if($request->image_path)
                                    <img src="{{ Storage::url($request->image_path) }}" alt="{{ $request->title }}" class="w-full h-48 md:h-full object-cover">
                                @else
                                    <div class="w-full h-48 md:h-full bg-slate-100 dark:bg-slate-700 flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-sm font-medium">No Image</span>
                                    </div>
                                @endif
                                
                                <!-- Status Badge Mobile -->
                                <div class="absolute top-4 right-4 md:hidden">
                                    @if($request->status === 'pending')
                                        <span class="px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full shadow-md">Pending</span>
                                    @elseif($request->status === 'in_progress')
                                        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full shadow-md">In Progress</span>
                                    @else
                                        <span class="px-3 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-md">Resolved</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Content Section -->
                            <div class="p-6 md:p-8 flex-1 flex flex-col">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $request->title }}</h3>
                                        <p class="text-sm font-medium text-rose-600 dark:text-rose-400 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $request->property->title }}
                                        </p>
                                    </div>
                                    <!-- Status Badge Desktop -->
                                    <div class="hidden md:block">
                                        @if($request->status === 'pending')
                                            <span class="px-4 py-1.5 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-sm font-bold rounded-full border border-amber-200 dark:border-amber-800">Pending</span>
                                        @elseif($request->status === 'in_progress')
                                            <span class="px-4 py-1.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-sm font-bold rounded-full border border-blue-200 dark:border-blue-800">In Progress</span>
                                        @else
                                            <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-sm font-bold rounded-full border border-emerald-200 dark:border-emerald-800">Resolved</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="text-slate-600 dark:text-slate-400 text-base mb-6 flex-1">{{ $request->description }}</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-auto pt-6 border-t border-slate-100 dark:border-slate-700/50">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden flex-shrink-0 mr-3">
                                            @if($request->user->profile_image)
                                                <img src="{{ Storage::url($request->user->profile_image) }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($request->user->full_name ?? $request->user->username) }}&background=64748b&color=fff" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Reported By</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $request->user->full_name ?? $request->user->username }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center sm:justify-end gap-3">
                                        <a href="{{ route('messages.show', $request->user) }}" class="p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition-colors" title="Message Tenant">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </a>
                                        
                                        <!-- Update Status Form -->
                                        <form action="{{ route('landlord.maintenance.update', $request) }}" method="POST" class="flex items-center gap-2" x-data x-ref="form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" @change="$refs.form.submit()" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 shadow-sm font-medium py-2.5 pl-4 pr-10">
                                                <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Set Pending</option>
                                                <option value="in_progress" {{ $request->status === 'in_progress' ? 'selected' : '' }}>Set In Progress</option>
                                                <option value="resolved" {{ $request->status === 'resolved' ? 'selected' : '' }}>Set Resolved</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
