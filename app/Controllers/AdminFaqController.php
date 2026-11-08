<?php
/**
 * AdminFaqController — Plant For Fun
 * Pengelolaan penuh Tanya Jawab (FAQ) platform.
 */

require_once __DIR__ . '/../../core/Controller.php';

class AdminFaqController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'layouts/admin';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /admin/faq — Tampilkan daftar FAQ
     */
    public function index(): void
    {
        $category = $_GET['kategori'] ?? null;
        $search = trim($_GET['q'] ?? '');

        $sql = "SELECT * FROM faqs WHERE 1=1";
        $params = [];

        if (!empty($category)) {
            $sql .= " AND category = :cat";
            $params[':cat'] = $category;
        }

        if (!empty($search)) {
            $sql .= " AND (question LIKE :q OR answer LIKE :q)";
            $params[':q'] = "%{$search}%";
        }

        $sql .= " ORDER BY sort_order ASC, id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil kategori unik untuk filter tabs
        $categories = $this->db->query("SELECT DISTINCT category FROM faqs ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

        $pendingCount = (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='menunggu_verifikasi'")->fetchColumn();

        $this->view('admin/faqs', [
            'pageTitle'            => 'Manajemen Tanya Jawab (FAQ)',
            'pendingPaymentsCount' => $pendingCount,
            'faqs'                 => $faqs,
            'categories'           => $categories,
            'selectedCategory'     => $category,
            'searchQuery'          => $search,
        ]);
    }

    /**
     * POST /admin/faq/simpan — Tambah FAQ baru
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/faq');
            return;
        }

        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $category = trim($_POST['category'] ?? 'umum') ?: 'umum';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($question) || empty($answer)) {
            Session::flash('error', 'Pertanyaan dan jawaban wajib diisi.');
            $this->redirect('/admin/faq');
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO faqs (question, answer, category, sort_order, is_active, created_at, updated_at)
            VALUES (:q, :a, :cat, :sort, :active, NOW(), NOW())
        ");
        $stmt->execute([
            ':q'      => $question,
            ':a'      => $answer,
            ':cat'    => $category,
            ':sort'   => $sortOrder,
            ':active' => $isActive,
        ]);

        Session::flash('success', 'FAQ baru berhasil ditambahkan.');
        $this->redirect('/admin/faq');
    }

    /**
     * POST /admin/faq/{id}/update — Edit FAQ
     */
    public function update(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/faq');
            return;
        }

        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $category = trim($_POST['category'] ?? 'umum') ?: 'umum';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($question) || empty($answer)) {
            Session::flash('error', 'Pertanyaan dan jawaban wajib diisi.');
            $this->redirect('/admin/faq');
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE faqs
            SET question = :q, answer = :a, category = :cat, sort_order = :sort, is_active = :active, updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':q'      => $question,
            ':a'      => $answer,
            ':cat'    => $category,
            ':sort'   => $sortOrder,
            ':active' => $isActive,
            ':id'     => $id,
        ]);

        Session::flash('success', 'FAQ berhasil diperbarui.');
        $this->redirect('/admin/faq');
    }

    /**
     * POST /admin/faq/{id}/hapus — Hapus FAQ
     */
    public function delete(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/faq');
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM faqs WHERE id = :id");
        $stmt->execute([':id' => $id]);

        Session::flash('success', 'FAQ berhasil dihapus.');
        $this->redirect('/admin/faq');
    }

    /**
     * POST /admin/faq/{id}/toggle — Aktif / Nonaktifkan FAQ
     */
    public function toggle(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/faq');
            return;
        }

        $stmt = $this->db->prepare("UPDATE faqs SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);

        Session::flash('success', 'Status FAQ berhasil diperbarui.');
        $this->redirect('/admin/faq');
    }
}

