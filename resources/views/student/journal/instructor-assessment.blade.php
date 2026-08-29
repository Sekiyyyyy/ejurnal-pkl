<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Penilaian Instruktur (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
                    <strong>Pemberitahuan:</strong> Halaman ini diisi secara langsung oleh <strong>Instruktur / Penanggung Jawab DUDI</strong>.
                </div>

                <form action="{{ route('journal.update-instructor-assessment', $journal->id) }}" method="POST">
                    @csrf
                    
                    <!-- BAGIAN 1: OBSERVASI -->
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">A. Lembar Observasi</h3>
                    <div class="space-y-6 mb-8">
                        @foreach($obsPoints as $index => $point)
                            <div class="bg-gray-50 border rounded p-4">
                                <h4 class="font-bold text-md text-gray-800 mb-3">{{ $index + 1 }}. {{ $point->name }}</h4>
                                
                                <!-- Tabel Sub Poin (Checklist Ya/Tidak) -->
                                <table class="w-full text-sm mb-4 bg-white border">
                                    <thead class="bg-gray-100 border-b">
                                        <tr>
                                            <th class="p-2 text-left">Indikator</th>
                                            <th class="p-2 w-20 text-center">Ya</th>
                                            <th class="p-2 w-20 text-center">Tidak</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($point->children as $child)
                                        <tr>
                                            <td class="p-2 text-gray-700">
                                                @if($point->order_number == 3)
                                                    <!-- Input teks dinamis untuk Poin 3 -->
                                                    <input type="text" name="obs_custom_name[{{ $child->id }}]" 
                                                            value="{{ optional($existing[$child->id] ?? null)->description }}" 
                                                            placeholder="Isi nama kompetensi teknis..." 
                                                            class="w-full text-sm border-gray-300 rounded">
                                                @else
                                                    {{ $child->name }}
                                                @endif
                                            </td>
                                            <td class="p-2 text-center">
                                                <input type="radio" name="obs_yes[{{ $child->id }}]" value="1" 
                                                    {{ optional($existing[$child->id] ?? null)->is_yes == '1' ? 'checked' : '' }} 
                                                    class="text-indigo-600" {{ $point->order_number == 3 ? '' : 'required' }}>
                                            </td>
                                            <td class="p-2 text-center">
                                                <input type="radio" name="obs_yes[{{ $child->id }}]" value="0" 
                                                    {{ optional($existing[$child->id] ?? null)->is_yes == '0' ? 'checked' : '' }} 
                                                    class="text-indigo-600" {{ $point->order_number == 3 ? '' : 'required' }}>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Deskripsi Kesimpulan Observasi -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi & Catatan (Terkait {{ $point->name }})</label>
                                    <textarea name="obs_desc[{{ $point->id }}]" rows="2" class="w-full border-gray-300 rounded text-sm" placeholder="Peserta didik sudah... namun perlu ditingkatkan dalam hal..." required>{{ optional($existing[$point->id] ?? null)->description }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- BAGIAN 2: PENILAIAN TEKNIS -->
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">B. Penilaian Teknis Tujuan Pembelajaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        @foreach($gradeTechs as $index => $tech)
                            <div class="bg-gray-50 border rounded p-4 flex flex-col justify-between">
                                <label class="text-sm font-medium text-gray-800 block mb-2">{{ $index + 1 }}. {{ $tech->name }}</label>
                                <div class="flex items-center">
                                    <span class="text-sm mr-2 text-gray-600">Nilai (0-100):</span>
                                    <input type="number" name="grade[{{ $tech->id }}]" min="0" max="100" class="border-gray-300 rounded w-24 text-sm" value="{{ optional($existing[$tech->id] ?? null)->score }}">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- BAGIAN 3: PENILAIAN TEKNIS CUSTOM -->
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">C. Penilaian Teknis Kompetensi Aktivitas PKL</h3>
                    <p class="text-xs text-gray-500 mb-4">Isikan nama pekerjaan teknis nyata yang dilakukan siswa, lalu berikan nilai (0-100). Kosongkan jika tidak ada.</p>
                    <div class="space-y-3 mb-8">
                        @foreach($gradeCustoms as $index => $custom)
                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 w-6">{{ $index + 1 }}.</span>
                                <input type="text" name="custom_name[{{ $custom->id }}]" class="border-gray-300 rounded w-full sm:w-2/3 text-sm" placeholder="Nama Kompetensi (Misal: Setting Mikrotik)" value="{{ optional($existing[$custom->id] ?? null)->description }}">
                                <input type="number" name="custom_grade[{{ $custom->id }}]" min="0" max="100" class="border-gray-300 rounded w-full sm:w-1/3 text-sm" placeholder="Nilai (0-100)" value="{{ optional($existing[$custom->id] ?? null)->score }}">
                            </div>
                        @endforeach
                    </div>

                    <!-- BAGIAN 4: PENILAIAN NON TEKNIS (BUDAYA KERJA) -->
                    <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-4">D. Penilaian Budaya Kerja (Non-Teknis)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        @foreach($gradeNonTechs as $index => $nonTech)
                            <div class="flex items-center justify-between border-b pb-2">
                                <label class="text-sm font-medium text-gray-800">{{ $index + 1 }}. {{ $nonTech->name }}</label>
                                <input type="number" name="grade[{{ $nonTech->id }}]" min="0" max="100" class="border-gray-300 rounded w-20 text-sm ml-2" placeholder="Nilai" value="{{ optional($existing[$nonTech->id] ?? null)->score }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 flex justify-end">
                        <x-primary-button class="text-lg px-6 py-3 bg-rose-600 hover:bg-rose-700">
                            Simpan Seluruh Penilaian
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>