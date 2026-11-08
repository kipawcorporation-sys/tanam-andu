<?php
/**
 * View: admin/trees.php — Inventaris Pohon & Spesies Konservasi (Pro)
 */
$activeTab = $_GET['tab'] ?? 'pohon';

$totTrees = $treeStats['total_trees'] ?? $total ?? 0;
$totSpecies = $treeStats['total_species'] ?? count($allSpecies ?? []);
$totLocations = $treeStats['total_locations'] ?? count($locations ?? []);
$totCO2 = $treeStats['total_co2'] ?? 0;
?>

<style>
/* ── Hero ── */
.tree-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 55%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(6, 21, 16, 0.2);
}
.tree-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.18) 0%, transparent 70%);
    pointer-events: none;
}
.tree-hero h4 {
    color: #fff;
    font-weight: 800;
    font-size: 1.45rem;
    margin: 0 0 6px;
}
.tree-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    margin: 0;
}

/* ── KPI Row ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 992px) { .kpi-row { grid-template-columns: repeat(2, 1fr); } }
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

/* ── Modern Tabs ── */
.tree-tab-bar {
    display: flex;
    gap: 6px;
    background: #e2e8f0;
    padding: 5px;
    border-radius: 14px;
    margin-bottom: 22px;
    max-width: 500px;
}
.tree-tab {
    flex: 1;
    text-align: center;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
    color: #64748b;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.tree-tab:hover {
    background: rgba(255, 255, 255, 0.7);
    color: #1e293b;
}
.tree-tab.active {
    background: #fff;
    color: #15803d;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.tree-tab .tab-badge {
    background: #f1f5f9;
    color: #64748b;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.72rem;
}
.tree-tab.active .tab-badge {
    background: #dcfce7;
    color: #15803d;
}

/* ── Table Card ── */
.tree-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    overflow: hidden;
}
.tree-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.tree-table thead th {
    background: #f8fafc;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 16px;
    white-space: nowrap;
}
.tree-table td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.tree-table tbody tr:hover {
    background: #f8faf8;
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
<div class="tree-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white bg-opacity-15 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <i class="bi bi-tree me-1"></i>REFORESTATION INVENTORY
                </span>
                <span class="badge bg-success bg-opacity-75 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <?= number_format($totTrees) ?> Batang Tercatat
                </span>
            </div>
            <h4><i class="bi bi-tree-fill me-2 text-success"></i>Inventaris Pohon &amp; Spesies Konservasi</h4>
            <p>Monitoring data geospasial tiap pohon tertanam, log perkembangan kesehatan bibit, dan kurasi master jenis tanaman.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($activeTab === 'spesies'): ?>
                <button class="btn btn-success fw-bold px-4 rounded-3 shadow-sm d-inline-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#modalTambahSpesies">
                    <i class="bi bi-plus-lg"></i>Tambah Jenis Pohon
                </button>
            <?php else: ?>
                <a href="/admin/pohon/export-csv" class="btn-hero-action">
                    <i class="bi bi-file-earmark-spreadsheet"></i>Unduh CSV Pohon
                </a>
                <a href="/transparansi" target="_blank" class="btn-hero-action">
                    <i class="bi bi-geo-alt"></i>Peta Publik ↗
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Flash Messages ── -->
<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (!empty($_SESSION['flash_warning'])): ?>
<div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_warning']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_warning']); endif; ?>

<!-- ══════════════════════════════════════════════════════════════
     KPI CARDS ROW
══════════════════════════════════════════════════════════════ -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">
            <i class="bi bi-tree-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-dark"><?= number_format($totTrees) ?></div>
            <div class="kpi-lbl">Total Pohon Tertanam</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="bi bi-flower1"></i>
        </div>
        <div>
            <div class="kpi-val text-primary"><?= number_format($totSpecies) ?></div>
            <div class="kpi-lbl">Jenis Spesies Pohon</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="bi bi-geo-alt-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-warning"><?= number_format($totLocations) ?></div>
            <div class="kpi-lbl">Titik Kawasan Konservasi</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
            <i class="bi bi-cloud-check-fill"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#9333ea; font-size:1.2rem;"><?= number_format($totCO2, 1, ',', '.') ?> kg</div>
            <div class="kpi-lbl">Estimasi Serapan CO₂</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     NAV TABS PRO
══════════════════════════════════════════════════════════════ -->
<div class="tree-tab-bar">
    <a href="/admin/pohon?tab=pohon" class="tree-tab <?= $activeTab !== 'spesies' ? 'active' : '' ?>">
        <i class="bi bi-tree"></i>Pohon Tertanam
        <span class="tab-badge font-mono"><?= number_format($totTrees) ?></span>
    </a>
    <a href="/admin/pohon?tab=spesies" class="tree-tab <?= $activeTab === 'spesies' ? 'active' : '' ?>">
        <i class="bi bi-flower1"></i>Master Jenis Spesies
        <span class="tab-badge font-mono"><?= count($allSpecies ?? []) ?></span>
    </a>
</div>

<?php if ($activeTab === 'spesies'): ?>
    <!-- ══════════════════════════════════════════════════════════
         TAB 2: KELOLA JENIS / SPESIES POHON
    ══════════════════════════════════════════════════════════ -->
    <div class="tree-card">
        <div class="tree-card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-grid-fill text-success"></i>
                <span class="fw-bold text-dark">Katalog Jenis &amp; Spesies Pohon</span>
            </div>
            <button class="btn btn-sm btn-success px-3" data-bs-toggle="modal" data-bs-target="#modalTambahSpesies">
                <i class="bi bi-plus-lg me-1"></i>Tambah Jenis
            </button>
        </div>
        <div class="table-responsive">
            <table class="table tree-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 65px;">Foto</th>
                        <th>Nama Jenis Spesies</th>
                        <th>Biaya Donasi</th>
                        <th>Serapan CO₂/Thn</th>
                        <th>Produksi O₂/Thn</th>
                        <th class="text-center">Total Ditanam</th>
                        <th class="text-center">Status</th>
                        <th style="width: 120px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allSpecies)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada jenis pohon terdaftar.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($allSpecies as $sp): ?>
                        <tr>
                            <td>
                                <?php if (!empty($sp['image'])): ?>
                                    <img src="<?= htmlspecialchars($sp['image']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>"
                                         class="rounded-3 border shadow-sm" style="width:50px; height:50px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="rounded-3 bg-success-subtle d-flex align-items-center justify-content-center border"
                                         style="width:50px; height:50px; color:#0a3622;">
                                        <i class="bi bi-tree fs-4"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($sp['name']) ?></div>
                                <div class="text-muted small text-truncate" style="max-width:260px; font-size:12px;">
                                    <?= htmlspecialchars($sp['description'] ?? 'Tidak ada catatan deskripsi') ?>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold font-mono text-success">
                                    Rp <?= number_format($sp['price_per_tree'], 0, ',', '.') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-mono">
                                    <?= number_format($sp['co2_absorption_kg_per_year'], 1) ?> kg/thn
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">
                                    <?= number_format($sp['oxygen_production_kg_per_year'], 1) ?> kg/thn
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border font-mono">
                                    <?= number_format($sp['planted_count'] ?? 0) ?> pohon
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($sp['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success" style="font-size:11px;">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:11px;">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary btn-edit-species"
                                            data-id="<?= $sp['id'] ?>"
                                            data-json="<?= htmlspecialchars(json_encode($sp)) ?>"
                                            title="Edit Jenis Pohon">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="/admin/spesies-pohon/<?= $sp['id'] ?>/hapus" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus / nonaktifkan jenis pohon \'<?= htmlspecialchars(addslashes($sp['name'])) ?>\'?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus / Nonaktifkan">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <!-- ══════════════════════════════════════════════════════════
         TAB 1: POHON TERTANAM (INVENTARIS FISIK)
    ══════════════════════════════════════════════════════════ -->
    <!-- Mini Species Badges -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?php foreach ($speciesStats as $sp): ?>
            <div class="bg-white p-2 px-3 rounded-3 border d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-tree text-success"></i>
                <span class="small fw-bold text-dark"><?= htmlspecialchars($sp['name']) ?>:</span>
                <span class="badge bg-light text-dark font-mono"><?= number_format($sp['count']) ?> batang</span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Table Card -->
    <div class="tree-card">
        <div class="tree-card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-fill text-success"></i>
                <span class="fw-bold text-dark">Data Inventaris Pohon Terverifikasi</span>
            </div>
            <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table tree-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode Pohon</th>
                        <th>Spesies</th>
                        <th>Donatur / Penanam</th>
                        <th>Lokasi Tanam</th>
                        <th>Koordinat GPS</th>
                        <th>Tanggal Tanam</th>
                        <th class="text-center">Status</th>
                        <th style="width: 100px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trees)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-tree fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data pohon yang tercatat dalam sistem.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($trees as $tr): ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-mono">
                                    <?= htmlspecialchars($tr['code'] ?? ('TREE-'.$tr['id'])) ?>
                                </span>
                                <div class="text-muted" style="font-size:10px;">ID: #<?= $tr['id'] ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">
                                    <i class="bi bi-patch-check-fill me-1"></i><?= htmlspecialchars($tr['species_name'] ?? 'Pohon') ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small"><?= htmlspecialchars($tr['planter_name'] ?? 'Donatur Anonim') ?></div>
                            </td>
                            <td>
                                <span class="small text-secondary">
                                    <?= !empty($tr['location_name']) ? htmlspecialchars(mb_substr($tr['location_name'], 0, 32)) : '—' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($tr['latitude']) && !empty($tr['longitude'])): ?>
                                    <a href="https://maps.google.com/?q=<?= $tr['latitude'] ?>,<?= $tr['longitude'] ?>" target="_blank"
                                       class="badge bg-success-subtle text-success text-decoration-none font-mono" style="font-size:11px;">
                                        <i class="bi bi-pin-map-fill me-1"></i><?= number_format($tr['latitude'], 4) ?>, <?= number_format($tr['longitude'], 4) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    <?= !empty($tr['planted_at']) ? date('d M Y', strtotime($tr['planted_at'])) : '—' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $badgeColor = match($tr['status'] ?? 'pending') {
                                    'ditanam'  => 'bg-primary-subtle text-primary',
                                    'tumbuh'   => 'bg-success-subtle text-success',
                                    'dipantau' => 'bg-info-subtle text-info',
                                    'pending'  => 'bg-warning-subtle text-warning-emphasis',
                                    default    => 'bg-secondary-subtle text-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeColor ?> rounded-pill px-3 py-1 font-mono" style="font-size:11px;">
                                    <?= htmlspecialchars(ucfirst($tr['status'] ?? 'pending')) ?>
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-tree"
                                        data-id="<?= $tr['id'] ?>"
                                        data-json="<?= htmlspecialchars(json_encode($tr)) ?>"
                                        title="Ubah Status &amp; Lokasi Pohon">
                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                <small class="text-muted">Menampilkan <?= count($trees) ?> dari <?= number_format($total) ?> pohon</small>
                <div class="btn-group btn-group-sm">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?tab=pohon&page=<?= $i ?>"
                           class="btn <?= $i === $page ? 'btn-success' : 'btn-outline-secondary' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: TAMBAH JENIS POHON
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalTambahSpesies" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <form action="/admin/spesies-pohon/simpan" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #061510 0%, #12382c 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Jenis Pohon Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small">Nama Jenis Pohon <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Kopi Arabika Kerenceng, Mangrove, Mahoni..." required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Biaya Donasi per Pohon (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="price_per_tree" class="form-control font-mono" value="50.000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Serapan CO₂ (kg/tahun)</label>
                            <input type="number" step="0.01" name="co2_absorption_kg_per_year" class="form-control font-mono" value="28.50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Produksi Oksigen (kg/tahun)</label>
                            <input type="number" step="0.01" name="oxygen_production_kg_per_year" class="form-control font-mono" value="100.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Foto / Gambar Spesies</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi &amp; Manfaat Ekologis</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Informasi habitat, perakaran, manfaat tanah, dan zona ketinggian lahan..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="tambah_is_active" checked>
                                <label class="form-check-label fw-semibold" for="tambah_is_active">Aktifkan untuk donasi publik</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">Simpan Jenis Pohon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: EDIT JENIS POHON
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditSpesies" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <form id="formEditSpesies" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #061510 0%, #12382c 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Jenis Pohon</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small">Nama Jenis Pohon <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_sp_name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Biaya Donasi per Pohon (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="price_per_tree" id="edit_sp_price" class="form-control font-mono" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Serapan CO₂ (kg/tahun)</label>
                            <input type="number" step="0.01" name="co2_absorption_kg_per_year" id="edit_sp_co2" class="form-control font-mono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Produksi Oksigen (kg/tahun)</label>
                            <input type="number" step="0.01" name="oxygen_production_kg_per_year" id="edit_sp_o2" class="form-control font-mono">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Ganti Gambar Spesies (Opsional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi &amp; Karakteristik</label>
                            <textarea name="description" id="edit_sp_desc" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_sp_active" value="1">
                                <label class="form-check-label fw-semibold" for="edit_sp_active">Status Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Perbarui Jenis Pohon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: EDIT DATA POHON TERTANAM
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditTree" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <form id="formEditTree" method="POST">
                <?= csrf_field() ?>
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #061510 0%, #12382c 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill me-2 text-success"></i>Ubah Status &amp; Lokasi Pohon</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kode Pohon</label>
                            <input type="text" id="tree_code_display" class="form-control font-mono" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status Pertumbuhan</label>
                            <select name="status" id="tree_status" class="form-select">
                                <option value="pending">Pending (Menunggu Ditanam)</option>
                                <option value="ditanam">Ditanam (Bibit Masuk Lahan)</option>
                                <option value="tumbuh">Tumbuh (Stabil &amp; Berdaun)</option>
                                <option value="dipantau">Dipantau (Monitoring Tim Lapangan)</option>
                                <option value="mati">Mati (Perlu Sulam)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Tanam</label>
                            <input type="date" name="planted_at" id="tree_planted_at" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kawasan Penanaman</label>
                            <select name="planting_location_id" id="tree_location_id" class="form-select">
                                <option value="">— Pilih Kawasan —</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?> (<?= htmlspecialchars($loc['city']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Jenis Spesies Pohon</label>
                            <select name="tree_species_id" id="tree_species_id" class="form-select">
                                <?php foreach ($allSpecies as $asp): ?>
                                    <option value="<?= $asp['id'] ?>"><?= htmlspecialchars($asp['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Latitude</label>
                            <input type="number" step="0.0000001" name="latitude" id="tree_lat" class="form-control font-mono" placeholder="-6.123456">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Longitude</label>
                            <input type="number" step="0.0000001" name="longitude" id="tree_lng" class="form-control font-mono" placeholder="106.123456">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit-species').forEach(btn => {
    btn.addEventListener('click', function() {
        const sp = JSON.parse(this.dataset.json);
        document.getElementById('formEditSpesies').action = '/admin/spesies-pohon/' + sp.id + '/update';
        document.getElementById('edit_sp_name').value = sp.name || '';
        document.getElementById('edit_sp_price').value = Number(sp.price_per_tree || 50000).toLocaleString('id-ID');
        document.getElementById('edit_sp_co2').value = sp.co2_absorption_kg_per_year || 0;
        document.getElementById('edit_sp_o2').value = sp.oxygen_production_kg_per_year || 0;
        document.getElementById('edit_sp_desc').value = sp.description || '';
        document.getElementById('edit_sp_active').checked = sp.is_active == 1;
        new bootstrap.Modal(document.getElementById('modalEditSpesies')).show();
    });
});

document.querySelectorAll('.btn-edit-tree').forEach(btn => {
    btn.addEventListener('click', function() {
        const tr = JSON.parse(this.dataset.json);
        document.getElementById('formEditTree').action = '/admin/pohon/' + tr.id + '/update';
        document.getElementById('tree_code_display').value = tr.code || ('TREE-' + tr.id);
        document.getElementById('tree_status').value = tr.status || 'pending';
        document.getElementById('tree_planted_at').value = tr.planted_at || '';
        document.getElementById('tree_location_id').value = tr.planting_location_id || '';
        document.getElementById('tree_species_id').value = tr.tree_species_id || '';
        document.getElementById('tree_lat').value = tr.latitude || '';
        document.getElementById('tree_lng').value = tr.longitude || '';
        new bootstrap.Modal(document.getElementById('modalEditTree')).show();
    });
});
</script>
