<?php
/**
 * AdminEventController — TanamPohon.id
 * CRUD & Manajemen Lengkap Jadwal Kegiatan / Ekspedisi Penanaman Pohon.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminEventController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    public function index(): void
    {
        $db   = Database::getInstance()->getConnection();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset  = ($page - 1) * $perPage;

        $statusFilter   = $_GET['status'] ?? '';
        $categoryFilter = $_GET['category'] ?? '';
        $searchQuery    = trim($_GET['q'] ?? '');

        $conditions = [];
        $params     = [];

        if (!empty($statusFilter)) {
            $conditions[] = "e.status = :status";
            $params[':status'] = $statusFilter;
        }

        if (!empty($categoryFilter)) {
            $conditions[] = "e.category = :category";
            $params[':category'] = $categoryFilter;
        }

        if (!empty($searchQuery)) {
            $conditions[] = "(e.title LIKE :q OR e.gathering_point LIKE :q OR pl.name LIKE :q OR pl.city LIKE :q)";
            $params[':q'] = "%{$searchQuery}%";
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Total data
        $totalStmt = $db->prepare("SELECT COUNT(*) FROM planting_events e LEFT JOIN planting_locations pl ON pl.id = e.location_id $where");
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        // Query data
        $stmt = $db->prepare("
            SELECT e.*, COALESCE(e.latitude, pl.latitude) AS latitude, COALESCE(e.longitude, pl.longitude) AS longitude, pl.name AS location_name, pl.city, pl.province
            FROM planting_events e
            LEFT JOIN planting_locations pl ON pl.id = e.location_id
            $where
            ORDER BY e.event_date DESC, e.gathering_time DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // KPI Summary Stats
        $kpi = $db->query("
            SELECT
                COUNT(*) as total_events,
                SUM(CASE WHEN status = 'upcoming' AND event_date >= CURDATE() THEN 1 ELSE 0 END) as upcoming_events,
                SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_events,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_events,
                COALESCE(SUM(quota), 0) as total_quota,
                COALESCE(SUM(registered_count), 0) as total_registered
            FROM planting_events
        ")->fetch(PDO::FETCH_ASSOC);

        $occupancyRate = ($kpi['total_quota'] > 0) 
            ? round(($kpi['total_registered'] / $kpi['total_quota']) * 100, 1) 
            : 0;

        $locations = $db->query("SELECT id, name, city, province, latitude, longitude FROM planting_locations ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $categories = $db->query("SELECT DISTINCT category FROM planting_events WHERE category IS NOT NULL AND category != '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/events', [
            'pageTitle'           => 'Manajemen Ekspedisi & Jadwal Kegiatan',
            'pendingPaymentsCount'=> $pendingCount,
            'events'              => $events,
            'locations'           => $locations,
            'categories'          => $categories,
            'kpi'                 => $kpi,
            'occupancyRate'       => $occupancyRate,
            'statusFilter'        => $statusFilter,
            'categoryFilter'      => $categoryFilter,
            'searchQuery'         => $searchQuery,
            'total'               => $total,
            'page'                => $page,
            'perPage'             => $perPage,
            'offset'              => $offset,
            'totalPages'          => (int)ceil($total / $perPage),
        ]);
    }

    public function store(): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/kegiatan'); return; }
        $db = Database::getInstance()->getConnection();

        $lat = (!empty($_POST['latitude']) && is_numeric($_POST['latitude'])) ? (float)$_POST['latitude'] : null;
        $lng = (!empty($_POST['longitude']) && is_numeric($_POST['longitude'])) ? (float)$_POST['longitude'] : null;
        $gmaps = trim($_POST['google_maps_link'] ?? '');
        if (empty($gmaps) && $lat !== null && $lng !== null) {
            $gmaps = "https://maps.google.com/?q={$lat},{$lng}";
        } elseif (!empty($gmaps) && ($lat === null || $lng === null)) {
            if (preg_match('/[?&]q=([-\d\.]+),([-\d\.]+)/', $gmaps, $m) || preg_match('/@([-\d\.]+),([-\d\.]+)/', $gmaps, $m)) {
                $lat = (float)$m[1];
                $lng = (float)$m[2];
            }
        }

        $db->prepare("
            INSERT INTO planting_events
                (title, description, location_id, category, difficulty_level, elevation_masl,
                 cover_image, event_date, gathering_time, gathering_point, google_maps_link,
                 latitude, longitude, tree_species_info, items_to_bring, itinerary, coordinator_name, coordinator_phone,
                 quota, registration_fee, includes_tshirt, tshirt_included_note, cancel_deadline,
                 status, created_at, updated_at)
            VALUES
                (:title, :desc, :loc, :category, :difficulty, :elevation,
                 :cover_image, :edate, :gtime, :gpoint, :gmaps,
                 :lat, :lng, :tsinfo, :items, :itinerary, :coord_name, :coord_phone,
                 :quota, :fee, :tshirt, :tshirt_note, :cancel,
                 :status, NOW(), NOW())
        ")->execute([
            ':title'       => trim($_POST['title'] ?? ''),
            ':desc'        => trim($_POST['description'] ?? ''),
            ':loc'         => ($_POST['location_id'] ?? null) ?: null,
            ':category'    => trim($_POST['category'] ?? 'Agroforestri Kopi'),
            ':difficulty'  => $_POST['difficulty_level'] ?? 'sedang',
            ':elevation'   => (int)($_POST['elevation_masl'] ?? 1550),
            ':cover_image' => trim($_POST['cover_image'] ?? '') ?: null,
            ':edate'       => $_POST['event_date'] ?? '',
            ':gtime'       => $_POST['gathering_time'] ?? '07:00:00',
            ':gpoint'      => trim($_POST['gathering_point'] ?? ''),
            ':gmaps'       => $gmaps ?: null,
            ':lat'         => $lat,
            ':lng'         => $lng,
            ':tsinfo'      => trim($_POST['tree_species_info'] ?? '') ?: null,
            ':items'       => trim($_POST['items_to_bring'] ?? '') ?: null,
            ':itinerary'   => trim($_POST['itinerary'] ?? '') ?: null,
            ':coord_name'  => trim($_POST['coordinator_name'] ?? '') ?: null,
            ':coord_phone' => trim($_POST['coordinator_phone'] ?? '') ?: null,
            ':quota'       => (int)($_POST['quota'] ?? 0),
            ':fee'         => (float)str_replace(['.', ','], ['', '.'], $_POST['registration_fee'] ?? '0'),
            ':tshirt'      => isset($_POST['includes_tshirt']) ? 1 : 0,
            ':tshirt_note' => trim($_POST['tshirt_included_note'] ?? '') ?: null,
            ':cancel'      => ($_POST['cancel_deadline'] ?? '') ?: null,
            ':status'      => $_POST['status'] ?? 'upcoming',
        ]);

        Session::flash('success', 'Ekspedisi penanaman baru berhasil ditambahkan.');
        $this->redirect('/admin/kegiatan');
    }

    public function update(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/kegiatan'); return; }
        $db = Database::getInstance()->getConnection();

        $lat = (!empty($_POST['latitude']) && is_numeric($_POST['latitude'])) ? (float)$_POST['latitude'] : null;
        $lng = (!empty($_POST['longitude']) && is_numeric($_POST['longitude'])) ? (float)$_POST['longitude'] : null;
        $gmaps = trim($_POST['google_maps_link'] ?? '');
        if (empty($gmaps) && $lat !== null && $lng !== null) {
            $gmaps = "https://maps.google.com/?q={$lat},{$lng}";
        } elseif (!empty($gmaps) && ($lat === null || $lng === null)) {
            if (preg_match('/[?&]q=([-\d\.]+),([-\d\.]+)/', $gmaps, $m) || preg_match('/@([-\d\.]+),([-\d\.]+)/', $gmaps, $m)) {
                $lat = (float)$m[1];
                $lng = (float)$m[2];
            }
        }

        $db->prepare("
            UPDATE planting_events SET
                title = :title, description = :desc, location_id = :loc,
                category = :category, difficulty_level = :difficulty, elevation_masl = :elevation,
                cover_image = :cover_image, event_date = :edate, gathering_time = :gtime,
                gathering_point = :gpoint, google_maps_link = :gmaps,
                latitude = :lat, longitude = :lng, tree_species_info = :tsinfo,
                items_to_bring = :items, itinerary = :itinerary, coordinator_name = :coord_name,
                coordinator_phone = :coord_phone, quota = :quota, registration_fee = :fee,
                includes_tshirt = :tshirt, tshirt_included_note = :tshirt_note, cancel_deadline = :cancel,
                status = :status, updated_at = NOW()
            WHERE id = :id
        ")->execute([
            ':title'       => trim($_POST['title'] ?? ''),
            ':desc'        => trim($_POST['description'] ?? ''),
            ':loc'         => ($_POST['location_id'] ?? null) ?: null,
            ':category'    => trim($_POST['category'] ?? 'Agroforestri Kopi'),
            ':difficulty'  => $_POST['difficulty_level'] ?? 'sedang',
            ':elevation'   => (int)($_POST['elevation_masl'] ?? 1550),
            ':cover_image' => trim($_POST['cover_image'] ?? '') ?: null,
            ':edate'       => $_POST['event_date'] ?? '',
            ':gtime'       => $_POST['gathering_time'] ?? '07:00:00',
            ':gpoint'      => trim($_POST['gathering_point'] ?? ''),
            ':gmaps'       => $gmaps ?: null,
            ':lat'         => $lat,
            ':lng'         => $lng,
            ':tsinfo'      => trim($_POST['tree_species_info'] ?? '') ?: null,
            ':items'       => trim($_POST['items_to_bring'] ?? '') ?: null,
            ':itinerary'   => trim($_POST['itinerary'] ?? '') ?: null,
            ':coord_name'  => trim($_POST['coordinator_name'] ?? '') ?: null,
            ':coord_phone' => trim($_POST['coordinator_phone'] ?? '') ?: null,
            ':quota'       => (int)($_POST['quota'] ?? 0),
            ':fee'         => (float)str_replace(['.', ','], ['', '.'], $_POST['registration_fee'] ?? '0'),
            ':tshirt'      => isset($_POST['includes_tshirt']) ? 1 : 0,
            ':tshirt_note' => trim($_POST['tshirt_included_note'] ?? '') ?: null,
            ':cancel'      => ($_POST['cancel_deadline'] ?? '') ?: null,
            ':status'      => $_POST['status'] ?? 'upcoming',
            ':id'          => $id,
        ]);

        Session::flash('success', 'Data ekspedisi penanaman berhasil diperbarui.');
        $this->redirect('/admin/kegiatan');
    }

    /**
     * POST /admin/kegiatan/{id}/toggle-status
     */
    public function toggleStatus(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/kegiatan'); return; }
        $newStatus = $_POST['status'] ?? '';
        $allowed = ['upcoming', 'ongoing', 'completed', 'cancelled'];

        if (in_array($newStatus, $allowed)) {
            $db = Database::getInstance()->getConnection();
            $db->prepare("UPDATE planting_events SET status = :st, updated_at = NOW() WHERE id = :id")
               ->execute([':st' => $newStatus, ':id' => $id]);
            Session::flash('success', 'Status kegiatan berhasil diubah menjadi: ' . ucfirst($newStatus));
        }
        $this->redirect('/admin/kegiatan');
    }

    /**
     * POST /admin/kegiatan/{id}/hapus
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/kegiatan'); return; }
        $db = Database::getInstance()->getConnection();

        $partCount = (int)$db->prepare("SELECT COUNT(*) FROM participants WHERE event_id = :id AND registration_status != 'batal'")
            ->execute([':id' => $id]) ? (int)$db->query("SELECT COUNT(*) FROM participants WHERE event_id = {$id}")->fetchColumn() : 0;

        if ($partCount > 0) {
            // Jika ada peserta, batalkan event daripada menghapus permanen
            $db->prepare("UPDATE planting_events SET status = 'cancelled', updated_at = NOW() WHERE id = :id")->execute([':id' => $id]);
            Session::flash('warning', "Kegiatan memiliki {$partCount} peserta terdaftar. Status diubah menjadi 'Dibatalkan' untuk menjaga histori audit.");
        } else {
            $db->prepare("DELETE FROM planting_events WHERE id = :id")->execute([':id' => $id]);
            Session::flash('success', 'Kegiatan berhasil dihapus dari sistem.');
        }

        $this->redirect('/admin/kegiatan');
    }

    /**
     * GET /admin/kegiatan/{id}/export-peserta
     * Download daftar peserta dalam format CSV
     */
    public function exportParticipants(int $id): void
    {
        $db = Database::getInstance()->getConnection();
        $eventStmt = $db->prepare("SELECT * FROM planting_events WHERE id = :id");
        $eventStmt->execute([':id' => $id]);
        $event = $eventStmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            Session::flash('error', 'Kegiatan tidak ditemukan.');
            $this->redirect('/admin/kegiatan');
            return;
        }

        $stmt = $db->prepare("
            SELECT 
                pa.id, t.ticket_code, u.name, u.email, u.phone, 
                pa.tshirt_size, pa.registration_status, pa.checked_in_at, 
                pa.notes, pa.created_at
            FROM participants pa
            JOIN users u ON u.id = pa.user_id
            LEFT JOIN tickets t ON t.participant_id = pa.id
            WHERE pa.event_id = :id
            ORDER BY pa.created_at ASC
        ");
        $stmt->execute([':id' => $id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = 'peserta-ekspedisi-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($event['title'])) . '-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM untuk Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['No', 'Kode Tiket', 'Nama Relawan', 'Email', 'No Telepon', 'Ukuran Kaos', 'Status Pendaftaran', 'Waktu Check-in', 'Catatan / Alergi', 'Waktu Mendaftar'], ',', '"', "\\");

        $no = 1;
        foreach ($participants as $p) {
            fputcsv($output, [
                $no++,
                $p['ticket_code'] ?? '-',
                $p['name'],
                $p['email'],
                $p['phone'] ?? '-',
                $p['tshirt_size'] ?? '-',
                ucfirst($p['registration_status']),
                $p['checked_in_at'] ? date('d/m/Y H:i', strtotime($p['checked_in_at'])) : 'Belum Check-in',
                $p['notes'] ?? '-',
                date('d/m/Y H:i', strtotime($p['created_at'])),
            ], ',', '"', "\\");
        }

        fclose($output);
        exit;
    }

    /**
     * GET /admin/kegiatan/{id}/presensi
     * Tampilan printable daftar hadir / presensi lapangan
     */
    public function printAttendance(int $id): void
    {
        $db = Database::getInstance()->getConnection();
        $eventStmt = $db->prepare("
            SELECT e.*, pl.name as location_name, pl.city 
            FROM planting_events e
            LEFT JOIN planting_locations pl ON pl.id = e.location_id
            WHERE e.id = :id
        ");
        $eventStmt->execute([':id' => $id]);
        $event = $eventStmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            Session::flash('error', 'Kegiatan tidak ditemukan.');
            $this->redirect('/admin/kegiatan');
            return;
        }

        $stmt = $db->prepare("
            SELECT 
                pa.id, t.ticket_code, u.name, u.phone, 
                pa.tshirt_size, pa.registration_status, pa.checked_in_at, pa.notes
            FROM participants pa
            JOIN users u ON u.id = pa.user_id
            LEFT JOIN tickets t ON t.participant_id = pa.id
            WHERE pa.event_id = :id AND pa.registration_status != 'batal'
            ORDER BY u.name ASC
        ");
        $stmt->execute([':id' => $id]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Render standalone printable HTML view
        require VIEW_PATH . '/admin/events_attendance_print.php';
        exit;
    }
}
