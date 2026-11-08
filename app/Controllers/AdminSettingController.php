<?php
/**
 * AdminSettingController — TanamPohon.id
 * Pengaturan umum platform & manajemen otomasi terjadwal (Cron Job).
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Services/CronService.php';

class AdminSettingController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
        $this->db = Database::getInstance()->getConnection();
    }

    public function index(): void
    {
        $pendingCount = (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        // Ambil semua settings dari tabel settings
        $settings = [];
        try {
            $rows = $this->db->query("SELECT `key`, `value` FROM settings")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $settings[$row['key']] = $row['value'];
            }
        } catch (\Exception $e) {
            // Fallback
        }

        // Ambil riwayat log eksekusi cron terbaru
        $cronLogs = [];
        try {
            $stmtLogs = $this->db->query("
                SELECT action, description, created_at 
                FROM activity_logs 
                WHERE action LIKE 'cron_%' 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            if ($stmtLogs) {
                $cronLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {}

        $lastCronRun = $settings['last_cron_run'] ?? null;
        $cronSecret = env('CRON_SECRET_TOKEN', 'tanampohon_secret_cron_token_2026');
        $cronWebhookUrl = url('/cron/run?token=' . urlencode($cronSecret));
        $cronCliCommand = 'php ' . BASE_PATH . '/cron/run.php > /dev/null 2>&1';

        $this->view('admin/settings', [
            'pageTitle'            => 'Pengaturan Sistem & Cron Job',
            'pendingPaymentsCount' => $pendingCount,
            'settings'             => $settings,
            'lastCronRun'          => $lastCronRun,
            'cronSecret'           => $cronSecret,
            'cronWebhookUrl'       => $cronWebhookUrl,
            'cronCliCommand'       => $cronCliCommand,
            'cronLogs'             => $cronLogs,
        ]);
    }

    public function update(): void
    {
        if (!$this->isPost()) { 
            $this->redirect('/admin/pengaturan'); 
            return; 
        }

        $fields = [
            'app_name'              => trim($_POST['app_name'] ?? 'TanamPohon.id'),
            'app_tagline'           => trim($_POST['app_tagline'] ?? ''),
            'admin_email'           => trim($_POST['admin_email'] ?? ''),
            'whatsapp_support'      => trim($_POST['whatsapp_support'] ?? ''),
            'payment_timeout_hours' => (int)($_POST['payment_timeout_hours'] ?? 24),
            'referral_bonus_points' => (int)($_POST['referral_bonus_points'] ?? 25),
            'fonnte_token'          => trim($_POST['fonnte_token'] ?? ''),
            'fonnte_enabled'        => isset($_POST['fonnte_enabled']) ? '1' : '0',
            'fonnte_sender'         => trim($_POST['fonnte_sender'] ?? ''),
        ];

        $stmt = $this->db->prepare("
            INSERT INTO settings (`key`, `value`, `group`, `type`, `updated_at`)
            VALUES (:key, :value, 'general', 'text', NOW())
            ON DUPLICATE KEY UPDATE `value` = :value_update, `updated_at` = NOW()
        ");

        foreach ($fields as $key => $val) {
            $stmt->execute([
                'key'          => $key,
                'value'        => (string)$val,
                'value_update' => (string)$val,
            ]);
        }

        Session::flash('success', 'Pengaturan sistem berhasil disimpan.');
        $this->redirect('/admin/pengaturan');
    }

    /**
     * Memicu eksekusi cron job secara manual langsung dari admin panel
     */
    public function runCron(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/pengaturan');
            return;
        }

        $cron = new CronService();
        $res = $cron->runAll();

        $msg = sprintf(
            "Cron Job berhasil dijalankan dalam %s detik! %d pembayaran kedaluwarsa dibatalkan (%d stok & %d kuota dipulihkan), %d pengingat pembayaran terkirim, %d pengingat event terkirim.",
            $res['execution_time_sec'],
            $res['expired_payments']['total_cancelled'],
            $res['expired_payments']['restored_stock_items'],
            $res['expired_payments']['restored_event_quotas'],
            $res['payment_reminders']['total_reminded'],
            $res['event_reminders']['total_reminders']
        );

        Session::flash('success', $msg);
        $this->redirect('/admin/pengaturan');
    }

    /**
     * POST /admin/pengaturan/fonnte/test — Kirim pesan WhatsApp uji coba via Fonnte API
     */
    public function testFonnte(): void
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit;
        }

        $phone = trim($this->request->input('phone', ''));
        if (empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Nomor WhatsApp tujuan tes wajib diisi.']);
            exit;
        }

        require_once __DIR__ . '/../Services/FonnteService.php';
        $fonnte = new FonnteService();
        $res = $fonnte->testConnection($phone);
        echo json_encode($res);
        exit;
    }
}
