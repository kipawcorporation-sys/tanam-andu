<?php
/**
 * View: admin/certificates.php — Manajemen Sertifikat Digital
 * Platform: Plant For Fun • Kerenceng Coffee Expedition
 */
?>

<style>
.cert-hero {
    background: linear-gradient(135deg, #0a1f17 0%, #12382c 60%, #1a4d3c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(6,21,16,.35);
}
.cert-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(191,210,84,.18) 0%, transparent 70%);
    pointer-events: none;
}
.stat-pill {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 14px;
    padding: 14px 20px;
    text-align: center;
    min-width: 120px;
}
.cert-table th { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; white-space: nowrap; }
.cert-table td { vertical-align: middle; font-size: .88rem; }
.badge-donasi      { background: #d1fae5; color: #065f46; }
.badge-partisipasi { background: #dbeafe; color: #1e40af; }
.cert-num { font-family: 'Space Mono', monospace; font-size: .78rem; color: #12382c; font-weight: 700; }
.btn-export { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25); color:#fff; border-radius:10px; font-size:.82rem; font-weight:700; }
.btn-export:hover { background: rgba(255,255,255,.22); color:#fff; }
</style>

<!-- HERO HEADER -->
<div class="cert-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 position-relative" style="z-index:2;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge rounded-pill font-mono px-3 py-1" style="background:rgba(191,210,84,.2);color:#bfd254;border:1px solid rgba(191,210,84,.3);">
                    <i class="bi bi-patch-check me-1"></i>SERTIFIKAT DIGITAL
                </span>
            </div>
            <h2 class="fw-bold text-white mb-1 font-poster" style="font-size:1.6rem;">Manajemen Sertifikat</h2>
            <p class="text-white-50 mb-0 small">Semua sertifikat donasi & partisipasi relawan yang telah diterbitkan oleh sistem.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="/admin/sertifikat/export-csv" class="btn btn-export btn-sm px-3 py-2">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <button class="btn btn-sm py-2 px-3 fw-bold" style="background:#bfd254;color:#0a1f17;border-radius:10px;" data-bs-toggle="modal" data-bs-target="#reissueModal">
                <i class="bi bi-plus-circle me-1"></i>Terbitkan Manual
            </button>
        </div>
    </div>

    <!-- Stat Pills -->
    <div class="d-flex flex-wrap gap-3 mt-4 position-relative" style="z-index:2;">
        <div class="stat-pill">
            <div class="text-white-50 small mb-1">Total Sertifikat</div>
            <div class="text-white fw-bold fs-5 font-mono"><?= number_format($stats['total']) ?></div>
        </div>
        <div class="stat-pill">
            <div class="text-white-50 small mb-1">🌱 Donasi</div>
            <div class="fw-bold fs-5 font-mono" style="color:#bfd254;"><?= number_format($stats['donasi']) ?></div>
        </div>
        <div class="stat-pill">
            <div class="text-white-50 small mb-1">🙋 Partisipasi</div>
            <div class="fw-bold fs-5 font-mono" style="color:#60a5fa;"><?= number_format($stats['partisipasi']) ?></div>
        </div>
        <div class="stat-pill">
            <div class="text-white-50 small mb-1">📅 Bulan Ini</div>
            <div class="text-white fw-bold fs-5 font-mono"><?= number_format($stats['bulan_ini']) ?></div>
        </div>
    </div>
</div>

<!-- FILTER BAR -->
<div class="card border-0 shadow-sm rounded-4 mb-4 p-3">
    <form method="GET" action="/admin/sertifikat" class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-muted mb-1">Cari Sertifikat / Nama / Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0"
                       placeholder="CERT-... atau nama donatur..."
                       value="<?= e($q) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Jenis Sertifikat</label>
            <select name="type" class="form-select">
                <option value="">Semua Jenis</option>
                <option value="donasi"      <?= $typeFilter === 'donasi'      ? 'selected' : '' ?>>🌱 Donasi Pohon</option>
                <option value="partisipasi" <?= $typeFilter === 'partisipasi' ? 'selected' : '' ?>>🙋 Partisipasi Relawan</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success fw-semibold flex-fill rounded-3">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <?php if ($q || $typeFilter): ?>
            <a href="/admin/sertifikat" class="btn btn-outline-secondary rounded-3" title="Reset filter">
                <i class="bi bi-x-lg"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- TABEL SERTIFIKAT -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <?php if (!empty($certificates)): ?>
        <div class="table-responsive">
            <table class="table cert-table mb-0">
                <thead class="table-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3">Nomor Sertifikat</th>
                        <th class="py-3">Jenis</th>
                        <th class="py-3">Penerima</th>
                        <th class="py-3">Detail</th>
                        <th class="py-3 text-center">Tanggal Terbit</th>
                        <th class="py-3 text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($certificates as $cert): ?>
                <?php
                    $data    = $cert['data'] ?? [];
                    $isRevoked = !empty($cert['revoked_at']);

                    // Detail baris berdasarkan jenis
                    if ($cert['type'] === 'donasi') {
                        $detail  = ($data['total_trees'] ?? '-') . ' pohon';
                        $detail2 = $data['species_summary'] ?? '';
                        $badgeClass = 'badge-donasi';
                        $badgeLabel = '🌱 Donasi';
                        $certUrl = '/verifikasi-sertifikat/' . $cert['certificate_number'];
                        $linkId  = $cert['certifiable_id'];
                        $linkHref = "/admin/donasi"; // link ke halaman donasi admin
                    } else {
                        $detail  = $data['event_title'] ?? '-';
                        $detail2 = isset($data['event_date']) ? date('d M Y', strtotime($data['event_date'])) : '';
                        $badgeClass = 'badge-partisipasi';
                        $badgeLabel = '🙋 Partisipasi';
                        $certUrl = '/verifikasi-sertifikat/' . $cert['certificate_number'];
                        $linkHref = "/admin/peserta";
                    }
                ?>
                    <tr class="<?= $isRevoked ? 'opacity-50' : '' ?>">
                        <td class="ps-4">
                            <div class="cert-num mb-1">
                                <?= e($cert['certificate_number']) ?>
                            </div>
                            <?php if ($isRevoked): ?>
                            <span class="badge bg-danger-subtle text-danger" style="font-size:.68rem;">DICABUT</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge px-2 py-1 <?= $badgeClass ?>" style="font-size:.75rem;border-radius:6px;">
                                <?= $badgeLabel ?>
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:.88rem;"><?= e($cert['user_name'] ?? '-') ?></div>
                            <div class="text-muted" style="font-size:.76rem;"><?= e($cert['user_email'] ?? '') ?></div>
                        </td>
                        <td>
                            <div style="font-size:.85rem;" class="fw-semibold text-dark"><?= e($detail) ?></div>
                            <?php if ($detail2): ?>
                            <div class="text-muted" style="font-size:.75rem;"><?= e($detail2) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div style="font-size:.82rem;"><?= date('d M Y', strtotime($cert['issued_at'])) ?></div>
                            <div class="text-muted" style="font-size:.74rem;"><?= date('H:i', strtotime($cert['issued_at'])) ?> WIB</div>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                <!-- Lihat sertifikat -->
                                <a href="<?= e($certUrl) ?>" target="_blank"
                                   class="btn btn-sm btn-outline-success rounded-3 py-1 px-2"
                                   title="Lihat sertifikat publik">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <!-- Lihat sumber (donasi/peserta) -->
                                <a href="<?= e($linkHref) ?>" 
                                   class="btn btn-sm btn-outline-secondary rounded-3 py-1 px-2"
                                   title="Lihat data sumber">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <?php if (!$isRevoked): ?>
                                <!-- Cabut sertifikat -->
                                <form method="POST" action="/admin/sertifikat/<?= $cert['id'] ?>/cabut"
                                      onsubmit="return confirm('Yakin cabut sertifikat <?= e($cert['certificate_number']) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 py-1 px-2" title="Cabut sertifikat">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light rounded-bottom-4">
            <small class="text-muted">
                Menampilkan <?= count($certificates) ?> dari <?= number_format($total) ?> sertifikat
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link rounded-2"
                           href="?page=<?= $i ?><?= $q ? '&q='.urlencode($q) : '' ?><?= $typeFilter ? '&type='.$typeFilter : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="fs-1 mb-3">📜</div>
            <h5 class="fw-bold text-dark">Belum Ada Sertifikat</h5>
            <p class="text-muted small mb-4">
                <?= ($q || $typeFilter) ? 'Tidak ada sertifikat yang cocok dengan filter saat ini.' : 'Sertifikat akan muncul di sini setelah donasi berhasil diverifikasi atau relawan di-checkin.' ?>
            </p>
            <?php if ($q || $typeFilter): ?>
            <a href="/admin/sertifikat" class="btn btn-outline-success rounded-pill px-4">Reset Filter</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: Terbitkan Sertifikat Manual -->
<div class="modal fade" id="reissueModal" tabindex="-1" aria-labelledby="reissueModalLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="reissueModalLabel">
                    <i class="bi bi-patch-plus me-2 text-success"></i>Terbitkan Sertifikat Donasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/sertifikat/terbitkan">
                <?= csrf_field() ?>
                <div class="modal-body pt-3">
                    <p class="text-muted small mb-3">
                        Gunakan form ini untuk menerbitkan sertifikat donasi yang belum otomatis terbit.
                        Hanya berlaku untuk donasi berstatus <strong>berhasil</strong>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">ID Donasi</label>
                        <input type="number" name="donation_id" class="form-control"
                               placeholder="Contoh: 42" min="1" required>
                        <div class="form-text">Cek ID donasi di halaman <a href="/admin/donasi" target="_blank">Manajemen Donasi</a>.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold rounded-3 px-4">
                        <i class="bi bi-patch-check me-1"></i>Terbitkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
