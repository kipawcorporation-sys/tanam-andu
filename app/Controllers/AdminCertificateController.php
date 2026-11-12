<?php
/**
 * AdminCertificateController — TanamPohon.id
 *
 * Manajemen sertifikat digital: lihat semua, terbitkan ulang,
 * cabut, dan ekspor data sertifikat donasi & relawan.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Certificate.php';

class AdminCertificateController extends Controller
{
    private Certificate $certModel;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
        $this->certModel = new Certificate();
    }

    /**
     * Halaman daftar semua sertifikat yang telah diterbitkan
     */
    public function index(): void
    {
        $db  = Database::getInstance()->getConnection();
        $q   = trim($_GET['q'] ?? '');
        $type = $_GET['type'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        // --- Query utama ---
        $where = ['1=1'];
        $params = [];

        if ($q !== '') {
            $where[] = "(c.certificate_number LIKE :q1 OR u.name LIKE :q2 OR u.email LIKE :q3)";
            $like = "%{$q}%";
            $params[':q1'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
        }
        if ($type !== '') {
            $where[] = "c.type = :type";
            $params[':type'] = $type;
        }

        $whereStr = implode(' AND ', $where);

        try {
            $stmtTotal = $db->prepare("
                SELECT COUNT(*) FROM certificates c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE {$whereStr}
            ");
            $stmtTotal->execute($params);
            $total = (int)$stmtTotal->fetchColumn();

            $stmtCerts = $db->prepare("
                SELECT c.*,
                       u.name  AS user_name,
                       u.email AS user_email,
                       u.phone AS user_phone
                FROM certificates c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE {$whereStr}
                ORDER BY c.issued_at DESC
                LIMIT :limit OFFSET :offset
            ");
            foreach ($params as $k => $v) {
                $stmtCerts->bindValue($k, $v);
            }
            $stmtCerts->bindValue(':limit',  $perPage, PDO::PARAM_INT);
            $stmtCerts->bindValue(':offset', $offset,  PDO::PARAM_INT);
            $stmtCerts->execute();
            $certificates = $stmtCerts->fetchAll(PDO::FETCH_ASSOC);

            // Decode data_snapshot tiap sertifikat
            foreach ($certificates as &$cert) {
                $cert['data'] = json_decode($cert['data_snapshot'] ?? '{}', true) ?: [];
            }
            unset($cert);

        } catch (\Exception $e) {
            $certificates = [];
            $total = 0;
        }

        // Statistik ringkasan
        $stats = ['total' => 0, 'donasi' => 0, 'partisipasi' => 0, 'bulan_ini' => 0];
        try {
            $r = $db->query("SELECT
                COUNT(*) AS total,
                SUM(type = 'donasi') AS donasi,
                SUM(type = 'partisipasi') AS partisipasi,
                SUM(DATE_FORMAT(issued_at,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')) AS bulan_ini
                FROM certificates")->fetch(PDO::FETCH_ASSOC);
            $stats = $r ?: $stats;
        } catch (\Exception $e) {}

        // Ambil daftar donasi & peserta yang belum memiliki sertifikat (antrean siap terbit)
        $unissuedDonations = [];
        $unissuedParticipants = [];
        try {
            $unissuedDonations = $db->query("
                SELECT d.id, d.donor_name, d.donor_email, d.total_trees, d.total_amount, d.created_at
                FROM donations d
                LEFT JOIN certificates c ON (c.certifiable_type = 'donation' AND c.certifiable_id = d.id)
                WHERE d.status = 'berhasil' AND c.id IS NULL
                ORDER BY d.created_at DESC
                LIMIT 50
            ")->fetchAll(PDO::FETCH_ASSOC);

            $unissuedParticipants = $db->query("
                SELECT p.id, u.name as participant_name, u.email, e.title as event_title, p.registration_status, p.checked_in_at, p.created_at
                FROM participants p
                JOIN users u ON u.id = p.user_id
                JOIN planting_events e ON e.id = p.event_id
                LEFT JOIN certificates c ON (c.certifiable_type = 'participant' AND c.certifiable_id = p.id)
                WHERE p.registration_status IN ('hadir', 'terkonfirmasi') AND c.id IS NULL
                ORDER BY p.created_at DESC
                LIMIT 50
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $totalPages = max(1, (int)ceil($total / $perPage));

        $this->view('admin/certificates', [
            'pageTitle'            => 'Manajemen Sertifikat Digital - Admin',
            'certificates'         => $certificates,
            'stats'                => $stats,
            'total'                => $total,
            'page'                 => $page,
            'totalPages'           => $totalPages,
            'q'                    => $q,
            'typeFilter'           => $type,
            'unissuedDonations'    => $unissuedDonations,
            'unissuedParticipants' => $unissuedParticipants,
        ]);
    }

    /**
     * Terbitkan ulang sertifikat donasi yang belum punya sertifikat
     * atau mint sertifikat baru untuk donasi berhasil yang terlewat
     */
    public function reissueDonation(): void
    {
        $donationId = (int)$this->request->input('donation_id');
        if ($donationId <= 0) {
            Session::flash('error', 'ID donasi tidak valid.');
            $this->redirect('/admin/sertifikat');
            return;
        }

        $db = Database::getInstance()->getConnection();

        try {
            // Ambil user_id dari donasi
            $stmt = $db->prepare("SELECT user_id, status FROM donations WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $donationId]);
            $donation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$donation) {
                Session::flash('error', 'Donasi tidak ditemukan.');
                $this->redirect('/admin/sertifikat');
                return;
            }

            if ($donation['status'] !== 'berhasil') {
                Session::flash('error', 'Sertifikat hanya bisa diterbitkan untuk donasi berstatus berhasil.');
                $this->redirect('/admin/sertifikat');
                return;
            }

            $cert = $this->certModel->issueDonationCertificate($donationId, (int)$donation['user_id']);
            Session::flash('success', "Sertifikat Donasi {$cert['certificate_number']} berhasil diterbitkan.");

        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menerbitkan sertifikat donasi: ' . $e->getMessage());
        }

        $this->redirect('/admin/sertifikat');
    }

    /**
     * Terbitkan sertifikat partisipasi relawan secara manual
     */
    public function reissueParticipant(): void
    {
        $participantId = (int)$this->request->input('participant_id');
        if ($participantId <= 0) {
            Session::flash('error', 'ID relawan tidak valid.');
            $this->redirect('/admin/sertifikat');
            return;
        }

        $db = Database::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("SELECT id, user_id, registration_status FROM participants WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $participantId]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant) {
                Session::flash('error', 'Data pendaftaran relawan tidak ditemukan.');
                $this->redirect('/admin/sertifikat');
                return;
            }

            // Jika belum hadir, otomatis update jadi hadir
            if ($participant['registration_status'] !== 'hadir') {
                $db->prepare("
                    UPDATE participants 
                    SET registration_status = 'hadir', checked_in_at = COALESCE(checked_in_at, NOW()), updated_at = NOW()
                    WHERE id = :id
                ")->execute(['id' => $participantId]);
            }

            $cert = $this->certModel->issueParticipantCertificate($participantId, (int)$participant['user_id']);
            Session::flash('success', "Sertifikat Relawan {$cert['certificate_number']} berhasil diterbitkan.");

        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menerbitkan sertifikat relawan: ' . $e->getMessage());
        }

        $this->redirect('/admin/sertifikat');
    }

    /**
     * Terbitkan semua sertifikat yang belum terbit secara batch (sekali klik)
     */
    public function batchIssue(): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/sertifikat'); return; }

        $db = Database::getInstance()->getConnection();
        $target = $this->request->input('target', 'all');

        $countDonation = 0;
        $countParticipant = 0;

        if ($target === 'all' || $target === 'donations') {
            try {
                $donations = $db->query("
                    SELECT d.id, d.user_id
                    FROM donations d
                    LEFT JOIN certificates c ON (c.certifiable_type = 'donation' AND c.certifiable_id = d.id)
                    WHERE d.status = 'berhasil' AND c.id IS NULL
                ")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($donations as $d) {
                    try {
                        $this->certModel->issueDonationCertificate((int)$d['id'], (int)$d['user_id']);
                        $countDonation++;
                    } catch (\Exception $e) {}
                }
            } catch (\Exception $e) {}
        }

        if ($target === 'all' || $target === 'participants') {
            try {
                $participants = $db->query("
                    SELECT p.id, p.user_id, p.registration_status
                    FROM participants p
                    LEFT JOIN certificates c ON (c.certifiable_type = 'participant' AND c.certifiable_id = p.id)
                    WHERE p.registration_status IN ('hadir', 'terkonfirmasi') AND c.id IS NULL
                ")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($participants as $p) {
                    try {
                        if ($p['registration_status'] !== 'hadir') {
                            $db->prepare("UPDATE participants SET registration_status = 'hadir', checked_in_at = COALESCE(checked_in_at, NOW()), updated_at = NOW() WHERE id = :id")
                               ->execute(['id' => $p['id']]);
                        }
                        $this->certModel->issueParticipantCertificate((int)$p['id'], (int)$p['user_id']);
                        $countParticipant++;
                    } catch (\Exception $e) {}
                }
            } catch (\Exception $e) {}
        }

        $totalIssued = $countDonation + $countParticipant;
        if ($totalIssued > 0) {
            Session::flash('success', "Berhasil menerbitkan {$totalIssued} sertifikat secara otomatis ({$countDonation} donasi, {$countParticipant} relawan)!");
        } else {
            Session::flash('info', 'Semua donasi dan relawan yang memenuhi syarat sudah memiliki sertifikat.');
        }

        $this->redirect('/admin/sertifikat');
    }

    /**
     * Cabut / hapus sertifikat (soft delete via revoked_at)
     */
    public function revoke(): void
    {
        $certId = (int)$this->request->input('cert_id');
        if ($certId <= 0) {
            Session::flash('error', 'ID sertifikat tidak valid.');
            $this->redirect('/admin/sertifikat');
            return;
        }

        $db = Database::getInstance()->getConnection();
        try {
            // Tambah revoked_at jika kolom ada; jika tidak, hapus langsung
            $cols = $db->query("SHOW COLUMNS FROM certificates LIKE 'revoked_at'")->fetchAll();
            if (!empty($cols)) {
                $stmt = $db->prepare("UPDATE certificates SET revoked_at = NOW() WHERE id = :id");
            } else {
                $stmt = $db->prepare("DELETE FROM certificates WHERE id = :id");
            }
            $stmt->execute(['id' => $certId]);
            Session::flash('success', 'Sertifikat berhasil dicabut.');
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal mencabut sertifikat: ' . $e->getMessage());
        }

        $this->redirect('/admin/sertifikat');
    }

    /**
     * Export semua sertifikat ke CSV
     */
    public function exportCsv(): void
    {
        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->query("
                SELECT c.certificate_number, c.type, c.issued_at,
                       u.name AS user_name, u.email AS user_email, c.data_snapshot
                FROM certificates c
                LEFT JOIN users u ON c.user_id = u.id
                ORDER BY c.issued_at DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $rows = [];
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="sertifikat-' . date('Ymd-His') . '.csv"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($out, ['Nomor Sertifikat', 'Jenis', 'Nama Penerima', 'Email', 'Tanggal Terbit', 'Detail']);

        foreach ($rows as $row) {
            $data = json_decode($row['data_snapshot'] ?? '{}', true) ?: [];
            $detail = $row['type'] === 'donasi'
                ? ($data['species_summary'] ?? '') . ' — ' . ($data['total_trees'] ?? '') . ' pohon'
                : ($data['event_title'] ?? '');

            fputcsv($out, [
                $row['certificate_number'],
                ucfirst($row['type']),
                $row['user_name'],
                $row['user_email'],
                $row['issued_at'],
                $detail,
            ]);
        }
        fclose($out);
        exit;
    }
}

