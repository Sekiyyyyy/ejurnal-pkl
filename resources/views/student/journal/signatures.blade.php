<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload Tanda Tangan & Paraf (Fase {{ $journal->phase }})
            </h2>
            <a href="{{ route('journal.show', $journal->id) }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ $errors->first() }}</div>
                @endif

                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                    <strong>Informasi:</strong> Format file harus berupa gambar <strong>(JPG/PNG)</strong>. Ukuran maksimal untuk setiap gambar adalah <strong>1 MB</strong>.
                </div>

                <form action="{{ route('journal.update-signatures', $journal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Paraf Instruktur -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Paraf Instruktur (Untuk Logbook)</h4>
                            @if($journal->instructor_paraf)
                                <img src="{{ asset('storage/' . $journal->instructor_paraf) }}" alt="Paraf Instruktur" class="h-20 object-contain mb-3 bg-white border p-1 rounded">
                            @endif
                            <input type="file" name="instructor_paraf" class="text-xs w-full" accept=".jpg,.jpeg,.png">
                        </div>

                        <!-- Tanda Tangan Instruktur -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Instruktur (Penilaian)</h4>
                            @if($journal->instructor_signature)
                                <img src="{{ asset('storage/' . $journal->instructor_signature) }}" alt="Ttd Instruktur" class="h-20 object-contain mb-3 bg-white border p-1 rounded">
                            @endif
                            <input type="file" name="instructor_signature" class="text-xs w-full" accept=".jpg,.jpeg,.png">
                        </div>

                        <!-- Tanda Tangan Siswa -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Siswa</h4>
                            @if($journal->student_signature)
                                <img src="{{ asset('storage/' . $journal->student_signature) }}" alt="Ttd Siswa" class="h-20 object-contain mb-3 bg-white border p-1 rounded">
                            @endif
                            <input type="file" name="student_signature" class="text-xs w-full" accept=".jpg,.jpeg,.png">
                        </div>

                        <!-- Tanda Tangan Orang Tua -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Orang Tua</h4>
                            @if($journal->parent_signature)
                                <img src="{{ asset('storage/' . $journal->parent_signature) }}" alt="Ttd Ortu" class="h-20 object-contain mb-3 bg-white border p-1 rounded">
                            @endif
                            <input type="file" name="parent_signature" class="text-xs w-full" accept=".jpg,.jpeg,.png">
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>Upload / Simpan Gambar</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>