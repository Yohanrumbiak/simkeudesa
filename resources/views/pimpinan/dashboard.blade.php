@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Pimpinan</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $institution?->name ?? 'Lembaga' }} - Tahun Anggaran {{ $activeYear }}
            </p>
        </div>

        <!-- Filter Tahun -->
        <form method="GET" action="{{ route('pimpinan.dashboard') }}" class="flex items-center gap-2">
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-700">
                @foreach(range(date('Y'), date('Y') - 4) as $year)
                    <option value="{{ $year }}" {{ $activeYear == $year ? 'selected' : '' }}>
                        Tahun {{ $year }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- 4 Ringkasan Utama (Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Anggaran -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Anggaran (APBDes)</p>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-2">
                Rp {{ number_format($totalBudget, 0, ',', '.') }}
            </h3>
        </div>

        <!-- Total Penerimaan -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Penerimaan</p>
            <h3 class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                Rp {{ number_format($totalReceipts, 0, ',', '.') }}
            </h3>
        </div>

        <!-- Total Realisasi / Pengeluaran -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Realisasi Belanja</p>
            <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-2">
                Rp {{ number_format($totalRealization, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Serapan: <strong>{{ $overallAbsorption }}%</strong></p>
        </div>

        <!-- Sisa Anggaran -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sisa Anggaran</p>
            <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">
                Rp {{ number_format($remainingBudget, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <!-- Informasi / Notifikasi Strategis -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg text-amber-800 dark:text-amber-300">
            <span class="font-semibold">Pencairan Menunggu Verifikasi:</span> {{ $pendingDisbursementsCount }} pengajuan
        </div>
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-blue-800 dark:text-blue-300">
            <span class="font-semibold">Pengajuan Perlu Revisi:</span> {{ $revisionCount }} pengajuan
        </div>
    </div>

    <!-- Peringkat Realisasi Desa -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Peringkat Realisasi Anggaran Desa</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Desa</th>
                        <th class="px-4 py-3">Anggaran</th>
                        <th class="px-4 py-3">Realisasi</th>
                        <th class="px-4 py-3">Sisa</th>
                        <th class="px-4 py-3">Persentase</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villageRankings as $rank)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $rank['village']->name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($rank['budget'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($rank['realization'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($rank['remaining'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $rank['rate'] }}%</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $rank['badge'] }}">
                                    {{ $rank['status_label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center">Belum ada data desa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaksi Terakhir & Jadwal Kegiatan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaksi Terakhir -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Transaksi Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-2">Tanggal</th>
                            <th class="px-4 py-2">No. Transaksi</th>
                            <th class="px-4 py-2">Desa</th>
                            <th class="px-4 py-2">Tipe</th>
                            <th class="px-4 py-2">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2">{{ $tx['date'] }}</td>
                                <td class="px-4 py-2 font-mono text-xs">{{ $tx['transaction_number'] }}</td>
                                <td class="px-4 py-2">{{ $tx['village_name'] }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 text-xs rounded font-medium {{ $tx['type_badge'] }}">
                                        {{ $tx['type'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-medium">Rp {{ number_format($tx['amount'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Jadwal Kegiatan -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Jadwal Kegiatan</h2>
            <ul class="space-y-3">
                @forelse($schedules as $s)
                    <li class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $s->title ?? $s->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Batas Waktu: {{ $s->deadline_date }}</p>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">Tidak ada agenda kegiatan.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection