<?php
/**
 * Model Payment — TanamPohon.id
 * 
 * Mengelola transaksi pembayaran manual (transfer bank / e-wallet / QRIS).
 * Polimorfik untuk: donasi (donation), pesanan kaos (order), pendaftaran event (participant).
 */

require_once __DIR__ . '/../../core/Model.php';

class Payment extends Model
{
    protected string $table = 'payments';
    protected string $primaryKey = 'id';

    /**
     * Buat transaksi pembayaran baru
     *
     * @param array $data
     * @return int ID pembayaran yang baru dibuat
     */
    public function createPayment(array $data): int
    {
        $deadlineHours = (int)config('app.payment_deadline_hours', 24);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$deadlineHours} hours"));

        $sql = "INSERT INTO {$this->table} (
            invoice_no, payable_type, payable_id, user_id, guest_email,
            total_amount, unique_code, grand_total, status, expires_at, created_at, updated_at
        ) VALUES (
            :invoice_no, :payable_type, :payable_id, :user_id, :guest_email,
            :total_amount, :unique_code, :grand_total, :status, :expires_at, NOW(), NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'invoice_no'   => $data['invoice_no'],
            'payable_type' => $data['payable_type'],
            'payable_id'   => $data['payable_id'],
            'user_id'      => $data['user_id'] ?? null,
            'guest_email'  => $data['guest_email'] ?? null,
            'total_amount' => $data['total_amount'],
            'unique_code'  => $data['unique_code'] ?? 0,
            'grand_total'  => $data['grand_total'],
            'status'       => $data['status'] ?? 'menunggu_pembayaran',
            'expires_at'   => $expiresAt,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Cari pembayaran berdasarkan nomor invoice
     *
     * @param string $invoiceNo
     * @return array|false
     */
    public function findByInvoice(string $invoiceNo): array|false
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as user_name, u.email as user_email
            FROM {$this->table} p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.invoice_no = :invoice_no 
            LIMIT 1
        ");
        $stmt->execute(['invoice_no' => trim($invoiceNo)]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            return false;
        }

        // Cek apakah sudah kedaluwarsa secara otomatis
        if ($payment['status'] === 'menunggu_pembayaran' && strtotime($payment['expires_at']) < time()) {
            $this->updateStatus($payment['id'], 'kedaluwarsa');
            $payment['status'] = 'kedaluwarsa';
        }

        return $payment;
    }

    /**
     * Cari pembayaran berdasarkan nomor invoice dan email (untuk tamu / guest check)
     *
     * @param string $invoiceNo
     * @param string $email
     * @return array|false
     */
    public function findByInvoiceAndEmail(string $invoiceNo, string $email): array|false
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as user_name, u.email as user_email
            FROM {$this->table} p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.invoice_no = :invoice_no 
              AND (p.guest_email = :email1 OR u.email = :email2)
            LIMIT 1
        ");
        $stmt->execute([
            'invoice_no' => trim($invoiceNo),
            'email1'     => strtolower(trim($email)),
            'email2'     => strtolower(trim($email)),
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update status pembayaran
     *
     * @param int $paymentId
     * @param string $status
     * @param string|null $paidAt
     * @return bool
     */
    public function updateStatus(int $paymentId, string $status, ?string $paidAt = null): bool
    {
        $fields = ["status = :status", "updated_at = NOW()"];
        $params = [
            'status' => $status,
            'id'     => $paymentId,
        ];

        if ($paidAt !== null) {
            $fields[] = "paid_at = :paid_at";
            $params['paid_at'] = $paidAt;
        } elseif ($status === 'berhasil') {
            $fields[] = "paid_at = NOW()";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Ambil riwayat bukti transfer untuk pembayaran tertentu
     *
     * @param int $paymentId
     * @return array
     */
    public function getPaymentProofs(int $paymentId): array
    {
        $stmt = $this->db->prepare("
            SELECT pp.*, b.bank_name, b.account_number, b.account_holder, u.name as verifier_name
            FROM payment_proofs pp
            LEFT JOIN bank_accounts b ON pp.bank_account_id = b.id
            LEFT JOIN users u ON pp.verified_by = u.id
            WHERE pp.payment_id = :payment_id
            ORDER BY pp.created_at DESC
        ");
        $stmt->execute(['payment_id' => $paymentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil detail entitas yang dibayar (Polimorfik)
     *
     * @param array $payment
     * @return array|null
     */
    public function getPayableDetails(array $payment): ?array
    {
        $type = $payment['payable_type'];
        $id = $payment['payable_id'];

        if ($type === 'donation') {
            $stmt = $this->db->prepare("
                SELECT d.*, di.quantity, di.price_per_tree, di.subtotal, ts.name as species_name
                FROM donations d
                LEFT JOIN donation_items di ON di.donation_id = d.id
                LEFT JOIN tree_species ts ON di.tree_species_id = ts.id
                WHERE d.id = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($type === 'order') {
            $stmt = $this->db->prepare("
                SELECT o.*, oi.product_name, oi.variant_info, oi.quantity, oi.price, oi.subtotal
                FROM orders o
                LEFT JOIN order_items oi ON oi.order_id = o.id
                WHERE o.id = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($type === 'participant') {
            $stmt = $this->db->prepare("
                SELECT p.*, pe.title as event_title, pe.event_date, pe.gathering_point
                FROM participants p
                LEFT JOIN planting_events pe ON p.event_id = pe.id
                WHERE p.id = :id
                LIMIT 1
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }
}

