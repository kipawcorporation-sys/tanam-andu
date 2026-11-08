<?php
/**
 * View: admin/donations.php — Manajemen & Analisis Donasi Pohon Nusantara
 * Platform: Plant For Fun • Kerenceng Coffee Expedition
 */

$statusOptions = [
    'berhasil'            => ['Berhasil',           'badge-berhasil', 'bi-check-circle-fill'],
    'menunggu_pembayaran' => ['Menunggu Bayar',     'badge-menunggu', 'bi-clock-history'],
    'menunggu_verifikasi' => ['Perlu Verifikasi',   'badge-verifikasi', 'bi-hourglass-split'],
    'pending'             => ['Pending',            'badge-pending',  'bi-pause-circle'],
    'gagal'               => ['Gagal',              'badge-gagal',    'bi-x-circle-fill'],
    'dibatalkan'          => ['Dibatalkan',         'badge-batal',    'bi-slash-circle-fill'],
];
?>

<style>
/* Hero Header */
.donation-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(6, 21, 16, 0.35);
}
.donation-hero::after {
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
.btn-new-donation {
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
.btn-new-donation:hover {
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
.donations-card {
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
    transition: background .15s ease;
}
.table-custom tbody tr:hover {
    background: #f8fafc;
}
.table-custom td {
    padding: 14px 16px;
    vertical-align: middle;
    font-size: .84rem;
    color: #334155;
}

/* Status Badges */
.badge-berhasil {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.badge-menunggu {
    background: #fef9c3;
    color: #a16207;
    border: 1px solid #fde047;
}
.badge-verifikasi {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.badge-pending {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.badge-gagal {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
.badge-batal {
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #e5e7eb;
}

/* Pagination */
.pagination-custom .page-link {
    border-radius: 8px;
    margin: 0 3px;
    color: #12382c;
    border: 1px solid #e2e8f0;
    font-size: .82rem;
    font-weight: 600;
}
.pagination-custom .page-item.active .page-link {
    background: #12382c;
    border-color: #12382c;
    color: #fff;
}
</style>

<!-- Hero Section -->
<div class="donation-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-3 py-1 rounded-pill small">
                    <i class="bi bi-tree-fill me-1"></i>Konservasi Agroforestry Kerenceng
                </span>
                <span class="badge px-3 py-1 rounded-pill small" style="background: rgba(245, 158, 11, 0.2); color: #fde68a; border: 1px solid rgba(245, 158, 11, 0.5);">
                    <i class="bi bi-patch-check-fill me-1"></i>Sertifikat Terverifikasi
                </span>
            </div>
            <h4 class="text-white fw-bold mb-1 fs-3">
                <i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Manajemen & Analisis Donasi Pohon
            </h4>
            <p class="text-white-50 mb-0 small" style="max-width: 700px;">
                Pantau seluruh kontribusi adopsi bibit pohon, verifikasi status pembayaran transfer, terbitkan sertifikat digital, dan kelola hubungan personal dengan para donatur.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/admin/donasi/export?<?= http_build_query($filters) ?>" class="btn-export-csv" title="Unduh rekap data sesuai filter aktif">
                <i class="bi bi-file-earmark-excel"></i> Ekspor CSV
            </a>
            <a href="/donasi" target="_blank" class="btn-new-donation">
                <i class="bi bi-plus-circle-fill"></i> Halaman Donasi Publik
            </a>
        </div>
    </div>
</div>

<!-- 5 KPI Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4 col-xl">
        <div class="metric-box">
            <div class="metric-icon-wrap bg-success-subtle" style="color:#0a3622;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Dana Terkumpul</div>
                <div class="fs-5 fw-bold font-monospace" style="color:#15803d;">Rp <?= number_format($summary['total_amount'], 0, ',', '.') ?></div>
                <div class="small text-muted" style="font-size: .72rem;"><?= (int)$summary['verified_donations'] ?> donasi terverifikasi</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4 col-xl">
        <div class="metric-box">
            <div class="metric-icon-wrap bg-primary-subtle text-primary">
                <i class="bi bi-tree-fill"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Pohon Konservasi</div>
                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($summary['total_trees']) ?> <small class="text-muted fs-6 fw-normal">Batang</small></div>
                <div class="small text-muted" style="font-size: .72rem;">Ditanam & dirawat bersama petani</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4 col-xl">
        <div class="metric-box">
            <div class="metric-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-cloud-check-fill"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Estimasi Serap CO₂</div>
                <div class="fs-5 fw-bold font-monospace" style="color: #0369a1;"><?= number_format($summary['total_co2_kg'], 1) ?> <small class="text-muted fs-6 fw-normal">kg/thn</small></div>
                <div class="small text-muted" style="font-size: .72rem;">Kapasitas reduksi jejak karbon</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-6 col-xl">
        <div class="metric-box">
            <div class="metric-icon-wrap" style="background: #fef3c7; color: #b45309;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Keluarga Donatur</div>
                <div class="fs-4 fw-bold text-dark font-monospace"><?= number_format($summary['unique_donors']) ?> <small class="text-muted fs-6 fw-normal">Orang</small></div>
                <div class="small text-muted" style="font-size: .72rem;"><?= (int)$summary['gift_donations'] ?> Hadiah &bull; <?= (int)$summary['anon_donations'] ?> Anonim</div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl">
        <div class="metric-box">
            <div class="metric-icon-wrap bg-danger-subtle text-danger">
                <i class="bi bi-hourglass-bottom"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Menunggu Bayar</div>
                <div class="fs-4 fw-bold text-danger font-monospace"><?= (int)$summary['pending_donations'] ?> <small class="text-muted fs-6 fw-normal">Tagihan</small></div>
                <div class="small text-muted" style="font-size: .72rem;">Pending transfer/konfirmasi</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Multi-Filter Card -->
<div class="filter-card">
    <form action="/admin/donasi" method="GET" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Nama, email, telepon, invoice..." value="<?= e($filters['q']) ?>">
            </div>
        </div>

        <!-- Filter Status -->
        <div class="col-sm-6 col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <?php foreach ($statusOptions as $k => [$lbl, $cls, $icn]): ?>
                    <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filter Tipe Donasi -->
        <div class="col-sm-6 col-md-2">
            <select name="type" class="form-select form-select-sm">
                <option value="">Semua Tipe Donasi</option>
                <option value="regular" <?= $filters['type'] === 'regular' ? 'selected' : '' ?>>Pribadi (Reguler)</option>
                <option value="gift" <?= $filters['type'] === 'gift' ? 'selected' : '' ?>>Hadiah (Gift 🎁)</option>
                <option value="anon" <?= $filters['type'] === 'anon' ? 'selected' : '' ?>>Hamba Allah (Anonim)</option>
            </select>
        </div>

        <!-- Filter Spesies Pohon -->
        <div class="col-sm-6 col-md-2">
            <select name="species_id" class="form-select form-select-sm">
                <option value="">Semua Jenis Pohon</option>
                <?php foreach ($speciesList as $sp): ?>
                    <option value="<?= $sp['id'] ?>" <?= $filters['species_id'] == $sp['id'] ? 'selected' : '' ?>><?= e($sp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filter Periode -->
        <div class="col-sm-6 col-md-2">
            <select name="period" class="form-select form-select-sm">
                <option value="">Semua Periode</option>
                <option value="today" <?= $filters['period'] === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                <option value="week" <?= $filters['period'] === 'week' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                <option value="month" <?= $filters['period'] === 'month' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                <option value="year" <?= $filters['period'] === 'year' ? 'selected' : '' ?>>Tahun Ini</option>
            </select>
        </div>

        <!-- Tombol Aksi Filter -->
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-success fw-semibold w-100" title="Terapkan Filter">
                <i class="bi bi-funnel"></i>
            </button>
            <?php if (!empty(array_filter($filters))): ?>
                <a href="/admin/donasi" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="donations-card">
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light bg-opacity-50">
        <div class="fw-bold text-dark fs-6">
            <i class="bi bi-list-columns-reverse me-2 text-success"></i>Daftar Transaksi Donasi
            <span class="badge bg-secondary rounded-pill ms-2 fw-normal"><?= number_format($total) ?> donasi ditemukan</span>
        </div>
        <div class="small text-muted">
            Halaman <?= $page ?> dari <?= max(1, $totalPages) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>ID & Invoice</th>
                    <th>Donatur & Kontak</th>
                    <th>Bibit Pohon & Dampak</th>
                    <th>Total Donasi</th>
                    <th>Status</th>
                    <th>Sertifikat</th>
                    <th>Tanggal</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($donations)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-heartbreak text-muted" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold mt-3 mb-1 text-dark">Tidak Ada Data Donasi Ditemukan</h6>
                                <p class="text-muted small">Coba ubah kata kunci pencarian atau sesuaikan opsi filter Anda.</p>
                                <a href="/admin/donasi" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    Reset Semua Filter
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($donations as $d): ?>
                    <?php
                        [$stLabel, $stClass, $stIcon] = $statusOptions[$d['status']] ?? [$d['status'], 'badge-pending', 'bi-dot'];
                        $donorInitials = strtoupper(substr($d['donor_name'], 0, 2));
                    ?>
                    <tr>
                        <!-- ID & Invoice -->
                        <td>
                            <div class="fw-bold text-dark">#<?= $d['id'] ?></div>
                            <?php if (!empty($d['invoice_no'])): ?>
                                <a href="/pembayaran/<?= e($d['invoice_no']) ?>" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace text-decoration-none" title="Lihat halaman pembayaran publik">
                                    <?= e($d['invoice_no']) ?>
                                </a>
                            <?php else: ?>
                                <span class="small text-muted">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Donatur & Kontak -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold small flex-shrink-0"
                                     style="width: 34px; height: 34px; background: linear-gradient(135deg, #12382c, #16a34a); font-size: 11px;">
                                    <?= $donorInitials ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">
                                        <?= e($d['donor_name']) ?>
                                        <?php if (!empty($d['is_anonymous'])): ?>
                                            <span class="badge bg-secondary text-white" style="font-size: 9px;">Anonim</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted" style="font-size: .75rem;">
                                        <?= e($d['donor_email']) ?>
                                        <?php if (!empty($d['donor_phone'])): ?>
                                            &bull; <?= e($d['donor_phone']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($d['is_gift'])): ?>
                                        <div class="small text-danger fw-semibold" style="font-size: .73rem;">
                                            <i class="bi bi-gift-fill me-1"></i>Hadiah untuk: <?= e($d['gift_recipient_name'] ?: '-') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <!-- Bibit Pohon & Dampak -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success bg-opacity-15 border border-success border-opacity-25 px-2 py-1 fw-bold" style="color:#064e1e;">
                                    <i class="bi bi-tree-fill me-1"></i><?= (int)$d['total_trees'] ?> Batang
                                </span>
                                <span class="badge px-2 py-1 small" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;" title="Estimasi serapan karbon">
                                    <i class="bi bi-cloud me-1"></i><?= number_format($d['total_co2_kg'], 1) ?> kg CO₂/th
                                </span>
                            </div>
                            <div class="small text-muted mt-1 text-truncate" style="max-width: 200px;" title="<?= e($d['species_summary'] ?? '-') ?>">
                                <?= e($d['species_summary'] ?? '-') ?>
                            </div>
                        </td>

                        <!-- Total Donasi -->
                        <td>
                            <div class="fw-bold font-monospace fs-6" style="color:#15803d;">
                                Rp <?= number_format($d['total_amount'], 0, ',', '.') ?>
                            </div>
                            <?php if (!empty($d['unique_code'])): ?>
                                <div class="small text-muted" style="font-size: .72rem;">
                                    Kode Unik: <strong style="color: #b45309;">+<?= $d['unique_code'] ?></strong>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td>
                            <span class="badge <?= $stClass ?> px-2 py-1 rounded-pill small">
                                <i class="bi <?= $stIcon ?> me-1"></i><?= $stLabel ?>
                            </span>
                        </td>

                        <!-- Sertifikat -->
                        <td>
                            <?php if (!empty($d['certificate_number'])): ?>
                                <a href="/verifikasi-sertifikat/<?= e($d['certificate_number']) ?>" target="_blank"
                                   class="badge bg-success bg-opacity-10 border border-success border-opacity-25 font-monospace text-decoration-none" style="color:#064e1e;"
                                   title="Verifikasi keaslian sertifikat">
                                    <i class="bi bi-patch-check-fill me-1"></i><?= e($d['certificate_number']) ?>
                                </a>
                            <?php else: ?>
                                <span class="small text-muted fst-italic">Belum Terbit</span>
                            <?php endif; ?>
                        </td>

                        <!-- Tanggal -->
                        <td>
                            <div class="small text-dark"><?= date('d/m/Y', strtotime($d['created_at'])) ?></div>
                            <div class="small text-muted" style="font-size: .72rem;"><?= date('H:i', strtotime($d['created_at'])) ?> WIB</div>
                        </td>

                        <!-- Aksi -->
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success fw-semibold" onclick="openDetailModal(<?= $d['id'] ?>)" title="Lihat detail lengkap donasi">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item small" href="javascript:void(0)" onclick="openDetailModal(<?= $d['id'] ?>)">
                                            <i class="bi bi-card-text me-2 text-primary"></i>Rincian Donasi Lengkap
                                        </a>
                                    </li>
                                    <?php if (!empty($d['certificate_number'])): ?>
                                        <li>
                                            <a class="dropdown-item small" href="/verifikasi-sertifikat/<?= e($d['certificate_number']) ?>" target="_blank">
                                                <i class="bi bi-patch-check me-2 text-success"></i>Buka Sertifikat Digital
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><span class="dropdown-header small text-uppercase">Ubah Status Cepat</span></li>
                                    <?php if ($d['status'] !== 'berhasil'): ?>
                                        <li>
                                            <form action="/admin/donasi/<?= $d['id'] ?>/update-status" method="POST" onsubmit="return confirm('Ubah status donasi #<?= $d['id'] ?> menjadi BERHASIL dan terbitkan sertifikat?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="status" value="berhasil">
                                                <button type="submit" class="dropdown-item small text-success">
                                                    <i class="bi bi-check-circle me-2"></i>Setujui / Berhasil
                                                </button>
                                            </form>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($d['status'] !== 'dibatalkan'): ?>
                                        <li>
                                            <form action="/admin/donasi/<?= $d['id'] ?>/update-status" method="POST" onsubmit="return confirm('Batalkan donasi #<?= $d['id'] ?>?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="status" value="dibatalkan">
                                                <button type="submit" class="dropdown-item small text-danger">
                                                    <i class="bi bi-slash-circle me-2"></i>Batalkan Donasi
                                                </button>
                                            </form>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex align-items-center justify-content-between p-3 border-top">
            <span class="small text-muted">
                Menampilkan data ke <?= ($offset + 1) ?> - <?= min($offset + $perPage, $total) ?> dari total <?= $total ?>
            </span>
            <nav>
                <ul class="pagination pagination-sm pagination-custom mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- ======================================================================= -->
<!-- MODAL DETAIL & INSPEKTUR DONASI LENGKAP                                 -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalDetailDonasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #061510, #12382c); padding: 20px 28px;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="detailModalTitle">
                        <i class="bi bi-receipt me-2 text-success"></i>Rincian Donasi Pohon #<span id="dtId"></span>
                    </h5>
                    <div class="small text-white-50" id="dtInvoiceHeader"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div id="modalLoading" class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <div class="small text-muted mt-2">Memuat data rincian donasi...</div>
                </div>

                <div id="modalContent" style="display: none;">
                    <!-- Profil Donatur & Hadiah -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="small text-muted text-uppercase fw-bold mb-2">
                                    <i class="bi bi-person-fill text-success me-1"></i>Data Donatur
                                </div>
                                <div class="fs-6 fw-bold text-dark" id="dtDonorName"></div>
                                <div class="small text-secondary" id="dtDonorEmail"></div>
                                <div class="small text-secondary" id="dtDonorPhone"></div>
                                <div class="small mt-2 pt-2 border-top text-muted" id="dtDonorMessage"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="small text-muted text-uppercase fw-bold mb-2">
                                    <i class="bi bi-gift-fill text-danger me-1"></i>Hadiah / Persembahan (Gift)
                                </div>
                                <div id="dtGiftSection"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Item Pohon -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-tree text-success me-1"></i>Rincian Bibit Pohon Konservasi
                        </h6>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-sm table-borderless mb-0">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th class="ps-3 py-2 small fw-bold">Jenis Pohon</th>
                                        <th class="text-center py-2 small fw-bold">Jumlah</th>
                                        <th class="text-end py-2 small fw-bold">Harga Satuan</th>
                                        <th class="text-end pe-3 py-2 small fw-bold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="dtItemsList"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Status Pembayaran & Sertifikat -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="small text-muted fw-bold text-uppercase mb-1">Status Transaksi</div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-5 fw-bold text-success font-monospace" id="dtTotalAmount"></span>
                                    <span class="badge px-2 py-1 rounded-pill" id="dtStatusBadge"></span>
                                </div>
                                <div class="small text-muted mt-1" id="dtPaymentMeta"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="small text-muted fw-bold text-uppercase mb-1">Sertifikat Digital</div>
                                <div id="dtCertificateSection"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pratinjau Bukti Transfer jika ada -->
                    <div id="dtProofSection" class="p-3 bg-white border rounded-3 mb-3" style="display: none;">
                        <div class="small text-muted fw-bold text-uppercase mb-2">
                            <i class="bi bi-image text-primary me-1"></i>Bukti Transfer Terlampir
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="small text-muted" id="dtProofSender"></div>
                            <a id="dtProofLink" href="" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka Foto Bukti Transfer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                <div>
                    <a id="dtWaBtn" href="" target="_blank" class="btn btn-success fw-bold px-3">
                        <i class="bi bi-whatsapp me-1"></i> Hubungi WhatsApp Donatur
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openDetailModal(donationId) {
    const modalEl = document.getElementById('modalDetailDonasi');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('modalContent').style.display = 'none';
    document.getElementById('dtId').textContent = donationId;

    fetch('/admin/donasi/' + donationId + '/detail-json')
        .then(response => response.json())
        .then(data => {
            const d = data.donation;
            const items = data.items;
            const proof = data.proof;

            document.getElementById('dtInvoiceHeader').textContent = d.invoice_no ? ('Invoice: ' + d.invoice_no) : 'Donasi Langsung';
            document.getElementById('dtDonorName').textContent = d.donor_name + (d.is_anonymous == '1' ? ' (Hamba Allah - Anonim)' : '');
            document.getElementById('dtDonorEmail').textContent = d.donor_email || '-';
            document.getElementById('dtDonorPhone').textContent = d.donor_phone ? ('WhatsApp: ' + d.donor_phone) : '-';
            document.getElementById('dtDonorMessage').textContent = d.donor_message ? ('Pesan/Doa: "' + d.donor_message + '"') : 'Tidak ada pesan khusus dari donatur.';

            // Section Hadiah
            const giftSec = document.getElementById('dtGiftSection');
            if (d.is_gift == '1') {
                giftSec.innerHTML = `
                    <div class="fw-bold text-dark">Penerima: ${escapeHtml(d.gift_recipient_name || '-')}</div>
                    <div class="small text-muted mt-1 fst-italic">"${escapeHtml(d.gift_message || 'Selamat atas donasi pohon ini!')}"</div>
                `;
            } else {
                giftSec.innerHTML = '<div class="small text-muted">Bukan donasi hadiah (Donasi reguler pribadi).</div>';
            }

            // Section Items
            const itemsList = document.getElementById('dtItemsList');
            itemsList.innerHTML = '';
            items.forEach(it => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="ps-3 py-2">
                        <div class="fw-semibold text-dark">${escapeHtml(it.species_name)}</div>
                        <div class="small text-muted" style="font-size: 11px;">Serap CO₂: ${Number(it.co2_absorption_kg_per_year).toFixed(1)} kg/thn</div>
                    </td>
                    <td class="text-center py-2 fw-bold">${it.quantity} Batang</td>
                    <td class="text-end py-2 font-monospace">Rp ${Number(it.price_per_tree).toLocaleString('id-ID')}</td>
                    <td class="text-end pe-3 py-2 font-monospace fw-bold text-success">Rp ${Number(it.subtotal).toLocaleString('id-ID')}</td>
                `;
                itemsList.appendChild(tr);
            });

            // Status & Total
            document.getElementById('dtTotalAmount').textContent = 'Rp ' + Number(d.total_amount).toLocaleString('id-ID');
            const stBadge = document.getElementById('dtStatusBadge');
            stBadge.textContent = d.status.toUpperCase();
            stBadge.className = 'badge px-2 py-1 rounded-pill ' + (d.status === 'berhasil' ? 'bg-success' : 'bg-warning text-dark');
            document.getElementById('dtPaymentMeta').textContent = `Dibuat pada: ${d.created_at}`;

            // Certificate
            const certSec = document.getElementById('dtCertificateSection');
            if (d.certificate_number) {
                certSec.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-25 border border-success font-monospace px-2 py-1" style="color:#064e1e;">
                            ${escapeHtml(d.certificate_number)}
                        </span>
                        <a href="/verifikasi-sertifikat/${escapeHtml(d.certificate_number)}" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Buka
                        </a>
                    </div>
                `;
            } else {
                certSec.innerHTML = '<span class="small text-muted fst-italic">Sertifikat akan terbit otomatis saat donasi berhasil.</span>';
            }

            // Proof
            const proofSec = document.getElementById('dtProofSection');
            if (proof && proof.proof_url) {
                proofSec.style.display = 'block';
                document.getElementById('dtProofSender').innerHTML = `Pengirim: <strong>${escapeHtml(proof.sender_name || '-')}</strong> (${escapeHtml(proof.sender_bank || '-')}) &bull; Nominal: <strong>Rp ${Number(proof.amount_transferred || 0).toLocaleString('id-ID')}</strong>`;
                document.getElementById('dtProofLink').href = proof.proof_url;
            } else {
                proofSec.style.display = 'none';
            }

            // WhatsApp link
            const waBtn = document.getElementById('dtWaBtn');
            if (data.wa_url) {
                waBtn.href = data.wa_url;
                waBtn.style.display = 'inline-flex';
            } else {
                waBtn.style.display = 'none';
            }

            document.getElementById('modalLoading').style.display = 'none';
            document.getElementById('modalContent').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('modalLoading').innerHTML = '<div class="alert alert-danger py-2 px-3 small">Gagal memuat rincian donasi: ' + err + '</div>';
        });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>"']/g, function(m) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m];
    });
}
</script>
