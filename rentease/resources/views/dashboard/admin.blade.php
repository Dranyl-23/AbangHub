<x-app-layout>
    <div class="bg-transparent min-h-screen pb-16 text-slate-900 dark:text-white transition-colors duration-300">
        
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">
                        Superadmin Dashboard
                    </h1>
                    <p class="text-base text-slate-500 dark:text-slate-400">
                        Manage the platform, users, properties, and system health.
                    </p>
                </div>
                
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        Manage Users
                    </a>
                    <a href="{{ route('admin.properties.index') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                        Moderate Properties
                    </a>
                    <a href="{{ route('log-viewer.index') }}" target="_blank" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                        System Logs <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>

        <!-- Stats -->
        <div class="w-full mb-12">
            <h2 class="text-xl font-bold mb-4">Platform Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Platform Revenue (5%)</p>
                    <p class="text-2xl font-semibold text-emerald-600">₱{{ number_format($stats['platformRevenue'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Transaction Volume</p>
                    <p class="text-2xl font-semibold">₱{{ number_format($stats['totalPlatformVolume'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Active Leases</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ $stats['activeLeases'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Pending Maintenance</p>
                    <p class="text-2xl font-semibold text-amber-600">{{ $stats['pendingMaintenance'] }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Users</p>
                    <p class="text-2xl font-semibold">{{ $stats['totalUsers'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Properties</p>
                    <p class="text-2xl font-semibold">{{ $stats['totalProperties'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Transactions</p>
                    <p class="text-2xl font-semibold">{{ $stats['totalTransactions'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending KYC -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold tracking-tight">Pending KYC Verifications</h2>
                @if(count($pendingKyc) > 0)
                    <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400 py-1 px-3 rounded-full text-xs font-semibold">{{ count($pendingKyc) }} Pending</span>
                @endif
            </div>
            
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($pendingKyc as $doc)
                        <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div>
                                    <p class="text-[15px] font-semibold">{{ $doc->user->full_name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Type: <span class="font-medium uppercase">{{ str_replace('_', ' ', $doc->document_type) }}</span></p>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0 items-center">
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="px-4 py-1.5 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white underline text-sm font-medium">View ID</a>
                                <form action="{{ route('admin.kyc.approve', $doc) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-1.5 bg-black dark:bg-white text-white dark:text-black rounded-lg text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">Approve</button>
                                </form>
                                <form action="{{ route('admin.kyc.reject', $doc) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-1.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Reject</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                            No pending KYC verifications right now.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

