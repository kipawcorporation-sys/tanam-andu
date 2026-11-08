<?php
/**
 * Model PaymentProof — TanamPohon.id
 * 
 * Mengelola berkas bukti transfer yang diunggah peserta,
 * deteksi hash berkas ganda, dan verifikasi admin.
 */

require_once __DIR__ . '/../../core/Model.php';

class PaymentProof extends Model
{
    protected string $table = 'payment_proofs';
    protected string $primaryKey = 'id';

    /**
     * Simpan bukti transfer baru
     *
     * @param array $data
     * @return int ID bukti transfer
     */
    public function createProof(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (
            payment_id, bank_account_id, sender_bank, sender_name,
            amount_transferred, transfer_date, transfer_time, proof_file,
            file_hash, note, status, created_at
        ) VALUES (
            :payment_id, :bank_account_id, :sender_bank, :sender_name,
            :amount_transferred, :transfer_date, :transfer_time, :proof_file,
            :file_hash, :note, :status, NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'payment_id'         => $data['payment_id'],
            'bank_account_id'    => $data['bank_account_id'] ?? null,
            'sender_bank'        => trim($data['sender_bank']),
            'sender_name'        => trim($data['sender_name']),
            'amount_transferred' => $data['amount_transferred'],
            'transfer_date'      => $data['transfer_date'],
            'transfer_time'      => $data['transfer_time'] ?? date('H:i:s'),
            'proof_file'         => $data['proof_file'],
            'file_hash'          => $data['file_hash'] ?? null,
            'note'               => $data['note'] ?? null,
            'status'             => 'menunggu',
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Cari apakah berkas yang sama pernah diunggah di transaksi lain (deteksi duplikat)
     *
     * @param string $hash SHA-256 hash dari file
     * @param int|null $excludePaymentId Abaikan transaksi saat ini
     * @return array|false
     */
    public function findByFileHash(string $hash, ?int $excludePaymentId = null): array|false
    {
        if (empty($hash)) {
            return false;
        }

        $sql = "
            SELECT pp.*, p.invoice_no, p.grand_total, p.status as payment_status
            FROM {$this->table} pp
            JOIN payments p ON pp.payment_id = p.id
            WHERE pp.file_hash = :hash
        ";

        if ($excludePaymentId !== null) {
            $sql .= " AND pp.payment_id != :exclude_id";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $params = ['hash' => $hash];
        if ($excludePaymentId !== null) {
            $params['exclude_id'] = $excludePaymentId;
        }

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cari bukti berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT pp.*, p.invoice_no, p.user_id, p.guest_email
            FROM {$this->table} pp
            JOIN payments p ON pp.payment_id = p.id
            WHERE pp.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update status verifikasi bukti
     *
     * @param int $proofId
     * @param string $status 'disetujui' atau 'ditolak'
     * @param int $verifierId User ID admin
     * @param string|null $rejectedReason Alasan penolakan jika ditolak
     * @return bool
     */
    public function verifyProof(int $proofId, string $status, int $verifierId, ?string $rejectedReason = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = :status, 
                rejected_reason = :rejected_reason, 
                verified_by = :verifier_id, 
                verified_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            'status'          => $status,
            'rejected_reason' => $rejectedReason,
            'verifier_id'     => $verifierId,
            'id'              => $proofId,
        ]);
    }
}

