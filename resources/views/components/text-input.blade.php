@props(['disabled' => false])

@if ($attributes->get('type') === 'password')
    <div x-data="{ show: false }" class="relative w-full">
        <input 
            x-bind:type="show ? 'text' : 'password'"
            @disabled($disabled) 
            {{ $attributes->except('type')->merge(['class' => 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-rose-500 dark:focus:border-rose-500 focus:ring-rose-500 dark:focus:ring-rose-500 rounded-xl shadow-sm transition-colors duration-200 w-full pr-10']) }}
        >
        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none">
            <!-- Eye icon (Hide Password) -->
            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <!-- Eye slash icon (Show Password) -->
            <svg x-cloak x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
        </button>
    </div>
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:border-rose-500 dark:focus:border-rose-500 focus:ring-rose-500 dark:focus:ring-rose-500 rounded-xl shadow-sm transition-colors duration-200']) }}>
@endif
