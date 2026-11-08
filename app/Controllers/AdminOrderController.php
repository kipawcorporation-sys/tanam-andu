<?php
/**
 * AdminOrderController — TanamPohon.id
 * Manajemen pesanan kaos: daftar, detail, update status pengiriman, lacak resi.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminOrderController extends Controller
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
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $statusFilter = $_GET['status'] ?? '';
        $where  = $statusFilter ? "WHERE o.status = :status" : '';
        $params = $statusFilter ? [':status' => $statusFilter] : [];

        $totalStmt = $db->prepare("SELECT COUNT(*) FROM orders o $where");
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        $stmt = $db->prepare("
            SELECT
                o.id, o.order_number, o.status, o.subtotal, o.shipping_cost, o.total_amount,
                o.shipping_address_snapshot, o.shipping_method, o.pickup_at_event, o.notes,
                o.created_at, o.updated_at,
                u.name AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone,
                p.status AS payment_status, p.invoice_no
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            LEFT JOIN payments p ON p.id = o.payment_id
            $where
            ORDER BY o.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil item produk untuk masing-masing pesanan
        $orderIds = array_column($orders, 'id');
        $itemsByOrder = [];
        if (!empty($orderIds)) {
            $inClause = implode(',', array_map('intval', $orderIds));
            $items = $db->query("SELECT * FROM order_items WHERE order_id IN ($inClause)")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($items as $item) {
                $itemsByOrder[$item['order_id']][] = $item;
            }
        }
        foreach ($orders as &$ord) {
            $ord['items'] = $itemsByOrder[$ord['id']] ?? [];
        }
        unset($ord);

        // Statistik pesanan untuk KPI
        $orderStats = [
            'total'               => (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'menunggu_pembayaran' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='menunggu_pembayaran'")->fetchColumn(),
            'diproses'            => (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='diproses'")->fetchColumn(),
            'dikirim'             => (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='dikirim'")->fetchColumn(),
            'selesai'             => (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='selesai'")->fetchColumn(),
            'dibatalkan'          => (int)$db->query("SELECT COUNT(*) FROM orders WHERE status='dibatalkan'")->fetchColumn(),
            'total_omzet'         => (float)$db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status IN ('diproses','dikirim','selesai')")->fetchColumn(),
        ];

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/orders', [
            'pageTitle'           => 'Manajemen Pesanan Toko & Merchandise',
            'pendingPaymentsCount'=> $pendingCount,
            'orders'              => $orders,
            'statusFilter'        => $statusFilter,
            'total'               => $total,
            'page'                => $page,
            'perPage'             => $perPage,
            'totalPages'          => (int)ceil($total / $perPage),
            'orderStats'          => $orderStats,
        ]);
    }

    /**
     * GET /admin/pesanan/export-csv — Unduh data pesanan ke CSV
     */
    public function exportCsv(): void
    {
        $db = Database::getInstance()->getConnection();
        $orders = $db->query("
            SELECT
                o.id, o.order_number, o.status, o.subtotal, o.shipping_cost, o.total_amount,
                o.shipping_method, o.pickup_at_event, o.created_at, o.notes,
                u.name AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone,
                p.status AS payment_status, p.invoice_no
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            LEFT JOIN payments p ON p.id = o.payment_id
            ORDER BY o.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="daftar_pesanan_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'No Pesanan', 'Invoice', 'Nama Pembeli', 'Email', 'No Telepon', 'Metode Pengiriman', 'Subtotal', 'Ongkir', 'Total Tagihan', 'Status Pembayaran', 'Status Pesanan', 'Tanggal Pesan', 'Catatan / Resi'], ',', '"', "\\");

        foreach ($orders as $o) {
            fputcsv($out, [
                $o['id'],
                $o['order_number'],
                $o['invoice_no'] ?? '',
                $o['buyer_name'],
                $o['buyer_email'],
                $o['buyer_phone'] ?? '',
                $o['pickup_at_event'] ? 'Ambil di Lokasi Event' : ($o['shipping_method'] ?? 'Reguler'),
                $o['subtotal'],
                $o['shipping_cost'],
                $o['total_amount'],
                $o['payment_status'] ?? '',
                $o['status'],
                $o['created_at'],
                $o['notes'] ?? ''
            ], ',', '"', "\\");
        }
        fclose($out);
        exit;
    }

    /**
     * POST /admin/pesanan/{id}/kirim — Update status ke "dikirim" + simpan resi.
     */
    public function ship(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/pesanan'); return; }

        $db        = Database::getInstance()->getConnection();
        $trackCode = trim($_POST['tracking_code'] ?? '');
        $courier   = trim($_POST['courier'] ?? '');

        // Simpan info pengiriman ke kolom notes (JSON sederhana)
        $shippingInfo = json_encode([
            'tracking_code' => $trackCode,
            'courier'       => $courier,
            'shipped_at'    => date('Y-m-d H:i:s'),
        ]);

        $db->prepare("
            UPDATE orders
            SET status = 'dikirim', notes = :notes, updated_at = NOW()
            WHERE id = :id AND status = 'diproses'
        ")->execute([':notes' => $shippingInfo, ':id' => $id]);

        Session::flash('success', 'Pesanan berhasil diupdate ke status Dikirim.');
        $this->redirect('/admin/pesanan');
    }

    /**
     * POST /admin/pesanan/{id}/selesai — Tandai pesanan selesai (admin).
     */
    public function complete(int $id): void
    {
        if (!$this->isPost()) { $this->redirect('/admin/pesanan'); return; }

        $db = Database::getInstance()->getConnection();
        $db->prepare("UPDATE orders SET status = 'selesai', updated_at = NOW() WHERE id = :id")
           ->execute([':id' => $id]);

        Session::flash('success', 'Pesanan ditandai selesai.');
        $this->redirect('/admin/pesanan');
    }
}

