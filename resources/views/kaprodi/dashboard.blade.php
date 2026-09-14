<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-sm">
                <i class="fa-solid fa-layer-group text-xl"></i>
            </div>
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight">
                    Dashboard Validasi
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Kepala Program Keahlian</p>
            </div>
        </div>
    </x-slot>

    <!-- Background -->
    <div class="fixed inset-0 bg-slate-50 -z-10 pointer-events-none"></div>

    <div class="py-10 min-h-screen relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-start gap-4">
                    <div class="bg-emerald-100 p-2 rounded-full text-emerald-600">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h3 class="text-emerald-800 font-bold">Berhasil</h3>
                        <p class="text-emerald-600 text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Clean & Cohesive Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300 group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Total Jurnal</p>
                            <h4 class="text-5xl font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $totalJournals }}</h4>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-2 text-sm font-medium text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span> Seluruh Jurnal Aktif
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300 group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Menunggu ACC</p>
                            <h4 class="text-5xl font-black text-slate-800 group-hover:text-amber-500 transition-colors">{{ $waitingCount }}</h4>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-2 text-sm font-medium text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Perlu Validasi Anda
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300 group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-2">Selesai</p>
                            <h4 class="text-5xl font-black text-slate-800 group-hover:text-emerald-500 transition-colors">{{ $approvedCount }}</h4>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-2 text-sm font-medium text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Telah Di-ACC
                    </div>
                </div>
            </div>

            <!-- Main Data Section -->
            <div class="bg-white/80 backdrop-blur-2xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/60 overflow-hidden">
                
                <!-- Header & Search -->
                <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Daftar Pengajuan Jurnal</h3>
                        <p class="text-sm text-slate-500 mt-1">Kelola dan pantau seluruh aktivitas jurnal siswa secara real-time.</p>
                    </div>
                    
                    <form method="GET" action="{{ route('kaprodi.dashboard') }}" class="w-full md:w-auto relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Siswa atau NISN..." 
                            class="pl-11 pr-12 py-3 w-full md:w-80 bg-slate-50/50 border border-slate-200 focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl text-sm transition-all duration-300 font-medium text-slate-700 placeholder-slate-400">
                        
                        @if(request('search'))
                            <a href="{{ route('kaprodi.dashboard') }}" class="absolute inset-y-0 right-2 flex items-center p-2 text-slate-400 hover:text-red-500 transition-colors">
                                <i class="fa-solid fa-circle-xmark text-lg"></i>
                            </a>
                        @endif
                    </form>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider w-16 text-center">No</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Siswa</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Fase</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Status Validasi</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($journals as $index => $journal)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-6 text-center text-sm font-semibold text-slate-400">
                                    {{ $journals->firstItem() + $index }}
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-600 font-bold text-lg shadow-inner group-hover:scale-105 transition-transform duration-300">
                                                {{ substr($journal->student->name, 0, 1) }}
                                            </div>
                                            @if($journal->kaprodi_status === 'WAITING_KAPROG')
                                                <div class="absolute -top-1 -right-1 h-4 w-4 bg-amber-500 border-2 border-white rounded-full"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors">{{ $journal->student->name }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">{{ $journal->student->nisn }}</span>
                                                <span class="text-xs text-slate-400">&bull;</span>
                                                <span class="text-xs text-slate-500">{{ $journal->student->class }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm shadow-sm">
                                        {{ $journal->phase }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($journal->kaprodi_status === 'WAITING_KAPROG')
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 text-xs font-bold shadow-sm">
                                            <span class="relative flex h-2 w-2">
                                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                              <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                            </span>
                                            Menunggu Validasi
                                        </span>
                                    @elseif($journal->kaprodi_status === 'APPROVED')
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold shadow-sm">
                                            <i class="fa-solid fa-check text-emerald-500"></i>
                                            Telah Di-ACC
                                        </span>
                                    @elseif($journal->kaprodi_status === 'REJECTED')
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-50 border border-red-100 text-red-700 text-xs font-bold shadow-sm">
                                            <i class="fa-solid fa-xmark text-red-500"></i>
                                            Ditolak Kaprodi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs font-semibold">
                                            <i class="fa-solid fa-spinner text-slate-400"></i>
                                            Proses Siswa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <a href="{{ route('kaprodi.journal.show', $journal->id) }}" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all focus:ring-4 focus:ring-indigo-500/10 group-hover:-translate-y-0.5">
                                        Periksa <i class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-500">
                                        <div class="w-24 h-24 mb-6 rounded-3xl bg-slate-50 border border-slate-100 flex items-center justify-center shadow-inner">
                                            <i class="fa-regular fa-folder-open text-4xl text-slate-300"></i>
                                        </div>
                                        <p class="text-xl font-bold text-slate-700 mb-1">Belum ada jurnal</p>
                                        <p class="text-sm text-slate-400 max-w-sm text-center">Saat ini belum ada data jurnal siswa yang sesuai atau sedang dikerjakan pada jurusan Anda.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($journals->hasPages())
                    <div class="px-8 py-6 border-t border-slate-100 bg-white rounded-b-3xl">
                        {{ $journals->links('kaprodi.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
