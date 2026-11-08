<?php
/**
 * AdminLocationController — Plant For Fun
 * Pengelolaan penuh lokasi penanaman pohon & peta konservasi.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminLocationController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /admin/lokasi — Daftar lokasi penanaman
     */
    public function index(): void
    {
        $sql = "
            SELECT pl.*,
                   (SELECT COUNT(*) FROM trees WHERE planting_location_id = pl.id) AS real_trees_planted,
                   (SELECT COUNT(*) FROM planting_events WHERE location_id = pl.id) AS total_events
            FROM planting_locations pl
            ORDER BY pl.id ASC
        ";

        $locations = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar event per lokasi untuk detail modal
        $eventsSql = "
            SELECT id, title, location_id, event_date, status, quota, registered_count
            FROM planting_events
            ORDER BY event_date DESC
        ";
        $rawEvents = $this->db->query($eventsSql)->fetchAll(PDO::FETCH_ASSOC);
        $eventsByLoc = [];
        foreach ($rawEvents as $ev) {
            $eventsByLoc[$ev['location_id']][] = $ev;
        }

        foreach ($locations as &$loc) {
            $loc['events_list'] = $eventsByLoc[$loc['id']] ?? [];
        }
        unset($loc);

        $pendingCount = (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/locations', [
            'pageTitle'            => 'Manajemen Lokasi Penanaman',
            'pendingPaymentsCount' => $pendingCount,
            'locations'            => $locations,
        ]);
    }

    /**
     * GET /admin/lokasi/export-csv — Unduh data lokasi penanaman CSV
     */
    public function exportCsv(): void
    {
        $sql = "
            SELECT pl.*,
                   (SELECT COUNT(*) FROM trees WHERE planting_location_id = pl.id) AS real_trees_planted,
                   (SELECT COUNT(*) FROM planting_events WHERE location_id = pl.id) AS total_events
            FROM planting_locations pl
            ORDER BY pl.id ASC
        ";
        $locations = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="lokasi_penanaman_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM agar rapi di Microsoft Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['ID', 'Nama Lokasi', 'Kota/Kabupaten', 'Provinsi', 'Latitude', 'Longitude', 'Target Pohon', 'Pohon Tertanam', 'Pencapaian (%)', 'Total Kegiatan', 'Status Aktif', 'Deskripsi'], ',', '"', "\\");

        foreach ($locations as $loc) {
            $pct = $loc['total_trees_target'] > 0 ? round(($loc['real_trees_planted'] / $loc['total_trees_target']) * 100, 1) : 0;
            fputcsv($output, [
                $loc['id'],
                $loc['name'],
                $loc['city'],
                $loc['province'],
                $loc['latitude'] ?? '',
                $loc['longitude'] ?? '',
                $loc['total_trees_target'],
                $loc['real_trees_planted'],
                $pct . '%',
                $loc['total_events'],
                $loc['is_active'] ? 'Aktif' : 'Nonaktif',
                $loc['description'] ?? ''
            ], ',', '"', "\\");
        }
        fclose($output);
        exit;
    }

    /**
     * POST /admin/lokasi/{id}/toggle-status — Toggle status aktif/nonaktif
     */
    public function toggleStatus(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/lokasi');
            return;
        }

        $stmt = $this->db->prepare("UPDATE planting_locations SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);

        Session::flash('success', "Status lokasi berhasil diubah.");
        $this->redirect('/admin/lokasi');
    }

    /**
     * POST /admin/lokasi/simpan — Tambah lokasi baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/lokasi');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $lat = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $lng = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $target = max(0, (int)($_POST['total_trees_target'] ?? 1000));
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($province) || empty($city)) {
            Session::flash('error', 'Nama lokasi, provinsi, dan kota/kabupaten wajib diisi.');
            $this->redirect('/admin/lokasi');
            return;
        }

        $photoPath = null;
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->handlePhotoUpload($_FILES['photo']);
        }

        $stmt = $this->db->prepare("
            INSERT INTO planting_locations (name, description, province, city, latitude, longitude, total_trees_target, total_trees_planted, photo, is_active, created_at, updated_at)
            VALUES (:name, :desc, :province, :city, :lat, :lng, :target, 0, :photo, :active, NOW(), NOW())
        ");
        $stmt->execute([
            ':name'     => $name,
            ':desc'     => $desc ?: null,
            ':province' => $province,
            ':city'     => $city,
            ':lat'      => $lat,
            ':lng'      => $lng,
            ':target'   => $target,
            ':photo'    => $photoPath,
            ':active'   => $isActive,
        ]);

        Session::flash('success', "Lokasi '{$name}' berhasil ditambahkan.");
        $this->redirect('/admin/lokasi');
    }

    /**
     * POST /admin/lokasi/{id}/update — Edit lokasi
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/lokasi');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $lat = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $lng = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $target = max(0, (int)($_POST['total_trees_target'] ?? 1000));
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($province) || empty($city)) {
            Session::flash('error', 'Nama lokasi, provinsi, dan kota/kabupaten wajib diisi.');
            $this->redirect('/admin/lokasi');
            return;
        }

        $photoSql = "";
        $params = [
            ':name'     => $name,
            ':desc'     => $desc ?: null,
            ':province' => $province,
            ':city'     => $city,
            ':lat'      => $lat,
            ':lng'      => $lng,
            ':target'   => $target,
            ':active'   => $isActive,
            ':id'       => $id,
        ];

        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $newPhoto = $this->handlePhotoUpload($_FILES['photo']);
            if ($newPhoto) {
                $photoSql = ", photo = :photo";
                $params[':photo'] = $newPhoto;
            }
        }

        $stmt = $this->db->prepare("
            UPDATE planting_locations
            SET name = :name, description = :desc, province = :province, city = :city,
                latitude = :lat, longitude = :lng, total_trees_target = :target,
                is_active = :active {$photoSql}, updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute($params);

        Session::flash('success', "Data lokasi '{$name}' berhasil diperbarui.");
        $this->redirect('/admin/lokasi');
    }

    /**
     * POST /admin/lokasi/{id}/hapus — Hapus lokasi
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/lokasi');
            return;
        }

        $treesCount = (int)$this->db->query("SELECT COUNT(*) FROM trees WHERE planting_location_id = {$id}")->fetchColumn();
        $eventsCount = (int)$this->db->query("SELECT COUNT(*) FROM planting_events WHERE location_id = {$id}")->fetchColumn();

        if ($treesCount > 0 || $eventsCount > 0) {
            $this->db->prepare("UPDATE planting_locations SET is_active = 0 WHERE id = :id")->execute([':id' => $id]);
            Session::flash('warning', "Lokasi tidak dapat dihapus permanen karena masih terkait dengan pohon atau kegiatan. Status diubah menjadi NONAKTIF.");
        } else {
            $this->db->prepare("DELETE FROM planting_locations WHERE id = :id")->execute([':id' => $id]);
            Session::flash('success', "Lokasi berhasil dihapus permanen.");
        }

        $this->redirect('/admin/lokasi');
    }

    private function handlePhotoUpload(array $file): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            return null;
        }

        $uploadDir = BASE_PATH . '/public/uploads/locations';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'loc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
        $target = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return 'uploads/locations/' . $filename;
        }

        return null;
    }
}

