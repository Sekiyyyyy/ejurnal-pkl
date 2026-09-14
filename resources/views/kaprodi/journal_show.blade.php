<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('kaprodi.dashboard') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all hover:-translate-x-1">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                        Validasi Jurnal
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Detail jurnal PKL milik <span class="font-bold text-indigo-600">{{ $journal->student->name }}</span></p>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Background Pattern -->
    <div class="absolute top-0 inset-x-0 h-[30rem] bg-gradient-to-b from-indigo-50/50 to-white -z-10 pointer-events-none"></div>

    <div class="py-8 min-h-screen relative" x-data="{ 
        activeTab: 'biodata', 
        modalOpen: false, 
        modalImgSignature: '', 
        modalImgLive: '', 
        modalDate: '',
        modalRejectUrl: '',
        rejectModalOpen: false
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 backdrop-blur-sm p-4 rounded-2xl shadow-sm flex items-start gap-4 animate-[bounce_0.5s_ease-in-out]">
                    <div class="bg-emerald-500 p-2 rounded-full text-white">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h3 class="text-emerald-800 font-bold">Validasi Berhasil</h3>
                        <p class="text-emerald-600 text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 backdrop-blur-sm p-4 rounded-2xl shadow-sm flex items-start gap-4">
                    <div class="bg-red-500 p-2 rounded-full text-white">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-red-800 font-bold">Terjadi Kesalahan</h3>
                        <ul class="list-disc list-inside text-red-600 text-sm mt-1 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            <!-- Aksi Validasi (Keseluruhan Jurnal) -->
            @if($journal->kaprodi_status === 'WAITING_KAPROG' || $journal->kaprodi_status === 'REJECTED')
            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-8 shadow-2xl relative overflow-hidden group border border-indigo-500/30">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 pointer-events-none"></div>
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl group-hover:bg-indigo-500/40 transition-all duration-700"></div>
                
                <h3 class="text-lg font-bold text-white mb-6 relative z-10 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/20">
                        <i class="fa-solid fa-shield-halved text-indigo-300 text-sm"></i>
                    </div>
                    Keputusan Validasi Akhir
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <!-- Tombol ACC -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 hover:border-emerald-400/50 hover:bg-white/20 transition-all group/acc">
                        <h4 class="font-bold text-emerald-300 text-lg mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-check-circle"></i> Setujui Jurnal (ACC)
                        </h4>
                        <p class="text-sm text-indigo-100/70 mb-6">Tanda tangan digital Anda akan disematkan secara otomatis pada dokumen cetak siswa.</p>
                        <form action="{{ route('kaprodi.journal.approve', $journal->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui jurnal ini secara keseluruhan?');">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-900 text-sm font-bold py-3.5 px-6 rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-file-signature"></i> ACC JURNAL
                            </button>
                        </form>
                    </div>

                    <!-- Tombol Reject -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 hover:border-red-400/50 hover:bg-white/20 transition-all group/reject">
                        <h4 class="font-bold text-red-300 text-lg mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-circle-xmark"></i> Tolak Secara Keseluruhan
                        </h4>
                        <p class="text-sm text-indigo-100/70 mb-4">Gunakan fitur ini hanya jika terdapat kesalahan mayor pada jurnal (status akan diubah menjadi Ditolak).</p>
                        <form action="{{ route('kaprodi.journal.reject', $journal->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak keseluruhan jurnal ini?');">
                            @csrf
                            <div class="mb-4">
                                <textarea name="rejection_note" class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl text-sm text-white placeholder-slate-400 focus:border-red-400 focus:ring-1 focus:ring-red-400/50 py-3 transition-colors" rows="2" placeholder="Tuliskan alasan penolakan..." required></textarea>
                            </div>
                            <button type="submit" class="w-full bg-slate-800 hover:bg-red-500 text-white text-sm font-bold py-3.5 px-6 rounded-xl border border-slate-600 hover:border-transparent hover:shadow-[0_0_20px_rgba(239,68,68,0.4)] transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ban"></i> TOLAK JURNAL
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content Area -->
            <div class="bg-white/80 backdrop-blur-2xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/60 overflow-hidden">
                
                <!-- Beautiful Tabs Navigation -->
                <div class="bg-slate-50/50 border-b border-slate-100 p-2 md:p-3">
                    <nav class="flex flex-wrap md:flex-nowrap gap-2" aria-label="Tabs">
                        <button @click="activeTab = 'biodata'" 
                            :class="{'bg-white shadow-sm border border-slate-200 text-indigo-600': activeTab === 'biodata', 'text-slate-500 hover:bg-white/60 hover:text-slate-700': activeTab !== 'biodata'}" 
                            class="flex-1 min-w-[120px] px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-regular fa-id-card"></i> Biodata
                        </button>
                        <button @click="activeTab = 'jurnal'" 
                            :class="{'bg-white shadow-sm border border-slate-200 text-indigo-600': activeTab === 'jurnal', 'text-slate-500 hover:bg-white/60 hover:text-slate-700': activeTab !== 'jurnal'}" 
                            class="flex-1 min-w-[120px] px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-book-open"></i> Logbook
                        </button>
                        <button @click="activeTab = 'nilai'" 
                            :class="{'bg-white shadow-sm border border-slate-200 text-indigo-600': activeTab === 'nilai', 'text-slate-500 hover:bg-white/60 hover:text-slate-700': activeTab !== 'nilai'}" 
                            class="flex-1 min-w-[120px] px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-award"></i> Nilai Akhir
                        </button>
                        <button @click="activeTab = 'monitoring'" 
                            :class="{'bg-white shadow-sm border border-slate-200 text-indigo-600': activeTab === 'monitoring', 'text-slate-500 hover:bg-white/60 hover:text-slate-700': activeTab !== 'monitoring'}" 
                            class="flex-1 min-w-[120px] px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass-chart"></i> Monitoring
                        </button>
                    </nav>
                </div>

                <div class="p-6 md:p-10">
                    
                    <!-- TAB BIODATA -->
                    <div x-show="activeTab === 'biodata'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Profil Siswa -->
                            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-8 flex items-start gap-6">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-600 font-bold text-3xl shadow-inner shrink-0">
                                    {{ substr($journal->student->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Informasi Siswa</h4>
                                    <p class="text-2xl font-black text-slate-800 mb-2">{{ $journal->student->name }}</p>
                                    <div class="space-y-1 mt-4">
                                        <div class="flex items-center gap-3 text-sm text-slate-600">
                                            <i class="fa-solid fa-id-badge text-slate-400 w-5 text-center"></i>
                                            <span class="font-medium">NISN: {{ $journal->student->nisn }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-slate-600">
                                            <i class="fa-solid fa-graduation-cap text-slate-400 w-5 text-center"></i>
                                            <span class="font-medium">{{ $journal->student->class }} / {{ $journal->student->major->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail PKL -->
                            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-8">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-5 border-b border-slate-200 pb-2">Informasi Praktik Kerja Lapangan</h4>
                                
                                <div class="space-y-5">
                                    <div>
                                        <p class="text-xs text-slate-500 mb-1">Perusahaan / Tempat PKL</p>
                                        <p class="font-bold text-slate-800">{{ $journal->company_name ?? 'Belum Diisi' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">Fase Jurnal</p>
                                            <div class="inline-flex items-center justify-center px-3 py-1 bg-indigo-50 text-indigo-700 text-sm font-bold rounded-lg border border-indigo-100">Fase {{ $journal->phase }}</div>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">Status Kaprodi</p>
                                            <p class="font-bold text-slate-800">{{ $journal->kaprodi_status }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB LOGBOOK -->
                    <div x-show="activeTab === 'jurnal'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="space-y-8">
                        @php
                            $startDate = $journal->start_date ? \Carbon\Carbon::parse($journal->start_date)->startOfWeek() : now()->startOfWeek();
                            $weeklyApprovals = $journal->weeklyApprovals()->orderBy('week_number', 'asc')->get();
                        @endphp

                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-2">
                            <h3 class="font-bold text-slate-800 text-lg">Pemeriksaan Logbook Mingguan</h3>
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold shadow-sm border border-slate-200">{{ $weeklyApprovals->count() }} Minggu Tercatat</span>
                        </div>

                        @forelse($weeklyApprovals as $wa)
                            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm relative overflow-hidden group hover:shadow-md hover:border-indigo-200 transition-all">
                                
                                <!-- Status Ribbon -->
                                @if($wa->is_rejected)
                                    <div class="absolute top-6 right-6 bg-red-50 text-red-700 border border-red-200 text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm">
                                        <i class="fa-solid fa-xmark"></i> DITOLAK
                                    </div>
                                @elseif($wa->approved_at)
                                    <div class="absolute top-6 right-6 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm">
                                        <i class="fa-solid fa-check text-emerald-500"></i> ACC INSTRUKTUR
                                    </div>
                                @endif

                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500 font-black text-xl group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                        {{ $wa->week_number }}
                                    </div>
                                    <div>
                                        <h4 class="text-base font-bold text-slate-800">Laporan Minggu Ke-{{ $wa->week_number }}</h4>
                                        <p class="text-sm text-slate-500">Verifikasi tanda tangan dan bukti foto harian</p>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col md:flex-row gap-8">
                                    <div class="w-full md:w-1/3">
                                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 relative group/img cursor-pointer"
                                             @click="modalOpen = true; modalImgLive = '{{ asset('storage/' . $wa->instructor_live_photo) }}'; modalImgSignature = '{{ $wa->instructor_paraf ? asset('storage/' . $wa->instructor_paraf) : '' }}'; modalDate = 'Logbook Minggu {{ $wa->week_number }}'; modalRejectUrl = '{{ route('kaprodi.journal.reject-weekly', $wa->id) }}'">
                                            
                                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                                <i class="fa-solid fa-camera"></i> Live Photo
                                            </p>
                                            
                                            <div class="relative rounded-xl overflow-hidden shadow-inner bg-slate-200 h-40">
                                                @if($wa->instructor_live_photo)
                                                    <img src="{{ asset('storage/' . $wa->instructor_live_photo) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110">
                                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                        <span class="bg-white/20 text-white border border-white/40 px-4 py-2 rounded-lg text-sm font-bold shadow-lg backdrop-blur-md">
                                                            <i class="fa-solid fa-magnifying-glass-plus mr-1"></i> Perbesar
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                                                        <i class="fa-regular fa-image text-3xl"></i>
                                                        <span class="text-xs font-medium">Tidak Ada Foto</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="w-full md:w-2/3 flex flex-col justify-center">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Aksi Validasi Spesifik</p>
                                        
                                        @if(!$wa->is_rejected)
                                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 hover:bg-red-50 hover:border-red-100 transition-colors group/btn cursor-pointer"
                                             @click="rejectModalOpen = true; modalRejectUrl = '{{ route('kaprodi.journal.reject-weekly', $wa->id) }}'">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover/btn:bg-red-100 group-hover/btn:text-red-500 group-hover/btn:border-red-200 transition-colors">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-slate-700 group-hover/btn:text-red-700 transition-colors">Tolak Logbook Minggu Ini</h5>
                                                    <p class="text-xs text-slate-500 group-hover/btn:text-red-600/70 mt-1">Gunakan ini jika foto atau tanda tangan terindikasi palsu.</p>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="bg-red-50/50 border border-red-100 rounded-2xl p-5">
                                            <div class="flex items-start gap-4">
                                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                                                    <i class="fa-solid fa-ban"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-red-800">Telah Ditolak Kaprodi</h5>
                                                    <p class="text-sm text-red-700/80 mt-1 italic">"{{ $wa->rejection_note }}"</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-12 text-center">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                                    <i class="fa-regular fa-calendar-xmark text-3xl text-slate-300"></i>
                                </div>
                                <h4 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Logbook</h4>
                                <p class="text-sm text-slate-500">Siswa belum mengajukan persetujuan mingguan apa pun.</p>
                            </div>
                        @endforelse
                        </div>
                    </div>

                    <!-- TAB NILAI AKHIR -->
                    <div x-show="activeTab === 'nilai'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
                            <h3 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-4 mb-6">Pemeriksaan Form Penilaian Instruktur</h3>
                            
                            <div class="flex flex-col md:flex-row gap-10">
                                <div class="w-full md:w-1/3">
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 relative group cursor-pointer"
                                         @click="modalOpen = true; modalImgLive = '{{ asset('storage/' . $journal->instructor_live_photo) }}'; modalImgSignature = '{{ $journal->instructor_signature ? asset('storage/' . $journal->instructor_signature) : '' }}'; modalDate = 'Penilaian Akhir PKL'; modalRejectUrl = '{{ route('kaprodi.journal.reject-final', $journal->id) }}'">
                                        
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-camera"></i> Live Photo
                                        </p>
                                        
                                        <div class="relative rounded-xl overflow-hidden shadow-inner bg-slate-200 h-64">
                                            @if($journal->instructor_live_photo)
                                                <img src="{{ asset('storage/' . $journal->instructor_live_photo) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                    <span class="bg-white/20 text-white border border-white/40 px-4 py-2 rounded-lg text-sm font-bold shadow-lg backdrop-blur-md">
                                                        <i class="fa-solid fa-expand mr-1"></i> Lihat Penuh
                                                    </span>
                                                </div>
                                            @else
                                                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                                                    <i class="fa-solid fa-image-portrait text-4xl"></i>
                                                    <span class="text-xs font-medium">Belum Ada Foto</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="w-full md:w-2/3 flex flex-col justify-center">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Aksi Validasi Spesifik</p>
                                    
                                    @if(!$journal->instructor_rejection_note)
                                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 hover:bg-red-50 hover:border-red-100 transition-colors group/btn cursor-pointer shadow-sm"
                                             @click="rejectModalOpen = true; modalRejectUrl = '{{ route('kaprodi.journal.reject-final', $journal->id) }}'">
                                            <div class="flex items-center gap-5">
                                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover/btn:bg-red-100 group-hover/btn:text-red-500 group-hover/btn:border-red-200 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-trash-can text-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-base font-bold text-slate-800 group-hover/btn:text-red-700 transition-colors">Tolak & Hapus Penilaian Ini</h5>
                                                    <p class="text-sm text-slate-500 group-hover/btn:text-red-600/70 mt-1 leading-relaxed">Menghapus seluruh input nilai akhir beserta foto dari instruktur jika terindikasi pelanggaran otorisasi.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-red-50/50 border border-red-100 rounded-2xl p-6">
                                            <div class="flex items-start gap-4">
                                                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-base font-bold text-red-800">Telah Ditolak & Dihapus Kaprodi</h5>
                                                    <p class="text-sm text-red-700 mt-2 italic bg-red-100/50 p-3 rounded-lg border border-red-100">"{{ $journal->instructor_rejection_note }}"</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB MONITORING GURU -->
                    <div x-show="activeTab === 'monitoring'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
                            <h3 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-4 mb-6">Pemeriksaan Form Monitoring Guru</h3>
                            
                            <div class="flex flex-col md:flex-row gap-10">
                                <div class="w-full md:w-1/3">
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 relative group cursor-pointer"
                                         @click="modalOpen = true; modalImgLive = '{{ asset('storage/' . $journal->teacher_live_photo) }}'; modalImgSignature = '{{ $journal->teacher_signature ? asset('storage/' . $journal->teacher_signature) : '' }}'; modalDate = 'Monitoring Guru Pembimbing'; modalRejectUrl = '{{ route('kaprodi.journal.reject-monitoring', $journal->id) }}'">
                                        
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-camera"></i> Live Photo Guru
                                        </p>
                                        
                                        <div class="relative rounded-xl overflow-hidden shadow-inner bg-slate-200 h-64">
                                            @if($journal->teacher_live_photo)
                                                <img src="{{ asset('storage/' . $journal->teacher_live_photo) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                    <span class="bg-white/20 text-white border border-white/40 px-4 py-2 rounded-lg text-sm font-bold shadow-lg backdrop-blur-md">
                                                        <i class="fa-solid fa-expand mr-1"></i> Lihat Penuh
                                                    </span>
                                                </div>
                                            @else
                                                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                                                    <i class="fa-solid fa-chalkboard-user text-4xl"></i>
                                                    <span class="text-xs font-medium">Belum Ada Foto</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="w-full md:w-2/3 flex flex-col justify-center">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Aksi Validasi Spesifik</p>
                                    
                                    @if(!$journal->teacher_rejection_note)
                                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 hover:bg-red-50 hover:border-red-100 transition-colors group/btn cursor-pointer shadow-sm"
                                             @click="rejectModalOpen = true; modalRejectUrl = '{{ route('kaprodi.journal.reject-monitoring', $journal->id) }}'">
                                            <div class="flex items-center gap-5">
                                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover/btn:bg-red-100 group-hover/btn:text-red-500 group-hover/btn:border-red-200 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-eraser text-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-base font-bold text-slate-800 group-hover/btn:text-red-700 transition-colors">Tolak & Hapus Data Monitoring</h5>
                                                    <p class="text-sm text-slate-500 group-hover/btn:text-red-600/70 mt-1 leading-relaxed">Pilih ini jika foto terindikasi bukan merupakan Guru Pembimbing yang bersangkutan.</p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-red-50/50 border border-red-100 rounded-2xl p-6">
                                            <div class="flex items-start gap-4">
                                                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-base font-bold text-red-800">Telah Ditolak & Dihapus Kaprodi</h5>
                                                    <p class="text-sm text-red-700 mt-2 italic bg-red-100/50 p-3 rounded-lg border border-red-100">"{{ $journal->teacher_rejection_note }}"</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Rejection Modal -->
        <div x-show="rejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition.opacity>
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="rejectModalOpen = false"></div>
                
                <div class="inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-white relative z-10"
                     x-show="rejectModalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                    
                    <form :action="modalRejectUrl" method="POST">
                        @csrf
                        <div class="px-8 pt-8 pb-6 bg-white relative">
                            <!-- Decorative header blur -->
                            <div class="absolute top-0 inset-x-0 h-24 bg-gradient-to-b from-red-50 to-transparent -z-10"></div>
                            
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-full bg-red-100 text-red-500 flex items-center justify-center shadow-inner">
                                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800 tracking-tight">Konfirmasi Penolakan</h3>
                            </div>
                            
                            <p class="text-sm text-slate-500 mb-6 leading-relaxed">Anda akan menolak dan menghapus data validasi terkait. Mohon sertakan alasan yang jelas agar siswa/guru/instruktur dapat melakukan perbaikan.</p>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">Alasan Penolakan</label>
                                <textarea name="rejection_note" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:border-red-400 focus:ring-4 focus:ring-red-400/10 py-3 transition-colors resize-none" placeholder="Misal: Foto buram atau bukan orang yang bersangkutan..." required></textarea>
                            </div>
                        </div>
                        <div class="px-8 py-5 bg-slate-50/80 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" @click="rejectModalOpen = false" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-red-500 text-white text-sm font-bold rounded-xl shadow-[0_4px_12px_rgba(239,68,68,0.25)] hover:bg-red-600 hover:shadow-[0_4px_15px_rgba(239,68,68,0.35)] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                                Konfirmasi & Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Live Photo & Signature Modal (Glassmorphism Dark) -->
        <div x-show="modalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;" x-transition.opacity>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" @click="modalOpen = false"></div>
                
                <div class="inline-block w-full max-w-5xl overflow-hidden text-left align-middle transition-all transform bg-slate-900/90 shadow-[0_0_50px_rgba(0,0,0,0.5)] rounded-3xl border border-slate-700/50 backdrop-blur-2xl relative z-10"
                     x-show="modalOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                    
                    <div class="p-8 relative">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-700/50">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 shadow-inner">
                                    <i class="fa-solid fa-fingerprint text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white tracking-tight" x-text="modalDate"></h3>
                                    <p class="text-indigo-400 text-sm font-medium mt-1 uppercase tracking-wider">Verifikasi Otorisasi Kredensial</p>
                                </div>
                            </div>
                            <button @click="modalOpen = false" class="text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 w-10 h-10 rounded-full flex items-center justify-center border border-slate-700 transition-colors">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Content Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Live Photo Panel -->
                            <div class="flex flex-col bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden relative group/zoom">
                                <div class="px-5 py-4 bg-slate-800/80 border-b border-slate-700/50 flex items-center gap-3">
                                    <i class="fa-solid fa-camera text-slate-400"></i>
                                    <h4 class="text-sm font-bold text-slate-200 uppercase tracking-widest">Kamera Langsung</h4>
                                </div>
                                <div class="flex-grow flex items-center justify-center p-4 bg-black/40 min-h-[400px]">
                                    <template x-if="modalImgLive">
                                        <img :src="modalImgLive" class="max-h-[500px] w-auto object-contain rounded-xl shadow-2xl">
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Signature Panel -->
                            <div class="flex flex-col bg-slate-800/50 border border-slate-700/50 rounded-2xl overflow-hidden">
                                <div class="px-5 py-4 bg-slate-800/80 border-b border-slate-700/50 flex items-center gap-3">
                                    <i class="fa-solid fa-pen-nib text-slate-400"></i>
                                    <h4 class="text-sm font-bold text-slate-200 uppercase tracking-widest">Tanda Tangan / Paraf Digital</h4>
                                </div>
                                <div class="flex-grow flex items-center justify-center p-4 bg-slate-200/90 relative min-h-[400px]">
                                    <!-- Grid Pattern Overlay -->
                                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] opacity-20 pointer-events-none"></div>
                                    
                                    <template x-if="modalImgSignature">
                                        <img :src="modalImgSignature" class="max-h-[300px] w-auto object-contain p-4 mix-blend-multiply" style="filter: contrast(1.5);">
                                    </template>
                                    <template x-if="!modalImgSignature">
                                        <div class="flex flex-col items-center justify-center gap-3 text-slate-500">
                                            <i class="fa-solid fa-file-signature text-5xl opacity-50"></i>
                                            <p class="text-sm font-bold uppercase tracking-widest">Tidak Dilampirkan</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
