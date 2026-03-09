<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — OptiPart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#EBF3FC',
                            100: '#BFDBF7',
                            200: '#93C3F2',
                            500: '#2d7dd4',
                            600: '#1D61AF',
                            700: '#154d8c',
                            900: '#0e3a6b',
                        },
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .nav-active {
            background: #EBF3FC !important;
            color: #1D61AF !important;
            font-weight: 600;
            border-left: 3px solid #1D61AF;
            padding-left: 7px !important;
        }

        .nav-active .nav-icon {
            color: #1D61AF;
        }

        .dropdown-menu {
            opacity: 0;
            transform: translateY(-6px) scale(0.97);
            pointer-events: none;
            transition: all .18s ease;
        }

        .dropdown-menu.open {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #F8FAFC;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 3px;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800">

    <div class="flex min-h-screen">

        {{-- ═══ OVERLAY (mobile) ═══ --}}
        <div id="sidebar-overlay" onclick="closeSidebar()"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden lg:hidden">
        </div>

        {{-- ═══ SIDEBAR ═══ --}}
        <aside id="sidebar"
            class="fixed top-0 left-0 h-full w-60 bg-white border-r border-slate-200 flex flex-col z-50
                  -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen
                  transition-transform duration-250 ease-in-out">

            {{-- Brand --}}
            <div class="flex items-center gap-2.5 px-4 py-[18px] border-b border-slate-100">
                <div class="w-[34px] h-[34px] rounded-[9px] bg-brand-600 flex items-center justify-center text-base flex-shrink-0"
                    style="box-shadow:0 3px 10px rgba(29,97,175,0.3)">
                    📍
                </div>
                <span class="text-[15px] font-extrabold text-slate-800 tracking-tight">OptiPart</span>
                {{-- Close btn (mobile only) --}}
                <button onclick="closeSidebar()"
                    class="ml-auto lg:hidden w-7 h-7 rounded-[7px] bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center text-sm transition-colors">
                    ✕
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-2.5 py-4 space-y-0.5 overflow-y-auto">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 px-2 mb-1">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2.5 px-2.5 py-[9px] rounded-[9px] text-[13.5px] font-medium text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-all duration-150
               {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                    <span class="nav-icon text-[15px] w-5 text-center">🏠</span>
                    Dashboard
                </a>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-2.5 px-2.5 py-[9px] rounded-[9px] text-[13.5px] font-medium text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-all duration-150
               {{ request()->routeIs('users.*') ? 'nav-active' : '' }}">
                        <span class="nav-icon text-[15px] w-5 text-center">👥</span>
                        Users
                    </a>
                @endif

                <a href="{{ route('attendances.index') }}"
                    class="flex items-center gap-2.5 px-2.5 py-[9px] rounded-[9px] text-[13.5px] font-medium text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-all duration-150
               {{ request()->routeIs('attendances.*') ? 'nav-active' : '' }}">
                    <span class="nav-icon text-[15px] w-5 text-center">📋</span>
                    <span class="flex-1">Absensi</span>
                    @php
                        $ongoingCount = \App\Models\Attendance::whereNull('checkout_time')
                            ->whereDate('attendance_date', today())
                            ->when(!auth()->user()->isAdmin(), fn($q) => $q->where('user_id', auth()->id()))
                            ->count();
                    @endphp
                    @if ($ongoingCount > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-brand-600 text-white">
                            {{ $ongoingCount }}
                        </span>
                    @endif
                </a>
            </nav>

            {{-- User card --}}
            <div class="p-2.5 border-t border-slate-100">
                <div
                    class="flex items-center gap-2.5 px-2 py-2 rounded-[9px] hover:bg-slate-50 cursor-pointer transition-colors">
                    <div
                        class="w-8 h-8 rounded-[9px] bg-brand-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[13px] font-600 text-slate-800 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ auth()->user()->role->name }}</div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ═══ MAIN ═══ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Topbar --}}
            <header
                class="sticky top-0 z-30 h-[60px] bg-white border-b border-slate-200 flex items-center justify-between px-5 gap-4">

                <div class="flex items-center gap-3">
                    {{-- Hamburger --}}
                    <button onclick="openSidebar()"
                        class="lg:hidden w-9 h-9 rounded-[9px] border-[1.5px] border-slate-200 bg-white flex items-center justify-center text-lg text-slate-600 hover:bg-slate-50 transition-colors">
                        ☰
                    </button>
                    <div>
                        <h1 class="text-[17px] font-bold text-slate-800 tracking-tight leading-none">
                            {{ $title ?? 'Dashboard' }}</h1>
                        <p class="text-[12px] text-slate-400 mt-0.5">
                            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative" id="user-dropdown-wrap">
                    <button onclick="toggleDropdown()"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-[9px] bg-brand-50 border-[1.5px] border-brand-100 text-brand-600 text-[13px] font-semibold hover:bg-brand-100 transition-colors cursor-pointer">
                        <div
                            class="w-6 h-6 rounded-[6px] bg-brand-600 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                        <span id="dd-chevron" class="text-[10px] transition-transform duration-200">▾</span>
                    </button>

                    {{-- Dropdown --}}
                    <div id="user-dropdown"
                        class="dropdown-menu absolute top-[calc(100%+8px)] right-0 bg-white border-[1.5px] border-slate-200 rounded-xl min-w-[200px] shadow-xl shadow-slate-200/60 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <div class="text-[13px] font-bold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ auth()->user()->role->name }}</div>
                        </div>
                        <a href="#"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors cursor-pointer">
                            <span class="text-[15px]">👤</span> Lihat Profile
                        </a>
                        <div class="h-px bg-slate-100 mx-0"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-[13px] font-medium text-rose-500 hover:bg-rose-50 transition-colors cursor-pointer">
                                <span class="text-[15px]">⏻</span> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-6 bg-slate-50">

                @if (session('success'))
                    <div
                        class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                        ❌ {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function toggleDropdown() {
            const dd = document.getElementById('user-dropdown');
            const chev = document.getElementById('dd-chevron');
            const isOpen = dd.classList.contains('open');
            dd.classList.toggle('open', !isOpen);
            chev.style.transform = isOpen ? '' : 'rotate(180deg)';
        }
        document.addEventListener('click', function(e) {
            if (!document.getElementById('user-dropdown-wrap').contains(e.target)) {
                document.getElementById('user-dropdown').classList.remove('open');
                document.getElementById('dd-chevron').style.transform = '';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>

</html>
