<?php
/**
 * AdminProductController — Plant For Fun
 * Pengelolaan penuh katalog produk (Kopi Spesialti, Kaos, Paket Bundle),
 * varian (biji, gilingan, ukuran, kemasan), foto produk, dan stok.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminProductController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /admin/produk — Daftar seluruh produk
     */
    public function index(): void
    {
        $category = $_GET['kategori'] ?? null;
        $sql = "
            SELECT p.*,
                   COALESCE(SUM(pv.stock), 0) AS total_stock,
                   COUNT(pv.id) AS variant_count,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM products p
            LEFT JOIN product_variants pv ON pv.product_id = p.id
        ";

        $params = [];
        if (!empty($category)) {
            if ($category === 'merchandise' || $category === 'kaos') {
                $sql .= " WHERE p.category IN ('kaos', 'merchandise')";
            } else {
                $sql .= " WHERE p.category = :cat";
                $params[':cat'] = $category;
            }
        }

        $sql .= " GROUP BY p.id ORDER BY p.sort_order ASC, p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil rincian varian untuk setiap produk agar bisa langsung diedit di modal
        foreach ($products as &$prod) {
            $stmtV = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = :pid ORDER BY id ASC");
            $stmtV->execute([':pid' => $prod['id']]);
            $prod['variants'] = $stmtV->fetchAll(PDO::FETCH_ASSOC);
        }

        $pendingCount = (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $productStats = [
            'total'       => (int)$this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'kopi'        => (int)$this->db->query("SELECT COUNT(*) FROM products WHERE category = 'kopi'")->fetchColumn(),
            'merchandise' => (int)$this->db->query("SELECT COUNT(*) FROM products WHERE category IN ('kaos', 'merchandise')")->fetchColumn(),
            'bundle'      => (int)$this->db->query("SELECT COUNT(*) FROM products WHERE category = 'bundle'")->fetchColumn(),
            'total_stock' => (int)$this->db->query("SELECT COALESCE(SUM(stock), 0) FROM product_variants")->fetchColumn(),
            'active'      => (int)$this->db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn(),
        ];

        $this->view('admin/products', [
            'pageTitle'            => 'Manajemen Produk & Kopi Spesialti',
            'pendingPaymentsCount' => $pendingCount,
            'products'             => $products,
            'selectedCategory'     => $category,
            'productStats'         => $productStats,
        ]);
    }

    /**
     * GET /admin/produk/export-csv — Unduh data katalog produk ke CSV
     */
    public function exportCsv(): void
    {
        $products = $this->db->query("
            SELECT p.id, p.name, p.slug, p.category, p.base_price, p.weight_gram, p.is_active,
                   COALESCE(SUM(pv.stock), 0) AS total_stock,
                   COUNT(pv.id) AS variant_count
            FROM products p
            LEFT JOIN product_variants pv ON pv.product_id = p.id
            GROUP BY p.id
            ORDER BY p.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="katalog_produk_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Nama Produk', 'Slug', 'Kategori', 'Harga Dasar (Rp)', 'Berat (gram)', 'Total Stok Gudang', 'Jumlah Varian', 'Status'], ',', '"', "\\");

        foreach ($products as $p) {
            fputcsv($out, [
                $p['id'],
                $p['name'],
                $p['slug'],
                $p['category'],
                $p['base_price'],
                $p['weight_gram'],
                $p['total_stock'],
                $p['variant_count'],
                $p['is_active'] ? 'Aktif' : 'Nonaktif'
            ], ',', '"', "\\");
        }
        fclose($out);
        exit;
    }

    /**
     * POST /admin/produk/simpan — Tambah produk baru
     */
    public function store(): void
    {
        if (!$this->isPost()) { 
            $this->redirect('/admin/produk'); 
            return; 
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            Session::flash('error', 'Nama produk wajib diisi.');
            $this->redirect('/admin/produk');
            return;
        }

        $slug = $this->makeSlug($name);
        $desc = trim($_POST['description'] ?? '') ?: null;
        $cat = trim($_POST['category'] ?? 'kopi') ?: 'kopi';
        $price = (float)str_replace(['.', ','], ['', '.'], $_POST['base_price'] ?? '0');
        $weight = (int)($_POST['weight_gram'] ?? 200);
        $active = isset($_POST['is_active']) ? 1 : 0;
        $sort = (int)($_POST['sort_order'] ?? 0);

        $stmt = $this->db->prepare("
            INSERT INTO products (name, slug, description, category, base_price, weight_gram, is_active, sort_order, created_at, updated_at)
            VALUES (:name, :slug, :desc, :cat, :price, :weight, :active, :sort, NOW(), NOW())
        ");
        $stmt->execute([
            ':name'   => $name,
            ':slug'   => $slug,
            ':desc'   => $desc,
            ':cat'    => $cat,
            ':price'  => $price,
            ':weight' => $weight,
            ':active' => $active,
            ':sort'   => $sort,
        ]);

        $productId = (int)$this->db->lastInsertId();

        // Upload gambar jika ada
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($_FILES['image']);
            if ($imagePath) {
                $this->db->prepare("
                    INSERT INTO product_images (product_id, image_path, sort_order, is_primary, created_at)
                    VALUES (:pid, :img, 1, 1, NOW())
                ")->execute([':pid' => $productId, ':img' => $imagePath]);
            }
        }

        // Buat default varian awal jika diisi
        $defaultVariant = trim($_POST['default_variant_name'] ?? '');
        $defaultStock = (int)($_POST['default_stock'] ?? 20);
        if ($defaultStock > 0 || !empty($defaultVariant)) {
            $sku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 4)) . '-' . rand(100, 999);
            $this->db->prepare("
                INSERT INTO product_variants (product_id, size, color, sku, price, stock, is_active, created_at)
                VALUES (:pid, :size, :color, :sku, :price, :stock, 1, NOW())
            ")->execute([
                ':pid'   => $productId,
                ':size'  => $defaultVariant ?: ($cat === 'kopi' ? '200gr' : 'All Size'),
                ':color' => $cat === 'kopi' ? 'Biji Utuh (Whole Bean)' : 'Original',
                ':sku'   => $sku,
                ':price' => $price,
                ':stock' => $defaultStock,
            ]);
        }

        Session::flash('success', "Produk '{$name}' berhasil ditambahkan ke katalog!");
        $this->redirect('/admin/produk');
    }

    /**
     * POST /admin/produk/{id}/update — Edit produk
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) { 
            $this->redirect('/admin/produk'); 
            return; 
        }

        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '') ?: null;
        $cat = trim($_POST['category'] ?? 'kopi') ?: 'kopi';
        $price = (float)str_replace(['.', ','], ['', '.'], $_POST['base_price'] ?? '0');
        $weight = (int)($_POST['weight_gram'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        $sort = (int)($_POST['sort_order'] ?? 0);

        $this->db->prepare("
            UPDATE products SET
                name = :name, description = :desc, category = :cat,
                base_price = :price, weight_gram = :weight,
                is_active = :active, sort_order = :sort, updated_at = NOW()
            WHERE id = :id
        ")->execute([
            ':name'   => $name,
            ':desc'   => $desc,
            ':cat'    => $cat,
            ':price'  => $price,
            ':weight' => $weight,
            ':active' => $active,
            ':sort'   => $sort,
            ':id'     => $id,
        ]);

        // Upload gambar baru jika ada
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($_FILES['image']);
            if ($imagePath) {
                // Hapus primary lama lalu set yang baru
                $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = :pid")->execute([':pid' => $id]);
                $this->db->prepare("
                    INSERT INTO product_images (product_id, image_path, sort_order, is_primary, created_at)
                    VALUES (:pid, :img, 1, 1, NOW())
                ")->execute([':pid' => $id, ':img' => $imagePath]);
            }
        }

        Session::flash('success', "Data produk '{$name}' berhasil diperbarui.");
        $this->redirect('/admin/produk');
    }

    /**
     * POST /admin/produk/{id}/hapus — Hapus produk
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/produk');
            return;
        }

        // Cek apakah ada di riwayat order (via product_variants)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM order_items oi
            JOIN product_variants pv ON pv.id = oi.product_variant_id
            WHERE pv.product_id = :pid
        ");
        $stmt->execute([':pid' => $id]);
        $orderCount = (int)$stmt->fetchColumn();

        if ($orderCount > 0) {
            $this->db->prepare("UPDATE products SET is_active = 0 WHERE id = :id")->execute([':id' => $id]);
            Session::flash('warning', "Produk tidak dihapus permanen karena ada dalam riwayat pesanan pelanggan. Status diubah menjadi NONAKTIF.");
        } else {
            // Hapus relasi child lebih dulu agar tidak FK violation
            $this->db->prepare("DELETE FROM product_images WHERE product_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM product_variants WHERE product_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM products WHERE id = :id")->execute([':id' => $id]);
            Session::flash('success', "Produk berhasil dihapus secara permanen.");
        }

        $this->redirect('/admin/produk');
    }

    /**
     * POST /admin/produk/{id}/varian/simpan — Tambah varian untuk produk
     */
    public function storeVariant(int $productId): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/produk');
            return;
        }

        $size = trim($_POST['size'] ?? '') ?: '200gr';
        $color = trim($_POST['color'] ?? '') ?: 'Biji Utuh';
        $price = (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0');
        $stock = max(0, (int)($_POST['stock'] ?? 0));
        $sku = trim($_POST['sku'] ?? '');

        if (empty($sku)) {
            $sku = 'VAR-' . $productId . '-' . rand(100, 999);
        }

        $this->db->prepare("
            INSERT INTO product_variants (product_id, size, color, sku, price, stock, is_active, created_at)
            VALUES (:pid, :size, :color, :sku, :price, :stock, 1, NOW())
        ")->execute([
            ':pid'   => $productId,
            ':size'  => $size,
            ':color' => $color,
            ':sku'   => $sku,
            ':price' => $price,
            ':stock' => $stock,
        ]);

        Session::flash('success', "Varian '{$size} - {$color}' berhasil ditambahkan!");
        $this->redirect('/admin/produk');
    }

    /**
     * POST /admin/produk/varian/{id}/stok — Update stok & harga varian cepat
     */
    public function updateStock(int $variantId): void
    {
        if (!$this->isPost()) { 
            $this->redirect('/admin/produk'); 
            return; 
        }

        $newStock = max(0, (int)($_POST['stock'] ?? 0));
        $newPrice = isset($_POST['price']) ? (float)str_replace(['.', ','], ['', '.'], $_POST['price']) : null;

        if ($newPrice !== null && $newPrice > 0) {
            $this->db->prepare("UPDATE product_variants SET stock = :stock, price = :price WHERE id = :id")
                     ->execute([':stock' => $newStock, ':price' => $newPrice, ':id' => $variantId]);
        } else {
            $this->db->prepare("UPDATE product_variants SET stock = :stock WHERE id = :id")
                     ->execute([':stock' => $newStock, ':id' => $variantId]);
        }

        Session::flash('success', 'Varian dan stok berhasil diperbarui.');
        $this->redirect('/admin/produk');
    }

    /**
     * POST /admin/produk/varian/{id}/hapus — Hapus varian
     */
    public function deleteVariant(int $variantId): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/produk');
            return;
        }

        $this->db->prepare("DELETE FROM product_variants WHERE id = :id")->execute([':id' => $variantId]);
        Session::flash('success', 'Varian produk berhasil dihapus.');
        $this->redirect('/admin/produk');
    }

    /**
     * Helper upload foto produk
     */
    private function handleImageUpload(array $file): ?string
    {
        $uploadDir = BASE_PATH . '/storage/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return null;
        }

        $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return 'storage/uploads/products/' . $filename;
        }

        return null;
    }

    private function makeSlug(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return $text . '-' . substr(uniqid(), -4);
    }
}
