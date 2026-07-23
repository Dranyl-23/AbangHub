<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-[calc(100vh-64px)]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <a href="{{ route('tenant.applications.index') }}" class="text-rose-600 dark:text-rose-400 hover:text-rose-700 font-medium inline-flex items-center mb-4 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Applications
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Review & Sign Lease</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Please read the terms carefully and provide your digital signature.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-8 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Residential Lease Agreement</h2>
                            <p class="text-slate-500 dark:text-slate-400">For {{ $lease->property->title }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl mb-8">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1">Landlord</p>
                            <p class="text-slate-900 dark:text-white font-medium">{{ $lease->property->owner->full_name ?? $lease->property->owner->username }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1">Tenant</p>
                            <p class="text-slate-900 dark:text-white font-medium">{{ auth()->user()->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1">Monthly Rent</p>
                            <p class="text-slate-900 dark:text-white font-medium">₱{{ number_format($lease->monthly_rent, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-bold mb-1">Lease Period</p>
                            <p class="text-slate-900 dark:text-white font-medium">{{ \Carbon\Carbon::parse($lease->start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($lease->end_date)->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Fake Document Content -->
                    <div class="prose prose-slate dark:prose-invert max-w-none text-sm text-slate-600 dark:text-slate-400 h-96 overflow-y-auto pr-4 custom-scrollbar bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">1. Terms of Agreement</h3>
                        <p>This Lease Agreement is made between the Landlord, <strong>{{ $lease->property->owner->full_name ?? $lease->property->owner->username }}</strong>, and the Tenant, <strong>{{ auth()->user()->full_name }}</strong>. The Landlord hereby leases to the Tenant the property located at <strong>{{ $lease->property->address }}, {{ $lease->property->city }}</strong> for the term specified above.</p>
                        
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 mt-6">2. Rent Payments</h3>
                        <p>The Tenant agrees to pay the Monthly Rent of <strong>₱{{ number_format($lease->monthly_rent, 0) }}</strong> in advance on the same day of each month. A security deposit may be required before move-in.</p>

                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 mt-6">3. Maintenance and Repairs</h3>
                        <p>The Tenant shall keep the premises in a clean and sanitary condition. The Tenant shall promptly notify the Landlord of any necessary repairs. The Tenant shall not make any alterations or improvements to the property without the Landlord's written consent.</p>

                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 mt-6">4. Rules and Regulations</h3>
                        <p>The Tenant shall comply with all rules and regulations governing the premises. No pets are allowed without prior written permission. Smoking is prohibited inside the property.</p>

                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 mt-6">5. Termination</h3>
                        <p>Either party may terminate this agreement at the end of the term by providing a 30-day written notice. If the Tenant defaults on rent payments, the Landlord may terminate this agreement immediately according to local laws.</p>
                    </div>
                </div>

                <div class="p-8 bg-slate-50 dark:bg-slate-800" x-data="signaturePad()">
                    <form action="{{ route('tenant.leases.processSignature', $lease) }}" method="POST" @submit="submitForm">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Digital Signature</label>
                            <p class="text-xs text-slate-500 mb-4">Please sign inside the box below to accept the terms of this lease agreement.</p>
                            
                            <div class="relative bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl overflow-hidden shadow-inner">
                                <canvas x-ref="canvas" class="w-full h-48 cursor-crosshair touch-none" @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing" @touchstart.prevent="startDrawingTouch" @touchmove.prevent="drawTouch" @touchend.prevent="stopDrawing"></canvas>
                                
                                <button type="button" @click="clearSignature" class="absolute top-4 right-4 p-2 bg-white dark:bg-slate-800 text-slate-500 hover:text-red-500 rounded-full shadow-md transition-colors border border-slate-200 dark:border-slate-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                
                                <div class="absolute bottom-4 left-4 right-4 flex items-center gap-4 pointer-events-none opacity-20">
                                    <div class="h-px bg-slate-900 dark:bg-white flex-1"></div>
                                    <div class="text-sm font-serif">Sign Here</div>
                                    <div class="h-px bg-slate-900 dark:bg-white flex-1"></div>
                                </div>
                            </div>
                            <input type="hidden" name="signature" x-ref="signatureInput" id="signature">
                            @error('signature')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-rose-600/30 transition-all active:scale-[0.98]">
                                Agree and Sign Lease
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('signaturePad', () => ({
                isDrawing: false,
                ctx: null,
                hasSignature: false,
                
                init() {
                    const canvas = this.$refs.canvas;
                    this.ctx = canvas.getContext('2d');
                    
                    // Set actual size in memory (scaled to account for high DPI devices).
                    const scale = window.devicePixelRatio || 1;
                    canvas.width = canvas.offsetWidth * scale;
                    canvas.height = canvas.offsetHeight * scale;
                    this.ctx.scale(scale, scale);
                    
                    this.ctx.lineWidth = 3;
                    this.ctx.lineCap = 'round';
                    this.ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#fff' : '#0f172a';
                },
                
                getCoordinates(e) {
                    const rect = this.$refs.canvas.getBoundingClientRect();
                    const clientX = e.clientX || (e.touches && e.touches[0].clientX);
                    const clientY = e.clientY || (e.touches && e.touches[0].clientY);
                    return {
                        x: clientX - rect.left,
                        y: clientY - rect.top
                    };
                },
                
                startDrawing(e) {
                    this.isDrawing = true;
                    this.hasSignature = true;
                    const coords = this.getCoordinates(e);
                    this.ctx.beginPath();
                    this.ctx.moveTo(coords.x, coords.y);
                },
                
                startDrawingTouch(e) {
                    this.startDrawing(e);
                },
                
                draw(e) {
                    if (!this.isDrawing) return;
                    const coords = this.getCoordinates(e);
                    this.ctx.lineTo(coords.x, coords.y);
                    this.ctx.stroke();
                },
                
                drawTouch(e) {
                    this.draw(e);
                },
                
                stopDrawing() {
                    if (!this.isDrawing) return;
                    this.isDrawing = false;
                    this.ctx.closePath();
                },
                
                clearSignature() {
                    const canvas = this.$refs.canvas;
                    this.ctx.clearRect(0, 0, canvas.width, canvas.height);
                    this.hasSignature = false;
                },
                
                submitForm(e) {
                    if (!this.hasSignature) {
                        e.preventDefault();
                        alert('Please provide your signature before submitting.');
                        return;
                    }
                    this.$refs.signatureInput.value = this.$refs.canvas.toDataURL('image/png');
                }
            }));
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569;
        }
    </style>
    @endpush
</x-app-layout>
