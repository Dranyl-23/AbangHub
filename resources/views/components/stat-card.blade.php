@props([
    'title',
    'value',
    'icon',
    'trend' => null,
    'trendValue' => null,
    'color' => 'emerald'
])

@php
$colorClasses = [
    'emerald' => 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
    'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
    'amber' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
    'red' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
];
$iconBg = $colorClasses[$color] ?? $colorClasses['emerald'];
@endphp

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 flex items-center hover:-translate-y-1 transition-transform duration-300">
    <div class="p-4 rounded-full {{ $iconBg }} mr-4">
        {!! $icon !!}
    </div>
    <div class="flex-grow">
        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</h3>
        <div class="flex items-baseline mt-1">
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
            @if($trend)
                <span class="ml-2 flex items-center text-sm font-medium {{ $trend === 'up' ? 'text-rose-600 dark:text-rose-400' : 'text-red-600 dark:text-red-400' }}">
                    @if($trend === 'up')
                        <svg class="w-4 h-4 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                    @else
                        <svg class="w-4 h-4 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                    @endif
                    {{ $trendValue }}
                </span>
            @endif
        </div>
    </div>
</div>
