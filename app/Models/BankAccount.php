<?php
/**
 * Model BankAccount — TanamPohon.id
 * 
 * Mengelola data rekening bank dan QRIS penerima pembayaran.
 */

require_once __DIR__ . '/../../core/Model.php';

class BankAccount extends Model
{
    protected string $table = 'bank_accounts';
    protected string $primaryKey = 'id';

    /**
     * Ambil semua rekening bank yang aktif diurutkan: Rekening Utama pertama, lalu sort_order
     *
     * @return array
     */
    public function getActiveAccounts(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE is_active = 1 
            ORDER BY is_primary DESC, sort_order ASC, id ASC
        ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$item) {
            $item['qris_image_url'] = $this->resolveImageUrl($item['qris_image'] ?? null);
            $item['bank_logo_url'] = $this->resolveImageUrl($item['bank_logo'] ?? null);
        }
        unset($item);

        return $results;
    }

    /**
     * Cari rekening berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row['qris_image_url'] = $this->resolveImageUrl($row['qris_image'] ?? null);
            $row['bank_logo_url'] = $this->resolveImageUrl($row['bank_logo'] ?? null);
        }
        return $row;
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
