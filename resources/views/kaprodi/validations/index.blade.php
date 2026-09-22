<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-sm">
                <i class="fa-solid fa-camera-retro text-xl"></i>
            </div>
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight">
                    Validasi Bukti Siswa
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-0.5">
                    Program Keahlian {{ $kaprodi->major->name ?? $kaprodi->major->code }}
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Background -->
    <div class="fixed inset-0 bg-slate-50 -z-10 pointer-events-none"></div>

    <div class="py-10 min-h-screen relative" x-data="{ 
        previewModal: false, 
        previewImg: '', 
        previewTitle: '',
        rejectModal: false,
        rejectAction: '',
        rejectTitle: '',
        rejectStudent: '',
        rejectImg: '',
        deleteModal: false,
        deleteAction: '',
        deleteTitle: '',
        deleteStudent: '',
        deleteImg: ''
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-start gap-4 shadow-sm">
                    <div class="bg-emerald-100 p-2 rounded-full text-emerald-600 shrink-0">
                        <i class="fa-solid fa-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-emerald-800 font-bold text-sm">Berhasil</h3>
                        <p class="text-emerald-600 text-xs mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-start gap-4 shadow-sm">
                    <div class="bg-red-100 p-2 rounded-full text-red-600 shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-red-800 font-bold text-sm">Terjadi Kesalahan</h3>
                        <ul class="text-red-600 text-xs mt-0.5 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Stats Overview -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Bukti</p>
                    <h4 class="text-3xl font-black text-slate-800">{{ $stats['total'] }}</h4>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                    <p class="text-xs font-bold text-amber-500 uppercase tracking-wider mb-1">Menunggu Review</p>
                    <h4 class="text-3xl font-black text-slate-800">{{ $stats['pending'] }}</h4>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">Disetujui / Valid</p>
                    <h4 class="text-3xl font-black text-slate-800">{{ $stats['approved'] }}</h4>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                    <p class="text-xs font-bold text-red-500 uppercase tracking-wider mb-1">Ditolak</p>
                    <h4 class="text-3xl font-black text-slate-800">{{ $stats['rejected'] }}</h4>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                <form action="{{ route('kaprodi.validations.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Cari Siswa / NISN</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Nama siswa atau NISN..." 
                                   class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fa-solid fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter Type -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Validasi</label>
                        <select name="type" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all" {{ $selectedType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            <option value="weekly" {{ $selectedType === 'weekly' ? 'selected' : '' }}>ACC Mingguan</option>
                            <option value="monitoring" {{ $selectedType === 'monitoring' ? 'selected' : '' }}>Monitoring Guru</option>
                            <option value="final" {{ $selectedType === 'final' ? 'selected' : '' }}>Penilaian Akhir</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <i class="fa-solid fa-filter text-xs"></i> Filter
                        </button>
                        @if($search || $selectedType !== 'all' || $selectedStatus !== 'all')
                            <a href="{{ route('kaprodi.validations.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Content Grid -->
            @if($validations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($validations as $item)
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group">
                            <!-- Card Top Header -->
                            <div class="p-4 border-b border-slate-100 bg-slate-50/60 space-y-2.5">
                                <!-- Type Badge & Phase/Company -->
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border tracking-wide {{ $item['type_badge'] }}">
                                        <i class="fa-solid {{ $item['type_icon'] }} text-[10px]"></i>
                                        {{ $item['type_label'] }}
                                    </span>
                                    <span class="text-[11px] font-medium text-slate-400 truncate max-w-[180px]" title="{{ $item['sub_title'] }}">
                                        {{ $item['sub_title'] }}
                                    </span>
                                </div>

                                <!-- Student Info -->
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-black flex items-center justify-center text-sm shrink-0 border border-blue-200">
                                        {{ strtoupper(substr($item['student']->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold text-slate-800 text-sm truncate leading-snug" title="{{ $item['student']->name }}">
                                            {{ $item['student']->name }}
                                        </h3>
                                        <p class="text-xs text-slate-400 truncate">
                                            {{ $item['student']->nisn }} • {{ $item['student']->class ?? 'Kelas' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 space-y-3.5 flex-1">
                                <!-- Proof Images Grid -->
                                <div class="grid grid-cols-2 gap-2.5">
                                    <!-- Foto Live Kamera -->
                                    <div class="space-y-1.5">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Foto Live Kamera</span>
                                        @if($item['photo'])
                                            <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-4/3 bg-slate-900 group/img cursor-pointer"
                                                 @click="previewModal = true; previewImg = '{{ asset('storage/' . $item['photo']) }}'; previewTitle = 'Foto Live: {{ addslashes($item['student']->name) }} ({{ $item['type_label'] }})'">
                                                <img src="{{ asset('storage/' . $item['photo']) }}" alt="Foto Live" 
                                                     class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-300"
                                                     loading="lazy" decoding="async">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white gap-1.5 text-xs font-semibold">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-xs"></i> Zoom
                                                </div>
                                            </div>
                                        @else
                                            <div class="rounded-xl border border-dashed border-slate-200 aspect-4/3 flex flex-col items-center justify-center bg-slate-50 text-slate-400 text-xs">
                                                <i class="fa-solid fa-camera-slash text-base mb-1"></i>
                                                <span class="text-[11px]">Tidak ada foto</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Tanda Tangan / Paraf -->
                                    <div class="space-y-1.5">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanda Tangan / Paraf</span>
                                        @if($item['signature'])
                                            <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-4/3 bg-white group/sig cursor-pointer flex items-center justify-center p-2"
                                                 @click="previewModal = true; previewImg = '{{ asset('storage/' . $item['signature']) }}'; previewTitle = 'Tanda Tangan: {{ addslashes($item['person_name']) }}'">
                                                <img src="{{ asset('storage/' . $item['signature']) }}" alt="TTD / Paraf" 
                                                     class="max-h-full max-w-full object-contain group-hover/sig:scale-105 transition-transform duration-300"
                                                     loading="lazy" decoding="async">
                                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover/sig:opacity-100 transition-opacity flex items-center justify-center text-white gap-1.5 text-xs font-semibold">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-xs"></i> Zoom
                                                </div>
                                            </div>
                                        @else
                                            <div class="rounded-xl border border-dashed border-slate-200 aspect-4/3 flex flex-col items-center justify-center bg-slate-50 text-slate-400 text-xs">
                                                <i class="fa-solid fa-pen-slash text-base mb-1"></i>
                                                <span class="text-[11px]">Tidak ada TTD</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Description & Status Info -->
                                <div class="bg-slate-50/90 p-3 rounded-xl border border-slate-100 text-xs space-y-2">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-0.5">Keterangan Bukti</span>
                                        <p class="font-bold text-slate-800 leading-snug break-words">{{ $item['title'] }}</p>
                                    </div>
                                    <div class="pt-2 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                        <div>
                                            <span class="text-slate-400 block text-[10px] font-medium">Pembimbing / Guru:</span>
                                            <span class="font-semibold text-slate-700 truncate block" title="{{ $item['person_name'] }}">
                                                {{ $item['person_name'] }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px] font-medium">Waktu Verifikasi:</span>
                                            <span class="font-medium text-slate-600 block">
                                                {{ $item['date_formatted'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rejection Note if Rejected -->
                                @if($item['status'] === 'rejected' && $item['rejection_note'])
                                    <div class="p-3 bg-red-50/80 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                                        <div class="font-bold flex items-center gap-1 text-red-800">
                                            <i class="fa-solid fa-circle-exclamation text-xs"></i> Catatan Penolakan:
                                        </div>
                                        <p class="text-red-600 italic leading-relaxed">"{{ $item['rejection_note'] }}"</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="p-3.5 border-t border-slate-100 bg-white flex flex-wrap items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $item['status_badge'] }}">
                                    @if($item['status'] === 'approved')
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    @elseif($item['status'] === 'rejected')
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    @else
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                                    @endif
                                    {{ $item['status_label'] }}
                                </span>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <!-- Link ke Jurnal Siswa -->
                                    <a href="{{ $item['journal_show_kaprodi'] }}" 
                                       class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-1"
                                       title="Lihat Detail Jurnal">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> <span>Detail</span>
                                    </a>

                                    <!-- Tombol Tolak Bukti -->
                                    @if($item['status'] !== 'rejected')
                                        <button type="button"
                                                @click="
                                                    rejectModal = true; 
                                                    rejectAction = '{{ $item['reject_action_kaprodi'] }}';
                                                    rejectTitle = '{{ addslashes($item['title']) }}';
                                                    rejectStudent = '{{ addslashes($item['student']->name) }} ({{ $item['student']->nisn }})';
                                                    rejectImg = '{{ $item['photo'] ? asset('storage/' . $item['photo']) : ($item['signature'] ? asset('storage/' . $item['signature']) : '') }}';
                                                "
                                                class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-600 text-amber-700 hover:text-white border border-amber-200 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-1"
                                                title="Tolak Bukti dan berikan catatan alasan">
                                            <i class="fa-solid fa-ban text-[10px]"></i> <span>Tolak</span>
                                        </button>
                                    @endif

                                    <!-- Tombol Hapus Bukti -->
                                    <button type="button"
                                            @click="
                                                deleteModal = true; 
                                                deleteAction = '{{ $item['delete_action_kaprodi'] }}';
                                                deleteTitle = '{{ addslashes($item['title']) }}';
                                                deleteStudent = '{{ addslashes($item['student']->name) }} ({{ $item['student']->nisn }})';
                                                deleteImg = '{{ $item['photo'] ? asset('storage/' . $item['photo']) : ($item['signature'] ? asset('storage/' . $item['signature']) : '') }}';
                                            "
                                            class="px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-1"
                                            title="Hapus Bukti Secara Permanen">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i> <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $validations->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center space-y-4 shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto text-2xl border border-slate-100">
                        <i class="fa-solid fa-camera-rotate"></i>
                    </div>
                    <div class="max-w-md mx-auto">
                        <h3 class="font-bold text-slate-800 text-base">Belum Ada Bukti Validasi</h3>
                        <p class="text-xs text-slate-400 mt-1">Belum ada bukti foto live atau tanda tangan yang diunggah siswa program keahlian ini.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- 1. MODAL PREVIEW GAMBAR BESAR -->
        <div x-show="previewModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
             style="display: none;">
            <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl" @click.away="previewModal = false">
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <h4 class="font-bold text-sm text-slate-800 truncate pr-4" x-text="previewTitle"></h4>
                    <button type="button" @click="previewModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="p-6 flex items-center justify-center bg-slate-900 min-h-[300px] max-h-[70vh] overflow-auto">
                    <img :src="previewImg" alt="Preview" class="max-h-[65vh] max-w-full object-contain rounded-xl shadow-lg">
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="previewModal = false" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. MODAL TOLAK BUKTI KAPRODI -->
        <div x-show="rejectModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
             style="display: none;">
            <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl" @click.away="rejectModal = false">
                <form :action="rejectAction" method="POST">
                    @csrf
                    <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-3 bg-red-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 text-base">Tolak Bukti Validasi</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Bukti tidak valid akan di-reset agar siswa/pembimbing dapat memperbarui.</p>
                            </div>
                        </div>
                        <button type="button" @click="rejectModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Info Siswa & Bukti -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Siswa:</span>
                                <span class="font-bold text-slate-800" x-text="rejectStudent"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Bukti:</span>
                                <span class="font-semibold text-slate-700" x-text="rejectTitle"></span>
                            </div>
                        </div>

                        <!-- Input Alasan Penolakan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_note" rows="3" required
                                      placeholder="Tulis alasan penolakan secara jelas (contoh: foto bukan foto live instruktur, foto buram, atau TTD salah)..."
                                      class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Catatan ini akan langsung terlihat di jurnal siswa terkait.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="rejectModal = false" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-ban text-xs"></i> Konfirmasi Tolak Bukti
                        </button>
                    </div>
                </form>
            </div>
        <!-- 3. MODAL HAPUS BUKTI PERMANEN -->
        <div x-show="deleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
             style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl" @click.away="deleteModal = false">
                <form :action="deleteAction" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-3 bg-red-50/70">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-trash-can"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 text-base">Hapus Bukti Validasi</h3>
                                <p class="text-xs text-slate-500 mt-0.5">File foto & tanda tangan akan dihapus permanen.</p>
                            </div>
                        </div>
                        <button type="button" @click="deleteModal = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Thumbnail preview -->
                        <template x-if="deleteImg">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <img :src="deleteImg" alt="Bukti" class="w-14 h-12 object-cover rounded-xl border border-slate-200 shrink-0 bg-white">
                                <div class="min-w-0 text-xs">
                                    <p class="font-bold text-slate-800 truncate" x-text="deleteStudent"></p>
                                    <p class="text-slate-500 truncate mt-0.5" x-text="deleteTitle"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!deleteImg">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-1">
                                <p class="font-bold text-slate-800" x-text="deleteStudent"></p>
                                <p class="text-slate-500" x-text="deleteTitle"></p>
                            </div>
                        </template>

                        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-2.5 text-xs text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600 shrink-0"></i>
                            <span>Apakah Anda yakin ingin menghapus bukti ini secara permanen? File dan tanda tangan akan dihapus dari server dan status verifikasi siswa akan di-reset.</span>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="deleteModal = false" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition-colors shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can text-xs"></i> Ya, Hapus Bukti
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
