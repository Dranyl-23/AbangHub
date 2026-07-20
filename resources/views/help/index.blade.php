<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-slate-900 overflow-hidden -mt-6 -mx-4 sm:-mx-8 lg:-mx-16 xl:-mx-24">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-600/20 to-blue-600/20 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-slate-900/60"></div>
            <!-- Decorative pattern -->
            <svg class="absolute inset-0 h-full w-full opacity-10" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="help-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="1" fill="none" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#help-pattern)" />
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
                How can we help you?
            </h1>
            <p class="text-xl text-slate-300 max-w-2xl mx-auto mb-10">
                Search our knowledge base or browse the categories below to find answers to your questions.
            </p>
            
            <div class="max-w-2xl mx-auto relative group">
                <input type="text" class="block w-full pl-12 pr-4 py-4 border-0 rounded-2xl bg-white/10 backdrop-blur-md text-white placeholder-slate-300 focus:ring-2 focus:ring-rose-500 shadow-xl transition-all relative z-0" placeholder="Search for articles, guides, or questions...">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                    <svg class="h-6 w-6 text-slate-400 group-focus-within:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-16 bg-slate-50 dark:bg-slate-900 min-h-screen -mx-4 sm:-mx-8 lg:-mx-16 xl:-mx-24" x-data="{ category: 'general' }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Category Tabs -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button @click="category = 'general'" :class="{ 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 border-transparent': category === 'general', 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700': category !== 'general' }" class="px-6 py-2.5 rounded-full font-bold shadow-md transition-colors border">
                    General
                </button>
                <button @click="category = 'tenants'" :class="{ 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 border-transparent': category === 'tenants', 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700': category !== 'tenants' }" class="px-6 py-2.5 rounded-full font-bold shadow-md transition-colors border">
                    Tenants
                </button>
                <button @click="category = 'landlords'" :class="{ 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 border-transparent': category === 'landlords', 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700': category !== 'landlords' }" class="px-6 py-2.5 rounded-full font-bold shadow-md transition-colors border">
                    Landlords
                </button>
            </div>

            <!-- FAQ Section -->
            <div class="space-y-4" x-data="{ activeAccordion: 1 }">
                
                <!-- General Category -->
                <div x-show="category === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300">
                        <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white text-left">What is RentEase?</h3>
                            <svg class="w-5 h-5 text-slate-500 transform transition-transform duration-300 shrink-0" :class="{ 'rotate-180 text-rose-500': activeAccordion === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeAccordion === 1" x-collapse>
                            <div class="px-6 pb-5 pt-2 text-slate-600 dark:text-slate-400">
                                RentEase is a premium property rental platform connecting verified landlords with reliable tenants. We streamline the entire process from finding a home to paying rent and requesting maintenance.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenants Category -->
                <div x-show="category === 'tenants'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4" style="display: none;">
                    <!-- FAQ Item 2 -->
                    <!-- FAQ Item 3 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300">
                        <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white text-left">How do I request to book a property?</h3>
                            <svg class="w-5 h-5 text-slate-500 transform transition-transform duration-300 shrink-0" :class="{ 'rotate-180 text-rose-500': activeAccordion === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeAccordion === 3" x-collapse>
                            <div class="px-6 pb-5 pt-2 text-slate-600 dark:text-slate-400">
                                Simply browse our properties, find the one you like, and click the "Request to Book" button on the property details page. The landlord will review your application and approve it if you're a good match.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300">
                        <button @click="activeAccordion = activeAccordion === 4 ? null : 4" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white text-left">When do I pay the security deposit?</h3>
                            <svg class="w-5 h-5 text-slate-500 transform transition-transform duration-300 shrink-0" :class="{ 'rotate-180 text-rose-500': activeAccordion === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeAccordion === 4" x-collapse>
                            <div class="px-6 pb-5 pt-2 text-slate-600 dark:text-slate-400">
                                You are required to pay the security deposit along with your initial monthly rent immediately after the landlord approves your booking application.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Landlords Category -->
                <div x-show="category === 'landlords'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4" style="display: none;">

                    <!-- FAQ Item 5 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300">
                        <button @click="activeAccordion = activeAccordion === 5 ? null : 5" class="w-full px-6 py-5 flex items-center justify-between focus:outline-none bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white text-left">How do I list my property?</h3>
                            <svg class="w-5 h-5 text-slate-500 transform transition-transform duration-300 shrink-0" :class="{ 'rotate-180 text-rose-500': activeAccordion === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeAccordion === 5" x-collapse>
                            <div class="px-6 pb-5 pt-2 text-slate-600 dark:text-slate-400">
                                If you are logged in as a Landlord, simply go to your Dashboard and click "Add Property". Fill out the required details, upload photos, and hit save!
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support Section -->
            <div class="mt-16 bg-white dark:bg-slate-800 rounded-3xl p-8 sm:p-12 text-center shadow-xl border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-rose-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Still need help?</h2>
                    <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-lg mx-auto">
                        If you couldn't find the answer to your question in our FAQ, our support team is always ready to assist you.
                    </p>
                    <a href="mailto:support@rentease.com" class="inline-flex items-center px-8 py-4 bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-600/30 hover:bg-rose-700 hover:-translate-y-1 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Contact Support
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
