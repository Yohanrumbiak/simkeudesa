<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIMKeuDesa') }} - Dashboard Desa {{ $village->name ?? '' }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen antialiased">

    <!-- Container Utama -->
    <div class="p-6 space-y-6 max-w-7xl mx-auto">
        
        <!-- Header Dashboard Desa -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Kepala Desa</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Desa {{ $village->name ?? 'Unggul' }} — Tahun Anggaran {{ $activeYear }}
                </p>
            </div>

            <!-- Filter Tahun Anggaran & Logout -->
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <select name="year" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-700 text-sm focus:outline-none">
                        @foreach(range(date('Y'), date('Y') - 4) as $y)
                            <option value="{{ $y }}" {{ $activeYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition shadow-sm">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <!-- Alert Jika Ada Revisi Pencairan -->
        @if($revisionAlerts->count() > 0)
            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg text-amber-800 dark:text-amber-300 flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                <p class="text-sm font-medium">
                    Terdapat <span class="font-bold">{{ $revisionAlerts->count() }}</span> pengajuan pencairan anggaran yang memerlukan revisi dokumen.
                </p>
            </div>
        @endif

        <!-- 4 Kartu Ringkasan Utama (Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Anggaran -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pagu Anggaran (APBDes)</p>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-2">
                    Rp {{ number_format($totalBudget, 0, ',', '.') }}
                </h3>
            </div>

            <!-- Card 2: Total Penerimaan -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Penerimaan</p>
                <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                    Rp {{ number_format($totalReceipts, 0, ',', '.') }}
                </h3>
            </div>

            <!-- Card 3: Realisasi Belanja -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Realisasi Belanja</p>
                <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-2">
                    Rp {{ number_format($totalExpenditures, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Serapan: <strong>{{ $absorptionRate }}%</strong></p>
            </div>

            <!-- Card 4: Sisa Anggaran -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sisa Anggaran</p>
                <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">
                    Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <!-- Tabel Transaksi Terbaru -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Transaksi Terkini Desa</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">No. Transaksi</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($tx['date'])->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">{{ $tx['transaction_number'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded font-medium {{ $tx['type_badge'] }}">
                                        {{ $tx['type'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $tx['category'] }}</td>
                                <td class="px-4 py-3">{{ $tx['description'] ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-right text-gray-900 dark:text-white">
                                    {{ $tx['amount_sign'] }} Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada transaksi pada tahun anggaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                </table>
            </div>
        </div>

    </div>

</body>
</html>