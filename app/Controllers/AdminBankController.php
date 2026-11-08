<?php
/**
 * AdminBankController — TanamPohon.id
 * Manajemen rekening bank, kanal QRIS & e-wallet penerima transfer donasi/belanja.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminBankController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // Ambil daftar rekening beserta statistik verifikasi pembayarannya
        $stmt = $db->query("
            SELECT b.*,
                   COUNT(pp.id) AS total_proofs,
                   COUNT(CASE WHEN p.status = 'berhasil' THEN 1 END) AS approved_count,
                   COALESCE(SUM(CASE WHEN p.status = 'berhasil' THEN p.grand_total ELSE 0 END), 0) AS total_received
            FROM bank_accounts b
            LEFT JOIN payment_proofs pp ON pp.bank_account_id = b.id
            LEFT JOIN payments p ON pp.payment_id = p.id
            GROUP BY b.id
            ORDER BY b.is_primary DESC, b.sort_order ASC, b.id ASC
        ");
        $banks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format path gambar / QRIS
        foreach ($banks as &$bank) {
            $bank['qris_image_url'] = $this->resolveImageUrl($bank['qris_image'] ?? null);
            $bank['bank_logo_url'] = $this->resolveImageUrl($bank['bank_logo'] ?? null);
        }
        unset($bank);

        // Statistik ringkas
        $totalAccounts = count($banks);
        $activeAccounts = count(array_filter($banks, fn($b) => (int)$b['is_active'] === 1));
        $qrisAccounts = count(array_filter($banks, fn($b) => $b['channel_type'] === 'qris' && (int)$b['is_active'] === 1));
        $totalReceivedAll = array_sum(array_column($banks, 'total_received'));

        $primaryBank = null;
        foreach ($banks as $b) {
            if (!empty($b['is_primary'])) {
                $primaryBank = $b;
                break;
            }
        }

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status = 'menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/banks', [
            'pageTitle'            => 'Rekening Bank & QRIS Pembayaran',
            'pendingPaymentsCount' => $pendingCount,
            'banks'                => $banks,
            'stats'                => [
                'total_accounts'   => $totalAccounts,
                'active_accounts'  => $activeAccounts,
                'qris_accounts'    => $qrisAccounts,
                'total_received'   => $totalReceivedAll,
                'primary_bank'     => $primaryBank,
            ],
        ]);
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/rekening');
            return;
        }

        $bankName      = trim($_POST['bank_name'] ?? '');
        $channelType   = trim($_POST['channel_type'] ?? 'bank_transfer');
        $accountNumber = trim($_POST['account_number'] ?? '');
        $accountHolder = trim($_POST['account_holder'] ?? '');
        $branch        = trim($_POST['branch'] ?? '');
        $instructions  = trim($_POST['instructions'] ?? '');
        $sortOrder     = (int)($_POST['sort_order'] ?? 0);
        $isPrimary     = isset($_POST['is_primary']) ? 1 : 0;
        $isActive      = isset($_POST['is_active']) ? 1 : 1;

        if (empty($bankName) || empty($accountNumber) || empty($accountHolder)) {
            Session::flash('error', 'Nama Bank, Nomor Rekening/NMID, dan Nama Pemilik Rekening wajib diisi.');
            $this->redirect('/admin/rekening');
            return;
        }

        // Upload berkas QRIS jika ada
        $qrisImage = null;
        if (!empty($_FILES['qris_image']['name']) && $_FILES['qris_image']['error'] === UPLOAD_ERR_OK) {
            $qrisImage = $this->handleQrisUpload($_FILES['qris_image']);
        }

        $db = Database::getInstance()->getConnection();

        // Jika rekening ini diset sebagai Utama, reset rekening utama sebelumnya
        if ($isPrimary) {
            $db->exec("UPDATE bank_accounts SET is_primary = 0");
        }

        $stmt = $db->prepare("
            INSERT INTO bank_accounts (
                bank_name, channel_type, account_number, account_holder,
                branch, instructions, qris_image, sort_order, is_active, is_primary,
                created_at, updated_at
            ) VALUES (
                :bn, :ct, :an, :ah,
                :br, :ins, :qi, :so, :ia, :ip,
                NOW(), NOW()
            )
        ");

        $stmt->execute([
            ':bn'  => $bankName,
            ':ct'  => in_array($channelType, ['bank_transfer', 'qris', 'e_wallet']) ? $channelType : 'bank_transfer',
            ':an'  => $accountNumber,
            ':ah'  => $accountHolder,
            ':br'  => $branch ?: null,
            ':ins' => $instructions ?: null,
            ':qi'  => $qrisImage,
            ':so'  => $sortOrder,
            ':ia'  => $isActive,
            ':ip'  => $isPrimary,
        ]);

        Session::flash('success', "Kanal pembayaran '{$bankName}' berhasil ditambahkan ke sistem.");
        $this->redirect('/admin/rekening');
    }

    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/rekening');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $curr = $db->prepare("SELECT * FROM bank_accounts WHERE id = :id");
        $curr->execute([':id' => $id]);
        $existing = $curr->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            Session::flash('error', 'Rekening tidak ditemukan.');
            $this->redirect('/admin/rekening');
            return;
        }

        $bankName      = trim($_POST['bank_name'] ?? '');
        $channelType   = trim($_POST['channel_type'] ?? $existing['channel_type']);
        $accountNumber = trim($_POST['account_number'] ?? '');
        $accountHolder = trim($_POST['account_holder'] ?? '');
        $branch        = trim($_POST['branch'] ?? '');
        $instructions  = trim($_POST['instructions'] ?? '');
        $sortOrder     = (int)($_POST['sort_order'] ?? 0);
        $isPrimary     = isset($_POST['is_primary']) ? 1 : 0;
        $isActive      = isset($_POST['is_active']) ? 1 : 0;

        if (empty($bankName) || empty($accountNumber) || empty($accountHolder)) {
            Session::flash('error', 'Nama Bank, Nomor Rekening/NMID, dan Nama Pemilik Rekening wajib diisi.');
            $this->redirect('/admin/rekening');
            return;
        }

        // Upload berkas QRIS baru jika diunggah
        $qrisImage = $existing['qris_image'];
        if (!empty($_FILES['qris_image']['name']) && $_FILES['qris_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleQrisUpload($_FILES['qris_image']);
            if ($uploaded) {
                $qrisImage = $uploaded;
            }
        }

        // Jika diset sebagai Utama, reset rekening utama lainnya
        if ($isPrimary) {
            $db->exec("UPDATE bank_accounts SET is_primary = 0");
            $isActive = 1; // Rekening utama otomatis aktif
        }

        $stmt = $db->prepare("
            UPDATE bank_accounts
            SET bank_name = :bn, channel_type = :ct, account_number = :an, account_holder = :ah,
                branch = :br, instructions = :ins, qris_image = :qi,
                sort_order = :so, is_active = :ia, is_primary = :ip, updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':bn'  => $bankName,
            ':ct'  => in_array($channelType, ['bank_transfer', 'qris', 'e_wallet']) ? $channelType : 'bank_transfer',
            ':an'  => $accountNumber,
            ':ah'  => $accountHolder,
            ':br'  => $branch ?: null,
            ':ins' => $instructions ?: null,
            ':qi'  => $qrisImage,
            ':so'  => $sortOrder,
            ':ia'  => $isActive,
            ':ip'  => $isPrimary,
            ':id'  => $id,
        ]);

        Session::flash('success', "Informasi rekening '{$bankName}' berhasil diperbarui.");
        $this->redirect('/admin/rekening');
    }

    public function toggleActive(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/rekening');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, bank_name, is_active, is_primary FROM bank_accounts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $bank = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            Session::flash('error', 'Rekening tidak ditemukan.');
            $this->redirect('/admin/rekening');
            return;
        }

        // Jika rekening utama dinonaktifkan, peringatkan
        if ($bank['is_primary'] && $bank['is_active']) {
            Session::flash('warning', "Perhatian: Rekening {$bank['bank_name']} adalah Rekening Utama. Tetapkan rekening lain sebagai Utama terlebih dahulu sebelum menonaktifkan.");
            $this->redirect('/admin/rekening');
            return;
        }

        $newStatus = $bank['is_active'] ? 0 : 1;
        $up = $db->prepare("UPDATE bank_accounts SET is_active = :ia, updated_at = NOW() WHERE id = :id");
        $up->execute([':ia' => $newStatus, ':id' => $id]);

        $statusLabel = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        Session::flash('success', "Status rekening {$bank['bank_name']} berhasil {$statusLabel}.");
        $this->redirect('/admin/rekening');
    }

    public function setPrimary(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/rekening');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, bank_name FROM bank_accounts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $bank = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            Session::flash('error', 'Rekening tidak ditemukan.');
            $this->redirect('/admin/rekening');
            return;
        }

        // Reset semua primary
        $db->exec("UPDATE bank_accounts SET is_primary = 0");

        // Jadikan target sebagai primary dan otomatis aktif
        $up = $db->prepare("UPDATE bank_accounts SET is_primary = 1, is_active = 1, updated_at = NOW() WHERE id = :id");
        $up->execute([':id' => $id]);

        Session::flash('success', "Rekening {$bank['bank_name']} sekarang ditetapkan sebagai Rekening Utama.");
        $this->redirect('/admin/rekening');
    }

    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/rekening');
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        // Cek apakah masih digunakan di payment_proofs
        $count = $db->prepare("SELECT COUNT(*) FROM payment_proofs WHERE bank_account_id = :id");
        $count->execute([':id' => $id]);
        if ((int)$count->fetchColumn() > 0) {
            Session::flash('error', 'Rekening tidak dapat dihapus karena tercatat dalam histori bukti pembayaran donatur/pesanan.');
            $this->redirect('/admin/rekening');
            return;
        }

        // Cek apakah rekening utama
        $target = $db->prepare("SELECT bank_name, is_primary FROM bank_accounts WHERE id = :id");
        $target->execute([':id' => $id]);
        $row = $target->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['is_primary']) {
            Session::flash('error', 'Rekening Utama tidak dapat dihapus langsung. Tunjuk rekening lain sebagai Rekening Utama terlebih dahulu.');
            $this->redirect('/admin/rekening');
            return;
        }

        $db->prepare("DELETE FROM bank_accounts WHERE id = :id")->execute([':id' => $id]);
        Session::flash('success', 'Rekening berhasil dihapus.');
        $this->redirect('/admin/rekening');
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    private function handleQrisUpload(array $file): ?string
    {
        $uploadDir = BASE_PATH . '/storage/uploads/qris/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return null;
        }

        $filename = 'qris_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return 'uploads/qris/' . $filename;
        }

        return null;
    }

    private function resolveImageUrl(?string $file): ?string
    {
        if (empty($file)) return null;
        if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
            return $file;
        }
        $file = ltrim($file, '/');
        if (str_starts_with($file, 'storage/')) {
            return '/' . $file;
        }
        if (str_starts_with($file, 'uploads/')) {
            return '/storage/' . $file;
        }
        return '/storage/uploads/' . $file;
    }
}
