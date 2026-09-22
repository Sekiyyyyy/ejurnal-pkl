<x-guest-layout>
    <!-- Header -->
    <header class="mb-6 text-center text-white">
        <h1 class="text-2xl font-black tracking-wider uppercase bg-clip-text text-transparent bg-gradient-to-r from-emerald-300 via-teal-200 to-cyan-300 drop-shadow-sm">
            DAFTAR E-JURNAL
        </h1>
        <h2 class="text-sm font-bold tracking-wide text-white/95 uppercase drop-shadow-sm">
            SMKN 1 BERINGIN
        </h2>
    </header>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" aria-label="Form Pendaftaran Akun Siswa">
        @csrf

        <!-- Input Nama -->
        <div>
            <label for="name" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-user text-emerald-400 text-xs"></i>
                <span>Nama Lengkap</span>
            </label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Nama sesuai data sekolah"
                   class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none placeholder-white/50">
            @error('name') <span class="text-rose-300 font-semibold text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Input NISN -->
        <div>
            <label for="nisn" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-id-card text-emerald-400 text-xs"></i>
                <span>NISN (Nomor Induk Siswa Nasional)</span>
            </label>
            <input id="nisn" 
                   type="text" 
                   name="nisn" 
                   value="{{ old('nisn') }}" 
                   required
                   maxlength="10" 
                   inputmode="numeric" 
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   placeholder="10 Digit NISN"
                   class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none placeholder-white/50">
            @error('nisn') <span class="text-rose-300 font-semibold text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>
        
        <!-- Input Jurusan -->
        <div>
            <label for="major_id" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap text-emerald-400 text-xs"></i>
                <span>Jurusan / Program Keahlian</span>
            </label>
            <select id="major_id" 
                    name="major_id" 
                    required
                    class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none [&>option]:text-gray-900 cursor-pointer">
                <option value="" disabled selected>Pilih Program Keahlian Anda</option>
                @foreach($majors as $major)
                    <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                @endforeach
            </select>
            @error('major_id') <span class="text-rose-300 font-semibold text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Input Email -->
        <div>
            <label for="email" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-envelope text-emerald-400 text-xs"></i>
                <span>Alamat Email</span>
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required
                   autocomplete="email"
                   placeholder="emailaktif@gmail.com"
                   class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none placeholder-white/50">
            @error('email') <span class="text-rose-300 font-semibold text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>

        <!-- Input Password dengan Fitur Mata -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                           autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="input-field block w-full rounded-xl py-2.5 pl-4 pr-10 text-sm focus:outline-none placeholder-white/50">
                    <!-- Tombol Mata -->
                    <button type="button" 
                            onclick="togglePassword('password')" 
                            aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-white/60 hover:text-emerald-300 transition-colors">
                        <svg id="icon-password" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Konfirmasi Password dengan Fitur Mata -->
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-white/90 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-xs"></i>
                    <span>Konfirmasi</span>
                </label>
                <div class="relative">
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required
                           autocomplete="new-password"
                           placeholder="Ulangi password"
                           class="input-field block w-full rounded-xl py-2.5 pl-4 pr-10 text-sm focus:outline-none placeholder-white/50">
                    <!-- Tombol Mata -->
                    <button type="button" 
                            onclick="togglePassword('password_confirmation')" 
                            aria-label="Tampilkan konfirmasi password"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-white/60 hover:text-emerald-300 transition-colors">
                        <svg id="icon-password_confirmation" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @error('password') <span class="text-rose-300 font-semibold text-xs block -mt-1">{{ $message }}</span> @enderror

        <!-- Tombol Daftar -->
        <div class="pt-3 pb-1">
            <button type="submit" 
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 transition-all duration-300 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-[#063024]">
                <i class="fa-solid fa-user-plus mr-2 text-xs"></i>
                Buat Akun Siswa
            </button>
        </div>

        <!-- Link ke Login -->
        <div class="text-center text-xs font-medium text-white/80 pt-2 border-t border-white/15">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-emerald-300 hover:text-emerald-100 hover:underline transition-colors ml-1">
                Masuk di sini
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