<x-guest-layout>
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
                <h1 class="text-4xl font-bold text-white mb-4 tracking-tight">Join RentEase</h1>
                <p class="text-rose-50 text-lg max-w-md mx-auto">Start your journey with us. Whether you're looking for a place or renting one out, we've got you covered.</p>
            </div>
            
            <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-rose-400/20 blur-3xl"></div>
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-rose-400/20 blur-3xl"></div>
        </div>

        <!-- Right Side: Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50 dark:bg-slate-900">
            <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 p-8 my-8">
                
                <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                    <x-application-logo class="w-8 h-8 text-rose-600" />
                    <span class="text-2xl font-bold text-rose-600">RentEase</span>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Create an account</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Fill in the details below to get started.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Google Signup Button -->
                    <div>
                        <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center py-2.5 px-4 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm bg-white dark:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Sign up with Google
                        </a>
                    </div>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400">Or sign up with email</span>
                        </div>
                    </div>

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
                        <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
                        <div class="mt-1">
                            <x-text-input id="full_name" type="text" name="full_name" :value="old('full_name')" required autofocus class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Username</label>
                        <div class="mt-1">
                            <x-text-input id="username" type="text" name="username" :value="old('username')" required class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                        <div class="mt-1">
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone Number (Optional)</label>
                        <div class="mt-1">
                            <x-text-input id="phone" type="text" name="phone" :value="old('phone')" class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                        <div class="mt-1">
                            <x-text-input id="password" type="password" name="password" required class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password</label>
                        <div class="mt-1">
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required class="block w-full" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-rose-600 hover:text-rose-500 dark:text-rose-400 dark:hover:text-rose-300">
                        Sign in instead
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
