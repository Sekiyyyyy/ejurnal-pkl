<section x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
    <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-slate-100">
        <div class="size-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-sm border border-emerald-100/50">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">
                {{ __('Perbarui Kata Sandi') }}
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ __('Pastikan akun Anda terlindungi dengan menggunakan kata sandi yang aman dan tidak mudah ditebak.') }}
            </p>
        </div>
    </div>

    <!-- Password Requirement Info -->
    <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-indigo-500 text-sm mt-0.5 shrink-0"></i>
        <div class="space-y-1">
            <p class="font-semibold text-slate-700">{{ __('Tips Keamanan Kata Sandi:') }}</p>
            <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                <li>Gunakan minimal 8 karakter.</li>
                <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol.</li>
                <li>Hindari menggunakan tanggal lahir atau data pribadi yang mudah ditebak.</li>
            </ul>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <!-- Kata Sandi Saat Ini -->
        <div>
            <label for="update_password_current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                {{ __('Kata Sandi Saat Ini') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-lock text-sm"></i>
                </div>
                <input 
                    id="update_password_current_password" 
                    name="current_password" 
                    :type="showCurrent ? 'text' : 'password'" 
                    class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl transition duration-150 ease-in-out focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 hover:border-slate-300 placeholder-slate-400" 
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi lama Anda"
                />
                <button 
                    type="button" 
                    @click="showCurrent = !showCurrent" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan / Sembunyikan Kata Sandi"
                >
                    <i class="fa-regular" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @if ($errors->updatePassword->get('current_password'))
                <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->updatePassword->first('current_password') }}
                </p>
            @endif
        </div>

        <!-- Kata Sandi Baru -->
        <div>
            <label for="update_password_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                {{ __('Kata Sandi Baru') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-key text-sm"></i>
                </div>
                <input 
                    id="update_password_password" 
                    name="password" 
                    :type="showNew ? 'text' : 'password'" 
                    class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl transition duration-150 ease-in-out focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 hover:border-slate-300 placeholder-slate-400" 
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <button 
                    type="button" 
                    @click="showNew = !showNew" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan / Sembunyikan Kata Sandi"
                >
                    <i class="fa-regular" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @if ($errors->updatePassword->get('password'))
                <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->updatePassword->first('password') }}
                </p>
            @endif
        </div>

        <!-- Konfirmasi Kata Sandi Baru -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                {{ __('Ulangi Kata Sandi Baru') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-check-double text-sm"></i>
                </div>
                <input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    :type="showConfirm ? 'text' : 'password'" 
                    class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl transition duration-150 ease-in-out focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 hover:border-slate-300 placeholder-slate-400" 
                    autocomplete="new-password"
                    placeholder="Ketik ulang kata sandi baru"
                />
                <button 
                    type="button" 
                    @click="showConfirm = !showConfirm" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                    aria-label="Tampilkan / Sembunyikan Kata Sandi"
                >
                    <i class="fa-regular" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @if ($errors->updatePassword->get('password_confirmation'))
                <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </p>
            @endif
        </div>

        <!-- Tombol Aksi -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all duration-200 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                <i class="fa-solid fa-key"></i>
                <span>{{ __('Perbarui Kata Sandi') }}</span>
            </button>

            @if (session('status') === 'password-updated')
                <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200/80 px-3 py-1.5 rounded-lg animate-fade-in">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('Kata sandi diperbarui') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
