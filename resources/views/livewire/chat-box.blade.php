<div class="flex h-[calc(100vh-12rem)] bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden" wire:poll.5s="loadMessages">
    <!-- Sidebar / Conversations List -->
    <div class="w-full md:w-1/3 border-r border-slate-200 dark:border-slate-700 flex flex-col bg-slate-50 dark:bg-slate-800/50">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Messages</h2>
            <div class="mt-3 relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" class="block w-full rounded-full border-0 py-2 pl-10 pr-3 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 dark:bg-slate-900 dark:ring-slate-700 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-rose-600 sm:text-sm sm:leading-6" placeholder="Search conversations...">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <ul role="list" class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($conversations as $user)
                    <li wire:click="selectConversation({{ $user->id }})" class="cursor-pointer hover:bg-white dark:hover:bg-slate-700/50 transition-colors {{ $activeUser && $activeUser->id === $user->id ? 'bg-white dark:bg-slate-700 border-l-4 border-rose-500' : '' }}">
                        <div class="flex items-center p-4">
                            <div class="relative">
                                <img class="h-12 w-12 rounded-full object-cover bg-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=10b981&color=fff" alt="">
                                <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-rose-400 ring-2 ring-white dark:ring-slate-800"></span>
                            </div>
                            <div class="ml-4 flex-1 overflow-hidden">
                                <div class="flex justify-between items-baseline">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <span class="text-xs text-slate-500 dark:text-slate-400"></span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate capitalize">{{ $user->user_type }}</p>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                        No conversations yet.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="hidden md:flex md:w-2/3 flex-col bg-white dark:bg-slate-800">
        @if($activeUser)
            <!-- Chat Header -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center">
                    <img class="h-10 w-10 rounded-full object-cover bg-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode($activeUser->first_name . ' ' . $activeUser->last_name) }}&background=10b981&color=fff" alt="">
                    <div class="ml-3">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $activeUser->first_name }} {{ $activeUser->last_name }}</p>
                        <p class="text-xs text-rose-600 dark:text-rose-400 font-medium capitalize">{{ $activeUser->user_type }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                    </button>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 p-4 overflow-y-auto bg-slate-50 dark:bg-slate-900/30" id="chat-messages">
                <div class="space-y-4">
                    @forelse($messages as $msg)
                        @if($msg->sender_id === Auth::id())
                            <!-- Outgoing Message -->
                            <div class="flex justify-end">
                                <div class="max-w-[75%] rounded-2xl rounded-tr-none bg-rose-600 px-5 py-3 text-sm text-white shadow-sm">
                                    {{ $msg->message }}
                                    <span class="block mt-1 text-[10px] text-rose-200 text-right">{{ $msg->created_at->format('g:i A') }}</span>
                                </div>
                            </div>
                        @else
                            <!-- Incoming Message -->
                            <div class="flex justify-start">
                                <div class="max-w-[75%] rounded-2xl rounded-tl-none bg-white dark:bg-slate-700 px-5 py-3 text-sm text-slate-800 dark:text-slate-200 shadow-sm border border-slate-200 dark:border-slate-600">
                                    {{ $msg->message }}
                                    <span class="block mt-1 text-[10px] text-slate-400 text-left">{{ $msg->created_at->format('g:i A') }}</span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-slate-500 dark:text-slate-400">
                            <svg class="h-12 w-12 mb-3 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            <p class="text-sm">No messages yet. Say hello!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Message Input -->
            <div class="p-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
                <form wire:submit.prevent="sendMessage" class="flex gap-2">
                    <input type="text" wire:model="newMessage" required class="block w-full rounded-full border-0 py-2.5 pl-4 pr-12 text-sm text-slate-900 ring-1 ring-inset ring-slate-300 dark:bg-slate-900 dark:ring-slate-700 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-rose-600" placeholder="Type a message...">
                    <button type="submit" class="flex-shrink-0 inline-flex items-center justify-center rounded-full bg-rose-600 p-2.5 text-white shadow-sm hover:bg-rose-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" /></svg>
                    </button>
                </form>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex-1 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/50 p-8 text-center">
                <div class="h-20 w-20 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mb-6">
                    <svg class="h-10 w-10 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Your Messages</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Select a conversation from the sidebar to start chatting, or contact a landlord from their property listing.</p>
            </div>
        @endif
    </div>

    <!-- Scroll to bottom script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            let container = document.getElementById('chat-messages');
            if (container) container.scrollTop = container.scrollHeight;
            
            Livewire.on('messageSent', () => {
                setTimeout(() => {
                    let container = document.getElementById('chat-messages');
                    if (container) container.scrollTop = container.scrollHeight;
                }, 100);
            });
        });
    </script>
</div>
