<?php
/**
 * NewsController — Plant For Fun
 * Kerenceng Coffee Planting & Agroforestry Expedition
 * Halaman publik untuk membaca postingan, berita, dan kabar konservasi lingkungan.
 */

require_once __DIR__ . '/../../core/Controller.php';

class NewsController extends Controller
{
    /**
     * GET /kabar — Daftar artikel berita publik dengan kategori, search, dan featured post
     */
    public function index(): void
    {
        $db       = Database::getInstance()->getConnection();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 9;
        $offset   = ($page - 1) * $perPage;

        $category = trim($_GET['kategori'] ?? '');
        $keyword  = trim($_GET['q'] ?? '');

        // Query builder
        $where = ['n.is_published = 1'];
        $params = [];

        if (!empty($category)) {
            $where[] = 'n.category = :category';
            $params[':category'] = $category;
        }

        if (!empty($keyword)) {
            $where[] = '(n.title LIKE :kw1 OR n.excerpt LIKE :kw2 OR n.tags LIKE :kw3 OR n.content LIKE :kw4)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
            $params[':kw4'] = '%' . $keyword . '%';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        // Hitung total artikel
        $countStmt = $db->prepare("SELECT COUNT(*) FROM news n {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Ambil daftar artikel
        $stmt = $db->prepare("
            SELECT n.*, u.name AS author_name,
                   (SELECT COUNT(*) FROM news_comments nc WHERE nc.news_id = n.id AND nc.status = 'approved') AS comments_count
            FROM news n
            LEFT JOIN users u ON u.id = n.author_id
            {$whereClause}
            ORDER BY n.is_featured DESC, n.published_at DESC, n.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil artikel pilihan (Featured Headline)
        $featuredPost = null;
        if (empty($category) && empty($keyword) && $page === 1) {
            $stmtFeat = $db->query("
                SELECT n.*, u.name AS author_name,
                       (SELECT COUNT(*) FROM news_comments nc WHERE nc.news_id = n.id AND nc.status = 'approved') AS comments_count
                FROM news n
                LEFT JOIN users u ON u.id = n.author_id
                WHERE n.is_published = 1 AND n.is_featured = 1
                ORDER BY n.published_at DESC
                LIMIT 1
            ");
            $featuredPost = $stmtFeat->fetch(PDO::FETCH_ASSOC);

            // Jika tidak ada yang ditandai featured, gunakan artikel terbaru
            if (!$featuredPost && !empty($newsList)) {
                $featuredPost = $newsList[0];
            }
        }

        // Ambil daftar kategori unik beserta jumlah artikelnya
        $catStmt = $db->query("
            SELECT category, COUNT(*) as total_posts
            FROM news
            WHERE is_published = 1 AND category IS NOT NULL AND category != ''
            GROUP BY category
            ORDER BY total_posts DESC
        ");
        $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil 5 artikel terpopuler / trending berdasarkan views
        $trendingStmt = $db->query("
            SELECT id, title, slug, image, category, views_count, read_time_minutes, published_at
            FROM news
            WHERE is_published = 1
            ORDER BY views_count DESC, published_at DESC
            LIMIT 5
        ");
        $trendingPosts = $trendingStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('public/news', [
            'pageTitle'     => 'Kabar Lapangan & Berita Konservasi - ' . config('app.name'),
            'newsList'      => $newsList,
            'featuredPost'  => $featuredPost,
            'categories'    => $categories,
            'trendingPosts' => $trendingPosts,
            'selectedCat'   => $category,
            'searchKeyword' => $keyword,
            'total'         => $total,
            'page'          => $page,
            'perPage'       => $perPage,
            'totalPages'    => (int)ceil($total / $perPage),
        ]);
    }

    /**
     * GET /kabar/{slug} — Baca detail artikel berita
     */
    public function show(string $slug): void
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT n.*, u.name AS author_name, u.email AS author_email
            FROM news n
            LEFT JOIN users u ON u.id = n.author_id
            WHERE n.slug = :slug AND n.is_published = 1
            LIMIT 1
        ");
        $stmt->execute([':slug' => $slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            Session::flash('error', 'Artikel yang Anda cari tidak ditemukan atau belum diterbitkan.');
            $this->redirect('/kabar');
            return;
        }

        // Realtime update views counter (+1)
        $db->prepare("UPDATE news SET views_count = views_count + 1 WHERE id = :id")->execute([':id' => $post['id']]);
        $post['views_count'] = (int)$post['views_count'] + 1;

        // Ambil komentar yang sudah disetujui
        $commStmt = $db->prepare("
            SELECT id, name, comment, created_at
            FROM news_comments
            WHERE news_id = :id AND status = 'approved'
            ORDER BY created_at DESC
        ");
        $commStmt->execute([':id' => $post['id']]);
        $comments = $commStmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil artikel sebelumnya & artikel berikutnya
        $prevStmt = $db->prepare("
            SELECT title, slug FROM news 
            WHERE is_published = 1 AND id < :id 
            ORDER BY id DESC LIMIT 1
        ");
        $prevStmt->execute([':id' => $post['id']]);
        $prevPost = $prevStmt->fetch(PDO::FETCH_ASSOC);

        $nextStmt = $db->prepare("
            SELECT title, slug FROM news 
            WHERE is_published = 1 AND id > :id 
            ORDER BY id ASC LIMIT 1
        ");
        $nextStmt->execute([':id' => $post['id']]);
        $nextPost = $nextStmt->fetch(PDO::FETCH_ASSOC);

        // Ambil 3 artikel terkait (kategori yang sama atau terbaru)
        $stmtRelated = $db->prepare("
            SELECT id, title, slug, image, category, views_count, read_time_minutes, published_at
            FROM news
            WHERE is_published = 1 AND id != :id AND (category = :cat OR 1=1)
            ORDER BY (category = :cat2) DESC, published_at DESC
            LIMIT 3
        ");
        $stmtRelated->execute([
            ':id'   => $post['id'],
            ':cat'  => $post['category'] ?? '',
            ':cat2' => $post['category'] ?? '',
        ]);
        $relatedPosts = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);

        $this->view('public/news_detail', [
            'pageTitle'    => $post['title'] . ' - ' . config('app.name'),
            'post'         => $post,
            'comments'     => $comments,
            'prevPost'     => $prevPost ?: null,
            'nextPost'     => $nextPost ?: null,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * POST /kabar/{slug}/komentar — Kirim komentar pembaca
     */
    public function storeComment(string $slug): void
    {
        if (!$this->isPost()) {
            $this->redirect('/kabar/' . $slug);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM news WHERE slug = :slug AND is_published = 1 LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $postId = (int)$stmt->fetchColumn();

        if (!$postId) {
            $this->redirect('/kabar');
            return;
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $comment = trim($_POST['comment'] ?? '');

        if (empty($name) || empty($email) || empty($comment)) {
            Session::flash('error', 'Nama, email, dan pesan komentar wajib diisi.');
            $this->redirect("/kabar/{$slug}#komentar");
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Format alamat email tidak valid.');
            $this->redirect("/kabar/{$slug}#komentar");
            return;
        }

        // Simpan komentar
        $ins = $db->prepare("
            INSERT INTO news_comments (news_id, name, email, comment, status, created_at, updated_at)
            VALUES (:news_id, :name, :email, :comment, 'approved', NOW(), NOW())
        ");
        $ins->execute([
            ':news_id' => $postId,
            ':name'    => $name,
            ':email'   => $email,
            ':comment' => $comment,
        ]);

        Session::flash('success', 'Terima kasih! Komentar Anda telah berhasil dikirim dan dipublikasikan.');
        $this->redirect("/kabar/{$slug}#komentar");
    }
}
