<x-app-layout>
    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold leading-7 text-slate-900 dark:text-white sm:truncate sm:text-4xl sm:tracking-tight">
                        Add New Property
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">List your space and start earning with RentEase.</p>
                </div>
                <a href="{{ route('properties.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                    &larr; Back to properties
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-3xl overflow-hidden">
                <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    
                    <!-- Basic Info -->
                    <div class="border-b border-slate-200 dark:border-slate-700 pb-8 mb-8">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">1. Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Listing Title <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" id="title" required value="{{ old('title') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500" placeholder="e.g. Cozy Studio Apartment near USM">
                                @error('title') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="property_type" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Property Type <span class="text-rose-500">*</span></label>
                                <select name="property_type" id="property_type" required class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                    <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="boarding_house" {{ old('property_type') == 'boarding_house' ? 'selected' : '' }}>Boarding House</option>
                                    <option value="room" {{ old('property_type') == 'room' ? 'selected' : '' }}>Room</option>
                                    <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>House</option>
                                    <option value="condo" {{ old('property_type') == 'condo' ? 'selected' : '' }}>Condo</option>
                                </select>
                                @error('property_type') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="furnishing_status" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Furnishing <span class="text-rose-500">*</span></label>
                                <select name="furnishing_status" id="furnishing_status" required class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                    <option value="unfurnished" {{ old('furnishing_status') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                                    <option value="semi_furnished" {{ old('furnishing_status') == 'semi_furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                                    <option value="furnished" {{ old('furnishing_status') == 'furnished' ? 'selected' : '' }}>Fully Furnished</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Spaces -->
                    <div class="border-b border-slate-200 dark:border-slate-700 pb-8 mb-8">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">2. Pricing & Spaces</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="monthly_rent" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Monthly Rent (₱) <span class="text-rose-500">*</span></label>
                                <input type="number" step="0.01" name="monthly_rent" id="monthly_rent" required value="{{ old('monthly_rent') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                            <div>
                                <label for="security_deposit" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Security Deposit (₱)</label>
                                <input type="number" step="0.01" name="security_deposit" id="security_deposit" value="{{ old('security_deposit') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Status <span class="text-rose-500">*</span></label>
                                <select name="status" id="status" required class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="bedrooms" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Bedrooms <span class="text-rose-500">*</span></label>
                                <input type="number" name="bedrooms" id="bedrooms" required min="0" value="{{ old('bedrooms', 1) }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                            <div>
                                <label for="bathrooms" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Bathrooms <span class="text-rose-500">*</span></label>
                                <input type="number" name="bathrooms" id="bathrooms" required min="0" value="{{ old('bathrooms', 1) }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                            <div>
                                <label for="floor_area" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Floor Area (sqm)</label>
                                <input type="number" step="0.01" name="floor_area" id="floor_area" min="0" value="{{ old('floor_area') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="border-b border-slate-200 dark:border-slate-700 pb-8 mb-8">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">3. Location</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-3">
                                <label for="address" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Address <span class="text-rose-500">*</span></label>
                                <input type="text" name="address" id="address" required value="{{ old('address') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500" placeholder="e.g. 123 Main St.">
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Province <span class="text-rose-500">*</span></label>
                                <input type="text" name="province" id="province" required value="{{ old('province', 'Davao del Sur') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">City <span class="text-rose-500">*</span></label>
                                <input type="text" name="city" id="city" required value="{{ old('city', 'Digos City') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                            <div>
                                <label for="barangay" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Barangay</label>
                                <input type="text" name="barangay" id="barangay" value="{{ old('barangay') }}" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                            </div>
                        </div>
                    </div>

                    <!-- Description & Images -->
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">4. Details & Photos</h3>
                        <div class="space-y-6">
                            <div>
                                <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                                <textarea name="description" id="description" rows="5" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-rose-500 focus:border-rose-500" placeholder="Tell tenants what makes this place special...">{{ old('description') }}</textarea>
                            </div>
                            
                            <div>
                                <label for="images" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Upload Photos</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                            <label for="images" class="relative cursor-pointer bg-transparent rounded-md font-medium text-rose-600 hover:text-rose-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-rose-500">
                                                <span>Upload files</span>
                                                <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-slate-500">PNG, JPG, WEBP up to 5MB each. First image becomes the cover.</p>
                                    </div>
                                </div>
                                @error('images.*') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('properties.index') }}" class="px-6 py-3 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-rose-600 text-white font-semibold hover:bg-rose-700 shadow-lg shadow-rose-600/30 transition-all hover:-translate-y-0.5">Publish Listing</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
