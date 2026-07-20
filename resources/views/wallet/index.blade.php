<x-app-layout>
    <div class="bg-slate-50 dark:bg-slate-900 min-h-screen pb-16 pt-8 text-slate-900 dark:text-white transition-colors duration-300">
        
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-500 dark:from-white dark:to-slate-400">My Wallet</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">
                        Manage your earnings and transfer funds securely.
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-8 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl flex items-start gap-4 shadow-sm animate-fade-in-down">
                    <div class="bg-emerald-100 dark:bg-emerald-800/50 p-2 rounded-full">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="mt-1">
                        <h4 class="font-bold text-emerald-900 dark:text-emerald-300">Success</h4>
                        <p class="font-medium text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 px-5 py-4 rounded-2xl flex items-start gap-4 shadow-sm animate-fade-in-down">
                    <div class="bg-rose-100 dark:bg-rose-800/50 p-2 rounded-full">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="mt-1">
                        <h4 class="font-bold text-rose-900 dark:text-rose-300">Transaction Failed</h4>
                        <p class="font-medium text-sm mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: Balance & Withdrawal Form -->
                <div class="lg:col-span-7 space-y-8">
                    
                    <!-- Premium Balance Card (Glassmorphism/Gradient) -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl transition-transform hover:scale-[1.02] duration-300 ease-out">
                        <!-- Colorful Background Mesh -->
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-rose-900 z-0"></div>
                        <div class="absolute inset-0 opacity-40 z-0 mix-blend-overlay bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                        <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-rose-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 z-0"></div>
                        <div class="absolute -top-24 -left-24 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 z-0"></div>
                        
                        <!-- Card Content -->
                        <div class="relative z-10 p-8 flex flex-col h-full justify-between backdrop-blur-sm">
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <p class="text-white/70 font-semibold mb-1 text-xs uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        Available Balance
                                    </p>
                                    <h2 class="text-5xl font-black text-white tracking-tight drop-shadow-md">₱{{ number_format($wallet->balance, 2) }}</h2>
                                </div>
                                
                                <div class="bg-white/20 backdrop-blur-md p-3 rounded-2xl shadow-inner border border-white/10">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-white/20 pt-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10">
                                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->first_name ?? 'L', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white/60 text-xs font-semibold uppercase">Account Holder</p>
                                        <p class="text-white font-bold text-sm tracking-wide">{{ Auth::user()->full_name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-white/60 text-xs font-semibold uppercase">Total Earnings</p>
                                    <p class="font-bold text-white text-sm">₱{{ number_format($totalEarnings, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Withdrawal Form -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 p-8 shadow-xl shadow-slate-200/50 dark:shadow-none relative overflow-hidden">
                        <!-- Decorative element -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 dark:bg-slate-700/30 rounded-bl-[100px] -z-10"></div>
                        
                        <div class="flex items-center gap-3 mb-8">
                            <div class="p-2 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold">Withdraw Funds</h3>
                        </div>
                        
                        <form action="{{ route('wallet.withdraw') }}" method="POST" class="space-y-8">
                            @csrf
                            
                            <!-- Amount Input -->
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wider">Amount to Withdraw (PHP)</label>
                                <div class="relative flex items-center">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                        <span class="text-2xl text-slate-400 font-bold group-focus-within:text-rose-500 transition-colors">₱</span>
                                    </div>
                                    <input type="number" name="amount" min="500" max="{{ $wallet->balance }}" step="0.01" 
                                           class="pl-14 w-full h-16 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 text-3xl text-slate-900 dark:text-white font-extrabold focus:border-rose-500 focus:ring-0 focus:bg-white dark:focus:bg-slate-800 transition-all shadow-inner" 
                                           placeholder="0.00" required>
                                </div>
                                <div class="flex justify-between items-center mt-2 px-1">
                                    <p class="text-xs text-slate-500 font-medium">Minimum: ₱500.00</p>
                                    <button type="button" onclick="document.querySelector('input[name=amount]').value = {{ $wallet->balance }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">Withdraw Max</button>
                                </div>
                                @error('amount') <p class="text-sm text-rose-600 mt-2 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Payout Method Selection -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wider">Select Payout Method</label>
                                <div class="grid grid-cols-3 gap-4">
                                    
                                    <label class="relative flex flex-col items-center justify-center p-5 border-2 border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-blue-300 dark:hover:border-blue-700 [&:has(:checked)]:border-blue-500 [&:has(:checked)]:bg-blue-50 dark:[&:has(:checked)]:bg-blue-900/20 [&:has(:checked)]:shadow-md transition-all group overflow-hidden">
                                        <input type="radio" name="method" value="gcash" class="sr-only" required>
                                        <div class="h-10 mb-3 flex items-center justify-center group-[&:has(:checked)]:scale-110 transition-transform">
                                            <img src="{{ asset('images/gcash.png') }}" alt="GCash Logo" class="h-8 object-contain">
                                        </div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 group-[&:has(:checked)]:text-blue-600 dark:group-[&:has(:checked)]:text-blue-400">GCash</span>
                                    </label>

                                    <label class="relative flex flex-col items-center justify-center p-5 border-2 border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-emerald-300 dark:hover:border-emerald-700 [&:has(:checked)]:border-emerald-500 [&:has(:checked)]:bg-emerald-50 dark:[&:has(:checked)]:bg-emerald-900/20 [&:has(:checked)]:shadow-md transition-all group overflow-hidden">
                                        <input type="radio" name="method" value="maya" class="sr-only">
                                        <div class="h-10 mb-3 flex items-center justify-center group-[&:has(:checked)]:scale-110 transition-transform">
                                            <img src="{{ asset('images/maya.png') }}" alt="Maya Logo" class="h-7 object-contain">
                                        </div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 group-[&:has(:checked)]:text-emerald-600 dark:group-[&:has(:checked)]:text-emerald-400">Maya</span>
                                    </label>

                                    <label class="relative flex flex-col items-center justify-center p-5 border-2 border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-slate-400 dark:hover:border-slate-500 [&:has(:checked)]:border-slate-800 dark:[&:has(:checked)]:border-white [&:has(:checked)]:bg-slate-100 dark:[&:has(:checked)]:bg-slate-800 [&:has(:checked)]:shadow-md transition-all group overflow-hidden">
                                        <input type="radio" name="method" value="bank" class="sr-only">
                                        <div class="w-10 h-10 mb-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full flex items-center justify-center group-[&:has(:checked)]:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                        </div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">Bank</span>
                                    </label>

                                </div>
                                @error('method') <p class="text-sm text-rose-600 mt-2 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <!-- Account Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Account Name</label>
                                    <input type="text" name="account_name" value="{{ old('account_name', Auth::user()->full_name) }}" 
                                           class="w-full h-12 px-4 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 font-medium transition-colors" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Account Number</label>
                                    <input type="text" name="account_number" 
                                           class="w-full h-12 px-4 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 font-bold tracking-widest transition-colors" 
                                           placeholder="09XX XXX XXXX" required>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-slate-900 to-slate-800 hover:from-black hover:to-slate-900 dark:from-rose-600 dark:to-rose-500 dark:hover:from-rose-500 dark:hover:to-rose-400 text-white rounded-2xl font-bold text-lg shadow-xl shadow-slate-900/20 dark:shadow-rose-900/20 transition-all hover:-translate-y-1 active:translate-y-0 flex justify-center items-center gap-2">
                                Confirm & Withdraw
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </button>
                            
                            <p class="text-center text-xs text-slate-500 font-medium">
                                Secured by <span class="font-bold text-slate-700 dark:text-slate-300">RentEase Pay</span>. Funds usually arrive within 5-10 minutes.
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Right: Transaction History (Payouts) -->
                <div class="lg:col-span-5">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 overflow-hidden shadow-xl shadow-slate-200/50 dark:shadow-none sticky top-24">
                        <div class="p-8 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/50">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Recent Withdrawals</h3>
                            <p class="text-sm text-slate-500 mt-1">Track your payout status.</p>
                        </div>
                        
                        <div class="p-0 max-h-[600px] overflow-y-auto">
                            <ul class="divide-y divide-slate-100 dark:divide-slate-700/50">
                                @forelse($payoutRequests as $payout)
                                    <li class="p-6 hover:bg-slate-50 dark:hover:bg-slate-750/50 transition-colors group">
                                        <div class="flex items-center gap-4">
                                            
                                            <!-- Status Icon -->
                                            <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center 
                                                @if($payout->status === 'approved') bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400
                                                @elseif($payout->status === 'rejected') bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400
                                                @else bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 @endif
                                            ">
                                                @if($payout->status === 'approved')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                @elseif($payout->status === 'rejected')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                @else
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </div>
                                            
                                            <!-- Details -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-0.5">
                                                    <span class="font-black text-lg text-slate-900 dark:text-white">-₱{{ number_format($payout->amount, 0) }}</span>
                                                    <span class="text-[10px] uppercase tracking-widest font-black 
                                                        @if($payout->status === 'approved') text-emerald-600 dark:text-emerald-400
                                                        @elseif($payout->status === 'rejected') text-rose-600 dark:text-rose-400
                                                        @else text-amber-600 dark:text-amber-400 @endif
                                                    ">
                                                        {{ $payout->status }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-slate-600 dark:text-slate-400 truncate">
                                                    To <span class="capitalize font-bold">{{ $payout->method }}</span> ••{{ substr($payout->account_number, -4) }}
                                                </p>
                                                <p class="text-xs text-slate-400 mt-1 font-medium">{{ $payout->created_at->format('M d, Y • h:i A') }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center flex flex-col items-center justify-center py-16">
                                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="font-bold text-slate-700 dark:text-slate-300">No withdrawals yet</p>
                                        <p class="text-sm text-slate-500 mt-1">Your payout history will appear here.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
