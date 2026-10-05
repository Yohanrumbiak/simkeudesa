<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\ApbdesItem;
use App\Models\BudgetSector;
use App\Models\Expenditure;
use App\Models\Institution;
use App\Models\Receipt;
use App\Models\Village;
use App\Models\VillageAsset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $reportType = $request->input('type', 'consolidated');
        $selectedVillageId = $request->input('village_id');

        $villages = Village::where('is_active', true)->get();
        $sectors = BudgetSector::all();

        $reportData = $this->generateReportData($reportType, $activeYear, $selectedVillageId, $request);

        return view('staff.reports.index', compact(
            'institution',
            'activeYear',
            'reportType',
            'selectedVillageId',
            'villages',
            'sectors',
            'reportData'
        ));
    }

    public function print(Request $request): View
    {
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $reportType = $request->input('type', 'consolidated');
        $selectedVillageId = $request->input('village_id');

        $villages = Village::where('is_active', true)->get();
        $selectedVillage = $selectedVillageId ? Village::find($selectedVillageId) : null;
        $reportData = $this->generateReportData($reportType, $activeYear, $selectedVillageId, $request);

        return view('staff.reports.print', compact(
            'institution',
            'activeYear',
            'reportType',
            'selectedVillage',
            'reportData'
        ));
    }

    public function exportExcel(Request $request): Response
    {
        $institution = Institution::first();
        $activeYear = (int) $request->input('year', $institution?->active_fiscal_year ?? 2026);
        $reportType = $request->input('type', 'consolidated');
        $selectedVillageId = $request->input('village_id');

        $reportData = $this->generateReportData($reportType, $activeYear, $selectedVillageId, $request);

        $filename = 'Laporan_' . ucfirst($reportType) . '_' . $activeYear . '_' . date('Ymd_His') . '.csv';

        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM for Indonesian Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Header info
        fputcsv($output, [$institution->name ?? 'SIMKeuDesa']);
        fputcsv($output, [$institution->agency ?? 'Dinas Pemberdayaan Masyarakat dan Desa']);
        fputcsv($output, ['LAPORAN: ' . strtoupper(str_replace('_', ' ', $reportType))]);
        fputcsv($output, ['Tahun Anggaran: ' . $activeYear]);
        fputcsv($output, []); // Empty row

        if ($reportType === 'consolidated' || $reportType === 'realization' || $reportType === 'remaining') {
            fputcsv($output, ['No', 'Kode Desa', 'Nama Desa', 'Kecamatan', 'Anggaran Belanja (Rp)', 'Realisasi Belanja (Rp)', 'Sisa Anggaran (Rp)', 'Penyerapan (%)']);
            $no = 1;
            foreach ($reportData['rows'] as $row) {
                fputcsv($output, [
                    $no++,
                    $row['village']->code,
                    $row['village']->name,
                    $row['village']->subdistrict,
                    $row['budget'],
                    $row['realized'],
                    $row['remaining'],
                    $row['percentage'] . '%',
                ]);
            }
            fputcsv($output, [
                'TOTAL',
                '',
                '',
                '',
                $reportData['totals']['budget'],
                $reportData['totals']['realized'],
                $reportData['totals']['remaining'],
                $reportData['totals']['percentage'] . '%',
            ]);
        } elseif ($reportType === 'receipts') {
            fputcsv($output, ['No', 'Tanggal', 'No Transaksi', 'Desa', 'Sumber Dana', 'Uraian', 'Penyetor', 'Nominal (Rp)', 'Status']);
            $no = 1;
            foreach ($reportData['rows'] as $r) {
                fputcsv($output, [
                    $no++,
                    $r->date->format('d/m/Y'),
                    $r->transaction_number,
                    $r->village?->name,
                    $r->funding_source,
                    $r->description,
                    $r->payer,
                    $r->amount,
                    $r->status,
                ]);
            }
        } elseif ($reportType === 'expenditures') {
            fputcsv($output, ['No', 'Tanggal', 'No Bukti', 'Desa', 'Bidang', 'Kegiatan', 'Uraian', 'Penerima', 'Metode', 'Nominal (Rp)']);
            $no = 1;
            foreach ($reportData['rows'] as $e) {
                fputcsv($output, [
                    $no++,
                    $e->date->format('d/m/Y'),
                    $e->transaction_number,
                    $e->village?->name,
                    $e->sector?->name,
                    $e->activity_name,
                    $e->description,
                    $e->payee,
                    $e->payment_method,
                    $e->amount,
                ]);
            }
        } elseif ($reportType === 'assets') {
            fputcsv($output, ['No', 'Kode Aset', 'Desa', 'Nama Barang', 'Kategori', 'Tahun', 'Nilai Perolehan (Rp)', 'Kondisi', 'Lokasi']);
            $no = 1;
            foreach ($reportData['rows'] as $a) {
                fputcsv($output, [
                    $no++,
                    $a->asset_code,
                    $a->village?->name,
                    $a->name,
                    $a->category,
                    $a->acquisition_year,
                    $a->acquisition_value,
                    ucfirst(str_replace('_', ' ', $a->condition)),
                    $a->location,
                ]);
            }
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function generateReportData(string $type, int $year, ?int $villageId, Request $request): array
    {
        $villages = Village::where('is_active', true)->get();
        if ($villageId) {
            $villages = $villages->where('id', $villageId);
        }

        if (in_array($type, ['consolidated', 'realization', 'remaining'], true)) {
            $rows = [];
            $totalBudget = 0;
            $totalRealized = 0;

            foreach ($villages as $v) {
                $b = $v->getTotalBudgetForYear($year);
                $r = $v->getTotalExpendituresForYear($year);
                $rem = max(0, $b - $r);
                $pct = $b > 0 ? round(($r / $b) * 100, 1) : 0;

                $rows[] = [
                    'village' => $v,
                    'budget' => $b,
                    'realized' => $r,
                    'remaining' => $rem,
                    'percentage' => $pct,
                ];

                $totalBudget += $b;
                $totalRealized += $r;
            }

            return [
                'type' => $type,
                'rows' => $rows,
                'totals' => [
                    'budget' => $totalBudget,
                    'realized' => $totalRealized,
                    'remaining' => max(0, $totalBudget - $totalRealized),
                    'percentage' => $totalBudget > 0 ? round(($totalRealized / $totalBudget) * 100, 1) : 0,
                ],
            ];
        }

        if ($type === 'receipts') {
            $q = Receipt::with('village')->where('fiscal_year', $year)->where('status', 'diverifikasi');
            if ($villageId) {
                $q->where('village_id', $villageId);
            }
            $rows = $q->orderBy('date', 'asc')->get();
            return [
                'type' => 'receipts',
                'rows' => $rows,
                'total_amount' => $rows->sum('amount'),
            ];
        }

        if ($type === 'expenditures') {
            $q = Expenditure::with(['village', 'sector'])->where('fiscal_year', $year)->whereIn('status', ['diverifikasi', 'dibayar']);
            if ($villageId) {
                $q->where('village_id', $villageId);
            }
            $rows = $q->orderBy('date', 'asc')->get();
            return [
                'type' => 'expenditures',
                'rows' => $rows,
                'total_amount' => $rows->sum('amount'),
            ];
        }

        if ($type === 'assets') {
            $q = VillageAsset::with('village');
            if ($villageId) {
                $q->where('village_id', $villageId);
            }
            $rows = $q->orderBy('acquisition_year', 'desc')->get();
            return [
                'type' => 'assets',
                'rows' => $rows,
                'total_amount' => $rows->sum('acquisition_value'),
            ];
        }

        // Default APBDes report
        $q = Apbdes::with(['village', 'items.sector'])->where('fiscal_year', $year);
        if ($villageId) {
            $q->where('village_id', $villageId);
        }
        $rows = $q->get();
        return [
            'type' => 'apbdes',
            'rows' => $rows,
        ];
    }
}
