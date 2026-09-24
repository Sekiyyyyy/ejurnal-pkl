<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-600 font-semibold">Pengaturan Profil</span>
                </nav>
                <h2 class="font-bold text-2xl text-slate-800 tracking-tight flex items-center gap-2.5">
                    <i class="fa-solid fa-user-gear text-indigo-600 text-xl"></i>
                    <span>{{ __('Pengaturan Akun Pengguna') }}</span>
                </h2>
            </div>
            
            @if(Auth::user()->role === 'student')
                <a href="{{ route('student.profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold border border-indigo-200/80 transition-all shadow-sm self-start sm:self-auto">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Kelola Biodata PKL & Orang Tua</span>
                </a>
            @elseif(Auth::user()->role === 'kaprodi')
                <a href="{{ route('kaprodi.profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold border border-indigo-200/80 transition-all shadow-sm self-start sm:self-auto">
                    <i class="fa-solid fa-signature"></i>
                    <span>Pengaturan Tanda Tangan Kaprodi</span>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8" x-data="{ activeTab: 'profile' }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Profile Card -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 shadow-xl border border-slate-800">
                <!-- Background Glow Effect -->
                <div class="absolute -top-24 -right-24 size-72 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 size-72 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <!-- Avatar Initials -->
                        @php
                            $names = explode(' ', trim($user->name));
                            $initials = strtoupper(substr($names[0] ?? 'U', 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                        @endphp
                        <div class="relative shrink-0">
                            <div class="size-16 sm:size-20 rounded-2xl bg-gradient-to-tr from-indigo-500 to-emerald-400 flex items-center justify-center text-white text-2xl sm:text-3xl font-black shadow-lg shadow-indigo-500/30 ring-4 ring-white/10">
                                {{ $initials }}
                            </div>
                            <span class="absolute -bottom-1 -right-1 size-5 rounded-full bg-emerald-500 ring-2 ring-slate-900 flex items-center justify-center" title="Akun Aktif">
                                <span class="size-2 rounded-full bg-white animate-pulse"></span>
                            </span>
                        </div>

                        <!-- User Meta Information -->
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white truncate">
                                    {{ $user->name }}
                                </h1>
                                
                                @if($user->role === 'student')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        <i class="fa-solid fa-graduation-cap text-[10px]"></i>
                                        Siswa PKL
                                    </span>
                                @elseif($user->role === 'kaprodi')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                        <i class="fa-solid fa-user-tie text-[10px]"></i>
                                        Kaprodi
                                    </span>
                                @elseif($user->role === 'super_admin')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                                        Guru
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-slate-300 mt-1 flex items-center gap-2">
                                <i class="fa-regular fa-envelope text-slate-400"></i>
                                <span>{{ $user->email }}</span>
                            </p>

                            <!-- Role specifics -->
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2.5 text-xs text-slate-400">
                                @if($user->role === 'student' && $user->student)
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-id-badge text-indigo-400"></i>
                                        NISN: <strong class="text-slate-200">{{ $user->student->nisn }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-school text-indigo-400"></i>
                                        Kelas: <strong class="text-slate-200">{{ $user->student->class ?? '-' }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-network-wired text-indigo-400"></i>
                                        Jurusan: <strong class="text-slate-200">{{ $user->student->major->name ?? '-' }}</strong>
                                    </span>
                                @elseif($user->role === 'kaprodi' && $user->kaprodi)
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-regular fa-id-badge text-purple-400"></i>
                                        NIP: <strong class="text-slate-200">{{ $user->kaprodi->nip ?? '-' }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-network-wired text-purple-400"></i>
                                        Jurusan: <strong class="text-slate-200">{{ $user->kaprodi->major->name ?? '-' }}</strong>
                                    </span>
                                @endif
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar-check text-slate-400"></i>
                                    Bergabung: <span class="text-slate-300">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action / Status Badge -->
                    <div class="flex items-center md:flex-col md:items-end justify-between border-t md:border-t-0 border-white/10 pt-4 md:pt-0">
                        <div class="text-left md:text-right">
                            <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400 block">Status Akun</span>
                            <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5 md:justify-end mt-0.5">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                                Terverifikasi & Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segmented Modern Tabs Navigation -->
            <div class="flex items-center gap-2 p-1.5 bg-slate-200/60 rounded-2xl w-fit max-w-full overflow-x-auto shadow-inner">
                <button 
                    type="button" 
                    @click="activeTab = 'profile'"
                    :class="activeTab === 'profile' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap focus:outline-none"
                >
                    <i class="fa-regular fa-user"></i>
                    <span>Informasi Profil</span>
                </button>

                <button 
                    type="button" 
                    @click="activeTab = 'password'"
                    :class="activeTab === 'password' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap focus:outline-none"
                >
                    <i class="fa-solid fa-lock"></i>
                    <span>Keamanan & Password</span>
                </button>

                <button 
                    type="button" 
                    @click="activeTab = 'danger'"
                    :class="activeTab === 'danger' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap focus:outline-none"
                >
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Zona Berbahaya</span>
                </button>
            </div>

            <!-- Tab 1: Profile Information -->
            <div 
                x-show="activeTab === 'profile'" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80"
            >
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Tab 2: Update Password -->
            <div 
                x-show="activeTab === 'password'" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80"
                style="display: none;"
            >
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Tab 3: Danger Zone (Delete User) -->
            <div 
                x-show="activeTab === 'danger'" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80"
                style="display: none;"
            >
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
