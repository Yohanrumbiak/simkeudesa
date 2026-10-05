<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivitySchedule;
use App\Models\Apbdes;
use App\Models\BudgetSector;
use App\Models\DisbursementApplication;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Receipt;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = $request->input('year', $institution?->active_fiscal_year ?? 2026);

        // Summary Cards for all villages
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

        $overallAbsorption = $totalBudget > 0 ? round(($totalRealization / $totalBudget) * 100, 1) : 0;
        $remainingBudget = max(0, $totalBudget - $totalRealization);

        // Chart 1: Budget vs Realization per Village
        $villages = Village::where('is_active', true)->get();
        $villageChartLabels = [];
        $villageBudgetValues = [];
        $villageRealizationValues = [];

        foreach ($villages as $v) {
            $vBudget = $v->getTotalBudgetForYear($activeYear);
            $vRealization = $v->getTotalExpendituresForYear($activeYear);

            $villageChartLabels[] = $v->name;
            $villageBudgetValues[] = $vBudget;
            $villageRealizationValues[] = $vRealization;
        }

        // Chart 2: Expenditure Composition by 5 Sectors
        $sectors = BudgetSector::all();
        $sectorChartLabels = [];
        $sectorChartValues = [];
        $sectorChartColors = [];

        foreach ($sectors as $s) {
            $sectorSum = (float) Expenditure::where('fiscal_year', $activeYear)
                ->where('budget_sector_id', $s->id)
                ->whereIn('status', ['diverifikasi', 'dibayar'])
                ->sum('amount');

            $sectorChartLabels[] = $s->name;
            $sectorChartValues[] = $sectorSum;
            $sectorChartColors[] = $s->color;
        }

        // Recent Transactions (Combined Receipts & Expenditures)
        $recentReceipts = Receipt::with('village')
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
                    'amount_sign' => '+',
                    'status' => $r->status,
                    'status_badge' => $r->status_badge,
                ];
            });

        $recentExpenditures = Expenditure::with(['village', 'sector'])
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
                    'amount_sign' => '-',
                    'status' => $e->status,
                    'status_badge' => $e->status_badge,
                ];
            });

        $recentTransactions = $recentReceipts->concat($recentExpenditures)
            ->sortByDesc('date')
            ->take(8)
            ->values();

        // Disbursement Applications awaiting verification
        $pendingDisbursements = DisbursementApplication::with('village')
            ->where('status', 'menunggu_verifikasi')
            ->latest()
            ->take(5)
            ->get();

        $revisionDisbursements = DisbursementApplication::with('village')
            ->where('status', 'perlu_revisi')
            ->latest()
            ->take(3)
            ->get();

        // Activity Schedules & Deadlines
        $schedules = ActivitySchedule::orderBy('deadline_date', 'asc')
            ->take(5)
            ->get();

        return view('staff.dashboard', compact(
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
            'sectorChartLabels',
            'sectorChartValues',
            'sectorChartColors',
            'recentTransactions',
            'pendingDisbursements',
            'revisionDisbursements',
            'schedules'
        ));
    }
}
