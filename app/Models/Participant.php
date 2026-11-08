<?php
/**
 * Model Participant — TanamPohon.id
 * 
 * Mengelola pendaftaran peserta relawan ke acara penanaman pohon,
 * ukuran kaos relawan, status pendaftaran, dan integrasi tiket QR.
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/PlantingEvent.php';
require_once __DIR__ . '/Ticket.php';
require_once __DIR__ . '/Payment.php';

class Participant extends Model
{
    protected string $table = 'participants';
    protected string $primaryKey = 'id';

    /**
     * Daftarkan peserta ke dalam kegiatan penanaman
     *
     * @param array $participantData Data pendaftaran relawan
     * @param array $event Data kegiatan
     * @return array
     * @throws Exception
     */
    public function registerToEvent(array $participantData, array $event): array
    {
        $this->db->beginTransaction();

        try {
            $userId = (int)$participantData['user_id'];
            $eventId = (int)$event['id'];

            // 1. Cek apakah user sudah pernah mendaftar ke event ini
            $stmtCheck = $this->db->prepare("
                SELECT id, registration_status FROM {$this->table} 
                WHERE user_id = :user_id AND event_id = :event_id 
                  AND registration_status != 'batal'
                LIMIT 1
            ");
            $stmtCheck->execute(['user_id' => $userId, 'event_id' => $eventId]);
            if ($stmtCheck->fetch()) {
                throw new \Exception('Anda sudah terdaftar dalam kegiatan penanaman ini.');
            }

            // 2. Cek ketersediaan kuota
            $eventModel = new PlantingEvent();
            if (!$eventModel->isQuotaAvailable($eventId)) {
                throw new \Exception('Maaf, kuota peserta untuk kegiatan ini sudah penuh.');
            }

            $fee = (float)$event['registration_fee'];
            $isPaid = ($fee > 0);
            $initialStatus = $isPaid ? 'menunggu' : 'terkonfirmasi';

            // 3. Simpan data peserta
            $sqlParticipant = "INSERT INTO {$this->table} (
                user_id, event_id, registration_status, tshirt_size, notes, created_at, updated_at
            ) VALUES (
                :user_id, :event_id, :status, :tshirt_size, :notes, NOW(), NOW()
            )";

            $stmtPart = $this->db->prepare($sqlParticipant);
            $stmtPart->execute([
                'user_id'     => $userId,
                'event_id'    => $eventId,
                'status'      => $initialStatus,
                'tshirt_size' => $participantData['tshirt_size'] ?? 'L',
                'notes'       => $participantData['notes'] ?? null,
            ]);

            $participantId = (int)$this->db->lastInsertId();

            // 4. Tambah kuota terdaftar di event
            $eventModel->incrementRegistered($eventId);

            $result = [
                'participant_id' => $participantId,
                'event_id'       => $eventId,
                'is_paid'        => $isPaid,
            ];

            // 5. Jika kegiatan gratis: langsung terbitkan e-ticket QR
            if (!$isPaid) {
                $ticketModel = new Ticket();
                $ticket = $ticketModel->issueTicket($participantId);
                $result['ticket_code'] = $ticket['ticket_code'];
            } else {
                // Jika kegiatan berbayar / bundle kaos: buat invoice pembayaran manual
                $isUniqueCodeActive = config('app.unique_code_enabled', true);
                $uniqueCode = $isUniqueCodeActive ? generate_unique_code(100, 999) : 0;
                $grandTotal = $fee + $uniqueCode;
                $invoiceNo = generate_invoice_number('INV');

                $paymentModel = new Payment();
                $paymentId = $paymentModel->createPayment([
                    'invoice_no'   => $invoiceNo,
                    'payable_type' => 'participant',
                    'payable_id'   => $participantId,
                    'user_id'      => $userId,
                    'total_amount' => $fee,
                    'unique_code'  => $uniqueCode,
                    'grand_total'  => $grandTotal,
                    'status'       => 'menunggu_pembayaran',
                ]);

                // Hubungkan payment_id ke peserta
                $stmtUpdate = $this->db->prepare("UPDATE {$this->table} SET payment_id = :payment_id WHERE id = :id");
                $stmtUpdate->execute(['payment_id' => $paymentId, 'id' => $participantId]);

                $result['payment_id'] = $paymentId;
                $result['invoice_no'] = $invoiceNo;
            }

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Ambil riwayat keikutsertaan event milik pengguna secara lengkap beserta tiket, feedback, & sertifikat
     *
     * @param int $userId
     * @param string $filter 'all'|'upcoming'|'completed'|'cancelled'
     * @return array
     */
    public function getUserParticipationsWithDetails(int $userId, string $filter = 'all'): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   e.id as event_id, e.title as event_title, e.description as event_desc,
                   e.event_date, e.gathering_time, e.gathering_point, e.google_maps_link,
                   e.registration_fee, e.includes_tshirt, e.items_to_bring,
                   e.coordinator_name, e.coordinator_phone, e.status as event_status, e.cancel_deadline,
                   l.name as location_name, l.city as location_city, l.province as location_province,
                   t.ticket_code, t.status as ticket_status,
                   pay.invoice_no, pay.status as payment_status, pay.grand_total, pay.expires_at as payment_expires_at,
                   ef.id as feedback_id, ef.rating as feedback_rating, ef.comment as feedback_comment,
                   c.id as certificate_id, c.certificate_number
            FROM {$this->table} p
            JOIN planting_events e ON p.event_id = e.id
            LEFT JOIN planting_locations l ON e.location_id = l.id
            LEFT JOIN tickets t ON t.participant_id = p.id
            LEFT JOIN payments pay ON p.payment_id = pay.id
            LEFT JOIN event_feedbacks ef ON ef.participant_id = p.id
            LEFT JOIN certificates c ON c.certifiable_type = 'participant' AND c.certifiable_id = p.id
            WHERE p.user_id = :user_id
            ORDER BY e.event_date DESC, p.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $today = date('Y-m-d');
        $filtered = [];

        foreach ($rows as $item) {
            $isCancelled = ($item['registration_status'] === 'batal');
            $isAttended = ($item['registration_status'] === 'hadir');
            $isPast = ($item['event_date'] < $today);
            $isUpcoming = (!$isCancelled && !$isPast);

            $item['is_upcoming'] = $isUpcoming;
            $item['is_past'] = $isPast;
            $item['is_cancellable'] = ($isUpcoming && in_array($item['registration_status'], ['menunggu', 'terkonfirmasi']));

            if ($filter === 'upcoming' && $isUpcoming) {
                $filtered[] = $item;
            } elseif ($filter === 'completed' && ($isAttended || ($isPast && !$isCancelled))) {
                $filtered[] = $item;
            } elseif ($filter === 'cancelled' && $isCancelled) {
                $filtered[] = $item;
            } elseif ($filter === 'all') {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /**
     * Batalkan pendaftaran kegiatan sebelum batas waktu hari-H
     *
     * @param int $participantId
     * @param int $userId
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function cancelRegistration(int $participantId, int $userId, string $reason = 'Dibatalkan oleh peserta'): bool
    {
        $this->db->beginTransaction();

        try {
            // Ambil data pendaftaran
            $stmt = $this->db->prepare("
                SELECT p.*, e.event_date 
                FROM {$this->table} p
                JOIN planting_events e ON p.event_id = e.id
                WHERE p.id = :id AND p.user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute(['id' => $participantId, 'user_id' => $userId]);
            $part = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$part) {
                throw new \Exception('Data pendaftaran relawan tidak ditemukan.');
            }

            if ($part['registration_status'] === 'batal') {
                throw new \Exception('Pendaftaran ini sudah dibatalkan sebelumnya.');
            }

            if ($part['registration_status'] === 'hadir') {
                throw new \Exception('Pendaftaran tidak dapat dibatalkan karena Anda sudah melakukan check-in.');
            }

            // Cek batas tanggal: tidak bisa dibatalkan jika tanggal event sudah tiba / lewat
            $today = date('Y-m-d');
            if ($part['event_date'] <= $today) {
                throw new \Exception('Pembatalan hanya dapat dilakukan minimal H-1 sebelum tanggal pelaksanaan kegiatan.');
            }

            // Update status peserta menjadi batal
            $stmtCancel = $this->db->prepare("
                UPDATE {$this->table}
                SET registration_status = 'batal',
                    cancelled_at = NOW(),
                    cancel_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtCancel->execute([
                'reason' => $reason,
                'id'     => $participantId,
            ]);

            // Kembalikan kuota di event jika status sebelumnya terkonfirmasi/menunggu
            $stmtEvent = $this->db->prepare("
                UPDATE planting_events 
                SET registered_count = GREATEST(0, registered_count - 1)
                WHERE id = :event_id
            ");
            $stmtEvent->execute(['event_id' => $part['event_id']]);

            // Jika ada tiket, nonaktifkan tiket
            $stmtTicket = $this->db->prepare("
                UPDATE tickets SET status = 'cancelled' WHERE participant_id = :part_id
            ");
            $stmtTicket->execute(['part_id' => $participantId]);

            // Jika ada payment dan belum lunas, ubah status payment
            if (!empty($part['payment_id'])) {
                $stmtPay = $this->db->prepare("
                    UPDATE payments SET status = 'kedaluwarsa' 
                    WHERE id = :payment_id AND status = 'menunggu_pembayaran'
                ");
                $stmtPay->execute(['payment_id' => $part['payment_id']]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Kirim ulasan dan rating pasca kegiatan
     *
     * @param int $participantId
     * @param int $userId
     * @param int $rating
     * @param string|null $comment
     * @return bool
     * @throws Exception
     */
    public function submitFeedback(int $participantId, int $userId, int $rating, ?string $comment = null): bool
    {
        // Validasi rating 1-5
        if ($rating < 1 || $rating > 5) {
            throw new \Exception('Rating harus antara 1 sampai 5 bintang.');
        }

        // Cek data pendaftaran
        $stmt = $this->db->prepare("
            SELECT p.*, e.id as event_id, e.event_date
            FROM {$this->table} p
            JOIN planting_events e ON p.event_id = e.id
            WHERE p.id = :id AND p.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['id' => $participantId, 'user_id' => $userId]);
        $part = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$part) {
            throw new \Exception('Data pendaftaran relawan tidak ditemukan.');
        }

        if ($part['registration_status'] === 'batal') {
            throw new \Exception('Anda tidak dapat memberikan ulasan untuk kegiatan yang telah dibatalkan.');
        }

        // Cek apakah feedback sudah pernah diberikan
        $stmtCheck = $this->db->prepare("SELECT id FROM event_feedbacks WHERE participant_id = :id LIMIT 1");
        $stmtCheck->execute(['id' => $participantId]);
        if ($stmtCheck->fetch()) {
            throw new \Exception('Anda sudah mengirimkan ulasan untuk kegiatan ini.');
        }

        // Simpan feedback
        $stmtInsert = $this->db->prepare("
            INSERT INTO event_feedbacks (participant_id, event_id, rating, comment, created_at)
            VALUES (:participant_id, :event_id, :rating, :comment, NOW())
        ");
        $stmtInsert->execute([
            'participant_id' => $participantId,
            'event_id'       => $part['event_id'],
            'rating'         => $rating,
            'comment'        => $comment ? trim($comment) : null,
        ]);

        // Tambah poin kontribusi user (+10 poin untuk apresiasi feedback)
        $stmtUser = $this->db->prepare("
            UPDATE users SET contribution_points = contribution_points + 10 WHERE id = :id
        ");
        $stmtUser->execute(['id' => $userId]);

        return true;
    }

    /**
     * Ambil riwayat keikutsertaan event milik pengguna
     *
     * @param int $userId
     * @return array
     */
    public function getUserParticipations(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, e.title as event_title, e.event_date, e.gathering_time, e.gathering_point,
                   l.name as location_name, l.city as location_city,
                   t.ticket_code, t.status as ticket_status,
                   pay.invoice_no, pay.status as payment_status
            FROM {$this->table} p
            JOIN planting_events e ON p.event_id = e.id
            LEFT JOIN planting_locations l ON e.location_id = l.id
            LEFT JOIN tickets t ON t.participant_id = p.id
            LEFT JOIN payments pay ON p.payment_id = pay.id
            WHERE p.user_id = :user_id
            ORDER BY e.event_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

