<x-guest-layout>
    <!-- Header -->
    <header class="mb-8 text-center text-white">
        <h1 class="text-2xl sm:text-3xl font-black tracking-wider uppercase bg-clip-text text-transparent bg-gradient-to-r from-emerald-300 via-teal-200 to-cyan-300 drop-shadow-sm">
            E-JURNAL PKL
        </h1>
        <h2 class="text-base sm:text-lg font-bold tracking-wide text-white/95 uppercase drop-shadow-sm">
            SMKN 1 BERINGIN
        </h2>
    </header>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5" aria-label="Form Login E-Jurnal">
        @csrf

        <!-- Input Email / Akun / NISN -->
        <div>
            <label for="email" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-user-circle text-emerald-400 text-xs"></i>
                <span>Email Akun atau NISN</span>
            </label>
            <input id="email" 
                   type="text" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   placeholder="nama@email.com atau NISN siswa"
                   class="input-field block w-full rounded-xl py-3 px-4 text-sm focus:outline-none placeholder-white/50">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-300 font-semibold" />
        </div>

        <!-- Input Password dengan Fitur Mata -->
        <div>
            <label for="password" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-emerald-400 text-xs"></i>
                <span>Password</span>
            </label>
            <div class="relative">
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="input-field block w-full rounded-xl py-3 pl-4 pr-11 text-sm focus:outline-none placeholder-white/50">
                <!-- Tombol Mata -->
                <button type="button" 
                        onclick="togglePassword('password')" 
                        aria-label="Tampilkan atau sembunyikan password"
                        class="absolute inset-y-0 right-0 px-3.5 flex items-center text-white/60 hover:text-emerald-300 transition-colors">
                    <svg id="icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <!-- Default: Mata Tertutup (Eye Slash) -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-300 font-semibold" />
        </div>

        <!-- Checkbox Ingat Saya & Lupa Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember" 
                       class="rounded border-white/30 bg-white/10 text-emerald-500 focus:ring-emerald-400 focus:ring-offset-0 transition">
                <span class="ml-2 text-xs font-medium text-white/90">Ingat saya</span>
            </label>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-300 hover:text-emerald-100 hover:underline transition-colors">
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Tombol Masuk Gradien Hijau Zamrud Modern -->
        <div class="pt-3 pb-1">
            <button type="submit" 
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition-all duration-300 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-[#063024]">
                <i class="fa-solid fa-right-to-bracket mr-2 text-xs"></i>
                Masuk ke Sistem
            </button>
        </div>

        <!-- Link ke Pendaftaran Siswa -->
        <div class="text-center text-xs font-medium text-white/80 pt-2 border-t border-white/15">
            Belum punya akun siswa? 
            <a href="{{ route('register') }}" class="font-bold text-emerald-300 hover:text-emerald-100 hover:underline transition-colors ml-1">
                Daftar di sini
            </a>
        </div>
    </form>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            }
        }
    </script>
</x-guest-layout>