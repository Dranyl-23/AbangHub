<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Business Compliance Hub</h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400">Upload your legal documents to become a Fully Verified Landlord. Completing this builds trust with potential tenants.</p>
            </div>

            @if(session('success'))
                <div class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-400 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @php
                $requirements = [
                    'dti_sec' => [
                        'title' => 'DTI / SEC Registration',
                        'desc' => 'Proof of business name registration (Department of Trade and Industry or Securities and Exchange Commission).',
                    ],
                    'barangay_clearance' => [
                        'title' => 'Barangay Business Clearance',
                        'desc' => 'Clearance from the barangay where your property is located.',
                    ],
                    'mayors_permit' => [
                        'title' => 'Mayor\'s / Business Permit',
                        'desc' => 'The official business permit issued by your local City/Municipal Hall.',
                    ],
                    'bir_2303' => [
                        'title' => 'BIR Certificate of Registration (Form 2303)',
                        'desc' => 'Proof of registration with the Bureau of Internal Revenue.',
                    ],
                    'fire_safety' => [
                        'title' => 'Fire Safety Inspection Certificate (FSIC)',
                        'desc' => 'Issued by the Bureau of Fire Protection (BFP) ensuring property safety.',
                    ],
                    'sanitary_permit' => [
                        'title' => 'Sanitary Permit',
                        'desc' => 'Issued by the local City/Municipal Health Office.',
                    ]
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($requirements as $key => $req)
                    @php
                        $isUploaded = isset($documents[$key]);
                        $doc = $isUploaded ? $documents[$key] : null;
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border {{ $isUploaded ? 'border-emerald-200 dark:border-emerald-800' : 'border-slate-200 dark:border-slate-700' }} p-6 relative overflow-hidden transition-all">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            @if($isUploaded)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Uploaded
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    Pending
                                </span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 dark:text-white pr-20">{{ $req['title'] }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-6">{{ $req['desc'] }}</p>

                        @if($isUploaded)
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-sm font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Document
                                </a>
                                
                                <form action="{{ route('landlord.compliance.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
                                    @csrf
                                    <input type="hidden" name="document_type" value="{{ $key }}">
                                    <label class="cursor-pointer text-sm font-semibold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                                        Update File
                                        <input type="file" name="document_file" class="hidden" accept=".pdf,image/*" onchange="this.form.submit()">
                                    </label>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('landlord.compliance.store') }}" method="POST" enctype="multipart/form-data" class="mt-auto">
                                @csrf
                                <input type="hidden" name="document_type" value="{{ $key }}">
                                
                                <label class="flex justify-center w-full h-24 px-4 transition bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-xl appearance-none cursor-pointer hover:border-rose-400 dark:hover:border-rose-500 focus:outline-none">
                                    <span class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="font-medium text-slate-500 dark:text-slate-400">
                                            Click to browse file
                                        </span>
                                    </span>
                                    <input type="file" name="document_file" class="hidden" accept=".pdf,image/*" onchange="this.form.submit()">
                                </label>
                                @error('document_file')
                                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </form>
                        @endif

                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
