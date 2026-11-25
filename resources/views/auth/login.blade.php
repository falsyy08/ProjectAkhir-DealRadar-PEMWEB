<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-5 bg-orange-500 rounded-full"></span>
            <h1 class="text-xl font-black tracking-wide uppercase text-white">
                MASUK <span class="text-orange-500">AKUN</span>
            </h1>
        </div>
        <p class="text-xs text-gray-400">
            Kelola wishlist game incaran, detektor diskon palsu, dan batas anggaran belanjamu.
        </p>
    </div>

    <!-- Status Session (misal setelah reset password) -->
    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="nama@email.com"
                   class="w-full rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-500 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-300">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-amber-300 hover:text-orange-400 transition">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-500 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ingat Saya -->
        <div class="flex items-center pt-1">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 rounded bg-[#1a1a22] border-white/20 text-orange-600 focus:ring-orange-500 focus:ring-offset-0">
            <label for="remember_me" class="ms-2 text-xs text-gray-300 select-none cursor-pointer">
                Ingat sesi masuk saya
            </label>
        </div>

        <!-- Tombol Submit -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full py-2.5 px-4 bg-orange-600 hover:bg-orange-500 text-white font-extrabold text-sm uppercase tracking-wider rounded-lg transition shadow-lg shadow-orange-600/30 cursor-pointer">
                Masuk ke Dashboard →
            </button>
        </div>

        <!-- Tautan ke Halaman Daftar -->
        <div class="pt-4 border-t border-white/10 text-center">
            <p class="text-xs text-gray-400">
                Belum punya akun DealRadar?
                <a href="{{ route('register') }}" class="font-bold text-orange-400 hover:text-orange-300 underline ml-1">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>