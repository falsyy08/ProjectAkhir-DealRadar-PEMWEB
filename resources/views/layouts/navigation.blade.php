<nav x-data="{ open: false }" class="relative z-20 bg-[#0c0c10]/90 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Logo DealRadar -->
                <a href="{{ route('home') }}" class="text-2xl font-black tracking-tight uppercase text-white">
                    DEAL <span class="text-orange-500">RADAR</span>
                </a>

                <!-- Menu Navigasi -->
                <div class="hidden sm:flex sm:items-center sm:space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-300 hover:text-orange-400 transition">
                        Beranda Promo
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-bold {{ request()->routeIs('dashboard') ? 'text-orange-500 border-b-2 border-orange-500 py-5' : 'text-gray-300 hover:text-orange-400' }} transition">
                        Wishlist & Budget
                    </a>
                </div>
            </div>

            <!-- Dropdown Profil User -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-white/15 text-xs font-bold rounded-lg text-gray-200 bg-white/5 hover:bg-white/10 transition">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4 text-orange-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil Akun
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar (Log Out)
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Tombol Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-white/10">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#121217] border-t border-white/10 px-4 pt-3 pb-4 space-y-2">
        <a href="{{ route('home') }}" class="block py-2 text-sm text-gray-300">Beranda Promo</a>
        <a href="{{ route('dashboard') }}" class="block py-2 text-sm font-bold text-orange-500">Wishlist & Budget</a>
        <div class="pt-2 border-t border-white/10">
            <p class="text-xs text-gray-400 mb-2">{{ Auth::user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-rose-400">Keluar</button>
            </form>
        </div>
    </div>
</nav>