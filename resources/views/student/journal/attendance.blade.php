<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kehadiran PKL (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Menu Jurnal</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Kolom Kiri: Form Absensi -->
            <div class="md:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Isi Kehadiran</h3>
                    
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

                    <form action="{{ route('journal.store-attendance', $journal->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <x-input-label for="date" value="Tanggal" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" value="{{ date('Y-m-d') }}" required />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="status" value="Status Kehadiran" />
                            <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required onchange="toggleTimeInputs()">
                                <option value="Hadir">Hadir</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alpa">Alpa</option>
                                <option value="Libur">Libur</option>
                            </select>
                        </div>

                        <div id="time-inputs" class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <x-input-label for="entry_time" value="Jam Masuk" />
                                <x-text-input id="entry_time" class="block mt-1 w-full" type="time" name="entry_time" />
                            </div>
                            <div>
                                <x-input-label for="exit_time" value="Jam Pulang" />
                                <x-text-input id="exit_time" class="block mt-1 w-full" type="time" name="exit_time" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="notes" value="Keterangan (Opsional)" />
                            <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes" placeholder="Misal: Surat dokter ada di lampiran" />
                        </div>

                        <x-primary-button class="w-full justify-center">
                            Simpan Kehadiran
                        </x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Tabel Riwayat Absensi -->
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Riwayat Kehadiran</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Masuk - Pulang</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $att->date->format('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded 
                                            {{ $att->status == 'Hadir' ? 'bg-green-100 text-green-800' : ($att->status == 'Alpa' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $att->status == 'Hadir' ? ($att->entry_time ? \Carbon\Carbon::parse($att->entry_time)->format('H:i') : '-') . ' - ' . ($att->exit_time ? \Carbon\Carbon::parse($att->exit_time)->format('H:i') : '-') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $att->notes ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada data kehadiran yang dicatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleTimeInputs() {
            const status = document.getElementById('status').value;
            const timeInputs = document.getElementById('time-inputs');
            
            // Logika Jam Masuk/Pulang
            if (status === 'Hadir') {
                timeInputs.style.display = 'grid';
            } else {
                timeInputs.style.display = 'none';
                document.getElementById('entry_time').value = '';
                document.getElementById('exit_time').value = '';
            }
        }
        
        // Initialize on load
        toggleTimeInputs();
    </script>
</x-app-layout>