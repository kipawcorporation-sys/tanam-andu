<?php
/**
 * AdminNewsController — Plant For Fun
 * Kerenceng Coffee Planting & Agroforestry Expedition
 * Manajemen Postingan, Artikel, dan Berita Lapangan Penanaman Pohon.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminNewsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
    }

    /**
     * GET /admin/postingan — Daftar seluruh postingan berita
     */
    public function index(): void
    {
        $db      = Database::getInstance()->getConnection();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $offset  = ($page - 1) * $perPage;

        $statusFilter = $_GET['status'] ?? '';
        $catFilter    = $_GET['category'] ?? '';
        $keyword      = trim($_GET['q'] ?? '');

        $where  = [];
        $params = [];

        if ($statusFilter === 'published') {
            $where[] = 'n.is_published = 1';
        } elseif ($statusFilter === 'draft') {
            $where[] = 'n.is_published = 0';
        }

        if (!empty($catFilter)) {
            $where[] = 'n.category = :cat';
            $params[':cat'] = $catFilter;
        }

        if (!empty($keyword)) {
            $where[] = '(n.title LIKE :kw1 OR n.tags LIKE :kw2 OR n.excerpt LIKE :kw3)';
            $params[':kw1'] = '%' . $keyword . '%';
            $params[':kw2'] = '%' . $keyword . '%';
            $params[':kw3'] = '%' . $keyword . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Hitung total dengan filter
        $totalStmt = $db->prepare("SELECT COUNT(*) FROM news n {$whereClause}");
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        // Ambil data postingan
        $stmt = $db->prepare("
            SELECT n.*, u.name AS author_name,
                   (SELECT COUNT(*) FROM news_comments nc WHERE nc.news_id = n.id) AS total_comments
            FROM news n
            LEFT JOIN users u ON u.id = n.author_id
            {$whereClause}
            ORDER BY n.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ringkasan Statistik
        $stats = [
            'total'     => (int)$db->query("SELECT COUNT(*) FROM news")->fetchColumn(),
            'published' => (int)$db->query("SELECT COUNT(*) FROM news WHERE is_published = 1")->fetchColumn(),
            'draft'     => (int)$db->query("SELECT COUNT(*) FROM news WHERE is_published = 0")->fetchColumn(),
            'views'     => (int)$db->query("SELECT SUM(views_count) FROM news")->fetchColumn(),
            'comments'  => (int)$db->query("SELECT COUNT(*) FROM news_comments")->fetchColumn(),
        ];

        // Daftar kategori untuk filter dropdown
        $categories = $db->query("
            SELECT DISTINCT category FROM news WHERE category IS NOT NULL AND category != '' ORDER BY category ASC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/news', [
            'pageTitle'           => 'Manajemen Postingan & Berita',
            'pendingPaymentsCount'=> $pendingCount,
            'posts'               => $posts,
            'stats'               => $stats,
            'categories'          => $categories,
            'statusFilter'        => $statusFilter,
            'catFilter'           => $catFilter,
            'keyword'             => $keyword,
            'total'               => $total,
            'page'                => $page,
            'perPage'             => $perPage,
            'totalPages'          => (int)ceil($total / $perPage),
        ]);
    }

    /**
     * GET /admin/postingan/tambah — Form buat postingan baru
     */
    public function create(): void
    {
        $db = Database::getInstance()->getConnection();
        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $categories = $db->query("
            SELECT DISTINCT category FROM news WHERE category IS NOT NULL AND category != '' ORDER BY category ASC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $this->view('admin/news_form', [
            'pageTitle'           => 'Tulis Postingan Baru',
            'pendingPaymentsCount'=> $pendingCount,
            'post'                => null,
            'categories'          => $categories,
            'isEdit'              => false,
        ]);
    }

    /**
     * POST /admin/postingan/simpan — Simpan postingan baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan');
            return;
        }

        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? 'Kabar Lapangan');
        $customCat   = trim($_POST['custom_category'] ?? '');
        if (!empty($customCat)) {
            $category = $customCat;
        }

        $excerpt     = trim($_POST['excerpt'] ?? '');
        $tags        = trim($_POST['tags'] ?? '');
        $content     = trim($_POST['content'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
        $authorId    = (int)auth('id');

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Judul dan konten postingan wajib diisi.');
            $this->redirect('/admin/postingan/tambah');
            return;
        }

        // Estimasi waktu baca (rata-rata 200 kata per menit)
        $wordCount = str_word_count(strip_tags($content));
        $readTime  = max(1, (int)ceil($wordCount / 200));

        // Auto excerpt jika kosong
        if (empty($excerpt)) {
            $excerpt = mb_substr(strip_tags($content), 0, 160) . '...';
        }

        $slug = $this->createSlug($title);
        $imagePath = null;

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($_FILES['image']);
        }

        $publishedAt = $isPublished ? date('Y-m-d H:i:s') : null;

        $db = Database::getInstance()->getConnection();

        // Jika ditandai featured, lepas status featured dari postingan lain
        if ($isFeatured) {
            $db->exec("UPDATE news SET is_featured = 0");
        }

        $stmt = $db->prepare("
            INSERT INTO news
                (title, slug, category, excerpt, tags, content, image, author_id, is_published, is_featured, read_time_minutes, published_at, created_at, updated_at)
            VALUES
                (:title, :slug, :category, :excerpt, :tags, :content, :image, :author_id, :is_published, :is_featured, :read_time, :published_at, NOW(), NOW())
        ");
        $stmt->execute([
            ':title'        => $title,
            ':slug'         => $slug,
            ':category'     => $category,
            ':excerpt'      => $excerpt,
            ':tags'         => $tags,
            ':content'      => $content,
            ':image'        => $imagePath,
            ':author_id'    => $authorId ?: null,
            ':is_published' => $isPublished,
            ':is_featured'  => $isFeatured,
            ':read_time'    => $readTime,
            ':published_at' => $publishedAt,
        ]);

        Session::flash('success', 'Postingan artikel baru berhasil disimpan dan dipublikasikan.');
        $this->redirect('/admin/postingan');
    }

    /**
     * GET /admin/postingan/{id}/edit — Form edit postingan
     */
    public function edit(int $id): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM news WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            Session::flash('error', 'Postingan tidak ditemukan.');
            $this->redirect('/admin/postingan');
            return;
        }

        $categories = $db->query("
            SELECT DISTINCT category FROM news WHERE category IS NOT NULL AND category != '' ORDER BY category ASC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/news_form', [
            'pageTitle'           => 'Edit Postingan: ' . $post['title'],
            'pendingPaymentsCount'=> $pendingCount,
            'post'                => $post,
            'categories'          => $categories,
            'isEdit'              => true,
        ]);
    }

    /**
     * POST /admin/postingan/{id}/update — Simpan update postingan
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan');
            return;
        }

        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? 'Kabar Lapangan');
        $customCat   = trim($_POST['custom_category'] ?? '');
        if (!empty($customCat)) {
            $category = $customCat;
        }

        $excerpt     = trim($_POST['excerpt'] ?? '');
        $tags        = trim($_POST['tags'] ?? '');
        $content     = trim($_POST['content'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;

        if (empty($title) || empty($content)) {
            Session::flash('error', 'Judul dan konten postingan wajib diisi.');
            $this->redirect("/admin/postingan/{$id}/edit");
            return;
        }

        $db = Database::getInstance()->getConnection();

        $curr = $db->prepare("SELECT * FROM news WHERE id = :id LIMIT 1");
        $curr->execute([':id' => $id]);
        $currPost = $curr->fetch(PDO::FETCH_ASSOC);

        if (!$currPost) {
            Session::flash('error', 'Postingan tidak ditemukan.');
            $this->redirect('/admin/postingan');
            return;
        }

        // Estimasi waktu baca
        $wordCount = str_word_count(strip_tags($content));
        $readTime  = max(1, (int)ceil($wordCount / 200));

        if (empty($excerpt)) {
            $excerpt = mb_substr(strip_tags($content), 0, 160) . '...';
        }

        $imageSql = "";
        $params = [
            ':title'        => $title,
            ':category'     => $category,
            ':excerpt'      => $excerpt,
            ':tags'         => $tags,
            ':content'      => $content,
            ':is_published' => $isPublished,
            ':is_featured'  => $isFeatured,
            ':read_time'    => $readTime,
            ':id'           => $id,
        ];

        // Jika baru dipublish dari sebelumnya draft
        if ($isPublished && !$currPost['is_published']) {
            $imageSql .= ", published_at = NOW()";
        }

        // Upload gambar baru jika ada
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImage = $this->handleImageUpload($_FILES['image']);
            if ($newImage) {
                $imageSql .= ", image = :image";
                $params[':image'] = $newImage;
            }
        }

        if ($isFeatured && !$currPost['is_featured']) {
            $db->exec("UPDATE news SET is_featured = 0");
        }

        $stmt = $db->prepare("
            UPDATE news
            SET title = :title,
                category = :category,
                excerpt = :excerpt,
                tags = :tags,
                content = :content,
                is_published = :is_published,
                is_featured = :is_featured,
                read_time_minutes = :read_time,
                updated_at = NOW()
                {$imageSql}
            WHERE id = :id
        ");
        $stmt->execute($params);

        Session::flash('success', 'Postingan berhasil diperbarui.');
        $this->redirect('/admin/postingan');
    }

    /**
     * POST /admin/postingan/{id}/hapus — Hapus postingan
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $db->prepare("DELETE FROM news WHERE id = :id")->execute([':id' => $id]);

        Session::flash('success', 'Postingan berhasil dihapus dari sistem.');
        $this->redirect('/admin/postingan');
    }

    /**
     * POST /admin/postingan/{id}/toggle — Cepat ubah status publikasi
     */
    public function togglePublish(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $post = $db->query("SELECT id, is_published FROM news WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            $newPub = $post['is_published'] ? 0 : 1;
            $pubAtSql = $newPub ? ", published_at = NOW()" : "";
            $db->prepare("UPDATE news SET is_published = :pub {$pubAtSql}, updated_at = NOW() WHERE id = :id")
               ->execute([':pub' => $newPub, ':id' => $id]);
            Session::flash('success', $newPub ? 'Postingan berhasil diterbitkan.' : 'Postingan dialihkan ke Draf.');
        }

        $this->redirect('/admin/postingan');
    }

    /**
     * POST /admin/postingan/{id}/toggle-featured — Toggle artikel pilihan/headline
     */
    public function toggleFeatured(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $post = $db->query("SELECT id, is_featured FROM news WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            $newFeat = $post['is_featured'] ? 0 : 1;
            if ($newFeat) {
                $db->exec("UPDATE news SET is_featured = 0");
            }
            $db->prepare("UPDATE news SET is_featured = :feat WHERE id = :id")->execute([':feat' => $newFeat, ':id' => $id]);
            Session::flash('success', $newFeat ? 'Artikel berhasil dijadikan Berita Utama (Headline)!' : 'Status Berita Utama dicabut.');
        }

        $this->redirect('/admin/postingan');
    }

    /**
     * GET /admin/postingan/komentar — Moderasi Komentar Pembaca
     */
    public function comments(): void
    {
        $db = Database::getInstance()->getConnection();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $statusFilter = $_GET['status'] ?? '';
        $where = [];
        $params = [];

        if (!empty($statusFilter)) {
            $where[] = 'nc.status = :status';
            $params[':status'] = $statusFilter;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $totalStmt = $db->prepare("SELECT COUNT(*) FROM news_comments nc {$whereClause}");
        $totalStmt->execute($params);
        $total = (int)$totalStmt->fetchColumn();

        $stmt = $db->prepare("
            SELECT nc.*, n.title AS news_title, n.slug AS news_slug
            FROM news_comments nc
            JOIN news n ON n.id = nc.news_id
            {$whereClause}
            ORDER BY nc.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pendingCount = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/news_comments', [
            'pageTitle'           => 'Moderasi Komentar Berita',
            'pendingPaymentsCount'=> $pendingCount,
            'comments'            => $comments,
            'statusFilter'        => $statusFilter,
            'total'               => $total,
            'page'                => $page,
            'perPage'             => $perPage,
            'totalPages'          => (int)ceil($total / $perPage),
        ]);
    }

    /**
     * POST /admin/postingan/komentar/{id}/status — Setujui / Tolak komentar
     */
    public function updateCommentStatus(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan/komentar');
            return;
        }

        $status = $_POST['status'] ?? 'approved';
        if (!in_array($status, ['approved', 'pending', 'rejected'])) {
            $status = 'approved';
        }

        $db = Database::getInstance()->getConnection();
        $db->prepare("UPDATE news_comments SET status = :status WHERE id = :id")->execute([
            ':status' => $status,
            ':id'     => $id
        ]);

        Session::flash('success', 'Status komentar berhasil diperbarui.');
        $this->redirect('/admin/postingan/komentar');
    }

    /**
     * POST /admin/postingan/komentar/{id}/hapus — Hapus komentar
     */
    public function deleteComment(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/postingan/komentar');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $db->prepare("DELETE FROM news_comments WHERE id = :id")->execute([':id' => $id]);

        Session::flash('success', 'Komentar berhasil dihapus.');
        $this->redirect('/admin/postingan/komentar');
    }

    private function createSlug(string $title): string
    {
        $slug = mb_strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return $slug . '-' . substr(uniqid(), -5);
    }

    private function handleImageUpload(array $file): ?string
    {
        $uploadDir = BASE_PATH . '/storage/uploads/news/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return null;
        }

        $filename = 'news_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return '/storage/uploads/news/' . $filename;
        }

        return null;
    }
}
