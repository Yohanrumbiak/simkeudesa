@extends('layouts.app')

@section('title', 'Tambah Dokumen APBDes')
@section('header_title', 'Form Input Dokumen APBDes')
@section('header_subtitle', 'Penyusunan alokasi pendapatan dan belanja desa baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('staff.apbdes.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Master APBDes</span>
        </a>
    </div>

    <form method="POST" action="{{ route('staff.apbdes.store') }}" class="space-y-6">
        @csrf

        <!-- General Info Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-file-lines text-blue-600"></i>
                <span>Identitas Dokumen APBDes</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Desa <span class="text-rose-500">*</span></label>
                    <select name="village_id" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="">Pilih Desa...</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->subdistrict }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Anggaran <span class="text-rose-500">*</span></label>
                    <input type="number" name="fiscal_year" value="2026" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Dokumen <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" placeholder="Contoh: APBDes Induk T.A. 2026" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Peraturan Desa (Perdes) <span class="text-rose-500">*</span></label>
                    <input type="text" name="document_number" placeholder="Contoh: PERDES/04/2026" required
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Anggaran <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                        <option value="murni">APBDes Murni / Induk</option>
                        <option value="perubahan">Perubahan APBDes (P-APBDes)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Tambahan</label>
                    <input type="text" name="notes" placeholder="Catatan evaluasi tim DPMD..."
                           class="w-full text-xs py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Revenue Items Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-down-left text-emerald-600"></i>
                    <span>Rencana Pendapatan Desa (Kelompok 4)</span>
                </h3>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-12 gap-2 text-xs">
                    <div class="col-span-3">
                        <label class="text-[11px] font-bold text-slate-500 block mb-1">Kode Akun</label>
                        <input type="text" name="revenue_codes[]" value="4.2.1" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                    <div class="col-span-5">
                        <label class="text-[11px] font-bold text-slate-500 block mb-1">Sumber Pendapatan</label>
                        <input type="text" name="revenue_names[]" value="Dana Desa (DD) Alokasi Pusat" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                    <div class="col-span-4">
                        <label class="text-[11px] font-bold text-slate-500 block mb-1">Nominal Anggaran (Rp)</label>
                        <input type="number" name="revenue_amounts[]" value="850000000" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-2 text-xs">
                    <div class="col-span-3">
                        <input type="text" name="revenue_codes[]" value="4.2.2" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                    <div class="col-span-5">
                        <input type="text" name="revenue_names[]" value="Alokasi Dana Desa (ADD) APBD Kabupaten" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                    <div class="col-span-4">
                        <input type="number" name="revenue_amounts[]" value="650000000" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-2 text-xs">
                    <div class="col-span-3">
                        <input type="text" name="revenue_codes[]" value="4.1.1" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                    <div class="col-span-5">
                        <input type="text" name="revenue_names[]" value="Pendapatan Asli Desa (PADes)" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                    <div class="col-span-4">
                        <input type="number" name="revenue_amounts[]" value="180000000" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenditure Items Card across 5 Sectors -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up-right text-rose-600"></i>
                    <span>Rencana Belanja Desa (5 Bidang APBDes)</span>
                </h3>
            </div>

            <div class="space-y-3">
                @foreach($sectors as $idx => $sec)
                    <div class="p-3.5 rounded-xl border border-slate-200/70 bg-slate-50/50 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $sec->color }}"></span>
                            <span>{{ $sec->code }} - {{ $sec->name }}</span>
                        </div>
                        <input type="hidden" name="expenditure_sectors[]" value="{{ $sec->id }}">

                        <div class="grid grid-cols-12 gap-2 text-xs">
                            <div class="col-span-2">
                                <input type="text" name="expenditure_codes[]" value="5.{{ $idx + 1 }}.1" class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div class="col-span-6">
                                <input type="text" name="expenditure_names[]" value="Alokasi Belanja {{ $sec->name }}" class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-lg">
                            </div>
                            <div class="col-span-4">
                                <input type="number" name="expenditure_amounts[]" value="{{ [450000000, 680000000, 180000000, 220000000, 150000000][$idx] ?? 100000000 }}" class="w-full text-xs py-2 px-3 bg-white border border-slate-200 rounded-lg font-bold">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('staff.apbdes.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Dokumen APBDes</span>
            </button>
        </div>
    </form>

</div>
@endsection
