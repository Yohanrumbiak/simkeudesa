@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('header_title', 'Pusat Laporan Keuangan Desa')
@section('header_subtitle', 'Penyusunan laporan konsolidasi, realisasi, dan pertanggungjawaban APBDes')

@section('content')
<div class="space-y-6">

    <!-- Filter and Report Type Selection Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('staff.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Laporan Keuangan</label>
                <select name="type" class="w-full text-xs font-semibold py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="consolidated" {{ $reportType == 'consolidated' ? 'selected' : '' }}>1. Laporan Konsolidasi Seluruh Desa</option>
                    <option value="realization" {{ $reportType == 'realization' ? 'selected' : '' }}>2. Laporan Realisasi Anggaran APBDes</option>
                    <option value="receipts" {{ $reportType == 'receipts' ? 'selected' : '' }}>3. Laporan Penerimaan / Pendapatan Kas</option>
                    <option value="expenditures" {{ $reportType == 'expenditures' ? 'selected' : '' }}>4. Laporan Pengeluaran / Belanja</option>
                    <option value="remaining" {{ $reportType == 'remaining' ? 'selected' : '' }}>5. Laporan Sisa Anggaran (SiLPA)</option>
                    <option value="assets" {{ $reportType == 'assets' ? 'selected' : '' }}>6. Laporan Inventaris Aset Desa</option>
                    <option value="apbdes" {{ $reportType == 'apbdes' ? 'selected' : '' }}>7. Laporan Peraturan Desa (APBDes)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Cakupan Desa</label>
                <select name="village_id" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="">Semua Desa (Konsolidasi Kabupaten)</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" {{ $selectedVillageId == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->subdistrict }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Anggaran</label>
                <select name="year" class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="2026" {{ $activeYear == 2026 ? 'selected' : '' }}>T.A. 2026 (Aktif)</option>
                    <option value="2025" {{ $activeYear == 2025 ? 'selected' : '' }}>T.A. 2025 (Tutup Buku)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Tampilkan Laporan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Report Preview Paper Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Report Header Bar with Export Buttons -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/60">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Preview Dokumen Laporan</span>
                <h3 class="text-base sm:text-lg font-bold text-slate-800">
                    {{ strtoupper(str_replace('_', ' ', $reportType)) }} - T.A. {{ $activeYear }}
                </h3>
                <p class="text-xs text-slate-500">
                    {{ $selectedVillageId ? 'Desa ' . $villages->firstWhere('id', $selectedVillageId)?->name : 'Konsolidasi Seluruh Desa se-Kabupaten' }}
                </p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('staff.reports.print', ['type' => $reportType, 'village_id' => $selectedVillageId, 'year' => $activeYear]) }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Resmi / PDF</span>
                </a>
                <a href="{{ route('staff.reports.excel', ['type' => $reportType, 'village_id' => $selectedVillageId, 'year' => $activeYear]) }}"
                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Unduh Excel (CSV)</span>
                </a>
            </div>
        </div>

        <!-- Report Table Output by Type -->
        <div class="p-5 sm:p-6 overflow-x-auto">
            @if(in_array($reportType, ['consolidated', 'realization', 'remaining']))
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-300">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Kode Desa</th>
                            <th class="py-3 px-4">Nama Desa</th>
                            <th class="py-3 px-4">Kecamatan</th>
                            <th class="py-3 px-4 text-right">Anggaran Belanja (Rp)</th>
                            <th class="py-3 px-4 text-right">Realisasi Belanja (Rp)</th>
                            <th class="py-3 px-4 text-right">Sisa Anggaran (Rp)</th>
                            <th class="py-3 px-4 text-center">Penyerapan (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reportData['rows'] as $i => $r)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 text-slate-400">{{ $i + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-slate-500">{{ $r['village']->code }}</td>
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $r['village']->name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $r['village']->subdistrict }}</td>
                                <td class="py-3 px-4 text-right font-medium text-slate-700">Rp {{ number_format($r['budget'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700">Rp {{ number_format($r['realized'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-medium text-blue-700">Rp {{ number_format($r['remaining'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center font-bold text-slate-800">{{ $r['percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100 font-bold border-t-2 border-slate-300 text-slate-900">
                            <td colspan="4" class="py-3.5 px-4 text-right">TOTAL KESELURUHAN:</td>
                            <td class="py-3.5 px-4 text-right">Rp {{ number_format($reportData['totals']['budget'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-emerald-800">Rp {{ number_format($reportData['totals']['realized'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-blue-800">Rp {{ number_format($reportData['totals']['remaining'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">{{ $reportData['totals']['percentage'] }}%</td>
                        </tr>
                    </tfoot>
                </table>

            @elseif($reportType === 'receipts')
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-300">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">No Transaksi</th>
                            <th class="py-3 px-4">Desa</th>
                            <th class="py-3 px-4">Sumber Pendapatan</th>
                            <th class="py-3 px-4">Uraian</th>
                            <th class="py-3 px-4 text-right">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reportData['rows'] as $i => $rec)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 text-slate-400">{{ $i + 1 }}</td>
                                <td class="py-3 px-4">{{ $rec->date->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-mono">{{ $rec->transaction_number }}</td>
                                <td class="py-3 px-4 font-bold">{{ $rec->village->name }}</td>
                                <td class="py-3 px-4">{{ $rec->funding_source }}</td>
                                <td class="py-3 px-4 max-w-xs">{{ $rec->description }}</td>
                                <td class="py-3 px-4 text-right font-extrabold text-emerald-700">Rp {{ number_format($rec->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100 font-bold border-t-2 border-slate-300">
                            <td colspan="6" class="py-3.5 px-4 text-right">TOTAL PENERIMAAN:</td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-emerald-800">Rp {{ number_format($reportData['total_amount'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

            @elseif($reportType === 'expenditures')
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-300">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">No Bukti</th>
                            <th class="py-3 px-4">Desa</th>
                            <th class="py-3 px-4">Bidang APBDes</th>
                            <th class="py-3 px-4">Kegiatan & Uraian</th>
                            <th class="py-3 px-4">Penerima</th>
                            <th class="py-3 px-4 text-right">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reportData['rows'] as $i => $exp)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 text-slate-400">{{ $i + 1 }}</td>
                                <td class="py-3 px-4">{{ $exp->date->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-mono">{{ $exp->transaction_number }}</td>
                                <td class="py-3 px-4 font-bold">{{ $exp->village->name }}</td>
                                <td class="py-3 px-4">{{ $exp->sector->name }}</td>
                                <td class="py-3 px-4 max-w-xs">{{ $exp->activity_name }} &bull; {{ $exp->description }}</td>
                                <td class="py-3 px-4">{{ $exp->payee }}</td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100 font-bold border-t-2 border-slate-300">
                            <td colspan="7" class="py-3.5 px-4 text-right">TOTAL PENGELUARAN BELANJA:</td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">Rp {{ number_format($reportData['total_amount'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

            @elseif($reportType === 'assets')
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-300">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Kode Aset</th>
                            <th class="py-3 px-4">Desa</th>
                            <th class="py-3 px-4">Nama Barang</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Tahun</th>
                            <th class="py-3 px-4 text-right">Nilai Perolehan (Rp)</th>
                            <th class="py-3 px-4 text-center">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reportData['rows'] as $i => $ast)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 text-slate-400">{{ $i + 1 }}</td>
                                <td class="py-3 px-4 font-mono">{{ $ast->asset_code }}</td>
                                <td class="py-3 px-4 font-bold">{{ $ast->village->name }}</td>
                                <td class="py-3 px-4 font-semibold">{{ $ast->name }}</td>
                                <td class="py-3 px-4">{{ $ast->category }}</td>
                                <td class="py-3 px-4">{{ $ast->acquisition_year }}</td>
                                <td class="py-3 px-4 text-right font-bold text-blue-700">Rp {{ number_format($ast->acquisition_value, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $ast->condition_badge }}">
                                        {{ $ast->condition_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-100 font-bold border-t-2 border-slate-300">
                            <td colspan="6" class="py-3.5 px-4 text-right">TOTAL NILAI INVENTARIS:</td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-blue-800">Rp {{ number_format($reportData['total_amount'], 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
