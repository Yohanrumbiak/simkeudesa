@extends('layouts.app')

@section('title', 'Catat Belanja Desa')
@section('header_title', 'Input Pengeluaran Belanja Desa')
@section('header_subtitle', 'Pencatatan realisasi belanja per bidang dan kegiatan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('staff.expenditures.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Belanja</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <form method="POST" action="{{ route('staff.expenditures.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Desa Pelaksana <span class="text-rose-500">*</span></label>
                    <select name="village_id" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="">Pilih Desa...</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Belanja <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Bidang Anggaran APBDes <span class="text-rose-500">*</span></label>
                <select name="budget_sector_id" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="">Pilih Bidang...</option>
                    @foreach($sectors as $s)
                        <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="activity_name" placeholder="Contoh: Pekerjaan Rabat Beton Jalan Lingkungan RW 03" required
                       class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Uraian Belanja / Deskripsi Pembayaran <span class="text-rose-500">*</span></label>
                <input type="text" name="description" placeholder="Contoh: Pembelian semen, pasir, dan upah padat karya tunai" required
                       class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Belanja (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" placeholder="0" min="1" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500 font-bold text-rose-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Penerima Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="payee" placeholder="Contoh: Toko Bangunan Jaya / TPK Desa" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="Transfer Bank">Transfer Bank (Non-Tunai)</option>
                        <option value="Tunai">Tunai Kas Desa</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Bukti Kuitansi / Faktur (Opsional)</label>
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('staff.expenditures.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengeluaran Belanja</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
