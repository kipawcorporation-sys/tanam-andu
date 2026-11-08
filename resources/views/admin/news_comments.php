<?php
/**
 * View: admin/news_comments.php — Moderasi Komentar Berita & Artikel.
 */
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 28px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:220px; height:220px; background:radial-gradient(circle,rgba(99,102,241,.12) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.4rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }

.filter-bar { background:#fff; border-radius:14px; padding:16px 20px; margin-bottom:20px; border:1px solid rgba(0,0,0,.06); box-shadow:0 1px 4px rgba(0,0,0,.04); display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.pf-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
.pf-card-header { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); padding:16px 24px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.pf-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.9rem; }
table.pf-table { width:100%; border-collapse:collapse; }
table.pf-table thead th { background:#f8fafc; padding:12px 16px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
table.pf-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
table.pf-table tbody tr:hover { background:#f8fafc; }
table.pf-table td { padding:14px 16px; vertical-align:middle; font-size:.84rem; color:#374151; }

.status-pill { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:50px; font-size:.72rem; font-weight:700; }
.status-approved { background:#dcfce7; color:#15803d; }
.status-pending  { background:#fef9c3; color:#a16207; }
.status-rejected { background:#fee2e2; color:#b91c1c; }

.btn-approve { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:5px 10px; border-radius:7px; font-size:.74rem; font-weight:700; cursor:pointer; }
.btn-approve:hover { background:#bbf7d0; }
.btn-reject { background:#fef3c7; color:#b45309; border:1px solid #fde68a; padding:5px 10px; border-radius:7px; font-size:.74rem; font-weight:700; cursor:pointer; }
.btn-reject:hover { background:#fde68a; }
.btn-del { background:#fff; color:#ef4444; border:1px solid #fca5a5; padding:5px 10px; border-radius:7px; font-size:.74rem; font-weight:700; cursor:pointer; }
.btn-del:hover { background:#fee2e2; }

.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state i { font-size:3rem; display:block; margin-bottom:12px; color:#cbd5e1; }
.pf-pagination { display:flex; gap:4px; justify-content:center; padding:16px 24px; }
.pf-pagination a { width:34px; height:34px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:.82rem; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; color:#374151; }
.pf-pagination a:hover { background:#f1f5f9; }
.pf-pagination .active-page { background:#22c55e; color:#fff; border-color:#22c55e; }
</style>

<!-- Page Hero -->
<div class="page-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-chat-left-text-fill me-2"></i>Moderasi Komentar Artikel</h4>
            <p>Kelola opini, tanggapan, dan pertanyaan dari para pembaca serta donatur.</p>
        </div>
        <a href="/admin/postingan" class="btn btn-outline-light btn-sm px-3" style="border-radius:8px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Postingan
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <i class="bi bi-funnel text-muted"></i>
    <span class="text-muted small fw-semibold">Filter Status:</span>
    <form method="GET" class="d-flex gap-2 mb-0">
        <select name="status" class="form-select form-select-sm" style="min-width:180px;" onchange="this.form.submit()">
            <option value="">Semua Status Komentar</option>
            <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui (Tayang)</option>
            <option value="pending"  <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu Moderasi</option>
            <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak (Disembunyikan)</option>
        </select>
    </form>
    <div class="ms-auto text-muted small fw-semibold"><?= number_format($total) ?> total komentar</div>
</div>

<!-- Table Card -->
<div class="pf-card">
    <div class="pf-card-header">
        <h6><i class="bi bi-table me-2 text-success"></i>Daftar Komentar Masuk</h6>
        <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
    </div>
    <div class="table-responsive">
        <table class="pf-table">
            <thead>
                <tr>
                    <th style="width:200px;">Pembaca</th>
                    <th style="width:220px;">Artikel Terkait</th>
                    <th>Isi Pesan Komentar</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:130px;">Waktu Kirim</th>
                    <th style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-chat-left-dots"></i>
                            <div class="fw-semibold mb-1">Belum ada komentar pembaca</div>
                            <small>Komentar dari formulir publik akan tampil di sini untuk dimoderasi.</small>
                        </div>
                    </td></tr>
                <?php endif; ?>

                <?php foreach ($comments as $c): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($c['email']) ?></div>
                        </td>
                        <td>
                            <a href="/kabar/<?= htmlspecialchars($c['news_slug']) ?>" target="_blank" class="fw-semibold text-dark text-decoration-none small hover-success line-clamp-2">
                                <?= htmlspecialchars($c['news_title']) ?> <i class="bi bi-box-arrow-up-right ms-1 text-muted" style="font-size:.7rem;"></i>
                            </a>
                        </td>
                        <td>
                            <div class="p-2 rounded bg-light small text-secondary" style="white-space:pre-line; max-height:100px; overflow-y:auto;">
                                <?= htmlspecialchars($c['comment']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($c['status'] === 'approved'): ?>
                                <span class="status-pill status-approved"><i class="bi bi-check-circle-fill"></i>Tayang</span>
                            <?php elseif ($c['status'] === 'pending'): ?>
                                <span class="status-pill status-pending"><i class="bi bi-clock-fill"></i>Pending</span>
                            <?php else: ?>
                                <span class="status-pill status-rejected"><i class="bi bi-x-circle-fill"></i>Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small fw-semibold"><?= date('d M Y', strtotime($c['created_at'])) ?></div>
                            <div class="small text-muted"><?= date('H:i', strtotime($c['created_at'])) ?> WIB</div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <?php if ($c['status'] !== 'approved'): ?>
                                    <form action="/admin/postingan/komentar/<?= $c['id'] ?>/status" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-approve" title="Setujui dan Tayangkan">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($c['status'] !== 'rejected'): ?>
                                    <form action="/admin/postingan/komentar/<?= $c['id'] ?>/status" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-reject" title="Tolak / Sembunyikan">
                                            <i class="bi bi-eye-slash"></i> Tolak
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form action="/admin/postingan/komentar/<?= $c['id'] ?>/hapus" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-del" onclick="return confirm('Hapus komentar dari <?= htmlspecialchars(addslashes($c['name'])) ?>?')" title="Hapus Permanen">
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
                <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>"
                   class="<?= $i === $page ? 'active-page' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

