<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-5 bg-orange-500 rounded-full"></span>
            <h1 class="text-xl font-black tracking-wide uppercase text-white">
                BUAT <span class="text-orange-500">AKUN BARU</span>
            </h1>
        </div>
        <p class="text-xs text-gray-400">
            Mulai pantau diskon game PC lintas toko dan rencanakan anggaran belanjamu dalam Rupiah.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                Nama Lengkap
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Masukkan nama kamu"
                   class="w-full rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-500 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
            @error('name')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="nama@email.com"
                   class="w-full rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-500 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="Minimal 8 karakter"
                   class="w-full rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-500 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-300 mb-1.5">
                Konfirmasi Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="Ulangi password"
                   class="w-full rounded-lg bg-[#1a1a22] border border-white/15 text-white placeholder-gray-500 text-sm px-3.5 py-2.5 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition">
            @error('password_confirmation')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Daftar -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full py-2.5 px-4 bg-orange-600 hover:bg-orange-500 text-white font-extrabold text-sm uppercase tracking-wider rounded-lg transition shadow-lg shadow-orange-600/30 cursor-pointer">
                Daftar & Mulai Atur Budget →
            </button>
        </div>

        <!-- Tautan ke Halaman Login -->
        <div class="pt-4 border-t border-white/10 text-center">
            <p class="text-xs text-gray-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-orange-400 hover:text-orange-300 underline ml-1">
                    Masuk di Sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>