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

    <!-- Tombol Mengambang Pojok Kanan Bawah -->
    <button id="btnPanduanInfo" style="z-index: 40;" class="fixed bottom-6 right-6 bg-indigo-600 text-white w-14 h-14 rounded-full shadow-lg hover:bg-indigo-700 flex items-center justify-center cursor-pointer transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>

    <!-- Modal Container -->
    <div id="modalPanduanPortofolio" style="z-index: 50; display: none;" class="fixed inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <!-- Backdrop Hitam Blur -->
            <div id="backdropModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Kotak Modal -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-10">
                
                <!-- Header Modal -->
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                        FORMAT PORTOFOLIO LAPORAN PKL
                    </h3>
                    <button type="button" id="btnTutupAtas" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Isi Konten Modal (Scrollable) -->
                <div class="px-6 pt-5 pb-6 max-h-[70vh] overflow-y-auto text-sm text-gray-800 bg-white">
                    <p class="mb-4 text-justify leading-relaxed">
                        Dokumentasi portopolio laporan PKL disusun oleh peserta didik di bawah pembinaan instruktur tempat PKL dan guru pembimbing. Pembuatan dokumentasi portopolio dilakukan dengan cara mengompilasi, mendeskripsi catatan-catatan pengalaman belajar dari seluruh pekerjaan/aktivitas pembelajaran di Institusi Tempat PKL yang berasal dari Jurnal Kegiatan PKL. Hasil kompilasi kemudian dituangkan dalam bentuk dokumen portopolio.
                    </p>

                    <div class="mb-4">
                        <p class="font-bold text-gray-900 mb-2">a. Format portofolio laporan PKL memuat komponen sebagai berikut:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Halaman Sampul (Cover)</strong> — memuat judul laporan, logo sekolah, nama & NIS siswa, dan tahun ajaran.</li>
                            <li><strong>Lembar Pengesahan</strong> — dari pembimbing DU/DI, guru pembimbing, dan kepala program keahlian.</li>
                            <li><strong>Kata Pengantar.</strong></li>
                            <li><strong>Daftar Isi, Daftar Tabel, dan Daftar Gambar</strong> (jika ada).</li>
                            <li class="mt-2"><strong>BAB I — Pendahuluan</strong>
                                <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                    <li>Latar Belakang (diambil dari template Laporan PKL)</li>
                                    <li>Perencanaan Pembelajaran PKL (diambil dari template Laporan PKL)</li>
                                    <li>Tujuan Pelaksanaan PKL (diambil dari template Laporan PKL)</li>
                                    <li>Manfaat Pelaksanaan PKL (diambil dari template Laporan PKL)</li>
                                </ul>
                            </li>
                            <li class="mt-2"><strong>BAB II — Tinjauan Umum Perusahaan/Instansi</strong>
                                <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                    <li>Sejarah singkat perusahaan/instansi tempat PKL.</li>
                                    <li>Visi dan misi perusahaan.</li>
                                    <li>Struktur organisasi.</li>
                                    <li>Bidang usaha / layanan / produk perusahaan.</li>
                                </ul>
                            </li>
                            <li class="mt-2"><strong>BAB III — Pembahasan / Hasil Kegiatan</strong>
                                <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                    <li>Deskripsi tugas dan kegiatan yang dilaksanakan selama PKL.</li>
                                    <li>Uraian proyek/aplikasi yang dikerjakan (nama proyek, teknologi/bahasa pemrograman yang digunakan, peran dalam tim).</li>
                                    <li>Tahapan pengembangan perangkat lunak yang dilalui (analisis, desain, coding, testing, deployment).</li>
                                    <li>Kendala yang dihadapi selama PKL dan solusi/penyelesaiannya.</li>
                                    <li>Dokumentasi hasil kerja (screenshot aplikasi, diagram, source code penting).</li>
                                </ul>
                            </li>
                            <li class="mt-2"><strong>BAB IV — Penutup</strong>
                                <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                    <li>Kesimpulan hasil pelaksanaan PKL.</li>
                                    <li>Saran bagi sekolah, DU/DI, maupun peserta didik PKL selanjutnya.</li>
                                </ul>
                            </li>
                            <li class="mt-2"><strong>Lampiran</strong>
                                <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                    <li>Jurnal kegiatan harian dan daftar absensi yang telah diisi lengkap.</li>
                                    <li>Fotokopi sertifikat/surat keterangan telah melaksanakan PKL dari DU/DI.</li>
                                    <li>Dokumentasi foto kegiatan selama PKL.</li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="font-bold text-gray-900 mb-2">b. Format penulisan portofolio laporan PKL dibuat dengan ketentuan sebagai berikut:</p>
                        <ul class="list-disc pl-5 space-y-1 text-gray-600">
                            <li>Jenis kertas yang digunakan berukuran A4.</li>
                            <li>Margin pengetikan : Kiri 3 cm. Kanan, Atas, dan Bawah 2,54 cm.</li>
                            <li>Spasi pengetikan : 1,5 spasi.</li>
                            <li>Menggunakan jenis huruf Cambria ukuran 12.</li>
                            <li>Dijilid lakban hitam (tidak jilid jepit), menggunakan plastik bening di sampul depan, dan sampul belakang kertas jeruk berwarna Merah.</li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" id="btnTutupBawah" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none">
                        Tutup & Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Logika Modal -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById('modalPanduanPortofolio');
            const btnBantuan = document.getElementById('btnPanduanInfo');
            const btnTutupAtas = document.getElementById('btnTutupAtas');
            const btnTutupBawah = document.getElementById('btnTutupBawah');
            const backdrop = document.getElementById('backdropModal');

            function bukaModal() {
                modal.style.display = 'block';
            }

            function tutupModal() {
                modal.style.display = 'none';
            }

            // Memastikan modal muncul di awal sesi
            if (!sessionStorage.getItem('telahMelihatPanduanPortofolioPKL')) {
                bukaModal();
                sessionStorage.setItem('telahMelihatPanduanPortofolioPKL', 'true');
            }

            btnBantuan.addEventListener('click', bukaModal);
            btnTutupAtas.addEventListener('click', tutupModal);
            btnTutupBawah.addEventListener('click', tutupModal);
            backdrop.addEventListener('click', tutupModal);
        });
    </script>
</x-app-layout>