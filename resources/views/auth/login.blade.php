<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Lapor Bu!!</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://unpkg.com/feather-icons"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-blue-700 overflow-x-hidden">

    <div class="relative min-h-screen flex items-center justify-center px-4 sm:px-5 py-6 sm:py-8">

        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/10"></div>
        <div class="absolute top-24 -right-24 h-80 w-80 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-28 left-1/3 h-72 w-72 rounded-full bg-blue-400/20"></div>

        <div
            class="relative w-full max-w-lg rounded-3xl sm:rounded-[2rem] bg-white border border-slate-100 p-6 sm:p-8 shadow-2xl">

            <div class="mb-6 sm:mb-8 text-center">
                <img src="{{ asset('asset/logo.png') }}" alt="Logo SDIT Al-Kautsar"
                    class="h-20 sm:h-28 mx-auto object-contain">

                <p class="text-xs text-slate-500 mt-2">
                    SDIT Muhammadiyah Al-Kautsar Kartasura
                </p>
            </div>

            <div class="mb-6 sm:mb-7 text-center">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                    Login
                </h1>

            </div>

            @if ($errors->any())
                <div
                    class="mb-5 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    <i data-feather="alert-circle" class="h-5 w-5 shrink-0"></i>
                    <span>Username atau password tidak sesuai.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Username
                    </label>

                    <div class="relative">
                        <i data-feather="user"
                            class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"></i>

                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username"
                            required autofocus autocomplete="username"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Password
                    </label>

                    <div class="relative">
                        <i data-feather="lock"
                            class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"></i>

                        <input type="password" name="password" id="password" placeholder="Masukkan password" required
                            autocomplete="current-password"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-12 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-700"
                            aria-label="Tampilkan password">
                            <i data-feather="eye" id="eye-icon" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                        <span class="text-sm text-slate-600">
                            Ingat akun saya
                        </span>
                    </label>
                </div>

                <button type="submit" id="loginBtn"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-700 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 active:bg-blue-900">
                    <i data-feather="log-in" class="h-5 w-5"></i>
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-xs leading-relaxed text-slate-400">
                Gunakan akun yang telah diberikan oleh sekolah.<br>
                Jika mengalami kendala login, silakan hubungi administrator.
            </p>
        </div>
    </div>

    <script>
        feather.replace();

        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-feather', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-feather', 'eye');
            }

            feather.replace();
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const button = document.getElementById('loginBtn');

            button.disabled = true;
            button.innerHTML = `
                <i data-feather="loader" class="h-5 w-5 animate-spin"></i>
                Memproses...
            `;

            feather.replace();
        });
    </script>

</body>

</html>