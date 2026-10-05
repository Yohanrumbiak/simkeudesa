@extends('layouts.app')

@section('title', 'Aset & Inventaris Desa')
@section('header_title', 'Pengelolaan Aset dan Inventaris Desa')
@section('header_subtitle', 'Pencatatan, pemantauan kondisi, dan nilai inventaris aset milik desa')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Summary -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs text-slate-400">Total Nilai Perolehan Inventaris Aset:</span>
            <h3 class="text-2xl font-extrabold text-blue-700">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h3>
        </div>
        <a href="{{ route('staff.assets.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Aset Desa</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('staff.assets.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
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
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori Aset</label>
                <select name="category" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kondisi Aset</label>
                <select name="condition" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ request('condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ request('condition') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ request('condition') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / Kode / Lokasi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..."
                       class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('staff.assets.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table of Assets -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                        <th class="py-3.5 px-4">Kode & Nama Aset</th>
                        <th class="py-3.5 px-4">Desa</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Tahun Perolehan</th>
                        <th class="py-3.5 px-4 text-right">Nilai Perolehan (Rp)</th>
                        <th class="py-3.5 px-4 text-center">Jumlah</th>
                        <th class="py-3.5 px-4 text-center">Kondisi</th>
                        <th class="py-3.5 px-4">Lokasi / Catatan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets as $a)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $a->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $a->asset_code }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-700 block">{{ $a->village->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $a->village->subdistrict }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $a->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $a->acquisition_year }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-blue-700">
                                Rp {{ number_format($a->acquisition_value, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                {{ $a->quantity }} {{ $a->unit }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $a->condition_badge }}">
                                    {{ $a->condition_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-700 block truncate max-w-[160px]">{{ $a->location }}</span>
                                @if($a->maintenance_notes)
                                    <span class="text-[10px] text-slate-400 italic truncate max-w-[160px] block">{{ $a->maintenance_notes }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('staff.assets.edit', $a->id) }}" class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('staff.assets.destroy', $a->id) }}" onsubmit="return confirm('Hapus aset ini dari inventaris?')">
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
                            <td colspan="9" class="py-10 text-center text-slate-400">Tidak ada data aset desa ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
