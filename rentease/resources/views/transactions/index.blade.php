<x-app-layout>
    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="w-full">
            
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-3xl font-bold leading-7 text-slate-900 dark:text-white sm:truncate sm:text-4xl sm:tracking-tight">
                        Transactions
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Manage your payments, deposits, and financial history.</p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0 gap-3">
                    <a href="{{ route('transactions.export') }}" class="inline-flex items-center rounded-xl bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="-ml-0.5 mr-2 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Export CSV
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-8 rounded-xl bg-rose-50 dark:bg-rose-900/30 p-4 border border-rose-200 dark:border-rose-800">
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

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="rounded-3xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Completed</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">₱{{ number_format($totalReceived ?? 0, 0) }}</p>
                </div>
                <div class="rounded-3xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pending</p>
                    <p class="mt-2 text-3xl font-bold text-amber-500">₱{{ number_format($pendingAmount ?? 0, 0) }}</p>
                </div>
                <div class="hidden lg:block rounded-3xl bg-rose-600 p-6 shadow-lg shadow-rose-600/20 text-white relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/20"></div>
                    <p class="text-sm font-medium text-rose-100">Quick Tip</p>
                    <p class="mt-2 text-sm font-semibold text-white leading-relaxed">Always verify physical bank deposits before approving pending transactions.</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Details</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                            @forelse($transactions as $txn)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                        {{ $txn->created_at->format('M d, Y') }}
                                        <div class="text-xs text-slate-400">{{ $txn->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-white">{{ ucfirst($txn->type) }} Payment</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">{{ $txn->property->title ?? 'N/A' }}</div>
                                        @if(auth()->user()->user_type === 'landlord')
                                            <div class="text-xs text-rose-600 dark:text-rose-400 font-medium mt-1">From: {{ $txn->user->full_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">₱{{ number_format($txn->amount, 0) }}</div>
                                        @if($txn->reference_number)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1">Ref: {{ $txn->reference_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($txn->status === 'completed')
                                            <span class="inline-flex items-center rounded-md bg-rose-50 dark:bg-rose-900/30 px-2 py-1 text-xs font-bold text-rose-700 dark:text-rose-400 ring-1 ring-inset ring-rose-600/20">
                                                Completed
                                            </span>
                                        @elseif($txn->status === 'pending')
                                            <span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/30 px-2 py-1 text-xs font-bold text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-600/20">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-rose-50 dark:bg-rose-900/30 px-2 py-1 text-xs font-bold text-rose-700 dark:text-rose-400 ring-1 ring-inset ring-rose-600/20">
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(auth()->user()->user_type === 'landlord' && $txn->status === 'pending')
                                            <div class="flex justify-end gap-2">
                                                <form action="{{ route('transactions.updateStatus', $txn) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 font-bold bg-rose-50 dark:bg-rose-900/30 px-3 py-1 rounded-lg transition-colors">Approve</button>
                                                </form>
                                                <form action="{{ route('transactions.updateStatus', $txn) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="failed">
                                                    <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 font-bold bg-rose-50 dark:bg-rose-900/30 px-3 py-1 rounded-lg transition-colors">Reject</button>
                                                </form>
                                            </div>
                                        @elseif(auth()->user()->user_type === 'tenant' && $txn->status === 'pending')
                                            <button type="button" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 font-bold" onclick="alert('In a real app, this would open a modal to upload a GCash receipt or pay via Maya.')">
                                                Pay Now
                                            </button>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
