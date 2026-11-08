<?php
/**
 * Model Role — TanamPohon.id
 * Mengelola data peran pengguna (roles)
 */

require_once __DIR__ . '/../../core/Model.php';

class Role extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'roles';

    /** @var string Primary key */
    protected string $primaryKey = 'id';

    /**
     * Cari role berdasarkan slug
     *
     * @param string $slug Slug role (misal: 'peserta', 'super_admin')
     * @return array|false Data role atau false jika tidak ditemukan
     */
    public function findBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua role yang tersedia
     *
     * @return array Daftar semua role
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua role admin
     *
     * @return array Daftar role admin
     */
    public function getAdminRoles(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} WHERE slug IN ('super_admin', 'admin_keuangan', 'admin_event', 'admin_konten') ORDER BY id ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cari role berdasarkan ID
     *
     * @param int $id ID role
     * @return array|false Data role
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
