<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Village;
use App\Models\VillageAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageAssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = VillageAsset::with('village')->latest();

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->input('village_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $assets = $query->paginate(15)->withQueryString();
        $villages = Village::where('is_active', true)->get();
        $totalAssetValue = (float) $query->sum('acquisition_value');

        $categories = [
            'Tanah',
            'Peralatan dan Mesin',
            'Gedung dan Bangunan',
            'Jalan, Irigasi, dan Jaringan',
            'Aset Tetap Lainnya',
        ];

        return view('staff.assets.index', compact('assets', 'villages', 'categories', 'totalAssetValue'));
    }

    public function create(): View
    {
        $villages = Village::where('is_active', true)->get();
        $categories = [
            'Tanah',
            'Peralatan dan Mesin',
            'Gedung dan Bangunan',
            'Jalan, Irigasi, dan Jaringan',
            'Aset Tetap Lainnya',
        ];

        return view('staff.assets.create', compact('villages', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'acquisition_year' => 'required|integer|min:1980|max:' . date('Y'),
            'acquisition_value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'maintenance_notes' => 'nullable|string',
        ]);

        $village = Village::findOrFail($request->input('village_id'));
        $assetCode = 'AST/' . $village->code . '/' . str_pad(VillageAsset::where('village_id', $village->id)->count() + 1, 3, '0', STR_PAD_LEFT);

        $asset = VillageAsset::create([
            'village_id' => $village->id,
            'asset_code' => $assetCode,
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'acquisition_year' => $request->input('acquisition_year'),
            'acquisition_value' => $request->input('acquisition_value'),
            'quantity' => $request->input('quantity'),
            'unit' => $request->input('unit'),
            'location' => $request->input('location'),
            'condition' => $request->input('condition'),
            'maintenance_notes' => $request->input('maintenance_notes'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $village->id,
            'action' => 'CATAT_ASET',
            'description' => 'Mencatat aset desa baru: ' . $asset->name . ' (' . $asset->asset_code . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.assets.index')->with('success', 'Aset desa berhasil ditambahkan ke inventaris.');
    }

    public function edit(int $id): View
    {
        $asset = VillageAsset::findOrFail($id);
        $villages = Village::where('is_active', true)->get();
        $categories = [
            'Tanah',
            'Peralatan dan Mesin',
            'Gedung dan Bangunan',
            'Jalan, Irigasi, dan Jaringan',
            'Aset Tetap Lainnya',
        ];

        return view('staff.assets.edit', compact('asset', 'villages', 'categories'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $asset = VillageAsset::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'acquisition_year' => 'required|integer|min:1980|max:' . date('Y'),
            'acquisition_value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'maintenance_notes' => 'nullable|string',
        ]);

        $asset->update($request->only([
            'name',
            'category',
            'acquisition_year',
            'acquisition_value',
            'quantity',
            'unit',
            'location',
            'condition',
            'maintenance_notes',
        ]));

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $asset->village_id,
            'action' => 'UBAH_ASET',
            'description' => 'Memperbarui data inventaris aset ' . $asset->asset_code,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('staff.assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $asset = VillageAsset::findOrFail($id);
        $code = $asset->asset_code;
        $name = $asset->name;
        $villageId = $asset->village_id;

        $asset->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'village_id' => $villageId,
            'action' => 'HAPUS_ASET',
            'description' => 'Menghapus aset ' . $name . ' (' . $code . ') dari inventaris',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('staff.assets.index')->with('success', 'Aset desa berhasil dihapus dari inventaris.');
    }
}
