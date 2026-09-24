<section>
    <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-slate-100">
        <div class="size-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-sm border border-indigo-100/50">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">
                {{ __('Informasi Profil Akun') }}
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ __('Perbarui data identitas dasar dan alamat email login akun Anda.') }}
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                {{ __('Nama Lengkap') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-user text-sm"></i>
                </div>
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="block w-full pl-10 pr-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl transition duration-150 ease-in-out focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 hover:border-slate-300 placeholder-slate-400" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="Masukkan nama lengkap Anda"
                />
            </div>
            @if ($errors->get('name'))
                <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first('name') }}
                </p>
            @endif
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                {{ __('Alamat Email') }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-envelope text-sm"></i>
                </div>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="block w-full pl-10 pr-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl transition duration-150 ease-in-out focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 hover:border-slate-300 placeholder-slate-400" 
                    value="{{ old('email', $user->email) }}" 
                    required 
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
            </div>
            @if ($errors->get('email'))
                <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first('email') }}
                </p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-xs text-amber-800 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm mt-0.5 shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-medium">
                            {{ __('Alamat email Anda belum diverifikasi.') }}
                        </p>
                        <button form="send-verification" type="submit" class="mt-1 font-bold text-indigo-700 hover:text-indigo-900 underline hover:no-underline">
                            {{ __('Klik di sini untuk mengirim ulang tautan verifikasi.') }}
                        </button>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-2 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ __('Tautan verifikasi baru telah dikirimkan ke email Anda.') }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Tombol Aksi -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all duration-200 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                <span>{{ __('Simpan Perubahan') }}</span>
            </button>

            @if (session('status') === 'profile-updated')
                <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200/80 px-3 py-1.5 rounded-lg animate-fade-in">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('Perubahan disimpan') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
