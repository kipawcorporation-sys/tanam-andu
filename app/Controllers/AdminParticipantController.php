<?php
/**
 * AdminParticipantController — TanamPohon.id
 * Daftar peserta + QR check-in via kamera HTML5.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminParticipantController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    public function index(): void
    {
        $db      = Database::getInstance()->getConnection();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $eventFilter  = (int)($_GET['event_id'] ?? 0);
        $statusFilter = $_GET['status'] ?? '';

        $conditions = [];
        $params     = [];

        if ($eventFilter) {
            $conditions[] = 'pa.event_id = :event_id';
            $params[':event_id'] = $eventFilter;
        }
        if ($statusFilter) {
            $conditions[] = 'pa.registration_status = :status';
            $params[':status'] = $statusFilter;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $totalStmt = $db->prepare("SELECT COUNT(*) FROM participants pa $where");
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        $stmt = $db->prepare("
            SELECT
                pa.id, pa.registration_status, pa.tshirt_size, pa.checked_in_at,
                pa.created_at, pa.notes,
                u.name AS participant_name, u.email, u.phone,
                e.title AS event_title, e.event_date,
                t.ticket_code, t.status AS ticket_status,
                c.certificate_number, c.issued_at AS certificate_issued_at
            FROM participants pa
            LEFT JOIN users u ON u.id = pa.user_id
            LEFT JOIN planting_events e ON e.id = pa.event_id
            LEFT JOIN tickets t ON t.participant_id = pa.id
            LEFT JOIN certificates c ON (c.certifiable_type = 'participant' AND c.certifiable_id = pa.id)
            $where
            ORDER BY pa.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = $db->query("SELECT id, title, event_date FROM planting_events ORDER BY event_date DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        // Metrik global peserta
        $stats = [
            'total'         => (int)$db->query("SELECT COUNT(*) FROM participants")->fetchColumn(),
            'hadir'         => (int)$db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'hadir'")->fetchColumn(),
            'terkonfirmasi' => (int)$db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'terkonfirmasi'")->fetchColumn(),
            'menunggu'      => (int)$db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'menunggu'")->fetchColumn(),
            'batal'         => (int)$db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'batal'")->fetchColumn(),
        ];
        $tshirtStats = $db->query("
            SELECT tshirt_size, COUNT(*) as count 
            FROM participants 
            WHERE tshirt_size IS NOT NULL AND tshirt_size != '' 
            GROUP BY tshirt_size 
            ORDER BY count DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/participants', [
            'pageTitle'           => 'Manajemen Peserta & Presensi',
            'pendingPaymentsCount'=> $pendingCount,
            'participants'        => $participants,
            'events'              => $events,
            'eventFilter'         => $eventFilter,
            'statusFilter'        => $statusFilter,
            'total'               => $total,
            'page'                => $page,
            'perPage'             => $perPage,
            'totalPages'          => (int)ceil($total / $perPage),
            'stats'               => $stats,
            'tshirtStats'         => $tshirtStats,
        ]);
    }

    /**
     * GET /admin/checkin-qr — Halaman scanner QR check-in.
     */
    public function qrScanner(): void
    {
        $db = Database::getInstance()->getConnection();
        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        // Ambil semua kegiatan (upcoming & ongoing)
        $upcomingEvents = $db->query("
            SELECT id, title, event_date, registered_count, quota, status, gathering_point AS location_name
            FROM planting_events
            WHERE status IN ('upcoming','ongoing')
            ORDER BY event_date ASC LIMIT 20
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Statistik check-in hari ini
        $todayCheckin = (int)$db->query("SELECT COUNT(*) FROM participants WHERE DATE(checked_in_at) = CURDATE()")->fetchColumn();
        $totalCheckin = (int)$db->query("SELECT COUNT(*) FROM participants WHERE checked_in_at IS NOT NULL")->fetchColumn();
        $totalConfirmed = (int)$db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'terkonfirmasi'")->fetchColumn();
        $totalHadir    = (int)$db->query("SELECT COUNT(*) FROM participants WHERE registration_status = 'hadir'")->fetchColumn();

        // Riwayat check-in terbaru (10 terakhir)
        $recentCheckins = $db->query("
            SELECT pa.id, pa.checked_in_at, pa.tshirt_size, pa.registration_status,
                   u.name AS participant_name, u.email,
                   e.title AS event_title,
                   t.ticket_code
            FROM participants pa
            JOIN users u ON u.id = pa.user_id
            JOIN planting_events e ON e.id = pa.event_id
            LEFT JOIN tickets t ON t.participant_id = pa.id
            WHERE pa.checked_in_at IS NOT NULL
            ORDER BY pa.checked_in_at DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Daftar peserta terkonfirmasi belum hadir (untuk pencarian manual)
        $pendingParticipants = $db->query("
            SELECT pa.id, pa.tshirt_size, pa.registration_status,
                   u.name AS participant_name, u.email,
                   e.title AS event_title, e.event_date,
                   t.ticket_code
            FROM participants pa
            JOIN users u ON u.id = pa.user_id
            JOIN planting_events e ON e.id = pa.event_id
            LEFT JOIN tickets t ON t.participant_id = pa.id
            WHERE pa.registration_status = 'terkonfirmasi'
            ORDER BY e.event_date ASC, u.name ASC
            LIMIT 50
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/qr_checkin', [
            'pageTitle'            => 'Check-in QR Scanner',
            'pendingPaymentsCount' => $pendingCount,
            'upcomingEvents'       => $upcomingEvents,
            'todayCheckin'         => $todayCheckin,
            'totalCheckin'         => $totalCheckin,
            'totalConfirmed'       => $totalConfirmed,
            'totalHadir'           => $totalHadir,
            'recentCheckins'       => $recentCheckins,
            'pendingParticipants'  => $pendingParticipants,
        ]);
    }

    /**
     * POST /admin/checkin-qr/proses — Proses check-in dari data QR.
     */
    public function processCheckin(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metode tidak valid.']);
            return;
        }

        header('Content-Type: application/json');

        $ticketCode = trim($_POST['ticket_code'] ?? '');
        $adminId    = (int)auth('id');

        if (empty($ticketCode)) {
            echo json_encode(['success' => false, 'message' => 'Kode tiket tidak boleh kosong.']);
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Ambil tiket + peserta + kegiatan
        $stmt = $db->prepare("
            SELECT
                t.id AS ticket_id, t.ticket_code, t.status AS ticket_status,
                pa.id AS participant_id, pa.registration_status, pa.checked_in_at,
                pa.tshirt_size,
                u.name AS participant_name, u.email,
                e.title AS event_title, e.event_date
            FROM tickets t
            JOIN participants pa ON pa.id = t.participant_id
            JOIN users u ON u.id = pa.user_id
            JOIN planting_events e ON e.id = pa.event_id
            WHERE t.ticket_code = :code
            LIMIT 1
        ");
        $stmt->execute([':code' => $ticketCode]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            echo json_encode(['success' => false, 'message' => "Kode tiket '{$ticketCode}' tidak ditemukan."]);
            exit;
        }

        // Sudah check-in sebelumnya?
        if (!empty($ticket['checked_in_at'])) {
            echo json_encode([
                'success' => false,
                'duplicate' => true,
                'message' => 'Peserta sudah check-in pada ' . date('d M Y H:i', strtotime($ticket['checked_in_at'])),
                'data' => $ticket,
            ]);
            exit;
        }

        // Status bukan terkonfirmasi?
        if ($ticket['registration_status'] !== 'terkonfirmasi') {
            echo json_encode([
                'success' => false,
                'message' => 'Status peserta tidak valid: ' . $ticket['registration_status'],
                'data' => $ticket,
            ]);
            exit;
        }

        // Proses check-in
        $db->prepare("
            UPDATE participants
            SET registration_status = 'hadir', checked_in_at = NOW(), checked_in_by = :admin, updated_at = NOW()
            WHERE id = :id
        ")->execute([':admin' => $adminId, ':id' => $ticket['participant_id']]);

        $db->prepare("UPDATE tickets SET status = 'used' WHERE id = :id")
           ->execute([':id' => $ticket['ticket_id']]);

        // Otomatis terbitkan sertifikat penghargaan relawan
        try {
            require_once __DIR__ . '/../Models/Certificate.php';
            $certModel = new Certificate();
            $cert = $certModel->issueParticipantCertificate((int)$ticket['participant_id']);
            $ticket['certificate_number'] = $cert['certificate_number'] ?? null;
        } catch (\Exception $e) {
            // Lanjut jika terjadi kendala minor sertifikat
        }

        echo json_encode([
            'success' => true,
            'message' => "Check-in berhasil! Selamat datang, {$ticket['participant_name']}. Sertifikat relawan telah diterbitkan.",
            'data'    => $ticket,
        ]);
        exit;
    }

    /**
     * POST /admin/peserta/{id}/konfirmasi — Konfirmasi manual pendaftaran peserta.
     */
    public function confirm(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/peserta'); return; }

        $db = Database::getInstance()->getConnection();
        $db->prepare("
            UPDATE participants SET registration_status = 'terkonfirmasi', updated_at = NOW()
            WHERE id = :id
        ")->execute([':id' => $id]);

        Session::flash('success', 'Peserta berhasil dikonfirmasi.');
        $this->redirect('/admin/peserta');
    }

    /**
     * POST /admin/peserta/{id}/checkin — Check-in manual peserta langsung dari tabel.
     */
    public function manualCheckin(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/peserta'); return; }

        $adminId = (int)auth('id');
        $db = Database::getInstance()->getConnection();
        $db->prepare("
            UPDATE participants 
            SET registration_status = 'hadir', checked_in_at = NOW(), checked_in_by = :admin, updated_at = NOW()
            WHERE id = :id
        ")->execute([':admin' => $adminId, ':id' => $id]);

        $db->prepare("UPDATE tickets SET status = 'used' WHERE participant_id = :id")
           ->execute([':id' => $id]);

        // Otomatis terbitkan sertifikat relawan
        $certNum = '';
        try {
            require_once __DIR__ . '/../Models/Certificate.php';
            $certModel = new Certificate();
            $cert = $certModel->issueParticipantCertificate($id);
            $certNum = $cert['certificate_number'] ?? '';
        } catch (\Exception $e) {}

        $msg = "Peserta #{$id} berhasil ditandai HADIR.";
        if ($certNum) {
            $msg .= " Sertifikat digital {$certNum} otomatis diterbitkan!";
        }

        Session::flash('success', $msg);
        $this->redirect('/admin/peserta');
    }

    /**
     * POST /admin/peserta/{id}/terbitkan-sertifikat — Terbitkan manual sertifikat relawan.
     */
    public function issueCertificate(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/peserta'); return; }

        $db = Database::getInstance()->getConnection();

        try {
            // Pastikan peserta ada dan ubah ke 'hadir' jika belum
            $stmt = $db->prepare("SELECT id, user_id, registration_status FROM participants WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant) {
                Session::flash('error', 'Data peserta tidak ditemukan.');
                $this->redirect('/admin/peserta');
                return;
            }

            if ($participant['registration_status'] !== 'hadir') {
                $db->prepare("UPDATE participants SET registration_status = 'hadir', checked_in_at = COALESCE(checked_in_at, NOW()), updated_at = NOW() WHERE id = :id")
                   ->execute(['id' => $id]);
            }

            require_once __DIR__ . '/../Models/Certificate.php';
            $certModel = new Certificate();
            $cert = $certModel->issueParticipantCertificate($id);

            Session::flash('success', "Sertifikat Relawan {$cert['certificate_number']} berhasil diterbitkan!");
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menerbitkan sertifikat: ' . $e->getMessage());
        }

        $this->redirect('/admin/peserta');
    }

    /**
     * POST /admin/peserta/{id}/batal — Batalkan pendaftaran peserta.
     */
    public function cancel(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/peserta'); return; }

        $db = Database::getInstance()->getConnection();
        $db->prepare("
            UPDATE participants SET registration_status = 'batal', updated_at = NOW()
            WHERE id = :id
        ")->execute([':id' => $id]);

        $db->prepare("UPDATE tickets SET status = 'cancelled' WHERE participant_id = :id")
           ->execute([':id' => $id]);

        Session::flash('warning', "Pendaftaran peserta #{$id} telah dibatalkan.");
        $this->redirect('/admin/peserta');
    }

    /**
     * GET /admin/peserta/export-csv — Unduh seluruh data peserta format CSV.
     */
    public function exportCsv(): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT
                pa.id, pa.registration_status, pa.tshirt_size, pa.checked_in_at,
                pa.created_at, pa.notes,
                u.name AS participant_name, u.email, u.phone,
                e.title AS event_title, e.event_date,
                t.ticket_code, t.status AS ticket_status
            FROM participants pa
            LEFT JOIN users u ON u.id = pa.user_id
            LEFT JOIN planting_events e ON e.id = pa.event_id
            LEFT JOIN tickets t ON t.participant_id = pa.id
            ORDER BY pa.created_at DESC
        ");
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="daftar_peserta_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($out, ['ID', 'Nama Peserta', 'Email', 'No Telepon', 'Nama Kegiatan', 'Tanggal Kegiatan', 'Status Pendaftaran', 'Ukuran Kaos', 'Kode Tiket', 'Status Tiket', 'Waktu Check-in', 'Waktu Daftar', 'Catatan'], ',', '"', "\\");

        foreach ($participants as $p) {
            fputcsv($out, [
                $p['id'],
                $p['participant_name'],
                $p['email'],
                $p['phone'] ?? '',
                $p['event_title'],
                $p['event_date'],
                $p['registration_status'],
                $p['tshirt_size'] ?? '',
                $p['ticket_code'] ?? '',
                $p['ticket_status'] ?? '',
                $p['checked_in_at'] ?? 'Belum Hadir',
                $p['created_at'],
                $p['notes'] ?? ''
            ], ',', '"', "\\");
        }
        fclose($out);
        exit;
    }
}

