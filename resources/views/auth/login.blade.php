<x-guest-layout>
    <!-- Header persis seperti gambar -->
    <div class="mb-8 text-center text-white">
        <h1 class="text-2xl font-extrabold tracking-wider drop-shadow-md uppercase bg-clip-text text-transparent bg-gradient-to-r from-emerald-300 to-cyan-300">
            E-JURNAL
        </h1>
        <h2 class="text-lg font-semibold tracking-wide drop-shadow-sm uppercase">SMKN 1 BERINGIN</h2>
        <p class="mt-2 text-sm font-medium text-white/80">Masuk untuk melanjutkan</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Input Email/NISN -->
        <div>
            <label for="email" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="input-field block w-full rounded-xl py-3 px-4 text-sm focus:outline-none focus:ring-0">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400 font-medium" />
        </div>

        <!-- Input Password dengan Fitur Mata -->
        <div>
            <label for="password" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="input-field block w-full rounded-xl py-3 pl-4 pr-11 text-sm focus:outline-none focus:ring-0">
                <!-- Tombol Mata -->
                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 px-3.5 flex items-center text-white/50 hover:text-white transition">
                    <svg id="icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <!-- Default: Mata Tertutup (Eye Slash) -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400 font-medium" />
        </div>

        <!-- Checkbox Ingat Saya & Lupa Password -->
        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-none bg-white/10 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0">
                <label for="remember_me" class="ml-2 text-xs font-medium text-white/90">Ingat saya</label>
            </div>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-emerald-300 hover:text-emerald-100 hover:underline transition">
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Tombol Masuk Hijau Terang -->
        <div class="pt-4 pb-2">
            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] hover:shadow-emerald-500/50">
                Masuk ke Sistem
            </button>
        </div>

        <!-- Link ke Pendaftaran -->
        <div class="text-center text-xs font-medium text-white/80">
            Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-emerald-300 hover:text-emerald-100 hover:underline transition">Daftar di sini</a>
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