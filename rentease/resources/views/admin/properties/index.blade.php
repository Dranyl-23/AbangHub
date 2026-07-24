<x-app-layout>
    <div class="bg-transparent min-h-screen pb-16 text-slate-900 dark:text-white transition-colors duration-300">
        
        <div class="w-full pt-6 pb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight mb-2">Property Moderation</h1>
                    <p class="text-base text-slate-500 dark:text-slate-400">Manage and moderate all properties listed on the platform.</p>
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

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Property</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Owner</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stats</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                        @foreach($properties as $property)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-750/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-16 bg-slate-200 dark:bg-slate-700 rounded-md overflow-hidden">
                                            @if($property->images->count() > 0)
                                                <img class="h-full w-full object-cover" src="{{ Storage::url($property->images->first()->image_path) }}" alt="">
                                            @else
                                                <img class="h-full w-full object-cover" src="https://picsum.photos/seed/{{ $property->id }}/100/75" alt="">
                                            @endif
                                        </div>
                                        <div class="ml-4 max-w-[200px]">
                                            <div class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                                <a href="{{ route('properties.show', $property) }}" target="_blank" class="hover:underline hover:text-rose-600">{{ $property->title }}</a>
                                            </div>
                                            <div class="text-xs text-slate-500 truncate">{{ $property->city }}, {{ $property->province }}</div>
                                            <div class="text-xs font-medium text-rose-600 mt-0.5">₱{{ number_format($property->monthly_rent, 0) }}/mo</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900 dark:text-white font-medium">{{ $property->owner->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $property->owner->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-slate-500">Apps: <span class="font-semibold text-slate-900 dark:text-white">{{ $property->applications_count }}</span></div>
                                    <div class="text-xs text-slate-500">Trans: <span class="font-semibold text-slate-900 dark:text-white">{{ $property->transactions_count }}</span></div>
                                    <div class="text-xs text-slate-500">Reviews: <span class="font-semibold text-slate-900 dark:text-white">{{ $property->reviews_count }}</span></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        @if($property->status === 'available')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">Available</span>
                                        @elseif($property->status === 'rented')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Rented</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Maint.</span>
                                        @endif

                                        @if($property->is_banned)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400">Banned/Hidden</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.properties.toggleBan', $property) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        @if($property->is_banned)
                                            <button type="submit" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300">Unban & Show</button>
                                        @else
                                            <button type="submit" class="text-rose-600 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300">Ban & Hide</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($properties->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
