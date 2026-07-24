<x-app-layout>
    <div class="py-6 sm:py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="w-full">
            <livewire:chat-box :selectedUserId="isset($selectedUser) ? $selectedUser->id : null" />
        </div>
    </div>
</x-app-layout>
