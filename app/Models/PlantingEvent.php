<?php
/**
 * Model PlantingEvent — TanamPohon.id
 * 
 * Mengelola kegiatan penanaman pohon, jadwal ekspedisi, lokasi,
 * koordinator, itinerary, dan manajemen kuota peserta relawan.
 */

require_once __DIR__ . '/../../core/Model.php';

class PlantingEvent extends Model
{
    protected string $table = 'planting_events';
    protected string $primaryKey = 'id';

    /**
     * Ambil semua event penanaman mendatang
     *
     * @return array
     */
    public function getUpcomingEvents(): array
    {
        $stmt = $this->db->query("
            SELECT e.*, l.name as location_name, l.province, l.city, l.photo as location_photo, l.latitude, l.longitude
            FROM {$this->table} e
            LEFT JOIN planting_locations l ON e.location_id = l.id
            WHERE e.status = 'upcoming' AND e.event_date >= CURDATE()
            ORDER BY e.event_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil event dengan filter lengkap (pencarian, status, kategori, kesulitan, biaya)
     *
     * @param array $filters
     * @return array
     */
    public function getFilteredEvents(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        // Filter status: upcoming, ongoing, completed, all
        $status = $filters['status'] ?? 'upcoming';
        if ($status === 'upcoming') {
            $conditions[] = "(e.status = 'upcoming' AND e.event_date >= CURDATE())";
        } elseif ($status === 'completed') {
            $conditions[] = "(e.status = 'completed' OR e.event_date < CURDATE())";
        } elseif ($status === 'all') {
            // No status filter
        } elseif (!empty($status)) {
            $conditions[] = "e.status = :status";
            $params[':status'] = $status;
        }

        // Pencarian teks
        if (!empty($filters['q'])) {
            $conditions[] = "(e.title LIKE :q OR e.description LIKE :q OR e.gathering_point LIKE :q OR l.name LIKE :q OR l.city LIKE :q)";
            $params[':q'] = '%' . trim($filters['q']) . '%';
        }

        // Filter Kategori
        if (!empty($filters['category'])) {
            $conditions[] = "e.category = :category";
            $params[':category'] = $filters['category'];
        }

        // Filter Kesulitan
        if (!empty($filters['difficulty'])) {
            $conditions[] = "e.difficulty_level = :difficulty";
            $params[':difficulty'] = $filters['difficulty'];
        }

        // Filter Biaya: gratis / berbayar
        if (!empty($filters['fee_type'])) {
            if ($filters['fee_type'] === 'free') {
                $conditions[] = "e.registration_fee = 0";
            } elseif ($filters['fee_type'] === 'paid') {
                $conditions[] = "e.registration_fee > 0";
            }
        }

        // Filter Lokasi
        if (!empty($filters['location_id'])) {
            $conditions[] = "e.location_id = :loc_id";
            $params[':loc_id'] = (int)$filters['location_id'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Sorting
        $sort = $filters['sort'] ?? 'date_asc';
        $orderBy = match ($sort) {
            'date_desc' => 'e.event_date DESC, e.gathering_time DESC',
            'quota_asc' => '(e.quota - e.registered_count) ASC',
            'quota_desc'=> '(e.quota - e.registered_count) DESC',
            'title_asc' => 'e.title ASC',
            default     => 'e.event_date ASC, e.gathering_time ASC',
        };

        $sql = "
            SELECT e.*, COALESCE(e.latitude, l.latitude) as latitude, COALESCE(e.longitude, l.longitude) as longitude, l.name as location_name, l.province, l.city, l.photo as location_photo
            FROM {$this->table} e
            LEFT JOIN planting_locations l ON e.location_id = l.id
            {$whereClause}
            ORDER BY {$orderBy}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil statistik global ekspedisi
     *
     * @return array
     */
    public function getExpeditionStats(): array
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_events,
                SUM(CASE WHEN status = 'upcoming' AND event_date >= CURDATE() THEN 1 ELSE 0 END) as upcoming_events,
                SUM(CASE WHEN status = 'completed' OR event_date < CURDATE() THEN 1 ELSE 0 END) as completed_events,
                COALESCE(SUM(quota), 0) as total_quota,
                COALESCE(SUM(registered_count), 0) as total_registered,
                COUNT(DISTINCT location_id) as total_locations
            FROM {$this->table}
        ");
        $data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Rata-rata elevasi kegiatan
        $elevStmt = $this->db->query("SELECT AVG(elevation_masl) as avg_elev FROM {$this->table} WHERE elevation_masl > 0");
        $avgElev = (int)($elevStmt->fetchColumn() ?: 1200);
        $data['avg_elevation'] = $avgElev;

        return $data;
    }

    /**
     * Ambil daftar kategori ekspedisi yang unik
     *
     * @return array
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query("
            SELECT DISTINCT category 
            FROM {$this->table} 
            WHERE category IS NOT NULL AND category != '' 
            ORDER BY category ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Ambil ekspedisi unggulan terdekat berikutnya
     *
     * @return array|null
     */
    public function getNextFeaturedExpedition(): ?array
    {
        $stmt = $this->db->query("
            SELECT e.*, l.name as location_name, l.province, l.city, l.photo as location_photo, l.latitude, l.longitude
            FROM {$this->table} e
            LEFT JOIN planting_locations l ON e.location_id = l.id
            WHERE e.status = 'upcoming' AND e.event_date >= CURDATE()
            ORDER BY (CASE WHEN e.id = 3 THEN 0 ELSE 1 END), e.event_date ASC
            LIMIT 1
        ");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Cari event berdasarkan ID lengkap dengan relasi lokasi
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT e.*, COALESCE(e.latitude, l.latitude) as latitude, COALESCE(e.longitude, l.longitude) as longitude, l.name as location_name, l.province, l.city, l.photo as location_photo
            FROM {$this->table} e
            LEFT JOIN planting_locations l ON e.location_id = l.id
            WHERE e.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cek ketersediaan kuota event
     *
     * @param int $eventId
     * @return bool
     */
    public function isQuotaAvailable(int $eventId): bool
    {
        $event = $this->findById($eventId);
        if (!$event) {
            return false;
        }
        return ($event['registered_count'] < $event['quota']);
    }

    /**
     * Tambah jumlah peserta terdaftar (kuota berkurang)
     *
     * @param int $eventId
     * @return bool
     */
    public function incrementRegistered(int $eventId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET registered_count = registered_count + 1, updated_at = NOW() 
            WHERE id = :id AND registered_count < quota
        ");
        return $stmt->execute(['id' => $eventId]);
    }

    /**
     * Kurangi jumlah peserta jika dibatalkan
     *
     * @param int $eventId
     * @return bool
     */
    public function decrementRegistered(int $eventId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET registered_count = GREATEST(0, registered_count - 1), updated_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $eventId]);
    }
}
