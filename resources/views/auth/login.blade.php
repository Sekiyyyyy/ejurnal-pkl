<x-guest-layout>
    <!-- Header persis seperti gambar -->
    <div class="mb-6 text-center text-white">
        <h1 class="text-xl font-extrabold tracking-wide drop-shadow-sm uppercase">E-JURNAL SMKN 1 BERINGIN</h1>
        <p class="mt-1 text-sm font-medium text-white/90">Masuk untuk melanjutkan</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Input Email/NISN -->
        <div>
            <label for="email" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-300" />
        </div>

        <!-- Input Password dengan Fitur Mata -->
        <div>
            <label for="password" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="block w-full rounded-lg border-none bg-[#f4f7fb] pl-4 pr-10 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
                <!-- Tombol Mata -->
                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg id="icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <!-- Default: Mata Tertutup (Eye Slash) -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-300" />
        </div>

        <!-- Checkbox Ingat Saya & Lupa Password -->
        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-none bg-[#f4f7fb] text-[#21a650] focus:ring-[#21a650]">
                <label for="remember_me" class="ml-2 text-xs font-medium text-white drop-shadow-sm">Ingat saya</label>
            </div>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-white drop-shadow-sm hover:underline">
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Tombol Masuk Hijau Terang -->
        <div class="pt-3">
            <button type="submit" class="w-full rounded-lg bg-[#21a650] py-3 text-sm font-bold text-white transition hover:bg-[#1d8f45]">
                Masuk
            </button>
        </div>

        <!-- Link ke Pendaftaran -->
        <div class="mt-4 text-center text-xs font-medium text-white drop-shadow-sm">
            Belum punya akun? <a href="{{ route('register') }}" class="font-bold hover:underline">Daftar di sini</a>
        </div>
    </form>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            
            // Ubah tipe dari password ke teks atau sebaliknya
            if (input.type === 'password') {
                input.type = 'text';
                // Ganti icon ke Mata Terbuka
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            } else {
                input.type = 'password';
                // Ganti icon ke Mata Tertutup (Tercoret)
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            }
        }
    </script>
</x-guest-layout>