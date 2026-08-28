<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Jurnal: {{ $journal->student->name }} (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('teacher.dashboard') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Info Siswa & PKL -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Informasi Pelaksanaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500">NISN:</span>
                        <span class="font-medium text-gray-900">{{ $journal->student->nisn }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Tempat PKL:</span>
                        <span class="font-medium text-gray-900">{{ $journal->company->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Instruktur / Pembimbing DUDI:</span>
                        <span class="font-medium text-gray-900">{{ $journal->instructor_name ?? '-' }} ({{ $journal->instructor_phone ?? '-' }})</span>
                    </div>
                </div>
            </div>

            <!-- Menu Aksi Penilaian Guru -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                
                <!-- Lembar Monitoring -->
                <a href="{{ route('teacher.monitoring', $journal->id) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition border-l-4 border-indigo-500">
                    <h4 class="font-bold text-gray-900">Lembar Monitoring</h4>
                    <p class="text-xs text-gray-500 mt-1">Isi checklist evaluasi bimbingan</p>
                    <div class="mt-3 text-xs text-indigo-600 font-semibold">
                        {{ $journal->assessments()->whereHas('assessment', fn($q) => $q->where('category', 'monitoring'))->count() }} / 10 Terisi
                    </div>
                </a>

                <!-- Kehadiran Summary -->
                <div class="block bg-white shadow-sm rounded-lg p-6 border-l-4 border-blue-500">
                    <h4 class="font-bold text-gray-900">Rekap Kehadiran</h4>
                    <p class="text-xs text-gray-500 mt-1">Total catatan absensi siswa</p>
                    <div class="mt-3 text-xs text-blue-600 font-semibold">
                        {{ $journal->attendances->count() }} Hari Tercatat
                    </div>
                </div>

                <!-- Kegiatan Harian Summary -->
                <div class="block bg-white shadow-sm rounded-lg p-6 border-l-4 border-emerald-500">
                    <h4 class="font-bold text-gray-900">Logbook Kegiatan</h4>
                    <p class="text-xs text-gray-500 mt-1">Total uraian pekerjaan harian</p>
                    <div class="mt-3 text-xs text-emerald-600 font-semibold">
                        {{ $journal->dailyActivities->count() }} Kegiatan Masuk
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>