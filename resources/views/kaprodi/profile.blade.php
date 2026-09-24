<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Kaprodi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <x-alert type="success" :message="session('success')" class="mb-6" />
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('kaprodi.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap (beserta gelar)')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $kaprodi->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nip" :value="__('NIP / NIK')" />
                            <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip', $kaprodi->nip)" />
                            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-bold text-gray-700 mb-2">Tanda Tangan Kepala Program Keahlian</h4>
                        <p class="text-sm text-gray-500 mb-4">Tanda tangan ini akan otomatis dilampirkan ke jurnal siswa saat Anda menekan tombol "ACC Jurnal". Pastikan tanda tangan jelas.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($kaprodi->signature)
                                <div class="border rounded p-4 bg-gray-50 text-center flex flex-col justify-center items-center h-56">
                                    <p class="text-xs text-gray-500 mb-2">Tanda Tangan Saat Ini:</p>
                                    <img src="{{ asset('storage/' . $kaprodi->signature) }}" alt="Tanda Tangan" class="max-h-32 object-contain bg-white border p-2 rounded">
                                </div>
                            @endif
                            
                            <div class="border rounded p-4 bg-white flex flex-col gap-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-700 mb-2">{{ $kaprodi->signature ? 'Ganti dengan Upload File Baru' : 'Upload File Tanda Tangan' }}</p>
                                    <input type="file" name="signature_file" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md p-2">
                                    <p class="text-xs text-gray-500 mt-1">Gunakan gambar dengan background transparan (PNG) untuk hasil terbaik.</p>
                                    <x-input-error :messages="$errors->get('signature_file')" class="mt-2" />
                                </div>

                                <div class="border-t pt-4">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">Atau Gambar Tanda Tangan Anda</p>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 overflow-hidden touch-none relative h-32">
                                        <canvas id="signature-pad" class="w-full h-full cursor-crosshair"></canvas>
                                        <div class="absolute top-2 right-2 z-10">
                                            <button type="button" id="clear-signature" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded shadow">Ulang</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="signature_base64" id="signature-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 pt-4 border-t">
                        <x-primary-button>
                            {{ __('Simpan Profil & Tanda Tangan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('signature-pad');
        const input = document.getElementById('signature-input');
        const clearBtn = document.getElementById('clear-signature');
        
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
        
        // Prevent scrolling while drawing on touch devices
        canvas.addEventListener('touchstart', function(e) { e.preventDefault(); }, { passive: false });
        canvas.addEventListener('touchmove', function(e) { e.preventDefault(); }, { passive: false });
    });
    </script>
</x-app-layout>
