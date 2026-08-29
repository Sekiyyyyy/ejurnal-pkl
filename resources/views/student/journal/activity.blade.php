<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kegiatan Harian PKL (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Menu Jurnal</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Kolom Kiri: Form Kegiatan -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Isi Kegiatan</h3>
                    
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

                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <x-input-label for="start_time" value="Jam Mulai" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" required />
                            </div>
                            <div>
                                <x-input-label for="end_time" value="Jam Selesai" />
                                <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" required />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="division" value="Divisi / Departemen (Opsional)" />
                            <x-text-input id="division" class="block mt-1 w-full" type="text" name="division" placeholder="Misal: IT Support" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="activity" value="Uraian Pekerjaan / Aktivitas" />
                            <textarea id="activity" name="activity" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="character_values" value="Nilai Karakter Budaya Kerja" />
                            <x-text-input id="character_values" class="block mt-1 w-full" type="text" name="character_values" placeholder="Misal: Disiplin, Tanggung Jawab" />
                        </div>

                        <x-primary-button class="w-full justify-center">
                            Simpan Kegiatan
                        </x-primary-button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Tabel Riwayat Kegiatan -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Riwayat Kegiatan Harian</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3 min-w-[100px]">Waktu</th>
                                    <th class="px-4 py-3 min-w-[200px]">Aktivitas</th>
                                    <th class="px-4 py-3 min-w-[120px]">Divisi & Karakter</th>
                                    <th class="px-4 py-3 min-w-[200px]">Validasi Instruktur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($activities as $act)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 align-top">
                                        <div class="font-semibold text-gray-900">{{ $act->date->format('d M Y') }}</div>
                                        <div class="text-xs">{{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($act->end_time)->format('H:i') }}</div>
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
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                Telah Di-ACC
                                            </span>
                                            @if($act->instructor_notes)
                                                <div class="mt-2 text-xs bg-yellow-50 p-2 rounded border border-yellow-100">
                                                    <strong>Catatan:</strong> {{ $act->instructor_notes }}
                                                </div>
                                            @endif
                                        @else
                                            <!-- TOMBOL ACC HARIAN INSTRUKTUR -->
                                            <div class="bg-gray-100 p-2 rounded border border-gray-200">
                                                <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Aksi Instruktur</p>
                                                
                                                @if($journal->instructor_paraf)
                                                    <form action="{{ route('journal.approve-activity', [$journal->id, $act->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" name="instructor_notes" placeholder="Catatan... (Opsional)" class="text-xs border-gray-300 rounded w-full mb-1 p-1">
                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-2 py-1.5 rounded w-full transition shadow-sm">
                                                            ✓ ACC & Bubuhkan Paraf
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="text-xs text-red-600 font-semibold p-1 bg-red-50 rounded">
                                                        ⚠ Upload Paraf Instruktur terlebih dahulu di menu "Tanda Tangan".
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada aktivitas yang dicatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>