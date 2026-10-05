@extends('layouts.app')

@section('title', 'Catat Penerimaan Desa')
@section('header_title', 'Input Penerimaan Kas Desa')
@section('header_subtitle', 'Pencatatan pendapatan masuk ke Rekening Kas Desa')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('staff.receipts.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Penerimaan</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <form method="POST" action="{{ route('staff.receipts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Desa Penerima <span class="text-rose-500">*</span></label>
                    <select name="village_id" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="">Pilih Desa...</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Penerimaan <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sumber Pendapatan <span class="text-rose-500">*</span></label>
                <select name="funding_source" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="">Pilih Sumber Dana...</option>
                    @foreach($fundingSources as $fs)
                        <option value="{{ $fs }}">{{ $fs }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Uraian / Deskripsi Penerimaan <span class="text-rose-500">*</span></label>
                <input type="text" name="description" placeholder="Contoh: Penyaluran Dana Desa Tahap I 40% dari RKUN" required
                       class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Penerimaan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" placeholder="0" min="1" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500 font-bold text-emerald-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Penyetor / Asal Dana</label>
                    <input type="text" name="payer" placeholder="Contoh: KPPN Bandung II / BPKAD"
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Bukti Transaksi / SP2D (Opsional)</label>
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, JPG, PNG (Maks 5 MB)</span>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('staff.receipts.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Penerimaan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
