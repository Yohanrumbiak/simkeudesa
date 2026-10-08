<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\ActivitySchedule;
use App\Models\Apbdes;
use App\Models\BudgetSector;
use App\Models\DisbursementApplication;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Notification;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageHeadDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $village = $user->village;

        if (!$village) {
            abort(403, 'Akun Anda belum terhubung dengan data desa manapun.');
        }

        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);

        // Summary cards for this village only
        $apbdes = $village->getApbdesForYear($activeYear);
        $totalBudget = $apbdes ? (float) $apbdes->total_expenditure_budget : 0.0;
        $totalRealization = $village->getTotalExpendituresForYear($activeYear);
        $totalReceipts = $village->getTotalReceiptsForYear($activeYear);
        $totalExpenditures = $totalRealization;
        // $remainingBudget = max(0, $totalBudget - $totalRealization); 
        $remainingBudget = max(0, $totalBudget - $totalRealization);
        $absorptionRate = $totalBudget > 0 ? round(($totalRealization / $totalBudget) * 100, 1) : 0;

        // Disbursement application status & document completeness
        $latestDisbursement = DisbursementApplication::with(['documents', 'reviews.reviewer'])
            ->where('village_id', $village->id)
            ->where('fiscal_year', $activeYear)
            ->latest()
            ->first();

        $revisionAlerts = DisbursementApplication::where('village_id', $village->id)
            ->where('status', 'perlu_revisi')
            ->get();

        // Sector realization breakdown for this village
        $sectors = BudgetSector::all();
        $sectorChartLabels = [];
        $sectorChartValues = [];
        $sectorChartColors = [];

        foreach ($sectors as $s) {
            $sum = (float) Expenditure::where('village_id', $village->id)
                ->where('fiscal_year', $activeYear)
                ->where('budget_sector_id', $s->id)
                ->whereIn('status', ['diverifikasi', 'dibayar'])
                ->sum('amount');

            $sectorChartLabels[] = $s->name;
            $sectorChartValues[] = $sum;
            $sectorChartColors[] = $s->color;
        }

        // Recent village transactions (Receipts & Expenditures)
        $recentReceipts = Receipt::where('village_id', $village->id)
            ->where('fiscal_year', $activeYear)
            ->latest('date')
            ->take(4)
            ->get()
            ->map(function ($r) {
                return [
                    'date' => $r->date,
                    'transaction_number' => $r->transaction_number,
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

        $recentExpenditures = Expenditure::with('sector')
            ->where('village_id', $village->id)
            ->where('fiscal_year', $activeYear)
            ->latest('date')
            ->take(4)
            ->get()
            ->map(function ($e) {
                return [
                    'date' => $e->date,
                    'transaction_number' => $e->transaction_number,
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

        $recentTransactions = $recentReceipts->concat($recentExpenditures)->sortByDesc('date')->values();

        // Schedules
        $schedules = ActivitySchedule::orderBy('deadline_date', 'asc')->take(4)->get();

        // Notifications for this village
        $notifications = Notification::where('village_id', $village->id)
            ->where('role_target', 'kepala_desa')
            ->latest()
            ->take(5)
            ->get();

        return view('desa.dashboard', compact(
            'village',
            'institution',
            'activeYear',
            'apbdes',
            'totalBudget',
            'totalRealization',
            'totalReceipts',
            'totalExpenditures',
            'remainingBudget',
            'absorptionRate',
            'latestDisbursement',
            'revisionAlerts',
            'sectorChartLabels',
            'sectorChartValues',
            'sectorChartColors',
            'recentTransactions',
            'schedules',
            'notifications'
        ));
    }
}
