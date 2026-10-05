@extends('layouts.app')

@section('title', 'Penerimaan Desa')
@section('header_title', 'Penerimaan dan Pendapatan Kas Desa')
@section('header_subtitle', 'Pencatatan dan monitoring seluruh sumber pendapatan desa')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Summary -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs text-slate-400">Total Penerimaan Terverifikasi (T.A. {{ $activeYear }}):</span>
            <h3 class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h3>
        </div>
        <a href="{{ route('staff.receipts.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            <span>Catat Penerimaan Baru</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('staff.receipts.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
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
                <label class="block text-xs font-semibold text-slate-600 mb-1">Sumber Dana</label>
                <select name="funding_source" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Sumber Dana</option>
                    @foreach($fundingSources as $fs)
                        <option value="{{ $fs }}" {{ request('funding_source') == $fs ? 'selected' : '' }}>{{ $fs }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari No Bukti / Uraian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci pencarian..."
                       class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('staff.receipts.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Receipts Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                        <th class="py-3.5 px-4">Tanggal / No Bukti</th>
                        <th class="py-3.5 px-4">Desa</th>
                        <th class="py-3.5 px-4">Sumber Pendapatan</th>
                        <th class="py-3.5 px-4">Uraian Penerimaan</th>
                        <th class="py-3.5 px-4 text-right">Nominal (Rp)</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receipts as $r)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-700 block">{{ $r->date->format('d M Y') }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $r->transaction_number }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $r->village->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $r->payer ?? 'Penyetor Kas' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-100 text-cyan-800">
                                    {{ $r->funding_source }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-slate-700 font-medium max-w-sm">{{ $r->description }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-emerald-600">
                                + Rp {{ number_format($r->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $r->status_badge }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('staff.receipts.edit', $r->id) }}" class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('staff.receipts.destroy', $r->id) }}" onsubmit="return confirm('Hapus transaksi penerimaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">Belum ada transaksi penerimaan yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($receipts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
