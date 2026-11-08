<?php
/**
 * View: admin/news.php — Manajemen Postingan, Artikel, dan Berita Lapangan.
 */
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 28px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:220px; height:220px; background:radial-gradient(circle,rgba(191,210,84,.08) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.4rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }
.btn-new-post { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; padding:9px 20px; border-radius:10px; font-size:.84rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:.2s; text-decoration:none; }
.btn-new-post:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(34,197,94,.35); color:#fff; }
.btn-comments { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:rgba(255,255,255,.9); padding:9px 18px; border-radius:10px; font-size:.84rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:.2s; text-decoration:none; }
.btn-comments:hover { background:rgba(255,255,255,.2); color:#fff; }

.stat-pills-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px; }
.stat-box { background:#fff; border-radius:14px; padding:16px 20px; border:1px solid rgba(0,0,0,.06); box-shadow:0 2px 8px rgba(0,0,0,.04); display:flex; align-items:center; gap:14px; }
.stat-box-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
.stat-box-icon.green { background:#dcfce7; color:#16a34a; }
.stat-box-icon.blue { background:#dbeafe; color:#2563eb; }
.stat-box-icon.amber { background:#fef3c7; color:#d97706; }
.stat-box-icon.purple { background:#f3e8ff; color:#9333ea; }
.stat-box-icon.gray { background:#f1f5f9; color:#64748b; }
.stat-box-num { font-size:1.25rem; font-weight:800; color:#0f172a; line-height:1.1; font-family:'JetBrains Mono',monospace; }
.stat-box-lbl { font-size:.76rem; color:#64748b; font-weight:600; margin-top:2px; }

.filter-bar { background:#fff; border-radius:14px; padding:16px 20px; margin-bottom:20px; border:1px solid rgba(0,0,0,.06); box-shadow:0 1px 4px rgba(0,0,0,.04); display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.pf-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
.pf-card-header { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); padding:16px 24px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.pf-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.9rem; }
table.pf-table { width:100%; border-collapse:collapse; }
table.pf-table thead th { background:#f8fafc; padding:12px 16px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
table.pf-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
table.pf-table tbody tr:hover { background:#f8fafc; }
table.pf-table td { padding:14px 16px; vertical-align:middle; font-size:.84rem; color:#374151; }

.post-thumb { width:70px; height:52px; border-radius:10px; object-fit:cover; border:2px solid #f1f5f9; }
.thumb-placeholder { width:70px; height:52px; border-radius:10px; background:#f1f5f9; border:2px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:1.2rem; }
.post-title { font-weight:700; color:#0f172a; text-decoration:none; display:block; line-height:1.35; margin-bottom:4px; font-size:.9rem; }
.post-title:hover { color:#22c55e; }
.post-meta-sub { display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-size:.74rem; color:#64748b; }
.cat-pill { background:#ecfdf5; color:#047857; font-weight:700; font-size:.7rem; padding:2px 8px; border-radius:50px; border:1px solid #a7f3d0; }
.featured-pill { background:#fef3c7; color:#92400e; font-weight:800; font-size:.68rem; padding:2px 8px; border-radius:50px; border:1px solid #fde68a; display:inline-flex; align-items:center; gap:4px; }
.pub-pill { background:#dcfce7; color:#15803d; display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:50px; font-size:.72rem; font-weight:700; }
.draft-pill { background:#f1f5f9; color:#64748b; display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:50px; font-size:.72rem; font-weight:700; }

.btn-act { padding:6px 10px; border-radius:8px; font-size:.74rem; font-weight:700; cursor:pointer; transition:.15s; display:inline-flex; align-items:center; gap:4px; text-decoration:none; border:none; }
.btn-act-featured { background:#fef3c7; color:#b45309; border:1px solid #fde68a; }
.btn-act-featured:hover { background:#fde68a; }
.btn-act-featured.active { background:#f59e0b; color:#fff; border-color:#d97706; }
.btn-act-pub { background:#fef9c3; color:#a16207; border:1px solid #fef08a; }
.btn-act-pub:hover { background:#fef08a; }
.btn-act-draft { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
.btn-act-draft:hover { background:#bbf7d0; }
.btn-act-edit { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.btn-act-edit:hover { background:#dbeafe; }
.btn-act-del { background:#fff; color:#ef4444; border:1px solid #fca5a5; }
.btn-act-del:hover { background:#fee2e2; border-color:#ef4444; }
.btn-act-view { background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
.btn-act-view:hover { background:#f1f5f9; color:#0f172a; }

.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state i { font-size:3rem; display:block; margin-bottom:12px; color:#cbd5e1; }
.pf-pagination { display:flex; gap:4px; justify-content:center; padding:16px 24px; }
.pf-pagination a { width:34px; height:34px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:.82rem; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; color:#374151; transition:.15s; }
.pf-pagination a:hover { background:#f1f5f9; }
.pf-pagination .active-page { background:#22c55e; color:#fff; border-color:#22c55e; }
</style>

<!-- Page Hero -->
<div class="page-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-newspaper me-2"></i>Postingan & Kabar Lapangan</h4>
            <p>Kelola artikel publikasi, dokumentasi ekspedisi, berita agroforestry, dan jurnal lapangan.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/postingan/komentar" class="btn-comments">
                <i class="bi bi-chat-left-text-fill"></i>Moderasi Komentar (<?= number_format($stats['comments'] ?? 0) ?>)
            </a>
            <a href="/admin/postingan/tambah" class="btn-new-post">
                <i class="bi bi-pencil-square"></i>Tulis Postingan Baru
            </a>
        </div>
    </div>
</div>

<!-- 5 Stat Boxes -->
<div class="stat-pills-row">
    <div class="stat-box">
        <div class="stat-box-icon green"><i class="bi bi-journal-check"></i></div>
        <div>
            <div class="stat-box-num"><?= number_format($stats['total'] ?? 0) ?></div>
            <div class="stat-box-lbl">Total Artikel</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon blue"><i class="bi bi-send-check"></i></div>
        <div>
            <div class="stat-box-num"><?= number_format($stats['published'] ?? 0) ?></div>
            <div class="stat-box-lbl">Diterbitkan</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon gray"><i class="bi bi-file-earmark-text"></i></div>
        <div>
            <div class="stat-box-num"><?= number_format($stats['draft'] ?? 0) ?></div>
            <div class="stat-box-lbl">Draf Simpanan</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon amber"><i class="bi bi-eye"></i></div>
        <div>
            <div class="stat-box-num"><?= number_format($stats['views'] ?? 0) ?></div>
            <div class="stat-box-lbl">Total Pembaca</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-box-icon purple"><i class="bi bi-chat-dots"></i></div>
        <div>
            <div class="stat-box-num"><?= number_format($stats['comments'] ?? 0) ?></div>
            <div class="stat-box-lbl">Komentar Masuk</div>
        </div>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="filter-bar">
    <i class="bi bi-funnel text-muted"></i>
    <span class="text-muted small fw-semibold">Pencarian & Filter:</span>
    <form method="GET" class="d-flex gap-2 mb-0 flex-wrap align-items-center flex-grow-1">
        <div class="input-group input-group-sm" style="max-width:280px;">
            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Cari judul, tag, isi..."
                   value="<?= htmlspecialchars($keyword ?? '') ?>">
        </div>

        <select name="category" class="form-select form-select-sm" style="min-width:170px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($catFilter ?? '') === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status" class="form-select form-select-sm" style="min-width:150px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="published" <?= ($statusFilter ?? '') === 'published' ? 'selected' : '' ?>>Terbit (Published)</option>
            <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>>Draf (Unpublished)</option>
        </select>

        <button type="submit" class="btn btn-sm btn-dark px-3">Terapkan</button>
        <?php if (!empty($keyword) || !empty($catFilter) || !empty($statusFilter)): ?>
            <a href="/admin/postingan" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                <i class="bi bi-x-circle me-1"></i>Reset
            </a>
        <?php endif; ?>
    </form>
    <div class="ms-auto text-muted small fw-semibold"><?= number_format($total) ?> artikel ditemukan</div>
</div>

<!-- Table Card -->
<div class="pf-card">
    <div class="pf-card-header">
        <h6><i class="bi bi-table me-2 text-success"></i>Daftar Postingan Artikel & Berita</h6>
        <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
    </div>
    <div class="table-responsive">
        <table class="pf-table">
            <thead>
                <tr>
                    <th style="width:80px;">Sampul</th>
                    <th>Judul & Informasi Artikel</th>
                    <th style="width:120px;">Kategori</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:130px;">Statistik</th>
                    <th style="width:130px;">Waktu Terbit</th>
                    <th style="width:180px;">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-newspaper"></i>
                            <div class="fw-semibold mb-1">Tidak ada postingan ditemukan</div>
                            <small class="d-block mb-3">Cobalah mengubah kata kunci atau buat postingan artikel baru</small>
                            <a href="/admin/postingan/tambah" class="btn btn-success btn-sm">
                                <i class="bi bi-plus-lg me-1"></i>Tulis Postingan Baru
                            </a>
                        </div>
                    </td></tr>
                <?php endif; ?>

                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="" class="post-thumb">
                            <?php else: ?>
                                <div class="thumb-placeholder"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/kabar/<?= htmlspecialchars($post['slug']) ?>" target="_blank" class="post-title">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                            <div class="post-meta-sub">
                                <?php if (!empty($post['is_featured'])): ?>
                                    <span class="featured-pill"><i class="bi bi-star-fill me-1" style="color: #d97706;"></i>HEADLINE UTAMA</span>
                                <?php endif; ?>
                                <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></span>
                                <span>•</span>
                                <span><i class="bi bi-clock me-1"></i><?= $post['read_time_minutes'] ?? 3 ?> mnt baca</span>
                                <?php if (!empty($post['tags'])): ?>
                                    <span>•</span>
                                    <span class="text-truncate" style="max-width:200px;"><i class="bi bi-tags me-1"></i><?= htmlspecialchars($post['tags']) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="cat-pill"><?= htmlspecialchars($post['category'] ?? 'Umum') ?></span>
                        </td>
                        <td>
                            <?php if ($post['is_published']): ?>
                                <span class="pub-pill"><i class="bi bi-check-circle-fill"></i>Terbit</span>
                            <?php else: ?>
                                <span class="draft-pill"><i class="bi bi-file-earmark"></i>Draf</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark"><i class="bi bi-eye text-primary me-1"></i><?= number_format($post['views_count'] ?? 0) ?> views</div>
                            <div class="small text-muted"><i class="bi bi-chat-left-text text-secondary me-1"></i><?= number_format($post['total_comments'] ?? 0) ?> komentar</div>
                        </td>
                        <td>
                            <div class="small fw-semibold"><?= !empty($post['published_at']) ? date('d M Y', strtotime($post['published_at'])) : '—' ?></div>
                            <div class="small text-muted"><?= !empty($post['published_at']) ? date('H:i', strtotime($post['published_at'])) . ' WIB' : 'Belum Terbit' ?></div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <!-- Toggle Featured -->
                                <form action="/admin/postingan/<?= $post['id'] ?>/toggle-featured" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-act btn-act-featured <?= !empty($post['is_featured']) ? 'active' : '' ?>"
                                            title="<?= !empty($post['is_featured']) ? 'Cabut dari Headline Utama' : 'Jadikan Headline Berita Utama' ?>">
                                        <i class="bi bi-star<?= !empty($post['is_featured']) ? '-fill' : '' ?>"></i>
                                    </button>
                                </form>

                                <!-- Toggle Publish / Draft -->
                                <form action="/admin/postingan/<?= $post['id'] ?>/toggle" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-act <?= $post['is_published'] ? 'btn-act-pub' : 'btn-act-draft' ?>"
                                            title="<?= $post['is_published'] ? 'Ubah ke Draf' : 'Terbitkan Sekarang' ?>">
                                        <i class="bi <?= $post['is_published'] ? 'bi-eye-slash' : 'bi-send-check' ?>"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <a href="/admin/postingan/<?= $post['id'] ?>/edit" class="btn-act btn-act-edit" title="Edit Postingan">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Lihat Publik -->
                                <a href="/kabar/<?= htmlspecialchars($post['slug']) ?>" target="_blank" class="btn-act btn-act-view" title="Lihat di Web Publik">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>

                                <!-- Hapus -->
                                <form action="/admin/postingan/<?= $post['id'] ?>/hapus" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-act btn-act-del"
                                            onclick="return confirm('Hapus artikel &quot;<?= htmlspecialchars(addslashes($post['title'])) ?>&quot; secara permanen?')"
                                            title="Hapus Artikel">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="pf-pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&category=<?= urlencode($catFilter) ?>&status=<?= urlencode($statusFilter) ?>&q=<?= urlencode($keyword) ?>"
                   class="<?= $i === $page ? 'active-page' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
