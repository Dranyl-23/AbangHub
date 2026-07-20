<section>
    <header>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
            {{ __('Security') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('put')

        <div class="max-w-xl space-y-6">
            <div>
                <label for="update_password_current_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Current Password</label>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">New Password</label>
                <input id="update_password_password" name="password" type="password" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Confirm New Password</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
            <button type="submit" class="px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl shadow-md hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
