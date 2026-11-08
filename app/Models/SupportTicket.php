<?php
/**
 * Model SupportTicket — TanamPohon.id
 *
 * Mengelola tiket bantuan user (support_tickets + support_replies).
 * Setiap akses membaca menyertakan user_id untuk mencegah IDOR.
 */

require_once __DIR__ . '/../../core/Model.php';

class SupportTicket extends Model
{
    protected string $table      = 'support_tickets';
    protected string $primaryKey = 'id';

    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    /**
     * Ambil semua tiket milik user beserta info balasan terakhir.
     *
     * @param int $userId
     * @return array
     */
    public function getUserTickets(int $userId): array
    {
        $sql = "SELECT
                    t.*,
                    (SELECT COUNT(*) FROM support_replies r WHERE r.ticket_id = t.id) AS reply_count,
                    (SELECT r2.message FROM support_replies r2
                     WHERE r2.ticket_id = t.id ORDER BY r2.created_at DESC LIMIT 1) AS last_reply,
                    (SELECT r3.is_admin FROM support_replies r3
                     WHERE r3.ticket_id = t.id ORDER BY r3.created_at DESC LIMIT 1) AS last_reply_is_admin,
                    (SELECT r4.created_at FROM support_replies r4
                     WHERE r4.ticket_id = t.id ORDER BY r4.created_at DESC LIMIT 1) AS last_reply_at
                FROM {$this->table} t
                WHERE t.user_id = :user_id
                ORDER BY t.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil satu tiket lengkap beserta semua balasannya (anti-IDOR).
     *
     * @param int $ticketId
     * @param int $userId
     * @return array|null  ['ticket' => array, 'replies' => array]
     */
    public function getTicketWithReplies(int $ticketId, int $userId): ?array
    {
        // Ambil tiket (anti-IDOR)
        $stmt1 = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt1->execute([':id' => $ticketId, ':user_id' => $userId]);
        $ticket = $stmt1->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            return null;
        }

        // Ambil semua balasan
        $stmt2 = $this->db->prepare(
            "SELECT sr.*, u.name AS replier_name, u.avatar AS replier_avatar
             FROM support_replies sr
             LEFT JOIN users u ON u.id = sr.user_id
             WHERE sr.ticket_id = :ticket_id
             ORDER BY sr.created_at ASC"
        );
        $stmt2->execute([':ticket_id' => $ticketId]);
        $replies = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return [
            'ticket'  => $ticket,
            'replies' => $replies,
        ];
    }

    /**
     * Ambil tiket berdasarkan ID saja (untuk admin — tanpa filter user_id).
     *
     * @param int $ticketId
     * @return array|null
     */
    public function findByIdAdmin(int $ticketId): ?array
    {
        $sql = "SELECT t.*, u.name AS user_name, u.email AS user_email
                FROM {$this->table} t
                LEFT JOIN users u ON u.id = t.user_id
                WHERE t.id = :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $ticketId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Hitung tiket milik user berdasarkan status.
     *
     * @param int    $userId
     * @param string $status  'open'|'replied'|'closed'|''
     * @return int
     */
    public function countByStatus(int $userId, string $status = ''): int
    {
        if ($status) {
            $sql = "SELECT COUNT(*) FROM {$this->table}
                    WHERE user_id = :user_id AND status = :status";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId, ':status' => $status]);
            return (int)$stmt->fetchColumn();
        }

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    // -------------------------------------------------------------------------
    // WRITE
    // -------------------------------------------------------------------------

    /**
     * Buat tiket bantuan baru.
     *
     * @param int    $userId
     * @param string $subject
     * @param string $message
     * @return int  ID tiket baru
     */
    public function createTicket(int $userId, string $subject, string $message): int
    {
        $sql = "INSERT INTO {$this->table} (user_id, subject, message, status, created_at, updated_at)
                VALUES (:user_id, :subject, :message, 'open', NOW(), NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':subject' => $subject,
            ':message' => $message,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Tambah balasan ke tiket.
     * Otomatis update status tiket:
     *  - Admin membalas → status 'replied'
     *  - User membalas → status 'open'
     *
     * @param int    $ticketId
     * @param int    $userId
     * @param string $message
     * @param bool   $isAdmin
     * @return int  ID reply baru
     */
    public function addReply(int $ticketId, int $userId, string $message, bool $isAdmin = false): int
    {
        // Simpan balasan
        $sql = "INSERT INTO support_replies (ticket_id, user_id, message, is_admin, created_at)
                VALUES (:ticket_id, :user_id, :message, :is_admin, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':user_id'   => $userId,
            ':message'   => $message,
            ':is_admin'  => $isAdmin ? 1 : 0,
        ]);

        $replyId = (int)$this->db->lastInsertId();

        // Update status & timestamp tiket
        $newStatus = $isAdmin ? 'replied' : 'open';
        $stmt2 = $this->db->prepare("UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id");
        $stmt2->execute([':status' => $newStatus, ':id' => $ticketId]);

        return $replyId;
    }

    /**
     * Tutup tiket (hanya pemilik tiket atau admin).
     *
     * @param int $ticketId
     * @param int $userId   Pass 0 jika admin
     * @return bool
     */
    public function closeTicket(int $ticketId, int $userId): bool
    {
        if ($userId > 0) {
            // Validasi kepemilikan
            $stmt1 = $this->db->prepare("SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1");
            $stmt1->execute([':id' => $ticketId, ':user_id' => $userId]);
            $ticket = $stmt1->fetch(PDO::FETCH_ASSOC);
            if (!$ticket) {
                return false;
            }
        }

        $stmt2 = $this->db->prepare("UPDATE {$this->table} SET status = 'closed', updated_at = NOW() WHERE id = :id");
        $stmt2->execute([':id' => $ticketId]);

        return true;
    }
}
