<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Menu Jurnal PKL {{ $journal->phase }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
    
        <!-- Card Utama: Progress & Tombol -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                
                <div class="w-full md:w-2/3">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Progress Kelengkapan Jurnal</h3>
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-1">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">{{ $progress }}% Selesai. Pastikan semua data, logbook, tanda tangan, dan penilaian terisi.</p>
                </div>
                
                <div class="w-full md:w-1/3">
                    @if($progress < 100)
                        <button disabled class="w-full bg-gray-300 text-gray-600 font-semibold py-3 px-4 rounded-lg cursor-not-allowed text-center">
                            🔒 Lengkapi Data Untuk Cetak
                        </button>
                    @elseif(!$hasActiveTemplate)
                        <button disabled class="w-full bg-gray-300 text-gray-600 font-semibold py-3 px-4 rounded-lg cursor-not-allowed text-center">
                            🔒 Template Belum Di-upload
                        </button>
                    @else
                        <a href="{{ route('journal.export', $journal->id) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow transition text-center">
                            ⬇️ Cetak Jurnal
                        </a>
                    @endif
                </div>

            </div>
        </div>

        <!-- Rejection Alerts -->
        @if($journal->instructor_rejection_note || $journal->teacher_rejection_note || $journal->weeklyApprovals->where('is_rejected', true)->count() > 0)
            <div class="mt-6 space-y-3">
                @if($journal->instructor_rejection_note)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">❌</div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Bukti Penilaian Akhir Ditolak Admin</h3>
                                <div class="mt-1 text-sm text-red-700">
                                    <p><strong>Alasan:</strong> {{ $journal->instructor_rejection_note }}</p>
                                    <p class="mt-1 font-semibold">Silakan buka menu Penilaian Instruktur untuk mengambil ulang foto live bersama instruktur.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($journal->teacher_rejection_note)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">❌</div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Bukti Monitoring Guru Ditolak Admin</h3>
                                <div class="mt-1 text-sm text-red-700">
                                    <p><strong>Alasan:</strong> {{ $journal->teacher_rejection_note }}</p>
                                    <p class="mt-1 font-semibold">Silakan buka menu Monitoring Guru untuk mengambil ulang foto live bersama guru pembimbing.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @foreach($journal->weeklyApprovals->where('is_rejected', true) as $wa)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">❌</div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Bukti ACC Mingguan (Minggu Ke-{{ $wa->week_number }}) Ditolak Admin</h3>
                                <div class="mt-1 text-sm text-red-700">
                                    <p><strong>Alasan:</strong> {{ $wa->rejection_note }}</p>
                                    <p class="mt-1 font-semibold">Status logbook pada minggu tersebut telah dikembalikan menjadi 'Pending'. Silakan klik tombol ACC Mingguan lagi untuk foto ulang.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Alert Dinamis (SUDUT PANDANG SISWA) -->
        @if($progress < 100)
            <div class="mt-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 text-sm rounded-lg shadow-sm flex items-center gap-2">
                <span>ℹ️</span>
                <div>
                    <strong>Status Jurnal:</strong> 
                    @if(!$isProfileFilled || !$isDataPklFilled || !$isDailyFilled || !$isTtdFilled)
                        Silakan lengkapi data mandiri (Biodata, Data PKL, Logbook, dan Tanda Tangan) Anda.
                    @elseif(!$isDailyApproved)
                        Data mandiri Anda sudah lengkap, namun <strong>Logbook/Kehadiran belum di-ACC sepenuhnya oleh Instruktur.</strong>
                    @else
                        Data mandiri dan Logbook Anda sudah lengkap. Progress belum 100% karena <strong>menunggu Instruktur atau Guru Pembimbing menyelesaikan form penilaian/monitoring.</strong>
                    @endif
                </div>
            </div>
        @elseif(!$hasActiveTemplate)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-sm rounded-lg shadow-sm">
                ⚠️ <strong>Informasi:</strong> Jurnal Anda sudah 100%, namun tombol cetak belum bisa diklik karena format dokumen dari sekolah belum tersedia.
            </div>
        @endif

    </div>

    <!-- Bagian Grid Card Menu Di Bawahnya -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                
                <a href="{{ route('student.profile.edit') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isProfileFilled ? 'border-green-500' : 'border-sky-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Biodata Diri</h3>
                    <p class="text-sm text-gray-500 mt-1">Identitas siswa dan orang tua</p>
                    <div class="mt-3 text-sm font-semibold {{ $isProfileFilled ? 'text-green-600' : 'text-sky-600' }}">
                        {{ $isProfileFilled ? '✓ Biodata lengkap' : '⚠ Belum lengkap' }}
                    </div>
                </a>

                <a href="{{ route('journal.data-pkl', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isDataPklFilled ? 'border-green-500' : 'border-indigo-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Data PKL</h3>
                    <p class="text-sm text-gray-500 mt-1">Tempat PKL, Instruktur, Pembimbing</p>
                    <div class="mt-3 text-sm font-semibold {{ $isDataPklFilled ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $isDataPklFilled ? '✓ Sudah lengkap' : '⚠ Belum lengkap' }}
                    </div>
                </a>

                <a href="{{ route('journal.activity', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isDailyApproved ? 'border-green-500' : ($isDailyFilled ? 'border-blue-500' : 'border-emerald-500') }}">
                    <h3 class="text-lg font-bold text-gray-900">Kehadiran & Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">Absensi harian dan logbook aktivitas</p>
                    <div class="mt-3 text-sm font-semibold {{ $isDailyApproved ? 'text-green-600' : ($isDailyFilled ? 'text-blue-600' : 'text-emerald-600') }}">
                        {{ $isDailyApproved ? '✓ Telah di-ACC sepenuhnya' : ($isDailyFilled ? '✓ Sesuai periode (Menunggu ACC)' : '⚠ Belum lengkap / kurang') }}
                    </div>
                </a>

                <a href="{{ route('journal.signatures', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isTtdFilled ? 'border-green-500' : 'border-purple-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Tanda Tangan</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload paraf dan tanda tangan</p>
                    <div class="mt-3 text-sm font-semibold {{ $isTtdFilled ? 'text-green-600' : 'text-red-500' }}">
                        {{ $isTtdFilled ? '✓ Tanda tangan lengkap' : '⚠ Masih ada gambar yang kosong' }}
                    </div>
                </a>

                <!-- Card Penilaian Instruktur -->
                @php
                    if($totalPenilaianCriteria == 0) $borderPenilaian = 'border-rose-500';
                    elseif($isPenilaianFilled) $borderPenilaian = 'border-green-500';
                    elseif($answeredPenilaianCount > 0) $borderPenilaian = 'border-orange-500';
                    else $borderPenilaian = 'border-rose-500';
                @endphp
                <a href="{{ route('journal.instructor-assessment', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $borderPenilaian }}">
                    <h3 class="text-lg font-bold text-gray-900">Penilaian Instruktur</h3>
                    <p class="text-sm text-gray-500 mt-1">Lembar observasi dan nilai akhir</p>
                    <div class="mt-3 text-sm font-semibold {{ $totalPenilaianCriteria == 0 ? 'text-rose-600' : ($isPenilaianFilled ? 'text-green-600' : ($answeredPenilaianCount > 0 ? 'text-orange-600' : 'text-rose-600')) }}">
                        @if($totalPenilaianCriteria == 0)
                            ⚠ Belum ada format penilaian
                        @elseif($isPenilaianFilled)
                            ✓ Sudah dinilai lengkap
                        @elseif($answeredPenilaianCount > 0)
                            ⚠ Sedang dinilai Instruktur ({{ $answeredPenilaianCount }}/{{ $totalPenilaianCriteria }})
                        @else
                            ⚠ Menunggu penilaian Instruktur
                        @endif
                    </div>
                </a>

                <!-- Card Monitoring Guru -->
                @php
                    if($totalMonitoringCriteria == 0) $borderMonitoring = 'border-yellow-500';
                    elseif($isMonitoringFilled) $borderMonitoring = 'border-green-500';
                    elseif($answeredMonitoringCount > 0) $borderMonitoring = 'border-orange-500';
                    else $borderMonitoring = 'border-yellow-500';
                @endphp
                <a href="{{ route('journal.monitoring', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $borderMonitoring }}">
                    <h3 class="text-lg font-bold text-gray-900">Monitoring Guru</h3>
                    <p class="text-sm text-gray-500 mt-1">Checklist evaluasi bimbingan</p>
                    <div class="mt-3 text-sm font-semibold {{ $totalMonitoringCriteria == 0 ? 'text-yellow-600' : ($isMonitoringFilled ? 'text-green-600' : ($answeredMonitoringCount > 0 ? 'text-orange-600' : 'text-yellow-600')) }}">
                        @if($totalMonitoringCriteria == 0)
                            ⚠ Belum ada format monitoring
                        @elseif($isMonitoringFilled)
                            ✓ Sudah dimonitoring lengkap
                        @elseif($answeredMonitoringCount > 0)
                            ⚠ Sedang dimonitoring Guru ({{ $answeredMonitoringCount }}/{{ $totalMonitoringCriteria }})
                        @else
                            ⚠ Menunggu monitoring Guru
                        @endif
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>