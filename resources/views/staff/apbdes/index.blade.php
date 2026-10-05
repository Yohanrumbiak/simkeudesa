@extends('layouts.app')

@section('title', 'Data APBDes')
@section('header_title', 'Anggaran Pendapatan dan Belanja Desa (APBDes)')
@section('header_subtitle', 'Pengelolaan dan evaluasi dokumen APBDes desa se-Kabupaten')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Master Dokumen APBDes</h3>
            <p class="text-xs text-slate-400 mt-0.5">Tahun Anggaran {{ $activeYear }}</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('staff.apbdes.create') }}" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah APBDes Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('staff.apbdes.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
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
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status APBDes</label>
                <select name="status" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Status</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari No Perdes / Judul</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..."
                       class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari Data</span>
                </button>
                <a href="{{ route('staff.apbdes.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table APBDes -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Desa</th>
                        <th class="py-3.5 px-4">No & Judul Perdes</th>
                        <th class="py-3.5 px-4">Jenis</th>
                        <th class="py-3.5 px-4 text-right">Anggaran Pendapatan</th>
                        <th class="py-3.5 px-4 text-right">Anggaran Belanja</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($apbdesList as $idx => $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-slate-400 font-semibold">{{ $apbdesList->firstItem() + $idx }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $item->village->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $item->village->subdistrict }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-700 block">{{ $item->title }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->document_number ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->type === 'murni' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($item->type) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-600">
                                Rp {{ number_format($item->total_revenue_budget, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($item->total_expenditure_budget, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $item->status_badge }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('staff.apbdes.show', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Rincian</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400 text-xs">
                                Tidak ada dokumen APBDes ditemukan untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($apbdesList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $apbdesList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
