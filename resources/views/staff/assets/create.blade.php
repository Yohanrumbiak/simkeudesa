@extends('layouts.app')

@section('title', 'Tambah Aset Desa')
@section('header_title', 'Input Inventaris Aset Desa Baru')
@section('header_subtitle', 'Pencatatan aset tetap dan barang inventaris milik pemerintah desa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('staff.assets.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Inventaris Aset</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <form method="POST" action="{{ route('staff.assets.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Desa Pemilik <span class="text-rose-500">*</span></label>
                    <select name="village_id" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="">Pilih Desa...</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Aset <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Barang / Aset <span class="text-rose-500">*</span></label>
                <input type="text" name="name" placeholder="Contoh: Gedung Kantor dan Balai Desa / Mobil Ambulans Suzuki APV" required
                       class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Perolehan <span class="text-rose-500">*</span></label>
                    <input type="number" name="acquisition_year" value="{{ date('Y') }}" min="1980" max="{{ date('Y') }}" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nilai Perolehan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="acquisition_value" placeholder="0" min="0" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500 font-bold text-blue-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kondisi <span class="text-rose-500">*</span></label>
                    <select name="condition" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah <span class="text-rose-500">*</span></label>
                    <input type="number" name="quantity" value="1" min="1" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="Unit" placeholder="Unit, Buah, M2, Paket" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi Keberadaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="location" placeholder="Contoh: Kantor Desa / RW 02" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pemeliharaan / Keterangan</label>
                <textarea name="maintenance_notes" rows="3" placeholder="Informasi status pemeliharaan, nomor BPKB / sertifikat, dsb..."
                          class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('staff.assets.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Aset Desa</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
