<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <!-- ========================================== -->
    <!-- BAGIAN 1: KONTEN UTAMA DASHBOARD           -->
    <!-- ========================================== -->

    <!-- Alert Informasi PKL Fase 1 Offline ke Fase 2 -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6 mb-2">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 text-sm rounded-lg shadow-sm flex items-start gap-3">
            <div class="mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <strong>Informasi:</strong><br>
                Bagi siswa yang telah melaksanakan PKL Fase 1 secara offline dan kini memasuki Fase 2, cukup melakukan pencatatan Jurnal PKL Fase 1 pada sistem. Fase 2 tidak perlu diisi kembali.
            </div>
        </div>
    </div>

    <!-- Daftar Card Jurnal -->
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->has('access'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Peringatan</p>
                    <p>{{ $errors->first('access') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $fase1Completed = $journals->where('phase', 1)->whereIn('status', ['COMPLETED', 'READY_TO_GENERATE', 'GENERATED'])->isNotEmpty();
                @endphp

                @foreach($journals as $journal)
                @php
                    $isLocked = ($journal->phase == 2 && !$fase1Completed);
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 relative {{ $isLocked ? 'opacity-80' : '' }}">
                    @if($isLocked)
                        <div class="absolute top-4 right-4 text-red-500">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold text-gray-900 mb-2">Jurnal PKL Fase {{ $journal->phase }}</h3>
                    <p class="text-sm text-gray-600 mb-4">Status: 
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $journal->status == 'DRAFT' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $journal->status }}
                        </span>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-700 mb-1"><strong>Tempat PKL:</strong> {{ $journal->company_name ?? 'Belum dipilih' }}</p>
                        <p class="text-sm text-gray-700 mb-4"><strong>Pembimbing:</strong> {{ $journal->teacher_name ?? 'Belum dipilih' }}</p>
                        
                        @if($isLocked)
                            <button disabled class="inline-block bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                Terkunci (Selesaikan Fase 1)
                            </button>
                        @else
                            <a href="{{ route('journal.show', $journal->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                                Buka Jurnal
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- BAGIAN 2: FITUR TOMBOL BANTUAN & MODAL     -->
    <!-- ========================================== -->
    <div x-data="{ isOpen: false }" 
         x-init="setTimeout(() => isOpen = true, 500)">
         
        <!-- Tombol Mengambang Pojok Kanan Bawah -->
        <button @click="isOpen = true" style="z-index: 40;" class="fixed bottom-6 right-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white w-14 h-14 rounded-full shadow-xl hover:shadow-indigo-500/50 hover:scale-105 flex items-center justify-center cursor-pointer transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </button>

        <!-- Modal Container -->
        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                
                <!-- Backdrop Hitam Blur -->
                <div x-show="isOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0"
                     @click="isOpen = false" 
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Kotak Modal -->
                <div x-show="isOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full relative z-10 border border-gray-100">
                    
                    <style>
                        /* Custom Scrollbar */
                        .custom-scrollbar::-webkit-scrollbar {
                            width: 8px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-track {
                            background: #f1f5f9; 
                            border-radius: 8px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-thumb {
                            background: #94a3b8; 
                            border-radius: 8px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                            background: #64748b; 
                        }
                    </style>
                    
                    <!-- Header Modal -->
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-5 flex justify-between items-center shadow-md">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl leading-6 font-bold text-white tracking-wide" id="modal-title">
                                FORMAT PORTOFOLIO LAPORAN PKL
                            </h3>
                        </div>
                        <button type="button" @click="isOpen = false" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Isi Konten Modal (Scrollable) -->
                    <div class="px-5 pt-5 pb-6 max-h-[70vh] overflow-y-auto custom-scrollbar text-sm text-gray-700 bg-slate-50">
                        
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 shadow-sm flex gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-justify leading-relaxed text-blue-900 text-sm">
                                Dokumentasi portofolio laporan PKL disusun oleh peserta didik di bawah pembinaan instruktur tempat PKL dan guru pembimbing. Pembuatan dokumentasi portofolio dilakukan dengan cara mengompilasi, mendeskripsi catatan-catatan pengalaman belajar dari seluruh pekerjaan/aktivitas pembelajaran di Institusi Tempat PKL yang berasal dari Jurnal Kegiatan PKL. Hasil kompilasi kemudian dituangkan dalam bentuk dokumen portofolio.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                            <!-- Komponen Laporan -->
                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                <h4 class="font-bold text-gray-900 mb-4 text-base flex items-center border-b pb-2">
                                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                                    a. Format portofolio laporan PKL memuat komponen sebagai berikut.
                                </h4>
                                
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <svg class="h-4 w-4 text-emerald-500 mr-2 shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span><strong>Halaman Sampul (Cover)</strong> — memuat judul laporan, logo sekolah, nama & NIS siswa, dan tahun ajaran.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-4 w-4 text-emerald-500 mr-2 shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span><strong>Lembar Pengesahan</strong> — dari pembimbing DU/DI, guru pembimbing, dan kepala program keahlian.</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-4 w-4 text-emerald-500 mr-2 shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span><strong>Kata Pengantar.</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-4 w-4 text-emerald-500 mr-2 shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span><strong>Daftar Isi, Daftar Tabel, dan Daftar Gambar (jika ada).</strong></span>
                                    </li>
                                    
                                    <li class="pt-2">
                                        <div class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded inline-flex items-center text-sm mb-2 w-full border-l-4 border-indigo-500">
                                            BAB I — Pendahuluan
                                        </div>
                                        <ul class="list-disc pl-8 space-y-1 text-gray-700 mt-1">
                                            <li>Latar Belakang (diambil dari template Laporan PKL)</li>
                                            <li>Perencanaan Pembelajaran PKL (diambil dari template Laporan PKL)</li>
                                            <li>Tujuan Pelaksanaan PKL (diambil dari template Laporan PKL)</li>
                                            <li>Manfaat Pelaksanaan PKL (diambil dari template Laporan PKL)</li>
                                        </ul>
                                    </li>
                                    
                                    <li class="pt-2">
                                        <div class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded inline-flex items-center text-sm mb-2 w-full border-l-4 border-blue-500">
                                            BAB II — Tinjauan Umum Perusahaan/Instansi
                                        </div>
                                        <ul class="list-disc pl-8 space-y-1 text-gray-700 mt-1">
                                            <li>Sejarah singkat perusahaan/instansi tempat PKL.</li>
                                            <li>Visi dan misi perusahaan.</li>
                                            <li>Struktur organisasi.</li>
                                            <li>Bidang usaha / layanan / produk perusahaan.</li>
                                        </ul>
                                    </li>
                                    
                                    <li class="pt-2">
                                        <div class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded inline-flex items-center text-sm mb-2 w-full border-l-4 border-emerald-500">
                                            BAB III — Pembahasan / Hasil Kegiatan
                                        </div>
                                        <ul class="list-disc pl-8 space-y-1 text-gray-700 mt-1">
                                            <li>Deskripsi tugas dan kegiatan yang dilaksanakan selama PKL.</li>
                                            <li>Uraian proyek/aplikasi yang dikerjakan (nama proyek, teknologi/bahasa pemrograman yang digunakan, peran dalam tim).</li>
                                            <li>Tahapan pengembangan perangkat lunak yang dilalui (analisis, desain, coding, testing, deployment).</li>
                                            <li>Kendala yang dihadapi selama PKL dan solusi/penyelesaiannya.</li>
                                            <li>Dokumentasi hasil kerja (screenshot aplikasi, diagram, source code penting).</li>
                                        </ul>
                                    </li>
                                    
                                    <li class="pt-2">
                                        <div class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded inline-flex items-center text-sm mb-2 w-full border-l-4 border-amber-500">
                                            BAB IV — Penutup
                                        </div>
                                        <ul class="list-disc pl-8 space-y-1 text-gray-700 mt-1">
                                            <li>Kesimpulan hasil pelaksanaan PKL.</li>
                                            <li>Saran bagi sekolah, DU/DI, maupun peserta didik PKL selanjutnya.</li>
                                        </ul>
                                    </li>
                                    
                                    <li class="pt-2">
                                        <div class="font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded inline-flex items-center text-sm mb-2 w-full border-l-4 border-purple-500">
                                            Lampiran
                                        </div>
                                        <ul class="list-disc pl-8 space-y-1 text-gray-700 mt-1">
                                            <li>Jurnal kegiatan harian dan daftar absensi yang telah diisi lengkap.</li>
                                            <li>Fotokopi sertifikat/surat keterangan telah melaksanakan PKL dari DU/DI.</li>
                                            <li>Dokumentasi foto kegiatan selama PKL.</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <!-- Format Penulisan -->
                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm h-fit mt-2">
                                <h4 class="font-bold text-gray-900 mb-4 text-base flex items-center border-b pb-2">
                                    <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    b. Format penulisan portofolio laporan PKL dibuat dengan ketentuan sebagai berikut:
                                </h4>
                                
                                <ul class="space-y-2">
                                    <li class="flex items-start bg-slate-50 p-2.5 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        <span class="text-gray-800 text-sm">Jenis kertas yang digunakan berukuran A4.</span>
                                    </li>
                                    
                                    <li class="flex items-start bg-slate-50 p-2.5 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                        <span class="text-gray-800 text-sm">Margin pengetikan : Kiri 3 cm. Kanan, Atas, dan Bawah 2,54 cm.</span>
                                    </li>
                                    
                                    <li class="flex items-start bg-slate-50 p-2.5 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                                        <span class="text-gray-800 text-sm">Spasi pengetikan : 1,5 spasi.</span>
                                    </li>
                                    
                                    <li class="flex items-start bg-slate-50 p-2.5 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span class="text-gray-800 text-sm">Menggunakan jenis huruf Cambria ukuran 12.</span>
                                    </li>
                                    
                                    <li class="flex items-start bg-slate-50 p-2.5 rounded-lg border border-slate-100 hover:bg-slate-100 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400 mr-3 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        <span class="text-gray-800 text-sm leading-relaxed">Dijilid lakban hitam (tidak jilid jepit), menggunakan plastik bening di sampul depan, dan sampul belakang kertas jeruk berwarna Merah.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Modal -->
                    <div class="bg-white px-6 py-4 flex justify-end border-t border-gray-100">
                        <button type="button" @click="isOpen = false" class="inline-flex justify-center items-center rounded-lg border border-transparent bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Saya Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>