@props(['idPrefix' => 'auth', 'submitButtonId' => 'btn-submit', 'formId' => 'form-auth'])

<div class="mb-6 bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Live Authenticator</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Digital Signature -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanda Tangan / Paraf</label>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 overflow-hidden touch-none relative">
                <canvas id="{{ $idPrefix }}-signature-pad" class="w-full h-48 cursor-crosshair"></canvas>
                <div class="absolute top-2 right-2 flex space-x-2 z-10">
                    <button type="button" id="{{ $idPrefix }}-clear-signature" class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded shadow">Ulang</button>
                </div>
            </div>
            <input type="hidden" name="signature_base64" id="{{ $idPrefix }}-signature-input" required>
        </div>

        <!-- Live Camera -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Wajah Live</label>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 overflow-hidden relative w-full flex flex-col items-center justify-center" style="height: 192px;">
                <video id="{{ $idPrefix }}-camera-video" class="w-full h-full object-cover" autoplay playsinline muted style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></video>
                <canvas id="{{ $idPrefix }}-camera-canvas" class="hidden w-full h-full object-cover" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;"></canvas>
                <div class="flex justify-center" style="position: absolute; bottom: 16px; left: 0; right: 0; z-index: 10;">
                    <button type="button" id="{{ $idPrefix }}-take-photo" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full shadow-lg flex items-center space-x-2" style="position: relative; z-index: 10;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        <span>Ambil Foto</span>
                    </button>
                    <button type="button" id="{{ $idPrefix }}-retake-photo" class="hidden bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-full shadow-lg flex items-center space-x-2" style="position: relative; z-index: 10;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        <span>Ulangi</span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="live_photo_base64" id="{{ $idPrefix }}-photo-input" required>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const idPrefix = '{{ $idPrefix }}';
    const formId = '{{ $formId }}';
    
    // --- Signature Pad Setup ---
    const canvas = document.getElementById(`${idPrefix}-signature-pad`);
    const signatureInput = document.getElementById(`${idPrefix}-signature-input`);
    const clearButton = document.getElementById(`${idPrefix}-clear-signature`);
    
    // Fix DPI scaling for sharp lines
    function resizeCanvas() {
        const ratio =  Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        if (signaturePad) signaturePad.clear(); 
    }
    
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)', // Transparent
        penColor: 'black'
    });
    
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();
    
    clearButton.addEventListener("click", () => {
        signaturePad.clear();
        signatureInput.value = '';
    });
    
    // Update input on end of stroke
    signaturePad.addEventListener("endStroke", () => {
        if (!signaturePad.isEmpty()) {
            signatureInput.value = signaturePad.toDataURL('image/png');
        }
    });

    // --- Camera Setup ---
    const video = document.getElementById(`${idPrefix}-camera-video`);
    const photoCanvas = document.getElementById(`${idPrefix}-camera-canvas`);
    const takeButton = document.getElementById(`${idPrefix}-take-photo`);
    const retakeButton = document.getElementById(`${idPrefix}-retake-photo`);
    const photoInput = document.getElementById(`${idPrefix}-photo-input`);
    let stream = null;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user" }, // Use front camera
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            console.error("Camera access denied:", err);
            alert("Harap berikan izin akses kamera untuk menggunakan fitur ini.");
        }
    }

    startCamera();

    takeButton.addEventListener('click', () => {
        // Capture exactly what the camera outputs, maintaining its native aspect ratio
        const rawWidth = video.videoWidth;
        const rawHeight = video.videoHeight;
        
        // Scale down to max 800px on the longest edge to save storage space
        const maxSize = 800;
        let targetWidth = rawWidth;
        let targetHeight = rawHeight;
        
        if (rawWidth > maxSize || rawHeight > maxSize) {
            if (rawWidth > rawHeight) {
                targetWidth = maxSize;
                targetHeight = Math.round((rawHeight / rawWidth) * maxSize);
            } else {
                targetHeight = maxSize;
                targetWidth = Math.round((rawWidth / rawHeight) * maxSize);
            }
        }
        
        photoCanvas.width = targetWidth;
        photoCanvas.height = targetHeight;
        
        const ctx = photoCanvas.getContext('2d');
        
        // Draw the full video frame scaled to the target size, no distortion or forced cropping
        ctx.drawImage(video, 0, 0, targetWidth, targetHeight);
        
        // Compress to 60% quality WebP or JPEG
        const compressedDataUrl = photoCanvas.toDataURL('image/jpeg', 0.6);
        photoInput.value = compressedDataUrl;
        
        video.classList.add('hidden');
        photoCanvas.classList.remove('hidden');
        takeButton.classList.add('hidden');
        retakeButton.classList.remove('hidden');
    });

    retakeButton.addEventListener('click', () => {
        photoInput.value = '';
        video.classList.remove('hidden');
        photoCanvas.classList.add('hidden');
        takeButton.classList.remove('hidden');
        retakeButton.classList.add('hidden');
    });
    
    // --- Form Submission Validation ---
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function(e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert("Harap isi tanda tangan/paraf!");
                return false;
            }
            if (!photoInput.value) {
                e.preventDefault();
                alert("Harap ambil foto live!");
                return false;
            }
            // Ensure values are set
            signatureInput.value = signaturePad.toDataURL('image/png');
        });
    }
});
</script>
