<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-rose-600 dark:bg-rose-500 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-rose-700 dark:hover:bg-rose-400 focus:bg-rose-700 dark:focus:bg-rose-400 active:bg-rose-800 dark:active:bg-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-sm shadow-rose-600/20']) }}>
    {{ $slot }}
</button>
