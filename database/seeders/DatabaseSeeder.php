<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\ActivitySchedule;
use App\Models\Apbdes;
use App\Models\ApbdesItem;
use App\Models\BudgetSector;
use App\Models\DisbursementApplication;
use App\Models\DisbursementDocument;
use App\Models\DisbursementReview;
use App\Models\Expenditure;
use App\Models\FiscalYear;
use App\Models\Institution;
use App\Models\Notification;
use App\Models\Receipt;
use App\Models\User;
use App\Models\Village;
use App\Models\VillageAsset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Institution
        $institution = Institution::create([
            'name' => 'Pemerintah Kabupaten Bandung Barat',
            'agency' => 'Dinas Pemberdayaan Masyarakat dan Desa (DPMD)',
            'system_name' => 'SIMKeuDesa',
            'system_subtitle' => 'Sistem Informasi Manajemen Keuangan Desa Terpadu',
            'address' => 'Jl. Raya Padalarang No. 45, Kompleks Perkantoran Pemkab Bandung Barat',
            'phone' => '(022) 686-2345',
            'email' => 'dpmd@bandungbaratkab.go.id',
            'active_fiscal_year' => 2026,
        ]);

        // 2. Fiscal Years
        FiscalYear::create(['year' => 2025, 'description' => 'Tahun Anggaran 2025 (Tutup Buku)', 'is_active' => false]);
        FiscalYear::create(['year' => 2026, 'description' => 'Tahun Anggaran Berjalan 2026', 'is_active' => true]);

        // 3. 5 Standard APBDes Budget Sectors
        $sectors = [
            [
                'code' => '01',
                'name' => 'Bidang Penyelenggaraan Pemerintahan Desa',
                'description' => 'Penghasilan tetap aparatur, operasional BPD, RT/RW, dan tata praja desa.',
                'color' => '#2563eb', // Blue
                'icon' => 'fa-landmark',
            ],
            [
                'code' => '02',
                'name' => 'Bidang Pelaksanaan Pembangunan Desa',
                'description' => 'Infrastruktur pemukiman, drainase, jalan desa, irigasi, dan sanitasi lingkungan.',
                'color' => '#059669', // Emerald
                'icon' => 'fa-trowel-bricks',
            ],
            [
                'code' => '03',
                'name' => 'Bidang Pembinaan Kemasyarakatan Desa',
                'description' => 'Ketentraman, ketertiban, kebudayaan, keagamaan, kepemudaan dan olahraga.',
                'color' => '#d97706', // Amber
                'icon' => 'fa-users',
            ],
            [
                'code' => '04',
                'name' => 'Bidang Pemberdayaan Masyarakat Desa',
                'description' => 'Peningkatan kapasitas petani, permodalan BUMDes, pelatihan UMKM, posyandu.',
                'color' => '#7c3aed', // Violet
                'icon' => 'fa-seedling',
            ],
            [
                'code' => '05',
                'name' => 'Bidang Penanggulangan Bencana, Darurat & Mendesak Desa',
                'description' => 'Penanganan bencana alam, bantuan darurat, dan BLT Dana Desa.',
                'color' => '#dc2626', // Red
                'icon' => 'fa-shield-halved',
            ],
        ];

        $sectorModels = [];
        foreach ($sectors as $s) {
            $sectorModels[$s['code']] = BudgetSector::create($s);
        }

        // 4. Villages (6 Villages)
        $villagesData = [
            [
                'code' => '32.17.06.2001',
                'name' => 'Desa Sukamaju',
                'subdistrict' => 'Kecamatan Cisarua',
                'head_name' => 'H. Asep Sunandar, S.Sos',
                'phone' => '0812-3456-7890',
                'address' => 'Jl. Kolonel Masturi No. 124, Desa Sukamaju',
                'bank_name' => 'Bank BJB',
                'bank_account_number' => '0012-3456-78901',
                'bank_account_holder' => 'KAS DESA SUKAMAJU',
                'is_active' => true,
            ],
            [
                'code' => '32.17.08.2002',
                'name' => 'Desa Makmur Jaya',
                'subdistrict' => 'Kecamatan Lembang',
                'head_name' => 'Drs. Dadang Suryana',
                'phone' => '0813-9876-5432',
                'address' => 'Jl. Raya Tangkuban Parahu No. 88, Desa Makmur Jaya',
                'bank_name' => 'Bank BJB',
                'bank_account_number' => '0012-3456-78902',
                'bank_account_holder' => 'KAS DESA MAKMUR JAYA',
                'is_active' => true,
            ],
            [
                'code' => '32.17.08.2003',
                'name' => 'Desa Cibodas',
                'subdistrict' => 'Kecamatan Lembang',
                'head_name' => 'Hj. Neneng Hasanah, SE',
                'phone' => '0815-4433-2211',
                'address' => 'Jl. Maribaya No. 45, Desa Cibodas',
                'bank_name' => 'Bank BJB',
                'bank_account_number' => '0012-3456-78903',
                'bank_account_holder' => 'KAS DESA CIBODAS',
                'is_active' => true,
            ],
            [
                'code' => '32.17.07.2004',
                'name' => 'Desa Cempaka Putih',
                'subdistrict' => 'Kecamatan Parongpong',
                'head_name' => 'Wawan Setiawan, S.Pd',
                'phone' => '0821-6677-8899',
                'address' => 'Jl. Cihanjuang Km 4.5, Desa Cempaka Putih',
                'bank_name' => 'Bank BJB',
                'bank_account_number' => '0012-3456-78904',
                'bank_account_holder' => 'KAS DESA CEMPAKA PUTIH',
                'is_active' => true,
            ],
            [
                'code' => '32.17.01.2005',
                'name' => 'Desa Margahayu',
                'subdistrict' => 'Kecamatan Padalarang',
                'head_name' => 'Deden Kurnia, ST',
                'phone' => '0857-1122-3344',
                'address' => 'Jl. Rancabali No. 12, Desa Margahayu',
                'bank_name' => 'Bank BJB',
                'bank_account_number' => '0012-3456-78905',
                'bank_account_holder' => 'KAS DESA MARGAHAYU',
                'is_active' => true,
            ],
            [
                'code' => '32.17.01.2006',
                'name' => 'Desa Jayamekar',
                'subdistrict' => 'Kecamatan Padalarang',
                'head_name' => 'Bambang Irawan',
                'phone' => '0878-5544-3322',
                'address' => 'Jl. Caringin No. 78, Desa Jayamekar',
                'bank_name' => 'Bank BJB',
                'bank_account_number' => '0012-3456-78906',
                'bank_account_holder' => 'KAS DESA JAYAMEKAR',
                'is_active' => true,
            ],
        ];

        $villages = [];
        foreach ($villagesData as $vd) {
            $villages[] = Village::create($vd);
        }

        // 5. Users
        // Admin / Pemda Staff
        $admin = User::create([
            'name' => 'Rudi Hermawan, S.STP, M.Si',
            'username' => 'admin_pemda',
            'nip' => '198503152010011005',
            'email' => 'admin@simkeudesa.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'position' => 'Kasie Fasilitasi Keuangan & Aset Desa - DPMD',
            'phone' => '0812-2233-4455',
            'is_active' => true,
        ]);

        // Regional Government Leaders (Pimpinan)
        $leader1 = User::create([
            'name' => 'Dr. H. Aris Munandar, M.Si',
            'username' => 'bupati_kbb',
            'nip' => '197204181997031002',
            'email' => 'bupati@bandungbaratkab.go.id',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'position' => 'Bupati Bandung Barat',
            'phone' => '0811-2233-445',
            'is_active' => true,
        ]);

        $leader2 = User::create([
            'name' => 'Drs. H. Dedi Supriyadi, M.AP',
            'username' => 'kadis_pmd',
            'nip' => '197608252000031004',
            'email' => 'kadis.pmd@bandungbaratkab.go.id',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'position' => 'Kepala Dinas PMD',
            'phone' => '0813-1122-334',
            'is_active' => true,
        ]);

        // Village Heads
        $kadesUsers = [];
        $kadesCredentials = [
            ['username' => 'kades_sukamaju', 'nip' => '197906122008011012', 'email' => 'sukamaju@desa.id', 'v_index' => 0],
            ['username' => 'kades_makmurjaya', 'nip' => '198102142009021008', 'email' => 'makmurjaya@desa.id', 'v_index' => 1],
            ['username' => 'kades_cibodas', 'nip' => '198305102011012015', 'email' => 'cibodas@desa.id', 'v_index' => 2],
            ['username' => 'kades_cempaka', 'nip' => '198601052014021003', 'email' => 'cempakaputih@desa.id', 'v_index' => 3],
            ['username' => 'kades_margahayu', 'nip' => '198411202012011009', 'email' => 'margahayu@desa.id', 'v_index' => 4],
            ['username' => 'kades_jayamekar', 'nip' => '198807192015031001', 'email' => 'jayamekar@desa.id', 'v_index' => 5],
        ];

        foreach ($kadesCredentials as $kc) {
            $v = $villages[$kc['v_index']];
            $kadesUsers[$kc['v_index']] = User::create([
                'name' => $v->head_name,
                'username' => $kc['username'],
                'nip' => $kc['nip'],
                'email' => $kc['email'],
                'password' => Hash::make('password'),
                'role' => 'kepala_desa',
                'village_id' => $v->id,
                'position' => 'Kepala ' . $v->name,
                'phone' => $v->phone,
                'is_active' => true,
            ]);
        }

        // 6. APBDes for each village (2026)
        $budgetsConfig = [
            0 => ['revenue' => 1850000000, 'expenditure' => 1850000000],
            1 => ['revenue' => 2100000000, 'expenditure' => 2100000000],
            2 => ['revenue' => 1950000000, 'expenditure' => 1950000000],
            3 => ['revenue' => 1720000000, 'expenditure' => 1720000000],
            4 => ['revenue' => 1680000000, 'expenditure' => 1680000000],
            5 => ['revenue' => 1550000000, 'expenditure' => 1550000000],
        ];

        foreach ($villages as $idx => $v) {
            $b = $budgetsConfig[$idx];
            $apbdes = Apbdes::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'title' => 'APBDes Induk T.A. 2026 ' . $v->name,
                'document_number' => 'PERDES/' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT) . '/2026',
                'type' => 'murni',
                'total_revenue_budget' => $b['revenue'],
                'total_expenditure_budget' => $b['expenditure'],
                'total_financing_budget' => 0,
                'status' => 'disetujui',
                'approval_date' => '2026-01-05',
                'approved_by' => $admin->id,
                'notes' => 'Dokumen APBDes telah dievaluasi dan disetujui oleh Tim Pembina DPMD Kabupaten.',
                'document_path' => 'documents/apbdes/apbdes_2026_' . $v->id . '.pdf',
            ]);

            // Revenue Items
            ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'pendapatan',
                'account_code' => '4.2.1',
                'activity_name' => 'Dana Desa (DD) Alokasi Pusat',
                'original_amount' => $b['revenue'] * 0.48,
                'revised_amount' => $b['revenue'] * 0.48,
            ]);
            ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'pendapatan',
                'account_code' => '4.2.2',
                'activity_name' => 'Alokasi Dana Desa (ADD) APBD Kabupaten',
                'original_amount' => $b['revenue'] * 0.32,
                'revised_amount' => $b['revenue'] * 0.32,
            ]);
            ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'pendapatan',
                'account_code' => '4.1.1',
                'activity_name' => 'Pendapatan Asli Desa (PADes) Hasil Usaha & Aset',
                'original_amount' => $b['revenue'] * 0.12,
                'revised_amount' => $b['revenue'] * 0.12,
            ]);
            ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'pendapatan',
                'account_code' => '4.2.3',
                'activity_name' => 'Bagi Hasil Pajak Daerah & Retribusi (PBH)',
                'original_amount' => $b['revenue'] * 0.08,
                'revised_amount' => $b['revenue'] * 0.08,
            ]);

            // Expenditure Items by 5 Sectors
            // 01: Pemerintahan Desa (approx 30%)
            $itemGov = ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'belanja',
                'budget_sector_id' => $sectorModels['01']->id,
                'account_code' => '5.1.1',
                'activity_name' => 'Penghasilan Tetap, Tunjangan & Operasional Pemdes serta BPD',
                'original_amount' => $b['expenditure'] * 0.30,
                'revised_amount' => $b['expenditure'] * 0.30,
            ]);

            // 02: Pembangunan Desa (approx 40%)
            $itemDev = ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'belanja',
                'budget_sector_id' => $sectorModels['02']->id,
                'account_code' => '5.2.1',
                'activity_name' => 'Peningkatan Jalan Rabat Beton, Drainase Lingkungan & Sanitasi',
                'original_amount' => $b['expenditure'] * 0.40,
                'revised_amount' => $b['expenditure'] * 0.40,
            ]);

            // 03: Pembinaan Kemasyarakatan (approx 10%)
            $itemSoc = ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'belanja',
                'budget_sector_id' => $sectorModels['03']->id,
                'account_code' => '5.3.1',
                'activity_name' => 'Kegiatan Pembinaan Karang Taruna, Seni Budaya & Pos Kamling',
                'original_amount' => $b['expenditure'] * 0.10,
                'revised_amount' => $b['expenditure'] * 0.10,
            ]);

            // 04: Pemberdayaan Masyarakat (approx 12%)
            $itemEmp = ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'belanja',
                'budget_sector_id' => $sectorModels['04']->id,
                'account_code' => '5.4.1',
                'activity_name' => 'Penguatan BUMDes, Pelatihan Kelompok Tani & Penanganan Stunting',
                'original_amount' => $b['expenditure'] * 0.12,
                'revised_amount' => $b['expenditure'] * 0.12,
            ]);

            // 05: Penanggulangan Bencana & Darurat (approx 8%)
            $itemDis = ApbdesItem::create([
                'apbdes_id' => $apbdes->id,
                'type' => 'belanja',
                'budget_sector_id' => $sectorModels['05']->id,
                'account_code' => '5.5.1',
                'activity_name' => 'Bantuan Langsung Tunai (BLT-DD) & Kesiapsiagaan Kebencanaan',
                'original_amount' => $b['expenditure'] * 0.08,
                'revised_amount' => $b['expenditure'] * 0.08,
            ]);

            // 7. Receipts (Penerimaan yang sudah masuk kas)
            // Realization varies: Sukamaju has 65% received, Makmur Jaya has 75%, Cibodas 60%, etc.
            $recMultiplier = [0.68, 0.76, 0.62, 0.58, 0.50, 0.45][$idx];
            Receipt::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'RCV/' . $v->code . '/2026/001',
                'date' => '2026-02-10',
                'funding_source' => 'Dana Desa (DD)',
                'description' => 'Pencairan Dana Desa Tahap I (40%) dari Rekening Kas Umum Negara',
                'amount' => $b['revenue'] * 0.48 * 0.40,
                'payer' => 'KPPN Bandung II / RKUN',
                'destination_account' => $v->bank_account_number,
                'supporting_document' => 'documents/receipts/sp2d_dd_01.pdf',
                'status' => 'diverifikasi',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            Receipt::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'RCV/' . $v->code . '/2026/002',
                'date' => '2026-03-05',
                'funding_source' => 'Alokasi Dana Desa (ADD)',
                'description' => 'Penyaluran ADD Triwulan I & II dari Kas Daerah Kabupaten',
                'amount' => $b['revenue'] * 0.32 * 0.50,
                'payer' => 'BPKAD Kabupaten Bandung Barat',
                'destination_account' => $v->bank_account_number,
                'supporting_document' => 'documents/receipts/sp2d_add_01.pdf',
                'status' => 'diverifikasi',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            Receipt::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'RCV/' . $v->code . '/2026/003',
                'date' => '2026-04-15',
                'funding_source' => 'Pendapatan Asli Desa (PADes)',
                'description' => 'Setoran bagi hasil BUMDes & sewa tanah kas desa',
                'amount' => $b['revenue'] * 0.12 * 0.60,
                'payer' => 'BUMDes Mitra Mandiri',
                'destination_account' => $v->bank_account_number,
                'supporting_document' => 'documents/receipts/setoran_bumdes.pdf',
                'status' => 'diverifikasi',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            // 8. Expenditures (Realisasi Belanja)
            // Sector 01: Siltap & Operasional
            Expenditure::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'EXP/' . $v->code . '/2026/001',
                'date' => '2026-03-28',
                'budget_sector_id' => $sectorModels['01']->id,
                'apbdes_item_id' => $itemGov->id,
                'activity_name' => 'Pembayaran Penghasilan Tetap (Siltap) Perangkat Desa Triwulan I',
                'description' => 'Gaji dan tunjangan kepala desa, sekdes, kaur, kasi, dan kadus',
                'amount' => $itemGov->original_amount * 0.45,
                'payee' => 'Aparatur Pemerintah ' . $v->name,
                'payment_method' => 'Transfer Bank',
                'supporting_document' => 'documents/expenditures/daftar_siltap_tw1.pdf',
                'status' => 'dibayar',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            // Sector 02: Pembangunan Jalan / Fisik
            Expenditure::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'EXP/' . $v->code . '/2026/002',
                'date' => '2026-04-20',
                'budget_sector_id' => $sectorModels['02']->id,
                'apbdes_item_id' => $itemDev->id,
                'activity_name' => 'Pekerjaan Rabat Beton Jalan Lingkungan RW 03 (Panjang 350m)',
                'description' => 'Pembelian semen, pasir, koral, sewa molen, dan upah padat karya tunai',
                'amount' => $itemDev->original_amount * 0.35,
                'payee' => 'TPK Desa ' . $v->name,
                'payment_method' => 'Transfer Bank',
                'supporting_document' => 'documents/expenditures/spk_rabat_beton.pdf',
                'status' => 'dibayar',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            // Sector 03: Pembinaan
            Expenditure::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'EXP/' . $v->code . '/2026/003',
                'date' => '2026-05-12',
                'budget_sector_id' => $sectorModels['03']->id,
                'apbdes_item_id' => $itemSoc->id,
                'activity_name' => 'Fasilitasi Kegiatan Porseni Pemuda & Pembinaan Linmas',
                'description' => 'Pengadaan seragam linmas, sarana olahraga karang taruna, dan konsumsi musyawarah',
                'amount' => $itemSoc->original_amount * 0.30,
                'payee' => 'Karang Taruna & Satlinmas Desa',
                'payment_method' => 'Tunai',
                'supporting_document' => 'documents/expenditures/kuitansi_linmas.pdf',
                'status' => 'dibayar',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            // Sector 04: Pemberdayaan
            Expenditure::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'EXP/' . $v->code . '/2026/004',
                'date' => '2026-06-08',
                'budget_sector_id' => $sectorModels['04']->id,
                'apbdes_item_id' => $itemEmp->id,
                'activity_name' => 'Pemberian Makanan Tambahan (PMT) Balita Stunting & Pelatihan Olahan Pangan',
                'description' => 'Bahan makanan bergizi untuk 25 balita, narasumber pelatihan UMKM desa',
                'amount' => $itemEmp->original_amount * 0.28,
                'payee' => 'Kader Posyandu & TPK Pemberdayaan',
                'payment_method' => 'Transfer Bank',
                'supporting_document' => 'documents/expenditures/pmt_stunting_lpj.pdf',
                'status' => 'dibayar',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            // Sector 05: BLT Desa
            Expenditure::create([
                'village_id' => $v->id,
                'fiscal_year' => 2026,
                'transaction_number' => 'EXP/' . $v->code . '/2026/005',
                'date' => '2026-06-25',
                'budget_sector_id' => $sectorModels['05']->id,
                'apbdes_item_id' => $itemDis->id,
                'activity_name' => 'Penyaluran Bantuan Langsung Tunai (BLT-DD) Bulan 1 s.d. 3',
                'description' => 'Penyaluran untuk 35 KPM @ Rp 300.000 per bulan selama 3 bulan',
                'amount' => $itemDis->original_amount * 0.40,
                'payee' => '35 Keluarga Penerima Manfaat (KPM)',
                'payment_method' => 'Tunai',
                'supporting_document' => 'documents/expenditures/daftar_penerima_blt.pdf',
                'status' => 'dibayar',
                'created_by' => $kadesUsers[$idx]->id,
            ]);

            // 9. Disbursement Applications
            // Village 0 (Sukamaju): Has one complete Tahap I, and Tahap II pending review
            if ($idx === 0) {
                // Application 1: Complete
                $app1 = DisbursementApplication::create([
                    'village_id' => $v->id,
                    'fiscal_year' => 2026,
                    'application_number' => 'SPP-DD-01/' . $v->code . '/2026',
                    'title' => 'Permohonan Penyaluran Dana Desa Tahap I T.A. 2026',
                    'phase' => 'Tahap I (40%)',
                    'requested_amount' => 355200000,
                    'submission_date' => '2026-01-20',
                    'status' => 'disalurkan',
                    'review_notes' => 'Dokumen lengkap, regulasi APBDes sesuai Perbup, dana telah ditransfer ke RKD.',
                    'verified_by' => $admin->id,
                    'verified_at' => '2026-02-05 10:15:00',
                ]);

                DisbursementDocument::create([
                    'disbursement_application_id' => $app1->id,
                    'document_name' => 'Surat Pengantar Permohonan Penyaluran',
                    'file_path' => 'uploads/disbursements/surat_pengantar_sukamaju_01.pdf',
                    'file_size' => 450200,
                    'file_type' => 'pdf',
                    'status' => 'lengkap',
                    'notes' => 'Telah ditandatangani basah oleh Kades.',
                ]);
                DisbursementDocument::create([
                    'disbursement_application_id' => $app1->id,
                    'document_name' => 'Perdes APBDes T.A. 2026 yang Sudah Diundangkan',
                    'file_path' => 'uploads/disbursements/perdes_apbdes_sukamaju.pdf',
                    'file_size' => 1250300,
                    'file_type' => 'pdf',
                    'status' => 'lengkap',
                    'notes' => 'Lengkap dengan lembaran daerah.',
                ]);
                DisbursementDocument::create([
                    'disbursement_application_id' => $app1->id,
                    'document_name' => 'Rencana Anggaran Biaya (RAB) Tahap I',
                    'file_path' => 'uploads/disbursements/rab_tahap1_sukamaju.pdf',
                    'file_size' => 840500,
                    'file_type' => 'pdf',
                    'status' => 'lengkap',
                    'notes' => 'Perhitungan volume pekerjaan sesuai standar harga daerah.',
                ]);

                // Application 2: Awaiting Review (menunggu_verifikasi)
                $app2 = DisbursementApplication::create([
                    'village_id' => $v->id,
                    'fiscal_year' => 2026,
                    'application_number' => 'SPP-DD-02/' . $v->code . '/2026',
                    'title' => 'Permohonan Penyaluran Dana Desa Tahap II (40%) T.A. 2026',
                    'phase' => 'Tahap II (40%)',
                    'requested_amount' => 355200000,
                    'submission_date' => '2026-07-01',
                    'status' => 'menunggu_verifikasi',
                    'review_notes' => 'Sedang dalam antrean telaah oleh tim verifikator bidang keuangan DPMD.',
                ]);

                DisbursementDocument::create([
                    'disbursement_application_id' => $app2->id,
                    'document_name' => 'Laporan Realisasi Penyerapan DD Tahap I (Minimal 50%)',
                    'file_path' => 'uploads/disbursements/lpj_tahap1_sukamaju.pdf',
                    'file_size' => 2100400,
                    'file_type' => 'pdf',
                    'status' => 'menunggu_verifikasi',
                    'notes' => 'Menunggu verifikasi fisik dan kesesuaian kuitansi.',
                ]);
                DisbursementDocument::create([
                    'disbursement_application_id' => $app2->id,
                    'document_name' => 'Laporan Capaian Output Kegiatan Fisik Tahap I (Minimal 35%)',
                    'file_path' => 'uploads/disbursements/capaian_output_sukamaju.pdf',
                    'file_size' => 1850000,
                    'file_type' => 'pdf',
                    'status' => 'menunggu_verifikasi',
                ]);
                DisbursementDocument::create([
                    'disbursement_application_id' => $app2->id,
                    'document_name' => 'Surat Pernyataan Tanggung Jawab Belanja (SPTJB)',
                    'file_path' => 'uploads/disbursements/sptjb_sukamaju.pdf',
                    'file_size' => 620000,
                    'file_type' => 'pdf',
                    'status' => 'menunggu_verifikasi',
                ]);
            } elseif ($idx === 1) {
                // Makmur Jaya: Needs Revision (perlu_revisi)
                $appRev = DisbursementApplication::create([
                    'village_id' => $v->id,
                    'fiscal_year' => 2026,
                    'application_number' => 'SPP-DD-01/' . $v->code . '/2026',
                    'title' => 'Permohonan Penyaluran Dana Desa Tahap II T.A. 2026',
                    'phase' => 'Tahap II (40%)',
                    'requested_amount' => 403200000,
                    'submission_date' => '2026-06-20',
                    'status' => 'perlu_revisi',
                    'review_notes' => 'Laporan realisasi output fisik tahap I belum melampirkan foto 0%, 50%, dan 100% pada kegiatan rabat beton RW 04. Mohon perbaiki dan unggah ulang dokumen pendukung.',
                    'verified_by' => $admin->id,
                    'verified_at' => '2026-06-25 14:30:00',
                ]);

                DisbursementDocument::create([
                    'disbursement_application_id' => $appRev->id,
                    'document_name' => 'Laporan Realisasi Penyerapan Dana Desa Tahap I',
                    'file_path' => 'uploads/disbursements/lpj_makmurjaya.pdf',
                    'file_size' => 1950000,
                    'file_type' => 'pdf',
                    'status' => 'lengkap',
                ]);
                DisbursementDocument::create([
                    'disbursement_application_id' => $appRev->id,
                    'document_name' => 'Laporan Capaian Output Kegiatan Tahap I',
                    'file_path' => 'uploads/disbursements/output_makmurjaya.pdf',
                    'file_size' => 1400000,
                    'file_type' => 'pdf',
                    'status' => 'perlu_revisi',
                    'notes' => 'Foto dokumentasi fisik 100% dan berita acara serah terima dari TPK belum ada.',
                ]);

                DisbursementReview::create([
                    'disbursement_application_id' => $appRev->id,
                    'reviewer_id' => $admin->id,
                    'status_before' => 'menunggu_verifikasi',
                    'status_after' => 'perlu_revisi',
                    'notes' => 'Dokumentasi progres fisik belum lengkap. Diminta revisi dalam waktu 7 hari kerja.',
                ]);
            } else {
                // Other villages: Complete Tahap I
                $appOther = DisbursementApplication::create([
                    'village_id' => $v->id,
                    'fiscal_year' => 2026,
                    'application_number' => 'SPP-DD-01/' . $v->code . '/2026',
                    'title' => 'Permohonan Penyaluran Dana Desa Tahap I T.A. 2026',
                    'phase' => 'Tahap I (40%)',
                    'requested_amount' => $b['revenue'] * 0.48 * 0.40,
                    'submission_date' => '2026-02-15',
                    'status' => 'lengkap',
                    'review_notes' => 'Kelengkapan dokumen memenuhi syarat penyaluran sesuai petunjuk teknis.',
                    'verified_by' => $admin->id,
                    'verified_at' => '2026-02-28 09:30:00',
                ]);

                DisbursementDocument::create([
                    'disbursement_application_id' => $appOther->id,
                    'document_name' => 'Berkas Persyaratan Penyaluran DD Tahap I',
                    'file_path' => 'uploads/disbursements/berkas_' . $v->code . '.pdf',
                    'file_size' => 2450000,
                    'file_type' => 'pdf',
                    'status' => 'lengkap',
                ]);
            }

            // 10. Village Assets
            VillageAsset::create([
                'village_id' => $v->id,
                'asset_code' => 'AST/' . $v->code . '/001',
                'name' => 'Gedung Kantor dan Balai Pertemuan Desa',
                'category' => 'Gedung dan Bangunan',
                'acquisition_year' => 2018,
                'acquisition_value' => 650000000,
                'quantity' => 1,
                'unit' => 'Unit',
                'location' => $v->address,
                'condition' => 'baik',
                'maintenance_notes' => 'Pemeliharaan rutin cat dan instalasi listrik pada awal tahun anggaran.',
            ]);

            VillageAsset::create([
                'village_id' => $v->id,
                'asset_code' => 'AST/' . $v->code . '/002',
                'name' => 'Mobil Ambulans Siaga Desa Suzuki APV',
                'category' => 'Peralatan dan Mesin',
                'acquisition_year' => 2021,
                'acquisition_value' => 245000000,
                'quantity' => 1,
                'unit' => 'Unit',
                'location' => 'Garasi Kantor Desa ' . $v->name,
                'condition' => 'baik',
                'maintenance_notes' => 'Servis berkala dan ganti oli setiap 5.000 km di bengkel resmi.',
            ]);

            VillageAsset::create([
                'village_id' => $v->id,
                'asset_code' => 'AST/' . $v->code . '/003',
                'name' => 'Perangkat Komputer PC & Server SIMDes',
                'category' => 'Peralatan dan Mesin',
                'acquisition_year' => 2023,
                'acquisition_value' => 52000000,
                'quantity' => 4,
                'unit' => 'Set',
                'location' => 'Ruang Pelayanan Umum & Sekdes',
                'condition' => 'baik',
                'maintenance_notes' => 'Terhubung UPS dan cadangan data otomatis.',
            ]);

            VillageAsset::create([
                'village_id' => $v->id,
                'asset_code' => 'AST/' . $v->code . '/004',
                'name' => 'Traktor Tangan Quick Capung Metal Pertanian',
                'category' => 'Peralatan dan Mesin',
                'acquisition_year' => 2020,
                'acquisition_value' => 38000000,
                'quantity' => 2,
                'unit' => 'Unit',
                'location' => 'Gudang Kelompok Tani Mandiri',
                'condition' => ($idx % 2 === 0 ? 'baik' : 'rusak_ringan'),
                'maintenance_notes' => 'Rantai dan vanbelt perlu penggantian suku cadang.',
            ]);
        }

        // 11. Activity Schedules (Jadwal Kegiatan & Batas Pelaporan)
        ActivitySchedule::create([
            'title' => 'Batas Akhir Pelaporan Realisasi DD Tahap I',
            'description' => 'Seluruh desa wajib mengunggah laporan penyerapan minimal 50% dan output fisik minimal 35% melalui SIMKeuDesa.',
            'category' => 'Batas Pelaporan',
            'deadline_date' => now()->addDays(12)->toDateString(),
            'status' => 'berlangsung',
        ]);

        ActivitySchedule::create([
            'title' => 'Musyawarah Perencanaan Pembangunan Desa (Musrenbangdes) RKPDes 2027',
            'description' => 'Penyusunan usulan prioritas pembangunan tahun berikutnya bersama BPD dan tokoh masyarakat.',
            'category' => 'Musyawarah',
            'deadline_date' => now()->addDays(24)->toDateString(),
            'status' => 'akan_datang',
        ]);

        ActivitySchedule::create([
            'title' => 'Verifikasi Lapangan Fisik & Administrasi Dana Desa Tahap II',
            'description' => 'Kunjungan tim monitoring DPMD dan Inspektorat Daerah ke lokasi pekerjaan padat karya.',
            'category' => 'Evaluasi',
            'deadline_date' => now()->addDays(38)->toDateString(),
            'status' => 'akan_datang',
        ]);

        ActivitySchedule::create([
            'title' => 'Rekonsiliasi Sisa Lebih Pembiayaan Anggaran (SiLPA) Triwulan II',
            'description' => 'Pencocokan rekening koran kas desa dengan buku kas umum di SIMKeuDesa.',
            'category' => 'Penyaluran Dana',
            'deadline_date' => now()->addDays(45)->toDateString(),
            'status' => 'akan_datang',
        ]);

        // 12. Notifications
        Notification::create([
            'village_id' => $villages[0]->id,
            'role_target' => 'admin',
            'title' => 'Permohonan Pencairan Baru: Desa Sukamaju',
            'message' => 'Desa Sukamaju mengajukan permohonan penyaluran Dana Desa Tahap II (40%) sebesar Rp 355.200.000.',
            'type' => 'info',
            'link' => '/staff/disbursements',
            'is_read' => false,
        ]);

        Notification::create([
            'village_id' => $villages[1]->id,
            'role_target' => 'kepala_desa',
            'user_id' => $kadesUsers[1]->id,
            'title' => 'Dokumen Penyaluran Perlu Revisi',
            'message' => 'Dokumen Capaian Output Fisik Tahap I memerlukan kelengkapan dokumentasi foto 0%, 50%, 100%.',
            'type' => 'warning',
            'link' => '/desa/disbursements',
            'is_read' => false,
        ]);

        Notification::create([
            'role_target' => 'pimpinan',
            'title' => 'Laporan Progres Realisasi Keuangan Desa Semester I Siap Dievaluasi',
            'message' => 'Tingkat penyerapan rata-rata APBDes seluruh desa di Kabupaten Bandung Barat telah mencapai 58.4%.',
            'type' => 'success',
            'link' => '/pimpinan/dashboard',
            'is_read' => false,
        ]);

        // 13. Activity Logs
        ActivityLog::create([
            'user_id' => $admin->id,
            'village_id' => null,
            'action' => 'LOGIN',
            'description' => 'Administrator masuk ke sistem SIMKeuDesa.',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'village_id' => $villages[0]->id,
            'action' => 'VERIFIKASI_APBDES',
            'description' => 'Menyetujui APBDes Induk T.A. 2026 Desa Sukamaju.',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $kadesUsers[0]->id,
            'village_id' => $villages[0]->id,
            'action' => 'UNGGAH_DOKUMEN',
            'description' => 'Mengajukan dokumen permohonan penyaluran Dana Desa Tahap II.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
