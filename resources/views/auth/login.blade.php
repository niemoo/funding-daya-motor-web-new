<x-layouts.auth>
    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- ═══ LEFT PANEL ═══ --}}
        <div class="relative hidden lg:flex flex-col justify-between p-14 overflow-hidden"
            style="background: linear-gradient(150deg, #0e3a6b 0%, #1D61AF 60%, #2d7dd4 100%)">

            {{-- Decorative rings --}}
            <div class="absolute top-0 right-0 w-[420px] h-[420px] rounded-full border border-white/10"
                style="transform: translate(110px,-120px)"></div>
            <div class="absolute bottom-0 left-0 w-[260px] h-[260px] rounded-full border border-white/10"
                style="transform: translate(-90px,50px)"></div>
            <div class="absolute inset-0"
                style="background: radial-gradient(ellipse at 10% 10%, rgba(255,255,255,0.07) 0%, transparent 55%), radial-gradient(ellipse at 90% 90%, rgba(255,255,255,0.04) 0%, transparent 55%)">
            </div>

            {{-- Brand --}}
            <div class="relative z-10 flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-xl flex items-center justify-center text-xl bg-white/15 border border-white/25">
                    📍
                </div>
                <span class="text-[17px] font-extrabold text-white tracking-tight">OptiPart</span>
            </div>

            {{-- Hero --}}
            <div class="relative z-10">
                <h1 class="text-[42px] font-extrabold text-white leading-[1.12] tracking-tight mb-4">
                    Monitor Tim Sales<br>
                    <span class="text-white/60">Secara Real-time</span>
                </h1>
                <p class="text-[15px] text-white/60 leading-relaxed max-w-sm">
                    Pantau aktivitas kunjungan toko, check-in/out, dan performa sales dari satu dashboard terpusat.
                </p>
            </div>

            {{-- Stats --}}
            <div class="relative z-10 flex gap-9">
                {{-- <div>
                    <div class="text-[28px] font-extrabold text-white tracking-tight">248</div>
                    <div class="text-[12px] text-white/50 mt-0.5">Total Kunjungan</div>
                </div>
                <div>
                    <div class="text-[28px] font-extrabold text-white tracking-tight">18</div>
                    <div class="text-[12px] text-white/50 mt-0.5">Sales Aktif</div>
                </div>
                <div>
                    <div class="text-[28px] font-extrabold text-white tracking-tight">96%</div>
                    <div class="text-[12px] text-white/50 mt-0.5">Kehadiran</div>
                </div> --}}
            </div>
        </div>

        {{-- ═══ RIGHT PANEL — FORM ═══ --}}
        <div class="flex items-center justify-center px-8 py-12 lg:px-20 bg-white min-h-screen lg:min-h-0">
            <div class="w-full max-w-sm">

                {{-- Mobile brand --}}
                <div class="flex items-center gap-2.5 mb-8 lg:hidden">
                    <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-lg">📍</div>
                    <span class="text-[16px] font-extrabold text-slate-800">OptiPart</span>
                </div>

                <h2 class="text-[26px] font-extrabold text-slate-800 tracking-tight mb-1.5">Selamat Datang 👋</h2>
                <p class="text-slate-400 text-[14px] mb-8">Masuk ke akun Anda untuk melanjutkan</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@email.com"
                            class="w-full px-3.5 py-3 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all placeholder-slate-400
                           {{ $errors->has('email') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-100' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        @error('email')
                            <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                        <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Password</label>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="w-full px-3.5 py-3 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all placeholder-slate-400
                           {{ $errors->has('password') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-100' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        @error('password')
                            <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember --}}
                    <div class="flex items-center gap-2 mb-5">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-4 h-4 rounded border-slate-300 accent-brand-600">
                        <label for="remember" class="text-[13px] text-slate-500 cursor-pointer">Ingat saya</label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full py-3 rounded-[10px] text-[15px] font-bold text-white bg-brand-600 hover:bg-brand-700 transition-all duration-150 tracking-tight"
                        style="box-shadow: 0 5px 18px rgba(29,97,175,0.28)">
                        Masuk ke Dashboard →
                    </button>
                </form>

                <div class="mt-7 pt-5 border-t border-slate-100 text-center text-[12px] text-slate-400">
                    © {{ date('Y') }} OptiPart · Sistem khusus tim internal
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>
{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
