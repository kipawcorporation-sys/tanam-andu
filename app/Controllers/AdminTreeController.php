<?php
/**
 * AdminTreeController — TanamPohon.id
 * Manajemen data pohon yang sudah ditanam & CRUD Jenis/Spesies Pohon.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminTreeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    public function index(): void
    {
        $db      = Database::getInstance()->getConnection();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $total = (int)$db->query("SELECT COUNT(*) FROM trees")->fetchColumn();

        $stmt = $db->prepare("
            SELECT
                tr.id, tr.code, tr.planted_at, tr.latitude, tr.longitude,
                tr.status, tr.created_at, tr.planting_location_id, tr.tree_species_id,
                ts.name AS species_name,
                pl.name AS location_name,
                COALESCE(u.name, d.donor_name, 'Donatur Anonim') AS planter_name,
                (SELECT photo FROM tree_trackings WHERE tree_id = tr.id ORDER BY tracked_at DESC LIMIT 1) AS photo_path
            FROM trees tr
            LEFT JOIN tree_species ts ON ts.id = tr.tree_species_id
            LEFT JOIN planting_locations pl ON pl.id = tr.planting_location_id
            LEFT JOIN donation_items di ON di.id = tr.donation_item_id
            LEFT JOIN donations d ON d.id = di.donation_id
            LEFT JOIN users u ON u.id = d.user_id
            ORDER BY tr.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil semua jenis/spesies pohon untuk tab Kelola Jenis Pohon
        $allSpecies = $db->query("
            SELECT ts.*,
                   COUNT(tr.id) AS planted_count
            FROM tree_species ts
            LEFT JOIN trees tr ON tr.tree_species_id = ts.id
            GROUP BY ts.id
            ORDER BY ts.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Statistik ringkas spesies
        $speciesStats = $db->query("
            SELECT ts.name, COUNT(tr.id) AS count
            FROM tree_species ts
            LEFT JOIN trees tr ON tr.tree_species_id = ts.id
            GROUP BY ts.id ORDER BY count DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Daftar lokasi tanam untuk dropdown form edit pohon
        $locations = $db->query("SELECT id, name, city FROM planting_locations ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $treeStats = [
            'total_trees'     => $total,
            'total_species'   => count($allSpecies),
            'total_locations' => count($locations),
            'total_co2'       => (float)$db->query("
                SELECT COALESCE(SUM(ts.co2_absorption_kg_per_year * 10), 0)
                FROM trees tr
                JOIN tree_species ts ON ts.id = tr.tree_species_id
            ")->fetchColumn(),
        ];

        $this->view('admin/trees', [
            'pageTitle'           => 'Inventaris Pohon & Spesies Konservasi',
            'pendingPaymentsCount'=> $pendingCount,
            'trees'               => $trees,
            'allSpecies'          => $allSpecies,
            'speciesStats'        => $speciesStats,
            'locations'           => $locations,
            'total'               => $total,
            'page'                => $page,
            'perPage'             => $perPage,
            'totalPages'          => (int)ceil($total / $perPage),
            'treeStats'           => $treeStats,
        ]);
    }

    /**
     * GET /admin/pohon/export-csv — Unduh data pohon format CSV
     */
    public function exportCsv(): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT
                tr.id, tr.code, tr.planted_at, tr.latitude, tr.longitude,
                tr.status, tr.created_at,
                ts.name AS species_name,
                pl.name AS location_name,
                COALESCE(u.name, d.donor_name, 'Donatur Anonim') AS planter_name
            FROM trees tr
            LEFT JOIN tree_species ts ON ts.id = tr.tree_species_id
            LEFT JOIN planting_locations pl ON pl.id = tr.planting_location_id
            LEFT JOIN donation_items di ON di.id = tr.donation_item_id
            LEFT JOIN donations d ON d.id = di.donation_id
            LEFT JOIN users u ON u.id = d.user_id
            ORDER BY tr.created_at DESC
        ");
        $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="inventaris_pohon_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Kode Pohon', 'Jenis Spesies', 'Lokasi Penanaman', 'Nama Donatur / Penanam', 'Status Pertumbuhan', 'Latitude', 'Longitude', 'Tanggal Ditanam', 'Tanggal Terdaftar'], ',', '"', "\\");

        foreach ($trees as $t) {
            fputcsv($out, [
                $t['id'],
                $t['code'] ?? '',
                $t['species_name'] ?? '',
                $t['location_name'] ?? '',
                $t['planter_name'] ?? 'Anonim',
                $t['status'] ?? 'pending',
                $t['latitude'] ?? '',
                $t['longitude'] ?? '',
                $t['planted_at'] ?? 'Belum ditanam',
                $t['created_at']
            ], ',', '"', "\\");
        }
        fclose($out);
        exit;
    }

    /**
     * POST /admin/spesies-pohon/simpan — Tambah jenis pohon baru.
     */
    public function storeSpecies(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/pohon?tab=spesies');
            return;
        }

        $name       = trim($_POST['name'] ?? '');
        $desc       = trim($_POST['description'] ?? '');
        $price      = (float)str_replace(['.', ','], ['', '.'], $_POST['price_per_tree'] ?? '50000');
        $co2        = (float)str_replace(['.', ','], ['', '.'], $_POST['co2_absorption_kg_per_year'] ?? '0');
        $o2         = (float)str_replace(['.', ','], ['', '.'], $_POST['oxygen_production_kg_per_year'] ?? '0');
        $isActive   = isset($_POST['is_active']) ? 1 : 0;
        $imagePath  = null;

        if (empty($name)) {
            Session::flash('error', 'Nama jenis pohon wajib diisi.');
            $this->redirect('/admin/pohon?tab=spesies');
            return;
        }

        // Upload foto jika ada
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($_FILES['image']);
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO tree_species
                (name, description, image, co2_absorption_kg_per_year, oxygen_production_kg_per_year, price_per_tree, is_active, created_at)
            VALUES
                (:name, :desc, :image, :co2, :o2, :price, :is_active, NOW())
        ");
        $stmt->execute([
            ':name'      => $name,
            ':desc'      => $desc ?: null,
            ':image'     => $imagePath,
            ':co2'       => $co2,
            ':o2'        => $o2,
            ':price'     => $price,
            ':is_active' => $isActive,
        ]);

        Session::flash('success', "Jenis pohon '{$name}' berhasil ditambahkan.");
        $this->redirect('/admin/pohon?tab=spesies');
    }

    /**
     * POST /admin/spesies-pohon/{id}/update — Edit jenis pohon.
     */
    public function updateSpecies(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/pohon?tab=spesies');
            return;
        }

        $name       = trim($_POST['name'] ?? '');
        $desc       = trim($_POST['description'] ?? '');
        $price      = (float)str_replace(['.', ','], ['', '.'], $_POST['price_per_tree'] ?? '50000');
        $co2        = (float)str_replace(['.', ','], ['', '.'], $_POST['co2_absorption_kg_per_year'] ?? '0');
        $o2         = (float)str_replace(['.', ','], ['', '.'], $_POST['oxygen_production_kg_per_year'] ?? '0');
        $isActive   = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name)) {
            Session::flash('error', 'Nama jenis pohon wajib diisi.');
            $this->redirect('/admin/pohon?tab=spesies');
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Cek gambar baru
        $imageSql = "";
        $params = [
            ':name'      => $name,
            ':desc'      => $desc ?: null,
            ':co2'       => $co2,
            ':o2'        => $o2,
            ':price'     => $price,
            ':is_active' => $isActive,
            ':id'        => $id,
        ];

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImage = $this->handleImageUpload($_FILES['image']);
            if ($newImage) {
                $imageSql = ", image = :image";
                $params[':image'] = $newImage;
            }
        }

        $stmt = $db->prepare("
            UPDATE tree_species
            SET name = :name,
                description = :desc,
                co2_absorption_kg_per_year = :co2,
                oxygen_production_kg_per_year = :o2,
                price_per_tree = :price,
                is_active = :is_active
                {$imageSql}
            WHERE id = :id
        ");
        $stmt->execute($params);

        Session::flash('success', "Jenis pohon '{$name}' berhasil diperbarui.");
        $this->redirect('/admin/pohon?tab=spesies');
    }

    /**
     * POST /admin/spesies-pohon/{id}/hapus — Hapus atau nonaktifkan jenis pohon.
     */
    public function deleteSpecies(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/pohon?tab=spesies');
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Cek apakah jenis pohon sudah ditanam atau tercatat di donasi
        $usedCount = (int)$db->query("SELECT COUNT(*) FROM trees WHERE tree_species_id = {$id}")->fetchColumn();
        $usedInDonation = (int)$db->query("SELECT COUNT(*) FROM donation_items WHERE tree_species_id = {$id}")->fetchColumn();

        if ($usedCount > 0 || $usedInDonation > 0) {
            // Nonaktifkan saja jika sudah dipakai
            $db->prepare("UPDATE tree_species SET is_active = 0 WHERE id = :id")->execute([':id' => $id]);
            Session::flash('warning', "Jenis pohon tidak dapat dihapus permanen karena sudah tercatat dalam riwayat pohon/donasi. Status telah diubah menjadi NONAKTIF.");
        } else {
            $db->prepare("DELETE FROM tree_species WHERE id = :id")->execute([':id' => $id]);
            Session::flash('success', "Jenis pohon berhasil dihapus.");
        }

        $this->redirect('/admin/pohon?tab=spesies');
    }

    /**
     * POST /admin/pohon/{id}/update — Admin leluasa mengubah status, tanggal tanam, lokasi, koordinat pohon.
     */
    public function updateTree(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/pohon');
            return;
        }

        $status     = $_POST['status'] ?? 'pending';
        $plantedAt  = !empty($_POST['planted_at']) ? $_POST['planted_at'] : null;
        $locationId = !empty($_POST['planting_location_id']) ? (int)$_POST['planting_location_id'] : null;
        $speciesId  = !empty($_POST['tree_species_id']) ? (int)$_POST['tree_species_id'] : null;
        $lat        = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $lng        = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            UPDATE trees
            SET status = :status,
                planted_at = :planted_at,
                planting_location_id = :location_id,
                tree_species_id = COALESCE(:species_id, tree_species_id),
                latitude = :lat,
                longitude = :lng
            WHERE id = :id
        ");
        $stmt->execute([
            ':status'      => $status,
            ':planted_at'  => $plantedAt,
            ':location_id' => $locationId,
            ':species_id'  => $speciesId,
            ':lat'         => $lat,
            ':lng'         => $lng,
            ':id'          => $id,
        ]);

        Session::flash('success', "Data pohon #{$id} berhasil diperbarui.");
        $this->redirect('/admin/pohon');
    }

    /**
     * Helper upload gambar jenis pohon
     */
    private function handleImageUpload(array $file): ?string
    {
        $uploadDir = BASE_PATH . '/storage/uploads/species/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return null;
        }

        $filename = 'species_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return '/storage/uploads/species/' . $filename;
        }

        return null;
    }
}
