<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\BudgetSector;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageApbdesController extends Controller
{
    public function index(Request $request): View
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);

        $apbdes = Apbdes::with(['items.sector', 'approver'])
            ->where('village_id', $village->id)
            ->where('fiscal_year', $activeYear)
            ->first();

        $revenueItems = $apbdes ? $apbdes->items->where('type', 'pendapatan') : collect();
        $expenditureItems = $apbdes ? $apbdes->items->where('type', 'belanja')->groupBy('budget_sector_id') : collect();
        $sectors = BudgetSector::all()->keyBy('id');

        return view('desa.apbdes.index', compact('village', 'apbdes', 'revenueItems', 'expenditureItems', 'sectors', 'activeYear'));
    }
}
