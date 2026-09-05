<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Siswa: {{ $student->name }}</h2>
            <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Daftar Siswa</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Biodata Singkat -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-900">Biodata Siswa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <p><span class="font-semibold w-32 inline-block">Nama Lengkap</span>: {{ $student->name }}</p>
                        <p><span class="font-semibold w-32 inline-block">NISN</span>: {{ $student->nisn }}</p>
                        <p><span class="font-semibold w-32 inline-block">Email Login</span>: {{ $student->user->email ?? '-' }}</p>
                        <p><span class="font-semibold w-32 inline-block">Jurusan</span>: {{ $student->major->name ?? '-' }}</p>
                        <p><span class="font-semibold w-32 inline-block">Kelas</span>: {{ $student->class ?? '-' }}</p>
                        <p><span class="font-semibold w-32 inline-block">TTL</span>: {{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p><span class="font-semibold w-32 inline-block">No HP Siswa</span>: {{ $student->phone ?? '-' }}</p>
                        <p><span class="font-semibold w-32 inline-block">Nama Ortu</span>: {{ $student->parent_name ?? '-' }}</p>
                        <p><span class="font-semibold w-32 inline-block">No HP Ortu</span>: {{ $student->parent_phone ?? '-' }}</p>
                        <p><span class="font-semibold w-32 inline-block">Tgl Mendaftar</span>: {{ $student->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Rekap Jurnal & Timeline -->
            @foreach($student->journals as $journal)
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 text-indigo-700">Jurnal PKL {{ $journal->phase }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-800">Informasi DUDI</h4>
                            <p class="text-sm"><span class="font-medium">Perusahaan:</span> {{ $journal->company_name ?? 'Belum diisi' }}</p>
                            <p class="text-sm"><span class="font-medium">Alamat:</span> {{ $journal->company_address ?? '-' }}</p>
                            <p class="text-sm mt-2"><span class="font-medium">Instruktur:</span> {{ $journal->instructor_name ?? '-' }}</p>
                            <p class="text-sm"><span class="font-medium">Guru Pembimbing:</span> {{ $journal->teacher_name ?? '-' }}</p>
                            
                            <h4 class="font-semibold mt-4 mb-2 text-gray-800">Status Penyelesaian</h4>
                            <p class="text-sm">
                                Status: 
                                <span class="font-bold {{ $journal->status == 'COMPLETED' ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $journal->status }}
                                </span>
                            </p>
                            <p class="text-sm">
                                Diselesaikan Pada: 
                                @if($journal->phase == 1 && $student->jurnal_1_completed_at)
                                    {{ \Carbon\Carbon::parse($student->jurnal_1_completed_at)->format('d M Y H:i') }}
                                @elseif($journal->phase == 2 && $student->jurnal_2_completed_at)
                                    {{ \Carbon\Carbon::parse($student->jurnal_2_completed_at)->format('d M Y H:i') }}
                                @else
                                    <span class="text-gray-400 italic">Belum selesai</span>
                                @endif
                            </p>
                            <p class="text-sm mt-1">Total Kegiatan (Logbook): {{ $journal->dailyActivities->count() }} hari</p>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-800">Bukti Pengesahan Instruktur</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Tanda Tangan</p>
                                    @if($journal->instructor_signature)
                                        <img src="{{ asset('storage/' . $journal->instructor_signature) }}" class="border rounded max-h-32 object-contain bg-gray-50">
                                    @else
                                        <div class="h-20 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400 border border-dashed">Belum ada</div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Foto Live Wajah</p>
                                    @if($journal->instructor_live_photo)
                                        <img src="{{ asset('storage/' . $journal->instructor_live_photo) }}" class="border rounded max-h-32 object-cover">
                                    @else
                                        <div class="h-20 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400 border border-dashed">Belum ada</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
