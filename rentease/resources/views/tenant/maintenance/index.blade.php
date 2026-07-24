<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Maintenance Requests</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Report issues and track repairs for your rented properties.</p>
                </div>
                <a href="{{ route('tenant.maintenance.create') }}" class="inline-flex items-center px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-md shadow-rose-500/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Report Issue
                </a>
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
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No maintenance requests</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto">Everything looks good! If you find any issues in your rented property, report them here.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($requests as $request)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow group flex flex-col">
                            @if($request->image_path)
                                <div class="h-48 overflow-hidden relative">
                                    <img src="{{ asset($request->image_path) }}" alt="{{ $request->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                            @else
                                <div class="h-32 bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $request->property->title }}</span>
                                    
                                    @if($request->status === 'pending')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-xs font-bold rounded-full">Pending</span>
                                    @elseif($request->status === 'in_progress')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold rounded-full">In Progress</span>
                                    @else
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-bold rounded-full">Resolved</span>
                                    @endif
                                </div>
                                
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $request->title }}</h3>
                                <p class="text-slate-600 dark:text-slate-400 text-sm mb-6 line-clamp-3 flex-1">{{ $request->description }}</p>
                                
                                <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500">
                                    <span>Reported {{ $request->created_at->format('M d, Y') }}</span>
                                    <span class="font-medium">Ref #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
