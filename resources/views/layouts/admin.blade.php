<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Jurnal') }} - Administrator</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- FontAwesome (Non-blocking) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        </noscript>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            /* Custom Scrollbar */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-800" x-data="{ sidebarOpen: false, sidebarMinimized: false }">
        
        <div class="flex h-screen w-full overflow-hidden">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

            <!-- Sidebar -->
            <aside 
                :class="{
                    'translate-x-0': sidebarOpen,
                    '-translate-x-full': !sidebarOpen,
                    'w-64': !sidebarMinimized,
                    'w-20': sidebarMinimized
                }" 
                class="fixed inset-y-0 left-0 z-40 lg:z-10 bg-white border-r border-slate-200 transition-all duration-300 ease-in-out lg:translate-x-0 lg:static flex flex-col shadow-sm">
                
                <!-- Brand Header -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-slate-200 shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden group">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah" class="block h-8 w-auto shrink-0 group-hover:scale-105 transition-transform" />
                        <span x-show="!sidebarMinimized" class="text-lg font-bold text-slate-800 tracking-wide truncate">E-Jurnal</span>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="flex-1 overflow-y-auto overflow-x-hidden py-4">
                    <ul class="space-y-1 px-3">
                        <li x-show="!sidebarMinimized" class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-2">Menu Utama</li>
                        
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Dashboard' : ''">
                                <i class="fa-solid fa-chart-line w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Dashboard</span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.majors.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.majors.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Manajemen Jurusan' : ''">
                                <i class="fa-solid fa-layer-group w-5 text-center {{ request()->routeIs('admin.majors.*') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Manajemen Jurusan</span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Manajemen Siswa' : ''">
                                <i class="fa-solid fa-users w-5 text-center {{ request()->routeIs('admin.students.*') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Manajemen Siswa</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.validations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.validations.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Validasi Bukti Siswa' : ''">
                                <i class="fa-solid fa-camera-retro w-5 text-center {{ request()->routeIs('admin.validations.*') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Validasi Bukti Siswa</span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.kaprodi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.kaprodi.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Manajemen Kaprodi' : ''">
                                <i class="fa-solid fa-user-tie w-5 text-center {{ request()->routeIs('admin.kaprodi.*') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Manajemen Kaprodi</span>
                            </a>
                        </li>
                        
                        <li x-show="!sidebarMinimized" class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">Konfigurasi</li>
                        
                        <li>
                            <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.templates.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Master Template' : ''">
                                <i class="fa-solid fa-file-signature w-5 text-center {{ request()->routeIs('admin.templates.*') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Master Template</span>
                            </a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.assessments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.assessments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}" :title="sidebarMinimized ? 'Master Penilaian' : ''">
                                <i class="fa-solid fa-star-half-stroke w-5 text-center {{ request()->routeIs('admin.assessments.*') ? 'text-indigo-700' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarMinimized" class="truncate">Master Penilaian</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- User Profile & Logout -->
                <div class="border-t border-slate-200 p-4 shrink-0">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 shrink-0">
                            <i class="fa-solid fa-user-shield text-slate-500"></i>
                        </div>
                        <div x-show="!sidebarMinimized" class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">Administrator</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 border border-slate-200 text-slate-600 text-sm font-medium rounded-md transition-colors" :title="sidebarMinimized ? 'Logout' : ''">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span x-show="!sidebarMinimized">Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Workspace -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-hidden">
                
                <!-- Navbar / Header -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 shrink-0 z-10">
                    <div class="flex items-center gap-4">
                        <!-- Mobile Menu Toggle -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Desktop Sidebar Minimize Toggle -->
                        <button @click="sidebarMinimized = !sidebarMinimized" class="hidden lg:flex text-slate-500 hover:text-indigo-600 transition-colors focus:outline-none">
                            <i class="fa-solid" :class="sidebarMinimized ? 'fa-indent' : 'fa-outdent'" class="text-xl"></i>
                        </button>

                        <div class="hidden sm:block">
                            @if (isset($header))
                                {{ $header }}
                            @else
                                <h2 class="text-lg font-bold text-slate-800">Admin Dashboard</h2>
                            @endif
                        </div>
                    </div>

                    <!-- Right Elements -->
                    <div class="flex items-center gap-4" x-data="clock()">
                        <div class="hidden sm:flex flex-col items-end">
                            <div class="flex items-center gap-1.5 text-sm font-bold text-slate-700">
                                <i class="fa-regular fa-clock text-indigo-500 mr-0.5"></i>
                                <span x-text="time"></span>
                            </div>
                            <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto px-4 sm:px-6 py-6 lg:py-8">
                    <div class="sm:hidden mb-6">
                        @if (isset($header))
                            {{ $header }}
                        @endif
                    </div>
                    
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
                
            </div>
        </div>

        <script>
            function clock() {
                return {
                    time: '',
                    init() {
                        this.updateTime();
                        setInterval(() => this.updateTime(), 1000);
                    },
                    updateTime() {
                        const now = new Date();
                        this.time = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    }
                }
            }
        </script>
    </body>
</html>
