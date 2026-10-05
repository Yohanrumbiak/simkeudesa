<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\VillageAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageAssetController extends Controller
{
    public function index(Request $request): View
    {
        $village = auth()->user()->village;
        $query = VillageAsset::where('village_id', $village->id)->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        $assets = $query->paginate(12)->withQueryString();
        $totalAssetValue = (float) $query->sum('acquisition_value');

        $categories = [
            'Tanah',
            'Peralatan dan Mesin',
            'Gedung dan Bangunan',
            'Jalan, Irigasi, dan Jaringan',
            'Aset Tetap Lainnya',
        ];

        return view('desa.assets.index', compact('village', 'assets', 'categories', 'totalAssetValue'));
    }

    public function create(): View
    {
        $village = auth()->user()->village;
        $categories = [
            'Tanah',
            'Peralatan dan Mesin',
            'Gedung dan Bangunan',
            'Jalan, Irigasi, dan Jaringan',
            'Aset Tetap Lainnya',
        ];

        return view('desa.assets.create', compact('village', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $village = auth()->user()->village;

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
            'action' => 'TAMBAH_ASET_DESA',
            'description' => 'Menambahkan aset desa: ' . $asset->name . ' (' . $asset->asset_code . ')',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('desa.assets.index')->with('success', 'Aset desa berhasil ditambahkan.');
    }
}
