<?php
/**
 * AdminShippingController — Plant For Fun
 * Pengelolaan Mesin Cek Ongkir Mandiri (Internal Shipping Rate Engine).
 * Admin dapat mengatur tarif dasar per zona, estimasi waktu, dan mengaktifkan/menonaktifkan kurir.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Services/ShippingService.php';

class AdminShippingController extends Controller
{
    private PDO $db;
    private ShippingService $shippingService;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
        $this->db = Database::getInstance()->getConnection();
        $this->shippingService = new ShippingService();
    }

    /**
     * GET /admin/ongkir — Tampilkan daftar zona tarif ongkir & statistik kota
     */
    public function index(): void
    {
        $zones = $this->shippingService->getAllZones();
        $totalCities = (int)$this->db->query("SELECT COUNT(*) FROM shipping_cities")->fetchColumn();
        $pendingCount = (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/shipping', [
            'pageTitle'            => 'Mesin Ongkir Mandiri & Tarif Zona',
            'pendingPaymentsCount' => $pendingCount,
            'zones'                => $zones,
            'totalCities'          => $totalCities,
            'origin'               => 'Cicalengka, Kabupaten Bandung, Jawa Barat (Kebon Kopi Kerenceng)',
        ]);
    }

    /**
     * POST /admin/ongkir/{id}/update — Edit tarif dasar & pengaturan kurir zona
     */
    public function updateZone(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/ongkir');
            return;
        }

        $baseRate = (float)str_replace(['.', ','], ['', '.'], $_POST['base_rate_per_kg'] ?? '10000');
        $etdRegular = trim($_POST['etd_regular'] ?? '2-3 Hari');
        $etdExpress = trim($_POST['etd_express'] ?? '1-2 Hari');
        $jneActive = isset($_POST['jne_active']) ? 1 : 0;
        $jntActive = isset($_POST['jnt_active']) ? 1 : 0;
        $sicepatActive = isset($_POST['sicepat_active']) ? 1 : 0;
        $posActive = isset($_POST['pos_active']) ? 1 : 0;
        $localActive = isset($_POST['local_active']) ? 1 : 0;

        $stmt = $this->db->prepare("
            UPDATE shipping_zones
            SET base_rate_per_kg = :rate,
                etd_regular = :etd_reg,
                etd_express = :etd_exp,
                jne_active = :jne,
                jnt_active = :jnt,
                sicepat_active = :sicepat,
                pos_active = :pos,
                local_active = :local,
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':rate'    => $baseRate,
            ':etd_reg' => $etdRegular,
            ':etd_exp' => $etdExpress,
            ':jne'     => $jneActive,
            ':jnt'     => $jntActive,
            ':sicepat' => $sicepatActive,
            ':pos'     => $posActive,
            ':local'   => $localActive,
            ':id'      => $id,
        ]);

        Session::flash('success', 'Pengaturan tarif zona logistik berhasil diperbarui.');
        $this->redirect('/admin/ongkir');
    }

    /**
     * POST /admin/ongkir/kota/simpan — Tambah kota kustom baru
     */
    public function storeCity(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/ongkir');
            return;
        }

        $province = trim($_POST['province'] ?? '');
        $cityName = trim($_POST['city_name'] ?? '');
        $cityType = trim($_POST['city_type'] ?? 'Kabupaten');
        $zoneId = (int)($_POST['zone_id'] ?? 1);
        $postalCode = trim($_POST['postal_code'] ?? '') ?: null;

        if (empty($province) || empty($cityName)) {
            Session::flash('error', 'Provinsi dan Nama Kota/Kabupaten wajib diisi.');
            $this->redirect('/admin/ongkir');
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO shipping_cities (province, city_name, city_type, zone_id, postal_code, created_at)
            VALUES (:prov, :city, :type, :zone, :postal, NOW())
        ");
        $stmt->execute([
            ':prov'   => $province,
            ':city'   => $cityName,
            ':type'   => $cityType,
            ':zone'   => $zoneId,
            ':postal' => $postalCode,
        ]);

        Session::flash('success', "Kota '{$cityName}' berhasil ditambahkan ke database ongkir.");
        $this->redirect('/admin/ongkir');
    }
}

