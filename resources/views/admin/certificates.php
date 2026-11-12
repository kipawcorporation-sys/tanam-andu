<?php
/**
 * View: admin/certificates.php — Manajemen Sertifikat Digital
 * Platform: Plant For Fun • Kerenceng Coffee Expedition
 */
?>

<style>
/* Hero Header */
.cert-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(6, 21, 16, 0.35);
}
.cert-hero::after {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.2) 0%, transparent 70%);
    pointer-events: none;
}
.btn-export-csv {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #fff;
    padding: 9px 18px;
    border-radius: 12px;
    font-size: .84rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .2s ease;
    text-decoration: none;
}
.btn-export-csv:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
    transform: translateY(-2px);
}
.btn-new-cert {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    border: none;
    padding: 9px 20px;
    border-radius: 12px;
    font-size: .84rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(34, 197, 94, 0.3);
    transition: all .2s ease;
    text-decoration: none;
}
.btn-new-cert:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(34, 197, 94, 0.45);
}

/* Metric Cards */
.metric-box {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    padding: 18px 20px;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform .15s ease;
}
.metric-box:hover {
    transform: translateY(-2px);
}
.metric-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}

/* Filter Card */
.filter-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    padding: 18px 22px;
    margin-bottom: 24px;
}

/* Table Card */
.cert-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}
.table-custom {
    margin-bottom: 0;
}
.table-custom thead th {
    background: #f8fafc;
    padding: 14px 16px;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.table-custom tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background-color .15s ease;
}
.table-custom tbody tr:hover {
    background-color: #f8fafc;
}
.table-custom td {
    padding: 14px 16px;
    vertical-align: middle;
    font-size: .86rem;
}

.badge-donasi {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.badge-partisipasi {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}
.cert-num-badge {
    font-family: 'JetBrains Mono', monospace;
    font-size: .8rem;
    font-weight: 700;
    color: #12382c;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-block;
}
</style>

<!-- 1. HERO BANNER -->
<div class="cert-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 position-relative" style="z-index: 2;">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge rounded-pill font-mono px-3 py-1" style="background: rgba(191,210,84,0.2); color: #bfd254; border: 1px solid rgba(191,210,84,0.3);">
                    <i class="bi bi-patch-check me-1"></i>KERENCENG CERTIFICATION SYSTEM
                </span>
                <span class="badge rounded-pill bg-white bg-opacity-10 text-white font-mono px-3 py-1 border border-white border-opacity-10">
                    QR-VERIFIED DIGITAL CREDENTIALS
                </span>
            </div>
            <h2 class="display-6 fw-bold text-white mb-2 font-brand" style="letter-spacing: -0.5px;">
                Manajemen Sertifikat Digital
            </h2>
            <p class="text-white-50 lead fs-6 mb-0" style="max-width: 680px;">
                Pantau seluruh sertifikat penghargaan donasi pohon & partisipasi relawan ekspedisi. Dilengkapi kode unik dan tautan verifikasi keaslian publik secara real-time.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="/admin/sertifikat/export-csv" class="btn-export-csv">
                <i class="bi bi-filetype-csv"></i> Ekspor CSV
            </a>
            <button type="button" class="btn-new-cert" data-bs-toggle="modal" data-bs-target="#reissueModal">
                <i class="bi bi-plus-circle-fill"></i> Terbitkan Manual
            </button>
        </div>
    </div>
</div>

<!-- 2. FOUR KPI STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="metric-box">
            <div class="metric-icon-wrap" style="background: rgba(18, 56, 44, 0.08); color: #12382c;">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Sertifikat</div>
                <div class="fs-4 fw-bold text-dark font-mono"><?= number_format($stats['total']) ?></div>
                <div class="small text-muted" style="font-size: 0.75rem;">Terbit di database</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="metric-box">
            <div class="metric-icon-wrap" style="background: rgba(34, 197, 94, 0.12); color: #15803d;">
                <i class="bi bi-tree-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Donasi Pohon</div>
                <div class="fs-4 fw-bold text-success font-mono"><?= number_format($stats['donasi']) ?></div>
                <div class="small text-muted" style="font-size: 0.75rem;">Sertifikat Adopsi</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="metric-box">
            <div class="metric-icon-wrap" style="background: rgba(59, 130, 246, 0.12); color: #1d4ed8;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Relawan Ekspedisi</div>
                <div class="fs-4 fw-bold text-primary font-mono"><?= number_format($stats['partisipasi']) ?></div>
                <div class="small text-muted" style="font-size: 0.75rem;">Sertifikat Kehadiran</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="metric-box">
            <div class="metric-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Bulan Ini</div>
                <div class="fs-4 fw-bold text-warning font-mono"><?= number_format($stats['bulan_ini']) ?></div>
                <div class="small text-muted" style="font-size: 0.75rem;">Penerbitan baru</div>
            </div>
        </div>
    </div>
</div>

<!-- 3. FILTER & SEARCH BAR -->
<div class="filter-card">
    <form method="GET" action="/admin/sertifikat" class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small fw-bold text-muted mb-1">Cari Nomor Sertifikat / Nama / Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0"
                       placeholder="Contoh: CERT-202611... atau nama donatur"
                       value="<?= e($q) ?>">
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Kategori Sertifikat</label>
            <select name="type" class="form-select">
                <option value="">Semua Kategori</option>
                <option value="donasi"      <?= $typeFilter === 'donasi'      ? 'selected' : '' ?>>🌱 Donasi Pohon</option>
                <option value="partisipasi" <?= $typeFilter === 'partisipasi' ? 'selected' : '' ?>>🙋 Partisipasi Relawan</option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success fw-bold flex-fill rounded-3">
                <i class="bi bi-funnel me-1"></i> Terapkan Filter
            </button>
            <?php if ($q || $typeFilter): ?>
            <a href="/admin/sertifikat" class="btn btn-outline-secondary rounded-3" title="Reset filter">
                <i class="bi bi-x-lg"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- 4. DATA TABLE -->
<div class="cert-card">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
        <div class="fw-bold text-dark small">
            <i class="bi bi-list-task me-1 text-success"></i>
            Daftar Sertifikat Diterbitkan (<?= number_format($total) ?> data)
        </div>
        <div class="text-muted small">
            Halaman <?= $page ?> dari <?= $totalPages ?>
        </div>
    </div>

    <?php if (!empty($certificates)): ?>
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Nomor Sertifikat</th>
                    <th>Jenis</th>
                    <th>Penerima</th>
                    <th>Ringkasan Kontribusi</th>
                    <th class="text-center">Tanggal Terbit</th>
                    <th class="text-center pe-4" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($certificates as $cert): ?>
            <?php
                $data      = $cert['data'] ?? [];
                $isRevoked = !empty($cert['revoked_at']);

                if ($cert['type'] === 'donasi') {
                    $badgeClass = 'badge-donasi';
                    $badgeLabel = '🌱 Donasi';
                    $titleSummary = ($data['total_trees'] ?? '-') . ' Pohon Tertanam';
                    $subSummary   = $data['species_summary'] ?? '';
                    $verifyUrl    = '/verifikasi-sertifikat/' . $cert['certificate_number'];
                    $sourceUrl    = '/admin/donasi';
                } else {
                    $badgeClass = 'badge-partisipasi';
                    $badgeLabel = '🙋 Relawan';
                    $titleSummary = $data['event_title'] ?? 'Ekspedisi Penanaman';
                    $subSummary   = isset($data['event_date']) ? date('d M Y', strtotime($data['event_date'])) : '';
                    $verifyUrl    = '/verifikasi-sertifikat/' . $cert['certificate_number'];
                    $sourceUrl    = '/admin/peserta';
                }
            ?>
                <tr class="<?= $isRevoked ? 'opacity-50 bg-light' : '' ?>">
                    <td class="ps-4">
                        <div class="cert-num-badge mb-1">
                            <?= e($cert['certificate_number']) ?>
                        </div>
                        <?php if ($isRevoked): ?>
                        <div><span class="badge bg-danger text-white" style="font-size: 0.65rem;">DICABUT</span></div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <span class="badge px-2 py-1 <?= $badgeClass ?>" style="font-size: 0.75rem; border-radius: 6px;">
                            <?= $badgeLabel ?>
                        </span>
                    </td>

                    <td>
                        <div class="fw-bold text-dark"><?= e($cert['user_name'] ?? ($data['recipient_name'] ?? '-')) ?></div>
                        <div class="text-muted small"><?= e($cert['user_email'] ?? '-') ?></div>
                    </td>

                    <td>
                        <div class="fw-semibold text-dark"><?= e($titleSummary) ?></div>
                        <?php if ($subSummary): ?>
                        <div class="text-muted small"><?= e($subSummary) ?></div>
                        <?php endif; ?>
                    </td>

                    <td class="text-center">
                        <div class="fw-semibold text-dark"><?= date('d M Y', strtotime($cert['issued_at'])) ?></div>
                        <div class="text-muted small"><?= date('H:i', strtotime($cert['issued_at'])) ?> WIB</div>
                    </td>

                    <td class="text-center pe-4">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <!-- Tombol Verifikasi Publik -->
                            <a href="<?= e($verifyUrl) ?>" target="_blank"
                               class="btn btn-sm btn-outline-success rounded-3 px-2 py-1"
                               title="Lihat Sertifikat Publik (QR-Verified)">
                                <i class="bi bi-eye"></i>
                            </a>

                            <!-- Tombol Sumber Donasi/Peserta -->
                            <a href="<?= e($sourceUrl) ?>"
                               class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1"
                               title="Buka Data Sumber">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>

                            <?php if (!$isRevoked): ?>
                            <!-- Tombol Cabut -->
                            <form method="POST" action="/admin/sertifikat/<?= $cert['id'] ?>/cabut"
                                  onsubmit="return confirm('Apakah Anda yakin ingin mencabut sertifikat <?= e($cert['certificate_number']) ?>?')"
                                  class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Cabut Sertifikat">
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

    <!-- Pagination Footer -->
    <?php if ($totalPages > 1): ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center px-4 py-3 border-top bg-light">
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
        <div class="display-4 text-muted mb-3">📜</div>
        <h5 class="fw-bold text-dark">Belum Ada Sertifikat Ditemukan</h5>
        <p class="text-muted small mb-4" style="max-width: 460px; margin: 0 auto;">
            <?= ($q || $typeFilter)
                ? 'Tidak ada data sertifikat yang cocok dengan kata kunci pencarian atau filter yang dipilih.'
                : 'Sertifikat digital akan otomatis dibuat ketika donasi disetujui atau relawan melakukan check-in kegiatan.' ?>
        </p>
        <?php if ($q || $typeFilter): ?>
        <a href="/admin/sertifikat" class="btn btn-outline-success rounded-pill px-4">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian
        </a>
        <?php else: ?>
        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#reissueModal">
            <i class="bi bi-plus-circle me-1"></i> Terbitkan Sertifikat Manual
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- 5. MODAL: Terbitkan Sertifikat Manual (Donasi & Relawan) -->
<div class="modal fade" id="reissueModal" tabindex="-1" aria-labelledby="reissueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold" id="reissueModalLabel">
                    <i class="bi bi-patch-plus-fill me-2 text-success"></i>Terbitkan Sertifikat Manual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Tab Navigasi Jenis Sertifikat -->
            <ul class="nav nav-pills nav-fill px-3 pt-2 pb-0 gap-2" id="certTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold rounded-3 small py-2" id="donasi-tab" data-bs-toggle="pill" data-bs-target="#tab-donasi" type="button" role="tab">
                        🌱 Donasi Pohon
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold rounded-3 small py-2" id="relawan-tab" data-bs-toggle="pill" data-bs-target="#tab-relawan" type="button" role="tab">
                        🙋 Relawan Ekspedisi
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="certTabContent">
                <!-- FORM 1: SERTIFIKAT DONASI -->
                <div class="tab-pane fade show active" id="tab-donasi" role="tabpanel">
                    <form method="POST" action="/admin/sertifikat/terbitkan">
                        <?= csrf_field() ?>
                        <div class="modal-body pt-3">
                            <div class="p-3 bg-light rounded-3 small text-muted mb-3 border">
                                <i class="bi bi-info-circle text-success me-1"></i>
                                Sertifikat donasi diterbitkan dengan rincian spesies pohon, estimasi serapan CO2, dan QR verifikasi resmi. Hanya berlaku untuk donasi berstatus <strong>berhasil</strong>.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">ID Transaksi Donasi</label>
                                <input type="number" name="donation_id" class="form-control"
                                       placeholder="Contoh: 1, 2, atau 10" min="1" required>
                                <div class="form-text">
                                    Cek ID transaksi donasi di menu <a href="/admin/donasi" target="_blank" class="fw-semibold">Manajemen Donasi</a>.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success fw-bold rounded-3 px-4">
                                <i class="bi bi-patch-check-fill me-1"></i> Terbitkan Sertifikat Donasi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- FORM 2: SERTIFIKAT RELAWAN -->
                <div class="tab-pane fade" id="tab-relawan" role="tabpanel">
                    <form method="POST" action="/admin/sertifikat/terbitkan-relawan">
                        <?= csrf_field() ?>
                        <div class="modal-body pt-3">
                            <div class="p-3 bg-light rounded-3 small text-muted mb-3 border">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                Sertifikat relawan diterbitkan atas partisipasi aksi lapangan ekspedisi penanaman pohon. Jika status peserta belum hadir, sistem akan otomatis menandainya sebagai <strong>hadir</strong>.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">ID Pendaftaran Peserta Relawan</label>
                                <input type="number" name="participant_id" class="form-control"
                                       placeholder="Contoh: 1, 5, atau 12" min="1" required>
                                <div class="form-text">
                                    Cek ID peserta relawan di menu <a href="/admin/peserta" target="_blank" class="fw-semibold">Manajemen Peserta</a>.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4">
                                <i class="bi bi-patch-check-fill me-1"></i> Terbitkan Sertifikat Relawan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
