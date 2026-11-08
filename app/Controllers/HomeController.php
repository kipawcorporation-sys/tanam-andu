<?php
/**
 * HomeController — TanamPohon.id
 * 
 * Mengelola tampilan halaman publik utama:
 * Landing page, Tentang Kami, Syarat & Ketentuan, Kebijakan Privasi, dan FAQ.
 */

require_once __DIR__ . '/../../core/Controller.php';

class HomeController extends Controller
{
    /**
     * Tampilkan landing page utama
     */
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();

        // 1. Ambil data dampak (counter impact)
        $impactStats = [
            'total_trees'   => 0,
            'total_donors'  => 0,
            'total_events'  => 0,
            'co2_absorbed'  => 0,
        ];

        try {
            // Total pohon dari donasi berhasil
            $stmtTree = $db->query("SELECT COALESCE(SUM(total_trees), 0) FROM donations WHERE status = 'berhasil'");
            $impactStats['total_trees'] = (int)$stmtTree->fetchColumn();

            // Total donatur unik
            $stmtDonors = $db->query("SELECT COUNT(DISTINCT donor_email) FROM donations WHERE status = 'berhasil'");
            $impactStats['total_donors'] = (int)$stmtDonors->fetchColumn();

            // Total kegiatan penanaman
            $stmtEvents = $db->query("SELECT COUNT(*) FROM planting_events WHERE status IN ('upcoming', 'completed')");
            $impactStats['total_events'] = (int)$stmtEvents->fetchColumn();

            // Estimasi serapan CO2 (rata-rata 25kg/pohon/tahun)
            $impactStats['co2_absorbed'] = $impactStats['total_trees'] * 25;
        } catch (\Exception $e) {
            // Abaikan jika database belum terisi
        }

        // 2. Ambil jenis pohon aktif untuk etalase donasi
        $treeSpecies = [];
        try {
            $stmtSpecies = $db->query("SELECT * FROM tree_species WHERE is_active = 1 ORDER BY FIELD(id, 6, 7, 2, 1, 4, 5), price_per_tree ASC LIMIT 4");
            $treeSpecies = $stmtSpecies->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 3. Ambil event penanaman terdekat
        $upcomingEvents = [];
        try {
            $stmtEvents = $db->query("
                SELECT e.*, l.name as location_name, l.city as location_city 
                FROM planting_events e
                LEFT JOIN planting_locations l ON e.location_id = l.id
                WHERE e.status = 'upcoming' AND e.event_date >= CURDATE()
                ORDER BY e.event_date ASC 
                LIMIT 3
            ");
            $upcomingEvents = $stmtEvents->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 4. Ambil produk kopi spesialti kurasi andu
        $featuredCoffee = [];
        try {
            $stmtCoffee = $db->query("
                SELECT p.*, MIN(pv.price) as min_price, COUNT(pv.id) as variant_count,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p 
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_active = 1
                WHERE p.category = 'kopi' AND p.is_active = 1 
                GROUP BY p.id 
                ORDER BY p.sort_order ASC 
                LIMIT 4
            ");
            $featuredCoffee = $stmtCoffee->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 5. Ambil merchandise kaos / bundle
        $featuredMerch = [];
        try {
            $stmtMerch = $db->query("
                SELECT p.*, MIN(pv.price) as min_price 
                FROM products p 
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_active = 1
                WHERE p.category IN ('kaos', 'bundle') AND p.is_active = 1 
                GROUP BY p.id 
                ORDER BY p.sort_order ASC 
                LIMIT 3
            ");
            $featuredMerch = $stmtMerch->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 6. Ambil FAQ populer
        $faqs = [];
        try {
            $stmtFaqs = $db->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 5");
            $faqs = $stmtFaqs->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 7. Ambil 3 kabar & artikel terbaru
        $latestNews = [];
        try {
            $stmtNews = $db->query("
                SELECT n.*, u.name as author_name,
                       (SELECT COUNT(*) FROM news_comments nc WHERE nc.news_id = n.id AND nc.status = 'approved') AS comments_count
                FROM news n
                LEFT JOIN users u ON u.id = n.author_id
                WHERE n.is_published = 1 
                ORDER BY n.is_featured DESC, n.published_at DESC 
                LIMIT 3
            ");
            $latestNews = $stmtNews->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $this->view('public/home', [
            'pageTitle'      => 'Plant For Fun - Kerenceng Coffee Planting & Agroforestry Expedition',
            'impactStats'    => $impactStats,
            'treeSpecies'    => $treeSpecies,
            'upcomingEvents' => $upcomingEvents,
            'featuredCoffee' => $featuredCoffee,
            'featuredMerch'  => $featuredMerch,
            'faqs'           => $faqs,
            'latestNews'     => $latestNews,
        ]);
    }

    /**
     * Halaman statis: Tentang Kami
     */
    public function about(): void
    {
        $this->view('public/about', [
            'pageTitle' => 'Tentang Kami - ' . config('app.name'),
        ]);
    }

    /**
     * Halaman statis: Syarat & Ketentuan
     */
    public function terms(): void
    {
        $this->view('public/terms', [
            'pageTitle' => 'Syarat & Ketentuan - ' . config('app.name'),
        ]);
    }

    /**
     * Halaman statis: Kebijakan Privasi
     */
    public function privacy(): void
    {
        $this->view('public/privacy', [
            'pageTitle' => 'Kebijakan Privasi - ' . config('app.name'),
        ]);
    }

    /**
     * Halaman FAQ lengkap
     */
    public function faq(): void
    {
        $db = Database::getInstance()->getConnection();
        $faqs = [];
        try {
            $stmt = $db->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY category, sort_order ASC");
            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $this->view('public/faq', [
            'pageTitle' => 'Tanya Jawab (FAQ) - ' . config('app.name'),
            'faqs'      => $faqs,
        ]);
    }
}

