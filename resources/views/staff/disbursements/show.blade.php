@extends('layouts.app')

@section('title', 'Detail & Verifikasi Berkas Penyaluran')
@section('header_title', 'Verifikasi Dokumen Penyaluran Dana')
@section('header_subtitle', $application->title . ' - ' . $application->village->name)

@section('content')
<div class="space-y-6">

    <!-- Top Back button & status header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('staff.disbursements.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Permohonan</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500">Status Saat Ini:</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $application->status_badge }}">
                {{ $application->status_label }}
            </span>
        </div>
    </div>

    <!-- Application Overview Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 cols: Application Info & Documents Checklist -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Application Summary -->
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice text-blue-600"></i>
                        <span>Informasi Permohonan Penyaluran</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-slate-500">{{ $application->application_number }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Nama Desa:</span>
                        <span class="text-slate-800 font-bold text-sm">{{ $application->village->name }}</span>
                        <p class="text-slate-500">{{ $application->village->subdistrict }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Kepala Desa:</span>
                        <span class="text-slate-800 font-semibold">{{ $application->village->head_name }}</span>
                        <p class="text-slate-500">{{ $application->village->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Tahap Penyaluran:</span>
                        <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold inline-block">
                            {{ $application->phase }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Nominal Pengajuan:</span>
                        <span class="text-blue-700 font-extrabold text-base">
                            Rp {{ number_format($application->requested_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Tanggal Pengajuan:</span>
                        <span class="text-slate-700 font-semibold">{{ $application->submission_date->format('d F Y') }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Rekening Kas Desa (RKD):</span>
                        <span class="text-slate-800 font-mono font-semibold">{{ $application->village->bank_name }} - {{ $application->village->bank_account_number }}</span>
                        <p class="text-[11px] text-slate-400">a.n {{ $application->village->bank_account_holder }}</p>
                    </div>
                </div>

                @if($application->review_notes)
                    <div class="mt-4 p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/60 text-xs text-amber-900">
                        <span class="font-bold block mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-comment-dots text-amber-600"></i>
                            Catatan Verifikator Terakhir:
                        </span>
                        <p>{{ $application->review_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Card 2: Uploaded Documents Verification Checklist -->
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-folder-open text-cyan-600"></i>
                            <span>Daftar Dokumen Persyaratan yang Diunggah</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Periksa keabsahan dan kelengkapan dokumen sesuai Permendagri</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                        {{ $application->documents->count() }} Berkas
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($application->documents as $doc)
                        <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 text-base">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800">{{ $doc->document_name }}</h4>
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-400">
                                            <span>Format: <strong class="text-slate-600 uppercase">{{ $doc->file_type }}</strong></span>
                                            <span>&bull;</span>
                                            <span>Ukuran: <strong class="text-slate-600">{{ number_format($doc->file_size / 1024, 1) }} KB</strong></span>
                                            <span>&bull;</span>
                                            <span>Diunggah: {{ $doc->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if($doc->notes)
                                            <p class="text-[11px] text-amber-700 bg-amber-50 p-2 rounded-lg mt-2 border border-amber-200/50">
                                                <strong>Catatan Revisi:</strong> {{ $doc->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $doc->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                    </span>
                                    <a href="{{ route('staff.disbursements.download', $doc->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition">
                                        <i class="fa-solid fa-download"></i>
                                        <span>Unduh Berkas</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl">
                            Belum ada berkas lampiran yang diunggah.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Card 3: Verification History Timeline -->
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                    <i class="fa-solid fa-timeline text-indigo-600"></i>
                    <span>Riwayat Ulasan & Telaah Dokumen</span>
                </h3>

                <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($application->reviews as $rev)
                        <div class="relative">
                            <div class="absolute -left-[29px] top-1 w-3.5 h-3.5 rounded-full bg-blue-600 ring-4 ring-blue-100"></div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 text-xs">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="font-bold text-slate-800">{{ $rev->reviewer?->name ?? 'Verifikator DPMD' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $rev->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-200 text-slate-700">{{ $rev->status_before }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold">{{ $rev->status_after }}</span>
                                </div>
                                @if($rev->notes)
                                    <p class="text-slate-600 mt-1 italic">"{{ $rev->notes }}"</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 py-2">Belum ada riwayat verifikasi sebelumnya.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right 1 col: Verification Action Form -->
        <div class="space-y-6">

            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs sticky top-20">
                <div class="pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-stamp text-blue-600"></i>
                        <span>Keputusan Verifikasi</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tetapkan status kelengkapan berkas</p>
                </div>

                <form method="POST" action="{{ route('staff.disbursements.verify', $application->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Status Hasil Verifikasi <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" required class="w-full text-xs font-semibold py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">
                            <option value="menunggu_verifikasi" {{ $application->status == 'menunggu_verifikasi' ? 'selected' : '' }}>
                                ⏳ Menunggu Verifikasi
                            </option>
                            <option value="perlu_revisi" {{ $application->status == 'perlu_revisi' ? 'selected' : '' }}>
                                ⚠️ Perlu Perbaikan / Revisi (Kembalikan ke Desa)
                            </option>
                            <option value="lengkap" {{ $application->status == 'lengkap' ? 'selected' : '' }}>
                                ✅ Berkas Lengkap & Memenuhi Syarat
                            </option>
                            <option value="disalurkan" {{ $application->status == 'disalurkan' ? 'selected' : '' }}>
                                💰 Telah Disalurkan (SP2D Diterbitkan)
                            </option>
                            <option value="ditolak" {{ $application->status == 'ditolak' ? 'selected' : '' }}>
                                ❌ Ditolak
                            </option>
                        </select>
                    </div>

                    <!-- Individual Document Checklist Statuses -->
                    @if($application->documents->count() > 0)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 space-y-2">
                            <span class="text-[11px] font-bold text-slate-700 block">Status Per Dokumen:</span>
                            @foreach($application->documents as $doc)
                                <div class="text-xs border-b border-slate-200/60 pb-2 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="truncate max-w-[140px] text-slate-700 font-medium">{{ $doc->document_name }}</span>
                                        <select name="document_status[{{ $doc->id }}]" class="text-[10px] py-1 px-1.5 rounded-lg border border-slate-200 bg-white">
                                            <option value="lengkap" {{ $doc->status == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                                            <option value="perlu_revisi" {{ $doc->status == 'perlu_revisi' ? 'selected' : '' }}>Revisi</option>
                                            <option value="ditolak" {{ $doc->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </div>
                                    <input type="text" name="document_notes[{{ $doc->id }}]" value="{{ $doc->notes }}" placeholder="Catatan per dokumen (opsional)"
                                           class="w-full text-[10px] py-1 px-2 border border-slate-200 rounded-md bg-white">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Catatan / Arahan Verifikasi untuk Kepala Desa
                        </label>
                        <textarea name="review_notes" rows="4" placeholder="Tuliskan catatan detail jika dokumen perlu perbaikan, poin yang harus dilengkapi, atau instruksi berikutnya..."
                                  class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-hidden focus:border-blue-500">{{ old('review_notes', $application->review_notes) }}</textarea>
                    </div>

                    <!-- Government Procedural Note -->
                    <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-[11px] text-blue-900 leading-relaxed">
                        <i class="fa-solid fa-circle-info text-blue-600 mr-1"></i>
                        <strong>Ketentuan SOP:</strong> Status berkas <em>Lengkap</em> menandakan syarat administratif telah dipenuhi desa. Penyaluran dana aktual ke RKD dilakukan setelah penerbitan SP2D oleh BPKAD.
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Keputusan Verifikasi</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
