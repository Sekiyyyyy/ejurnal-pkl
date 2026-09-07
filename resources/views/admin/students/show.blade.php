<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Detail Akun Siswa: <span class="text-indigo-600">{{ $student->name }}</span>
            </h2>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        activeTab: 'biodata', 
        modalOpen: false, 
        modalImgSignature: '', 
        modalImgLive: '', 
        modalDate: '',
        modalRejectUrl: '',
        rejectModalOpen: false
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs Navigation -->
            <div class="bg-white shadow-sm sm:rounded-t-xl border-b border-gray-200 overflow-x-auto">
                <nav class="flex px-4 sm:px-6 space-x-2 sm:space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'biodata'" 
                        :class="{'border-indigo-500 text-indigo-600': activeTab === 'biodata', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'biodata'}" 
                        class="whitespace-nowrap py-4 px-3 border-b-4 font-bold text-sm sm:text-base transition-colors duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Biodata Siswa
                    </button>
                    <button @click="activeTab = 'jurnal'" 
                        :class="{'border-indigo-500 text-indigo-600': activeTab === 'jurnal', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'jurnal'}" 
                        class="whitespace-nowrap py-4 px-3 border-b-4 font-bold text-sm sm:text-base transition-colors duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        Isi Jurnal
                    </button>
                    <button @click="activeTab = 'nilai'" 
                        :class="{'border-indigo-500 text-indigo-600': activeTab === 'nilai', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'nilai'}" 
                        class="whitespace-nowrap py-4 px-3 border-b-4 font-bold text-sm sm:text-base transition-colors duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                        Nilai Akhir
                    </button>
                    <button @click="activeTab = 'validasi'" 
                        :class="{'border-indigo-500 text-indigo-600': activeTab === 'validasi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'validasi'}" 
                        class="whitespace-nowrap py-4 px-3 border-b-4 font-bold text-sm sm:text-base transition-colors duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        Histori Validasi
                    </button>
                    <button @click="activeTab = 'monitoring'" 
                        :class="{'border-indigo-500 text-indigo-600': activeTab === 'monitoring', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'monitoring'}" 
                        class="whitespace-nowrap py-4 px-3 border-b-4 font-bold text-sm sm:text-base transition-colors duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        Hasil Monitoring
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="bg-white shadow-sm sm:rounded-b-xl min-h-[500px]">
                
                <!-- TAB 1: BIODATA -->
                <div x-show="activeTab === 'biodata'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 sm:p-8" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Informasi Akun</h3>
                            <dl class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                    <dd class="mt-1 text-base font-bold text-gray-900">{{ $student->name }}</dd>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-500">NISN</dt>
                                    <dd class="mt-1 text-base font-bold text-gray-900">{{ $student->nisn }}</dd>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-500">Email Akun</dt>
                                    <dd class="mt-1 text-base font-bold text-gray-900">{{ $student->user->email ?? '-' }}</dd>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-500">Jurusan</dt>
                                    <dd class="mt-1 text-base font-bold text-gray-900">{{ $student->major->name ?? 'Belum ada jurusan' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">Status Jurnal PKL</h3>
                            @if($student->journals->isEmpty())
                                <div class="bg-yellow-50 text-yellow-700 p-4 rounded-lg border border-yellow-200">
                                    Siswa belum memiliki data Jurnal PKL.
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($student->journals as $journal)
                                        <div class="border rounded-lg p-4 {{ $journal->status == 'approved' ? 'border-green-300 bg-green-50' : 'border-gray-200' }}">
                                            <div class="flex justify-between items-center mb-2">
                                                <h4 class="font-bold text-lg text-gray-800">PKL Tahap {{ $journal->phase }}</h4>
                                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $journal->status == 'approved' ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                                    {{ strtoupper($journal->status ?? 'Aktif') }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 mb-1">
                                                <span class="font-semibold">Tempat:</span> {{ $journal->company_name ?? '-' }}
                                            </div>
                                            <div class="text-sm text-gray-600 mb-4">
                                                <span class="font-semibold">Instruktur:</span> {{ $journal->instructor_name ?? '-' }}
                                            </div>
                                            <!-- Reset Button untuk Testing -->
                                            <div class="border-t pt-3 flex justify-end">
                                                <form action="{{ route('admin.students.reset-journal', ['student_id' => $student->id, 'journal_id' => $journal->id]) }}" method="POST" onsubmit="return confirm('YAKIN RESET PROGRESS? Semua data harian, nilai, tanda tangan, foto akan dihapus (kecuali data profil PKL & penempatan).');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-bold py-1 px-3 rounded flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                        Reset Progress Jurnal
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 2: ISI JURNAL -->
                <div x-show="activeTab === 'jurnal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 sm:p-8" style="display: none;">
                    @forelse($student->journals as $journal)
                        <div class="mb-8 last:mb-0">
                            <h3 class="text-xl font-black text-gray-900 mb-4 pb-2 border-b-2 border-indigo-100 flex items-center gap-2">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">{{ $journal->phase }}</span>
                                Rekap Kegiatan Harian (PKL Tahap {{ $journal->phase }})
                            </h3>
                            
                            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                        <tr>
                                            <th class="px-4 py-3 w-32">Tanggal</th>
                                            <th class="px-4 py-3">Kegiatan</th>
                                            <th class="px-4 py-3 w-32 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($journal->dailyActivities as $activity)
                                            <tr class="hover:bg-indigo-50/50 transition">
                                                <td class="px-4 py-3 whitespace-nowrap font-medium">{{ \Carbon\Carbon::parse($activity->date)->format('d M Y') }}</td>
                                                <td class="px-4 py-3">{{ $activity->activity }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($activity->is_approved)
                                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">ACC</span>
                                                    @else
                                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">Belum ada catatan kegiatan harian.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jurnal</h3>
                            <p class="mt-1 text-sm text-gray-500">Siswa ini belum memulai jurnal PKL.</p>
                        </div>
                    @endforelse
                </div>

                <!-- TAB 3: NILAI AKHIR -->
                <div x-show="activeTab === 'nilai'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 sm:p-8" style="display: none;">
                    @forelse($student->journals as $journal)
                        <div class="mb-12 last:mb-0">
                            <h3 class="text-xl font-black text-gray-900 mb-6 pb-2 border-b-2 border-indigo-100 flex items-center gap-2">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">{{ $journal->phase }}</span>
                                Penilaian Akhir (PKL Tahap {{ $journal->phase }})
                            </h3>
                            
                            <!-- Highlight Box: Final Validation Proof -->
                            <div class="mb-8 relative overflow-hidden bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-2xl shadow-sm p-6 sm:p-8">
                                <!-- Ribbon/Badge -->
                                <div class="absolute top-0 right-0">
                                    <div class="bg-red-500 text-white text-xs font-bold px-4 py-1 rounded-bl-lg shadow">BUKTI VALIDASI FINAL</div>
                                </div>
                                
                                <div class="flex flex-col md:flex-row items-center gap-8">
                                    <div class="w-full md:w-1/3 flex flex-col items-center">
                                        <div class="text-center mb-3">
                                            <p class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-1">Foto Live Instruktur</p>
                                            <p class="text-xs text-indigo-600 bg-indigo-100 inline-block px-2 py-1 rounded">Diambil saat penilaian akhir</p>
                                        </div>
                                        <div class="relative group w-48 h-48 rounded-xl overflow-hidden shadow-md border-4 border-white cursor-pointer"
                                             @click="modalOpen = true; modalImgLive = '{{ $journal->instructor_live_photo ? asset('storage/' . $journal->instructor_live_photo) : '' }}'; modalImgSignature = '{{ $journal->instructor_signature ? asset('storage/' . $journal->instructor_signature) : '' }}'; modalDate = 'Penilaian Akhir PKL {{ $journal->phase }}'; modalRejectUrl = '{{ $journal->status == 'COMPLETED' ? route('admin.students.reject-final-assessment', $journal->id) : '' }}'">
                                            @if($journal->instructor_live_photo)
                                                <img src="{{ asset('storage/' . $journal->instructor_live_photo) }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" alt="Live Photo Instruktur">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center">
                                                    <svg class="text-white opacity-0 group-hover:opacity-100 h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                                </div>
                                            @else
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="w-full md:w-2/3">
                                        <div class="bg-white rounded-xl p-6 shadow-sm border border-indigo-100 h-full flex flex-col justify-center">
                                            <h4 class="font-bold text-gray-800 text-lg mb-2">Otorisasi Instruktur</h4>
                                            <p class="text-sm text-gray-600 mb-6">Bukti ini disubmit oleh instruktur industri sebagai verifikasi sah bahwa nilai yang diberikan valid dan tidak direkayasa.</p>
                                            
                                            <div class="flex items-center gap-6">
                                                <div class="w-1/2">
                                                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Tanda Tangan</p>
                                                    <div class="h-24 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center p-2 cursor-pointer hover:bg-gray-100 transition"
                                                         @click="modalOpen = true; modalImgLive = '{{ $journal->instructor_live_photo ? asset('storage/' . $journal->instructor_live_photo) : '' }}'; modalImgSignature = '{{ $journal->instructor_signature ? asset('storage/' . $journal->instructor_signature) : '' }}'; modalDate = 'Penilaian Akhir PKL {{ $journal->phase }}'; modalRejectUrl = '{{ $journal->status == 'COMPLETED' ? route('admin.students.reject-final-assessment', $journal->id) : '' }}'">
                                                        @if($journal->instructor_signature)
                                                            <img src="{{ asset('storage/' . $journal->instructor_signature) }}" class="max-h-full max-w-full object-contain" alt="TTD Instruktur">
                                                        @else
                                                            <span class="text-gray-400 text-xs italic">Belum ada tanda tangan</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="w-1/2">
                                                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Informasi Instruktur</p>
                                                    <div class="text-sm">
                                                        <div class="font-bold text-gray-900">{{ $journal->instructor_name ?? 'Belum diset' }}</div>
                                                        <div class="text-gray-500 text-xs mb-2">{{ $journal->instructor_position ?? 'Instruktur PKL' }}</div>
                                                        <div class="text-gray-600 text-xs flex items-center gap-1">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                                            {{ $journal->company_name ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Lembar Observasi -->
                            <h4 class="font-bold text-lg text-gray-800 mb-4 mt-8">Lembar Observasi</h4>
                            @php
                                $obsPoints = \App\Models\Assessment::where('major_id', $student->major_id)
                                    ->where('category', 'observation_point')
                                    ->whereNull('parent_id')
                                    ->with('children')
                                    ->orderBy('order_number')
                                    ->get();
                                $existing = $journal->assessments->keyBy('assessment_id');
                            @endphp
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                @foreach($obsPoints as $index => $point)
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                                        <h5 class="font-bold text-md text-gray-800 mb-4">{{ $index + 1 }}. {{ $point->name }}</h5>
                                        <div class="space-y-3 mb-4">
                                            @foreach($point->children as $child)
                                                <div class="flex items-start justify-between border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                                    <div class="text-sm text-gray-700 pr-4">
                                                        @if($point->order_number == 3)
                                                            {{ optional($existing[$child->id] ?? null)->description ?: 'Kompetensi tidak diisi' }}
                                                        @else
                                                            {{ $child->name }}
                                                        @endif
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        @if(optional($existing[$child->id] ?? null)->is_yes === 1)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ya</span>
                                                        @elseif(optional($existing[$child->id] ?? null)->is_yes === 0)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak</span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="bg-white border border-gray-100 rounded-lg p-3">
                                            <p class="text-xs font-bold text-gray-500 uppercase mb-1">Catatan Instruktur</p>
                                            <p class="text-sm text-gray-700 italic">
                                                "{{ optional($existing[$point->id] ?? null)->description ?: 'Tidak ada catatan.' }}"
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Table Nilai -->
                            <h4 class="font-bold text-lg text-gray-800 mb-4">Rincian Nilai Aspek</h4>
                            <div class="overflow-hidden border border-gray-200 rounded-xl shadow-sm">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                        <tr>
                                            <th class="px-6 py-4 w-1/2">Aspek Penilaian</th>
                                            <th class="px-6 py-4 text-center">Nilai Angka</th>
                                            <th class="px-6 py-4 text-center">Predikat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($journal->assessments->whereNotNull('score') as $assessment)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 font-medium text-gray-900">
                                                    {{ $assessment->assessment->category == 'grade_custom' ? $assessment->description : $assessment->assessment->name }}
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg font-bold">
                                                        {{ $assessment->score }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @php
                                                        $score = $assessment->score;
                                                        $predicate = 'E';
                                                        $color = 'text-red-600 bg-red-100';
                                                        if($score >= 90) { $predicate = 'A (Sangat Baik)'; $color = 'text-green-700 bg-green-100'; }
                                                        elseif($score >= 80) { $predicate = 'B (Baik)'; $color = 'text-blue-700 bg-blue-100'; }
                                                        elseif($score >= 70) { $predicate = 'C (Cukup)'; $color = 'text-yellow-700 bg-yellow-100'; }
                                                        elseif($score >= 60) { $predicate = 'D (Kurang)'; $color = 'text-orange-700 bg-orange-100'; }
                                                    @endphp
                                                    <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold {{ $color }}">
                                                        {{ $predicate }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">Belum ada nilai yang diinput oleh instruktur.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500">Siswa ini belum memulai jurnal PKL.</p>
                        </div>
                    @endforelse
                </div>

                <!-- TAB 4: HISTORI VALIDASI (Thumbnail Grid) -->
                <div x-show="activeTab === 'validasi'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 sm:p-8" style="display: none;">
                    
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-gray-900 mb-2">Audit Kehadiran Instruktur</h2>
                        <p class="text-gray-600 text-sm">Setiap kotak di bawah ini mewakili validasi mingguan yang dilakukan instruktur menggunakan kamera secara langsung (Live Camera).</p>
                    </div>

                    @forelse($student->journals as $journal)
                        <div class="mb-10 last:mb-0">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-100 inline-block">
                                PKL Tahap {{ $journal->phase }} - Histori ACC Mingguan
                            </h3>
                            
                            @if($journal->weeklyApprovals->isEmpty())
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center text-gray-500 italic">
                                    Belum ada data validasi mingguan pada tahap ini.
                                </div>
                            @else
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    @foreach($journal->weeklyApprovals as $wa)
                                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 group cursor-pointer"
                                             @click="modalOpen = true; modalImgLive = '{{ $wa->instructor_live_photo ? asset('storage/' . $wa->instructor_live_photo) : '' }}'; modalImgSignature = '{{ $wa->instructor_paraf ? asset('storage/' . $wa->instructor_paraf) : '' }}'; modalDate = 'ACC Minggu Ke-{{ $wa->week_number }} | {{ $wa->approved_at ? $wa->approved_at->format('d M Y, H:i') : 'Unknown' }}'; modalRejectUrl = '{{ !$wa->is_rejected ? route('admin.students.reject-weekly-approval', $wa->id) : '' }}'">
                                            
                                            <!-- Thumbnail -->
                                            <div class="h-40 bg-gray-100 relative overflow-hidden">
                                                @if($wa->instructor_live_photo)
                                                    <img src="{{ asset('storage/' . $wa->instructor_live_photo) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Live Photo Minggu {{ $wa->week_number }}">
                                                    
                                                    <!-- Lightbox Icon overlay -->
                                                    <div class="absolute inset-0 bg-indigo-900 bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                                        <svg class="text-white opacity-0 group-hover:opacity-100 h-10 w-10 transform scale-50 group-hover:scale-100 transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center h-full text-gray-400 flex-col">
                                                        <svg class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                        <span class="text-xs">No Photo</span>
                                                    </div>
                                                @endif
                                                
                                                <!-- Status Badge -->
                                                <div class="absolute top-2 right-2 flex flex-col gap-1 items-end">
                                                    @if($wa->is_rejected)
                                                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">DITOLAK</span>
                                                    @else
                                                        <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow">VALIDATED</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Card Info -->
                                            <div class="p-4 border-t border-gray-100">
                                                <h4 class="font-bold text-gray-900 text-sm">ACC Minggu Ke-{{ $wa->week_number }}</h4>
                                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    {{ $wa->approved_at ? $wa->approved_at->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500">Siswa ini belum memiliki data jurnal.</p>
                        </div>
                    @endforelse
                </div>

                <!-- TAB 5: HASIL MONITORING GURU -->
                <div x-show="activeTab === 'monitoring'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 sm:p-8" style="display: none;">
                    
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-gray-900 mb-2">Hasil Monitoring Guru Pembimbing</h2>
                        <p class="text-gray-600 text-sm">Data ini diisi secara langsung oleh Guru Pembimbing melalui skema "Device Handoff" (meminjam perangkat siswa) beserta bukti autentikasi foto Live & Tanda Tangan.</p>
                    </div>

                    @forelse($student->journals as $journal)
                        <div class="mb-10 last:mb-0 border-b border-gray-200 pb-10">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">{{ $journal->phase }}</span>
                                PKL Tahap {{ $journal->phase }}
                            </h3>
                            
                            @if(!$journal->monitoring_locked_at)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center text-yellow-700">
                                    <svg class="mx-auto h-12 w-12 text-yellow-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <p class="font-bold">Guru belum melakukan monitoring.</p>
                                    <p class="text-sm mt-1">Form monitoring untuk tahap ini belum diisi dan dikunci oleh Guru Pembimbing.</p>
                                </div>
                            @else
                                <div class="flex flex-col lg:flex-row gap-8">
                                    
                                    <!-- Kolom Kiri: Tabel 10 Poin Monitoring -->
                                    <div class="w-full lg:w-2/3">
                                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                                <h4 class="font-bold text-gray-800">Instrumen Observasi</h4>
                                                <span class="text-xs text-gray-500 font-medium bg-gray-200 px-2 py-1 rounded">
                                                    Disubmit: {{ $journal->monitoring_locked_at->format('d M Y, H:i') }}
                                                </span>
                                            </div>
                                            <table class="w-full text-sm text-left text-gray-600">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                                    <tr>
                                                        <th class="px-6 py-3 w-16 text-center">No</th>
                                                        <th class="px-6 py-3">Indikator</th>
                                                        <th class="px-6 py-3 text-center w-24">Hasil</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @php
                                                        // Filter assessments for monitoring
                                                        $monitoringAnswers = $journal->assessments->filter(function($a) {
                                                            return $a->assessment->category === 'monitoring';
                                                        })->sortBy('assessment.order_number')->values();
                                                    @endphp
                                                    
                                                    @foreach($monitoringAnswers as $index => $answer)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-6 py-4 text-center font-medium">{{ $index + 1 }}</td>
                                                            <td class="px-6 py-4">{{ $answer->assessment->name }}</td>
                                                            <td class="px-6 py-4 text-center">
                                                                @if($answer->is_yes)
                                                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded font-bold text-xs uppercase">Ya</span>
                                                                @else
                                                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded font-bold text-xs uppercase">Tidak</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Kolom Kanan: Bukti Autentikasi Guru -->
                                    <div class="w-full lg:w-1/3">
                                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-6 shadow-sm h-full relative overflow-hidden">
                                            <!-- Ribbon -->
                                            <div class="absolute top-0 right-0">
                                                <div class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg shadow uppercase tracking-wider">Device Handoff</div>
                                            </div>
                                            
                                            <h4 class="font-bold text-indigo-900 text-lg mb-4">Bukti Otorisasi Guru</h4>
                                            
                                            <!-- Live Photo -->
                                            <div class="mb-5">
                                                <p class="text-xs font-bold text-indigo-700 uppercase mb-2 flex items-center gap-1">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                    Foto Live Guru
                                                </p>
                                                <div class="bg-white p-2 rounded-lg border border-indigo-200 shadow-sm relative group cursor-pointer"
                                                     @click="modalOpen = true; modalImgLive = '{{ $journal->teacher_live_photo ? asset('storage/' . $journal->teacher_live_photo) : '' }}'; modalImgSignature = '{{ $journal->teacher_signature ? asset('storage/' . $journal->teacher_signature) : '' }}'; modalDate = 'Monitoring Guru Pembimbing (PKL Tahap {{ $journal->phase }})'; modalRejectUrl = '{{ $journal->monitoring_locked_at ? route('admin.students.reject-monitoring', $journal->id) : '' }}'">
                                                    @if($journal->teacher_live_photo)
                                                        <img src="{{ asset('storage/' . $journal->teacher_live_photo) }}" class="w-full h-48 object-cover rounded" alt="Live Photo Guru">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center rounded">
                                                            <svg class="text-white opacity-0 group-hover:opacity-100 h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                                        </div>
                                                    @else
                                                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 rounded">
                                                            <span class="text-xs italic">Tidak ada foto</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Signature -->
                                            <div>
                                                <p class="text-xs font-bold text-indigo-700 uppercase mb-2 flex items-center gap-1">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                    Tanda Tangan
                                                </p>
                                                <div class="bg-white h-24 p-2 rounded-lg border border-indigo-200 shadow-sm flex items-center justify-center cursor-pointer hover:bg-gray-50 transition"
                                                     @click="modalOpen = true; modalImgLive = '{{ $journal->teacher_live_photo ? asset('storage/' . $journal->teacher_live_photo) : '' }}'; modalImgSignature = '{{ $journal->teacher_signature ? asset('storage/' . $journal->teacher_signature) : '' }}'; modalDate = 'Monitoring Guru Pembimbing (PKL Tahap {{ $journal->phase }})'; modalRejectUrl = '{{ $journal->monitoring_locked_at ? route('admin.students.reject-monitoring', $journal->id) : '' }}'">
                                                    @if($journal->teacher_signature)
                                                        <img src="{{ asset('storage/' . $journal->teacher_signature) }}" class="max-h-full max-w-full object-contain" alt="TTD Guru">
                                                    @else
                                                        <span class="text-gray-400 text-xs italic">Tidak ada tanda tangan</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 border border-gray-200 rounded-xl">
                            <p class="text-gray-500">Siswa ini belum memiliki data jurnal.</p>
                        </div>
                    @endforelse
                </div>

            </div>
            
        </div>

        <!-- MODAL / LIGHTBOX -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-80 transition-opacity" aria-hidden="true" @click="modalOpen = false"></div>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    
                    <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white" id="modal-title" x-text="modalDate"></h3>
                        <button @click="modalOpen = false" class="text-indigo-200 hover:text-white transition focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-6 sm:p-8">
                        <div class="flex flex-col md:flex-row gap-8 items-center">
                            
                            <!-- Foto Live -->
                            <div class="w-full md:w-1/2">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3 text-center">Foto Live Instruktur</h4>
                                <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
                                    <template x-if="modalImgLive">
                                        <img :src="modalImgLive" class="w-full h-auto rounded-lg object-contain max-h-[400px]" alt="Full Live Photo">
                                    </template>
                                    <template x-if="!modalImgLive">
                                        <div class="h-64 flex items-center justify-center bg-gray-100 rounded-lg text-gray-400">
                                            <span class="text-sm italic">Foto tidak tersedia</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Tanda Tangan -->
                            <div class="w-full md:w-1/2">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3 text-center">Tanda Tangan / Paraf</h4>
                                <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex items-center justify-center min-h-[200px]">
                                    <template x-if="modalImgSignature">
                                        <img :src="modalImgSignature" class="max-w-full h-auto max-h-[200px] object-contain" alt="Signature">
                                    </template>
                                    <template x-if="!modalImgSignature">
                                        <div class="flex items-center justify-center bg-gray-50 rounded-lg text-gray-400 h-full w-full">
                                            <span class="text-sm italic">Tanda tangan tidak tersedia</span>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Meta Data Info Box -->
                                <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <div>
                                            <h5 class="text-sm font-bold text-blue-800">Bukti Audit Kehadiran Sah</h5>
                                            <p class="text-xs text-blue-600 mt-1">Sistem merekam bahwa instruktur berada di tempat dan membubuhkan persetujuannya secara langsung (real-time) melalui aplikasi.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Rejection Button -->
                    <template x-if="modalRejectUrl">
                        <div class="bg-gray-100 border-t border-gray-200 px-4 py-4 sm:px-8 flex justify-end">
                            <button type="button" @click="rejectModalOpen = true; modalOpen = false" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg flex items-center gap-2 shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                Tolak Bukti Otorisasi Ini
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- REJECTION MODAL -->
        <div x-show="rejectModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="rejectModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-80 transition-opacity" aria-hidden="true" @click="rejectModalOpen = false; modalOpen = true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="rejectModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="modalRejectUrl" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tolak Bukti Otorisasi</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">Anda akan menolak bukti foto/tanda tangan ini. Bukti yang tidak valid akan dihapus dan siswa akan diminta untuk melakukan foto ulang bersama instruktur/guru.</p>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                                        <textarea name="rejection_note" rows="4" class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 border rounded-md p-3" placeholder="Contoh: Foto tidak jelas, hanya terlihat tembok. Harap foto bersama instruktur." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Konfirmasi Tolak</button>
                            <button type="button" @click="rejectModalOpen = false; modalOpen = true" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
