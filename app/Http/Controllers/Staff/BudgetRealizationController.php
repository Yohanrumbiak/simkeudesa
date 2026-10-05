<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\BudgetSector;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetRealizationController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $selectedVillageId = $request->input('village_id');
        $selectedSectorId = $request->input('sector_id');

        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();

        // Calculate Realization Data per Village
        $realizationData = [];
        $grandTotalBudget = 0;
        $grandTotalRealized = 0;

        $targetVillages = $selectedVillageId ? $villages->where('id', $selectedVillageId) : $villages;

        foreach ($targetVillages as $v) {
            $budget = $v->getTotalBudgetForYear($activeYear);

            $expQuery = Expenditure::where('village_id', $v->id)
                ->where('fiscal_year', $activeYear)
                ->whereIn('status', ['diverifikasi', 'dibayar']);

            if ($selectedSectorId) {
                $expQuery->where('budget_sector_id', $selectedSectorId);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $expQuery->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
            }

            $realized = (float) $expQuery->sum('amount');
            $remaining = max(0, $budget - $realized);
            $percentage = $budget > 0 ? round(($realized / $budget) * 100, 1) : 0;

            $realizationData[] = [
                'village' => $v,
                'budget' => $budget,
                'realized' => $realized,
                'remaining' => $remaining,
                'percentage' => $percentage,
            ];

            $grandTotalBudget += $budget;
            $grandTotalRealized += $realized;
        }

        $grandRemaining = max(0, $grandTotalBudget - $grandTotalRealized);
        $grandPercentage = $grandTotalBudget > 0 ? round(($grandTotalRealized / $grandTotalBudget) * 100, 1) : 0;

        // Sector breakdown
        $sectorBreakdown = [];
        foreach ($sectors as $s) {
            $sectorExpQuery = Expenditure::where('fiscal_year', $activeYear)
                ->where('budget_sector_id', $s->id)
                ->whereIn('status', ['diverifikasi', 'dibayar']);

            if ($selectedVillageId) {
                $sectorExpQuery->where('village_id', $selectedVillageId);
            }

            $sectorSum = (float) $sectorExpQuery->sum('amount');
            $sectorBreakdown[] = [
                'sector' => $s,
                'realized' => $sectorSum,
                'percentage' => $grandTotalRealized > 0 ? round(($sectorSum / $grandTotalRealized) * 100, 1) : 0,
            ];
        }

        return view('staff.realization.index', compact(
            'realizationData',
            'villages',
            'sectors',
            'activeYear',
            'selectedVillageId',
            'selectedSectorId',
            'grandTotalBudget',
            'grandTotalRealized',
            'grandRemaining',
            'grandPercentage',
            'sectorBreakdown'
        ));
    }
}
