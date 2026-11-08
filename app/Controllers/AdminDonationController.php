<?php
/**
 * AdminDonationController — TanamPohon.id
 * Manajemen & analisis donasi bibit pohon konservasi, sertifikat, dan laporan donatur.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Certificate.php';

class AdminDonationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // 1. Parameter Filter & Pencarian
        $q          = trim($_GET['q'] ?? '');
        $status     = trim($_GET['status'] ?? '');
        $type       = trim($_GET['type'] ?? '');
        $speciesId  = (int)($_GET['species_id'] ?? 0);
        $period     = trim($_GET['period'] ?? '');
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;

        // 2. Susun Klausul WHERE Dinamis
        $conditions = [];
        $params     = [];

        if (!empty($q)) {
            $conditions[] = "(
                d.donor_name LIKE :q1 OR
                d.donor_email LIKE :q2 OR
                d.donor_phone LIKE :q3 OR
                d.gift_recipient_name LIKE :q4 OR
                p.invoice_no LIKE :q5 OR
                c.certificate_number LIKE :q6
            )";
            $params[':q1'] = "%{$q}%";
            $params[':q2'] = "%{$q}%";
            $params[':q3'] = "%{$q}%";
            $params[':q4'] = "%{$q}%";
            $params[':q5'] = "%{$q}%";
            $params[':q6'] = "%{$q}%";
        }

        if (!empty($status)) {
            $conditions[] = "d.status = :status";
            $params[':status'] = $status;
        }

        if ($type === 'gift') {
            $conditions[] = "d.is_gift = 1";
        } elseif ($type === 'anon') {
            $conditions[] = "d.is_anonymous = 1";
        } elseif ($type === 'regular') {
            $conditions[] = "d.is_gift = 0 AND d.is_anonymous = 0";
        }

        if ($speciesId > 0) {
            $conditions[] = "EXISTS (SELECT 1 FROM donation_items sub_di WHERE sub_di.donation_id = d.id AND sub_di.tree_species_id = :sid)";
            $params[':sid'] = $speciesId;
        }

        if ($period === 'today') {
            $conditions[] = "DATE(d.created_at) = CURDATE()";
        } elseif ($period === 'week') {
            $conditions[] = "d.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $conditions[] = "d.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } elseif ($period === 'year') {
            $conditions[] = "YEAR(d.created_at) = YEAR(CURDATE())";
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        // 3. Hitung Total Data untuk Paginasi
        $countSql = "
            SELECT COUNT(DISTINCT d.id)
            FROM donations d
            LEFT JOIN payments p ON (p.id = d.payment_id OR (p.payable_type = 'donation' AND p.payable_id = d.id))
            LEFT JOIN certificates c ON (c.certifiable_type = 'donation' AND c.certifiable_id = d.id)
            {$whereClause}
        ";
        $totalStmt = $db->prepare($countSql);
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        // 4. Ambil Data Donasi dengan Rincian Spesies dan Pembayaran
        $sql = "
            SELECT
                d.id, d.user_id, d.donor_name, d.donor_email, d.donor_phone, d.donor_message,
                d.is_anonymous, d.is_gift, d.gift_recipient_name, d.gift_message,
                d.total_amount, d.total_trees, d.status, d.created_at, d.updated_at,
                p.id AS payment_id, p.invoice_no, p.status AS payment_status, p.unique_code, p.grand_total,
                c.certificate_number, c.issued_at AS certificate_issued_at,
                GROUP_CONCAT(DISTINCT CONCAT(di.quantity, 'x ', ts.name) ORDER BY ts.name ASC SEPARATOR ', ') AS species_summary,
                COALESCE(SUM(di.quantity * ts.co2_absorption_kg_per_year), 0) AS total_co2_kg
            FROM donations d
            LEFT JOIN payments p ON (p.id = d.payment_id OR (p.payable_type = 'donation' AND p.payable_id = d.id))
            LEFT JOIN certificates c ON (c.certifiable_type = 'donation' AND c.certifiable_id = d.id)
            LEFT JOIN donation_items di ON di.donation_id = d.id
            LEFT JOIN tree_species ts ON di.tree_species_id = ts.id
            {$whereClause}
            GROUP BY d.id
            ORDER BY d.created_at DESC
            LIMIT :lim OFFSET :off
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Statistik Ringkasan Global
        $summary = $db->query("
            SELECT
                COUNT(*) AS total_donations,
                COUNT(CASE WHEN status = 'berhasil' THEN 1 END) AS verified_donations,
                COUNT(CASE WHEN status IN ('pending', 'menunggu_pembayaran') THEN 1 END) AS pending_donations,
                COALESCE(SUM(CASE WHEN status = 'berhasil' THEN total_amount ELSE 0 END), 0) AS total_amount,
                COALESCE(SUM(CASE WHEN status = 'berhasil' THEN total_trees ELSE 0 END), 0) AS total_trees,
                COUNT(DISTINCT CASE WHEN status = 'berhasil' THEN donor_email END) AS unique_donors,
                COUNT(CASE WHEN is_gift = 1 AND status = 'berhasil' THEN 1 END) AS gift_donations,
                COUNT(CASE WHEN is_anonymous = 1 AND status = 'berhasil' THEN 1 END) AS anon_donations
            FROM donations
        ")->fetch(PDO::FETCH_ASSOC);

        // Hitung estimasi serap CO2 dari semua donasi yang berhasil
        $co2Result = $db->query("
            SELECT COALESCE(SUM(di.quantity * ts.co2_absorption_kg_per_year), 0) AS total_co2_kg
            FROM donations d
            JOIN donation_items di ON di.donation_id = d.id
            JOIN tree_species ts ON di.tree_species_id = ts.id
            WHERE d.status = 'berhasil'
        ")->fetch(PDO::FETCH_ASSOC);
        $summary['total_co2_kg'] = (float)($co2Result['total_co2_kg'] ?? 0);

        // Daftar spesies pohon untuk dropdown filter
        $speciesList = $db->query("SELECT id, name FROM tree_species WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status = 'menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/donations', [
            'pageTitle'            => 'Manajemen & Analisis Donasi Pohon',
            'pendingPaymentsCount' => $pendingCount,
            'donations'            => $donations,
            'summary'              => $summary,
            'speciesList'          => $speciesList,
            'total'                => $total,
            'page'                 => $page,
            'perPage'              => $perPage,
            'totalPages'           => (int)ceil($total / $perPage),
            'filters'              => [
                'q'          => $q,
                'status'     => $status,
                'type'       => $type,
                'species_id' => $speciesId,
                'period'     => $period,
            ],
        ]);
    }

    /**
     * Ambil detail lengkap donasi dalam format JSON untuk modal inspektur
     */
    public function detailJson(int $id): void
    {
        header('Content-Type: application/json');
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT
                d.*,
                p.invoice_no, p.status AS payment_status, p.unique_code, p.grand_total,
                p.expires_at, p.paid_at,
                c.certificate_number, c.issued_at AS certificate_issued_at
            FROM donations d
            LEFT JOIN payments p ON (p.id = d.payment_id OR (p.payable_type = 'donation' AND p.payable_id = d.id))
            LEFT JOIN certificates c ON (c.certifiable_type = 'donation' AND c.certifiable_id = d.id)
            WHERE d.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $donation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donation) {
            http_response_code(404);
            echo json_encode(['error' => 'Data donasi tidak ditemukan.']);
            exit;
        }

        // Ambil rincian pohon
        $itemStmt = $db->prepare("
            SELECT
                di.*,
                ts.name AS species_name, ts.description, ts.co2_absorption_kg_per_year,
                ts.image AS species_image
            FROM donation_items di
            JOIN tree_species ts ON di.tree_species_id = ts.id
            WHERE di.donation_id = :did
        ");
        $itemStmt->execute([':did' => $id]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil bukti transfer jika ada
        $proof = null;
        if (!empty($donation['payment_id'])) {
            $pStmt = $db->prepare("
                SELECT pp.*, b.bank_name, b.account_number, b.account_holder
                FROM payment_proofs pp
                LEFT JOIN bank_accounts b ON pp.bank_account_id = b.id
                WHERE pp.payment_id = :pid
                ORDER BY pp.id DESC
                LIMIT 1
            ");
            $pStmt->execute([':pid' => $donation['payment_id']]);
            $proof = $pStmt->fetch(PDO::FETCH_ASSOC);
            if ($proof && !empty($proof['proof_file'])) {
                $file = ltrim($proof['proof_file'], '/');
                $proof['proof_url'] = str_starts_with($file, 'storage/') ? '/' . $file : '/storage/uploads/' . $file;
            }
        }

        // Susun template WhatsApp ucapan terima kasih
        $waMessage = "Halo Kak {$donation['donor_name']},\n\nTerima kasih banyak atas partisipasi donasi pohon Anda di *Plant For Fun (Yayasan Tanam Pohon)*!\n\nID Donasi: #{$donation['id']}\nTotal Pohon: {$donation['total_trees']} Batang\nNominal: Rp " . number_format($donation['total_amount'], 0, ',', '.') . "\nStatus: " . ucfirst($donation['status']);
        if (!empty($donation['certificate_number'])) {
            $waMessage .= "\n\nSertifikat Digital Anda telah terbit dengan nomor: *{$donation['certificate_number']}*\nCek sertifikat: " . url('/verifikasi-sertifikat/' . $donation['certificate_number']);
        }
        $waMessage .= "\n\nSalam Lestari dari Pegunungan Kerenceng!";
        
        $phone = preg_replace('/[^0-9]/', '', $donation['donor_phone'] ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $waUrl = !empty($phone) ? "https://wa.me/{$phone}?text=" . rawurlencode($waMessage) : null;

        echo json_encode([
            'donation'  => $donation,
            'items'     => $items,
            'proof'     => $proof,
            'wa_url'    => $waUrl,
        ]);
        exit;
    }

    /**
     * Ekspor rekapitulasi data donasi ke berkas CSV (Kompatibel Excel)
     */
    public function export(): void
    {
        $db = Database::getInstance()->getConnection();

        $status = trim($_GET['status'] ?? '');
        $type   = trim($_GET['type'] ?? '');
        $period = trim($_GET['period'] ?? '');

        $conditions = [];
        $params     = [];

        if (!empty($status)) {
            $conditions[] = "d.status = :status";
            $params[':status'] = $status;
        }
        if ($type === 'gift') {
            $conditions[] = "d.is_gift = 1";
        } elseif ($type === 'anon') {
            $conditions[] = "d.is_anonymous = 1";
        } elseif ($type === 'regular') {
            $conditions[] = "d.is_gift = 0 AND d.is_anonymous = 0";
        }
        if ($period === 'today') {
            $conditions[] = "DATE(d.created_at) = CURDATE()";
        } elseif ($period === 'week') {
            $conditions[] = "d.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $conditions[] = "d.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } elseif ($period === 'year') {
            $conditions[] = "YEAR(d.created_at) = YEAR(CURDATE())";
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        $stmt = $db->prepare("
            SELECT
                d.id, d.donor_name, d.donor_email, d.donor_phone,
                d.is_anonymous, d.is_gift, d.gift_recipient_name, d.gift_message,
                d.total_amount, d.total_trees, d.status, d.created_at,
                p.invoice_no, p.status AS payment_status, p.unique_code, p.grand_total,
                c.certificate_number,
                GROUP_CONCAT(DISTINCT CONCAT(di.quantity, 'x ', ts.name) SEPARATOR ', ') AS species_summary
            FROM donations d
            LEFT JOIN payments p ON (p.id = d.payment_id OR (p.payable_type = 'donation' AND p.payable_id = d.id))
            LEFT JOIN certificates c ON (c.certifiable_type = 'donation' AND c.certifiable_id = d.id)
            LEFT JOIN donation_items di ON di.donation_id = d.id
            LEFT JOIN tree_species ts ON di.tree_species_id = ts.id
            {$whereClause}
            GROUP BY d.id
            ORDER BY d.created_at DESC
        ");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = 'rekap_donasi_pohon_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM agar Excel menampilkan aksen huruf dengan benar
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'ID Donasi',
            'Nomor Invoice',
            'Tanggal Transaksi',
            'Nama Donatur',
            'Email Donatur',
            'Nomor WhatsApp',
            'Tipe Donasi',
            'Penerima Hadiah',
            'Pesan Hadiah / Doa',
            'Rincian Bibit Pohon',
            'Total Pohon (Batang)',
            'Nominal Donasi (Rp)',
            'Kode Unik',
            'Total Tagihan (Rp)',
            'Status Pembayaran',
            'Status Donasi',
            'Nomor Sertifikat'
        ]);

        foreach ($rows as $r) {
            $tipe = 'Reguler';
            if ($r['is_anonymous']) $tipe = 'Anonim';
            elseif ($r['is_gift']) $tipe = 'Hadiah (Gift)';

            fputcsv($output, [
                '#' . $r['id'],
                $r['invoice_no'] ?: '-',
                $r['created_at'],
                $r['donor_name'],
                $r['donor_email'],
                $r['donor_phone'] ?: '-',
                $tipe,
                $r['gift_recipient_name'] ?: '-',
                $r['gift_message'] ?: '-',
                $r['species_summary'] ?: '-',
                $r['total_trees'],
                (float)$r['total_amount'],
                (int)($r['unique_code'] ?? 0),
                (float)($r['grand_total'] ?? $r['total_amount']),
                ucfirst($r['payment_status'] ?? '-'),
                ucfirst($r['status']),
                $r['certificate_number'] ?: '-'
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Perbarui status donasi secara manual oleh admin
     */
    public function updateStatus(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/donasi');
            return;
        }

        $newStatus = trim($_POST['status'] ?? '');
        $allowed = ['berhasil', 'menunggu_pembayaran', 'pending', 'dibatalkan'];

        if (!in_array($newStatus, $allowed)) {
            Session::flash('error', 'Status donasi tidak valid.');
            $this->redirect('/admin/donasi');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM donations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $donation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donation) {
            Session::flash('error', 'Donasi tidak ditemukan.');
            $this->redirect('/admin/donasi');
            return;
        }

        // Perbarui status donasi
        $up = $db->prepare("UPDATE donations SET status = :st, updated_at = NOW() WHERE id = :id");
        $up->execute([':st' => $newStatus, ':id' => $id]);

        // Jika diset ke berhasil, sinkronkan pembayaran dan sertifikat
        if ($newStatus === 'berhasil') {
            $db->prepare("
                UPDATE payments
                SET status = 'berhasil', paid_at = NOW(), updated_at = NOW()
                WHERE (id = :pid OR (payable_type = 'donation' AND payable_id = :did))
            ")->execute([
                ':pid' => $donation['payment_id'] ?: 0,
                ':did' => $id,
            ]);

            // Terbitkan sertifikat donasi jika belum ada
            try {
                $certModel = new Certificate();
                $userId = (int)($donation['user_id'] ?: 1);
                $certModel->issueDonationCertificate($id, $userId);
            } catch (\Exception $e) {
                // Abaikan jika sudah ada atau gagal minor
            }
        } elseif ($newStatus === 'dibatalkan') {
            $db->prepare("
                UPDATE payments
                SET status = 'dibatalkan', updated_at = NOW()
                WHERE (id = :pid OR (payable_type = 'donation' AND payable_id = :did))
            ")->execute([
                ':pid' => $donation['payment_id'] ?: 0,
                ':did' => $id,
            ]);
        }

        Session::flash('success', "Status donasi #{$id} berhasil diperbarui menjadi '" . ucfirst($newStatus) . "'.");
        $this->redirect('/admin/donasi');
    }
}
