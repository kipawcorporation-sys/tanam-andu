<?php
/**
 * View: admin/faqs.php — Manajemen Tanya Jawab (FAQ)
 */
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 28px 32px; margin-bottom: 28px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:200px; height:200px; background:radial-gradient(circle,rgba(34,197,94,.12) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.4rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }
.btn-new-item { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; padding:9px 20px; border-radius:10px; font-size:.84rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:.2s; }
.btn-new-item:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(34,197,94,.35); }
</style>
<div class="page-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-question-circle-fill me-2"></i>Tanya Jawab (FAQ)</h4>
            <p>Kelola pertanyaan yang sering diajukan di halaman utama dan pusat bantuan</p>
        </div>
        <button class="btn-new-item" data-bs-toggle="modal" data-bs-target="#modalTambahFaq">
            <i class="bi bi-plus-lg"></i>Tambah FAQ Baru
        </button>
    </div>
</div>

<!-- Filter Kategori & Pencarian -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="d-flex flex-wrap gap-2">
                    <a href="/admin/faq" class="btn btn-sm <?= empty($selectedCategory) ? 'btn-success' : 'btn-outline-secondary' ?>">
                        Semua Kategori (<?= count($faqs) ?>)
                    </a>
                    <?php 
                    $presetCats = ['umum', 'donasi', 'kopi', 'event', 'pembayaran', 'pohon', 'akun'];
                    $allCats = array_unique(array_merge($presetCats, $categories ?? []));
                    foreach ($allCats as $cat): 
                    ?>
                        <a href="/admin/faq?kategori=<?= urlencode($cat) ?>" 
                           class="btn btn-sm <?= ($selectedCategory === $cat) ? 'btn-success' : 'btn-outline-secondary' ?>">
                            <?= ucfirst($cat) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-5">
                <form action="/admin/faq" method="GET" class="d-flex gap-2">
                    <?php if (!empty($selectedCategory)): ?>
                        <input type="hidden" name="kategori" value="<?= htmlspecialchars($selectedCategory) ?>">
                    <?php endif; ?>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari pertanyaan / jawaban..." value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                    <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-search"></i></button>
                    <?php if (!empty($searchQuery)): ?>
                        <a href="/admin/faq<?= !empty($selectedCategory) ? '?kategori='.urlencode($selectedCategory) : '' ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabel FAQ -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Pertanyaan</th>
                        <th style="width: 140px;">Kategori</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 130px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($faqs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada FAQ ditemukan
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($faqs as $f): ?>
                        <tr class="<?= !$f['is_active'] ? 'opacity-50 bg-light' : '' ?>">
                            <td class="text-center fw-bold text-muted"><?= (int)$f['sort_order'] ?></td>
                            <td>
                                <div class="fw-semibold text-dark mb-1"><?= htmlspecialchars($f['question']) ?></div>
                                <div class="text-muted small text-truncate" style="max-width: 580px;">
                                    <?= htmlspecialchars($f['answer']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill text-uppercase" style="font-size: 11px;">
                                    <?= htmlspecialchars($f['category']) ?>
                                </span>
                            </td>
                            <td>
                                <form action="/admin/faq/<?= $f['id'] ?>/toggle" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm p-0 border-0" title="Klik untuk ubah status">
                                        <?php if ($f['is_active']): ?>
                                            <span class="badge bg-success-subtle px-2 py-1" style="color:#0a3622;"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary btn-edit-faq me-1"
                                        data-json="<?= htmlspecialchars(json_encode($f)) ?>"
                                        title="Edit FAQ">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="/admin/faq/<?= $f['id'] ?>/hapus" method="POST" class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan FAQ ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus FAQ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah FAQ -->
<div class="modal fade" id="modalTambahFaq" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/faq/simpan" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-success me-2"></i>Tambah FAQ Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pertanyaan *</label>
                        <input type="text" name="question" class="form-control" placeholder="Contoh: Bagaimana cara memesan Kopi Kerenceng?" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="umum">Umum</option>
                                <option value="donasi">Donasi Pohon</option>
                                <option value="kopi">Kopi Spesialti & Produk</option>
                                <option value="event">Kegiatan & Relawan</option>
                                <option value="pembayaran">Pembayaran & Rekening</option>
                                <option value="pohon">Pemantauan Pohon</option>
                                <option value="akun">Akun & Sertifikat</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jawaban Lengkap *</label>
                        <textarea name="answer" class="form-control" rows="5" placeholder="Tuliskan jawaban yang ramah dan jelas..." required></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="tambah_is_active" value="1" checked>
                        <label class="form-check-label fw-semibold" for="tambah_is_active">Langsung tampilkan di situs (Aktif)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit FAQ -->
<div class="modal fade" id="modalEditFaq" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditFaq" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pertanyaan *</label>
                        <input type="text" name="question" id="edit_question" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="category" id="edit_category" class="form-select">
                                <option value="umum">Umum</option>
                                <option value="donasi">Donasi Pohon</option>
                                <option value="kopi">Kopi Spesialti & Produk</option>
                                <option value="event">Kegiatan & Relawan</option>
                                <option value="pembayaran">Pembayaran & Rekening</option>
                                <option value="pohon">Pemantauan Pohon</option>
                                <option value="akun">Akun & Sertifikat</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jawaban Lengkap *</label>
                        <textarea name="answer" id="edit_answer" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                        <label class="form-check-label fw-semibold" for="edit_is_active">Tampilkan di situs (Aktif)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Perbarui FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit-faq').forEach(btn => {
    btn.addEventListener('click', function() {
        const f = JSON.parse(this.dataset.json);
        document.getElementById('formEditFaq').action = '/admin/faq/' + f.id + '/update';
        document.getElementById('edit_question').value = f.question;
        document.getElementById('edit_answer').value = f.answer;
        document.getElementById('edit_category').value = f.category || 'umum';
        document.getElementById('edit_sort_order').value = f.sort_order || 0;
        document.getElementById('edit_is_active').checked = (f.is_active == 1);
        new bootstrap.Modal(document.getElementById('modalEditFaq')).show();
    });
});
</script>

