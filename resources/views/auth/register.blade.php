<x-guest-layout>
    <!-- Header -->
    <div class="mb-8 text-center text-white">
        <h1 class="text-2xl font-extrabold tracking-wider drop-shadow-md uppercase bg-clip-text text-transparent bg-gradient-to-r from-emerald-300 to-cyan-300">
            E-JURNAL
        </h1>
        <h2 class="text-lg font-semibold tracking-wide drop-shadow-sm uppercase">SMKN 1 BERINGIN</h2>
        <p class="mt-2 text-sm font-medium text-white/80">Daftar untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Input Nama -->
        <div>
            <label for="name" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-0">
            @error('name') <span class="text-red-400 font-medium text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Input NISN -->
        <div>
            <label for="nisn" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">NISN</label>
            <input id="nisn" type="text" name="nisn" value="{{ old('nisn') }}" required
                   class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-0">
            @error('nisn') <span class="text-red-400 font-medium text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <!-- Input Jurusan -->
        <div>
            <label for="major_id" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Jurusan</label>
            <select id="major_id" name="major_id" required
                    class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-0 [&>option]:text-gray-900">
                <option value="" disabled selected>Pilih</option>
                @foreach($majors as $major)
                    <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                @endforeach
            </select>
            @error('major_id') <span class="text-red-400 font-medium text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Input Email -->
        <div>
            <label for="email" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="input-field block w-full rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:ring-0">
            @error('email') <span class="text-red-400 font-medium text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Input Password dengan Fitur Mata -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="password" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                           class="input-field block w-full rounded-xl py-2.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-0">
                    <!-- Tombol Mata -->
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 px-3 flex items-center text-white/50 hover:text-white transition">
                        <svg id="icon-password" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Konfirmasi Password dengan Fitur Mata -->
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold text-white/90 uppercase tracking-wider">Konfirmasi</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="input-field block w-full rounded-xl py-2.5 pl-4 pr-10 text-sm focus:outline-none focus:ring-0">
                    <!-- Tombol Mata -->
                    <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 px-3 flex items-center text-white/50 hover:text-white transition">
                        <svg id="icon-password_confirmation" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @error('password') <span class="text-red-400 font-medium text-xs block -mt-2">{{ $message }}</span> @enderror

        <!-- Tombol Daftar -->
        <div class="pt-4 pb-2">
            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] hover:shadow-emerald-500/50">
                Buat Akun Baru
            </button>
        </div>

        <!-- Link ke Login -->
        <div class="text-center text-xs font-medium text-white/80">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-emerald-300 hover:text-emerald-100 hover:underline transition">Masuk di sini</a>
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