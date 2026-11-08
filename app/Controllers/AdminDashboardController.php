<?php
/**
 * AdminDashboardController — TanamPohon.id
 *
 * Halaman ringkasan admin: statistik global, grafik pendapatan,
 * daftar pembayaran pending terbaru, dan aktivitas terkini.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    /**
     * GET /admin — Halaman ringkasan admin.
     */
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // ---- Stat cards ----
        $totalDonations = $db->query("SELECT COUNT(*) FROM donations WHERE status='berhasil'")->fetchColumn();
        $totalRevenue   = $db->query("SELECT COALESCE(SUM(grand_total),0) FROM payments WHERE status='berhasil'")->fetchColumn();
        $totalTrees     = $db->query("SELECT COALESCE(SUM(total_trees),0) FROM donations WHERE status='berhasil'")->fetchColumn();
        $totalUsers     = $db->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
        $totalEvents    = $db->query("SELECT COUNT(*) FROM planting_events")->fetchColumn();
        $totalOrders    = $db->query("SELECT COUNT(*) FROM orders WHERE status NOT IN ('dibatalkan')")->fetchColumn();

        // Pending payments (menunggu_verifikasi)
        $pendingPayments = $db->query("
            SELECT COUNT(*) FROM payments WHERE status = 'menunggu_verifikasi'
        ")->fetchColumn();

        // ---- Grafik pendapatan 6 bulan terakhir ----
        $revenueChart = $db->query("
            SELECT
                DATE_FORMAT(paid_at, '%Y-%m') AS month,
                DATE_FORMAT(paid_at, '%b %Y') AS label,
                SUM(grand_total) AS total
            FROM payments
            WHERE status = 'berhasil'
              AND paid_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // ---- Pembayaran pending terbaru ----
        $recentPending = $db->query("
            SELECT p.id, p.invoice_no, p.payable_type, p.grand_total,
                   p.created_at,
                   COALESCE(u.name, p.guest_email) AS payer,
                   pp.sender_name, pp.amount_transferred, pp.transfer_date,
                   pp.proof_file
            FROM payments p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN payment_proofs pp ON pp.payment_id = p.id AND pp.status = 'menunggu'
            WHERE p.status = 'menunggu_verifikasi'
            ORDER BY p.updated_at DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        // ---- Aktivitas terbaru (semua tipe) ----
        $recentActivity = $db->query("
            (SELECT 'donasi' AS type, d.id, d.donor_name AS label,
                    d.total_amount AS amount, d.status, d.created_at
             FROM donations d ORDER BY d.created_at DESC LIMIT 5)
            UNION ALL
            (SELECT 'pesanan' AS type, o.id, o.order_number AS label,
                    o.total_amount AS amount, o.status, o.created_at
             FROM orders o ORDER BY o.created_at DESC LIMIT 5)
            ORDER BY created_at DESC LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/dashboard', [
            'pageTitle'        => 'Dashboard Admin',
            'pendingPaymentsCount' => (int)$pendingPayments,
            'totalDonations'   => (int)$totalDonations,
            'totalRevenue'     => (float)$totalRevenue,
            'totalTrees'       => (int)$totalTrees,
            'totalUsers'       => (int)$totalUsers,
            'totalEvents'      => (int)$totalEvents,
            'totalOrders'      => (int)$totalOrders,
            'pendingPayments'  => (int)$pendingPayments,
            'revenueChart'     => $revenueChart,
            'recentPending'    => $recentPending,
            'recentActivity'   => $recentActivity,
        ]);
    }
}

