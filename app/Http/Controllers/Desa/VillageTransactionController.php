<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BudgetSector;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Receipt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageTransactionController extends Controller
{
    // Receipts methods
    public function receipts(Request $request): View
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);

        $query = Receipt::where('village_id', $village->id)->where('fiscal_year', $activeYear)->latest('date');

        if ($request->filled('funding_source')) {
            $query->where('funding_source', $request->input('funding_source'));
        }

        $receipts = $query->paginate(12)->withQueryString();
        $totalAmount = (float) $query->sum('amount');

        $fundingSources = [
            'Dana Desa (DD)',
            'Alokasi Dana Desa (ADD)',
            'Bagi Hasil Pajak & Retribusi (PBH)',
            'Pendapatan Asli Desa (PADes)',
            'Bantuan Keuangan Provinsi/Kabupaten',
            'Pendapatan Lain-lain',
        ];

        return view('desa.transactions.receipts', compact('village', 'receipts', 'totalAmount', 'fundingSources', 'activeYear'));
    }

    public function createReceipt(): View
    {
        $village = auth()->user()->village;
        $fundingSources = [
            'Dana Desa (DD)',
            'Alokasi Dana Desa (ADD)',
            'Bagi Hasil Pajak & Retribusi (PBH)',
            'Pendapatan Asli Desa (PADes)',
            'Bantuan Keuangan Provinsi/Kabupaten',
            'Pendapatan Lain-lain',
        ];

        return view('desa.transactions.create_receipt', compact('village', 'fundingSources'));
    }

    public function storeReceipt(Request $request): RedirectResponse
    {
        $village = auth()->user()->village;

        $request->validate([
            'date' => 'required|date',
            'funding_source' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payer' => 'nullable|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $transNo = 'RCV/' . $village->code . '/' . date('Y') . '/' . str_pad(Receipt::where('village_id', $village->id)->count() + 1, 3, '0', STR_PAD_LEFT);

        $docPath = null;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('documents/receipts', 'public');
        }

        $receipt = Receipt::create([
            'village_id' => $village->id,
            'fiscal_year' => (int) date('Y', strtotime($request->input('date'))),
            'transaction_number' => $transNo,
            'date' => $request->input('date'),
            'funding_source' => $request->input('funding_source'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'payer' => $request->input('payer'),
            'destination_account' => $village->bank_account_number,
            'supporting_document' => $docPath,
            'status' => 'diverifikasi',
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $village->id,
            'action' => 'INPUT_PENERIMAAN_DESA',
            'description' => 'Kepala Desa mencatat penerimaan: ' . $receipt->transaction_number . ' senilai Rp ' . number_format($receipt->amount, 0, ',', '.'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('desa.receipts')->with('success', 'Catatan penerimaan kas desa berhasil disimpan.');
    }

    // Expenditures methods
    public function expenditures(Request $request): View
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);

        $query = Expenditure::with('sector')
            ->where('village_id', $village->id)
            ->where('fiscal_year', $activeYear)
            ->latest('date');

        if ($request->filled('sector_id')) {
            $query->where('budget_sector_id', $request->input('sector_id'));
        }

        $expenditures = $query->paginate(12)->withQueryString();
        $sectors = BudgetSector::all();
        $totalAmount = (float) $query->sum('amount');

        return view('desa.transactions.expenditures', compact('village', 'expenditures', 'sectors', 'totalAmount', 'activeYear'));
    }

    public function createExpenditure(): View
    {
        $village = auth()->user()->village;
        $sectors = BudgetSector::all();

        return view('desa.transactions.create_expenditure', compact('village', 'sectors'));
    }

    public function storeExpenditure(Request $request): RedirectResponse
    {
        $village = auth()->user()->village;

        $request->validate([
            'date' => 'required|date',
            'budget_sector_id' => 'required|exists:budget_sectors,id',
            'activity_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payee' => 'required|string|max:255',
            'payment_method' => 'required|in:Transfer Bank,Tunai',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $transNo = 'EXP/' . $village->code . '/' . date('Y') . '/' . str_pad(Expenditure::where('village_id', $village->id)->count() + 1, 3, '0', STR_PAD_LEFT);

        $docPath = null;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('documents/expenditures', 'public');
        }

        $expenditure = Expenditure::create([
            'village_id' => $village->id,
            'fiscal_year' => (int) date('Y', strtotime($request->input('date'))),
            'transaction_number' => $transNo,
            'date' => $request->input('date'),
            'budget_sector_id' => $request->input('budget_sector_id'),
            'activity_name' => $request->input('activity_name'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'payee' => $request->input('payee'),
            'payment_method' => $request->input('payment_method'),
            'supporting_document' => $docPath,
            'status' => 'dibayar',
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $village->id,
            'action' => 'INPUT_BELANJA_DESA',
            'description' => 'Kepala Desa mencatat pengeluaran belanja: ' . $expenditure->transaction_number . ' senilai Rp ' . number_format($expenditure->amount, 0, ',', '.'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('desa.expenditures')->with('success', 'Catatan transaksi belanja desa berhasil disimpan.');
    }
}
