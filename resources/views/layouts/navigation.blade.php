<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo & Judul Aplikasi -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <!-- Logo Sekolah -->
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah" class="block h-10 w-auto" />
                        <!-- Tulisan E-Jurnal -->
                        <span class="font-bold text-xl text-gray-800 tracking-wide" style="font-family: 'Merriweather', serif;">E-Jurnal</span>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>



                    <!-- KHUSUS SISWA -->
                    @if(Auth::user()->role === 'student')
                        <x-nav-link :href="route('student.profile.edit')" :active="request()->routeIs('student.profile.*')">
                            {{ __('Biodata PKL') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-indigo-100 shadow-sm text-sm leading-4 font-bold rounded-full text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fa-solid fa-user-circle mr-2 text-indigo-400 text-base"></i>
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(Auth::user()->role === 'kaprodi')
                            <x-dropdown-link :href="route('kaprodi.profile.edit')" class="font-bold text-indigo-600 flex items-center justify-between">
                                <span><i class="fa-solid fa-user-pen mr-1.5"></i> {{ __('Pengaturan TTD') }}</span>
                                <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                            </x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile Toggle) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile Menu) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        
        <!-- Navigation Links (Mobile) -->
        <div class="pt-2 pb-3 space-y-1">
            
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>



            <!-- KHUSUS SISWA -->
            @if(Auth::user()->role === 'student')
                <x-responsive-nav-link :href="route('student.profile.edit')" :active="request()->routeIs('student.profile.*')">
                    {{ __('Biodata PKL') }}
                </x-responsive-nav-link>
            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if(Auth::user()->role === 'kaprodi')
                    <x-responsive-nav-link :href="route('kaprodi.profile.edit')" class="font-bold text-indigo-600 flex items-center justify-between pr-4">
                        <span><i class="fa-solid fa-user-pen mr-2"></i> {{ __('Pengaturan Tanda Tangan') }}</span>
                        <span class="flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>