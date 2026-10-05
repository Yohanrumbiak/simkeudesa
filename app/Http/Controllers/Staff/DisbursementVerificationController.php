<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DisbursementApplication;
use App\Models\DisbursementDocument;
use App\Models\DisbursementReview;
use App\Models\Notification;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DisbursementVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = DisbursementApplication::with(['village', 'documents', 'verifier'])->latest();

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->input('village_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('phase')) {
            $query->where('phase', 'like', '%' . $request->input('phase') . '%');
        }

        $applications = $query->paginate(10)->withQueryString();
        $villages = Village::where('is_active', true)->get();

        $stats = [
            'total' => DisbursementApplication::count(),
            'menunggu' => DisbursementApplication::where('status', 'menunggu_verifikasi')->count(),
            'perlu_revisi' => DisbursementApplication::where('status', 'perlu_revisi')->count(),
            'lengkap' => DisbursementApplication::where('status', 'lengkap')->count(),
            'disalurkan' => DisbursementApplication::where('status', 'disalurkan')->count(),
        ];

        return view('staff.disbursements.index', compact('applications', 'villages', 'stats'));
    }

    public function show(int $id): View
    {
        $application = DisbursementApplication::with(['village', 'documents', 'reviews.reviewer', 'verifier'])
            ->findOrFail($id);

        return view('staff.disbursements.show', compact('application'));
    }

    public function verify(Request $request, int $id): RedirectResponse
    {
        $application = DisbursementApplication::with('village')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:menunggu_verifikasi,perlu_revisi,lengkap,ditolak,disalurkan',
            'review_notes' => 'nullable|string',
            'document_status' => 'nullable|array',
            'document_notes' => 'nullable|array',
        ]);

        $statusBefore = $application->status;
        $statusAfter = $request->input('status');
        $notes = $request->input('review_notes');

        // Update application
        $application->status = $statusAfter;
        $application->review_notes = $notes;
        $application->verified_by = auth()->id();
        $application->verified_at = now();
        $application->save();

        // Update individual documents if provided
        if ($request->has('document_status')) {
            foreach ($request->input('document_status') as $docId => $docStatus) {
                $doc = DisbursementDocument::where('disbursement_application_id', $application->id)
                    ->where('id', $docId)
                    ->first();

                if ($doc) {
                    $doc->status = $docStatus;
                    $doc->notes = $request->input('document_notes.' . $docId, $doc->notes);
                    $doc->save();
                }
            }
        }

        // Record in review history
        DisbursementReview::create([
            'disbursement_application_id' => $application->id,
            'reviewer_id' => auth()->id(),
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
            'notes' => $notes,
        ]);

        // Send notification to Village Head
        $statusLabels = [
            'perlu_revisi' => 'Perlu Perbaikan / Revisi',
            'lengkap' => 'Dokumen Lengkap (Terverifikasi)',
            'ditolak' => 'Pengajuan Ditolak',
            'disalurkan' => 'Dana Telah Disalurkan',
            'menunggu_verifikasi' => 'Sedang Diproses',
        ];

        Notification::create([
            'village_id' => $application->village_id,
            'role_target' => 'kepala_desa',
            'title' => 'Status Berkas Penyaluran: ' . ($statusLabels[$statusAfter] ?? $statusAfter),
            'message' => 'Berkas ' . $application->title . ' telah ditinjau oleh tim verifikator DPMD. Catatan: ' . ($notes ?: 'Tidak ada catatan.'),
            'type' => $statusAfter === 'lengkap' || $statusAfter === 'disalurkan' ? 'success' : ($statusAfter === 'perlu_revisi' ? 'warning' : 'danger'),
            'link' => '/desa/disbursements/' . $application->id,
            'is_read' => false,
        ]);

        // Activity Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $application->village_id,
            'action' => 'VERIFIKASI_DOKUMEN',
            'description' => 'Verifikasi pengajuan ' . $application->application_number . ' status diubah dari ' . $statusBefore . ' ke ' . $statusAfter . '.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.disbursements.show', $application->id)
            ->with('success', 'Status pengajuan pencairan berhasil diperbarui.');
    }

    public function downloadDocument(int $id): BinaryFileResponse|RedirectResponse
    {
        $document = DisbursementDocument::findOrFail($id);

        if (Storage::disk('public')->exists($document->file_path)) {
            return response()->download(Storage::disk('public')->path($document->file_path), $document->document_name . '.' . $document->file_type);
        }

        // Fallback for mock seeded documents: return generated dummy PDF or back with notice
        return back()->with('info', 'File berkas permohonan ' . $document->document_name . ' siap diunduh (Simulasi berkas terverifikasi).');
    }
}
