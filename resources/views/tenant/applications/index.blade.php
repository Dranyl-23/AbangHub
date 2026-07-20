<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">My Applications</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Track the status of your rental requests.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6">
                @forelse($applications as $application)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col md:flex-row transition-shadow hover:shadow-md">
                        
                        <!-- Property Image -->
                        <div class="w-full md:w-64 h-48 md:h-auto shrink-0 bg-slate-100 dark:bg-slate-700 relative">
                            @if($application->property->primaryImage)
                                <img src="{{ Storage::url($application->property->primaryImage->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                                        <a href="{{ route('properties.show', $application->property) }}" class="hover:text-rose-600 transition-colors">
                                            {{ $application->property->title }}
                                        </a>
                                    </h2>
                                    
                                    <!-- Status Badge -->
                                    @if($application->status === 'pending')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-xs font-bold tracking-wide uppercase">Pending Review</span>
                                    @elseif($application->status === 'approved')
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full text-xs font-bold tracking-wide uppercase">Approved</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-bold tracking-wide uppercase">Rejected</span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $application->property->address }}, {{ $application->property->city }}
                                </p>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Rent</p>
                                        <p class="font-semibold text-slate-900 dark:text-white">₱{{ number_format($application->property->monthly_rent, 0) }} / mo</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Move-in Date</p>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $application->move_in_date ? \Carbon\Carbon::parse($application->move_in_date)->format('M d, Y') : 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-4 mt-2">
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    Applied {{ $application->created_at->diffForHumans() }}
                                </span>
                                
                                @if($application->status === 'approved')
                                    @php
                                        $lease = \App\Models\Lease::where('tenant_id', auth()->id())
                                            ->where('property_id', $application->property_id)
                                            ->first();
                                    @endphp
                                    @if($lease && $lease->status === 'pending_signature')
                                        <a href="{{ route('tenant.leases.sign', $lease) }}" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm inline-block">
                                            Review & Sign Lease
                                        </a>
                                    @elseif($lease && $lease->status === 'active')
                                        <a href="{{ route('tenant.invoices.index') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm inline-block">
                                            View Invoices
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="mx-auto w-20 h-20 bg-rose-50 dark:bg-slate-700 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-rose-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Applications Yet</h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">You haven't requested to book any properties yet. Start browsing to find your perfect home.</p>
                        <a href="{{ route('home') }}" class="inline-flex px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-medium transition-colors shadow-sm">
                            Browse Properties
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
