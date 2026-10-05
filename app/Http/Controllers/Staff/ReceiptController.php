<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Institution;
use App\Models\Receipt;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = $request->input('year', $institution?->active_fiscal_year ?? 2026);

        $query = Receipt::with(['village', 'creator'])->where('fiscal_year', $activeYear)->latest('date');

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->input('village_id'));
        }

        if ($request->filled('funding_source')) {
            $query->where('funding_source', $request->input('funding_source'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('payer', 'like', "%{$search}%");
            });
        }

        $receipts = $query->paginate(15)->withQueryString();
        $villages = Village::where('is_active', true)->get();
        $totalAmount = (float) $query->sum('amount');

        $fundingSources = [
            'Dana Desa (DD)',
            'Alokasi Dana Desa (ADD)',
            'Bagi Hasil Pajak & Retribusi (PBH)',
            'Pendapatan Asli Desa (PADes)',
            'Bantuan Keuangan Provinsi/Kabupaten',
            'Pendapatan Lain-lain',
        ];

        return view('staff.receipts.index', compact('receipts', 'villages', 'activeYear', 'totalAmount', 'fundingSources'));
    }

    public function create(): View
    {
        $villages = Village::where('is_active', true)->get();
        $fundingSources = [
            'Dana Desa (DD)',
            'Alokasi Dana Desa (ADD)',
            'Bagi Hasil Pajak & Retribusi (PBH)',
            'Pendapatan Asli Desa (PADes)',
            'Bantuan Keuangan Provinsi/Kabupaten',
            'Pendapatan Lain-lain',
        ];

        return view('staff.receipts.create', compact('villages', 'fundingSources'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'date' => 'required|date',
            'funding_source' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payer' => 'nullable|string|max:255',
            'destination_account' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $village = Village::findOrFail($request->input('village_id'));
        $transactionNumber = 'RCV/' . $village->code . '/' . date('Y') . '/' . str_pad(Receipt::where('village_id', $village->id)->count() + 1, 3, '0', STR_PAD_LEFT);

        $docPath = null;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('documents/receipts', 'public');
        }

        $receipt = Receipt::create([
            'village_id' => $village->id,
            'fiscal_year' => (int) date('Y', strtotime($request->input('date'))),
            'transaction_number' => $transactionNumber,
            'date' => $request->input('date'),
            'funding_source' => $request->input('funding_source'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'payer' => $request->input('payer'),
            'destination_account' => $request->input('destination_account', $village->bank_account_number),
            'supporting_document' => $docPath,
            'status' => 'diverifikasi',
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $village->id,
            'action' => 'TAMBAH_PENDAPATAN',
            'description' => 'Mencatat penerimaan ' . $receipt->transaction_number . ' senilai Rp ' . number_format($receipt->amount, 0, ',', '.'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.receipts.index')->with('success', 'Transaksi penerimaan berhasil dicatat.');
    }

    public function edit(int $id): View
    {
        $receipt = Receipt::findOrFail($id);
        $villages = Village::where('is_active', true)->get();
        $fundingSources = [
            'Dana Desa (DD)',
            'Alokasi Dana Desa (ADD)',
            'Bagi Hasil Pajak & Retribusi (PBH)',
            'Pendapatan Asli Desa (PADes)',
            'Bantuan Keuangan Provinsi/Kabupaten',
            'Pendapatan Lain-lain',
        ];

        return view('staff.receipts.edit', compact('receipt', 'villages', 'fundingSources'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $receipt = Receipt::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'funding_source' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payer' => 'nullable|string|max:255',
            'destination_account' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('document')) {
            if ($receipt->supporting_document && Storage::disk('public')->exists($receipt->supporting_document)) {
                Storage::disk('public')->delete($receipt->supporting_document);
            }
            $receipt->supporting_document = $request->file('document')->store('documents/receipts', 'public');
        }

        $receipt->update([
            'date' => $request->input('date'),
            'funding_source' => $request->input('funding_source'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'payer' => $request->input('payer'),
            'destination_account' => $request->input('destination_account'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $receipt->village_id,
            'action' => 'UBAH_PENDAPATAN',
            'description' => 'Mengubah data penerimaan ' . $receipt->transaction_number,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.receipts.index')->with('success', 'Transaksi penerimaan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $receipt = Receipt::findOrFail($id);
        $transNo = $receipt->transaction_number;
        $villageId = $receipt->village_id;

        $receipt->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $villageId,
            'action' => 'HAPUS_PENDAPATAN',
            'description' => 'Menghapus transaksi penerimaan ' . $transNo,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('staff.receipts.index')->with('success', 'Transaksi penerimaan telah dihapus.');
    }
}
