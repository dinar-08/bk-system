<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>@yield('title', 'Lapor Bu!!')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://unpkg.com/feather-icons"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #sidebar {
            transition: transform 0.3s ease;
            /* fallback dulu untuk browser lama, baru dioverride dvh di bawah */
            height: 100vh;
            height: 100dvh;
        }

        #overlay {
            transition: opacity 0.3s ease;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
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

        .nav-link.active i {
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

        #sidebar-footer {
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        /* ---- Penyesuaian khusus mobile ---- */
        @media (max-width: 1023px) {
            #sidebar {
                width: min(78vw, 260px);
            }

            header.mobile-topbar {
                padding-top: calc(0.75rem + env(safe-area-inset-top));
            }
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
            class="fixed left-0 top-0 w-60 flex flex-col z-30 -translate-x-full lg:translate-x-0 shadow-lg lg:h-screen"
            style="background:#1d4ed8;">

            {{-- Logo full width --}}
            <div class="flex-shrink-0 flex items-center justify-between"
                style="border-bottom:1px solid rgba(255,255,255,0.15);">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo" class="w-full object-contain p-3 lg:p-4"
                    style="max-height:80px;">

                {{-- Tombol tutup, hanya tampil di mobile --}}
                <button onclick="closeSidebar()" class="lg:hidden text-white/70 p-2 mr-2 flex-shrink-0"
                    aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 min-h-0 px-3 py-3 overflow-y-auto">
                <p class="nav-section">Menu</p>
                <a href="{{ route('orang_tua.dashboard') }}"
                    class="nav-link {{ request()->routeIs('orang_tua.dashboard') ? 'active' : '' }}">
                    <i data-feather="grid" class="w-4 h-4 flex-shrink-0"></i>
                    Dashboard
                </a>

                @php
                    $notifLaporan = auth()->user()->unreadNotifications()
                        ->whereIn('type', [
                            \App\Notifications\LaporanBaruNotification::class,
                            \App\Notifications\EvaluasiBaruNotification::class,
                            \App\Notifications\JadwalPemanggilanNotification::class,
                        ])->count();

                    $notifPerkembangan = auth()->user()->unreadNotifications()
                        ->where('type', \App\Notifications\MonitoringBaruNotification::class)
                        ->count();
                @endphp

                <a href="{{ route('orang_tua.laporan.index') }}"
                    class="nav-link {{ request()->routeIs('orang_tua.laporan.*') ? 'active' : '' }}"
                    style="justify-content: space-between;">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <i data-feather="file-text" class="w-4 h-4 flex-shrink-0"></i>
                        Laporan Saya
                    </span>
                    @if($notifLaporan > 0)
                        <span
                            style="background:#ef4444;color:#fff;font-size:10.5px;font-weight:700;min-width:18px;height:18px;border-radius:9999px;display:flex;align-items:center;justify-content:center;padding:0 4px;">
                            {{ $notifLaporan > 9 ? '9+' : $notifLaporan }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('orang_tua.perkembangan') }}"
                    class="nav-link {{ request()->routeIs('orang_tua.perkembangan*') ? 'active' : '' }}"
                    style="justify-content: space-between;">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <i data-feather="trending-up" class="w-4 h-4 flex-shrink-0"></i>
                        Perkembangan Anak
                    </span>
                    @if($notifPerkembangan > 0)
                        <span
                            style="background:#ef4444;color:#fff;font-size:10.5px;font-weight:700;min-width:18px;height:18px;border-radius:9999px;display:flex;align-items:center;justify-content:center;padding:0 4px;">
                            {{ $notifPerkembangan > 9 ? '9+' : $notifPerkembangan }}
                        </span>
                    @endif
                </a>

                <p class="nav-section">Akun</p>
                <a href="{{ route('profile.edit') }}"
                    class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i data-feather="user" class="w-4 h-4 flex-shrink-0"></i>
                    Profil
                </a>
            </nav>

            {{-- Footer --}}
            <div id="sidebar-footer" class="flex-shrink-0 p-3" style="border-top:1px solid rgba(255,255,255,0.15);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors"
                        style="color:rgba(255,255,255,0.65);"
                        onmouseover="this.style.background='rgba(255,255,255,0.12)';this.style.color='#fff';"
                        onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.65)';">
                        <i data-feather="log-out" class="w-4 h-4 flex-shrink-0"></i>
                        Keluar
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col lg:ml-60">

            {{-- Topbar mobile --}}
            <header class="mobile-topbar lg:hidden sticky top-0 z-10 px-4 pb-3 flex items-center gap-3 shadow-sm"
                style="background:#1d4ed8;">

                <button onclick="openSidebar()" class="text-white p-2 -ml-2 flex-shrink-0" aria-label="Buka menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex-1 min-w-0">
                    @php
                        $hour = now('Asia/Jakarta')->hour;

                        $greeting = match (true) {
                            $hour >= 4 && $hour < 11 => 'Selamat Pagi',
                            $hour >= 11 && $hour < 15 => 'Selamat Siang',
                            $hour >= 15 && $hour < 18 => 'Selamat Sore',
                            default => 'Selamat Malam',
                        };
                    @endphp

                    <h1 class="font-semibold text-white text-sm truncate">
                        {{ $greeting }}, {{ auth()->user()->name ?? 'Admin' }}
                    </h1>

                    <p class="text-[11px] text-blue-100 mt-0.5 truncate">
                        {{ now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                        •
                        {{ now('Asia/Jakarta')->format('H:i') }} WIB
                    </p>
                </div>
            </header>

            {{-- Topbar desktop --}}
            <header class="hidden lg:flex sticky top-0 z-10 px-8 py-4 items-center justify-between"
                style="background:#1d4ed8;">

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

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div
                        class="mb-4 sm:mb-5 flex items-start sm:items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl text-[13px] sm:text-sm">
                        <i data-feather="check-circle" class="w-4 h-4 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="mb-4 sm:mb-5 flex items-start sm:items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl text-[13px] sm:text-sm">
                        <i data-feather="alert-circle" class="w-4 h-4 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        feather.replace();
        function openSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('overlay');
            s.classList.remove('-translate-x-full');
            o.classList.remove('hidden');
            setTimeout(() => o.classList.remove('opacity-0'), 10);
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('overlay');
            s.classList.add('-translate-x-full');
            o.classList.add('opacity-0');
            setTimeout(() => o.classList.add('hidden'), 300);
            document.body.style.overflow = '';
        }
    </script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>

    {{-- ================= Push Notification Browser (kayak WhatsApp) ================= --}}
    <script>
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
        }

        async function daftarPushNotification() {
            const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]').content;

            if (!('serviceWorker' in navigator) || !('PushManager' in window) || !vapidPublicKey) {
                return; // browser tidak didukung, atau VAPID key belum di-generate di server
            }

            try {
                const registration = await navigator.serviceWorker.register('/sw.js');

                let permission = Notification.permission;
                if (permission === 'default') {
                    permission = await Notification.requestPermission();
                }
                if (permission !== 'granted') {
                    return;
                }

                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
                });
                
                await fetch('{{ route('push-subscription.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(subscription.toJSON()),
                });
            } catch (err) {
                console.error('Gagal mendaftarkan push notification:', err);
            }
        }

        daftarPushNotification();
    </script>

</body>

</html>