<x-app-layout>
    <div class="bg-transparent min-h-screen pb-16 text-slate-900 dark:text-white transition-colors duration-300">
        
        <div class="w-full pt-6 pb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">User Management</h1>
                    <p class="text-base text-slate-500 dark:text-slate-400">View and manage all users on the platform.</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        &larr; Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl p-4 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl p-4 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Joined</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-750/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->profile_image)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->avatar_url }}" alt="">
                                            @else
                                                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name ?? $user->username) }}&background=f1f5f9&color=0f172a" alt="">
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                                {{ $user->full_name ?? $user->username }}
                                                @if($user->is_verified)
                                                    <svg class="w-4 h-4 text-blue-500 inline -mt-0.5 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                                @endif
                                            </div>
                                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="user_type" onchange="this.form.submit()" class="text-sm rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500 py-1.5 pl-3 pr-8" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <option value="tenant" {{ $user->user_type === 'tenant' ? 'selected' : '' }}>Tenant</option>
                                            <option value="landlord" {{ $user->user_type === 'landlord' ? 'selected' : '' }}>Landlord</option>
                                            <option value="admin" {{ $user->user_type === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_banned)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400">
                                            Banned
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggleBan', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_banned)
                                                <button type="submit" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">Unban</button>
                                            @else
                                                <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300">Ban</button>
                                            @endif
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
