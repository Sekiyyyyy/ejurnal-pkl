<section class="space-y-6">
    <div class="flex items-center gap-3.5 pb-4 border-b border-rose-100">
        <div class="size-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shadow-sm border border-rose-100/50">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">
                {{ __('Zona Berbahaya: Hapus Akun') }}
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ __('Tindakan ini permanen. Seluruh data, jurnal, dan aktivitas akan dihapus selamanya.') }}
            </p>
        </div>
    </div>

    <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200/80 text-xs text-rose-800 flex items-start gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
        <div class="space-y-1">
            <p class="font-bold text-rose-900">{{ __('Peringatan Penghapusan Akun:') }}</p>
            <p class="text-rose-700 leading-relaxed">
                {{ __('Setelah akun Anda dihapus, semua data profil, catatan jurnal, riwayat kehadiran, serta penilaian yang terkait akan dihapus secara permanen dan tidak dapat dipulihkan kembali.') }}
            </p>
        </div>
    </div>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 hover:border-rose-300 text-xs font-bold uppercase tracking-wider rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-rose-500"
        >
            <i class="fa-regular fa-trash-can"></i>
            <span>{{ __('Hapus Akun Saya Secara Permanen') }}</span>
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4">
                <div class="size-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                        {{ __('Konfirmasi Penghapusan Akun') }}
                    </h2>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                        {{ __('Apakah Anda yakin ingin menghapus akun Anda? Seluruh riwayat jurnal, data PKL, dan informasi akun akan dimusnahkan. Silakan masukkan kata sandi Anda untuk mengonfirmasi.') }}
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    {{ __('Kata Sandi Akun') }}
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-lock text-sm"></i>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full pl-10 pr-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl transition duration-150 ease-in-out focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 hover:border-slate-300 placeholder-slate-400"
                        placeholder="{{ __('Masukkan kata sandi untuk verifikasi') }}"
                    />
                </div>
                @if ($errors->userDeletion->get('password'))
                    <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5 font-medium">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $errors->userDeletion->first('password') }}
                    </p>
                @endif
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-colors focus:outline-none"
                >
                    {{ __('Batal') }}
                </button>

                <button 
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md shadow-rose-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-rose-500"
                >
                    <i class="fa-regular fa-trash-can"></i>
                    <span>{{ __('Ya, Hapus Akun') }}</span>
                </button>
            </div>
        </form>
    </x-modal>
</section>
