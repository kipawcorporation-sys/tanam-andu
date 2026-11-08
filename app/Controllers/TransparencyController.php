<?php
/**
 * Controller TransparencyController — TanamPohon.id
 * 
 * Mengelola halaman publik transparansi keuangan & dampak ekologis,
 * peta sebaran pohon interaktif (Leaflet.js), leaderboard donatur & relawan,
 * serta verifikasi keaslian sertifikat donatur/partisipan.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../Models/Certificate.php';

class TransparencyController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Halaman Utama Transparansi Publik
     * Menampilkan realisasi dana donasi, serapan karbon riil, alokasi anggaran, dan laporan penanaman
     */
    /**
     * Halaman Utama Transparansi Publik
     * Menampilkan realisasi dana donasi, serapan karbon riil, alokasi anggaran, dan laporan penanaman
     */
    public function index(): void
    {
        // 1. Total Dana Donasi Berhasil
        $stmtDonation = $this->db->query("
            SELECT 
                COUNT(id) as total_transactions,
                COALESCE(SUM(total_amount), 0) as total_raised,
                COALESCE(SUM(total_trees), 0) as total_trees_donated
            FROM donations 
            WHERE status = 'berhasil'
        ");
        $donationStats = $stmtDonation->fetch(PDO::FETCH_ASSOC);

        // Target Finansial & Pohon Nasional (Bisa dari database setting atau default program)
        $targetAmount = 500000000; // Rp 500 Juta
        $targetTrees = 20000;      // 20.000 Pohon

        // 2. Statistik Pohon di Lapangan
        $stmtTrees = $this->db->query("
            SELECT 
                COUNT(id) as total_trees,
                SUM(CASE WHEN status IN ('ditanam', 'tumbuh', 'dipantau') THEN 1 ELSE 0 END) as total_planted,
                SUM(CASE WHEN status = 'tumbuh' THEN 1 ELSE 0 END) as total_growing,
                SUM(CASE WHEN status = 'dipantau' THEN 1 ELSE 0 END) as total_monitored
            FROM trees
        ");
        $treeStats = $stmtTrees->fetch(PDO::FETCH_ASSOC);

        // 3. Dampak Ekologis (Serapan CO2 & Produksi O2)
        $stmtEco = $this->db->query("
            SELECT 
                COALESCE(SUM(ts.co2_absorption_kg_per_year), 0) as total_co2_kg,
                COALESCE(SUM(ts.oxygen_production_kg_per_year), 0) as total_o2_kg
            FROM trees t
            JOIN tree_species ts ON t.tree_species_id = ts.id
            WHERE t.status IN ('ditanam', 'tumbuh', 'dipantau')
        ");
        $ecoStats = $stmtEco->fetch(PDO::FETCH_ASSOC);

        // 4. Progres per Lokasi Penanaman
        $stmtLocations = $this->db->query("
            SELECT l.*, 
                (SELECT COUNT(t.id) FROM trees t WHERE t.planting_location_id = l.id) as actual_planted_count
            FROM planting_locations l
            WHERE l.is_active = 1
            ORDER BY l.total_trees_target DESC
        ");
        $locations = $stmtLocations->fetchAll(PDO::FETCH_ASSOC);

        $locationsJson = json_encode(array_map(function($loc) {
            return [
                'id' => (int)$loc['id'],
                'name' => $loc['name'],
                'city' => $loc['city'],
                'province' => $loc['province'],
                'latitude' => (float)$loc['latitude'],
                'longitude' => (float)$loc['longitude'],
                'total_trees_target' => (int)$loc['total_trees_target'],
                'actual_planted_count' => (int)($loc['actual_planted_count'] ?? $loc['total_trees_planted']),
                'description' => $loc['description'] ?? '',
                'photo' => $loc['photo'] ?? null,
            ];
        }, $locations), JSON_UNESCAPED_UNICODE);

        // 5. Rekap Donatur Terkini & Buku Kas Transparansi (dengan invoice no)
        $stmtRecent = $this->db->query("
            SELECT 
                d.id,
                COALESCE(p.invoice_no, CONCAT('TP-DON-', LPAD(d.id, 5, '0'))) as invoice_no,
                d.donor_name, 
                d.is_anonymous, 
                d.total_trees, 
                d.total_amount, 
                d.created_at, 
                d.donor_message,
                p.status as payment_status
            FROM donations d
            LEFT JOIN payments p ON d.payment_id = p.id
            WHERE d.status = 'berhasil'
            ORDER BY d.created_at DESC
            LIMIT 25
        ");
        $recentDonations = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

        // 6. Kabar & Berita Penanaman Terbaru
        $stmtNews = $this->db->query("
            SELECT title, slug, content, image, published_at
            FROM news
            WHERE is_published = 1
            ORDER BY published_at DESC
            LIMIT 3
        ");
        $latestNews = [];
        if ($stmtNews) {
            $rawNews = $stmtNews->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rawNews as $n) {
                $plain = trim(strip_tags($n['content'] ?? ''));
                $n['excerpt'] = mb_strlen($plain) > 100 ? mb_substr($plain, 0, 100) . '...' : $plain;
                $latestNews[] = $n;
            }
        }

        // 7. Spesies Keanekaragaman Hayati (Biodiversitas)
        $stmtSpecies = $this->db->query("
            SELECT ts.id, ts.name, ts.image, ts.co2_absorption_kg_per_year, ts.oxygen_production_kg_per_year, ts.price_per_tree,
                   COUNT(t.id) as planted_count
            FROM tree_species ts
            LEFT JOIN trees t ON t.tree_species_id = ts.id AND t.status IN ('ditanam', 'tumbuh', 'dipantau')
            WHERE ts.is_active = 1
            GROUP BY ts.id
            ORDER BY planted_count DESC, ts.name ASC
        ");
        $treeSpecies = $stmtSpecies ? $stmtSpecies->fetchAll(PDO::FETCH_ASSOC) : [];

        // 8. Tren Realisasi Donasi — adaptif: per-hari jika ≤ 1 bulan, per-bulan jika lebih
        $stmtMonthCount = $this->db->query("
            SELECT COUNT(DISTINCT DATE_FORMAT(created_at, '%Y-%m')) as month_count
            FROM donations WHERE status = 'berhasil'
        ");
        $monthCount = (int)($stmtMonthCount ? $stmtMonthCount->fetchColumn() : 0);

        if ($monthCount <= 1) {
            // Data hanya 1 bulan → group by hari agar chart terisi
            $stmtTrends = $this->db->query("
                SELECT 
                    DATE(created_at) as ym,
                    DATE_FORMAT(created_at, '%d %b') as month_label,
                    COUNT(id) as count_donations,
                    COALESCE(SUM(total_amount), 0) as sum_amount,
                    COALESCE(SUM(total_trees), 0) as sum_trees
                FROM donations
                WHERE status = 'berhasil'
                GROUP BY DATE(created_at)
                ORDER BY ym ASC
                LIMIT 31
            ");
            $trendGranularity = 'daily';
        } else {
            // Sudah banyak bulan → group by bulan
            $stmtTrends = $this->db->query("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as ym,
                    DATE_FORMAT(created_at, '%b %Y') as month_label,
                    COUNT(id) as count_donations,
                    COALESCE(SUM(total_amount), 0) as sum_amount,
                    COALESCE(SUM(total_trees), 0) as sum_trees
                FROM donations
                WHERE status = 'berhasil'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
                ORDER BY ym ASC
                LIMIT 12
            ");
            $trendGranularity = 'monthly';
        }
        $donationTrends = $stmtTrends ? $stmtTrends->fetchAll(PDO::FETCH_ASSOC) : [];

        // 9. Statistik Tambahan: Relawan Hadir & Sertifikat Terbit
        $stmtVol = $this->db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'hadir'");
        $volunteersCount = (int)($stmtVol ? $stmtVol->fetchColumn() : 0);

        $stmtCert = $this->db->query("SELECT COUNT(*) FROM certificates");
        $certCount = (int)($stmtCert ? $stmtCert->fetchColumn() : 0);

        $this->view('public/transparency', [
            'pageTitle'         => 'Laporan Transparansi Publik & Dampak Ekologis - TanamPohon.id',
            'donationStats'     => $donationStats,
            'targetAmount'      => $targetAmount,
            'targetTrees'       => $targetTrees,
            'treeStats'         => $treeStats,
            'ecoStats'          => $ecoStats,
            'locations'         => $locations,
            'locationsJson'     => $locationsJson,
            'recentDonations'   => $recentDonations,
            'latestNews'        => $latestNews,
            'treeSpecies'       => $treeSpecies,
            'donationTrends'    => $donationTrends,
            'trendGranularity'  => $trendGranularity ?? 'monthly',
            'volunteersCount'   => $volunteersCount,
            'certCount'         => $certCount,
        ]);
    }

    /**
     * Unduh Laporan Transparansi Donasi Publik dalam format CSV
     */
    public function exportCsv(): void
    {
        $stmt = $this->db->query("
            SELECT 
                d.id,
                COALESCE(p.invoice_no, CONCAT('TP-DON-', LPAD(d.id, 5, '0'))) as invoice_no,
                CASE WHEN d.is_anonymous = 1 THEN 'Sahabat Lestari (Anonim)' ELSE d.donor_name END as donor_name,
                d.total_trees,
                d.total_amount,
                d.donor_message,
                d.status,
                d.created_at
            FROM donations d
            LEFT JOIN payments p ON d.payment_id = p.id
            WHERE d.status = 'berhasil'
            ORDER BY d.created_at DESC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="transparansi-donasi-tanampohon-' . date('Ymd-His') . '.csv"');

        $output = fopen('php://output', 'w');
        // Output UTF-8 BOM agar rapi di Microsoft Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['ID', 'Nomor Invoice / Ref', 'Nama Donatur', 'Jumlah Pohon', 'Nominal (Rp)', 'Pesan Dedikasi', 'Status Verifikasi', 'Tanggal & Waktu (WIB)'], ';', '"', '\\');

        foreach ($rows as $r) {
            fputcsv($output, [
                $r['id'],
                $r['invoice_no'],
                $r['donor_name'],
                $r['total_trees'],
                $r['total_amount'],
                $r['donor_message'] ?: '-',
                'Tervalidasi 100%',
                date('d/m/Y H:i:s', strtotime($r['created_at']))
            ], ';', '"', '\\');
        }

        fclose($output);
        exit;
    }

    /**
     * Halaman Peta Sebaran Pohon Interaktif (Leaflet.js)
     */
    public function map(): void
    {
        // Ambil filter spesies pohon
        $stmtSpecies = $this->db->query("SELECT id, name FROM tree_species WHERE is_active = 1 ORDER BY name ASC");
        $speciesList = $stmtSpecies->fetchAll(PDO::FETCH_ASSOC);

        // Ambil filter lokasi penanaman
        $stmtLocations = $this->db->query("SELECT id, name, city, province FROM planting_locations WHERE is_active = 1 ORDER BY name ASC");
        $locationList = $stmtLocations->fetchAll(PDO::FETCH_ASSOC);

        // Ambil seluruh titik pohon yang memiliki koordinat
        $trees = $this->getTreePoints();

        // Statistik ringkas untuk widget peta
        $totalPlanted = count($trees);
        $totalLocations = count($locationList);
        $totalSpecies = count($speciesList);

        $this->view('public/map', [
            'pageTitle'      => 'Peta Sebaran Pohon Tertanam - TanamPohon.id',
            'speciesList'    => $speciesList,
            'locationList'   => $locationList,
            'trees'          => $trees,
            'treesJson'      => json_encode($trees, JSON_UNESCAPED_UNICODE),
            'totalPlanted'   => $totalPlanted,
            'totalLocations' => $totalLocations,
            'totalSpecies'   => $totalSpecies,
        ]);
    }

    /**
     * Endpoint API JSON untuk filter pohon dinamis di peta
     */
    public function apiTrees(): void
    {
        $locationId = isset($_GET['location_id']) && $_GET['location_id'] !== '' ? (int)$_GET['location_id'] : null;
        $speciesId = isset($_GET['species_id']) && $_GET['species_id'] !== '' ? (int)$_GET['species_id'] : null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : null;

        $trees = $this->getTreePoints($locationId, $speciesId, $status);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'count'   => count($trees),
            'data'    => $trees
        ]);
        exit;
    }

    /**
     * Helper query titik pohon untuk peta
     */
    private function getTreePoints(?int $locationId = null, ?int $speciesId = null, ?string $status = null): array
    {
        $sql = "
            SELECT 
                t.id, 
                t.code, 
                t.status, 
                t.latitude, 
                t.longitude, 
                t.planted_at,
                ts.name as species_name, 
                ts.co2_absorption_kg_per_year,
                l.name as location_name, 
                l.city, 
                l.province,
                (
                    SELECT photo FROM tree_trackings tt 
                    WHERE tt.tree_id = t.id AND tt.photo IS NOT NULL 
                    ORDER BY tt.tracked_at DESC LIMIT 1
                ) as latest_photo,
                (
                    SELECT notes FROM tree_trackings tt 
                    WHERE tt.tree_id = t.id 
                    ORDER BY tt.tracked_at DESC LIMIT 1
                ) as latest_notes
            FROM trees t
            JOIN tree_species ts ON t.tree_species_id = ts.id
            LEFT JOIN planting_locations l ON t.planting_location_id = l.id
            WHERE t.latitude IS NOT NULL 
              AND t.longitude IS NOT NULL
        ";

        $params = [];

        if ($locationId) {
            $sql .= " AND t.planting_location_id = :loc_id";
            $params['loc_id'] = $locationId;
        }

        if ($speciesId) {
            $sql .= " AND t.tree_species_id = :species_id";
            $params['species_id'] = $speciesId;
        }

        if ($status) {
            $sql .= " AND t.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY t.planted_at DESC LIMIT 500";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalisasi format latitude/longitude menjadi float
        foreach ($results as &$r) {
            $r['latitude'] = (float)$r['latitude'];
            $r['longitude'] = (float)$r['longitude'];
            $r['co2_absorption_kg_per_year'] = (float)$r['co2_absorption_kg_per_year'];
            $r['planted_at_formatted'] = $r['planted_at'] ? date('d M Y', strtotime($r['planted_at'])) : '-';
        }

        return $results;
    }

    /**
     * Halaman Papan Peringkat (Leaderboard) Donatur & Relawan
     */
    public function leaderboard(): void
    {
        $period = $_GET['periode'] ?? 'all'; // all, year, month

        $dateFilterDonation = "";
        $paramsDonation = [];

        if ($period === 'month') {
            $dateFilterDonation = "AND d.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01 00:00:00')";
        } elseif ($period === 'year') {
            $dateFilterDonation = "AND d.created_at >= DATE_FORMAT(NOW(), '%Y-01-01 00:00:00')";
        }

        // 1. Peringkat Donatur Berdasarkan Jumlah Pohon & Donasi
        $sqlDonors = "
            SELECT 
                CASE 
                    WHEN d.is_anonymous = 1 THEN 'Sahabat Lestari'
                    WHEN u.name IS NOT NULL THEN u.name
                    ELSE d.donor_name
                END as display_name,
                d.is_anonymous,
                u.avatar,
                COUNT(d.id) as donation_count,
                SUM(d.total_trees) as total_trees,
                SUM(d.total_amount) as total_amount,
                MAX(d.created_at) as last_donation_at
            FROM donations d
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.status = 'berhasil' {$dateFilterDonation}
            GROUP BY 
                CASE 
                    WHEN d.is_anonymous = 1 THEN CONCAT('anon_', d.id)
                    WHEN d.user_id IS NOT NULL THEN CONCAT('user_', d.user_id)
                    ELSE CONCAT('guest_', LOWER(d.donor_email))
                END
            ORDER BY total_trees DESC, total_amount DESC
            LIMIT 20
        ";

        $stmtDonors = $this->db->prepare($sqlDonors);
        $stmtDonors->execute($paramsDonation);
        $topDonors = $stmtDonors->fetchAll(PDO::FETCH_ASSOC);

        // 2. Peringkat Relawan Berdasarkan Kehadiran Kegiatan
        $sqlVolunteers = "
            SELECT 
                u.id as user_id,
                u.name as display_name,
                u.avatar,
                COUNT(p.id) as events_attended,
                MAX(p.checked_in_at) as last_attended_at
            FROM participants p
            JOIN users u ON p.user_id = u.id
            WHERE p.registration_status = 'hadir'
            GROUP BY u.id
            ORDER BY events_attended DESC, last_attended_at DESC
            LIMIT 15
        ";
        $stmtVolunteers = $this->db->query($sqlVolunteers);
        $topVolunteers = $stmtVolunteers ? $stmtVolunteers->fetchAll(PDO::FETCH_ASSOC) : [];

        $this->view('public/leaderboard', [
            'pageTitle'     => 'Papan Peringkat Pahlawan Hijau - TanamPohon.id',
            'period'        => $period,
            'topDonors'     => $topDonors,
            'topVolunteers' => $topVolunteers,
        ]);
    }

    /**
     * Halaman Form Verifikasi Sertifikat Publik
     */
    public function verifyCertificate(): void
    {
        // Ambil sampel beberapa sertifikat resmi terbaru yang diterbitkan
        $stmtRecent = $this->db->query("
            SELECT certificate_number, type, issued_at, certifiable_type, data_snapshot
            FROM certificates
            ORDER BY issued_at DESC
            LIMIT 4
        ");
        $recentCertificates = [];
        if ($stmtRecent) {
            $rows = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $snapshot = json_decode($r['data_snapshot'] ?? '{}', true) ?: [];
                $r['recipient_name'] = $snapshot['recipient_name'] ?? 'Donatur Peduli';
                $recentCertificates[] = $r;
            }
        }

        $this->view('public/verify_certificate', [
            'pageTitle'          => 'Verifikasi Keaslian Sertifikat Resmi - TanamPohon.id',
            'certificate'        => null,
            'searchedNumber'     => null,
            'notFound'           => false,
            'recentCertificates' => $recentCertificates,
        ]);
    }

    /**
     * Halaman Hasil Pengecekan Sertifikat berdasarkan Nomor
     * URL: /verifikasi-sertifikat/{number}
     */
    public function checkCertificate(string $number = ''): void
    {
        $number = trim($number ?: ($_GET['nomor'] ?? ''));

        if (empty($number)) {
            $this->redirect('/verifikasi-sertifikat');
            return;
        }

        $certModel = new Certificate();
        $cert = $certModel->findByNumber($number);

        // Ambil sampel sertifikat terbaru untuk referensi
        $stmtRecent = $this->db->query("
            SELECT certificate_number, type, issued_at, certifiable_type, data_snapshot
            FROM certificates
            ORDER BY issued_at DESC
            LIMIT 4
        ");
        $recentCertificates = [];
        if ($stmtRecent) {
            $rows = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $snapshot = json_decode($r['data_snapshot'] ?? '{}', true) ?: [];
                $r['recipient_name'] = $snapshot['recipient_name'] ?? 'Donatur Peduli';
                $recentCertificates[] = $r;
            }
        }

        $this->view('public/verify_certificate', [
            'pageTitle'          => 'Hasil Verifikasi Sertifikat ' . e($number) . ' - TanamPohon.id',
            'certificate'        => $cert ?: null,
            'searchedNumber'     => $number,
            'notFound'           => empty($cert),
            'recentCertificates' => $recentCertificates,
        ]);
    }
}
