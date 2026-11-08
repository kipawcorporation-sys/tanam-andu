<?php
/**
 * AdminReportController — TanamPohon.id
 * Laporan ringkasan: pendapatan, pohon tertanam, perbandingan bulan.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // Revenue per bulan (12 bulan terakhir)
        $monthlyRevenue = $db->query("
            SELECT
                DATE_FORMAT(paid_at, '%Y-%m') AS month,
                DATE_FORMAT(paid_at, '%b %Y') AS label,
                SUM(grand_total) AS total,
                COUNT(*) AS tx_count
            FROM payments
            WHERE status = 'berhasil' AND paid_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Pohon per spesies dengan serapan CO2 & produksi O2
        $treesBySpecies = $db->query("
            SELECT ts.name, COUNT(tr.id) AS count,
                   COALESCE(SUM(ts.co2_absorption_kg_per_year * 10), 0) AS co2_kg,
                   COALESCE(SUM(ts.oxygen_production_kg_per_year * 10), 0) AS o2_kg
            FROM tree_species ts
            LEFT JOIN trees tr ON tr.tree_species_id = ts.id
            GROUP BY ts.id ORDER BY count DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Sebaran penanaman per lokasi
        $treesByLocation = $db->query("
            SELECT pl.name, pl.city, pl.province,
                   COUNT(tr.id) AS trees_planted,
                   pl.total_trees_target
            FROM planting_locations pl
            LEFT JOIN trees tr ON tr.planting_location_id = pl.id
            GROUP BY pl.id
            ORDER BY trees_planted DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Komposisi pendapatan (Donasi vs Toko Kopi/Produk vs Peserta)
        $revenueByType = $db->query("
            SELECT payable_type, SUM(grand_total) AS total, COUNT(*) AS count
            FROM payments
            WHERE status = 'berhasil'
            GROUP BY payable_type
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Top donors leaderboard
        $topDonors = $db->query("
            SELECT
                CASE WHEN d.is_anonymous=1 THEN 'Anonim' ELSE d.donor_name END AS name,
                d.donor_email,
                SUM(d.total_amount) AS total,
                SUM(d.total_trees) AS trees,
                COUNT(d.id) AS donations
            FROM donations d
            WHERE d.status = 'berhasil'
            GROUP BY d.donor_email
            ORDER BY total DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Orders per status
        $orderStats = $db->query("
            SELECT status, COUNT(*) AS cnt, COALESCE(SUM(total_amount), 0) AS amount
            FROM orders GROUP BY status
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Metrik Ringkasan Eksekutif
        $totalRevenue = (float)$db->query("SELECT COALESCE(SUM(grand_total), 0) FROM payments WHERE status = 'berhasil'")->fetchColumn();
        $totalTrees   = (int)$db->query("SELECT COUNT(*) FROM trees")->fetchColumn();
        $totalTx      = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status = 'berhasil'")->fetchColumn();
        $totalDonors  = (int)$db->query("SELECT COUNT(DISTINCT donor_email) FROM donations WHERE status = 'berhasil'")->fetchColumn();
        $totalCO2     = (float)$db->query("
            SELECT COALESCE(SUM(ts.co2_absorption_kg_per_year * 10), 0)
            FROM trees tr
            JOIN tree_species ts ON ts.id = tr.tree_species_id
        ")->fetchColumn();

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/reports', [
            'pageTitle'           => 'Laporan & Analisis Statistik',
            'pendingPaymentsCount'=> $pendingCount,
            'monthlyRevenue'      => $monthlyRevenue,
            'treesBySpecies'      => $treesBySpecies,
            'treesByLocation'     => $treesByLocation,
            'revenueByType'       => $revenueByType,
            'topDonors'           => $topDonors,
            'orderStats'          => $orderStats,
            'totalRevenue'        => $totalRevenue,
            'totalTrees'          => $totalTrees,
            'totalTx'             => $totalTx,
            'totalDonors'         => $totalDonors,
            'totalCO2'            => $totalCO2,
        ]);
    }
}

