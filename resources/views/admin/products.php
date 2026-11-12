<?php
/**
 * View: admin/products.php — Manajemen Katalog Produk & Kopi Spesialti
 * Plant For Fun (Kerenceng Coffee & Agroforestry)
 */
?>

<?php
$totProd = $productStats['total'] ?? count($products);
$totKopi = $productStats['kopi'] ?? 0;
$totMerch= $productStats['merchandise'] ?? 0;
$totBund = $productStats['bundle'] ?? 0;
$totStok = $productStats['total_stock'] ?? 0;
?>

<style>
/* ── Hero ── */
.prod-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 55%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(6, 21, 16, 0.2);
}
.prod-hero::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(191, 210, 84, 0.15) 0%, transparent 70%);
    pointer-events: none;
}
.prod-hero h4 {
    color: #fff;
    font-weight: 800;
    font-size: 1.45rem;
    margin: 0 0 6px;
}
.prod-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    margin: 0;
}

/* ── KPI Grid ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 992px) { .kpi-row { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 576px) { .kpi-row { grid-template-columns: repeat(2, 1fr); } }
.kpi-card {
    background: #fff;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.25s ease;
}
.kpi-card:hover {
    box-shadow: 0 8px 24px rgba(18, 56, 44, 0.09);
    transform: translateY(-2px);
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.kpi-val {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 2px;
}
.kpi-lbl {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.btn-hero-action {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 0.84rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    backdrop-filter: blur(4px);
    transition: all 0.2s;
    text-decoration: none;
    cursor: pointer;
}
.btn-hero-action:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
}
</style>

<!-- ══════════════════════════════════════════════════════════════
     PAGE HERO
══════════════════════════════════════════════════════════════ -->
<div class="prod-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white bg-opacity-15 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <i class="bi bi-box-seam me-1"></i>KERENCENG COFFEE &amp; MERCHANDISE
                </span>
                <span class="badge bg-success bg-opacity-75 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <?= $totProd ?> Produk Aktif
                </span>
            </div>
            <h4><i class="bi bi-cup-hot-fill me-2 text-warning"></i>Katalog Produk &amp; Kopi Spesialti</h4>
            <p>Kelola kurasi biji kopi mikro-lot Kerenceng 1.550 mdpl, paket bundle bibit pohon, varian gilingan, dan stok gudang.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/admin/produk/export-csv" class="btn-hero-action">
                <i class="bi bi-file-earmark-spreadsheet"></i>Unduh CSV
            </a>
            <a href="/produk" target="_blank" class="btn-hero-action">
                <i class="bi bi-shop"></i>Toko Publik ↗
            </a>
            <button class="btn btn-success fw-bold px-4 rounded-3 shadow-sm d-inline-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                <i class="bi bi-plus-lg"></i>Tambah Produk
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     KPI CARDS ROW
══════════════════════════════════════════════════════════════ -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-dark"><?= $totProd ?></div>
            <div class="kpi-lbl">Total Produk</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="bi bi-cup-hot-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-warning"><?= $totKopi ?></div>
            <div class="kpi-lbl">Kopi Spesialti</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
            <i class="bi bi-tag-fill"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#9333ea;"><?= $totMerch ?></div>
            <div class="kpi-lbl">Merchandise &amp; Kaos</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#3b82f6;">
            <i class="bi bi-gift-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-primary"><?= $totBund ?></div>
            <div class="kpi-lbl">Paket Bundle</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#16a34a;">
            <i class="bi bi-layers-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-success"><?= number_format($totStok) ?></div>
            <div class="kpi-lbl">Stok Gudang (Pcs)</div>
        </div>
    </div>
</div>

<!-- Filter Kategori & Search Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small fw-bold me-2 d-none d-md-inline">Kategori:</span>
                <a href="/admin/produk" class="btn btn-sm <?= empty($selectedCategory) ? 'btn-success' : 'btn-outline-secondary' ?>">
                    Semua (<?= count($products) ?>)
                </a>
                <a href="/admin/produk?kategori=kopi" class="btn btn-sm <?= ($selectedCategory === 'kopi') ? 'btn-success' : 'btn-outline-secondary' ?>">
                    ☕ Kopi Spesialti
                </a>
                <a href="/admin/produk?kategori=kaos" class="btn btn-sm <?= in_array($selectedCategory, ['kaos', 'merchandise']) ? 'btn-success' : 'btn-outline-secondary' ?>">
                    👕 Kaos &amp; Merchandise
                </a>
                <a href="/admin/produk?kategori=bundle" class="btn btn-sm <?= ($selectedCategory === 'bundle') ? 'btn-success' : 'btn-outline-secondary' ?>">
                    🎁 Paket Bundle
                </a>
            </div>
            <div class="input-group input-group-sm" style="max-width:240px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="prod-search" class="form-control border-start-0" placeholder="Cari nama produk..."
                       oninput="filterProductRows(this.value)">
            </div>
        </div>
    </div>
</div>

<!-- Tabel Produk -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Foto</th>
                        <th>Nama & Detail Produk</th>
                        <th>Kategori</th>
                        <th>Harga Dasar</th>
                        <th>Stok Total</th>
                        <th>Varian</th>
                        <th style="width: 90px;">Status</th>
                        <th style="width: 170px;" class="text-end pe-3">Kelola & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada produk dalam kategori ini
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($products as $p): 
                        if (!empty($p['primary_image'])) {
                            $thumb = asset($p['primary_image']);
                        } elseif ($p['category'] === 'kaos' || $p['category'] === 'bundle' || $p['category'] === 'merchandise') {
                            $thumb = asset('assets/images/kaos-plant-for-fun.jpg');
                        } else {
                            $thumb = asset('assets/images/coffee-honey.jpg');
                        }
                    ?>
                        <tr class="prod-row <?= !$p['is_active'] ? 'opacity-50 bg-light' : '' ?>"
                            data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
                            data-slug="<?= strtolower(htmlspecialchars($p['slug'])) ?>">
                            <td>
                                <img src="<?= htmlspecialchars($thumb) ?>" 
                                     alt="<?= htmlspecialchars($p['name']) ?>" 
                                     class="rounded-3 shadow-sm border" 
                                     style="width: 52px; height: 52px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="text-muted font-mono" style="font-size: 11px;"><?= htmlspecialchars($p['slug']) ?> &bull; <?= (int)$p['weight_gram'] ?>g</div>
                                <?php if (!empty($p['description'])): ?>
                                    <div class="text-muted small text-truncate" style="max-width: 320px; font-size: 12px;">
                                        <?= htmlspecialchars($p['description']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['category'] === 'kopi'): ?>
                                    <span class="badge bg-amber-subtle text-dark border px-2 py-1" style="background:#fff3cd;">
                                        ☕ Kopi Spesialti
                                    </span>
                                <?php elseif ($p['category'] === 'bundle'): ?>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                        🎁 Paket Bundle
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                        👕 <?= htmlspecialchars(ucfirst($p['category'] ?? 'Kaos')) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-success">
                                Rp <?= number_format($p['base_price'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <span class="badge <?= (int)$p['total_stock'] > 10 ? 'bg-success' : ((int)$p['total_stock'] > 0 ? 'bg-warning text-dark' : 'bg-danger') ?> font-mono">
                                    <?= number_format($p['total_stock']) ?> pcs
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-dark btn-variants-modal"
                                        data-product-id="<?= $p['id'] ?>"
                                        data-product-name="<?= htmlspecialchars($p['name']) ?>"
                                        data-category="<?= htmlspecialchars($p['category'] ?? 'kopi') ?>"
                                        data-base-price="<?= (float)$p['base_price'] ?>"
                                        data-variants="<?= htmlspecialchars(json_encode($p['variants'] ?? [])) ?>"
                                        title="Kelola Varian & Stok">
                                    <i class="bi bi-layers me-1"></i><?= $p['variant_count'] ?> varian
                                </button>
                            </td>
                            <td>
                                <?php if ($p['is_active']): ?>
                                    <span class="badge bg-success-subtle" style="color:#0a3622;">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary btn-edit-product me-1"
                                        data-id="<?= $p['id'] ?>"
                                        data-json="<?= htmlspecialchars(json_encode($p)) ?>"
                                        title="Edit Informasi Produk">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="/admin/produk/<?= $p['id'] ?>/hapus" method="POST" class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Produk">
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

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/produk/simpan" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-success me-2"></i>Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Produk *</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Kopi Arabika Kerenceng Anaerob Slow Dry 200gr" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori *</label>
                            <select name="category" class="form-select" required>
                                <option value="kopi" selected>☕ Kopi Spesialti</option>
                                <option value="kaos">👕 Kaos & Merchandise</option>
                                <option value="bundle">🎁 Paket Bundle & Bibit</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga Dasar (Rp) *</label>
                            <input type="text" name="base_price" class="form-control" placeholder="95000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Bersih (gram) *</label>
                            <input type="number" name="weight_gram" class="form-control" value="200" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Foto Produk Utama</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, WEBP (disarankan rasio 4:3 atau 1:1)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Varian Awal & Stok Gudang</label>
                            <div class="input-group">
                                <input type="text" name="default_variant_name" class="form-control" placeholder="Kemasan / Ukuran (cth: 200gr)" value="200gr">
                                <input type="number" name="default_stock" class="form-control" placeholder="Stok" value="30" style="max-width: 90px;">
                            </div>
                            <div class="form-text">Varian lain dapat ditambahkan setelah produk disimpan</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi & Catatan Rasa (Notes)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan profil sangrai, ketinggian tanam, notes rasa kopi, atau material bahan..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="tambah_p_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="tambah_p_active">Langsung tampilkan di Toko Publik (Aktif)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div class="modal fade" id="modalEditProduk" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditProduk" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Informasi Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Produk *</label>
                            <input type="text" name="name" id="edit_p_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategori *</label>
                            <select name="category" id="edit_p_category" class="form-select" required>
                                <option value="kopi">☕ Kopi Spesialti</option>
                                <option value="kaos">👕 Kaos & Merchandise</option>
                                <option value="bundle">🎁 Paket Bundle & Bibit</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga Dasar (Rp) *</label>
                            <input type="text" name="base_price" id="edit_p_base_price" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Bersih (gram) *</label>
                            <input type="number" name="weight_gram" id="edit_p_weight_gram" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" id="edit_p_sort_order" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ganti Foto Produk (Opsional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengganti foto yang sudah ada.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi & Catatan Rasa</label>
                            <textarea name="description" id="edit_p_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_p_is_active" value="1">
                                <label class="form-check-label fw-semibold" for="edit_p_is_active">Produk Aktif & Dapat Dibeli</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Perbarui Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kelola Varian & Stok -->
<div class="modal fade" id="modalVariants" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold" id="variantModalTitle"><i class="bi bi-layers text-success me-2"></i>Kelola Varian & Stok</h5>
                    <div class="text-muted small" id="variantModalSubtitle">Daftar varian ukuran kemasan, tipe gilingan, SKU, harga, dan ketersediaan stok</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Daftar Varian Saat Ini -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Varian Tersedia</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light small">
                                <tr>
                                    <th>Ukuran / Kemasan</th>
                                    <th>Varian / Gilingan</th>
                                    <th>SKU</th>
                                    <th style="width: 140px;">Harga (Rp)</th>
                                    <th style="width: 120px;">Stok</th>
                                    <th style="width: 130px;" class="text-center">Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody id="variantTableBody">
                                <!-- Diisi via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Tambah Varian Baru -->
                <div class="card border-0 bg-light p-3 rounded-3">
                    <h6 class="fw-bold mb-3 text-success"><i class="bi bi-plus-circle me-1"></i>Tambah Varian Baru untuk Produk Ini</h6>
                    <form id="formTambahVarian" method="POST">
                        <?= csrf_field() ?>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Ukuran / Kemasan *</label>
                                <input type="text" name="size" class="form-control form-control-sm" placeholder="200gr, 500gr, 1kg, L, XL" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Tipe / Gilingan *</label>
                                <input type="text" name="color" class="form-control form-control-sm" placeholder="Biji Utuh, Giling Halus, Kasar" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">SKU (Kode)</label>
                                <input type="text" name="sku" class="form-control form-control-sm" placeholder="Otomatis jika kosong">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Harga (Rp) *</label>
                                <input type="text" name="price" id="new_var_price" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small fw-semibold">Stok *</label>
                                <input type="number" name="stock" class="form-control form-control-sm" value="25" min="0" required>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-sm btn-success w-100" title="Simpan Varian"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Handler Edit Produk
document.querySelectorAll('.btn-edit-product').forEach(btn => {
    btn.addEventListener('click', function() {
        const p = JSON.parse(this.dataset.json);
        document.getElementById('formEditProduk').action = '/admin/produk/' + p.id + '/update';
        document.getElementById('edit_p_name').value = p.name || '';
        document.getElementById('edit_p_category').value = p.category || 'kopi';
        document.getElementById('edit_p_base_price').value = Math.round(p.base_price || 0);
        document.getElementById('edit_p_weight_gram').value = p.weight_gram || 200;
        document.getElementById('edit_p_sort_order').value = p.sort_order || 0;
        document.getElementById('edit_p_description').value = p.description || '';
        document.getElementById('edit_p_is_active').checked = (p.is_active == 1);
        new bootstrap.Modal(document.getElementById('modalEditProduk')).show();
    });
});

// Handler Modal Kelola Varian & Stok
document.querySelectorAll('.btn-variants-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        const pid = this.dataset.productId;
        const pname = this.dataset.productName;
        const basePrice = this.dataset.basePrice;
        const variants = JSON.parse(this.dataset.variants || '[]');

        document.getElementById('variantModalTitle').innerHTML = '<i class="bi bi-layers text-success me-2"></i>Varian: ' + pname;
        document.getElementById('formTambahVarian').action = '/admin/produk/' + pid + '/varian/simpan';
        document.getElementById('new_var_price').value = Math.round(basePrice);

        const tbody = document.getElementById('variantTableBody');
        tbody.innerHTML = '';

        if (variants.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada rincian varian untuk produk ini. Tambahkan di bawah.</td></tr>';
        } else {
            variants.forEach(v => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <form action="/admin/produk/varian/${v.id}/stok" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <td class="fw-semibold">${v.size || '-'}</td>
                        <td>${v.color || '-'}</td>
                        <td><code class="small text-muted">${v.sku || '-'}</code></td>
                        <td>
                            <input type="number" name="price" class="form-control form-control-sm font-mono" value="${Math.round(v.price)}" required>
                        </td>
                        <td>
                            <input type="number" name="stock" class="form-control form-control-sm font-mono" value="${v.stock}" min="0" required>
                        </td>
                        <td class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Perbarui Harga & Stok">
                                <i class="bi bi-check-lg"></i>
                            </button>
                    </form>
                    <form action="/admin/produk/varian/${v.id}/hapus" method="POST" class="d-inline" onsubmit="return confirm('Hapus varian ini?')">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Varian">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                        </td>
                `;
                tbody.appendChild(tr);
            });
        }

        new bootstrap.Modal(document.getElementById('modalVariants')).show();
    });
});

function filterProductRows(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.prod-row').forEach(row => {
        const name = row.dataset.name || '';
        const slug = row.dataset.slug || '';
        row.style.display = (!q || name.includes(q) || slug.includes(q)) ? '' : 'none';
    });
}
</script>
