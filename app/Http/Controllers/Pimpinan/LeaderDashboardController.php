<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\ActivitySchedule;
use App\Models\Apbdes;
use App\Models\BudgetSector;
use App\Models\DisbursementApplication;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Notification;
use App\Models\Receipt;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $selectedSectorId = $request->input('sector_id');

        // 4 Main Summary Cards in Rupiah calculated from database
        $totalBudget = (float) Apbdes::where('fiscal_year', $activeYear)
            ->where('status', 'disetujui')
            ->sum('total_expenditure_budget');

        $totalRealization = (float) Expenditure::where('fiscal_year', $activeYear)
            ->whereIn('status', ['diverifikasi', 'dibayar'])
            ->sum('amount');

        $totalReceipts = (float) Receipt::where('fiscal_year', $activeYear)
            ->where('status', 'diverifikasi')
            ->sum('amount');

        $totalExpenditures = $totalRealization;

        $overallAbsorption = $totalBudget > 0 ? round(($totalRealization / $totalBudget) * 100, 1) : 0.0;
        $remainingBudget = max(0, $totalBudget - $totalRealization);

        // Chart 1: Budget Realization Bar Chart by Village
        $villages = Village::where('is_active', true)->get();
        $villageChartLabels = [];
        $villageBudgetValues = [];
        $villageRealizationValues = [];
        $villageRankings = [];

        foreach ($villages as $v) {
            $vBudget = $v->getTotalBudgetForYear($activeYear);
            $vRealization = $v->getTotalExpendituresForYear($activeYear);
            $vRate = $vBudget > 0 ? round(($vRealization / $vBudget) * 100, 1) : 0;

            $villageChartLabels[] = $v->name;
            $villageBudgetValues[] = $vBudget;
            $villageRealizationValues[] = $vRealization;

            $villageRankings[] = [
                'village' => $v,
                'budget' => $vBudget,
                'realization' => $vRealization,
                'remaining' => max(0, $vBudget - $vRealization),
                'rate' => $vRate,
                'status_label' => $vRate >= 60 ? 'Optimal' : ($vRate >= 40 ? 'Sedang' : 'Rendah'),
                'badge' => $vRate >= 60 ? 'bg-emerald-100 text-emerald-800' : ($vRate >= 40 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'),
            ];
        }

        // Sort ranking by realization rate descending
        usort($villageRankings, fn ($a, $b) => $b['rate'] <=> $a['rate']);

        // Chart 2: Expenditure Composition across 5 Sectors (Doughnut)
        $sectors = BudgetSector::all();
        $sectorChartLabels = [];
        $sectorChartValues = [];
        $sectorChartPercentages = [];
        $sectorChartColors = [];

        foreach ($sectors as $s) {
            $sum = (float) Expenditure::where('fiscal_year', $activeYear)
                ->where('budget_sector_id', $s->id)
                ->whereIn('status', ['diverifikasi', 'dibayar'])
                ->sum('amount');

            $pct = $totalRealization > 0 ? round(($sum / $totalRealization) * 100, 1) : 0.0;

            $sectorChartLabels[] = $s->name;
            $sectorChartValues[] = $sum;
            $sectorChartPercentages[] = $pct;
            $sectorChartColors[] = $s->color;
        }

        // Recent Transaction Details
        $recentTransactions = collect();

        $receiptItems = Receipt::with('village')
            ->where('fiscal_year', $activeYear)
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'date' => $r->date,
                    'transaction_number' => $r->transaction_number,
                    'village_name' => $r->village?->name ?? '-',
                    'type' => 'Penerimaan',
                    'type_badge' => 'bg-emerald-100 text-emerald-800',
                    'category' => $r->funding_source,
                    'description' => $r->description,
                    'amount' => $r->amount,
                    'status' => $r->status,
                    'status_badge' => $r->status_badge,
                ];
            });

        $expenditureItems = Expenditure::with(['village', 'sector'])
            ->where('fiscal_year', $activeYear)
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function ($e) {
                return [
                    'date' => $e->date,
                    'transaction_number' => $e->transaction_number,
                    'village_name' => $e->village?->name ?? '-',
                    'type' => 'Pengeluaran',
                    'type_badge' => 'bg-rose-100 text-rose-800',
                    'category' => $e->sector?->name ?? 'Belanja Desa',
                    'description' => $e->description,
                    'amount' => $e->amount,
                    'status' => $e->status,
                    'status_badge' => $e->status_badge,
                ];
            });

        $recentTransactions = $receiptItems->concat($expenditureItems)->sortByDesc('date')->values();

        // Strategic Notifications & Activity Schedules
        $pendingDisbursementsCount = DisbursementApplication::where('status', 'menunggu_verifikasi')->count();
        $revisionCount = DisbursementApplication::where('status', 'perlu_revisi')->count();

        $schedules = ActivitySchedule::orderBy('deadline_date', 'asc')->take(4)->get();
        $notifications = Notification::where('role_target', 'pimpinan')->orWhereNull('role_target')->latest()->take(5)->get();

        return view('pimpinan.dashboard', compact(
            'institution',
            'activeYear',
            'totalBudget',
            'totalRealization',
            'totalReceipts',
            'totalExpenditures',
            'overallAbsorption',
            'remainingBudget',
            'villages',
            'villageChartLabels',
            'villageBudgetValues',
            'villageRealizationValues',
            'villageRankings',
            'sectors',
            'sectorChartLabels',
            'sectorChartValues',
            'sectorChartPercentages',
            'sectorChartColors',
            'recentTransactions',
            'pendingDisbursementsCount',
            'revisionCount',
            'schedules',
            'notifications'
        ));
    }

    public function transactions(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();

        $type = $request->input('type', 'all');
        $selectedVillageId = $request->input('village_id');
        $search = $request->input('search');

        $receiptQuery = Receipt::with('village')->where('fiscal_year', $activeYear);
        $expenditureQuery = Expenditure::with(['village', 'sector'])->where('fiscal_year', $activeYear);

        if ($selectedVillageId) {
            $receiptQuery->where('village_id', $selectedVillageId);
            $expenditureQuery->where('village_id', $selectedVillageId);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $receiptQuery->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            $expenditureQuery->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
        }

        if ($search) {
            $receiptQuery->where(fn ($q) => $q->where('transaction_number', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
            $expenditureQuery->where(fn ($q) => $q->where('transaction_number', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhere('activity_name', 'like', "%{$search}%"));
        }

        $allTransactions = collect();

        if ($type === 'all' || $type === 'receipts') {
            $receipts = $receiptQuery->get()->map(function ($r) {
                return (object) [
                    'date' => $r->date,
                    'transaction_number' => $r->transaction_number,
                    'village' => $r->village,
                    'type' => 'Penerimaan',
                    'type_badge' => 'bg-emerald-100 text-emerald-800',
                    'category' => $r->funding_source,
                    'description' => $r->description,
                    'amount' => $r->amount,
                    'supporting_document' => $r->supporting_document,
                    'status' => $r->status,
                    'status_badge' => $r->status_badge,
                ];
            });
            $allTransactions = $allTransactions->concat($receipts);
        }

        if ($type === 'all' || $type === 'expenditures') {
            $expenditures = $expenditureQuery->get()->map(function ($e) {
                return (object) [
                    'date' => $e->date,
                    'transaction_number' => $e->transaction_number,
                    'village' => $e->village,
                    'type' => 'Pengeluaran',
                    'type_badge' => 'bg-rose-100 text-rose-800',
                    'category' => $e->sector?->name ?? 'Belanja Desa',
                    'description' => $e->description,
                    'amount' => $e->amount,
                    'supporting_document' => $e->supporting_document,
                    'status' => $e->status,
                    'status_badge' => $e->status_badge,
                ];
            });
            $allTransactions = $allTransactions->concat($expenditures);
        }

        $transactions = $allTransactions->sortByDesc('date')->values();

        return view('pimpinan.transactions', compact(
            'transactions',
            'villages',
            'sectors',
            'activeYear',
            'type',
            'selectedVillageId'
        ));
    }

    public function monitoring(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $villages = Village::with('apbdes')->where('is_active', true)->get();

        $monitoringData = [];
        foreach ($villages as $v) {
            $apbdes = $v->getApbdesForYear($activeYear);
            $budget = $apbdes ? (float) $apbdes->total_expenditure_budget : 0;
            $realized = $v->getTotalExpendituresForYear($activeYear);
            $receipts = $v->getTotalReceiptsForYear($activeYear);
            $remaining = max(0, $budget - $realized);
            $rate = $budget > 0 ? round(($realized / $budget) * 100, 1) : 0;

            // Latest disbursement status
            $latestDisb = DisbursementApplication::where('village_id', $v->id)
                ->where('fiscal_year', $activeYear)
                ->latest()
                ->first();

            $monitoringData[] = [
                'village' => $v,
                'apbdes' => $apbdes,
                'budget' => $budget,
                'receipts' => $receipts,
                'realized' => $realized,
                'remaining' => $remaining,
                'rate' => $rate,
                'disbursement' => $latestDisb,
            ];
        }

        return view('pimpinan.monitoring', compact('institution', 'activeYear', 'monitoringData'));
    }
}
