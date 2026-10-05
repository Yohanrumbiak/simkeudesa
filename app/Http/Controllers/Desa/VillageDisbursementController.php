<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DisbursementApplication;
use App\Models\DisbursementDocument;
use App\Models\Institution;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageDisbursementController extends Controller
{
    public function index(): View
    {
        $village = auth()->user()->village;
        $applications = DisbursementApplication::with(['documents', 'reviews.reviewer', 'verifier'])
            ->where('village_id', $village->id)
            ->latest()
            ->paginate(10);

        return view('desa.disbursements.index', compact('village', 'applications'));
    }

    public function create(): View
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $phases = [
            'Tahap I (40%)',
            'Tahap II (40%)',
            'Tahap III (20%)',
            'Alokasi Dana Desa (ADD) Triwulan I & II',
            'Alokasi Dana Desa (ADD) Triwulan III & IV',
            'Bantuan Keuangan Khusus Kabupaten',
        ];

        return view('desa.disbursements.create', compact('village', 'institution', 'phases'));
    }

    public function store(Request $request): RedirectResponse
    {
        $village = auth()->user()->village;
        $activeYear = Institution::first()?->active_fiscal_year ?? 2026;

        $request->validate([
            'title' => 'required|string|max:255',
            'phase' => 'required|string',
            'requested_amount' => 'required|numeric|min:1',
            'submission_date' => 'required|date',
            'doc_names' => 'required|array',
            'doc_files' => 'required|array',
            'doc_files.*' => 'nullable|file|mimes:pdf,zip,rar,jpg,png|max:10240', // Max 10MB
        ]);

        $appNumber = 'SPP-DD/' . $village->code . '/' . date('Y') . '/' . str_pad(DisbursementApplication::where('village_id', $village->id)->count() + 1, 3, '0', STR_PAD_LEFT);

        $application = DisbursementApplication::create([
            'village_id' => $village->id,
            'fiscal_year' => $activeYear,
            'application_number' => $appNumber,
            'title' => $request->input('title'),
            'phase' => $request->input('phase'),
            'requested_amount' => $request->input('requested_amount'),
            'submission_date' => $request->input('submission_date'),
            'status' => 'menunggu_verifikasi',
            'review_notes' => 'Permohonan baru diajukan oleh Kepala Desa.',
        ]);

        // Upload documents
        if ($request->hasFile('doc_files')) {
            foreach ($request->file('doc_files') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $docName = $request->input('doc_names')[$idx] ?? 'Dokumen Pendukung ' . ($idx + 1);
                    $path = $file->store('uploads/disbursements', 'public');

                    DisbursementDocument::create([
                        'disbursement_application_id' => $application->id,
                        'document_name' => $docName,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'status' => 'menunggu_verifikasi',
                    ]);
                }
            }
        }

        // Notify staff
        Notification::create([
            'village_id' => $village->id,
            'role_target' => 'admin',
            'title' => 'Pengajuan Baru: ' . $village->name,
            'message' => $village->name . ' mengajukan berkas permohonan penyaluran: ' . $application->title,
            'type' => 'info',
            'link' => '/staff/disbursements/' . $application->id,
            'is_read' => false,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $village->id,
            'action' => 'PENGAJUAN_PENCAIRAN',
            'description' => 'Mengajukan dokumen permohonan penyaluran ' . $application->title . ' senilai Rp ' . number_format($application->requested_amount, 0, ',', '.'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('desa.disbursements.index')->with('success', 'Permohonan pencairan dana desa dan dokumen berhasil diajukan untuk diverifikasi.');
    }

    public function show(int $id): View
    {
        $village = auth()->user()->village;
        $application = DisbursementApplication::with(['documents', 'reviews.reviewer', 'verifier'])
            ->where('village_id', $village->id)
            ->findOrFail($id);

        return view('desa.disbursements.show', compact('village', 'application'));
    }

    public function reupload(Request $request, int $docId): RedirectResponse
    {
        $village = auth()->user()->village;

        $request->validate([
            'revised_file' => 'required|file|mimes:pdf,zip,rar,jpg,png|max:10240',
            'revision_note' => 'nullable|string|max:255',
        ]);

        $document = DisbursementDocument::whereHas('application', function ($q) use ($village) {
            $q->where('village_id', $village->id);
        })->findOrFail($docId);

        // Delete old file if exists
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $file = $request->file('revised_file');
        $path = $file->store('uploads/disbursements', 'public');

        $document->update([
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientOriginalExtension(),
            'status' => 'menunggu_verifikasi',
            'notes' => 'Diperbaiki oleh Desa: ' . ($request->input('revision_note') ?: 'Perbaikan dokumen diunggah ulang.'),
        ]);

        // Reset application status to menunggu_verifikasi if was perlu_revisi
        $app = $document->application;
        $app->update([
            'status' => 'menunggu_verifikasi',
            'review_notes' => 'Dokumen revisi telah diunggah kembali oleh Kepala Desa. Menunggu verifikasi ulang.',
        ]);

        Notification::create([
            'village_id' => $village->id,
            'role_target' => 'admin',
            'title' => 'Revisi Diunggah: ' . $village->name,
            'message' => $village->name . ' telah mengunggah perbaikan berkas untuk ' . $app->title,
            'type' => 'info',
            'link' => '/staff/disbursements/' . $app->id,
            'is_read' => false,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $village->id,
            'action' => 'UNGGAH_REVISI',
            'description' => 'Mengunggah perbaikan dokumen ' . $document->document_name . ' untuk ' . $app->application_number,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Dokumen revisi berhasil diunggah dan diajukan kembali ke tim verifikator.');
    }
}
