@extends('layouts.app')

@section('title', 'Verifikasi Dokumen Pencairan')
@section('header_title', 'Verifikasi Dokumen Penyaluran Dana Desa')
@section('header_subtitle', 'Pemeriksaan kelengkapan dan kepatuhan berkas pengajuan pencairan dana')

@section('content')
<div class="space-y-6">

    <!-- Stat Badges Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 block">Total Pengajuan</span>
            <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
        </div>
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200/80 shadow-xs">
            <span class="text-xs font-semibold text-amber-700 block">Menunggu Review</span>
            <span class="text-xl font-extrabold text-amber-800 mt-1 block">{{ $stats['menunggu'] }}</span>
        </div>
        <div class="bg-orange-50 rounded-xl p-4 border border-orange-200/80 shadow-xs">
            <span class="text-xs font-semibold text-orange-700 block">Perlu Revisi</span>
            <span class="text-xl font-extrabold text-orange-800 mt-1 block">{{ $stats['perlu_revisi'] }}</span>
        </div>
        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200/80 shadow-xs">
            <span class="text-xs font-semibold text-emerald-700 block">Dokumen Lengkap</span>
            <span class="text-xl font-extrabold text-emerald-800 mt-1 block">{{ $stats['lengkap'] }}</span>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200/80 shadow-xs">
            <span class="text-xs font-semibold text-blue-700 block">Telah Disalurkan</span>
            <span class="text-xl font-extrabold text-blue-800 mt-1 block">{{ $stats['disalurkan'] }}</span>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('staff.disbursements.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Desa</label>
                <select name="village_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="">Semua Desa</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" {{ request('village_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status Verifikasi</label>
                <select name="status" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="lengkap" {{ request('status') == 'lengkap' ? 'selected' : '' }}>Dokumen Lengkap</option>
                    <option value="disalurkan" {{ request('status') == 'disalurkan' ? 'selected' : '' }}>Telah Disalurkan</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tahap Penyaluran</label>
                <input type="text" name="phase" value="{{ request('phase') }}" placeholder="Contoh: Tahap I, Tahap II"
                       class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('staff.disbursements.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table of Disbursement Applications -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">Desa Pengaju</th>
                        <th class="py-3.5 px-4">No & Judul Pengajuan</th>
                        <th class="py-3.5 px-4">Tahap Penyaluran</th>
                        <th class="py-3.5 px-4 text-right">Nominal Pengajuan</th>
                        <th class="py-3.5 px-4">Tanggal Masuk</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($applications as $index => $app)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-slate-400 font-semibold">{{ $applications->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $app->village->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $app->village->subdistrict }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-700 block">{{ $app->title }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $app->application_number }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ $app->phase }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-blue-700">
                                Rp {{ number_format($app->requested_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $app->submission_date->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $app->status_badge }}">
                                    {{ $app->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('staff.disbursements.show', $app->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <span>Tinjau</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada dokumen permohonan penyaluran yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
