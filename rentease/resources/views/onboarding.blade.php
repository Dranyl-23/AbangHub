<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 fade-in">
            
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Almost there!</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Welcome, {{ explode(' ', auth()->user()->full_name)[0] }}! Please complete your profile to continue.
                </p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('onboarding.store') }}" method="POST">
                @csrf
                
                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">I want to...</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="user_type" value="tenant" class="peer sr-only" checked>
                            <div class="rounded-lg border-2 border-slate-200 dark:border-slate-700 px-4 py-3 text-center peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20 transition-all">
                                <span class="block text-sm font-medium text-slate-900 dark:text-white">Find a place</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Tenant</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="user_type" value="landlord" class="peer sr-only">
                            <div class="rounded-lg border-2 border-slate-200 dark:border-slate-700 px-4 py-3 text-center peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20 transition-all">
                                <span class="block text-sm font-medium text-slate-900 dark:text-white">List a property</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Landlord</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone Number (Optional)</label>
                    <div class="mt-1">
                        <input id="phone" type="text" name="phone" placeholder="09123456789" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-rose-500 focus:ring-rose-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                        Continue to Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
