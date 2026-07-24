<x-app-layout>
    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="w-full">
            
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-3xl font-bold leading-7 text-slate-900 dark:text-white sm:truncate sm:text-4xl sm:tracking-tight">
                        My Properties
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Manage your boarding houses, apartments, and rooms.</p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <a href="{{ route('properties.create') }}" class="ml-3 inline-flex items-center rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600 transition-colors">
                        <svg class="-ml-0.5 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                        Add New Property
                    </a>
                </div>
            </div>

            <!-- Success Message from session is now handled inside Livewire, but let's keep it just in case for non-livewire redirects -->
            @if(session('success') && !request()->routeIs('properties.index'))
                <div class="mb-8 rounded-xl bg-rose-50 dark:bg-rose-900/30 p-4 border border-rose-200 dark:border-rose-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-400 dark:text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-rose-800 dark:text-rose-200">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <livewire:property-manager />

        </div>
    </div>
</x-app-layout>
