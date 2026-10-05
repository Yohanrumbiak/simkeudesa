<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SIMKeuDesa</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 relative overflow-hidden bg-gradient-to-br from-[#070d1e] via-[#0b132b] to-[#1c2541] text-slate-100">

    <!-- Glowing Background Orbs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 my-8">

        <!-- Institution Emblem & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 text-white shadow-xl shadow-blue-500/25 ring-4 ring-blue-500/20 mb-4">
                <i class="fa-solid fa-landmark-dome text-2xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">SIMKeuDesa</h1>
            <p class="text-xs sm:text-sm text-cyan-300 font-medium mt-1">Sistem Informasi Pengelolaan Keuangan Desa</p>
            <p class="text-xs text-slate-400 mt-0.5">Pemerintah Kabupaten Bandung Barat &bull; DPMD</p>
        </div>

        <!-- Quick Demo Role Switcher -->
        <div class="bg-slate-900/80 backdrop-blur-md rounded-2xl border border-slate-800 p-3.5 mb-5 shadow-lg">
            <div class="text-[11px] font-bold text-slate-300 uppercase tracking-wider text-center mb-2 flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                <span>Pilih Akun Demo Cepat (1-Klik Isi Form)</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillCredentials('admin_pemda', 'password', 'Staff Pemda')" class="p-2 rounded-xl bg-blue-950/60 hover:bg-blue-900/80 border border-blue-800/50 text-left transition-all group">
                    <div class="flex items-center gap-1.5 text-blue-400 text-xs font-bold truncate">
                        <i class="fa-solid fa-user-shield text-[10px]"></i>
                        <span>Staff Pemda</span>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">NIP / Admin</p>
                </button>

                <button type="button" onclick="fillCredentials('kades_sukamaju', 'password', 'Kepala Desa Sukamaju')" class="p-2 rounded-xl bg-emerald-950/60 hover:bg-emerald-900/80 border border-emerald-800/50 text-left transition-all group">
                    <div class="flex items-center gap-1.5 text-emerald-400 text-xs font-bold truncate">
                        <i class="fa-solid fa-house-chimney-window text-[10px]"></i>
                        <span>Kades</span>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">Desa Sukamaju</p>
                </button>

                <button type="button" onclick="fillCredentials('197204181997031002', 'password', 'Bupati / Pimpinan')" class="p-2 rounded-xl bg-purple-950/60 hover:bg-purple-900/80 border border-purple-800/50 text-left transition-all group">
                    <div class="flex items-center gap-1.5 text-purple-400 text-xs font-bold truncate">
                        <i class="fa-solid fa-crown text-[10px]"></i>
                        <span>Pimpinan</span>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">Bupati / Eksekutif</p>
                </button>
            </div>
        </div>

        <!-- Main Login Card -->
        <div class="bg-slate-900/90 backdrop-blur-xl rounded-3xl border border-slate-800 p-6 sm:p-8 shadow-2xl shadow-black/40">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-white">Autentikasi Pengguna</h2>
                <p class="text-xs text-slate-400 mt-1">Masukkan NIP atau Username terdaftar untuk mengakses dashboard.</p>
            </div>

            <!-- Error Alerts -->
            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->has('login_id'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-xmark mt-0.5 shrink-0"></i>
                    <span>{{ $errors->first('login_id') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- NIP or Username Input -->
                <div>
                    <label for="login_id" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        NIP / Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-id-badge text-sm"></i>
                        </div>
                        <input type="text" id="login_id" name="login_id" value="{{ old('login_id', 'admin_pemda') }}" required
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-hidden focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="Contoh: 198503152010011005 atau admin_pemda">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Staff & Pimpinan menggunakan NIP, Kepala Desa menggunakan akun terdaftar.</p>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">
                            Kata Sandi
                        </label>
                        <span class="text-[11px] text-slate-400">Default: password</span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" value="password" required
                               class="w-full pl-10 pr-10 py-2.5 bg-slate-950/70 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-hidden focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300">
                            <i id="password-toggle-icon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Security notice -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-sm bg-slate-800 border-slate-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                        <span class="text-xs text-slate-300">Ingat sesi saya</span>
                    </label>
                    <span class="text-[11px] text-slate-400 flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved text-cyan-400"></i>
                        <span>SSL Terenkripsi</span>
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:from-blue-700 active:to-indigo-700 shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <!-- Footer Notice -->
        <div class="mt-6 text-center text-xs text-slate-400">
            Akses sistem ini dipantau dan diaudit berdasarkan peraturan pemerintah yang berlaku.
        </div>
    </div>

    <script>
        function fillCredentials(id, pass, label) {
            document.getElementById('login_id').value = id;
            document.getElementById('password').value = pass;
        }

        function togglePassword() {
            const passInput = document.getElementById('password');
            const icon = document.getElementById('password-toggle-icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
