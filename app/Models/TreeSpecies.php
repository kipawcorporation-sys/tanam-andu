<?php
/**
 * Model TreeSpecies — TanamPohon.id
 * 
 * Mengelola katalog jenis pohon untuk donasi dan penanaman.
 */

require_once __DIR__ . '/../../core/Model.php';

class TreeSpecies extends Model
{
    protected string $table = 'tree_species';
    protected string $primaryKey = 'id';

    /**
     * Ambil semua jenis pohon yang aktif
     *
     * @return array
     */
    public function getActiveSpecies(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE is_active = 1 
            ORDER BY FIELD(id, 6, 7, 2, 1, 4, 5), price_per_tree ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cari jenis pohon berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

