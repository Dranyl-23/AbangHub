<x-app-layout>
    <div class="h-[calc(100vh-64px)] bg-slate-50 dark:bg-slate-900 flex flex-col">
        
        <!-- Chat Header -->
        <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center">
                <a href="{{ route('messages.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="relative">
                    @if($user->profile_image)
                        <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->full_name ?? $user->username }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name ?? $user->username) }}&background=f43f5e&color=fff" alt="{{ $user->full_name ?? $user->username }}" class="w-10 h-10 rounded-full object-cover">
                    @endif
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
                </div>
                <div class="ml-3">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">
                        {{ $user->full_name ?? $user->username }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ ucfirst($user->user_type) }}</p>
                </div>
            </div>
            
            <button class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50 dark:bg-slate-900/50" id="messagesContainer">
            @if($messages->isEmpty())
                <div class="h-full flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-200 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">No messages yet. Send a message to start the conversation!</p>
                </div>
            @else
                @php
                    $lastDate = null;
                @endphp

                @foreach($messages as $message)
                    @php
                        $currentDate = $message->created_at->format('M j, Y');
                        $isSender = $message->sender_id === auth()->id();
                    @endphp

                    @if($lastDate !== $currentDate)
                        <div class="flex justify-center my-6">
                            <span class="px-3 py-1 bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-bold rounded-full shadow-sm">
                                {{ $currentDate }}
                            </span>
                        </div>
                        @php $lastDate = $currentDate; @endphp
                    @endif

                    <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }} group">
                        @if(!$isSender)
                            <div class="flex-shrink-0 mr-3 hidden sm:block">
                                @if($user->profile_image)
                                    <img src="{{ Storage::url($user->profile_image) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name ?? $user->username) }}&background=f43f5e&color=fff" class="w-8 h-8 rounded-full">
                                @endif
                            </div>
                        @endif
                        
                        <div class="max-w-[85%] sm:max-w-[75%] {{ $isSender ? 'order-1' : 'order-2' }}">
                            <div class="px-5 py-3 rounded-2xl shadow-sm {{ $isSender ? 'bg-rose-600 text-white rounded-br-sm' : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-bl-sm border border-slate-100 dark:border-slate-700/50' }}">
                                <p class="text-[15px] leading-relaxed break-words whitespace-pre-wrap">{{ $message->content }}</p>
                            </div>
                            <div class="mt-1 text-xs {{ $isSender ? 'text-right text-slate-400' : 'text-left text-slate-400' }}">
                                {{ $message->created_at->format('g:i A') }}
                                @if($isSender && $message->is_read)
                                    <span class="ml-1 text-rose-500">✓✓</span>
                                @elseif($isSender)
                                    <span class="ml-1">✓</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Chat Input Area -->
        <div class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 p-4 shrink-0 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
            <form action="{{ route('messages.store') }}" method="POST" class="max-w-4xl mx-auto relative flex items-end gap-3">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                
                <button type="button" class="p-3 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-full transition-colors shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </button>
                
                <div class="flex-1 bg-slate-100 dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-700 focus-within:border-rose-500 focus-within:ring-1 focus-within:ring-rose-500 transition-all flex items-center">
                    <textarea 
                        name="content" 
                        rows="1" 
                        class="w-full bg-transparent border-0 focus:ring-0 resize-none py-3 px-4 text-slate-900 dark:text-white placeholder-slate-400 min-h-[48px] max-h-32" 
                        placeholder="Type a message..." 
                        required 
                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                    ></textarea>
                </div>
                
                <button type="submit" class="p-3 bg-rose-600 hover:bg-rose-700 text-white rounded-full shadow-md shadow-rose-500/30 transition-all transform hover:scale-105 shrink-0 disabled:opacity-50" id="sendBtn">
                    <svg class="w-6 h-6 translate-x-[1px] translate-y-[-1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Scroll to bottom script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</x-app-layout>
