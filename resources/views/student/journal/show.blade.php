<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Menu Jurnal PKL {{ $journal->phase }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                
                <!-- Card Data PKL -->
                <a href="{{ route('journal.data-pkl', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 border-indigo-500">
                    <h3 class="text-lg font-bold text-gray-900">Data PKL</h3>
                    <p class="text-sm text-gray-500 mt-1">Tempat PKL, Instruktur, Pembimbing</p>
                    <div class="mt-3 text-sm {{ $journal->company_id ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $journal->company_id ? '✓ Sudah diisi (Klik untuk Edit)' : '⚠ Belum lengkap' }}
                    </div>
                </a>

                <!-- Card Kehadiran (AKTIF) -->
                <a href="{{ route('journal.attendance', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 border-blue-500">
                    <h3 class="text-lg font-bold text-gray-900">Kehadiran</h3>
                    <p class="text-sm text-gray-500 mt-1">Isi absen harian (Hadir, Izin, Sakit)</p>
                    <div class="mt-3 text-sm text-blue-600">
                        Total Absen: {{ $journal->attendances()->count() }} Hari
                    </div>
                </a>

                <!-- Card Kegiatan Harian (AKTIF) -->
                <a href="{{ route('journal.activity', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 border-emerald-500">
                    <h3 class="text-lg font-bold text-gray-900">Kegiatan Harian</h3>
                    <p class="text-sm text-gray-500 mt-1">Catat aktivitas dan pekerjaan harian</p>
                    <div class="mt-3 text-sm text-emerald-600">
                        Total: {{ $journal->dailyActivities()->count() }} Kegiatan
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>