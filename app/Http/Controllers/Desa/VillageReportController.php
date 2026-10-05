<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\BudgetSector;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Receipt;
use App\Models\VillageAsset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VillageReportController extends Controller
{
    public function index(Request $request): View
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $reportType = $request->input('type', 'realization');

        $reportData = $this->generateVillageReport($village, $reportType, $activeYear);

        return view('desa.reports.index', compact('village', 'institution', 'activeYear', 'reportType', 'reportData'));
    }

    public function print(Request $request): View
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $reportType = $request->input('type', 'realization');

        $reportData = $this->generateVillageReport($village, $reportType, $activeYear);

        return view('desa.reports.print', compact('village', 'institution', 'activeYear', 'reportType', 'reportData'));
    }

    public function exportExcel(Request $request): Response
    {
        $village = auth()->user()->village;
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $reportType = $request->input('type', 'realization');

        $reportData = $this->generateVillageReport($village, $reportType, $activeYear);

        $filename = 'Laporan_' . ucfirst($reportType) . '_' . str_replace(' ', '_', $village->name) . '_' . $activeYear . '.csv';

        $output = fopen('php://temp', 'r+');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['PEMERINTAH ' . strtoupper($village->name)]);
        fputcsv($output, ['KECAMATAN ' . strtoupper($village->subdistrict)]);
        fputcsv($output, ['LAPORAN: ' . strtoupper($reportType) . ' T.A. ' . $activeYear]);
        fputcsv($output, []);

        if ($reportType === 'realization') {
            fputcsv($output, ['Kode Bidang', 'Nama Bidang Anggaran', 'Realisasi Belanja (Rp)']);
            foreach ($reportData['sectors'] as $s) {
                fputcsv($output, [$s['sector']->code, $s['sector']->name, $s['realized']]);
            }
            fputcsv($output, ['TOTAL REALISASI', '', $reportData['total_realized']]);
        } elseif ($reportType === 'receipts') {
            fputcsv($output, ['Tanggal', 'No Transaksi', 'Sumber Dana', 'Uraian', 'Penyetor', 'Nominal (Rp)']);
            foreach ($reportData['rows'] as $r) {
                fputcsv($output, [$r->date->format('d/m/Y'), $r->transaction_number, $r->funding_source, $r->description, $r->payer, $r->amount]);
            }
        } elseif ($reportType === 'expenditures') {
            fputcsv($output, ['Tanggal', 'No Bukti', 'Bidang', 'Kegiatan', 'Uraian', 'Penerima', 'Nominal (Rp)']);
            foreach ($reportData['rows'] as $e) {
                fputcsv($output, [$e->date->format('d/m/Y'), $e->transaction_number, $e->sector?->name, $e->activity_name, $e->description, $e->payee, $e->amount]);
            }
        } elseif ($reportType === 'assets') {
            fputcsv($output, ['Kode Aset', 'Nama Barang', 'Kategori', 'Tahun', 'Nilai Perolehan (Rp)', 'Kondisi', 'Lokasi']);
            foreach ($reportData['rows'] as $a) {
                fputcsv($output, [$a->asset_code, $a->name, $a->category, $a->acquisition_year, $a->acquisition_value, $a->condition, $a->location]);
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function generateVillageReport($village, string $type, int $year): array
    {
        if ($type === 'realization') {
            $apbdes = $village->getApbdesForYear($year);
            $totalBudget = $apbdes ? (float) $apbdes->total_expenditure_budget : 0;
            $sectors = BudgetSector::all();
            $sectorData = [];
            $totalRealized = 0;

            foreach ($sectors as $s) {
                $sum = (float) Expenditure::where('village_id', $village->id)
                    ->where('fiscal_year', $year)
                    ->where('budget_sector_id', $s->id)
                    ->whereIn('status', ['diverifikasi', 'dibayar'])
                    ->sum('amount');

                $sectorData[] = [
                    'sector' => $s,
                    'realized' => $sum,
                ];
                $totalRealized += $sum;
            }

            return [
                'type' => 'realization',
                'apbdes' => $apbdes,
                'total_budget' => $totalBudget,
                'total_realized' => $totalRealized,
                'remaining' => max(0, $totalBudget - $totalRealized),
                'absorption' => $totalBudget > 0 ? round(($totalRealized / $totalBudget) * 100, 1) : 0,
                'sectors' => $sectorData,
            ];
        }

        if ($type === 'receipts') {
            $rows = Receipt::where('village_id', $village->id)->where('fiscal_year', $year)->orderBy('date', 'asc')->get();
            return ['type' => 'receipts', 'rows' => $rows, 'total' => $rows->sum('amount')];
        }

        if ($type === 'expenditures') {
            $rows = Expenditure::with('sector')->where('village_id', $village->id)->where('fiscal_year', $year)->orderBy('date', 'asc')->get();
            return ['type' => 'expenditures', 'rows' => $rows, 'total' => $rows->sum('amount')];
        }

        if ($type === 'assets') {
            $rows = VillageAsset::where('village_id', $village->id)->orderBy('acquisition_year', 'desc')->get();
            return ['type' => 'assets', 'rows' => $rows, 'total' => $rows->sum('acquisition_value')];
        }

        $apbdes = $village->getApbdesForYear($year);
        return ['type' => 'apbdes', 'apbdes' => $apbdes];
    }
}
