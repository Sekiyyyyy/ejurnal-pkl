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
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="w-full md:w-2/3">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Progress Kelengkapan Jurnal</h3>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-1">
                    <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-gray-500">{{ $progress }}% Selesai. Pastikan semua data, logbook, dan tanda tangan terisi penuh.</p>
            </div>
            
            <div class="w-full md:w-1/3 text-right">
                @if($progress >= 100)
                    <a href="{{ route('journal.export', $journal->id) }}" class="inline-block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow transition-colors">
                        🖨️ Cetak Jurnal (.docx)
                    </a>
                @else
                    <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 px-6 rounded-lg cursor-not-allowed">
                        🔒 Lengkapi Data Untuk Cetak
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                
                <!-- Card Biodata Diri -->
                <a href="{{ route('student.profile.edit') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isProfileFilled ? 'border-green-500' : 'border-sky-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Biodata Diri</h3>
                    <p class="text-sm text-gray-500 mt-1">Identitas siswa dan orang tua</p>
                    <div class="mt-3 text-sm font-semibold {{ $isProfileFilled ? 'text-green-600' : 'text-sky-600' }}">
                        {{ $isProfileFilled ? '✓ Biodata lengkap' : '⚠ Belum lengkap' }}
                    </div>
                </a>

                <!-- Card Data PKL -->
                <a href="{{ route('journal.data-pkl', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isDataPklFilled ? 'border-green-500' : 'border-indigo-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Data PKL</h3>
                    <p class="text-sm text-gray-500 mt-1">Tempat PKL, Instruktur, Pembimbing</p>
                    <div class="mt-3 text-sm font-semibold {{ $isDataPklFilled ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $isDataPklFilled ? '✓ Sudah lengkap' : '⚠ Belum lengkap' }}
                    </div>
                </a>

                <!-- Card Kehadiran & Kegiatan Harian (Digabung) -->
                <a href="{{ route('journal.activity', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isDailyFilled ? 'border-green-500' : 'border-emerald-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Kehadiran & Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">Absensi harian dan logbook aktivitas</p>
                    <div class="mt-3 text-sm font-semibold {{ $isDailyFilled ? 'text-green-600' : 'text-emerald-600' }}">
                        {{ $isDailyFilled ? '✓ Sesuai periode tanggal PKL' : '⚠ Belum lengkap / kurang' }}
                    </div>
                </a>

                <!-- Card Tanda Tangan -->
                <a href="{{ route('journal.signatures', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isTtdFilled ? 'border-green-500' : 'border-purple-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Tanda Tangan</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload paraf dan tanda tangan</p>
                    <div class="mt-3 text-sm font-semibold {{ $isTtdFilled ? 'text-green-600' : 'text-red-500' }}">
                        {{ $isTtdFilled ? '✓ Tanda tangan lengkap' : '⚠ Masih ada gambar yang kosong' }}
                    </div>
                </a>

                <!-- Card Penilaian Instruktur -->
                <a href="{{ route('journal.instructor-assessment', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isPenilaianFilled ? 'border-green-500' : 'border-rose-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Penilaian Instruktur</h3>
                    <p class="text-sm text-gray-500 mt-1">Lembar observasi dan nilai akhir</p>
                    <div class="mt-3 text-sm font-semibold {{ $isPenilaianFilled ? 'text-green-600' : 'text-rose-600' }}">
                        {{ $isPenilaianFilled ? '✓ Sudah dinilai' : '⚠ Belum diisi Instruktur' }}
                    </div>
                </a>

                <!-- Card Monitoring Guru -->
                <a href="{{ route('journal.monitoring', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 {{ $isMonitoringFilled ? 'border-green-500' : 'border-yellow-500' }}">
                    <h3 class="text-lg font-bold text-gray-900">Monitoring Guru</h3>
                    <p class="text-sm text-gray-500 mt-1">Checklist evaluasi bimbingan</p>
                    <div class="mt-3 text-sm font-semibold {{ $isMonitoringFilled ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $isMonitoringFilled ? '✓ Sudah dimonitoring' : '⚠ Belum diisi Guru' }}
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>