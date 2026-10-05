<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BudgetSector;
use App\Models\FiscalYear;
use App\Models\Institution;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $institution = Institution::first();
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();
        $sectors = BudgetSector::all();
        $villages = Village::all();
        $logs = ActivityLog::with('user')->latest()->take(20)->get();

        return view('staff.settings.index', compact('institution', 'fiscalYears', 'sectors', 'villages', 'logs'));
    }

    public function updateInstitution(Request $request): RedirectResponse
    {
        $institution = Institution::first();

        $request->validate([
            'name' => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'system_name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'active_fiscal_year' => 'required|integer',
        ]);

        $institution->update($request->only([
            'name',
            'agency',
            'system_name',
            'address',
            'phone',
            'email',
            'active_fiscal_year',
        ]));

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'PENGATURAN_INSTITUSI',
            'description' => 'Memperbarui profil lembaga dan tahun anggaran aktif',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Profil lembaga berhasil diperbarui.');
    }

    public function storeVillage(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|unique:villages,code',
            'name' => 'required|string|max:255',
            'subdistrict' => 'required|string|max:255',
            'head_name' => 'required|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:30',
        ]);

        Village::create($request->all());

        return back()->with('success', 'Master data desa baru berhasil ditambahkan.');
    }
}
