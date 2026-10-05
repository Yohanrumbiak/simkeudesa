@extends('layouts.app')

@section('title', 'Realisasi Anggaran')
@section('header_title', 'Monitoring Realisasi Anggaran APBDes')
@section('header_subtitle', 'Analisis komparatif penyerapan belanja desa terhadap plafon anggaran')

@section('content')
<div class="space-y-6">

    <!-- Grand Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Total Plafon Anggaran</span>
            <span class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1 block">
                Rp {{ number_format($grandTotalBudget, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400 mt-1 block">T.A. {{ $activeYear }}</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Total Realisasi Belanja</span>
            <span class="text-xl sm:text-2xl font-extrabold text-emerald-700 mt-1 block">
                Rp {{ number_format($grandTotalRealized, 0, ',', '.') }}
            </span>
            <span class="text-xs font-bold text-emerald-600 mt-1 block">
                <i class="fa-solid fa-arrow-trend-up mr-1"></i>{{ $grandPercentage }}% Terserap
            </span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Sisa Anggaran Belanja</span>
            <span class="text-xl sm:text-2xl font-extrabold text-blue-700 mt-1 block">
                Rp {{ number_format($grandRemaining, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400 mt-1 block">Belum dibelanjakan</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Rata-rata Penyerapan</span>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-2xl font-extrabold {{ $grandPercentage >= 50 ? 'text-emerald-700' : 'text-amber-600' }}">
                    {{ $grandPercentage }}%
                </span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $grandPercentage >= 50 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $grandPercentage >= 50 ? 'Kategori Baik' : 'Perlu Dorongan' }}
                </span>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(100, $grandPercentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('staff.realization.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Desa</label>
                <select name="village_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Desa</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" {{ request('village_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Bidang Anggaran</label>
                <select name="sector_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Bidang</option>
                    @foreach($sectors as $s)
                        <option value="{{ $s->id }}" {{ request('sector_id') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('staff.realization.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Realization per Village -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-table-list text-blue-600"></i>
                <span>Tabel Realisasi Belanja Desa Tahun Anggaran {{ $activeYear }}</span>
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Kode & Nama Desa</th>
                        <th class="py-3.5 px-4">Kecamatan</th>
                        <th class="py-3.5 px-4 text-right">Plafon Anggaran (Rp)</th>
                        <th class="py-3.5 px-4 text-right">Realisasi Belanja (Rp)</th>
                        <th class="py-3.5 px-4 text-right">Sisa Anggaran (Rp)</th>
                        <th class="py-3.5 px-4 text-center">Persentase</th>
                        <th class="py-3.5 px-4">Progres Penyerapan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($realizationData as $idx => $r)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4 text-slate-400 font-semibold">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $r['village']->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $r['village']->code }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $r['village']->subdistrict }}</td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-700">
                                Rp {{ number_format($r['budget'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-emerald-700">
                                Rp {{ number_format($r['realized'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-600">
                                Rp {{ number_format($r['remaining'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold {{ $r['percentage'] >= 60 ? 'bg-emerald-100 text-emerald-800' : ($r['percentage'] >= 40 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $r['percentage'] }}%
                                </span>
                            </td>
                            <td class="py-3.5 px-4 w-44">
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $r['percentage'] >= 60 ? 'bg-emerald-500' : ($r['percentage'] >= 40 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ min(100, $r['percentage']) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                        <td colspan="3" class="py-3 px-4 text-right">TOTAL KESELURUHAN:</td>
                        <td class="py-3 px-4 text-right text-slate-800">Rp {{ number_format($grandTotalBudget, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right text-emerald-800">Rp {{ number_format($grandTotalRealized, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right text-slate-800">Rp {{ number_format($grandRemaining, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center text-blue-800">{{ $grandPercentage }}%</td>
                        <td class="py-3 px-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
