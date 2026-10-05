@extends('layouts.app')

@section('title', 'Rincian APBDes ' . $apbdes->village->name)
@section('header_title', 'Rincian Anggaran Pendapatan dan Belanja Desa')
@section('header_subtitle', $apbdes->title . ' - ' . $apbdes->village->name)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('staff.apbdes.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Master APBDes</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500">Status Evaluasi:</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $apbdes->status_badge }}">
                {{ ucfirst($apbdes->status) }}
            </span>
        </div>
    </div>

    <!-- Header Details Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Peraturan Desa (Perdes)</span>
                <h3 class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $apbdes->title }}</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $apbdes->document_number ?? 'Belum ada nomor Perdes' }} &bull; Desa {{ $apbdes->village->name }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($apbdes->status !== 'disetujui')
                    <form method="POST" action="{{ route('staff.apbdes.status', $apbdes->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="disetujui">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Setujui APBDes</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('staff.reports.print', ['type' => 'apbdes', 'village_id' => $apbdes->village_id]) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Lembaran</span>
                </a>
            </div>
        </div>

        <!-- 3 Summary Numbers -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">
            <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200/70">
                <span class="text-xs font-semibold text-emerald-800 block">Total Pendapatan Desa (Akun 4)</span>
                <span class="text-xl font-extrabold text-emerald-700 mt-1 block">
                    Rp {{ number_format($apbdes->total_revenue_budget, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200/70">
                <span class="text-xs font-semibold text-blue-800 block">Total Belanja Desa (Akun 5)</span>
                <span class="text-xl font-extrabold text-blue-700 mt-1 block">
                    Rp {{ number_format($apbdes->total_expenditure_budget, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-xs font-semibold text-slate-700 block">Surplus / (Defisit) Anggaran</span>
                @php $surplus = $apbdes->total_revenue_budget - $apbdes->total_expenditure_budget; @endphp
                <span class="text-xl font-extrabold {{ $surplus >= 0 ? 'text-slate-800' : 'text-rose-600' }} mt-1 block">
                    Rp {{ number_format($surplus, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Section 1: Revenue Details (Pendapatan Desa) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-arrow-down text-emerald-600"></i>
                <span>Rincian Pendapatan Desa (Kelompok Akun 4)</span>
            </h4>
            <span class="text-xs font-bold text-emerald-700">Total: Rp {{ number_format($apbdes->total_revenue_budget, 0, ',', '.') }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                        <th class="py-3 px-4">Kode Rekening</th>
                        <th class="py-3 px-4">Uraian Sumber Pendapatan</th>
                        <th class="py-3 px-4 text-right">Anggaran Murni (Rp)</th>
                        <th class="py-3 px-4 text-right">Anggaran Perubahan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($revenueItems as $rev)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ $rev->account_code }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $rev->activity_name }}</td>
                            <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($rev->original_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">Rp {{ number_format($rev->revised_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400">Belum ada item rincian pendapatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Expenditure Details by 5 Sectors (Belanja Desa) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-arrow-up text-rose-600"></i>
                <span>Rincian Belanja Desa per 5 Bidang (Kelompok Akun 5)</span>
            </h4>
            <span class="text-xs font-bold text-blue-700">Total: Rp {{ number_format($apbdes->total_expenditure_budget, 0, ',', '.') }}</span>
        </div>

        <div class="p-5 space-y-6">
            @foreach($sectors as $sec)
                @php
                    $itemsInSector = $expenditureItems->get($sec->id, collect());
                    $sectorSum = $itemsInSector->sum('revised_amount');
                @endphp
                <div class="border border-slate-200/80 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 flex items-center justify-between border-b border-slate-200/80">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $sec->color }}"></span>
                            <span class="font-bold text-xs text-slate-800">{{ $sec->code }} - {{ $sec->name }}</span>
                        </div>
                        <span class="font-bold text-xs text-slate-700">
                            Subtotal: Rp {{ number_format($sectorSum, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-white text-[11px] font-semibold text-slate-400 border-b border-slate-100">
                                    <th class="py-2.5 px-4">Kode Akun</th>
                                    <th class="py-2.5 px-4">Nama Kegiatan / Uraian</th>
                                    <th class="py-2.5 px-4 text-right">Anggaran Murni</th>
                                    <th class="py-2.5 px-4 text-right">Anggaran Perubahan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($itemsInSector as $item)
                                    <tr class="hover:bg-slate-50/40">
                                        <td class="py-2.5 px-4 font-mono font-medium text-slate-500">{{ $item->account_code }}</td>
                                        <td class="py-2.5 px-4 text-slate-700 font-medium">{{ $item->activity_name }}</td>
                                        <td class="py-2.5 px-4 text-right text-slate-500">Rp {{ number_format($item->original_amount, 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-4 text-right font-bold text-slate-800">Rp {{ number_format($item->revised_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-3 px-4 text-slate-400 text-center italic">Tidak ada alokasi anggaran belanja pada bidang ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
