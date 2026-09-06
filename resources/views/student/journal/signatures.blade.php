<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload Tanda Tangan (Fase {{ $journal->phase }})
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

                <form id="signature-form" action="{{ route('journal.update-signatures', $journal->id) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Tanda Tangan Siswa -->
                        <div x-data="{ showPad: {{ $journal->student_signature ? 'false' : 'true' }} }" class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Siswa</h4>
                            @if($journal->student_signature)
                            <div x-show="!showPad" class="mb-3">
                                <img src="{{ asset('storage/' . $journal->student_signature) }}" alt="Ttd Siswa" class="h-20 object-contain bg-white border p-1 rounded mb-2">
                                <button type="button" @click="showPad = true" class="text-xs text-blue-600 hover:underline">Ganti Tanda Tangan</button>
                            </div>
                            @endif
                            
                            <div x-show="showPad" style="display: none;" class="border-2 border-dashed border-gray-300 rounded-lg bg-white overflow-hidden touch-none relative mb-2">
                                <canvas id="student-pad" class="w-full h-32 cursor-crosshair"></canvas>
                                <div class="absolute top-2 right-2 z-10">
                                    <button type="button" id="clear-student" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded shadow">Ulang</button>
                                </div>
                            </div>
                            <input type="hidden" name="student_signature" id="student-input">
                        </div>

                        <!-- Tanda Tangan Orang Tua -->
                        <div x-data="{ showPad: {{ $journal->parent_signature ? 'false' : 'true' }} }" class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Orang Tua</h4>
                            @if($journal->parent_signature)
                            <div x-show="!showPad" class="mb-3">
                                <img src="{{ asset('storage/' . $journal->parent_signature) }}" alt="Ttd Ortu" class="h-20 object-contain bg-white border p-1 rounded mb-2">
                                <button type="button" @click="showPad = true" class="text-xs text-blue-600 hover:underline">Ganti Tanda Tangan</button>
                            </div>
                            @endif
                            
                            <div x-show="showPad" style="display: none;" class="border-2 border-dashed border-gray-300 rounded-lg bg-white overflow-hidden touch-none relative mb-2">
                                <canvas id="parent-pad" class="w-full h-32 cursor-crosshair"></canvas>
                                <div class="absolute top-2 right-2 z-10">
                                    <button type="button" id="clear-parent" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded shadow">Ulang</button>
                                </div>
                            </div>
                            <input type="hidden" name="parent_signature" id="parent-input">
                        </div>

                        <!-- Tanda Tangan Kepala Program (Kaprog) -->
                        <div x-data="{ showPad: {{ $journal->kaprog_signature ? 'false' : 'true' }} }" class="border rounded-lg p-4 bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Kaprog Keahlian</h4>
                            @if($journal->kaprog_signature)
                            <div x-show="!showPad" class="mb-3">
                                <img src="{{ asset('storage/' . $journal->kaprog_signature) }}" alt="Ttd Kaprog" class="h-20 object-contain bg-white border p-1 rounded mb-2">
                                <button type="button" @click="showPad = true" class="text-xs text-blue-600 hover:underline">Ganti Tanda Tangan</button>
                            </div>
                            @endif
                            
                            <div x-show="showPad" style="display: none;" class="border-2 border-dashed border-gray-300 rounded-lg bg-white overflow-hidden touch-none relative mb-2">
                                <canvas id="kaprog-pad" class="w-full h-32 cursor-crosshair"></canvas>
                                <div class="absolute top-2 right-2 z-10">
                                    <button type="button" id="clear-kaprog" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded shadow">Ulang</button>
                                </div>
                            </div>
                            <input type="hidden" name="kaprog_signature" id="kaprog-input">
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>Simpan Tanda Tangan</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        function setupSignaturePad(padId, inputId, clearId) {
            const canvas = document.getElementById(padId);
            const input = document.getElementById(inputId);
            const clearBtn = document.getElementById(clearId);
            
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                if (pad) pad.clear(); 
            }
            
            const pad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'black'
            });
            
            const observer = new ResizeObserver(() => resizeCanvas());
            observer.observe(canvas);
            
            clearBtn.addEventListener("click", () => {
                pad.clear();
                input.value = '';
            });
            
            pad.addEventListener("endStroke", () => {
                if (!pad.isEmpty()) {
                    input.value = pad.toDataURL('image/png');
                }
            });

            return pad;
        }

        const studentPad = setupSignaturePad('student-pad', 'student-input', 'clear-student');
        const parentPad = setupSignaturePad('parent-pad', 'parent-input', 'clear-parent');
        const kaprogPad = setupSignaturePad('kaprog-pad', 'kaprog-input', 'clear-kaprog');
    });
    </script>
</x-app-layout>