<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Master Penilaian & Monitoring') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- FILTER PILIH JURUSAN -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.assessments.index') }}" class="flex flex-col sm:flex-row gap-4 items-center">
                    <label for="major_id" class="font-bold text-gray-700">Pilih Jurusan untuk Dikelola:</label>
                    <select name="major_id" id="major_id" class="border-gray-300 rounded-md w-full sm:w-1/2" onchange="this.form.submit()">
                        <option value="">-- Silakan Pilih Jurusan --</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ $selectedMajorId == $major->id ? 'selected' : '' }}>
                                {{ $major->code }} - {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- MUNCUL JIKA JURUSAN SUDAH DIPILIH -->
            @if($selectedMajorId)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Form Tambah Kriteria -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Tambah Kriteria Baru</h3>
                    <div class="text-xs text-red-600 mb-4 font-semibold bg-red-50 p-2 rounded">
                        Penting: Nomor Urut menentukan Tag di file Word. (Contoh: Nomor 1 = tag ${m1_y} atau ${nilai_tp_1}).
                    </div>
                    
                    <form action="{{ route('admin.assessments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="major_id" value="{{ $selectedMajorId }}">
                        
                        <div class="mb-4">
                            <x-input-label value="Kategori Penilaian" />
                            <select name="category" class="block mt-1 w-full border-gray-300 rounded-md" required>
                                <option value="monitoring">Monitoring Guru (Ya/Tidak)</option>
                                <option value="observation_point">Poin Observasi Utama (Judul)</option>
                                <option value="observation_sub" class="font-bold text-indigo-600">↳ Sub-Poin Observasi (Indikator)</option>
                                <option value="grade_technical">Nilai Teknis Utama</option>
                                <option value="grade_custom" class="font-bold text-blue-600">Teknis Aktivitas PKL (Custom Nama Pekerjaan)</option>
                                <option value="grade_non_technical">Nilai Non-Teknis Instruktur</option>
                            </select>
                        </div>

                        <!-- KHUSUS SUB POIN -->
                        <div class="mb-4 bg-gray-50 p-3 rounded border">
                            <x-input-label value="Pilih Induk (Wajib diisi jika memilih kategori Sub-Poin di atas)" />
                            <select name="parent_id" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                                <option value="">-- Bukan Sub Poin (Kosongkan) --</option>
                                @foreach($assessments->where('category', 'observation_point') as $parent)
                                    <option value="{{ $parent->id }}">Poin {{ $parent->order_number }}: {{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-input-label value="Nomor Urut (Mulai dari 1)" />
                            <x-text-input class="block mt-1 w-full" type="number" name="order_number" min="1" required />
                        </div>

                        <div class="mb-4">
                            <x-input-label value="Teks Pertanyaan / Kriteria" />
                            <textarea name="name" rows="3" class="block mt-1 w-full border-gray-300 rounded-md" required></textarea>
                        </div>

                        <x-primary-button class="w-full justify-center">Simpan Kriteria</x-primary-button>
                    </form>
                </div>

                <!-- Area Daftar Kriteria (Dipisah secara Spesifik) -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- A. MONITORING GURU -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-indigo-500">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">A. Monitoring Guru (Ya/Tidak)</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border text-center w-16">No</th>
                                        <th class="px-3 py-2 border">Kriteria Monitoring</th>
                                        <th class="px-3 py-2 border text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assessments->where('category', 'monitoring') as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2 border text-center font-bold text-gray-900">{{ $item->order_number }}</td>
                                        <td class="px-3 py-2 border text-gray-700">{{ $item->name }}</td>
                                        <td class="px-3 py-2 border text-center">
                                            <div class="flex justify-center gap-3 text-xs">
                                                <a href="{{ route('admin.assessments.edit', $item->id) }}" class="text-blue-600 font-bold hover:underline">Edit</a>
                                                <form action="{{ route('admin.assessments.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus kriteria ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400 italic">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- B. OBSERVASI INSTRUKTUR (BERSARANG) -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-emerald-500">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">B. Poin Observasi & Indikator</h3>
                        
                        @forelse($assessments->where('category', 'observation_point') as $point)
                            <div class="mb-6 border rounded-md p-4 bg-gray-50 shadow-sm">
                                <!-- Judul Poin Utama (1-4) -->
                                <div class="flex justify-between items-center mb-3 border-b pb-2">
                                    <h4 class="font-bold text-gray-900 text-base">
                                        {{ $point->order_number }}. {{ $point->name }} 
                                        <span class="text-xs text-gray-500 font-normal ml-2">(ID Induk: {{ $point->id }})</span>
                                    </h4>
                                    <div class="flex gap-3 text-xs">
                                        <a href="{{ route('admin.assessments.edit', $point->id) }}" class="text-blue-600 font-bold hover:underline">Edit Judul</a>
                                        <form action="{{ route('admin.assessments.destroy', $point->id) }}" method="POST" onsubmit="return confirm('Hapus Poin Utama beserta sub-poinnya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Tabel Sub-Poin (Indikator) -->
                                <table class="w-full text-sm text-left border bg-white">
                                    <thead class="bg-gray-200 text-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 border text-center w-16">Sub No</th>
                                            <th class="px-3 py-2 border">Indikator (Sub-Poin Ya/Tidak)</th>
                                            <th class="px-3 py-2 border text-center w-24">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assessments->where('category', 'observation_sub')->where('parent_id', $point->id) as $sub)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-3 py-2 border text-center font-semibold text-gray-900">{{ $sub->order_number }}</td>
                                            <td class="px-3 py-2 border text-gray-700">{{ $sub->name }}</td>
                                            <td class="px-3 py-2 border text-center">
                                                <div class="flex justify-center gap-3 text-xs">
                                                    <a href="{{ route('admin.assessments.edit', $sub->id) }}" class="text-blue-600 font-bold hover:underline">Edit</a>
                                                    <form action="{{ route('admin.assessments.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Hapus Indikator ini?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="px-3 py-2 text-center text-gray-400 italic">Belum ada indikator untuk poin ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-center">Belum ada Poin Observasi yang ditambahkan.</p>
                        @endforelse
                    </div>

                    <!-- C. NILAI TEKNIS -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-blue-500">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">C. Nilai Teknis Instruktur</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border text-center w-16">No</th>
                                        <th class="px-3 py-2 border">Kriteria Nilai Teknis</th>
                                        <th class="px-3 py-2 border text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assessments->where('category', 'grade_technical') as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2 border text-center font-bold text-gray-900">{{ $item->order_number }}</td>
                                        <td class="px-3 py-2 border text-gray-700">{{ $item->name }}</td>
                                        <td class="px-3 py-2 border text-center">
                                            <div class="flex justify-center gap-3 text-xs">
                                                <a href="{{ route('admin.assessments.edit', $item->id) }}" class="text-blue-600 font-bold hover:underline">Edit</a>
                                                <form action="{{ route('admin.assessments.destroy', $item->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400 italic">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- D. TEKNIS AKTIVITAS PKL (CUSTOM) -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-amber-500">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">C. Penilaian Teknis Kompetensi Aktivitas PKL (Custom)</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border text-center w-16">No</th>
                                        <th class="px-3 py-2 border">Label Baris (Contoh: Pekerjaan 1, dll)</th>
                                        <th class="px-3 py-2 border text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assessments->where('category', 'grade_custom') as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2 border text-center font-bold text-gray-900">{{ $item->order_number }}</td>
                                        <td class="px-3 py-2 border text-gray-700">{{ $item->name }}</td>
                                        <td class="px-3 py-2 border text-center">
                                            <div class="flex justify-center gap-3 text-xs">
                                                <a href="{{ route('admin.assessments.edit', $item->id) }}" class="text-blue-600 font-bold hover:underline">Edit</a>
                                                <form action="{{ route('admin.assessments.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400 italic">Belum ada data aktivitas custom.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- E. NILAI NON-TEKNIS -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-purple-500">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">D. Nilai Non-Teknis Instruktur</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border text-center w-16">No</th>
                                        <th class="px-3 py-2 border">Kriteria Nilai Non-Teknis</th>
                                        <th class="px-3 py-2 border text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assessments->where('category', 'grade_non_technical') as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2 border text-center font-bold text-gray-900">{{ $item->order_number }}</td>
                                        <td class="px-3 py-2 border text-gray-700">{{ $item->name }}</td>
                                        <td class="px-3 py-2 border text-center">
                                            <div class="flex justify-center gap-3 text-xs">
                                                <a href="{{ route('admin.assessments.edit', $item->id) }}" class="text-blue-600 font-bold hover:underline">Edit</a>
                                                <form action="{{ route('admin.assessments.destroy', $item->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400 italic">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>