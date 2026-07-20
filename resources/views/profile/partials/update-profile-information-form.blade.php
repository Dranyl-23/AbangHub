<section>
    <header>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
            {{ __('Personal Details') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __("Update your account's profile information and contact details.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Hidden Profile Image Input triggered from sidebar -->
        <input id="profile_image_input" name="profile_image" type="file" class="sr-only" accept="image/*" onchange="this.form.submit()" />
        <x-input-error class="mt-2 text-center" :messages="$errors->get('profile_image')" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="full_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                <input id="full_name" name="full_name" type="text" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" value="{{ old('full_name', $user->full_name) }}" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
            </div>

            <div>
                <label for="username" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-slate-400 font-medium">@</span>
                    </div>
                    <input id="username" name="username" type="text" class="w-full pl-8 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" value="{{ old('username', $user->username) }}" required autocomplete="username" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>
            
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                <input id="email" name="email" type="email" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="email" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-rose-600 dark:text-rose-400 font-medium">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification" class="underline hover:text-rose-800 dark:hover:text-rose-300 rounded-md focus:outline-none">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-emerald-600 dark:text-emerald-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number (Optional)</label>
                <input id="phone" name="phone" type="text" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 transition-colors" value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
            <button type="submit" class="px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-xl shadow-md hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
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
