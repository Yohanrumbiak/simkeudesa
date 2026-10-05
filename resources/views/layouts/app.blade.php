<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIMKeuDesa</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS CDN with custom config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            navy: '#0b132b',
                            deep: '#1c2541',
                            slate: '#3a506b',
                            primary: '#2563eb',
                            accent: '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #0b132b;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #1e293b;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex overflow-hidden text-slate-800 antialiased">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Navigation Sidebar (Dark Blue) -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0b132b] text-slate-300 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:inset-auto">
        <!-- Institution Logo & Brand -->
        <div class="p-5 border-b border-slate-800/80 bg-[#090f23]">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white shadow-lg shadow-blue-500/20 ring-2 ring-blue-400/30">
                    <i class="fa-solid fa-landmark text-xl"></i>
                </div>
                <div class="overflow-hidden">
                    <h1 class="font-extrabold text-lg text-white tracking-tight flex items-center gap-2">
                        SIMKeuDesa
                        <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30">v2.6</span>
                    </h1>
                    <p class="text-xs text-slate-400 truncate">Sistem Keuangan Desa</p>
                </div>
            </div>

            <!-- Identity Banner -->
            <div class="mt-4 p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/50 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-md bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[11px] font-semibold text-white truncate">
                        @if(auth()->user()->isVillageHead() && auth()->user()->village)
                            {{ auth()->user()->village->name }}
                        @else
                            Kab. Bandung Barat
                        @endif
                    </div>
                    <div class="text-[10px] text-slate-400 truncate">
                        @if(auth()->user()->isVillageHead() && auth()->user()->village)
                            {{ auth()->user()->village->subdistrict }}
                        @else
                            Dinas PMD Kabupaten
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>

            @if(auth()->user()->isAdmin())
                <!-- Staff Pemda / Admin Navigation -->
                <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-sm {{ request()->routeIs('staff.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Dashboard Staff</span>
                </a>

                <a href="{{ route('staff.disbursements.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.disbursements.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-file-circle-check w-5 text-center text-sm {{ request()->routeIs('staff.disbursements.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Verifikasi Dokumen</span>
                    </div>
                    @php $pendingCount = \App\Models\DisbursementApplication::where('status', 'menunggu_verifikasi')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-500 text-white animate-pulse">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('staff.apbdes.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.apbdes.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-book-bookmark w-5 text-center text-sm {{ request()->routeIs('staff.apbdes.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Data APBDes</span>
                </a>

                <a href="{{ route('staff.realization.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.realization.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center text-sm {{ request()->routeIs('staff.realization.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Realisasi Anggaran</span>
                </a>

                <div class="pt-4 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pembukuan Keuangan</div>

                <a href="{{ route('staff.receipts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.receipts.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-arrow-down-left w-5 text-center text-sm {{ request()->routeIs('staff.receipts.*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span>Penerimaan (Pendapatan)</span>
                </a>

                <a href="{{ route('staff.expenditures.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.expenditures.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-arrow-up-right w-5 text-center text-sm {{ request()->routeIs('staff.expenditures.*') ? 'text-white' : 'text-rose-400' }}"></i>
                    <span>Pengeluaran (Belanja)</span>
                </a>

                <a href="{{ route('staff.assets.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.assets.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center text-sm {{ request()->routeIs('staff.assets.*') ? 'text-white' : 'text-amber-400' }}"></i>
                    <span>Aset & Inventaris Desa</span>
                </a>

                <a href="{{ route('staff.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.reports.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-sm {{ request()->routeIs('staff.reports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Laporan Keuangan</span>
                </a>

                <div class="pt-4 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Konfigurasi Sistem</div>

                <a href="{{ route('staff.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center text-sm {{ request()->routeIs('staff.users.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Manajemen Pengguna</span>
                </a>

                <a href="{{ route('staff.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('staff.settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-sm {{ request()->routeIs('staff.settings.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Pengaturan Sistem</span>
                </a>

            @elseif(auth()->user()->isVillageHead())
                <!-- Village Head (Kepala Desa) Navigation -->
                <a href="{{ route('desa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-sm {{ request()->routeIs('desa.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Dashboard Desa</span>
                </a>

                <a href="{{ route('desa.disbursements.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.disbursements.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-cloud-arrow-up w-5 text-center text-sm {{ request()->routeIs('desa.disbursements.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Pencairan Dana</span>
                    </div>
                    @php 
                        $vRevCount = \App\Models\DisbursementApplication::where('village_id', auth()->user()->village_id)->where('status', 'perlu_revisi')->count(); 
                    @endphp
                    @if($vRevCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-500 text-white animate-pulse">{{ $vRevCount }} Revisi</span>
                    @endif
                </a>

                <a href="{{ route('desa.apbdes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.apbdes') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-book-bookmark w-5 text-center text-sm {{ request()->routeIs('desa.apbdes') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>APBDes Desa</span>
                </a>

                <div class="pt-4 pb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Transaksi Desa</div>

                <a href="{{ route('desa.receipts') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.receipts*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-arrow-down-left w-5 text-center text-sm {{ request()->routeIs('desa.receipts*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span>Penerimaan Kas</span>
                </a>

                <a href="{{ route('desa.expenditures') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.expenditures*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-arrow-up-right w-5 text-center text-sm {{ request()->routeIs('desa.expenditures*') ? 'text-white' : 'text-rose-400' }}"></i>
                    <span>Belanja / Pengeluaran</span>
                </a>

                <a href="{{ route('desa.assets.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.assets.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center text-sm {{ request()->routeIs('desa.assets.*') ? 'text-white' : 'text-amber-400' }}"></i>
                    <span>Inventaris Aset Desa</span>
                </a>

                <a href="{{ route('desa.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.reports.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-sm {{ request()->routeIs('desa.reports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Laporan Desa</span>
                </a>

                <a href="{{ route('desa.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('desa.profile') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-user-shield w-5 text-center text-sm {{ request()->routeIs('desa.profile') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Profil & Pengaturan Akun</span>
                </a>

            @elseif(auth()->user()->isLeader())
                <!-- Regional Government Leaders (Pimpinan Pemda) Navigation -->
                <a href="{{ route('pimpinan.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pimpinan.dashboard') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-sm {{ request()->routeIs('pimpinan.dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Dashboard Eksekutif</span>
                </a>

                <a href="{{ route('pimpinan.monitoring') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pimpinan.monitoring') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-chart-column w-5 text-center text-sm {{ request()->routeIs('pimpinan.monitoring') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Monitoring Realisasi Desa</span>
                </a>

                <a href="{{ route('pimpinan.transactions') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pimpinan.transactions') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-receipt w-5 text-center text-sm {{ request()->routeIs('pimpinan.transactions') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Rincian Transaksi Desa</span>
                </a>

                <a href="{{ route('pimpinan.disbursements.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pimpinan.disbursements.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-file-shield w-5 text-center text-sm {{ request()->routeIs('pimpinan.disbursements.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Pantau Penyaluran Dana</span>
                </a>

                <a href="{{ route('pimpinan.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('pimpinan.reports.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="fa-solid fa-file-contract w-5 text-center text-sm {{ request()->routeIs('pimpinan.reports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Laporan Konsolidasi</span>
                </a>
            @endif
        </nav>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-slate-800 bg-[#090f23]">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-sm ring-2 ring-slate-600">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block text-[10px] font-medium px-2 py-0.5 rounded-full {{ auth()->user()->role_badge_class }}">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors border border-red-500/20">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100/60">

        <!-- Top Header -->
        <header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between shadow-xs sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button type="button" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-hidden">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-800 tracking-tight leading-tight">
                        @yield('header_title', 'Dashboard')
                    </h2>
                    <p class="text-xs text-slate-400 hidden sm:block">
                        @yield('header_subtitle', 'Sistem Informasi Pengelolaan Keuangan Desa')
                    </p>
                </div>
            </div>

            <!-- Header Right Stats / Date & User info -->
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Fiscal Year Badge -->
                <div class="hidden md:flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold">
                    <i class="fa-regular fa-calendar-check text-blue-600"></i>
                    <span>T.A. 2026</span>
                </div>

                <!-- Live Date & Clock -->
                <div class="hidden lg:flex flex-col text-right">
                    <span class="text-xs font-semibold text-slate-700" id="live-date">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <span class="text-[11px] text-slate-400" id="live-clock">WIB</span>
                </div>

                <!-- Notifications Dropdown -->
                <div class="relative" id="notification-dropdown-wrapper">
                    <button type="button" onclick="toggleNotifications()" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                        <i class="fa-regular fa-bell text-lg"></i>
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white animate-pulse"></span>
                    </button>
                    <!-- Notification Panel -->
                    <div id="notification-menu" class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-slate-200 py-3 hidden z-50">
                        <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-800">Pemberitahuan Sistem</h3>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-semibold">Terbaru</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            @php
                                $headerNotifs = \App\Models\Notification::latest()->take(4)->get();
                            @endphp
                            @forelse($headerNotifs as $notif)
                                <div class="p-3 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-2 h-2 mt-1.5 rounded-full {{ $notif->type === 'warning' ? 'bg-amber-500' : ($notif->type === 'success' ? 'bg-emerald-500' : 'bg-blue-500') }} shrink-0"></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-slate-800">{{ $notif->title }}</p>
                                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{{ $notif->message }}</p>
                                            <span class="text-[10px] text-slate-400 mt-1 inline-block">{{ $notif->created_at?->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400">Tidak ada notifikasi baru</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Mini Pill -->
                <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-bold text-slate-800 leading-none truncate max-w-[120px]">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 leading-tight">{{ auth()->user()->role_label }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Message Alerts -->
        @if(session('success'))
            <div class="mx-4 sm:mx-6 mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs sm:text-sm font-medium shadow-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 sm:mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs sm:text-sm font-medium shadow-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 sm:mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium shadow-xs">
                <div class="flex items-center gap-2.5 mb-1.5 font-bold">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                    <span>Terjadi kesalahan pada input:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-4 text-xs text-rose-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Scrollable Body Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 px-6 py-3 text-center text-xs text-slate-400">
            SIMKeuDesa &copy; 2026 Pemerintah Kabupaten Bandung Barat &bull; Dinas Pemberdayaan Masyarakat dan Desa (DPMD)
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function toggleNotifications() {
            const menu = document.getElementById('notification-menu');
            menu.classList.toggle('hidden');
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notification-dropdown-wrapper');
            const menu = document.getElementById('notification-menu');
            if (wrapper && !wrapper.contains(e.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });

        // Realtime digital clock
        function updateClock() {
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @stack('scripts')
</body>
</html>
