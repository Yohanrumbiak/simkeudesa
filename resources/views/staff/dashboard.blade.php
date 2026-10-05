@extends('layouts.app')

@section('title', 'Dashboard Staff Pemda')
@section('header_title', 'Dashboard Monitoring Keuangan Desa')
@section('header_subtitle', 'Ringkasan Eksekutif Pengelolaan APBDes se-Kabupaten Bandung Barat')

@section('content')
<div class="space-y-6">

    <!-- Top Info / Alert Bar -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-80 bg-white/5 transform skew-x-12 pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/30 text-blue-200 text-xs font-semibold uppercase tracking-wider border border-blue-400/20">Portal Administrator</span>
                    <span class="text-xs text-blue-200">&bull; Tahun Anggaran {{ $activeYear }}</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold mt-1">Selamat Bertugas, {{ auth()->user()->name }}</h3>
                <p class="text-xs sm:text-sm text-blue-100/80 mt-0.5">Memantau {{ $villages->count() }} desa terdaftar. Terdapat {{ $pendingDisbursements->count() }} permohonan pencairan dana menunggu verifikasi Anda.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('staff.disbursements.index') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Tinjau Dokumen ({{ $pendingDisbursements->count() }})</span>
                </a>
                <a href="{{ route('staff.reports.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition border border-white/20 flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Laporan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Main Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

        <!-- Card 1: Total APBDes Budget -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Anggaran APBDes</p>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1.5 tracking-tight">
                        Rp {{ number_format($totalBudget, 0, ',', '.') }}
                    </h3>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-slate-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                            {{ $villages->count() }} Desa
                        </span>
                        <span>Alokasi Belanja Induk</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full w-full"></div>
            </div>
        </div>

        <!-- Card 2: Total Realization -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Realisasi Anggaran</p>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1.5 tracking-tight">
                        Rp {{ number_format($totalRealization, 0, ',', '.') }}
                    </h3>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-slate-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $overallAbsorption }}% Terserap
                        </span>
                        <span>Sisa: Rp {{ number_format($remainingBudget / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-chart-line-up"></i>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(100, $overallAbsorption) }}%"></div>
            </div>
        </div>

        <!-- Card 3: Total Receipts -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Penerimaan (Kas)</p>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1.5 tracking-tight">
                        Rp {{ number_format($totalReceipts, 0, ',', '.') }}
                    </h3>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-slate-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-cyan-100 text-cyan-800">
                            Masuk Kas Desa
                        </span>
                        <span>DD, ADD, PADes</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-arrow-down-left"></i>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-cyan-500 h-full rounded-full" style="width: {{ $totalBudget > 0 ? min(100, round(($totalReceipts / $totalBudget) * 100)) : 0 }}%"></div>
            </div>
        </div>

        <!-- Card 4: Total Expenditures -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pengeluaran (Belanja)</p>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1.5 tracking-tight">
                        Rp {{ number_format($totalExpenditures, 0, ',', '.') }}
                    </h3>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-slate-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                            SPP Terverifikasi
                        </span>
                        <span>5 Bidang APBDes</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-arrow-up-right"></i>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-rose-500 h-full rounded-full" style="width: {{ min(100, $overallAbsorption) }}%"></div>
            </div>
        </div>

    </div>

    <!-- Charts Section: Bar Chart & Doughnut Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Bar Chart: Budget vs Realization per Village (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-blue-600"></i>
                        <span>Perbandingan Anggaran vs Realisasi per Desa</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Komparasi alokasi APBDes dengan penyerapan belanja actual</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">
                    T.A. {{ $activeYear }}
                </span>
            </div>
            <div class="h-72 sm:h-80 w-full relative">
                <canvas id="villageRealizationChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Expenditure Composition by 5 Sectors (1 col) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-cyan-600"></i>
                            <span>Komposisi Belanja</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Proporsi pengeluaran berdasarkan 5 bidang APBDes</p>
                    </div>
                </div>
                <div class="h-56 relative flex items-center justify-center">
                    <canvas id="sectorCompositionChart"></canvas>
                </div>
            </div>

            <!-- Legend summary list -->
            <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                @foreach($sectorChartLabels as $idx => $label)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $sectorChartColors[$idx] }}"></span>
                            <span class="text-slate-600 truncate">{{ $label }}</span>
                        </div>
                        <span class="font-bold text-slate-800 shrink-0">
                            Rp {{ number_format($sectorChartValues[$idx] / 1000000, 1, ',', '.') }} Jt
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Bottom Section: Recent Transactions & Information Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Transactions Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                        <span>Transaksi Terakhir se-Kabupaten</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Catatan penerimaan dan pengeluaran kas yang baru tercatat</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('staff.receipts.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat Semua &rarr;</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                            <th class="py-3 px-4">Tanggal / No Bukti</th>
                            <th class="py-3 px-4">Desa</th>
                            <th class="py-3 px-4">Uraian Transaksi</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-700 block">{{ \Carbon\Carbon::parse($tx['date'])->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $tx['transaction_number'] }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-medium text-slate-800 block truncate max-w-[130px]">{{ $tx['village_name'] }}</span>
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $tx['type_badge'] }}">
                                        {{ $tx['type'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-medium text-slate-700 truncate max-w-xs">{{ $tx['description'] }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $tx['category'] }}</span>
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold {{ $tx['amount_sign'] === '+' ? 'text-emerald-600' : 'text-slate-800' }}">
                                    {{ $tx['amount_sign'] }} Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx['status_badge'] }}">
                                        {{ ucfirst($tx['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">Belum ada transaksi tercatat untuk tahun ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Information and Notifications Panel (1 col) -->
        <div class="space-y-6">

            <!-- Disbursement Requests Pending Review -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-amber-500"></i>
                        <span>Verifikasi Pencairan Dana</span>
                    </h4>
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                        {{ $pendingDisbursements->count() }} Menunggu
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingDisbursements as $p)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 hover:border-blue-300 transition-colors">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-slate-800 truncate">{{ $p->village->name }}</h5>
                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $p->title }}</p>
                                    <div class="text-[11px] font-bold text-blue-600 mt-1">
                                        Rp {{ number_format($p->requested_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <a href="{{ route('staff.disbursements.show', $p->id) }}" class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-semibold shrink-0">
                                    Periksa
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-xl">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-base mb-1 block"></i>
                            Tidak ada berkas menunggu verifikasi.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Activity Schedules & Reporting Deadlines -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-blue-600"></i>
                        <span>Jadwal & Batas Pelaporan</span>
                    </h4>
                    <span class="text-[10px] text-slate-400 font-medium">Agenda DPMD</span>
                </div>

                <div class="space-y-3">
                    @foreach($schedules as $sched)
                        <div class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex flex-col items-center justify-center shrink-0 border border-blue-100">
                                <span class="text-[10px] uppercase font-bold leading-none">{{ $sched->deadline_date->format('M') }}</span>
                                <span class="text-sm font-extrabold leading-none mt-0.5">{{ $sched->deadline_date->format('d') }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h5 class="text-xs font-bold text-slate-800 truncate">{{ $sched->title }}</h5>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{{ $sched->description }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $sched->days_remaining <= 15 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $sched->days_remaining >= 0 ? $sched->days_remaining . ' hari lagi' : 'Selesai' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $sched->category }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Village Realization Bar Chart
        const villageCtx = document.getElementById('villageRealizationChart').getContext('2d');
        new Chart(villageCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($villageChartLabels) !!},
                datasets: [
                    {
                        label: 'Anggaran Belanja APBDes (Rp)',
                        data: {!! json_encode($villageBudgetValues) !!},
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                    },
                    {
                        label: 'Realisasi Belanja (Rp)',
                        data: {!! json_encode($villageRealizationValues) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 10, weight: 'bold' }
                        }
                    }
                }
            }
        });

        // 2. Expenditure Composition Doughnut Chart
        const sectorCtx = document.getElementById('sectorCompositionChart').getContext('2d');
        new Chart(sectorCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($sectorChartLabels) !!},
                datasets: [{
                    data: {!! json_encode($sectorChartValues) !!},
                    backgroundColor: {!! json_encode($sectorChartColors) !!},
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
