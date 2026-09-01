<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kriteria Penilaian') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-0">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-lg font-bold border-b pb-2 mb-4">Edit Data Kriteria</h3>
                
                <form action="{{ route('admin.assessments.update', $assessment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <x-input-label value="Kategori Penilaian" />
                        <select name="category" class="block mt-1 w-full border-gray-300 rounded-md" required>
                            <option value="monitoring" {{ $assessment->category == 'monitoring' ? 'selected' : '' }}>Monitoring Guru (Ya/Tidak)</option>
                            <option value="observation_point" {{ $assessment->category == 'observation_point' ? 'selected' : '' }}>Poin Observasi Utama (Judul)</option>
                            <option value="observation_sub" {{ $assessment->category == 'observation_sub' ? 'selected' : '' }} class="font-bold text-indigo-600">↳ Sub-Poin Observasi (Indikator)</option>
                            <option value="grade_technical" {{ $assessment->category == 'grade_technical' ? 'selected' : '' }}>Nilai Teknis Instruktur</option>
                            <option value="grade_non_technical" {{ $assessment->category == 'grade_non_technical' ? 'selected' : '' }}>Nilai Non-Teknis Instruktur</option>
                        </select>
                    </div>

                    <!-- Kolom Edit Induk -->
                    <div class="mb-4 bg-gray-50 p-3 rounded border">
                        <x-input-label value="ID Induk Poin (Khusus Sub-Poin)" />
                        <x-text-input class="block mt-1 w-full bg-gray-100" type="number" name="parent_id" value="{{ $assessment->parent_id }}" placeholder="Kosongkan jika bukan sub-poin" />
                        <span class="text-xs text-gray-500 mt-1 block">Isi dengan ID Kriteria dari Poin Utama.</span>
                    </div>

                    <div class="mb-4">
                        <x-input-label value="Nomor Urut (Mulai dari 1)" />
                        <x-text-input class="block mt-1 w-full" type="number" name="order_number" value="{{ $assessment->order_number }}" min="1" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label value="Teks Pertanyaan / Kriteria" />
                        <textarea name="name" rows="3" class="block mt-1 w-full border-gray-300 rounded-md" required>{{ $assessment->name }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.assessments.index', ['major_id' => $assessment->major_id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">Batal</a>
                        <x-primary-button>Simpan Perubahan</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>