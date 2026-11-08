<?php
/**
 * AdminPaymentController — Plant For Fun
 * Kerenceng Coffee Planting & Agroforestry Expedition
 *
 * Antrian & Sistem Verifikasi Pembayaran Manual Transfer Bank:
 * - Dashboard metrik realtime (Pending amount, Today approved, Duplicate alerts)
 * - Lightbox modal inspeksi bukti transfer dengan zoom, rotasi 90°, dan komparasi nominal otomatis
 * - Batch Approve (Persetujuan Masal dengan 1-klik)
 * - Deteksi Anti-Fraud Duplikat File Bukti (SHA-256 Hash Matching)
 * - Generator pesan konfirmasi instan via WhatsApp ke pembayar
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminPaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    /**
     * GET /admin/verifikasi-pembayaran — Antrian verifikasi pembayaran
     */
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        $statusFilter = $_GET['status'] ?? 'menunggu_verifikasi';
        $typeFilter   = $_GET['type'] ?? '';
        $bankFilter   = $_GET['bank'] ?? '';
        $keyword      = trim($_GET['q'] ?? '');

        $page         = max(1, (int)($_GET['page'] ?? 1));
        $perPage      = 15;
        $offset       = ($page - 1) * $perPage;

        $where  = [];
        $params = [];

        if (!empty($statusFilter)) {
            $where[] = "p.status = :status";
            $params[':status'] = $statusFilter;
        }

        if (!empty($typeFilter)) {
            $where[] = "p.payable_type = :type";
            $params[':type'] = $typeFilter;
        }

        if (!empty($bankFilter)) {
            $where[] = "pp.sender_bank LIKE :bank";
            $params[':bank'] = '%' . $bankFilter . '%';
        }

        if (!empty($keyword)) {
            $where[] = "(p.invoice_no LIKE :kw1 OR u.name LIKE :kw2 OR u.email LIKE :kw3 OR pp.sender_name LIKE :kw4)";
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
            $params[':kw4'] = '%' . $keyword . '%';
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : '';

        // Hitung total dengan filter
        $totalStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM payments p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN payment_proofs pp ON pp.payment_id = p.id
            {$whereClause}
        ");
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        // Ambil data pembayaran
        $stmt = $db->prepare("
            SELECT
                p.id, p.invoice_no, p.payable_type, p.payable_id,
                p.total_amount, p.unique_code, p.grand_total, p.status, p.created_at, p.updated_at,
                COALESCE(u.name, p.guest_email, 'Tamu') AS payer_name,
                COALESCE(u.email, p.guest_email, '') AS payer_email,
                COALESCE(u.phone, '') AS payer_phone,
                pp.id AS proof_id,
                pp.sender_name, pp.sender_bank,
                pp.amount_transferred, pp.transfer_date, pp.transfer_time,
                pp.proof_file, pp.file_hash, pp.note AS proof_note,
                pp.status AS proof_status, pp.rejected_reason, pp.verified_at,
                va.name AS verifier_name
            FROM payments p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN payment_proofs pp ON pp.payment_id = p.id
            LEFT JOIN users va ON va.id = pp.verified_by
            {$whereClause}
            ORDER BY (p.status = 'menunggu_verifikasi') DESC, p.updated_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format proof file URLs dan hitung selisih nominal
        foreach ($payments as &$p) {
            $p['proof_url'] = $this->resolveProofUrl($p['proof_file'] ?? null);
            $p['nominal_diff'] = 0;
            $p['is_match'] = false;
            if (!empty($p['amount_transferred'])) {
                $p['nominal_diff'] = (float)$p['amount_transferred'] - (float)$p['grand_total'];
                $p['is_match'] = (abs($p['nominal_diff']) < 1);
            }
        }
        unset($p);

        // Deteksi duplikat file_hash (Anti-Fraud)
        $hashes = array_filter(array_column($payments, 'file_hash'));
        $duplicateHashes = [];
        if (!empty($hashes)) {
            $placeholders = implode(',', array_fill(0, count($hashes), '?'));
            $dup = $db->prepare("
                SELECT file_hash, COUNT(*) AS cnt
                FROM payment_proofs
                WHERE file_hash IN ($placeholders)
                GROUP BY file_hash HAVING cnt > 1
            ");
            $dup->execute(array_values($hashes));
            foreach ($dup->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $duplicateHashes[$row['file_hash']] = true;
            }
        }

        // Metrik & Statistik Ringkas
        $stats = [
            'pending_count'         => (int)$db->query("SELECT COUNT(*) FROM payments WHERE status = 'menunggu_verifikasi'")->fetchColumn(),
            'pending_amount'        => (float)$db->query("SELECT COALESCE(SUM(grand_total), 0) FROM payments WHERE status = 'menunggu_verifikasi'")->fetchColumn(),
            'today_approved_count'  => (int)$db->query("SELECT COUNT(*) FROM payments WHERE status = 'berhasil' AND DATE(updated_at) = CURDATE()")->fetchColumn(),
            'today_approved_amount' => (float)$db->query("SELECT COALESCE(SUM(grand_total), 0) FROM payments WHERE status = 'berhasil' AND DATE(updated_at) = CURDATE()")->fetchColumn(),
            'duplicate_count'       => count($duplicateHashes),
        ];

        // Daftar bank pengirim untuk filter
        $banks = $db->query("
            SELECT DISTINCT sender_bank FROM payment_proofs WHERE sender_bank IS NOT NULL AND sender_bank != '' ORDER BY sender_bank ASC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $this->view('admin/payments', [
            'pageTitle'            => 'Verifikasi Pembayaran Manual',
            'pendingPaymentsCount' => $stats['pending_count'],
            'payments'             => $payments,
            'stats'                => $stats,
            'duplicateHashes'      => $duplicateHashes,
            'banks'                => $banks,
            'statusFilter'         => $statusFilter,
            'typeFilter'           => $typeFilter,
            'bankFilter'           => $bankFilter,
            'keyword'              => $keyword,
            'total'                => $total,
            'page'                 => $page,
            'perPage'              => $perPage,
            'totalPages'           => (int)ceil($total / $perPage),
        ]);
    }

    /**
     * GET /admin/verifikasi-pembayaran/{id} — Detail pembayaran lengkap + bukti transfer
     */
    public function show(int $id): void
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT
                p.*,
                COALESCE(u.name, p.guest_email, 'Tamu') AS payer_name,
                u.email AS payer_email, u.phone AS payer_phone,
                pp.id AS proof_id, pp.sender_name, pp.sender_bank,
                pp.amount_transferred, pp.transfer_date, pp.transfer_time,
                pp.proof_file, pp.file_hash, pp.note AS proof_note,
                pp.status AS proof_status, pp.rejected_reason, pp.verified_at,
                va.name AS verifier_name
            FROM payments p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN payment_proofs pp ON pp.payment_id = p.id
            LEFT JOIN users va ON va.id = pp.verified_by
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            Session::flash('error', 'Data pembayaran tidak ditemukan.');
            $this->redirect('/admin/verifikasi-pembayaran');
            return;
        }

        $payment['proof_url'] = $this->resolveProofUrl($payment['proof_file'] ?? null);

        // Cek duplikat hash
        $isDuplicateHash = false;
        if (!empty($payment['file_hash'])) {
            $cnt = $db->prepare("SELECT COUNT(*) FROM payment_proofs WHERE file_hash = :h AND id != :pid");
            $cnt->execute([':h' => $payment['file_hash'], ':pid' => $payment['proof_id'] ?? 0]);
            $isDuplicateHash = (int)$cnt->fetchColumn() > 0;
        }

        // Ambil data entitas terkait (Donasi, Order Kopi, atau Peserta Event)
        $relatedData = $this->getRelatedData($db, $payment['payable_type'], (int)$payment['payable_id']);

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/payment_detail', [
            'pageTitle'            => 'Detail Invoice #' . $payment['invoice_no'],
            'pendingPaymentsCount' => $pendingCount,
            'payment'              => $payment,
            'relatedData'          => $relatedData,
            'isDuplicateHash'      => $isDuplicateHash,
        ]);
    }

    /**
     * POST /admin/verifikasi-pembayaran/{id}/setujui — Approve pembayaran manual
     */
    public function approve(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/verifikasi-pembayaran/' . $id);
            return;
        }

        $adminId = (int)auth('id');
        $db      = Database::getInstance()->getConnection();

        $payment = $db->prepare("SELECT * FROM payments WHERE id = :id AND status = 'menunggu_verifikasi' LIMIT 1");
        $payment->execute([':id' => $id]);
        $payment = $payment->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            Session::flash('error', 'Pembayaran tidak ditemukan atau sudah diverifikasi sebelumnya.');
            $this->redirect('/admin/verifikasi-pembayaran');
            return;
        }

        $db->beginTransaction();
        try {
            // Update status pembayaran
            $db->prepare("
                UPDATE payments
                SET status = 'berhasil', paid_at = NOW(), updated_at = NOW()
                WHERE id = :id
            ")->execute([':id' => $id]);

            // Update status bukti transfer
            $db->prepare("
                UPDATE payment_proofs
                SET status = 'disetujui', verified_by = :admin, verified_at = NOW()
                WHERE payment_id = :pid
            ")->execute([':admin' => $adminId, ':pid' => $id]);

            // Aktifkan entitas terkait (Donasi -> Terbit Pohon, Order -> Diproses, Participant -> Terkonfirmasi)
            $this->activateRelatedEntity($db, $payment['payable_type'], (int)$payment['payable_id'], $adminId);

            $db->commit();
            Session::flash('success', "Pembayaran invoice #{$payment['invoice_no']} senilai Rp " . number_format($payment['grand_total'], 0, ',', '.') . " berhasil disetujui!");

            // Kirim notifikasi WhatsApp ke user via Fonnte
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                $fonnte->notifyByPaymentId($id, 'approved');
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::flash('error', 'Gagal menyetujui pembayaran: ' . $e->getMessage());
        }

        $this->redirect('/admin/verifikasi-pembayaran');
    }

    /**
     * POST /admin/verifikasi-pembayaran/batch-setujui — Persetujuan masal sekaligus (Batch Approve)
     */
    public function batchApprove(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/verifikasi-pembayaran');
            return;
        }

        $ids = $_POST['payment_ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            Session::flash('error', 'Tidak ada pembayaran yang dipilih untuk disetujui.');
            $this->redirect('/admin/verifikasi-pembayaran');
            return;
        }

        $adminId = (int)auth('id');
        $db      = Database::getInstance()->getConnection();

        $approvedCount = 0;
        $totalNominal  = 0;

        $db->beginTransaction();
        try {
            foreach ($ids as $id) {
                $id = (int)$id;
                $stmt = $db->prepare("SELECT * FROM payments WHERE id = :id AND status = 'menunggu_verifikasi' LIMIT 1");
                $stmt->execute([':id' => $id]);
                $payment = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($payment) {
                    $db->prepare("
                        UPDATE payments SET status = 'berhasil', paid_at = NOW(), updated_at = NOW() WHERE id = :id
                    ")->execute([':id' => $id]);

                    $db->prepare("
                        UPDATE payment_proofs SET status = 'disetujui', verified_by = :admin, verified_at = NOW() WHERE payment_id = :pid
                    ")->execute([':admin' => $adminId, ':pid' => $id]);

                    $this->activateRelatedEntity($db, $payment['payable_type'], (int)$payment['payable_id'], $adminId);

                    $approvedCount++;
                    $totalNominal += (float)$payment['grand_total'];
                }
            }
            $db->commit();

            Session::flash('success', "Berhasil menyetujui {$approvedCount} pembayaran sekaligus dengan total Rp " . number_format($totalNominal, 0, ',', '.') . "!");

            // Kirim notifikasi WhatsApp ke seluruh user yang disetujui
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                foreach ($ids as $approvedId) {
                    $fonnte->notifyByPaymentId((int)$approvedId, 'approved');
                }
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::flash('error', 'Terjadi kesalahan saat memproses persetujuan masal: ' . $e->getMessage());
        }

        $this->redirect('/admin/verifikasi-pembayaran');
    }

    /**
     * POST /admin/verifikasi-pembayaran/{id}/tolak — Reject pembayaran (alasan wajib)
     */
    public function reject(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/verifikasi-pembayaran/' . $id);
            return;
        }

        $reason  = trim($_POST['reason'] ?? '');
        $adminId = (int)auth('id');
        $db      = Database::getInstance()->getConnection();

        if (empty($reason) || mb_strlen($reason) < 10) {
            Session::flash('error', 'Alasan penolakan wajib diisi minimal 10 karakter agar jelas bagi pembeli/donatur.');
            $this->redirect('/admin/verifikasi-pembayaran/' . $id);
            return;
        }

        $payment = $db->prepare("SELECT * FROM payments WHERE id = :id AND status = 'menunggu_verifikasi' LIMIT 1");
        $payment->execute([':id' => $id]);
        $payment = $payment->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            Session::flash('error', 'Pembayaran tidak ditemukan atau sudah diproses.');
            $this->redirect('/admin/verifikasi-pembayaran');
            return;
        }

        $db->beginTransaction();
        try {
            $db->prepare("UPDATE payments SET status = 'ditolak', updated_at = NOW() WHERE id = :id")
               ->execute([':id' => $id]);

            $db->prepare("
                UPDATE payment_proofs
                SET status = 'ditolak', rejected_reason = :reason,
                    verified_by = :admin, verified_at = NOW()
                WHERE payment_id = :pid
            ")->execute([':reason' => $reason, ':admin' => $adminId, ':pid' => $id]);

            $db->commit();
            Session::flash('success', "Pembayaran invoice #{$payment['invoice_no']} telah ditolak. Alasan: {$reason}");

            // Kirim notifikasi WhatsApp ke user bahwa pembayaran ditolak beserta alasannya
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                $fonnte->notifyByPaymentId($id, 'rejected', ['reason' => $reason]);
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::flash('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }

        $this->redirect('/admin/verifikasi-pembayaran');
    }

    // -------------------------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------------------------

    private function resolveProofUrl(?string $file): ?string
    {
        if (empty($file)) return null;
        if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
            return $file;
        }
        if (str_starts_with($file, '/storage/')) {
            return $file;
        }
        if (str_starts_with($file, 'payment_proofs/')) {
            return '/storage/uploads/' . $file;
        }
        return '/storage/uploads/payment_proofs/' . ltrim($file, '/');
    }

    private function getRelatedData(\PDO $db, string $type, int $id): array
    {
        return match ($type) {
            'donation' => $db->query("
                SELECT d.*, ts.name AS species_name
                FROM donations d
                LEFT JOIN donation_items di ON di.donation_id = d.id
                LEFT JOIN tree_species ts ON ts.id = di.tree_species_id
                WHERE d.id = {$id} LIMIT 1
            ")->fetch(PDO::FETCH_ASSOC) ?: [],

            'order' => $db->query("
                SELECT o.*,
                       GROUP_CONCAT(oi.product_name, ' (', oi.variant_info, ') x', oi.quantity SEPARATOR ', ') AS items_summary
                FROM orders o
                LEFT JOIN order_items oi ON oi.order_id = o.id
                WHERE o.id = {$id}
                GROUP BY o.id LIMIT 1
            ")->fetch(PDO::FETCH_ASSOC) ?: [],

            'participant' => $db->query("
                SELECT pa.*, e.title AS event_title, e.event_date,
                       u.name AS participant_name
                FROM participants pa
                LEFT JOIN planting_events e ON e.id = pa.event_id
                LEFT JOIN users u ON u.id = pa.user_id
                WHERE pa.id = {$id} LIMIT 1
            ")->fetch(PDO::FETCH_ASSOC) ?: [],

            default => [],
        };
    }

    private function activateRelatedEntity(\PDO $db, string $type, int $id, int $adminId): void
    {
        match ($type) {
            'donation' => $db->prepare("
                UPDATE donations SET status = 'berhasil', updated_at = NOW() WHERE id = :id
            ")->execute([':id' => $id]),

            'order' => $db->prepare("
                UPDATE orders SET status = 'diproses', updated_at = NOW() WHERE id = :id
            ")->execute([':id' => $id]),

            'participant' => $db->prepare("
                UPDATE participants SET registration_status = 'terkonfirmasi', updated_at = NOW() WHERE id = :id
            ")->execute([':id' => $id]),

            default => null,
        };
    }
}
