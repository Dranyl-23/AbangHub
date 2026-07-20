<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex min-h-screen">
        <!-- Left Side: Branding/Image -->
        <div class="hidden lg:flex lg:w-1/2 bg-rose-600 items-center justify-center relative overflow-hidden lg:sticky lg:top-0 lg:h-screen">
            <div class="absolute inset-0 bg-black/5"></div>
            <div class="relative z-10 px-12 text-center">
                <div class="mb-8 flex justify-center">
                    <div class="p-4 bg-white/10 rounded-3xl backdrop-blur-sm border border-white/20">
                        <x-application-logo class="w-16 h-16 text-white" />
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4 tracking-tight">RentEase</h1>
                <p class="text-rose-50 text-lg max-w-md mx-auto">Find Your Perfect Home in Digos City. The most trusted platform for tenants and landlords.</p>
            </div>
            
            <!-- Decorative circles -->
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-rose-400/20 blur-3xl"></div>
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-rose-400/20 blur-3xl"></div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50 dark:bg-slate-900">
            <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 p-8">
                
                <!-- Mobile Logo (shown only on small screens) -->
                <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                    <x-application-logo class="w-8 h-8 text-rose-600" />
                    <span class="text-2xl font-bold text-rose-600">RentEase</span>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome back</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Please enter your details to sign in.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Google Login Button -->
                    <div>
                        <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center py-2.5 px-4 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm bg-white dark:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Continue with Google
                        </a>
                    </div>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400">Or continue with</span>
                        </div>
                    </div>

                    <!-- Login Identity (Username or Email) -->
                    <div>
                        <label for="login" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username or Email</label>
                        <div class="mt-1">
                            <x-text-input id="login" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-medium text-rose-600 hover:text-rose-500 dark:text-rose-400 dark:hover:text-rose-300" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="mt-1">
                            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 dark:border-slate-600 dark:bg-slate-700 dark:ring-offset-slate-800">
                        <label for="remember_me" class="ml-2 block text-sm text-slate-600 dark:text-slate-400">Remember me</label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                            Sign in
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-rose-600 hover:text-rose-500 dark:text-rose-400 dark:hover:text-rose-300">
                        Sign up for free
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
