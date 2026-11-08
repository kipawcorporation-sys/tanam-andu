<?php
/**
 * Model Ticket — TanamPohon.id
 * 
 * Mengelola E-Ticket peserta relawan, nomor tiket unik,
 * data payload QR Code, dan status check-in.
 */

require_once __DIR__ . '/../../core/Model.php';

class Ticket extends Model
{
    protected string $table = 'tickets';
    protected string $primaryKey = 'id';

    /**
     * Terbitkan tiket baru untuk peserta
     *
     * @param int $participantId
     * @return array Data tiket yang dibuat
     */
    public function issueTicket(int $participantId): array
    {
        $ticketCode = 'TIKET-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $qrData = json_encode([
            'ticket_code'    => $ticketCode,
            'participant_id' => $participantId,
            'verified_url'   => url('/tiket/' . $ticketCode),
        ]);

        $sql = "INSERT INTO {$this->table} (
            participant_id, ticket_code, qr_data, status, created_at
        ) VALUES (
            :participant_id, :ticket_code, :qr_data, 'active', NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'participant_id' => $participantId,
            'ticket_code'    => $ticketCode,
            'qr_data'        => $qrData,
        ]);

        $ticketId = (int)$this->db->lastInsertId();

        return [
            'id'          => $ticketId,
            'ticket_code' => $ticketCode,
            'qr_data'     => $qrData,
            'status'      => 'active',
        ];
    }

    /**
     * Cari tiket berdasarkan kode tiket unik
     *
     * @param string $ticketCode
     * @return array|false
     */
    public function findByCode(string $ticketCode): array|false
    {
        $stmt = $this->db->prepare("
            SELECT t.*, p.registration_status, p.tshirt_size, p.notes as participant_notes,
                   u.name as participant_name, u.email as participant_email, u.phone as participant_phone,
                   e.id as event_id, e.title as event_title, e.event_date, e.gathering_time, e.gathering_point,
                   e.coordinator_name, e.coordinator_phone, e.includes_tshirt, e.items_to_bring,
                   l.name as location_name, l.city as location_city
            FROM {$this->table} t
            JOIN participants p ON t.participant_id = p.id
            JOIN users u ON p.user_id = u.id
            JOIN planting_events e ON p.event_id = e.id
            LEFT JOIN planting_locations l ON e.location_id = l.id
            WHERE t.ticket_code = :code
            LIMIT 1
        ");
        $stmt->execute(['code' => trim($ticketCode)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check-in peserta via scan QR
     *
     * @param int $ticketId
     * @param int $adminId
     * @return bool
     */
    public function checkIn(int $ticketId, int $adminId): bool
    {
        $this->db->beginTransaction();
        try {
            // Update status tiket
            $stmt1 = $this->db->prepare("UPDATE {$this->table} SET status = 'used' WHERE id = :id");
            $stmt1->execute(['id' => $ticketId]);

            // Update status peserta
            $stmt2 = $this->db->prepare("
                UPDATE participants p
                JOIN {$this->table} t ON t.participant_id = p.id
                SET p.registration_status = 'hadir', 
                    p.checked_in_at = NOW(), 
                    p.checked_in_by = :admin_id,
                    p.updated_at = NOW()
                WHERE t.id = :ticket_id
            ");
            $stmt2->execute(['admin_id' => $adminId, 'ticket_id' => $ticketId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}

