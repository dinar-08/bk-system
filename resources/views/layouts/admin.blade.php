<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | Lapor Bu!!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #sidebar {
            transition: transform 0.3s ease;
        }

        #overlay {
            transition: opacity 0.3s ease;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.65);
            transition: all 0.15s;
            text-decoration: none;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .nav-link.active {
            background: #fff;
            color: #1d4ed8;
        }

        .nav-link.active svg {
            color: #1d4ed8;
        }

        .nav-section {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
            padding: 18px 12px 6px;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <div class="flex min-h-screen">

        {{-- Overlay mobile --}}
        <div id="overlay" class="fixed inset-0 bg-black/40 z-20 hidden opacity-0 lg:hidden" onclick="closeSidebar()">
        </div>

        {{-- SIDEBAR --}}
        <aside id="sidebar"
            class="fixed left-0 top-0 h-screen w-60 flex flex-col z-30 -translate-x-full lg:translate-x-0 shadow-lg"
            style="background:#1d4ed8;">

            {{-- Logo full width --}}
            <div class="border-b flex-shrink-0" style="border-color:rgba(255,255,255,0.15);">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="w-full object-contain p-4"
                    style="max-height:100px;">
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-3 overflow-y-auto">
                <p class="nav-section">Menu Utama</p>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <p class="nav-section">Data</p>
                <a href="{{ route('admin.guru-bk.index') }}"
                    class="nav-link {{ request()->routeIs('admin.guru-bk.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Data Guru BK
                </a>
                <a href="{{ route('admin.siswa.index') }}"
                    class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Data Siswa
                </a>
                <p class="nav-section">Lainnya</p>
                <a href="{{ route('admin.arsip.index') }}"
                    class="nav-link {{ request()->routeIs('admin.arsip.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Arsip Siswa
                </a>
                <a href="{{ route('profile.edit') }}"
                    class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    Profil
                </a>
            </nav>

            {{-- Footer --}}
            <div class="p-3" style="border-top:1px solid rgba(255,255,255,0.15);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors"
                        style="color:rgba(255,255,255,0.65);"
                        onmouseover="this.style.background='rgba(255,255,255,0.12)';this.style.color='#fff';"
                        onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.65)';">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col lg:ml-60">

            {{-- Topbar mobile --}}
            <header class="lg:hidden sticky top-0 z-10 px-4 py-3 flex items-center gap-3 shadow-sm"
                style="background:#1d4ed8;">

                <button onclick="openSidebar()" class="text-white p-1 -ml-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div>
                    @php
                        $hour = now('Asia/Jakarta')->hour;

                        $greeting = match (true) {
                            $hour >= 4 && $hour < 11 => 'Selamat Pagi',
                            $hour >= 11 && $hour < 15 => 'Selamat Siang',
                            $hour >= 15 && $hour < 18 => 'Selamat Sore',
                            default => 'Selamat Malam',
                        };
                    @endphp

                    <h1 class="font-semibold text-white text-sm">
                        {{ $greeting }}, {{ auth()->user()->name ?? 'Admin' }}
                    </h1>

                    <p class="text-xs text-blue-100 mt-0.5">
                        {{ now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                        •
                        {{ now('Asia/Jakarta')->format('H:i') }} WIB
                    </p>
                </div>
            </header>

            {{-- Topbar desktop --}}
            <header class="hidden lg:flex sticky top-0 z-10 px-8 py-4 items-center" style="background:#1d4ed8;">

                <div>
                    @php
                        $hour = now('Asia/Jakarta')->hour;

                        $greeting = match (true) {
                            $hour >= 4 && $hour < 11 => 'Selamat Pagi',
                            $hour >= 11 && $hour < 15 => 'Selamat Siang',
                            $hour >= 15 && $hour < 18 => 'Selamat Sore',
                            default => 'Selamat Malam',
                        };
                    @endphp

                    <h1 class="font-semibold text-white text-base">
                        {{ $greeting }}, {{ auth()->user()->name ?? 'Admin' }}
                    </h1>

                    <p class="text-xs text-blue-100 mt-0.5">
                        {{ now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                        •
                        {{ now('Asia/Jakarta')->format('H:i') }} WIB
                    </p>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-8">
                @if (session('success'))
                    <div
                        class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        function openSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('overlay');
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
            setTimeout(() => o.classList.remove('opacity-0'), 10);
        }
        function closeSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('overlay');
            s.classList.add('-translate-x-full');
            o.classList.add('opacity-0');
            setTimeout(() => o.classList.add('hidden'), 300);
        }
    </script>

</body>

</html>