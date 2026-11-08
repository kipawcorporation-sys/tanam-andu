<?php
/**
 * Model UserAddress — TanamPohon.id
 *
 * Mengelola alamat pengiriman milik user (CRUD + set utama).
 * Setiap operasi menulis menyertakan user_id untuk mencegah IDOR.
 */

require_once __DIR__ . '/../../core/Model.php';

class UserAddress extends Model
{
    protected string $table      = 'user_addresses';
    protected string $primaryKey = 'id';

    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    /**
     * Ambil semua alamat milik user, diurutkan: utama dulu, lalu terbaru.
     *
     * @param int $userId
     * @return array
     */
    public function getUserAddresses(int $userId): array
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY is_primary DESC, created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil satu alamat berdasarkan id + user_id (anti-IDOR).
     *
     * @param int $id
     * @param int $userId
     * @return array|null
     */
    public function findById(int $id, int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE id = :id AND user_id = :user_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Hitung jumlah alamat yang dimiliki user.
     *
     * @param int $userId
     * @return int
     */
    public function countByUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil alamat utama (is_primary = 1) milik user.
     *
     * @param int $userId
     * @return array|null
     */
    public function getPrimary(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id AND is_primary = 1
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // -------------------------------------------------------------------------
    // WRITE
    // -------------------------------------------------------------------------

    /**
     * Tambah alamat baru.
     * Jika ini adalah alamat pertama user, otomatis jadi utama.
     *
     * @param array $data  Harus mengandung: user_id, label, recipient_name, phone,
     *                     province, city, district, postal_code, full_address
     * @return int  ID alamat yang baru dibuat
     */
    public function create(array $data): int
    {
        // Jika belum ada alamat sama sekali, jadikan utama
        $count = $this->countByUser((int)$data['user_id']);
        $isPrimary = ($count === 0) ? 1 : 0;

        $sql = "INSERT INTO {$this->table}
                    (user_id, label, recipient_name, phone, province, city,
                     district, postal_code, full_address, is_primary, created_at)
                VALUES
                    (:user_id, :label, :recipient_name, :phone, :province, :city,
                     :district, :postal_code, :full_address, :is_primary, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':label'          => $data['label'],
            ':recipient_name' => $data['recipient_name'],
            ':phone'          => $data['phone'],
            ':province'       => $data['province'],
            ':city'           => $data['city'],
            ':district'       => $data['district'],
            ':postal_code'    => $data['postal_code'],
            ':full_address'   => $data['full_address'],
            ':is_primary'     => $isPrimary,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update alamat yang sudah ada (anti-IDOR: sertakan user_id).
     *
     * @param int   $id
     * @param int   $userId
     * @param array $data
     * @return bool
     */
    public function updateAddress(int $id, int $userId, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET label          = :label,
                    recipient_name = :recipient_name,
                    phone          = :phone,
                    province       = :province,
                    city           = :city,
                    district       = :district,
                    postal_code    = :postal_code,
                    full_address   = :full_address
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':label'          => $data['label'],
            ':recipient_name' => $data['recipient_name'],
            ':phone'          => $data['phone'],
            ':province'       => $data['province'],
            ':city'           => $data['city'],
            ':district'       => $data['district'],
            ':postal_code'    => $data['postal_code'],
            ':full_address'   => $data['full_address'],
            ':id'             => $id,
            ':user_id'        => $userId,
        ]);

        return $stmt->rowCount() >= 0;
    }

    /**
     * Hapus alamat (anti-IDOR: sertakan user_id).
     * Tidak diizinkan menghapus alamat utama jika masih ada alamat lain.
     *
     * @param int $id
     * @param int $userId
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteAddress(int $id, int $userId): array
    {
        $address = $this->findById($id, $userId);
        if (!$address) {
            return ['success' => false, 'message' => 'Alamat tidak ditemukan.'];
        }

        // Jika ini alamat utama dan masih ada alamat lain → tolak
        if ($address['is_primary'] && $this->countByUser($userId) > 1) {
            return [
                'success' => false,
                'message' => 'Tidak dapat menghapus alamat utama. Silakan pilih alamat utama yang lain terlebih dahulu.',
            ];
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        return ['success' => true, 'message' => 'Alamat berhasil dihapus.'];
    }

    /**
     * Jadikan alamat sebagai alamat utama.
     * Lepas flag utama dari semua alamat user lain terlebih dahulu.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function setPrimary(int $id, int $userId): bool
    {
        // Pastikan alamat ini milik user
        $address = $this->findById($id, $userId);
        if (!$address) {
            return false;
        }

        // Lepas semua flag utama milik user ini
        $stmt1 = $this->db->prepare("UPDATE {$this->table} SET is_primary = 0 WHERE user_id = :user_id");
        $stmt1->execute([':user_id' => $userId]);

        // Set alamat ini sebagai utama
        $stmt2 = $this->db->prepare("UPDATE {$this->table} SET is_primary = 1 WHERE id = :id AND user_id = :user_id");
        $stmt2->execute([':id' => $id, ':user_id' => $userId]);

        return true;
    }
}
