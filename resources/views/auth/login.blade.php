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

        <!-- Input Password -->
        <div>
            <label for="password" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
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
</x-guest-layout>