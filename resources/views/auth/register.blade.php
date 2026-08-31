<x-guest-layout>
    <!-- Header -->
    <div class="mb-6 text-center text-white">
        <h1 class="text-xl font-extrabold tracking-wide drop-shadow-sm uppercase">E-JURNAL SMKN 1 BERINGIN</h1>
        <p class="mt-1 text-sm font-medium text-white/90">Daftar untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Input Nama -->
        <div>
            <label for="name" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
        </div>

        <!-- Input NISN -->
        <div>
            <label for="nisn" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">NISN</label>
            <input id="nisn" type="text" name="nisn" value="{{ old('nisn') }}" required
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
        </div>
        
        <!-- Input Jurusan -->
        <div>
            <label for="major_id" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Jurusan</label>
            <select id="major_id" name="major_id" required
                    class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-[#21a650]">
                <option value="" disabled selected>Pilih</option>
                @foreach($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Input Email -->
        <div>
            <label for="email" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
        </div>

        <!-- Input Password -->
        <div>
            <label for="password" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Password</label>
            <input id="password" type="password" name="password" required
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
        </div>
        
        <!-- Konfirmasi Password -->
        <div>
            <label for="password_confirmation" class="mb-1 block text-xs font-semibold text-white drop-shadow-sm">Konfirmasi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="block w-full rounded-lg border-none bg-[#f4f7fb] px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-[#21a650]">
        </div>

        <!-- Tombol Daftar -->
        <div class="pt-3">
            <button type="submit" class="w-full rounded-lg bg-[#21a650] py-3 text-sm font-bold text-white transition hover:bg-[#1d8f45]">
                Daftar
            </button>
        </div>

        <!-- Link ke Login -->
        <div class="mt-4 text-center text-xs font-medium text-white drop-shadow-sm">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-bold hover:underline">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>