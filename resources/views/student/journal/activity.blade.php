<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kehadiran & Kegiatan Harian PKL (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Menu Jurnal</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 bg-amber-50 border border-amber-300 text-amber-900 p-4 rounded-lg text-sm">
                ⚠️ <strong>Perhatian:</strong> Catatan harian wajib diisi lengkap untuk setiap hari selama periode PKL. 
                <br>• <strong>Tombol ACC & Catatan Instruktur</strong> baru akan aktif jika <strong>Paraf Instruktur sudah di-upload</strong> di menu <a href="{{ route('journal.signatures', $journal->id) }}" class="underline font-bold hover:text-amber-700">Tanda Tangan</a>.
                <br>• Catatan harian hanya dapat diedit atau dihapus selama <strong>belum disetujui (di-ACC)</strong> oleh instruktur.
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Kolom Kiri: Form Input Gabungan -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-800">Isi Harian (Absen & Logbook)</h3>
                        
                        @if(session('success'))
                            <div class="bg-green-100 text-green-700 p-2 rounded text-sm mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="bg-red-100 text-red-700 p-2 rounded text-sm mb-4">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('journal.store-activity', $journal->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <x-input-label for="date" value="Tanggal" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" value="{{ date('Y-m-d') }}" required />
                            </div>

                            <div class="mb-3">
                                <x-input-label for="status" value="Status Kehadiran" />
                                <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required onchange="toggleFormInputs()">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                    <option value="Libur">Libur</option>
                                </select>
                            </div>

                            <div id="hadir-inputs">
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <x-input-label for="start_time" value="Jam Mulai" />
                                        <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" />
                                    </div>
                                    <div>
                                        <x-input-label for="end_time" value="Jam Selesai" />
                                        <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="division" value="Divisi / Departemen" />
                                    <x-text-input id="division" class="block mt-1 w-full" type="text" name="division" placeholder="Misal: IT Support" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="activity" value="Uraian Pekerjaan / Aktivitas" />
                                    <textarea id="activity" name="activity" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Deskripsikan pekerjaan hari ini..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="character_values" value="Nilai Karakter Budaya Kerja" />
                                    <x-text-input id="character_values" class="block mt-1 w-full" type="text" name="character_values" placeholder="Misal: Disiplin, Tanggung Jawab" />
                                </div>
                            </div>

                            <x-primary-button class="w-full justify-center">
                                Simpan Catatan Harian
                            </x-primary-button>
                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan: Tabel Riwayat -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-800">Riwayat Kehadiran & Kegiatan Harian</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 min-w-[100px]">Tanggal & Status</th>
                                        <th class="px-4 py-3 min-w-[200px]">Aktivitas / Keterangan</th>
                                        <th class="px-4 py-3 min-w-[120px]">Divisi & Karakter</th>
                                        <th class="px-4 py-3 min-w-[200px]">Validasi Instruktur</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse($activities as $act)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 align-top">
                                            <div class="font-semibold text-gray-900">{{ $act->date->format('d M Y') }}</div>
                                            <div class="mt-1">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded 
                                                    {{ $act->status == 'Hadir' ? 'bg-green-100 text-green-800' : ($act->status == 'Alpa' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ $act->status }}
                                                </span>
                                            </div>
                                            @if($act->status == 'Hadir' && $act->start_time && $act->end_time)
                                                <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($act->end_time)->format('H:i') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            {{ $act->activity }}
                                        </td>
                                        <td class="px-4 py-3 align-top text-xs">
                                            <span class="block text-gray-800 font-medium">{{ $act->division ?? '-' }}</span>
                                            <span class="block mt-1 text-gray-500 italic">{{ $act->character_values ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3 align-top">
                                            @if($act->is_approved)
                                                <span class="inline-flex items-center text-xs text-green-700 bg-green-100 px-2 py-1 rounded">
                                                    ✓ Telah Di-ACC
                                                </span>
                                                @if($act->instructor_notes)
                                                    <div class="mt-2 text-xs bg-yellow-50 p-2 rounded border border-yellow-100">
                                                        <strong>Catatan:</strong> {{ $act->instructor_notes }}
                                                    </div>
                                                @endif
                                            @else
                                                <!-- AKSI SISWA -->
                                                <div class="flex gap-2 mb-2">
                                                    <a href="{{ route('journal.edit-activity', [$journal->id, $act->id]) }}" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-semibold hover:bg-blue-200">Edit</a>
                                                    <form action="{{ route('journal.delete-activity', [$journal->id, $act->id]) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded font-semibold hover:bg-red-200">Hapus</button>
                                                    </form>
                                                </div>

                                                <!-- TOMBOL ACC HARIAN INSTRUKTUR -->
                                                @if($journal->instructor_paraf)
                                                    <form action="{{ route('journal.approve-activity', [$journal->id, $act->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" name="instructor_notes" placeholder="Catatan instruktur (Wajib)..." class="text-xs border-gray-300 rounded w-full mb-1 p-1" required>
                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-2 py-1.5 rounded w-full transition shadow-sm">
                                                            ✓ ACC & Paraf
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada catatan kehadiran & kegiatan harian.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleFormInputs() {
            const status = document.getElementById('status').value;
            const hadirInputs = document.getElementById('hadir-inputs');
            
            if (status === 'Hadir') {
                hadirInputs.style.display = 'block';
                document.getElementById('start_time').required = true;
                document.getElementById('end_time').required = true;
                document.getElementById('activity').required = true;
            } else {
                hadirInputs.style.display = 'none';
                document.getElementById('start_time').required = false;
                document.getElementById('end_time').required = false;
                document.getElementById('activity').required = false;
            }
        }
        toggleFormInputs();
    </script>
</x-app-layout>