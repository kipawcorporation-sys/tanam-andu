<?php
/**
 * Model Tree — TanamPohon.id
 * 
 * Mengelola pohon individu yang didonasikan, kode pohon unik,
 * koordinat geografis di peta Leaflet.js, dan status pemantauan pohon.
 */

require_once __DIR__ . '/../../core/Model.php';

class Tree extends Model
{
    protected string $table = 'trees';
    protected string $primaryKey = 'id';

    /**
     * Ambil semua pohon milik pengguna tertentu
     *
     * @param int $userId
     * @return array
     */
    public function getUserTrees(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, ts.name as species_name, ts.co2_absorption_kg_per_year,
                   l.name as location_name, l.city as location_city, l.province as location_province,
                   d.donor_name, d.is_anonymous, d.gift_recipient_name, d.is_gift,
                   (
                       SELECT photo FROM tree_trackings 
                       WHERE tree_id = t.id AND photo IS NOT NULL 
                       ORDER BY tracked_at DESC LIMIT 1
                   ) as latest_photo,
                   (
                       SELECT notes FROM tree_trackings 
                       WHERE tree_id = t.id 
                       ORDER BY tracked_at DESC LIMIT 1
                   ) as latest_notes
            FROM {$this->table} t
            JOIN donation_items di ON t.donation_item_id = di.id
            JOIN donations d ON di.donation_id = d.id
            JOIN tree_species ts ON t.tree_species_id = ts.id
            LEFT JOIN planting_locations l ON t.planting_location_id = l.id
            WHERE d.user_id = :user_id AND d.status = 'berhasil'
            ORDER BY t.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cari pohon berdasarkan kode unik (misal: TREE-20261105-0001)
     *
     * @param string $code
     * @return array|false
     */
    public function findByCode(string $code): array|false
    {
        $stmt = $this->db->prepare("
            SELECT t.*, ts.name as species_name, ts.description as species_desc, ts.co2_absorption_kg_per_year,
                   l.name as location_name, l.city as location_city, l.province as location_province,
                   d.donor_name, d.is_anonymous, d.gift_recipient_name, d.is_gift, d.created_at as donation_date
            FROM {$this->table} t
            JOIN donation_items di ON t.donation_item_id = di.id
            JOIN donations d ON di.donation_id = d.id
            JOIN tree_species ts ON t.tree_species_id = ts.id
            LEFT JOIN planting_locations l ON t.planting_location_id = l.id
            WHERE t.code = :code 
            LIMIT 1
        ");
        $stmt->execute(['code' => trim($code)]);
        $tree = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tree) {
            return false;
        }

        // Ambil riwayat pemantauan
        $stmtTrack = $this->db->prepare("
            SELECT tt.*, u.name as tracker_name
            FROM tree_trackings tt
            LEFT JOIN users u ON tt.tracked_by = u.id
            WHERE tt.tree_id = :tree_id
            ORDER BY tt.tracked_at DESC
        ");
        $stmtTrack->execute(['tree_id' => $tree['id']]);
        $tree['trackings'] = $stmtTrack->fetchAll(PDO::FETCH_ASSOC);

        return $tree;
    }

    /**
     * Generate pohon individu secara otomatis ketika pembayaran donasi disetujui
     *
     * @param int $donationId
     * @param int|null $defaultLocationId
     * @return int Jumlah pohon yang dibuat
     */
    public function generateTreesForDonation(int $donationId, ?int $defaultLocationId = null): int
    {
        // Jika belum ada lokasi penanaman yang dipilih, gunakan lokasi aktif pertama
        if ($defaultLocationId === null) {
            $stmtLoc = $this->db->query("SELECT id FROM planting_locations WHERE is_active = 1 LIMIT 1");
            $defaultLocationId = (int)$stmtLoc->fetchColumn() ?: 1;
        }

        // Ambil item donasi
        $stmtItems = $this->db->prepare("
            SELECT id, tree_species_id, quantity 
            FROM donation_items 
            WHERE donation_id = :donation_id
        ");
        $stmtItems->execute(['donation_id' => $donationId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $createdCount = 0;
        $dateStr = date('Ymd');

        foreach ($items as $item) {
            for ($i = 0; $i < (int)$item['quantity']; $i++) {
                $code = 'TREE-' . $dateStr . '-' . strtoupper(bin2hex(random_bytes(3)));

                $sql = "INSERT INTO {$this->table} (
                    code, donation_item_id, tree_species_id, planting_location_id,
                    planted_at, status, latitude, longitude, created_at
                ) VALUES (
                    :code, :donation_item_id, :tree_species_id, :planting_location_id,
                    CURDATE(), 'ditanam', -6.3200000 + (RAND() * 0.01), 106.6800000 + (RAND() * 0.01), NOW()
                )";

                $stmtInsert = $this->db->prepare($sql);
                $stmtInsert->execute([
                    'code'                 => $code,
                    'donation_item_id'     => $item['id'],
                    'tree_species_id'      => $item['tree_species_id'],
                    'planting_location_id' => $defaultLocationId,
                ]);

                $treeId = (int)$this->db->lastInsertId();

                // Catat riwayat tracking pertama
                $stmtTrack = $this->db->prepare("
                    INSERT INTO tree_trackings (tree_id, status, notes, tracked_at, created_at)
                    VALUES (:tree_id, 'ditanam', 'Bibit pohon berhasil ditanam di lokasi konservasi.', NOW(), NOW())
                ");
                $stmtTrack->execute(['tree_id' => $treeId]);

                $createdCount++;
            }
        }

        return $createdCount;
    }
}

