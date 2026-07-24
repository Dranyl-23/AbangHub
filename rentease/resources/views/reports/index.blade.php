<x-app-layout>
    <div class="bg-slate-50 dark:bg-slate-900 min-h-screen pb-16 pt-8 text-slate-900 dark:text-white transition-colors duration-300">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight">Income & Reports</h1>
                    <p class="text-base text-slate-500 dark:text-slate-400 mt-1">
                        Track your earnings and monitor property performance.
                    </p>
                </div>
                
                <a href="{{ route('reports.export') }}" class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm text-sm font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </a>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Income</p>
                    <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">₱{{ number_format($totalRevenue, 0) }}</h3>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pending Receivables</p>
                    <h3 class="text-3xl font-bold text-amber-600 dark:text-amber-500 mt-2">₱{{ number_format($pendingPayments, 0) }}</h3>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Properties Generating Income</p>
                    <h3 class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ $activeLeases }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Chart Area -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
                    <h2 class="text-xl font-bold mb-6">Revenue Over Time (Last 6 Months)</h2>
                    <div class="relative h-80 w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Property Breakdown -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
                    <h2 class="text-xl font-bold mb-6">Income by Property</h2>
                    
                    @if($propertyBreakdown->count() > 0)
                        <div class="space-y-6">
                            @foreach($propertyBreakdown as $pb)
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="font-medium text-slate-900 dark:text-white truncate pr-4">{{ $pb->property?->title ?? 'Deleted Property' }}</span>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400 shrink-0">₱{{ number_format($pb->total, 0) }}</span>
                                    </div>
                                    @php
                                        // Calculate percentage for the bar (max 100%)
                                        $percentage = $totalRevenue > 0 ? ($pb->total / $totalRevenue) * 100 : 0;
                                    @endphp
                                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                        <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-48 text-center text-slate-500">
                            <svg class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <p>No income generated yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Load Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            // Get data from Laravel controller
            const rawData = @json($monthlyRevenue);
            
            // Prepare arrays for Chart.js
            const labels = rawData.map(item => item.month);
            const data = rawData.map(item => item.amount);

            // Determine colors based on dark mode class on html tag
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? '#334155' : '#e2e8f0';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Income (₱)',
                        data: data,
                        backgroundColor: '#f43f5e', // Rose 500
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#fff',
                            titleColor: isDark ? '#fff' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#475569',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '₱ ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                                drawBorder: false,
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    if(value >= 1000) return '₱' + (value/1000) + 'k';
                                    return '₱' + value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
