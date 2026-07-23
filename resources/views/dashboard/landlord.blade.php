<x-app-layout>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="bg-transparent min-h-screen pb-16 text-slate-900 dark:text-white transition-colors duration-300">
        
        <!-- Minimalist Header (Airbnb Style) -->
        <div class="w-full pt-6 pb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">
                        Welcome back, {{ explode(' ', Auth::user()->full_name ?? Auth::user()->username)[0] }}
                    </h1>
                    <p class="text-base text-slate-500 dark:text-slate-400">
                        Here's an overview of your properties and business.
                    </p>
                </div>
                
                <!-- Call to action buttons -->
                <div class="shrink-0 flex gap-3 mt-2 md:mt-0">
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm text-sm font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Manage Properties
                    </a>
                    <a href="{{ route('properties.create') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 transition-colors">
                        <svg class="-ml-1 mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                        Add Listing
                    </a>
                </div>
            </div>
        </div>

        <!-- KYC Banner -->
        @if(!Auth::user()->is_verified)
            @php
                $pendingDoc = Auth::user()->documents()->where('status', 'pending')->first();
            @endphp
            <div class="w-full mb-8 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-6 shadow-sm">
                @if($pendingDoc)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-800 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-amber-900 dark:text-amber-400">Verification Pending</h3>
                            <p class="text-sm text-amber-700 dark:text-amber-300">Your ID is currently being reviewed by our team. Please wait for approval to get your Verified badge.</p>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-800 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-amber-900 dark:text-amber-400">Get Verified</h3>
                                <p class="text-sm text-amber-700 dark:text-amber-300">Upload a Valid ID to get the "Verified Landlord" badge. This increases tenant trust and bookings.</p>
                            </div>
                        </div>
                        <form action="{{ route('landlord.compliance.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
                            @csrf
                            <input type="hidden" name="document_type" value="valid_id">
                            <input type="file" name="document_file" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer">
                            <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white rounded-full text-sm font-bold shadow-sm hover:bg-amber-700 transition-colors whitespace-nowrap">Upload ID</button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        <!-- Clean, Subtle Stats -->
        <div class="w-full mb-12">
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Net Income</p>
                    <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">₱{{ number_format($stats['netIncome'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Revenue</p>
                    <p class="text-2xl font-semibold text-slate-900 dark:text-white">₱{{ number_format($stats['totalRevenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Expenses</p>
                    <p class="text-2xl font-semibold text-rose-600 dark:text-rose-400">₱{{ number_format($stats['totalExpenses'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Pending Payments</p>
                    <p class="text-2xl font-semibold text-amber-600 dark:text-amber-400">₱{{ number_format($stats['pendingPayments'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Vacant Units</p>
                    <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $stats['vacantUnits'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Left Side: Main Content (Takes 2 cols) -->
                <div class="lg:col-span-2 space-y-12">
                    
                    <!-- Analytics Section -->
                    <div>
                        <h2 class="text-2xl font-semibold tracking-tight mb-6">Financial Analytics</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                            <!-- Income vs Expenses Chart -->
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
                                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-4 uppercase tracking-wider">Income vs Expenses (6 Mos)</h3>
                                <div class="relative h-64 w-full">
                                    <canvas id="incomeExpenseChart"></canvas>
                                </div>
                            </div>

                            <!-- Occupancy Rate Chart -->
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm flex flex-col">
                                <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-4 uppercase tracking-wider">Occupancy Rate</h3>
                                <div class="relative h-64 w-full flex-grow flex items-center justify-center">
                                    <canvas id="occupancyChart"></canvas>
                                    <!-- Center text -->
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                                        <span class="text-3xl font-bold text-slate-900 dark:text-white">{{ $occupancyRate }}%</span>
                                        <span class="text-xs text-slate-500">Rented</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Applications -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Pending Applications</h2>
                            @if(count($pendingApplications) > 0)
                                <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400 py-1 px-3 rounded-full text-xs font-semibold">{{ count($pendingApplications) }} New</span>
                            @endif
                        </div>
                        
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($pendingApplications as $app)
                                    <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 shrink-0 rounded-full overflow-hidden bg-slate-200">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($app->user->full_name) }}&background=e2e8f0&color=0f172a" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <p class="text-[15px] font-semibold">{{ $app->user->full_name }}</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Applied for <span class="text-slate-900 dark:text-white font-medium">{{ $app->property->title }}</span></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 shrink-0">
                                            <form action="{{ route('applications.updateStatus', $app) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="px-4 py-1.5 bg-black dark:bg-white text-white dark:text-black rounded-lg text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">Approve</button>
                                            </form>
                                            <form action="{{ route('applications.updateStatus', $app) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="px-4 py-1.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Decline</button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        No pending applications right now.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Active Tenants & Reviews -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Active Tenants</h2>
                        </div>
                        
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($activeLeases as $lease)
                                    <li class="p-5 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 shrink-0 rounded-full overflow-hidden bg-slate-200">
                                                    @if($lease->tenant->profile_image)
                                                        <img src="{{ Storage::url($lease->tenant->profile_image) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($lease->tenant->full_name) }}&background=e2e8f0&color=0f172a" class="w-full h-full object-cover">
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-[15px] font-semibold">{{ $lease->tenant->full_name }}</p>
                                                        @if($lease->tenant->average_tenant_rating > 0)
                                                            <div class="flex items-center gap-1 text-sm bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-500 px-2 rounded-full">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                                <span class="font-bold">{{ number_format($lease->tenant->average_tenant_rating, 1) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Renting <span class="text-slate-900 dark:text-white font-medium">{{ $lease->property->title }}</span></p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 shrink-0" x-data="{ showReviewModal: false }">
                                                <a href="{{ route('leases.download', $lease) }}" class="px-4 py-1.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-lg text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors flex items-center gap-1.5" title="Download Contract">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    <span class="hidden sm:inline">Contract</span>
                                                </a>
                                                <a href="{{ route('messages.show', $lease->tenant) }}" class="px-4 py-1.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Message</a>
                                                @php
                                                    $hasReviewedTenant = \App\Models\TenantReview::where('landlord_id', auth()->id())->where('tenant_id', $lease->tenant_id)->exists();
                                                @endphp
                                                @if(!$hasReviewedTenant)
                                                    <button @click="showReviewModal = true" class="px-4 py-1.5 bg-yellow-400 text-slate-900 rounded-lg text-sm font-bold hover:bg-yellow-500 transition-colors shadow-sm">Rate Tenant</button>
                                                @else
                                                    <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-medium border border-emerald-200">Rated</span>
                                                @endif
                                                
                                                <!-- Tenant Review Modal -->
                                                <div x-show="showReviewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                        <div x-show="showReviewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showReviewModal = false" aria-hidden="true"></div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div x-show="showReviewModal" x-transition:enter="ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200 transform" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                                            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                <div class="sm:flex sm:items-start">
                                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                        <h3 class="text-xl leading-6 font-bold text-slate-900 dark:text-white mb-4" id="modal-title">Rate {{ $lease->tenant->full_name }}</h3>
                                                                        <form action="{{ route('tenants.reviews.store', $lease->tenant) }}" method="POST" x-data="{ rating: 0, hoverRating: 0 }">
                                                                            @csrf
                                                                            <input type="hidden" name="rating" x-model="rating">
                                                                            
                                                                            <div class="flex items-center justify-center sm:justify-start mb-6">
                                                                                <template x-for="i in 5">
                                                                                    <svg @click="rating = i" @mouseenter="hoverRating = i" @mouseleave="hoverRating = 0" class="w-10 h-10 cursor-pointer transition-colors" :class="(hoverRating >= i || rating >= i) ? 'text-yellow-400' : 'text-slate-200 dark:text-slate-600'" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                                                </template>
                                                                            </div>
                                                                            
                                                                            <div class="mb-4 text-left">
                                                                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Feedback (Optional)</label>
                                                                                <textarea name="comment" rows="3" placeholder="Is this tenant a good payer? Does they keep the house clean?" class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500"></textarea>
                                                                            </div>
                                                                            
                                                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                                                <button type="submit" :disabled="rating === 0" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-rose-600 text-base font-bold text-white hover:bg-rose-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">Submit Review</button>
                                                                                <button type="button" @click="showReviewModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        You don't have any active tenants right now.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-1 space-y-12">
                    
                    <!-- Clean Call to Action -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 text-center">
                        <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Expand Your Portfolio</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Add more properties to attract more tenants and maximize your monthly revenue.</p>
                        <a href="{{ route('properties.create') }}" class="block w-full py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">List a new property</a>
                    </div>

                    <!-- Unread Messages -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Messages</h2>
                            <a href="{{ route('messages.index') }}" class="text-sm font-medium underline decoration-1 hover:text-slate-600 dark:hover:text-slate-300">View All</a>
                        </div>
                        
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($recentMessages as $msg)
                                    <li class="p-5 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors cursor-pointer">
                                        <a href="{{ route('messages.show', $msg->sender) }}" class="flex items-center gap-4">
                                            <div class="relative shrink-0">
                                                <img class="h-10 w-10 rounded-full object-cover bg-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode($msg->sender->full_name) }}&background=e2e8f0&color=0f172a">
                                                <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-800"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[15px] font-semibold truncate">{{ $msg->sender->full_name }}</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $msg->content }}</p>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        No unread messages.
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart text color depending on dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? '#334155' : '#f1f5f9';

        // 1. Income vs Expenses Bar Chart
        const ctxBar = document.getElementById('incomeExpenseChart').getContext('2d');
        const chartData = @json($chartData);
        
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Income (₱)',
                        data: chartData.income,
                        backgroundColor: '#10b981', // emerald-500
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Expenses (₱)',
                        data: chartData.expenses,
                        backgroundColor: '#f43f5e', // rose-500
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: textColor, usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-PH').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: textColor }
                    },
                    y: {
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { 
                            color: textColor,
                            callback: function(value) {
                                if (value >= 1000) return value / 1000 + 'k';
                                return value;
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. Occupancy Rate Doughnut Chart
        const ctxPie = document.getElementById('occupancyChart').getContext('2d');
        const occRate = {{ $occupancyRate }};
        const vacRate = {{ $vacantRate }};
        
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Rented', 'Vacant'],
                datasets: [{
                    data: [occRate, vacRate],
                    backgroundColor: [
                        '#10b981', // emerald-500
                        '#cbd5e1'  // slate-300
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // makes it thin
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, usePointStyle: true, padding: 20 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>

