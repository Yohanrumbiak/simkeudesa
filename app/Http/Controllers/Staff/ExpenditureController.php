<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BudgetSector;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExpenditureController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = $request->input('year', $institution?->active_fiscal_year ?? 2026);

        $query = Expenditure::with(['village', 'sector', 'creator'])->where('fiscal_year', $activeYear)->latest('date');

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->input('village_id'));
        }

        if ($request->filled('budget_sector_id')) {
            $query->where('budget_sector_id', $request->input('budget_sector_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('activity_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('payee', 'like', "%{$search}%");
            });
        }

        $expenditures = $query->paginate(15)->withQueryString();
        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();
        $totalAmount = (float) $query->sum('amount');

        return view('staff.expenditures.index', compact('expenditures', 'villages', 'sectors', 'activeYear', 'totalAmount'));
    }

    public function create(): View
    {
        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();

        return view('staff.expenditures.create', compact('villages', 'sectors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'date' => 'required|date',
            'budget_sector_id' => 'required|exists:budget_sectors,id',
            'activity_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'payee' => 'required|string|max:255',
            'payment_method' => 'required|in:Transfer Bank,Tunai',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $village = Village::findOrFail($request->input('village_id'));
        $transactionNumber = 'EXP/' . $village->code . '/' . date('Y') . '/' . str_pad(Expenditure::where('village_id', $village->id)->count() + 1, 3, '0', STR_PAD_LEFT);

        $docPath = null;
        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store('documents/expenditures', 'public');
        }

        $expenditure = Expenditure::create([
            'village_id' => $village->id,
            'fiscal_year' => (int) date('Y', strtotime($request->input('date'))),
            'transaction_number' => $transactionNumber,
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
            'action' => 'CATAT_BELANJA',
            'description' => 'Mencatat pengeluaran belanja ' . $expenditure->transaction_number . ' senilai Rp ' . number_format($expenditure->amount, 0, ',', '.'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.expenditures.index')->with('success', 'Catatan pengeluaran belanja desa berhasil disimpan.');
    }

    public function edit(int $id): View
    {
        $expenditure = Expenditure::findOrFail($id);
        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();

        return view('staff.expenditures.edit', compact('expenditure', 'villages', 'sectors'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $expenditure = Expenditure::findOrFail($id);

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

        if ($request->hasFile('document')) {
            if ($expenditure->supporting_document && Storage::disk('public')->exists($expenditure->supporting_document)) {
                Storage::disk('public')->delete($expenditure->supporting_document);
            }
            $expenditure->supporting_document = $request->file('document')->store('documents/expenditures', 'public');
        }

        $expenditure->update([
            'date' => $request->input('date'),
            'budget_sector_id' => $request->input('budget_sector_id'),
            'activity_name' => $request->input('activity_name'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'payee' => $request->input('payee'),
            'payment_method' => $request->input('payment_method'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $expenditure->village_id,
            'action' => 'UBAH_BELANJA',
            'description' => 'Memperbarui transaksi belanja ' . $expenditure->transaction_number,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.expenditures.index')->with('success', 'Catatan pengeluaran belanja berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $expenditure = Expenditure::findOrFail($id);
        $transNo = $expenditure->transaction_number;
        $villageId = $expenditure->village_id;

        $expenditure->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $villageId,
            'action' => 'HAPUS_BELANJA',
            'description' => 'Menghapus catatan belanja ' . $transNo,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('staff.expenditures.index')->with('success', 'Catatan transaksi belanja telah dihapus.');
    }
}
