@extends('layouts.app')

@section('title', 'Edit Aset Desa')
@section('header_title', 'Perbarui Data Inventaris Aset')
@section('header_subtitle', $asset->name . ' (' . $asset->asset_code . ')')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('staff.assets.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Inventaris Aset</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <form method="POST" action="{{ route('staff.assets.update', $asset->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Desa Pemilik</label>
                    <input type="text" value="{{ $asset->village->name }}" disabled
                           class="w-full text-xs py-2.5 px-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Aset <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        @foreach($categories as $c)
                            <option value="{{ $c }}" {{ $asset->category == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Barang / Aset <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ $asset->name }}" required
                       class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Perolehan <span class="text-rose-500">*</span></label>
                    <input type="number" name="acquisition_year" value="{{ $asset->acquisition_year }}" min="1980" max="{{ date('Y') }}" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nilai Perolehan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="acquisition_value" value="{{ (int)$asset->acquisition_value }}" min="0" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500 font-bold text-blue-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kondisi <span class="text-rose-500">*</span></label>
                    <select name="condition" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="baik" {{ $asset->condition == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ $asset->condition == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ $asset->condition == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" value="{{ $asset->quantity }}" min="1" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ $asset->unit }}" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi Keberadaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="location" value="{{ $asset->location }}" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pemeliharaan / Keterangan</label>
                <textarea name="maintenance_notes" rows="3"
                          class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">{{ $asset->maintenance_notes }}</textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('staff.assets.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
