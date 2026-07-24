<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Account Settings</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Manage your profile, security preferences, and personal information.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Left Sidebar: Profile Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 text-center sticky top-8">
                        <div class="relative inline-block mb-4 group cursor-pointer" onclick="document.getElementById('profile_image_input').click()">
                            @if($user->profile_image)
                                <img src="{{ $user->avatar_url }}" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-lg group-hover:opacity-75 transition-opacity">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=f43f5e&color=fff&size=256" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-lg group-hover:opacity-75 transition-opacity">
                            @endif
                            <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $user->full_name }}</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">{{ '@' . $user->username }}</p>
                        
                        <div class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $user->user_type === 'landlord' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                            {{ ucfirst($user->user_type) }}
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700 text-left space-y-3">
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path></svg>
                                <span class="truncate">{{ $user->email }}</span>
                            </div>
                            @if($user->phone)
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"></path></svg>
                                <span>{{ $user->phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Side: Forms -->
                <div class="lg:col-span-3 space-y-8">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div class="bg-rose-50 dark:bg-rose-900/10 rounded-3xl p-8 border border-rose-200 dark:border-rose-900/50 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-10">
                            <svg class="w-32 h-32 text-rose-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg>
                        </div>
                        <div class="relative z-10">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
