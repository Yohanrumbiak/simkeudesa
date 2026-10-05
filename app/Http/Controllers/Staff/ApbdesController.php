<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Apbdes;
use App\Models\ApbdesItem;
use App\Models\BudgetSector;
use App\Models\Institution;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApbdesController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = $request->input('year', $institution?->active_fiscal_year ?? 2026);

        $query = Apbdes::with(['village', 'approver'])->where('fiscal_year', $activeYear);

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->input('village_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhereHas('village', fn ($vq) => $vq->where('name', 'like', "%{$search}%"));
            });
        }

        $apbdesList = $query->paginate(10)->withQueryString();
        $villages = Village::where('is_active', true)->get();

        return view('staff.apbdes.index', compact('apbdesList', 'villages', 'activeYear'));
    }

    public function show(int $id): View
    {
        $apbdes = Apbdes::with(['village', 'approver', 'items.sector'])->findOrFail($id);

        $revenueItems = $apbdes->items->where('type', 'pendapatan');
        $expenditureItems = $apbdes->items->where('type', 'belanja')->groupBy('budget_sector_id');
        $sectors = BudgetSector::all()->keyBy('id');

        return view('staff.apbdes.show', compact('apbdes', 'revenueItems', 'expenditureItems', 'sectors'));
    }

    public function create(): View
    {
        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();
        $institution = Institution::first();

        return view('staff.apbdes.create', compact('villages', 'sectors', 'institution'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'fiscal_year' => 'required|integer',
            'title' => 'required|string|max:255',
            'document_number' => 'required|string|max:255',
            'type' => 'required|in:murni,perubahan',
            'revenue_names' => 'required|array',
            'revenue_codes' => 'required|array',
            'revenue_amounts' => 'required|array',
            'expenditure_sectors' => 'required|array',
            'expenditure_codes' => 'required|array',
            'expenditure_names' => 'required|array',
            'expenditure_amounts' => 'required|array',
        ]);

        $totalRevenue = array_sum(array_map('floatval', $request->input('revenue_amounts')));
        $totalExpenditure = array_sum(array_map('floatval', $request->input('expenditure_amounts')));

        $apbdes = Apbdes::create([
            'village_id' => $request->input('village_id'),
            'fiscal_year' => $request->input('fiscal_year'),
            'title' => $request->input('title'),
            'document_number' => $request->input('document_number'),
            'type' => $request->input('type'),
            'total_revenue_budget' => $totalRevenue,
            'total_expenditure_budget' => $totalExpenditure,
            'total_financing_budget' => 0,
            'status' => 'disetujui',
            'approval_date' => now()->toDateString(),
            'approved_by' => auth()->id(),
            'notes' => $request->input('notes'),
        ]);

        // Revenue items
        foreach ($request->input('revenue_names') as $i => $name) {
            if (!empty($name) && isset($request->input('revenue_amounts')[$i])) {
                ApbdesItem::create([
                    'apbdes_id' => $apbdes->id,
                    'type' => 'pendapatan',
                    'account_code' => $request->input('revenue_codes')[$i] ?? '4.x',
                    'activity_name' => $name,
                    'original_amount' => (float) $request->input('revenue_amounts')[$i],
                    'revised_amount' => (float) $request->input('revenue_amounts')[$i],
                ]);
            }
        }

        // Expenditure items
        foreach ($request->input('expenditure_names') as $i => $name) {
            if (!empty($name) && isset($request->input('expenditure_amounts')[$i])) {
                ApbdesItem::create([
                    'apbdes_id' => $apbdes->id,
                    'type' => 'belanja',
                    'budget_sector_id' => $request->input('expenditure_sectors')[$i],
                    'account_code' => $request->input('expenditure_codes')[$i] ?? '5.x',
                    'activity_name' => $name,
                    'original_amount' => (float) $request->input('expenditure_amounts')[$i],
                    'revised_amount' => (float) $request->input('expenditure_amounts')[$i],
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $apbdes->village_id,
            'action' => 'BUAT_APBDES',
            'description' => 'Membuat dokumen APBDes ' . $apbdes->title . ' T.A. ' . $apbdes->fiscal_year,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.apbdes.show', $apbdes->id)
            ->with('success', 'Data APBDes berhasil disimpan dan disetujui.');
    }

    public function verifyStatus(Request $request, int $id): RedirectResponse
    {
        $apbdes = Apbdes::findOrFail($id);
        $status = $request->input('status');

        $apbdes->status = $status;
        if ($status === 'disetujui') {
            $apbdes->approved_by = auth()->id();
            $apbdes->approval_date = now()->toDateString();
        }
        $apbdes->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $apbdes->village_id,
            'action' => 'UBAH_STATUS_APBDES',
            'description' => 'Mengubah status APBDes ' . $apbdes->title . ' menjadi ' . $status,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Status APBDes berhasil diperbarui.');
    }
}
