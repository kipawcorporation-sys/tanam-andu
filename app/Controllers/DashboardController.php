<?php
/**
 * DashboardController — TanamPohon.id
 * 
 * Mengelola area member peserta:
 * Ringkasan statistik pribadi, grafik kontribusi bulanan,
 * kegiatan terdekat dengan hitung mundur, dan pintasan aksi cepat.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Donation.php';
require_once __DIR__ . '/../Models/Participant.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Tree.php';
require_once __DIR__ . '/../Models/Certificate.php';
require_once __DIR__ . '/../Models/Badge.php';
require_once __DIR__ . '/../Models/Notification.php';
require_once __DIR__ . '/../Models/UserAddress.php';
require_once __DIR__ . '/../Models/SupportTicket.php';

class DashboardController extends Controller
{
    private User $userModel;
    private Donation $donationModel;
    private Participant $participantModel;
    private Order $orderModel;
    private Tree $treeModel;
    private Certificate $certModel;
    private Badge $badgeModel;
    private Notification $notificationModel;
    private UserAddress $addressModel;
    private SupportTicket $ticketModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->donationModel = new Donation();
        $this->participantModel = new Participant();
        $this->orderModel = new Order();
        $this->treeModel = new Tree();
        $this->certModel = new Certificate();
        $this->badgeModel        = new Badge();
        $this->notificationModel  = new Notification();
        $this->addressModel       = new UserAddress();
        $this->ticketModel        = new SupportTicket();
        // Gunakan layout dashboard
        $this->layout = 'layouts/dashboard';
    }

    /**
     * Tampilkan halaman ringkasan dashboard peserta
     */
    public function index(): void
    {
        $userId = (int)auth('id');
        $db = Database::getInstance()->getConnection();

        // 1. Ambil statistik lengkap user
        $stats = $this->userModel->getUserStats($userId);

        // 2. Tentukan lencana level kontribusi
        $totalTrees = $stats['total_trees'];
        if ($totalTrees >= 100) {
            $levelName = 'Legenda Hijau 🌟';
            $levelClass = 'bg-danger text-white';
        } elseif ($totalTrees >= 50) {
            $levelName = 'Pahlawan Lingkungan 🏆';
            $levelClass = 'bg-warning text-dark';
        } elseif ($totalTrees >= 25) {
            $levelName = 'Penjaga Hutan 🌲';
            $levelClass = 'bg-success text-white';
        } elseif ($totalTrees >= 10) {
            $levelName = 'Sahabat Bumi 🌿';
            $levelClass = 'bg-info text-dark';
        } elseif ($totalTrees >= 5) {
            $levelName = 'Pejuang Hijau 🌱';
            $levelClass = 'bg-success-subtle text-success border';
        } elseif ($totalTrees >= 1) {
            $levelName = 'Penanam Pemula 🍃';
            $levelClass = 'bg-light text-dark border';
        } else {
            $levelName = 'Calon Penanam 🌍';
            $levelClass = 'bg-light text-muted border';
        }

        // 3. Estimasi serapan CO2 tahunan (rata-rata 25kg/pohon/tahun)
        $co2AbsorptionKg = $totalTrees * 25.0;

        // 4. Data grafik kontribusi pohon 6 bulan terakhir (untuk Chart.js)
        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthTime = strtotime("-{$i} months");
            $monthYear = date('Y-m', $monthTime);
            $monthLabel = date('M Y', $monthTime);

            $stmtMonth = $db->prepare("
                SELECT COALESCE(SUM(total_trees), 0) as trees_count
                FROM donations
                WHERE user_id = :user_id 
                  AND status = 'berhasil'
                  AND DATE_FORMAT(created_at, '%Y-%m') = :month_year
            ");
            $stmtMonth->execute([
                'user_id'    => $userId,
                'month_year' => $monthYear,
            ]);
            $count = (int)$stmtMonth->fetchColumn();

            $chartLabels[] = $monthLabel;
            $chartData[] = $count;
        }

        // 5. Kegiatan terdekat yang didaftarkan peserta
        $stmtEvent = $db->prepare("
            SELECT p.*, e.title as event_title, e.event_date, e.gathering_time, e.gathering_point,
                   l.name as location_name, l.city as location_city, t.ticket_code
            FROM participants p
            JOIN planting_events e ON p.event_id = e.id
            LEFT JOIN planting_locations l ON e.location_id = l.id
            LEFT JOIN tickets t ON t.participant_id = p.id
            WHERE p.user_id = :user_id 
              AND p.registration_status IN ('terkonfirmasi', 'menunggu')
              AND e.event_date >= CURDATE()
            ORDER BY e.event_date ASC
            LIMIT 1
        ");
        $stmtEvent->execute(['user_id' => $userId]);
        $nextEvent = $stmtEvent->fetch(PDO::FETCH_ASSOC);

        // 6. Riwayat donasi terbaru (maks 5)
        $recentDonations = $this->donationModel->getUserDonations($userId);
        $recentDonations = array_slice($recentDonations, 0, 5);

        // 7. Notifikasi terbaru
        $stmtNotif = $db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT 3
        ");
        $stmtNotif->execute(['user_id' => $userId]);
        $recentNotifications = $stmtNotif->fetchAll(PDO::FETCH_ASSOC);

        $this->view('dashboard/index', [
            'pageTitle'           => 'Dashboard Saya - ' . config('app.name'),
            'user'                => auth(),
            'stats'               => $stats,
            'levelName'           => $levelName,
            'levelClass'          => $levelClass,
            'co2AbsorptionKg'     => $co2AbsorptionKg,
            'chartLabels'         => $chartLabels,
            'chartData'           => $chartData,
            'nextEvent'           => $nextEvent,
            'recentDonations'     => $recentDonations,
            'recentNotifications' => $recentNotifications,
        ]);
    }

    /**
     * Tampilkan riwayat Donasi Saya (Modul 7)
     */
    public function donations(): void
    {
        $userId = (int)auth('id');
        $allDonations = $this->donationModel->getUserDonations($userId);

        // Filter status jika ada
        $statusFilter = $this->request->input('status', 'all');
        $donations = [];

        foreach ($allDonations as $d) {
            // Ambil sertifikat jika donasi berhasil
            $cert = null;
            if ($d['payment_status'] === 'berhasil') {
                $cert = $this->certModel->findByDonationId($d['id']);
            }
            $d['certificate'] = $cert;

            if ($statusFilter === 'all') {
                $donations[] = $d;
            } elseif ($statusFilter === 'berhasil' && $d['payment_status'] === 'berhasil') {
                $donations[] = $d;
            } elseif ($statusFilter === 'pending' && in_array($d['payment_status'], ['menunggu_pembayaran', 'menunggu_verifikasi'])) {
                $donations[] = $d;
            } elseif ($statusFilter === 'ditolak' && $d['payment_status'] === 'ditolak') {
                $donations[] = $d;
            }
        }

        $this->view('dashboard/donations', [
            'pageTitle'    => 'Donasi Saya - ' . config('app.name'),
            'donations'    => $donations,
            'statusFilter' => $statusFilter,
            'totalCount'   => count($allDonations),
        ]);
    }

    /**
     * Tampilkan Pohon Saya & Peta Koordinat Penanaman (Modul 7)
     */
    public function trees(): void
    {
        $userId = (int)auth('id');
        $trees = $this->treeModel->getUserTrees($userId);

        $totalCo2 = 0.0;
        $speciesCount = [];
        $mapMarkers = [];

        foreach ($trees as $tree) {
            $totalCo2 += (float)$tree['co2_absorption_kg_per_year'];
            $sp = $tree['species_name'];
            $speciesCount[$sp] = ($speciesCount[$sp] ?? 0) + 1;

            if (!empty($tree['latitude']) && !empty($tree['longitude'])) {
                $mapMarkers[] = [
                    'code'     => $tree['code'],
                    'species'  => $tree['species_name'],
                    'status'   => ucfirst($tree['status']),
                    'lat'      => (float)$tree['latitude'],
                    'lng'      => (float)$tree['longitude'],
                    'location' => $tree['location_name'] ?? 'Kawasan Hutan Konservasi',
                    'planted'  => format_date($tree['planted_at'], 'd M Y'),
                ];
            }
        }

        $this->view('dashboard/trees', [
            'pageTitle'    => 'Pohon Saya - ' . config('app.name'),
            'trees'        => $trees,
            'totalTrees'   => count($trees),
            'totalCo2'     => $totalCo2,
            'speciesCount' => $speciesCount,
            'mapMarkers'   => $mapMarkers,
        ]);
    }

    /**
     * Tampilkan riwayat Kegiatan Saya (Modul 8)
     */
    public function events(): void
    {
        $userId = (int)auth('id');
        $filter = $this->request->input('filter', 'all');
        if (!in_array($filter, ['all', 'upcoming', 'completed', 'cancelled'])) {
            $filter = 'all';
        }

        $participations = $this->participantModel->getUserParticipationsWithDetails($userId, $filter);
        $allParticipations = $this->participantModel->getUserParticipationsWithDetails($userId, 'all');

        $counts = [
            'all'       => count($allParticipations),
            'upcoming'  => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        $today = date('Y-m-d');
        foreach ($participations as &$p) {
            // Auto issue sertifikat jika hadir dan belum memiliki sertifikat
            if ($p['registration_status'] === 'hadir' && empty($p['certificate_id'])) {
                try {
                    $cert = $this->certModel->issueParticipantCertificate((int)$p['id'], $userId);
                    $p['certificate_id'] = $cert['id'];
                    $p['certificate_number'] = $cert['certificate_number'];
                } catch (\Exception $e) {}
            }
        }
        unset($p);

        foreach ($allParticipations as $p) {
            if ($p['registration_status'] === 'batal') {
                $counts['cancelled']++;
            } elseif ($p['registration_status'] === 'hadir' || $p['event_date'] < $today) {
                $counts['completed']++;
            } else {
                $counts['upcoming']++;
            }
        }

        $this->view('dashboard/events', [
            'pageTitle'      => 'Kegiatan Relawan Saya - ' . config('app.name'),
            'participations' => $participations,
            'filter'         => $filter,
            'counts'         => $counts,
        ]);
    }

    /**
     * Batalkan pendaftaran kegiatan relawan (Modul 8)
     *
     * @param int $id ID partisipan
     */
    public function cancelEventRegistration(int $id): void
    {
        $userId = (int)auth('id');
        $reason = trim($this->request->input('reason', 'Dibatalkan oleh peserta'));

        try {
            $this->participantModel->cancelRegistration($id, $userId, $reason);
            Session::flash('success', 'Pendaftaran kegiatan berhasil dibatalkan. Kuota telah dikembalikan untuk peserta lain.');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/dashboard/kegiatan');
    }

    /**
     * Kirim ulasan dan rating pasca kegiatan relawan (Modul 8)
     *
     * @param int $id ID partisipan
     */
    public function submitEventFeedback(int $id): void
    {
        $userId = (int)auth('id');
        $rating = (int)$this->request->input('rating', 5);
        $comment = $this->request->input('comment');

        try {
            $this->participantModel->submitFeedback($id, $userId, $rating, $comment);
            Session::flash('success', 'Terima kasih! Ulasan Anda telah berhasil disimpan dan Anda mendapatkan +10 Poin Kontribusi.');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/dashboard/kegiatan');
    }

    /**
     * Tampilkan riwayat Pesanan Kaos Saya (Modul 8)
     */
    public function orders(): void
    {
        $userId = (int)auth('id');
        $statusFilter = $this->request->input('status', 'all');
        if (!in_array($statusFilter, ['all', 'unpaid', 'processing', 'shipped', 'completed', 'cancelled'])) {
            $statusFilter = 'all';
        }

        $orders = $this->orderModel->getUserOrdersWithDetails($userId, $statusFilter);
        $allOrders = $this->orderModel->getUserOrdersWithDetails($userId, 'all');

        $counts = [
            'all'        => count($allOrders),
            'unpaid'     => 0,
            'processing' => 0,
            'shipped'    => 0,
            'completed'  => 0,
            'cancelled'  => 0,
        ];

        foreach ($allOrders as $o) {
            $st = $o['status'];
            if ($st === 'menunggu_pembayaran' || $o['payment_status'] === 'menunggu_pembayaran') {
                $counts['unpaid']++;
            } elseif ($st === 'diproses') {
                $counts['processing']++;
            } elseif ($st === 'dikirim') {
                $counts['shipped']++;
            } elseif ($st === 'selesai') {
                $counts['completed']++;
            } elseif ($st === 'dibatalkan') {
                $counts['cancelled']++;
            }
        }

        $this->view('dashboard/orders', [
            'pageTitle'    => 'Pesanan Kaos Saya - ' . config('app.name'),
            'orders'       => $orders,
            'statusFilter' => $statusFilter,
            'counts'       => $counts,
        ]);
    }

    /**
     * Konfirmasi pesanan kaos telah diterima (Modul 8)
     *
     * @param int $id ID pesanan
     */
    public function confirmOrderReceived(int $id): void
    {
        $userId = (int)auth('id');

        try {
            $this->orderModel->confirmReceived($id, $userId);
            Session::flash('success', 'Terima kasih atas konfirmasi Anda! Pesanan telah selesai dan Anda mendapatkan +20 Poin Kontribusi.');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/dashboard/pesanan');
    }

    /**
     * Batalkan pesanan kaos (Modul 8)
     *
     * @param int $id ID pesanan
     */
    public function cancelOrder(int $id): void
    {
        $userId = (int)auth('id');
        $reason = trim($this->request->input('reason', 'Dibatalkan oleh pembeli'));

        try {
            $this->orderModel->cancelOrder($id, $userId, $reason);
            Session::flash('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/dashboard/pesanan');
    }

    /**
     * Tampilkan Halaman Sertifikat & Pencapaian Saya (Modul 9 & 7)
     */
    public function certificates(): void
    {
        $userId = (int)auth('id');

        // 1. Ambil data pohon user & sinkronisasi lencana otomatis
        $userStats = $this->userModel->getUserStats($userId);
        $totalTrees = (int)($userStats['total_trees'] ?? 0);
        $this->badgeModel->syncUserBadges($userId, $totalTrees);

        // 2. Ambil seluruh lencana dan lencana yang telah didapat
        $allBadges = $this->badgeModel->getAllBadges();
        $userBadges = $this->badgeModel->getUserBadges($userId);
        $earnedBadgeIds = array_column($userBadges, 'id');

        // Map tanggal perolehan lencana
        $badgeEarnedDates = [];
        foreach ($userBadges as $ub) {
            $badgeEarnedDates[$ub['id']] = $ub['earned_at'];
        }

        // 3. Progres ke lencana berikutnya
        $nextBadgeProgress = $this->badgeModel->getNextBadge($totalTrees);

        // 4. Kalkulasi dampak lingkungan
        $impact = $this->badgeModel->getUserEnvironmentalImpact($userId);

        // 5. Posisi leaderboard
        $leaderboard = $this->badgeModel->getUserLeaderboardRank($userId);

        // 6. Daftar sertifikat resmi (donasi & partisipasi)
        $certificates = $this->certModel->getUserCertificates($userId);

        $this->view('dashboard/certificates', [
            'pageTitle'          => 'Sertifikat & Pencapaian - ' . config('app.name'),
            'certificates'       => $certificates,
            'allBadges'          => $allBadges,
            'earnedBadgeIds'     => $earnedBadgeIds,
            'badgeEarnedDates'   => $badgeEarnedDates,
            'nextBadgeProgress'  => $nextBadgeProgress,
            'impact'             => $impact,
            'leaderboard'        => $leaderboard,
            'totalTrees'         => $totalTrees,
        ]);
    }

    /**
     * Alias untuk halaman pencapaian
     */
    public function achievements(): void
    {
        $this->certificates();
    }

    /**
     * Unduh / Cetak Sertifikat Digital (PDF / Printable View)
     *
     * @param int $id ID sertifikat
     */
    public function downloadCertificate(int $id): void
    {
        $cert = $this->certModel->findById($id);

        if (!$cert) {
            Session::flash('error', 'Sertifikat tidak ditemukan.');
            $this->redirect('/dashboard/donasi');
            return;
        }

        // Keamanan Anti-IDOR: hanya pemilik atau admin
        if (!is_admin() && (int)auth('id') !== (int)$cert['user_id']) {
            Session::flash('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat sertifikat ini.');
            $this->redirect('/dashboard');
            return;
        }

        // Render view sertifikat mandiri tanpa layout dashboard (clean printable)
        $this->layout = '';
        $this->view('dashboard/certificate', [
            'pageTitle' => 'Sertifikat ' . e($cert['certificate_number']),
            'cert'      => $cert,
        ], null);
    }

    /**
     * Tampilkan Halaman Ajak Teman / Referral (Modul 9)
     */
    public function referral(): void
    {
        $userId = (int)auth('id');
        $user = $this->userModel->findWithRole($userId) ?: (auth() ?: []);
        $referralStats = $this->userModel->getReferralStats($userId);

        // Bangun tautan referral unik
        $referralUrl = url('/daftar?ref=' . urlencode($user['referral_code'] ?? ''));

        // Pesan ajakan siap bagikan ke sosial media
        $shareText = "Yuk ikut bersama saya menanam pohon dan menjaga kelestarian bumi Indonesia di Plant For Fun! Daftar lewat tautan ini dan dapatkan bonus poin: " . $referralUrl;

        $this->view('dashboard/referral', [
            'pageTitle'     => 'Ajak Teman (Program Referral) - ' . config('app.name'),
            'user'          => $user,
            'referralStats' => $referralStats,
            'referralUrl'   => $referralUrl,
            'shareText'     => $shareText,
        ]);
    }

    /**
     * Tampilkan Halaman Notifikasi (Modul 9)
     */
    public function notifications(): void
    {
        $userId = (int)auth('id');
        $filter = $this->request->input('filter', 'all');
        if (!in_array($filter, ['all', 'unread'])) {
            $filter = 'all';
        }

        $notifications = $this->notificationModel->getUserNotifications(
            $userId, 
            $filter === 'unread' ? 'unread' : null
        );
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        $allCount = count($this->notificationModel->getUserNotifications($userId, null, 100));

        $this->view('dashboard/notifications', [
            'pageTitle'     => 'Notifikasi Saya - ' . config('app.name'),
            'notifications' => $notifications,
            'filter'        => $filter,
            'unreadCount'   => $unreadCount,
            'allCount'      => $allCount,
        ]);
    }

    /**
     * Tandai sebuah notifikasi telah dibaca (Modul 9)
     *
     * @param int $id
     */
    public function markNotificationAsRead(int $id): void
    {
        $userId = (int)auth('id');
        $this->notificationModel->markAsRead($id, $userId);

        // Jika request AJAX, kembalikan JSON
        if ($this->request->isAjax()) {
            $this->json(['success' => true]);
            return;
        }

        Session::flash('success', 'Notifikasi telah ditandai sebagai dibaca.');
        $this->redirect('/dashboard/notifikasi');
    }

    /**
     * Tandai semua notifikasi telah dibaca (Modul 9)
     */
    public function markAllNotificationsAsRead(): void
    {
        $userId = (int)auth('id');
        $this->notificationModel->markAllAsRead($userId);

        if ($this->request->isAjax()) {
            $this->json(['success' => true]);
            return;
        }

        Session::flash('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
        $this->redirect('/dashboard/notifikasi');
    }

    /**
     * Hapus sebuah notifikasi (Modul 9)
     *
     * @param int $id
     */
    public function deleteNotification(int $id): void
    {
        $userId = (int)auth('id');
        $this->notificationModel->deleteNotification($id, $userId);

        if ($this->request->isAjax()) {
            $this->json(['success' => true]);
            return;
        }

        Session::flash('success', 'Notifikasi berhasil dihapus.');
        $this->redirect('/dashboard/notifikasi');
    }

    // =========================================================================
    // MODUL 10 — PROFIL & BANTUAN
    // =========================================================================

    /**
     * Halaman profil: Info Diri, Keamanan, Alamat Pengiriman.
     */
    public function profile(): void
    {
        $userId   = (int)auth('id');
        $user     = $this->userModel->findWithRole($userId);
        $addresses = $this->addressModel->getUserAddresses($userId);

        // Stats ringkas untuk profile sidebar
        $stats = $this->userModel->getUserStats($userId);

        $this->view('dashboard/profile', [
            'pageTitle' => 'Pengaturan Profil - ' . config('app.name'),
            'user'      => $user,
            'addresses' => $addresses,
            'stats'     => $stats,
        ]);
    }

    /**
     * POST /profil/update — Update info diri (name, phone, city, dsb.)
     */
    public function updateProfile(): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil');
            return;
        }

        $name              = trim($_POST['name'] ?? '');
        $phone             = trim($_POST['phone'] ?? '');
        $city              = trim($_POST['city'] ?? '');
        $defaultTshirt     = $_POST['default_tshirt_size'] ?? null;
        $isAnonymousDefault = isset($_POST['is_anonymous_default']) ? 1 : 0;

        // Validasi
        if (empty($name) || mb_strlen($name) < 2) {
            Session::flash('error', 'Nama minimal 2 karakter.');
            $this->redirect('/dashboard/profil');
            return;
        }

        $allowedSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', null, ''];
        if (!in_array($defaultTshirt, $allowedSizes, true)) {
            $defaultTshirt = null;
        }

        $updated = $this->userModel->updateProfile($userId, [
            'name'                => $name,
            'phone'               => $phone,
            'city'                => $city,
            'default_tshirt_size' => $defaultTshirt ?: null,
            'is_anonymous_default'=> $isAnonymousDefault,
        ]);

        if ($updated) {
            // Refresh data user di session
            $freshUser = $this->userModel->findWithRole($userId);
            Session::setUser($freshUser);
            Session::flash('success', 'Profil berhasil diperbarui.');
        } else {
            Session::flash('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }

        $this->redirect('/dashboard/profil');
    }

    /**
     * POST /profil/password — Ganti password.
     */
    public function updatePassword(): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil');
            return;
        }

        $oldPassword  = $_POST['old_password'] ?? '';
        $newPassword  = $_POST['new_password'] ?? '';
        $confirmPass  = $_POST['confirm_password'] ?? '';

        // Ambil hash saat ini
        $user = $this->userModel->findWithRole($userId);

        if (!password_verify($oldPassword, $user['password'])) {
            Session::flash('password_error', 'Password lama tidak sesuai.');
            $this->redirect('/dashboard/profil#tab-keamanan');
            return;
        }

        if (mb_strlen($newPassword) < 8) {
            Session::flash('password_error', 'Password baru minimal 8 karakter.');
            $this->redirect('/dashboard/profil#tab-keamanan');
            return;
        }

        if ($newPassword !== $confirmPass) {
            Session::flash('password_error', 'Konfirmasi password tidak cocok.');
            $this->redirect('/dashboard/profil#tab-keamanan');
            return;
        }

        $this->userModel->updatePassword($userId, password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]));
        Session::flash('success', 'Password berhasil diubah. Silakan masuk kembali.');
        Session::logout();
        $this->redirect('/masuk');
    }

    /**
     * POST /profil/avatar — Upload foto profil.
     */
    public function uploadAvatar(): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil');
            return;
        }

        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Gagal mengunggah file. Pastikan file dipilih.');
            $this->redirect('/dashboard/profil');
            return;
        }

        $file = $_FILES['avatar'];

        // Validasi ukuran (maks 2 MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            Session::flash('error', 'Ukuran file terlalu besar. Maksimal 2 MB.');
            $this->redirect('/dashboard/profil');
            return;
        }

        // Validasi MIME type (strict finfo check)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowed, true)) {
            Session::flash('error', 'Format file tidak valid. Gunakan JPG, PNG, atau WebP.');
            $this->redirect('/dashboard/profil');
            return;
        }

        // Tentukan ekstensi berdasarkan MIME
        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $ext    = $extMap[$mimeType];

        // Buat direktori upload jika belum ada
        $uploadDir = BASE_PATH . '/storage/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Hapus avatar lama jika ada
        $currentUser = $this->userModel->findWithRole($userId);
        if (!empty($currentUser['avatar'])) {
            $oldFile = $uploadDir . basename($currentUser['avatar']);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Simpan file baru dengan nama unik
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Session::flash('error', 'Gagal menyimpan file. Silakan coba lagi.');
            $this->redirect('/dashboard/profil');
            return;
        }

        // Update kolom avatar di database (simpan path relatif)
        $avatarPath = '/storage/uploads/avatars/' . $filename;
        $this->userModel->updateAvatar($userId, $avatarPath);

        // Refresh session user
        $freshUser = $this->userModel->findWithRole($userId);
        Session::setUser($freshUser);

        Session::flash('success', 'Foto profil berhasil diperbarui.');
        $this->redirect('/dashboard/profil');
    }

    // -------------------------------------------------------------------------
    // ALAMAT PENGIRIMAN
    // -------------------------------------------------------------------------

    /**
     * GET /profil/alamat — Daftar alamat pengiriman.
     */
    public function addresses(): void
    {
        $userId    = (int)auth('id');
        $addresses = $this->addressModel->getUserAddresses($userId);

        $this->view('dashboard/addresses', [
            'pageTitle' => 'Alamat Pengiriman - ' . config('app.name'),
            'addresses' => $addresses,
        ]);
    }

    /**
     * POST /profil/alamat/simpan — Tambah alamat baru.
     */
    public function storeAddress(): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil/alamat');
            return;
        }

        $data = [
            'user_id'        => $userId,
            'label'          => trim($_POST['label'] ?? 'Rumah'),
            'recipient_name' => trim($_POST['recipient_name'] ?? ''),
            'phone'          => trim($_POST['phone'] ?? ''),
            'province'       => trim($_POST['province'] ?? ''),
            'city'           => trim($_POST['city'] ?? ''),
            'district'       => trim($_POST['district'] ?? ''),
            'postal_code'    => trim($_POST['postal_code'] ?? ''),
            'full_address'   => trim($_POST['full_address'] ?? ''),
        ];

        // Validasi field wajib
        $required = ['label', 'recipient_name', 'phone', 'province', 'city', 'district', 'postal_code', 'full_address'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                Session::flash('error', 'Semua field wajib diisi.');
                $this->redirect('/dashboard/profil#tab-alamat');
                return;
            }
        }

        // Batas maksimum 5 alamat per user
        if ($this->addressModel->countByUser($userId) >= 5) {
            Session::flash('error', 'Maksimal 5 alamat per akun. Hapus alamat lama terlebih dahulu.');
            $this->redirect('/dashboard/profil#tab-alamat');
            return;
        }

        $this->addressModel->create($data);
        Session::flash('success', 'Alamat berhasil ditambahkan.');
        $this->redirect('/dashboard/profil#tab-alamat');
    }

    /**
     * POST /profil/alamat/{id}/update — Edit alamat.
     */
    public function updateAddress(int $id): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil#tab-alamat');
            return;
        }

        // Anti-IDOR: pastikan milik user
        $address = $this->addressModel->findById($id, $userId);
        if (!$address) {
            Session::flash('error', 'Alamat tidak ditemukan.');
            $this->redirect('/dashboard/profil#tab-alamat');
            return;
        }

        $data = [
            'label'          => trim($_POST['label'] ?? 'Rumah'),
            'recipient_name' => trim($_POST['recipient_name'] ?? ''),
            'phone'          => trim($_POST['phone'] ?? ''),
            'province'       => trim($_POST['province'] ?? ''),
            'city'           => trim($_POST['city'] ?? ''),
            'district'       => trim($_POST['district'] ?? ''),
            'postal_code'    => trim($_POST['postal_code'] ?? ''),
            'full_address'   => trim($_POST['full_address'] ?? ''),
        ];

        $required = ['label', 'recipient_name', 'phone', 'province', 'city', 'district', 'postal_code', 'full_address'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                Session::flash('error', 'Semua field wajib diisi.');
                $this->redirect('/dashboard/profil#tab-alamat');
                return;
            }
        }

        $this->addressModel->updateAddress($id, $userId, $data);
        Session::flash('success', 'Alamat berhasil diperbarui.');
        $this->redirect('/dashboard/profil#tab-alamat');
    }

    /**
     * POST /profil/alamat/{id}/hapus — Hapus alamat.
     */
    public function deleteAddress(int $id): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil#tab-alamat');
            return;
        }

        $result = $this->addressModel->deleteAddress($id, $userId);

        if ($result['success']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }

        $this->redirect('/dashboard/profil#tab-alamat');
    }

    /**
     * POST /profil/alamat/{id}/utama — Set alamat sebagai utama.
     */
    public function setPrimaryAddress(int $id): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/profil#tab-alamat');
            return;
        }

        $ok = $this->addressModel->setPrimary($id, $userId);

        if ($ok) {
            Session::flash('success', 'Alamat utama berhasil diperbarui.');
        } else {
            Session::flash('error', 'Alamat tidak ditemukan.');
        }

        $this->redirect('/dashboard/profil#tab-alamat');
    }

    // -------------------------------------------------------------------------
    // PUSAT BANTUAN (SUPPORT TICKET)
    // -------------------------------------------------------------------------

    /**
     * GET /bantuan — Daftar tiket bantuan user.
     */
    public function support(): void
    {
        $userId  = (int)auth('id');
        $tickets = $this->ticketModel->getUserTickets($userId);

        // Hitung per status untuk badge ringkasan
        $countOpen    = $this->ticketModel->countByStatus($userId, 'open');
        $countReplied = $this->ticketModel->countByStatus($userId, 'replied');
        $countClosed  = $this->ticketModel->countByStatus($userId, 'closed');

        $this->view('dashboard/support', [
            'pageTitle'    => 'Pusat Bantuan - ' . config('app.name'),
            'tickets'      => $tickets,
            'countOpen'    => $countOpen,
            'countReplied' => $countReplied,
            'countClosed'  => $countClosed,
        ]);
    }

    /**
     * GET /bantuan/{id} — Detail tiket + thread percakapan.
     */
    public function showTicket(int $id): void
    {
        $userId = (int)auth('id');
        $data   = $this->ticketModel->getTicketWithReplies($id, $userId);

        if (!$data) {
            Session::flash('error', 'Tiket tidak ditemukan.');
            $this->redirect('/dashboard/bantuan');
            return;
        }

        $this->view('dashboard/ticket_detail', [
            'pageTitle' => 'Detail Tiket #' . $id . ' - ' . config('app.name'),
            'ticket'    => $data['ticket'],
            'replies'   => $data['replies'],
        ]);
    }

    /**
     * POST /bantuan/kirim — Buat tiket baru.
     */
    public function sendTicket(): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/bantuan');
            return;
        }

        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($subject) || mb_strlen($subject) < 5) {
            Session::flash('error', 'Subjek tiket minimal 5 karakter.');
            $this->redirect('/dashboard/bantuan');
            return;
        }

        if (empty($message) || mb_strlen($message) < 20) {
            Session::flash('error', 'Pesan minimal 20 karakter agar dapat kami tangani dengan baik.');
            $this->redirect('/dashboard/bantuan');
            return;
        }

        // Batas: maks 5 tiket berstatus 'open' sekaligus
        $openCount = $this->ticketModel->countByStatus($userId, 'open');
        if ($openCount >= 5) {
            Session::flash('error', 'Anda memiliki terlalu banyak tiket terbuka. Tunggu hingga tiket sebelumnya diselesaikan.');
            $this->redirect('/dashboard/bantuan');
            return;
        }

        $ticketId = $this->ticketModel->createTicket($userId, $subject, $message);
        Session::flash('success', 'Tiket bantuan berhasil dikirim. Kami akan merespons dalam 1×24 jam.');
        $this->redirect('/dashboard/bantuan/' . $ticketId);
    }

    /**
     * POST /bantuan/{id}/balas — Tambah balasan ke tiket.
     */
    public function replyTicket(int $id): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/bantuan/' . $id);
            return;
        }

        // Pastikan tiket milik user & masih bisa dibalas
        $data = $this->ticketModel->getTicketWithReplies($id, $userId);
        if (!$data) {
            Session::flash('error', 'Tiket tidak ditemukan.');
            $this->redirect('/dashboard/bantuan');
            return;
        }

        if ($data['ticket']['status'] === 'closed') {
            Session::flash('error', 'Tiket sudah ditutup dan tidak dapat dibalas.');
            $this->redirect('/dashboard/bantuan/' . $id);
            return;
        }

        $message = trim($_POST['message'] ?? '');
        if (empty($message) || mb_strlen($message) < 5) {
            Session::flash('error', 'Pesan balasan minimal 5 karakter.');
            $this->redirect('/dashboard/bantuan/' . $id);
            return;
        }

        $this->ticketModel->addReply($id, $userId, $message, false);
        Session::flash('success', 'Balasan berhasil dikirim.');
        $this->redirect('/dashboard/bantuan/' . $id);
    }

    /**
     * POST /bantuan/{id}/tutup — Tutup tiket (oleh user pemilik).
     */
    public function closeTicket(int $id): void
    {
        $userId = (int)auth('id');

        if (!$this->isPost()) {
            $this->redirect('/dashboard/bantuan/' . $id);
            return;
        }

        $ok = $this->ticketModel->closeTicket($id, $userId);

        if ($ok) {
            Session::flash('success', 'Tiket berhasil ditutup. Terima kasih telah menghubungi kami!');
        } else {
            Session::flash('error', 'Tiket tidak ditemukan atau tidak dapat ditutup.');
        }

        $this->redirect('/dashboard/bantuan');
    }
}

