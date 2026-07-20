<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <a href="{{ route('tenant.maintenance.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-rose-600 mb-4 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to requests
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Report an Issue</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Provide details about the maintenance issue so your landlord can resolve it quickly.</p>
            </div>

            @if($properties->isEmpty())
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Cannot create request</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p>You must have an active or approved lease to report a maintenance issue.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden p-8">
                    <form action="{{ route('tenant.maintenance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Property Selection -->
                        <div>
                            <label for="property_id" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Property <span class="text-rose-500">*</span></label>
                            <select id="property_id" name="property_id" required class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                <option value="">Select the property</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Issue Title <span class="text-rose-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g., Leaking kitchen sink, Broken AC" class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            @error('title')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Detailed Description <span class="text-rose-500">*</span></label>
                            <textarea id="description" name="description" rows="5" required placeholder="Describe the issue in detail. When did it start? Where exactly is it located?" class="w-full rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo Upload -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Photo Evidence (Optional)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors" id="drop-zone" onclick="document.getElementById('image').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                        <span class="relative cursor-pointer bg-transparent rounded-md font-medium text-rose-600 hover:text-rose-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                                        </span>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PNG, JPG, GIF up to 5MB</p>
                                    <p id="file-name" class="text-sm font-medium text-slate-900 dark:text-white mt-2"></p>
                                </div>
                            </div>
                            @error('image')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all hover:-translate-y-0.5">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
