<?php
/**
 * Model Certificate — TanamPohon.id
 * 
 * Mengelola sertifikat penghargaan digital (PDF & Web View)
 * untuk donasi pohon dan partisipasi relawan, dilengkapi QR Code verifikasi keaslian.
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/Donation.php';

class Certificate extends Model
{
    protected string $table = 'certificates';
    protected string $primaryKey = 'id';

    /**
     * Terbitkan sertifikat donasi digital
     *
     * @param int $donationId
     * @param int $userId
     * @return array Data sertifikat yang diterbitkan
     */
    public function issueDonationCertificate(int $donationId, int $userId): array
    {
        // Cek apakah sudah pernah diterbitkan
        $existing = $this->findByDonationId($donationId);
        if ($existing) {
            return $existing;
        }

        $donationModel = new Donation();
        $donation = $donationModel->getDonationWithItems($donationId);

        if (!$donation) {
            throw new \Exception('Data donasi tidak ditemukan.');
        }

        $certNumber = 'CERT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Rangkum jenis pohon yang didonasikan
        $speciesList = [];
        $totalCo2 = 0.0;
        foreach ($donation['items'] as $item) {
            $speciesList[] = $item['quantity'] . ' Pohon ' . $item['species_name'];
            $totalCo2 += ($item['quantity'] * (float)$item['co2_absorption_kg_per_year']);
        }

        $recipientName = $donation['donor_name'];
        if (!empty($donation['is_gift']) && !empty($donation['gift_recipient_name'])) {
            $recipientName = $donation['gift_recipient_name'] . ' (Hadiah dari ' . $donation['donor_name'] . ')';
        }

        $dataSnapshot = [
            'certificate_number' => $certNumber,
            'recipient_name'     => $recipientName,
            'total_trees'        => $donation['total_trees'],
            'species_summary'    => implode(', ', $speciesList),
            'total_co2_kg'       => $totalCo2,
            'donation_amount'    => $donation['total_amount'],
            'donation_date'      => $donation['created_at'],
            'signer_name'        => config('app.certificate_signer_name', 'Direktur TanamPohon.id'),
            'signer_title'       => 'Penyelenggara Program Penanaman',
            'verify_url'         => url('/verifikasi-sertifikat/' . $certNumber),
        ];

        $sql = "INSERT INTO {$this->table} (
            user_id, certifiable_type, certifiable_id, certificate_number,
            type, data_snapshot, issued_at, created_at
        ) VALUES (
            :user_id, 'donation', :certifiable_id, :certificate_number,
            'donasi', :data_snapshot, NOW(), NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'            => $userId,
            'certifiable_id'     => $donationId,
            'certificate_number' => $certNumber,
            'data_snapshot'      => json_encode($dataSnapshot, JSON_UNESCAPED_UNICODE),
        ]);

        $certId = (int)$this->db->lastInsertId();

        return array_merge([
            'id'                 => $certId,
            'user_id'            => $userId,
            'certificate_number' => $certNumber,
            'type'               => 'donasi',
        ], $dataSnapshot);
    }

    /**
     * Terbitkan sertifikat partisipasi relawan
     *
     * @param int $participantId
     * @param int $userId
     * @return array Data sertifikat yang diterbitkan
     */
    public function issueParticipantCertificate(int $participantId, int $userId): array
    {
        $existing = $this->findByParticipantId($participantId);
        if ($existing) {
            return $existing;
        }

        $stmtPart = $this->db->prepare("
            SELECT p.*, e.title as event_title, e.event_date, e.gathering_point,
                   l.name as location_name, l.city as location_city,
                   u.name as volunteer_name
            FROM participants p
            JOIN planting_events e ON p.event_id = e.id
            LEFT JOIN planting_locations l ON e.location_id = l.id
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :id AND p.user_id = :user_id
            LIMIT 1
        ");
        $stmtPart->execute(['id' => $participantId, 'user_id' => $userId]);
        $part = $stmtPart->fetch(PDO::FETCH_ASSOC);

        if (!$part) {
            throw new \Exception('Data pendaftaran relawan tidak ditemukan.');
        }

        $certNumber = 'CERT-REL-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $dataSnapshot = [
            'certificate_number' => $certNumber,
            'recipient_name'     => $part['volunteer_name'],
            'event_title'        => $part['event_title'],
            'event_date'         => $part['event_date'],
            'location'           => ($part['location_name'] ?? '') . ', ' . ($part['location_city'] ?? ''),
            'role'               => 'Relawan Penanaman Pohon',
            'signer_name'        => config('app.certificate_signer_name', 'Direktur TanamPohon.id'),
            'signer_title'       => 'Koordinator Program Aksi Relawan',
            'verify_url'         => url('/verifikasi-sertifikat/' . $certNumber),
        ];

        $sql = "INSERT INTO {$this->table} (
            user_id, certifiable_type, certifiable_id, certificate_number,
            type, data_snapshot, issued_at, created_at
        ) VALUES (
            :user_id, 'participant', :certifiable_id, :certificate_number,
            'partisipasi', :data_snapshot, NOW(), NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'            => $userId,
            'certifiable_id'     => $participantId,
            'certificate_number' => $certNumber,
            'data_snapshot'      => json_encode($dataSnapshot, JSON_UNESCAPED_UNICODE),
        ]);

        $certId = (int)$this->db->lastInsertId();

        return array_merge([
            'id'                 => $certId,
            'user_id'            => $userId,
            'certificate_number' => $certNumber,
            'type'               => 'partisipasi',
        ], $dataSnapshot);
    }

    /**
     * Cari sertifikat berdasarkan ID partisipan
     *
     * @param int $participantId
     * @return array|false
     */
    public function findByParticipantId(int $participantId): array|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE certifiable_type = 'participant' AND certifiable_id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $participantId]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cert) {
            $cert['data'] = json_decode($cert['data_snapshot'], true) ?: [];
        }

        return $cert;
    }

    /**
     * Cari sertifikat berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cert) {
            $cert['data'] = json_decode($cert['data_snapshot'], true) ?: [];
        }

        return $cert;
    }

    /**
     * Cari sertifikat berdasarkan nomor unik (untuk verifikasi publik)
     *
     * @param string $certNumber
     * @return array|false
     */
    public function findByNumber(string $certNumber): array|false
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as user_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.certificate_number = :cert_num 
            LIMIT 1
        ");
        $stmt->execute(['cert_num' => trim($certNumber)]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cert) {
            $cert['data'] = json_decode($cert['data_snapshot'], true) ?: [];
        }

        return $cert;
    }

    /**
     * Cari sertifikat berdasarkan ID donasi
     *
     * @param int $donationId
     * @return array|false
     */
    public function findByDonationId(int $donationId): array|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE certifiable_type = 'donation' AND certifiable_id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $donationId]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cert) {
            $cert['data'] = json_decode($cert['data_snapshot'], true) ?: [];
        }

        return $cert;
    }

    /**
     * Ambil semua sertifikat milik pengguna
     *
     * @param int $userId
     * @return array
     */
    public function getUserCertificates(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = :user_id 
            ORDER BY issued_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $certs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($certs as &$c) {
            $c['data'] = json_decode($c['data_snapshot'], true) ?: [];
        }

        return $certs;
    }
}
