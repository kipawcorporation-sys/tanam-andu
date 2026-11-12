<?php
/**
 * Model Product — TanamPohon.id
 * 
 * Mengelola produk merchandise (kaos, bundle), varian ukuran & warna,
 * dan manajemen stok (reserve & release stok).
 */

require_once __DIR__ . '/../../core/Model.php';

class Product extends Model
{
    protected string $table = 'products';
    protected string $primaryKey = 'id';

    /**
     * Ambil semua produk yang aktif
     *
     * @param string|null $category Filter kategori (kaos, bundle)
     * @return array
     */
    public function getActiveProducts(?string $category = null): array
    {
        $sql = "SELECT p.* FROM {$this->table} p WHERE p.is_active = 1";
        $params = [];

        if ($category !== null && $category !== 'all') {
            if ($category === 'kaos' || $category === 'merchandise') {
                $sql .= " AND p.category IN ('kaos', 'merchandise')";
            } else {
                $sql .= " AND p.category = :category";
                $params['category'] = $category;
            }
        }

        $sql .= " ORDER BY p.sort_order ASC, p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil varian dan gambar untuk setiap produk
        foreach ($products as &$product) {
            $product['variants'] = $this->getVariants($product['id']);
            $product['images'] = $this->getImages($product['id']);
            $product['total_stock'] = array_sum(array_map(fn($v) => max(0, $v['stock'] - $v['reserved_stock']), $product['variants']));
        }

        return $products;
    }

    /**
     * Cari produk berdasarkan slug URL
     *
     * @param string $slug
     * @return array|false
     */
    public function findBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = :slug AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return false;
        }

        $product['variants'] = $this->getVariants($product['id']);
        $product['images'] = $this->getImages($product['id']);

        return $product;
    }

    /**
     * Ambil daftar varian ukuran & warna untuk produk tertentu
     *
     * @param int $productId
     * @return array
     */
    public function getVariants(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM product_variants 
            WHERE product_id = :product_id AND is_active = 1 
            ORDER BY FIELD(size, 'S', 'M', 'L', 'XL', 'XXL'), id ASC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cari varian produk berdasarkan ID varian
     *
     * @param int $variantId
     * @return array|false
     */
    public function getVariantById(int $variantId): array|false
    {
        $stmt = $this->db->prepare("
            SELECT pv.*, p.name as product_name, p.slug as product_slug, p.weight_gram, p.category,
                   (SELECT image_path FROM product_images
                    WHERE product_id = p.id AND is_primary = 1
                    ORDER BY sort_order ASC LIMIT 1) as primary_image
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            WHERE pv.id = :id AND pv.is_active = 1 AND p.is_active = 1
            LIMIT 1
        ");
        $stmt->execute(['id' => $variantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil daftar gambar produk
     *
     * @param int $productId
     * @return array
     */
    public function getImages(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM product_images 
            WHERE product_id = :product_id 
            ORDER BY is_primary DESC, sort_order ASC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tahan stok (reserve) saat pengguna checkout
     * Mencegah stok dibeli pengguna lain saat menunggu verifikasi pembayaran
     *
     * @param int $variantId
     * @param int $qty
     * @return bool
     */
    public function reserveStock(int $variantId, int $qty): bool
    {
        // Pastikan stok tersedia sebelum menambah reserved_stock
        $stmt = $this->db->prepare("
            UPDATE product_variants 
            SET reserved_stock = reserved_stock + :qty 
            WHERE id = :id AND (stock - reserved_stock) >= :qty_check
        ");
        return $stmt->execute([
            'qty'       => $qty,
            'id'        => $variantId,
            'qty_check' => $qty,
        ]);
    }

    /**
     * Lepas stok yang tertahan (release) jika pesanan dibatalkan atau kedaluwarsa
     *
     * @param int $variantId
     * @param int $qty
     * @return bool
     */
    public function releaseStock(int $variantId, int $qty): bool
    {
        $stmt = $this->db->prepare("
            UPDATE product_variants 
            SET reserved_stock = GREATEST(0, reserved_stock - :qty) 
            WHERE id = :id
        ");
        return $stmt->execute([
            'qty' => $qty,
            'id'  => $variantId,
        ]);
    }

    /**
     * Potong stok riil secara permanen saat pembayaran disetujui admin
     *
     * @param int $variantId
     * @param int $qty
     * @return bool
     */
    public function commitStock(int $variantId, int $qty): bool
    {
        $stmt = $this->db->prepare("
            UPDATE product_variants 
            SET stock = GREATEST(0, stock - :qty),
                reserved_stock = GREATEST(0, reserved_stock - :qty2)
            WHERE id = :id
        ");
        return $stmt->execute([
            'qty'  => $qty,
            'qty2' => $qty,
            'id'   => $variantId,
        ]);
    }
}

