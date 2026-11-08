<?php
/**
 * Model Notification — TanamPohon.id
 * 
 * Mengelola notifikasi dalam aplikasi (in-app notifications) untuk peserta:
 * pembayaran disetujui, bukti ditolak, event terkonfirmasi, lencana baru, referral, dll.
 */

require_once __DIR__ . '/../../core/Model.php';

class Notification extends Model
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'id';

    /**
     * Ambil notifikasi milik pengguna
     *
     * @param int $userId
     * @param string|null $filter 'unread' atau null untuk semua
     * @param int $limit
     * @return array
     */
    public function getUserNotifications(int $userId, ?string $filter = null, int $limit = 50): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";

        if ($filter === 'unread') {
            $sql .= " AND is_read = 0";
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['data_json'] = !empty($row['data']) ? json_decode($row['data'], true) : [];
        }

        return $rows;
    }

    /**
     * Hitung jumlah notifikasi yang belum dibaca
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM {$this->table} 
            WHERE user_id = :user_id AND is_read = 0
        ");
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Buat notifikasi baru untuk pengguna
     *
     * @param int $userId
     * @param string $type ('payment', 'donation', 'event', 'order', 'badge', 'referral', 'system')
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int ID notifikasi baru
     */
    public function createNotification(int $userId, string $type, string $title, string $message, ?array $data = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                user_id, type, title, message, data, is_read, created_at
            ) VALUES (
                :user_id, :type, :title, :message, :data, 0, NOW()
            )
        ");

        $stmt->execute([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Tandai sebuah notifikasi telah dibaca
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_read = 1, read_at = NOW() 
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'id'      => $notificationId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Tandai semua notifikasi pengguna telah dibaca
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = :user_id AND is_read = 0
        ");
        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Hapus sebuah notifikasi
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'id'      => $notificationId,
            'user_id' => $userId,
        ]);
    }
}

