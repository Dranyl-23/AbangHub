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

        <!-- Clean, Subtle Stats -->
        <div class="w-full mb-12">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Total Revenue</p>
                    <p class="text-2xl font-semibold">₱{{ number_format($stats['totalRevenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Monthly Revenue</p>
                    <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">₱{{ number_format($stats['monthlyRevenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Pending Payments</p>
                    <p class="text-2xl font-semibold text-amber-600 dark:text-amber-400">₱{{ number_format($stats['pendingPayments'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 hover:border-slate-300 dark:hover:border-slate-600 transition-colors">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Vacant Units</p>
                    <p class="text-2xl font-semibold text-rose-600 dark:text-rose-400">{{ $stats['vacantUnits'] ?? 0 }}</p>
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

                    <!-- Maintenance Requests -->
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold tracking-tight">Maintenance Tickets</h2>
                            @if(count($maintenanceRequests) > 0)
                                <span class="bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-400 py-1 px-3 rounded-full text-xs font-semibold">{{ count($maintenanceRequests) }} Ongoing</span>
                            @endif
                        </div>
                        
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                            <ul role="list" class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($maintenanceRequests as $req)
                                    <li class="p-5 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors" x-data="{ showForm: false }">
                                        <div class="flex items-start justify-between cursor-pointer" @click="showForm = !showForm">
                                            <div class="flex-1 min-w-0 pr-4">
                                                <h3 class="text-[15px] font-semibold hover:underline">{{ $req->title }}</h3>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $req->property->title }} • {{ $req->user->full_name }}</p>
                                            </div>
                                            @if($req->status === 'resolved')
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">Fixed (₱{{ number_format($req->cost, 0) }})</span>
                                            @elseif($req->status === 'in_progress')
                                                <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 text-xs font-medium text-blue-700 dark:text-blue-400">Working</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700 px-2.5 py-1 text-xs font-medium text-slate-700 dark:text-slate-300">Pending</span>
                                            @endif
                                        </div>
                                        
                                        <!-- Update Form (Alpine x-show) -->
                                        <div x-show="showForm" x-transition class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                            <form action="{{ route('tenant.maintenance.update', $req) }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <div class="w-full sm:w-auto flex-1">
                                                    <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                                                    <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm focus:ring-rose-500">
                                                        <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="in_progress" {{ $req->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="resolved" {{ $req->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="w-full sm:w-auto flex-1">
                                                    <label class="block text-xs font-medium text-slate-500 mb-1">Cost (₱) if fixed</label>
                                                    <input type="number" name="cost" value="{{ $req->cost > 0 ? $req->cost : '' }}" placeholder="0.00" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm focus:ring-rose-500">
                                                </div>
                                                
                                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-lg text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">
                                                    Update
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                                        All properties are in perfect condition!
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
                                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $msg->message }}</p>
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
