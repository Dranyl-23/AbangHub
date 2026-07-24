<x-app-layout>
    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Messages</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Connect with landlords and tenants.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                @if($conversations->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-700">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">No messages yet</h3>
                        <p class="text-slate-500 dark:text-slate-400 mt-1">When you message a landlord or tenant, it will appear here.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($conversations as $conversation)
                            @php
                                $isSender = $conversation->sender_id === auth()->id();
                                $otherUser = $isSender ? $conversation->receiver : $conversation->sender;
                                $isUnread = !$isSender && !$conversation->is_read;
                            @endphp
                            <li>
                                <a href="{{ route('messages.show', $otherUser->id) }}" class="flex items-center p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors {{ $isUnread ? 'bg-rose-50/50 dark:bg-rose-900/10' : '' }}">
                                    <div class="relative flex-shrink-0">
                                        @if($otherUser->profile_image)
                                            <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->full_name ?? $otherUser->username }}" class="w-14 h-14 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($otherUser->full_name ?? $otherUser->username) }}&background=f43f5e&color=fff" alt="{{ $otherUser->full_name ?? $otherUser->username }}" class="w-14 h-14 rounded-full object-cover">
                                        @endif
                                        @if($isUnread)
                                            <span class="absolute top-0 right-0 block h-3 w-3 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-800"></span>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h2 class="text-base font-bold text-slate-900 dark:text-white truncate">
                                                {{ $otherUser->full_name ?? $otherUser->username }}
                                            </h2>
                                            <span class="text-xs text-slate-400 whitespace-nowrap">
                                                {{ $conversation->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-sm truncate {{ $isUnread ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-500 dark:text-slate-400' }}">
                                            @if($isSender)
                                                <span class="text-slate-400 mr-1">You:</span>
                                            @endif
                                            {{ $conversation->content }}
                                        </p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
