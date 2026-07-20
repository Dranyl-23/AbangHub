<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]" x-data="checkout()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">My Invoices</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Manage your rent payments and billing history.</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-sm font-medium text-slate-600 dark:text-slate-300">
                        Total Due: <strong class="text-rose-600 dark:text-rose-400">₱{{ number_format($invoices->where('status', 'pending')->sum('amount'), 0) }}</strong>
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($invoices as $invoice)
                        <li class="p-6 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors flex flex-col md:flex-row md:items-center gap-6">
                            
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $invoice->description }}</h3>
                                    @if($invoice->status === 'paid')
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-md text-xs font-bold tracking-wide uppercase">Paid</span>
                                    @elseif($invoice->status === 'overdue')
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-md text-xs font-bold tracking-wide uppercase">Overdue</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-md text-xs font-bold tracking-wide uppercase">Pending</span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-500 dark:text-slate-400 flex flex-wrap gap-x-6 gap-y-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $invoice->lease->property->title }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Invoice #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto">
                                <div class="text-left md:text-right">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase mb-1">Amount</p>
                                    <p class="text-xl font-black text-slate-900 dark:text-white">₱{{ number_format($invoice->amount, 0) }}</p>
                                </div>
                                
                                @if($invoice->status !== 'paid')
                                    <button type="button" @click="openModal({{ $invoice->id }}, {{ $invoice->amount }}, '{{ $invoice->description }}')" class="shrink-0 px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-600/30 transition-all active:scale-[0.98]">
                                        Pay Now
                                    </button>
                                @else
                                    <button disabled class="shrink-0 px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 font-bold rounded-xl cursor-not-allowed">
                                        Paid
                                    </button>
                                @endif
                            </div>
                            
                        </li>
                    @empty
                        <li class="p-12 text-center">
                            <div class="mx-auto w-16 h-16 bg-slate-50 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4 text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">You have no invoices at this time.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Simulated Checkout Modal (Alpine.js) -->
        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Background overlay -->
                <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                    
                    <div x-show="!isProcessing && !isSuccess">
                        <div class="px-6 py-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                            <div>
                                <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">Complete Payment</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1" x-text="description"></p>
                            </div>
                            <button @click="closeModal()" type="button" class="bg-white dark:bg-slate-800 rounded-full p-2 inline-flex items-center justify-center text-slate-400 hover:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none transition-colors border border-transparent dark:border-slate-700">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-6">
                            <div class="flex justify-between items-end mb-8">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Total Amount</span>
                                <span class="text-3xl font-black text-slate-900 dark:text-white" x-text="'₱' + new Intl.NumberFormat().format(amount)"></span>
                            </div>

                            <div class="space-y-4">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Select Payment Method</p>
                                
                                <!-- Payment Method Toggles -->
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="button" @click="paymentMethod = 'card'" :class="{'ring-2 ring-rose-500 border-rose-500 bg-rose-50 dark:bg-rose-900/20': paymentMethod === 'card', 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50': paymentMethod !== 'card'}" class="border rounded-2xl p-4 flex flex-col items-center justify-center gap-3 transition-all relative">
                                        <div x-show="paymentMethod === 'card'" class="absolute top-2 right-2 w-4 h-4 bg-rose-500 rounded-full flex items-center justify-center"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                        <svg class="w-8 h-8 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">Credit Card</span>
                                    </button>
                                    <button type="button" @click="paymentMethod = 'gcash'" :class="{'ring-2 ring-blue-500 border-blue-500 bg-blue-50 dark:bg-blue-900/20': paymentMethod === 'gcash', 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50': paymentMethod !== 'gcash'}" class="border rounded-2xl p-4 flex flex-col items-center justify-center gap-3 transition-all relative">
                                        <div x-show="paymentMethod === 'gcash'" class="absolute top-2 right-2 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">G</div>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">GCash</span>
                                    </button>
                                </div>

                                <!-- Card Details Form (Mock) -->
                                <div x-show="paymentMethod === 'card'" x-transition x-collapse class="pt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Card Number</label>
                                        <div class="relative">
                                            <input type="text" placeholder="0000 0000 0000 0000" class="block w-full pl-10 pr-4 py-3 border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Expiry Date</label>
                                            <input type="text" placeholder="MM/YY" class="block w-full px-4 py-3 border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">CVC</label>
                                            <input type="text" placeholder="123" class="block w-full px-4 py-3 border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- GCash Details Form (Mock) -->
                                <div x-show="paymentMethod === 'gcash'" x-transition x-collapse class="pt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mobile Number</label>
                                        <div class="relative">
                                            <input type="text" placeholder="09XX XXX XXXX" class="block w-full pl-10 pr-4 py-3 border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-2 flex items-center">
                                            <svg class="w-3 h-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Secure simulated payment
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 rounded-b-3xl">
                            <button @click="closeModal()" type="button" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-6 py-3 bg-white dark:bg-slate-800 text-base font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                                Cancel
                            </button>
                            <button @click="processPayment()" type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-md px-6 py-3 bg-slate-900 dark:bg-white text-base font-bold text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 sm:w-auto sm:text-sm transition-colors">
                                Pay <span x-text="'₱' + new Intl.NumberFormat().format(amount)" class="ml-1"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Processing State -->
                    <div x-show="isProcessing" class="px-6 py-12 text-center">
                        <svg class="animate-spin h-12 w-12 text-rose-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Processing Payment...</h3>
                        <p class="text-slate-500 dark:text-slate-400">Please do not close this window.</p>
                    </div>

                    <!-- Success State -->
                    <div x-show="isSuccess" style="display: none;" class="px-6 py-12 text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-6">
                            <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Payment Successful!</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-8">Your invoice has been paid successfully.</p>
                        
                        <!-- Hidden Form to submit to Laravel -->
                        <form x-ref="paymentForm" :action="'/tenant/invoices/' + invoiceId + '/pay'" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" x-model="paymentMethod">
                            <input type="hidden" name="amount" x-model="amount">
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-md px-6 py-3 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none transition-colors">
                                Return to Invoices
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkout', () => ({
                isOpen: false,
                invoiceId: null,
                amount: 0,
                description: '',
                paymentMethod: 'card',
                isProcessing: false,
                isSuccess: false,
                
                openModal(id, amt, desc) {
                    this.invoiceId = id;
                    this.amount = amt;
                    this.description = desc;
                    this.paymentMethod = 'card';
                    this.isProcessing = false;
                    this.isSuccess = false;
                    this.isOpen = true;
                },
                
                closeModal() {
                    if (this.isProcessing) return;
                    this.isOpen = false;
                },
                
                processPayment() {
                    this.isProcessing = true;
                    
                    // Simulate network request/processing time (2 seconds)
                    setTimeout(() => {
                        this.isProcessing = false;
                        this.isSuccess = true;
                        
                        // Automatically submit form after 1.5 seconds of showing success state
                        setTimeout(() => {
                            this.$refs.paymentForm.submit();
                        }, 1500);
                        
                    }, 2000);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
