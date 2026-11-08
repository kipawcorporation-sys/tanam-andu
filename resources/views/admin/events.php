<?php
/**
 * View: admin/events.php — Manajemen Lengkap Kegiatan & Ekspedisi Penanaman Pohon.
 */
$statusOptions = [
    'upcoming'  => 'Akan Datang',
    'ongoing'   => 'Sedang Berjalan',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];
$statusColors = [
    'upcoming'  => ['bg-info-subtle text-primary border border-primary-subtle', 'bi-calendar-event'],
    'ongoing'   => ['bg-success-subtle border border-success-subtle" style="color:#0a3622;', 'bi-play-circle-fill'],
    'completed' => ['bg-secondary-subtle text-secondary border', 'bi-check-circle'],
    'cancelled' => ['bg-danger-subtle text-danger border border-danger-subtle', 'bi-x-circle'],
];
$difficultyBadges = [
    'mudah'     => ['bg-success-subtle', '#0a3622', 'bi-speedometer2', 'Mudah (Pemula)'],
    'sedang'    => ['bg-warning-subtle', '#92400e', 'bi-speedometer', 'Sedang (Trek Ringan)'],
    'menantang' => ['bg-danger-subtle', '#991b1b', 'bi-fire', 'Menantang (Gunung)'],
];
$offset = $offset ?? (((($page ?? 1) - 1) * ($perPage ?? 15)));
?>

<!-- Leaflet CSS for Admin Interactive Map Picker -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 26px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:220px; height:220px; background:radial-gradient(circle,rgba(34,197,94,.15) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.35rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.65); font-size:.85rem; margin:0; }
.btn-new-event { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; padding:9px 20px; border-radius:10px; font-size:.84rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:.2s; text-decoration:none; }
.btn-new-event:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(34,197,94,.35); color:#fff; }

/* 5 KPI Metric Boxes */
.kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.kpi-card { background: #fff; border-radius: 14px; padding: 18px 20px; border: 1px solid rgba(18,56,44,0.08); box-shadow: 0 2px 10px rgba(18,56,44,0.03); display: flex; align-items: center; gap: 16px; }
.kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.kpi-val { font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 2px; }
.kpi-lbl { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

/* Filter Bar */
.filter-bar { background:#fff; border-radius:14px; padding:16px 20px; margin-bottom:20px; border:1px solid rgba(0,0,0,.06); box-shadow:0 1px 4px rgba(0,0,0,.04); }

/* Table Container */
.pf-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
.pf-card-header { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); padding:16px 24px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.pf-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.9rem; }
table.pf-table { width:100%; border-collapse:collapse; }
table.pf-table thead th { background:#f8fafc; padding:12px 16px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
table.pf-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
table.pf-table tbody tr:hover { background:#f8fafc; }
table.pf-table td { padding:14px 16px; vertical-align:middle; font-size:.84rem; color:#374151; }

.event-title { font-weight:700; color:#0f172a; text-decoration:none; display:block; font-size:0.92rem; }
.event-title:hover { color:#16a34a; }
.category-pill { background:#ecfdf5; color:#047857; font-size:0.7rem; font-weight:700; padding:3px 9px; border-radius:6px; display:inline-block; border:1px solid #a7f3d0; }
.elev-pill { background:#f0fdf4; color:#15803d; font-size:0.7rem; font-weight:700; padding:2px 7px; border-radius:50px; font-family:'JetBrains Mono',monospace; }

.quota-progress { height: 6px; border-radius: 6px; background: #e2e8f0; overflow: hidden; margin-top: 4px; }
.quota-progress-bar { height: 100%; border-radius: 6px; }

.pf-pagination { display:flex; gap:4px; justify-content:center; padding:16px 24px; }
.pf-pagination a { width:34px; height:34px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:.82rem; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; color:#374151; transition:.15s; }
.pf-pagination a:hover { background:#f1f5f9; }
.pf-pagination .active-page { background:#22c55e; color:#fff; border-color:#22c55e; }

/* Modal Ultra-Pro Styling & Smooth Scrolling Fix */
.modal-dialog-scrollable {
    max-height: calc(100% - 2.5rem);
}
.modal-content { 
    border-radius: 22px; 
    border: none; 
    box-shadow: 0 24px 70px rgba(0,0,0,.25); 
    overflow: hidden;
    max-height: calc(100vh - 2.5rem);
    display: flex;
    flex-direction: column;
}
.modal-form-wrap {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: calc(100vh - 2.5rem);
    min-height: 0;
    overflow: hidden;
    margin: 0;
}
.modal-header-pro { 
    background: linear-gradient(135deg, #061510 0%, #0d2b20 50%, #12382c 100%); 
    padding: 20px 28px; 
    border: none; 
    color: #fff; 
    position: relative;
    flex-shrink: 0;
}
.modal-header-pro .btn-close { filter: invert(1); }
.modal-nav-tabs { 
    background: #f1f5f9; 
    padding: 5px; 
    border-radius: 12px; 
    border: 1px solid #e2e8f0; 
    display: flex; 
    gap: 4px; 
    margin-bottom: 20px;
    flex-shrink: 0;
}
.modal-nav-tabs .nav-link { 
    border-radius: 8px; 
    font-size: 0.8rem; 
    font-weight: 700; 
    color: #64748b; 
    padding: 8px 12px; 
    border: none; 
    transition: all 0.2s; 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    flex: 1; 
    justify-content: center; 
    white-space: nowrap; 
}
.modal-nav-tabs .nav-link:hover { color: #0f172a; background: #e2e8f0; }
.modal-nav-tabs .nav-link.active { background: #12382c; color: #fff; box-shadow: 0 2px 8px rgba(18,56,44,0.18); }
.modal-body-pro { 
    padding: 24px 28px; 
    background: #fff; 
    overflow-y: auto !important;
    overflow-x: hidden;
    flex: 1 1 auto;
    min-height: 0;
    max-height: calc(100vh - 210px);
    -webkit-overflow-scrolling: touch;
}
.modal-body-pro::-webkit-scrollbar {
    width: 8px;
}
.modal-body-pro::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.modal-body-pro::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 4px;
}
.modal-body-pro::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
.modal-footer {
    flex-shrink: 0;
}
.form-section-title { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #12382c; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.form-section-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
.btn-preset-pill { border: 1px solid #cbd5e1; background: #f8fafc; font-size: 0.72rem; font-weight: 600; color: #334155; padding: 3px 10px; border-radius: 50px; cursor: pointer; transition: all 0.15s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-preset-pill:hover { background: #12382c; color: #fff; border-color: #12382c; }
.form-control:focus, .form-select:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.14); }

/* Rundown Visual Builder Cards & Action Buttons */
.rundown-builder-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px;
}
.rundown-row-card {
    transition: all 0.15s ease;
    border: 1px solid #e2e8f0;
}
.rundown-row-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.rundown-row-card .btn-row-up, .rundown-row-card .btn-row-down {
    border-color: #e2e8f0;
    color: #64748b;
    background: #f8fafc;
}
.rundown-row-card .btn-row-up:hover, .rundown-row-card .btn-row-down:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.rundown-row-card .btn-row-remove {
    border-color: #fee2e2;
    color: #ef4444;
    background: #fff;
}
.rundown-row-card .btn-row-remove:hover {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
}
</style>

<!-- Page Hero -->
<div class="page-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-compass me-2 text-success"></i>Manajemen Jadwal & Ekspedisi Penanaman</h4>
            <p>Kelola jadwal kegiatan aksi relawan, kuota peserta, koordinasi lapangan, dan lembar presensi.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/checkin-qr" class="btn btn-outline-light btn-sm rounded-3 fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-qr-code-scan"></i> Scanner QR Check-in
            </a>
            <button class="btn-new-event" data-bs-toggle="modal" data-bs-target="#modalTambahEvent">
                <i class="bi bi-plus-lg"></i> Ekspedisi Baru
            </button>
        </div>
    </div>
</div>

<!-- 5 KPI Metrics Row -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
            <i class="bi bi-calendar2-range"></i>
        </div>
        <div>
            <div class="kpi-val"><?= number_format($kpi['total_events'] ?? 0) ?></div>
            <div class="kpi-lbl">Total Ekspedisi</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <div class="kpi-val"><?= number_format($kpi['upcoming_events'] ?? 0) ?></div>
            <div class="kpi-lbl">Akan Datang</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <div class="kpi-val"><?= number_format($kpi['total_registered'] ?? 0) ?></div>
            <div class="kpi-lbl">Relawan Terdaftar</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f1f5f9; color:#475569;">
            <i class="bi bi-ticket-detailed"></i>
        </div>
        <div>
            <div class="kpi-val"><?= number_format($kpi['total_quota'] ?? 0) ?></div>
            <div class="kpi-lbl">Kapasitas Kursi</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ede9fe; color:#6d28d9;">
            <i class="bi bi-pie-chart-fill"></i>
        </div>
        <div>
            <div class="kpi-val"><?= $occupancyRate ?>%</div>
            <div class="kpi-lbl">Okupansi Kuota</div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <form class="row g-2 align-items-center" method="GET">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" class="form-control border-start-0" placeholder="Cari judul kegiatan, lokasi, titik kumpul...">
            </div>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status (<?= $total ?>)</option>
                <?php foreach ($statusOptions as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $statusFilter === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kategori Ekspedisi</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">Filter</button>
            <?php if (!empty($statusFilter) || !empty($categoryFilter) || !empty($searchQuery)): ?>
                <a href="/admin/kegiatan" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="pf-card">
    <div class="pf-card-header">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0"><i class="bi bi-compass me-2 text-success"></i>Daftar Ekspedisi Penanaman</h6>
            <span class="badge bg-light text-dark border font-monospace"><?= number_format($total) ?> kegiatan</span>
        </div>
        <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
    </div>

    <div class="table-responsive">
        <table class="pf-table">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Kegiatan & Kategori</th>
                    <th>Jadwal & Waktu</th>
                    <th>Lokasi & Elevasi</th>
                    <th>Kuota & Relawan</th>
                    <th>Biaya / Kaos</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 170px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Tidak ada kegiatan penanaman yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1 + ($offset ?? 0); foreach ($events as $ev): ?>
                        <?php 
                            $perc = ($ev['quota'] > 0) ? min(100, round(($ev['registered_count'] / $ev['quota']) * 100)) : 0;
                            $barColor = ($perc >= 100) ? 'bg-danger' : (($perc >= 75) ? 'bg-warning' : 'bg-success');
                            $diffInfo = $difficultyBadges[$ev['difficulty_level'] ?? 'sedang'] ?? $difficultyBadges['sedang'];
                            $stClass = $statusColors[$ev['status']][0] ?? 'bg-secondary-subtle text-secondary';
                            $stIcon  = $statusColors[$ev['status']][1] ?? 'bi-circle';
                        ?>
                        <tr>
                            <td class="font-monospace text-muted"><?= $no++ ?></td>
                            <td>
                                <a href="/kegiatan/<?= $ev['id'] ?>" target="_blank" class="event-title mb-1" title="Buka halaman publik">
                                    <?= htmlspecialchars($ev['title']) ?>
                                    <i class="bi bi-box-arrow-up-right ms-1 small text-muted" style="font-size: 11px;"></i>
                                </a>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="category-pill"><?= htmlspecialchars($ev['category'] ?? 'Agroforestri') ?></span>
                                    <span class="badge <?= $diffInfo[0] ?> rounded-pill" style="color: <?= $diffInfo[1] ?>; font-size: 10px;">
                                        <i class="bi <?= $diffInfo[2] ?> me-1"></i><?= $diffInfo[3] ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark font-monospace"><?= date('d M Y', strtotime($ev['event_date'])) ?></div>
                                <div class="text-muted small"><i class="bi bi-clock me-1"></i><?= substr($ev['gathering_time'], 0, 5) ?> WIB</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($ev['location_name'] ?? 'Lokasi Konservasi') ?></div>
                                <div class="text-muted small d-flex align-items-center gap-1">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($ev['city'] ?? '-') ?>
                                    <?php if (!empty($ev['elevation_masl'])): ?>
                                        &bull; <span class="elev-pill"><?= number_format($ev['elevation_masl']) ?> mdpl</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="min-width: 140px;">
                                <div class="d-flex justify-content-between small fw-bold font-monospace">
                                    <span><?= $ev['registered_count'] ?> / <?= $ev['quota'] ?></span>
                                    <span class="text-muted"><?= $perc ?>%</span>
                                </div>
                                <div class="quota-progress">
                                    <div class="quota-progress-bar <?= $barColor ?>" style="width: <?= $perc ?>%;"></div>
                                </div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">
                                    Sisa: <?= max(0, $ev['quota'] - $ev['registered_count']) ?> kursi
                                </div>
                            </td>
                            <td>
                                <?php if ($ev['registration_fee'] > 0): ?>
                                    <div class="fw-bold font-monospace text-dark">Rp <?= number_format($ev['registration_fee'], 0, ',', '.') ?></div>
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size: 10px;">Bundle Kaos</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle" style="color: #0a3622; font-size: 11px;">Gratis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Quick Status Dropdown -->
                                <div class="dropdown">
                                    <button class="badge <?= $stClass ?> px-2 py-1 rounded-pill small dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi <?= $stIcon ?> me-1"></i><?= $statusOptions[$ev['status']] ?? ucfirst($ev['status']) ?>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                        <li><h6 class="dropdown-header">Ubah Status Kegiatan:</h6></li>
                                        <?php foreach ($statusOptions as $k => $label): ?>
                                            <?php if ($k !== $ev['status']): ?>
                                                <li>
                                                    <form action="/admin/kegiatan/<?= $ev['id'] ?>/toggle-status" method="POST" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="<?= $k ?>">
                                                        <button type="submit" class="dropdown-item py-1">
                                                            <?= $label ?>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Kelola Peserta -->
                                    <a href="/admin/peserta?event_id=<?= $ev['id'] ?>" class="btn btn-sm btn-light border" title="Kelola Peserta Terdaftar">
                                        <i class="bi bi-people-fill text-primary"></i>
                                    </a>

                                    <!-- Cetak Presensi -->
                                    <a href="/admin/kegiatan/<?= $ev['id'] ?>/presensi" target="_blank" class="btn btn-sm btn-light border" title="Cetak Lembar Presensi Lapangan">
                                        <i class="bi bi-printer text-dark"></i>
                                    </a>

                                    <!-- Export CSV -->
                                    <a href="/admin/kegiatan/<?= $ev['id'] ?>/export-peserta" class="btn btn-sm btn-light border" title="Export Peserta ke CSV">
                                        <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                                    </a>

                                    <!-- Edit -->
                                    <button type="button" class="btn btn-sm btn-light border btn-edit-event" 
                                            data-json="<?= htmlspecialchars(json_encode($ev), ENT_QUOTES, 'UTF-8') ?>"
                                            title="Edit Data Ekspedisi">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>

                                    <!-- Hapus / Batalkan -->
                                    <form action="/admin/kegiatan/<?= $ev['id'] ?>/hapus" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus/membatalkan kegiatan ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Hapus / Batalkan">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pf-pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?status=<?= urlencode($statusFilter) ?>&category=<?= urlencode($categoryFilter) ?>&q=<?= urlencode($searchQuery) ?>&page=<?= $i ?>"
                   class="<?= $i === $page ? 'active-page' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH & EDIT EKSPEDISI (ULTRA PROFESSIONAL TABBED FORM) -->
<!-- ========================================================================= -->
<?php foreach (['modalTambahEvent' => 'add', 'modalEditEvent' => 'edit'] as $modalId => $pfx): ?>
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="<?= $pfx === 'edit' ? 'formEditEvent' : 'formTambahEvent' ?>"
                  action="/admin/kegiatan<?= $pfx === 'edit' ? '/0/update' : '/simpan' ?>" 
                  method="POST" 
                  class="modal-form-wrap">
                <?= csrf_field() ?>
                
                <!-- Modal Header Premium -->
                <div class="modal-header-pro d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge rounded-pill px-3 py-1 font-mono text-uppercase" style="background: rgba(34,197,94,0.2); color: #86efac; font-size: 10px; letter-spacing: 1px;">
                                <i class="bi bi-shield-check me-1"></i>SISTEM MANAJEMEN EKSPEDISI
                            </span>
                            <span class="text-white-50 small">&bull;</span>
                            <span class="text-white-50 small font-mono">PLANT FOR FUN CONSERVATION</span>
                        </div>
                        <h5 class="modal-title fw-bold font-poster text-uppercase text-white mb-0" style="letter-spacing: 0.5px;">
                            <i class="bi bi-<?= $pfx === 'edit' ? 'pencil-square text-warning' : 'compass text-success' ?> me-2"></i>
                            <?= $pfx === 'edit' ? 'Edit Data Ekspedisi Penanaman' : 'Buat Jadwal Ekspedisi Baru' ?>
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body modal-body-pro">
                    <!-- Nav Tabs Stepper -->
                    <ul class="nav modal-nav-tabs" id="<?= $pfx ?>_expeditionTabs" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link active w-100" id="<?= $pfx ?>-tab-info" data-bs-toggle="pill" data-bs-target="#<?= $pfx ?>-pane-info" type="button" role="tab">
                                <i class="bi bi-info-circle me-1"></i> 1. Info &amp; Jadwal
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="<?= $pfx ?>-tab-route" data-bs-toggle="pill" data-bs-target="#<?= $pfx ?>-pane-route" type="button" role="tab">
                                <i class="bi bi-geo-alt me-1"></i> 2. Rute &amp; Medan
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="<?= $pfx ?>-tab-rundown" data-bs-toggle="pill" data-bs-target="#<?= $pfx ?>-pane-rundown" type="button" role="tab">
                                <i class="bi bi-clock-history me-1"></i> 3. Rundown &amp; Pohon
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link w-100" id="<?= $pfx ?>-tab-pic" data-bs-toggle="pill" data-bs-target="#<?= $pfx ?>-pane-pic" type="button" role="tab">
                                <i class="bi bi-people me-1"></i> 4. PIC, Biaya &amp; Kaos
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content Panes -->
                    <div class="tab-content" id="<?= $pfx ?>_expeditionTabContent">
                        
                        <!-- ============================================================= -->
                        <!-- TAB 1: INFORMASI POKOK & JADWAL -->
                        <!-- ============================================================= -->
                        <div class="tab-pane fade show active" id="<?= $pfx ?>-pane-info" role="tabpanel">
                            <div class="form-section-title">
                                <i class="bi bi-card-heading text-success"></i> Identitas &amp; Jadwal Pelaksanaan
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small mb-1">Judul Ekspedisi / Aksi Konservasi *</label>
                                    <input type="text" name="title" id="<?= $pfx ?>_title" class="form-control" required maxlength="200" placeholder="Contoh: Plant For Fun: Kerenceng Coffee Planting & Agroforestry Expedition">
                                    <div class="form-text" style="font-size: 11px;">Gunakan judul yang jelas, memikat, dan mencerminkan lokasi penanaman.</div>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark small mb-1">Kategori Ekspedisi *</label>
                                    <input type="text" name="category" id="<?= $pfx ?>_category" class="form-control" required placeholder="Contoh: Agroforestri Gunung Kopi">
                                    <!-- Preset Pills -->
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="text-muted small me-1" style="font-size: 11px;">Preset Cepat:</span>
                                        <button type="button" class="btn-preset-pill" onclick="setCategory('<?= $pfx ?>', 'Agroforestri Gunung Kopi')">☕ Agroforestri Kopi</button>
                                        <button type="button" class="btn-preset-pill" onclick="setCategory('<?= $pfx ?>', 'Restorasi Mangrove Pesisir')">🌊 Restorasi Mangrove</button>
                                        <button type="button" class="btn-preset-pill" onclick="setCategory('<?= $pfx ?>', 'Hutan Kota & Edukasi')">🏙️ Hutan Kota</button>
                                        <button type="button" class="btn-preset-pill" onclick="setCategory('<?= $pfx ?>', 'Konservasi Hulu DAS')">🏞️ Hulu DAS</button>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark small mb-1">Status Ekspedisi *</label>
                                    <select name="status" id="<?= $pfx ?>_status" class="form-select">
                                        <?php foreach ($statusOptions as $v => $l): ?>
                                            <option value="<?= $v ?>"><?= $l ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">
                                <i class="bi bi-calendar2-week text-success"></i> Waktu, Lokasi &amp; Kapasitas Kuota
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">Tanggal Pelaksanaan *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
                                        <input type="date" name="event_date" id="<?= $pfx ?>_event_date" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">Jam Kumpul di Meeting Point *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                                        <input type="time" name="gathering_time" id="<?= $pfx ?>_gathering_time" class="form-control" value="06:30" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1">Target Kuota Relawan (Kursi) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                                        <input type="number" name="quota" id="<?= $pfx ?>_quota" class="form-control" min="1" required placeholder="50">
                                        <span class="input-group-text bg-light small">Orang</span>
                                    </div>
                                    <!-- Preset Kuota -->
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="text-muted small me-1" style="font-size: 11px;">Pilihan:</span>
                                        <button type="button" class="btn-preset-pill" onclick="setQuota('<?= $pfx ?>', 30)">30 Relawan</button>
                                        <button type="button" class="btn-preset-pill" onclick="setQuota('<?= $pfx ?>', 50)">50 Relawan</button>
                                        <button type="button" class="btn-preset-pill" onclick="setQuota('<?= $pfx ?>', 80)">80 Relawan</button>
                                        <button type="button" class="btn-preset-pill" onclick="setQuota('<?= $pfx ?>', 100)">100 Relawan</button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small mb-1">Kawasan Konservasi Mitra Terdaftar</label>
                                    <select name="location_id" id="<?= $pfx ?>_location_id" class="form-select" onchange="onLocationSelectChange('<?= $pfx ?>', this)">
                                        <option value="">— Pilih Kawasan Konservasi Terdaftar (Opsional) —</option>
                                        <?php foreach ($locations as $loc): ?>
                                            <option value="<?= $loc['id'] ?>"
                                                    data-lat="<?= $loc['latitude'] ?? '' ?>"
                                                    data-lng="<?= $loc['longitude'] ?? '' ?>"
                                                    data-name="<?= htmlspecialchars($loc['name']) ?>"
                                                    data-city="<?= htmlspecialchars($loc['city'] ?? '') ?>">
                                                <?= htmlspecialchars($loc['name']) ?> (<?= htmlspecialchars($loc['city']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text" style="font-size: 11px;">Menghubungkan kegiatan ke kawasan konservasi resmi di database TanamPohon.id. Memilih kawasan otomatis memposisikan peta.</div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================================= -->
                        <!-- TAB 2: RUTE LAPANGAN & MEDAN JALUR -->
                        <!-- ============================================================= -->
                        <div class="tab-pane fade" id="<?= $pfx ?>-pane-route" role="tabpanel">
                            <div class="form-section-title">
                                <i class="bi bi-geo-alt-fill text-danger"></i> Titik Kumpul &amp; Peta Interaktif
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark small mb-1">Titik Kumpul (Meeting Point) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-pin-map-fill text-danger"></i></span>
                                        <input type="text" name="gathering_point" id="<?= $pfx ?>_gathering_point" class="form-control" required placeholder="Contoh: Basecamp andu Roastery, Cicalengka">
                                    </div>
                                    <div class="form-text" style="font-size: 11px;">Lokasi kumpul awal sebelum pemberangkatan ke zona tanam.</div>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark small mb-1">Tautan Google Maps Titik Kumpul</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-google text-primary"></i></span>
                                        <input type="url" name="google_maps_link" id="<?= $pfx ?>_google_maps_link" class="form-control font-monospace" placeholder="https://maps.google.com/?q=-6.9833,107.8333" onchange="parseGmapsUrlToCoords('<?= $pfx ?>')">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="testGmapsUrl('<?= $pfx ?>')" title="Uji Buka di Google Maps">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </button>
                                    </div>
                                    <div class="form-text" style="font-size: 11px;">Otomatis terisi saat Anda memilih / menggeser pin di peta di bawah.</div>
                                </div>
                            </div>

                            <!-- Koordinat GPS & Geolocation Toolbar -->
                            <div class="p-3 mb-3 rounded-3 bg-light border">
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-sm-4">
                                        <label class="form-label fw-bold small mb-1 text-muted">Latitude (Garis Lintang)</label>
                                        <input type="text" name="latitude" id="<?= $pfx ?>_latitude" class="form-control form-control-sm font-monospace fw-bold" placeholder="-6.983300" oninput="onCoordInputChange('<?= $pfx ?>')">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label fw-bold small mb-1 text-muted">Longitude (Garis Bujur)</label>
                                        <input type="text" name="longitude" id="<?= $pfx ?>_longitude" class="form-control form-control-sm font-monospace fw-bold" placeholder="107.833300" oninput="onCoordInputChange('<?= $pfx ?>')">
                                    </div>
                                    <div class="col-sm-4 pt-sm-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-bold" onclick="detectAdminCurrentLocation('<?= $pfx ?>')">
                                            <i class="bi bi-crosshair me-1"></i>Deteksi GPS Saya
                                        </button>
                                    </div>
                                </div>

                                <!-- Preset Koordinat Konservasi Populer -->
                                <div class="d-flex align-items-center gap-1 flex-wrap pt-1 border-top mt-2">
                                    <span class="text-muted small fw-bold me-1" style="font-size: 11px;"><i class="bi bi-geo-fill text-success me-1"></i>Preset Titik Lokasi:</span>
                                    <button type="button" class="btn-preset-pill" onclick="setEventCoords('<?= $pfx ?>', -6.9833, 107.8333, 'Basecamp andu Roastery, Cicalengka', 1550)">☕ Basecamp Kerenceng (1.550m)</button>
                                    <button type="button" class="btn-preset-pill" onclick="setEventCoords('<?= $pfx ?>', -6.3300, 108.3200, 'Posko Konservasi Mangrove Pantai Karangsong', 5)">🌊 Mangrove Karangsong (5m)</button>
                                    <button type="button" class="btn-preset-pill" onclick="setEventCoords('<?= $pfx ?>', -6.3200, 106.6800, 'Amfiteater Hutan Kota Tangerang Selatan', 45)">🏙️ Hutan Kota Tangsel (45m)</button>
                                    <button type="button" class="btn-preset-pill" onclick="setEventCoords('<?= $pfx ?>', -7.2278, 107.9087, 'Balai Konservasi Alam Tarogong Kaler, Garut', 1150)">🏞️ DAS Cimanuk Garut (1.150m)</button>
                                </div>
                            </div>

                            <!-- Peta Interaktif Leaflet Map Picker -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <div class="small fw-bold text-dark">
                                        <i class="bi bi-cursor-fill text-danger me-1"></i>Klik pada peta atau seret (drag) pin merah untuk menentukan titik kumpul:
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Layer Switcher -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-success btn-sm fw-bold active" id="<?= $pfx ?>_btn_layer_street" onclick="switchAdminMapLayer('<?= $pfx ?>', 'street')">
                                                <i class="bi bi-map me-1"></i>Peta Jalan
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" id="<?= $pfx ?>_btn_layer_sat" onclick="switchAdminMapLayer('<?= $pfx ?>', 'satellite')">
                                                <i class="bi bi-globe-americas me-1"></i>Satelit
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map Search Bar -->
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="<?= $pfx ?>_map_search" class="form-control" placeholder="Cari nama kota, kecamatan, atau objek geografis (contoh: Cicalengka, Gunung Kerenceng)..." onkeydown="if(event.key==='Enter'){event.preventDefault();searchAdminMapLocation('<?= $pfx ?>');}">
                                    <button type="button" class="btn btn-outline-success fw-bold px-3" onclick="searchAdminMapLocation('<?= $pfx ?>')">Cari di Peta</button>
                                </div>

                                <div id="<?= $pfx ?>_admin_map" class="border rounded-3" style="height: 320px; width: 100%; position: relative; z-index: 1;">
                                    <!-- Leaflet map instance -->
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1 text-muted" style="font-size: 11px;">
                                    <span><i class="bi bi-info-circle me-1"></i>Data peta OpenStreetMap &amp; Esri Satellite Imagery.</span>
                                    <span id="<?= $pfx ?>_map_coord_status" class="font-mono text-dark fw-bold">Belum ada titik dipilih</span>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">
                                <i class="bi bi-activity text-warning"></i> Spesifikasi Medan Jalur Trekking &amp; Elevasi
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Tingkat Kesulitan Jalur Trekking *</label>
                                    <select name="difficulty_level" id="<?= $pfx ?>_difficulty_level" class="form-select">
                                        <option value="mudah">🟢 Mudah (Jalur datar/pantai, ramah pemula &amp; keluarga)</option>
                                        <option value="sedang" selected>🟡 Sedang (Trek bukit landai, jalan tanah perbukitan)</option>
                                        <option value="menantang">🔴 Menantang (Jalur lereng gunung terjal / zona rawa pasang surut)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Ketinggian Elevasi Lokasi (mdpl) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-cloud-arrow-up"></i></span>
                                        <input type="number" name="elevation_masl" id="<?= $pfx ?>_elevation_masl" class="form-control" placeholder="1550" value="1550">
                                        <span class="input-group-text bg-light">mdpl</span>
                                    </div>
                                    <!-- Preset Elevasi -->
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="text-muted small me-1" style="font-size: 11px;">Pilihan Cepat:</span>
                                        <button type="button" class="btn-preset-pill" onclick="setElevation('<?= $pfx ?>', 5)">🌊 5 mdpl (Pesisir)</button>
                                        <button type="button" class="btn-preset-pill" onclick="setElevation('<?= $pfx ?>', 45)">🏙️ 45 mdpl (Perkotaan)</button>
                                        <button type="button" class="btn-preset-pill" onclick="setElevation('<?= $pfx ?>', 1150)">🏞️ 1.150 mdpl (Sub-DAS)</button>
                                        <button type="button" class="btn-preset-pill" onclick="setElevation('<?= $pfx ?>', 1550)">⛰️ 1.550 mdpl (Gn. Kerenceng)</button>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold text-dark small mb-1">Tautan / Path Poster Sampul Ekspedisi</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-image"></i></span>
                                        <input type="text" name="cover_image" id="<?= $pfx ?>_cover_image" class="form-control" placeholder="assets/images/plant-for-fun-kerenceng.jpg">
                                    </div>
                                    <div class="form-text" style="font-size: 11px;">Bisa menggunakan asset lokal seperti <code>assets/images/plant-for-fun-kerenceng.jpg</code> atau URL gambar eksternal.</div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================================= -->
                        <!-- TAB 3: RUNDOWN & TARGET KONSERVASI (SMART VISUAL BUILDER) -->
                        <!-- ============================================================= -->
                        <div class="tab-pane fade" id="<?= $pfx ?>-pane-rundown" role="tabpanel">
                            
                            <!-- Header Bar: Title, Template Presets & Mode Toggle -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="form-section-title mb-0">
                                    <i class="bi bi-clock-history text-primary"></i> Linimasa &amp; Rundown Kegiatan
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Mode Switcher -->
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-success btn-sm fw-bold active" id="<?= $pfx ?>_btn_mode_builder" onclick="toggleRundownViewMode('<?= $pfx ?>', 'builder')">
                                            <i class="bi bi-ui-checks-grid me-1"></i> Builder Visual
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="<?= $pfx ?>_btn_mode_text" onclick="toggleRundownViewMode('<?= $pfx ?>', 'text')">
                                            <i class="bi bi-textarea-t me-1"></i> Teks Manual
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Presets Bar -->
                            <div class="p-2 mb-3 rounded-3 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="text-muted small fw-bold me-1" style="font-size: 11px;"><i class="bi bi-magic me-1 text-warning"></i>Gunakan Template:</span>
                                    <button type="button" class="btn-preset-pill" onclick="applyRundownTemplate('<?= $pfx ?>', 'kerenceng')">☕ Agroforestri Kopi Kerenceng</button>
                                    <button type="button" class="btn-preset-pill" onclick="applyRundownTemplate('<?= $pfx ?>', 'mangrove')">🌊 Restorasi Mangrove</button>
                                    <button type="button" class="btn-preset-pill" onclick="applyRundownTemplate('<?= $pfx ?>', 'hutankota')">🏙️ Hutan Kota &amp; Edukasi</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold" onclick="addRundownRow('<?= $pfx ?>')">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah Baris Jadwal
                                </button>
                            </div>

                            <!-- 1. Visual Rundown Row Builder Wrap -->
                            <div id="<?= $pfx ?>_rundown_builder_wrap" class="rundown-builder-box mb-3">
                                <div class="row g-2 mb-2 text-muted small fw-bold font-mono px-2" style="font-size: 11px;">
                                    <div class="col-4 col-sm-3">WAKTU (MULAI &ndash; SELESAI)</div>
                                    <div class="col-6 col-sm-7">AGENDA / NAMA KEGIATAN LAPANGAN</div>
                                    <div class="col-2 text-end pe-2">AKSI</div>
                                </div>

                                <!-- Rows injected via JavaScript -->
                                <div id="<?= $pfx ?>_rundown_rows">
                                    <!-- Dynamic rows -->
                                </div>

                                <!-- Bottom Action Bar for Rundown -->
                                <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold" onclick="addRundownRow('<?= $pfx ?>')">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah Baris Jadwal
                                    </button>
                                    <span class="text-muted" style="font-size: 11px;">
                                        <i class="bi bi-info-circle me-1"></i>Format 24 jam. Gunakan panah untuk mengatur urutan.
                                    </span>
                                </div>

                                <!-- Quick Activity Inserter Toolbar -->
                                <div class="pt-2 border-top mt-2">
                                    <span class="text-muted small me-1" style="font-size: 11px;"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Sisipkan Agenda Populer:</span>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Registrasi Ulang & Pembagian Toolkit')">+ Registrasi &amp; Toolkit</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Safety Induction, Briefing Jalur & Doa Bersama')">+ Safety Briefing &amp; Doa</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Trekking Bersama Menuju Zona Penanaman')">+ Trekking ke Zona Tanam</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Aksi Penanaman Bibit Pohon Bersama Petani')">+ Aksi Penanaman Bibit</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Sesi Cupping Kopi Spesialti & Edukasi Pascapanen')">+ Cupping Kopi &amp; Edukasi</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Makan Siang Bersama Olahan Dapur Petani')">+ Makan Siang Petani</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickActivity('<?= $pfx ?>', 'Penyerahan Simbolis Sertifikat Relawan & Penutupan')">+ Sertifikat &amp; Penutupan</button>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Direct Text Box Wrap (Synced automatically) -->
                            <div id="<?= $pfx ?>_rundown_text_wrap" class="mb-3" style="display: none;">
                                <textarea name="itinerary" id="<?= $pfx ?>_itinerary" class="form-control font-monospace" rows="7" 
                                          placeholder="05:30 - 06:15 | Kumpul di Basecamp andu Roastery & Registrasi Relawan&#10;06:15 - 07:00 | Briefing Jalur, Safety Induction & Doa Bersama" 
                                          style="font-size: 12.5px; line-height: 1.5;"
                                          oninput="renderRundownBuilder('<?= $pfx ?>', false)"></textarea>
                                <div class="form-text" style="font-size: 11px;">
                                    Tulis format <code>Jam Mulai - Jam Selesai | Aktivitas</code> per baris. Mode ini cocok jika Anda ingin menyalin jadwal langsung dari teks dokumen.
                                </div>
                            </div>

                            <div class="form-section-title mt-4">
                                <i class="bi bi-tree text-success"></i> Spesies Pohon, Perlengkapan &amp; Deskripsi
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Target Spesies Pohon yang Ditanam</label>
                                    <textarea name="tree_species_info" id="<?= $pfx ?>_tree_species_info" class="form-control" rows="2" placeholder="Contoh: Kopi Arabika Typica, Suren (Toona sureni), dan Mahoni"></textarea>
                                    <!-- Quick Tree Chips -->
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <span class="text-muted small me-1" style="font-size: 10.5px;">Pilih Jenis:</span>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'tree_species_info', 'Kopi Arabika Typica')">+ Kopi Typica</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'tree_species_info', 'Suren (Toona sureni)')">+ Pohon Suren</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'tree_species_info', 'Mahoni (Swietenia mahagoni)')">+ Mahoni</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'tree_species_info', 'Trembesi (Samanea saman)')">+ Trembesi</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'tree_species_info', 'Mangrove Rhizophora')">+ Mangrove</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'tree_species_info', 'Bambu Betung')">+ Bambu Betung</button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Checklist Perlengkapan yang Wajib Dibawa Relawan</label>
                                    <textarea name="items_to_bring" id="<?= $pfx ?>_items_to_bring" class="form-control" rows="2" placeholder="Sepatu trekking / bot anti selip, jas hujan ponco, botol minum pribadi (tumbler), pakaian ganti, sarung tangan kebun"></textarea>
                                    <!-- Quick Items Chips -->
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <span class="text-muted small me-1" style="font-size: 10.5px;">Pilih Barang:</span>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Sepatu bot / trekking anti selip')">+ Sepatu Trekking</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Jas hujan ponco')">+ Jas Hujan</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Tumbler botol minum isi ulang')">+ Tumbler Air</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Pakaian ganti & handuk kecil')">+ Pakaian Ganti</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Sarung tangan kebun')">+ Sarung Tangan</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Obat-obatan pribadi')">+ Obat Pribadi</button>
                                        <button type="button" class="btn-preset-pill" onclick="addQuickItem('<?= $pfx ?>', 'items_to_bring', 'Topi / pelindung matahari')">+ Topi Lapangan</button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small mb-1">Deskripsi &amp; Narasi Ekspedisi</label>
                                    <textarea name="description" id="<?= $pfx ?>_description" class="form-control" rows="3" placeholder="Ceritakan latar belakang ekologi kawasan, tantangan di lapangan, dan dampak jangka panjang bagi petani lokal..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================================= -->
                        <!-- TAB 4: PIC, BIAYA & PAKET MERCHANDISE -->
                        <!-- ============================================================= -->
                        <div class="tab-pane fade" id="<?= $pfx ?>-pane-pic" role="tabpanel">
                            <div class="form-section-title">
                                <i class="bi bi-person-badge-fill text-primary"></i> Penanggung Jawab Lapangan (PIC)
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Nama Koordinator Lapangan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                        <input type="text" name="coordinator_name" id="<?= $pfx ?>_coordinator_name" class="form-control" placeholder="Contoh: Ahmad Fauzi (andu Roastery)">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">No. WhatsApp Koordinator (Untuk Relawan)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-whatsapp text-success"></i></span>
                                        <input type="tel" name="coordinator_phone" id="<?= $pfx ?>_coordinator_phone" class="form-control" placeholder="08xxxxxxxxxx">
                                    </div>
                                    <div class="form-text" style="font-size: 11px;">Nomor ini akan terhubung langsung ke tombol bantuan WhatsApp relawan di halaman detail.</div>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">
                                <i class="bi bi-wallet2 text-success"></i> Biaya Registrasi &amp; Ketentuan
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Biaya Registrasi / Donasi Wajib (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">Rp</span>
                                        <input type="text" name="registration_fee" id="<?= $pfx ?>_registration_fee" class="form-control font-monospace fw-bold" value="0" placeholder="0 = Gratis Terbuka">
                                    </div>
                                    <!-- Preset Biaya -->
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="text-muted small me-1" style="font-size: 11px;">Pilihan Cepat:</span>
                                        <button type="button" class="btn-preset-pill" onclick="setFee('<?= $pfx ?>', '0')">🌱 Gratis (Rp 0)</button>
                                        <button type="button" class="btn-preset-pill" onclick="setFee('<?= $pfx ?>', '50.000')">Rp 50.000</button>
                                        <button type="button" class="btn-preset-pill" onclick="setFee('<?= $pfx ?>', '75.000')">Rp 75.000</button>
                                        <button type="button" class="btn-preset-pill" onclick="setFee('<?= $pfx ?>', '100.000')">Rp 100.000</button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small mb-1">Deadline Batas Pembatalan (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar-x text-danger"></i></span>
                                        <input type="date" name="cancel_deadline" id="<?= $pfx ?>_cancel_deadline" class="form-control">
                                    </div>
                                    <!-- Preset Tanggal Batal Otomatis -->
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="text-muted small me-1" style="font-size: 11px;">Hitung Otomatis:</span>
                                        <button type="button" class="btn-preset-pill" onclick="setCancelDeadlineOffset('<?= $pfx ?>', 3)">📅 H-3 Pelaksanaan</button>
                                        <button type="button" class="btn-preset-pill" onclick="setCancelDeadlineOffset('<?= $pfx ?>', 5)">📅 H-5 Pelaksanaan</button>
                                        <button type="button" class="btn-preset-pill" onclick="setCancelDeadlineOffset('<?= $pfx ?>', 7)">📅 H-7 Pelaksanaan</button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">
                                <i class="bi bi-gift-fill text-warning"></i> Paket Kaos &amp; Fasilitas Relawan
                            </div>

                            <div class="p-3 bg-light rounded-4 border">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="includes_tshirt" id="<?= $pfx ?>_includes_tshirt" role="switch" onchange="toggleTshirtInput('<?= $pfx ?>')">
                                    <label class="form-check-label fw-bold text-dark" for="<?= $pfx ?>_includes_tshirt">
                                        <i class="bi bi-gift-fill text-primary me-1"></i> Termasuk Paket Kaos Relawan Eksklusif
                                    </label>
                                </div>
                                <div id="<?= $pfx ?>_tshirt_wrap" class="mt-2">
                                    <input type="text" name="tshirt_included_note" id="<?= $pfx ?>_tshirt_included_note" class="form-control" placeholder="Contoh: Kaos Cotton Combed 24s Edisi Khusus Plant For Fun &bull; Sablon Matsui Eco">
                                    <div class="form-text" style="font-size: 11px;">Catatan ini akan tampil di kartu fasilitas relawan dan petunjuk pengambilan tiket.</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer Navigation & Submit -->
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm fw-semibold" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i>Batal
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 btn-sm fw-bold" onclick="nextModalTab('<?= $pfx ?>')">
                            Tab Berikutnya →
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold font-poster shadow-sm">
                            <i class="bi bi-<?= $pfx === 'edit' ? 'check2-circle' : 'plus-circle' ?> me-1"></i>
                            <?= $pfx === 'edit' ? 'Simpan Perubahan Ekspedisi' : 'Terbitkan Ekspedisi Baru' ?>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Leaflet JS for Interactive Map Picker -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- ========================================================================= -->
<!-- JAVASCRIPT LOGIC UNTUK MODAL EKSPEDISI -->
<!-- ========================================================================= -->
<script>
// ============================================================================
// 1. TEMPLATE RUNDOWN & BUILDER INTERAKTIF
// ============================================================================
const EXP_TEMPLATES = {
    'kerenceng': `05:30 - 06:15 | Kumpul di Basecamp andu Roastery & Registrasi Relawan
06:15 - 07:00 | Briefing Jalur, Safety Induction & Doa Bersama
07:00 - 08:30 | Trekking Ringan Menuju Zona Tanam Kebon Kopi (1.550 mdpl)
08:30 - 11:00 | Aksi Tanam Pohon Kopi Arabika & Penaung Bersama Petani Karsa
11:00 - 12:30 | Sesi Cupping Kopi Spesialti Kerenceng & Edukasi Pascapanen
12:30 - 13:30 | Makan Siang Bersama Hasil Bumi Dapur Petani Kerenceng
13:30 - 15:00 | Penurunan Kembali ke Basecamp & Pembagian Merchandise Eksklusif`,

    'mangrove': `07:00 - 07:30 | Registrasi Relawan & Pembagian Toolkit di Posko Pesisir
07:30 - 08:00 | Safety Briefing & Edukasi Ekosistem Pasang Surut Mangrove
08:00 - 10:30 | Aksi Penanaman 1.000 Bibit Mangrove Rhizophora di Zona Pesisir
10:30 - 11:30 | Pembersihan Sampah Pesisir Pantai (Coastal Clean-Up)
11:30 - 12:30 | Kelapa Muda, Ishoma & Ramah Tamah Petani Pesisir
12:30 - 13:00 | Penyerahan Simbolis Sertifikat & Penutupan`,

    'hutankota': `07:30 - 08:00 | Registrasi Ulang & Pengambilan Kaos Relawan
08:00 - 08:45 | Workshop Edukasi Kanopi Hijau Kota & Krisis Iklim
08:45 - 11:00 | Aksi Penanaman Pohon Peneduh (Trembesi & Mahoni)
11:00 - 12:00 | Diskusi Interaktif Bersama Komunitas Lingkungan & Kampus
12:00 - 13:00 | Pembagian Bibit Bawa Pulang & Santap Siang Ramah Lingkungan`
};

function escapeHtmlAttr(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function addHoursToTimeString(timeStr, hoursToAdd = 1) {
    if (!timeStr) return '';
    const parts = timeStr.trim().split(':');
    if (parts.length < 2) return '';
    let h = parseInt(parts[0], 10);
    let m = parseInt(parts[1], 10);
    if (isNaN(h) || isNaN(m)) return '';
    
    h = (h + hoursToAdd) % 24;
    const hh = String(h).padStart(2, '0');
    const mm = String(m).padStart(2, '0');
    return `${hh}:${mm}`;
}

// Parse string multiline textarea menjadi array objek rundown
function parseRundown(text) {
    if (!text || !text.trim()) return [];
    return text.trim().split('\n').map(line => {
        line = line.trim();
        if (!line) return null;
        let timeStr = '';
        let actStr = line;
        if (line.includes('|')) {
            const p = line.split('|');
            timeStr = p[0].trim();
            actStr = p.slice(1).join('|').trim();
        }
        let start = '';
        let end = '';
        if (timeStr.includes('-')) {
            const t = timeStr.split('-');
            start = t[0].trim();
            end = t[1].trim();
        } else {
            start = timeStr;
        }
        return { start, end, activity: actStr };
    }).filter(Boolean);
}

// Buat elemen baris rundown secara langsung di DOM
function createRundownRowElement(pfx, start = '', end = '', activity = '') {
    const row = document.createElement('div');
    row.className = 'rundown-row-card d-flex align-items-center gap-2 mb-2 p-2 rounded-3 bg-white border';
    row.innerHTML = `
        <div class="d-flex align-items-center gap-1 flex-shrink-0">
            <input type="text" class="form-control form-control-sm text-center font-monospace fw-bold rundown-input-start" 
                   style="width: 72px;" placeholder="06:00" value="${escapeHtmlAttr(start)}" title="Jam Mulai (HH:MM)">
            <span class="text-muted small">&ndash;</span>
            <input type="text" class="form-control form-control-sm text-center font-monospace fw-bold rundown-input-end" 
                   style="width: 72px;" placeholder="07:00" value="${escapeHtmlAttr(end)}" title="Jam Selesai (HH:MM)">
        </div>
        <div class="flex-grow-1">
            <input type="text" class="form-control form-control-sm rundown-input-act" 
                   placeholder="Aktivitas (Contoh: Aksi Tanam Pohon Bersama Petani)" 
                   value="${escapeHtmlAttr(activity)}">
        </div>
        <div class="flex-shrink-0 d-flex align-items-center gap-1">
            <button type="button" class="btn btn-outline-secondary btn-sm p-1 rounded-circle btn-row-up" title="Geser ke Atas" style="width: 28px; height: 28px;">
                <i class="bi bi-arrow-up" style="font-size: 11px;"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm p-1 rounded-circle btn-row-down" title="Geser ke Bawah" style="width: 28px; height: 28px;">
                <i class="bi bi-arrow-down" style="font-size: 11px;"></i>
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle btn-row-remove" title="Hapus Baris Ini" style="width: 28px; height: 28px;">
                <i class="bi bi-trash" style="font-size: 12px;"></i>
            </button>
        </div>
    `;

    // Event listener input perubahan teks -> sync ke textarea
    row.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => syncRundownToTextarea(pfx));
    });

    // Geser urutan ke atas
    row.querySelector('.btn-row-up')?.addEventListener('click', () => {
        if (row.previousElementSibling && row.previousElementSibling.classList.contains('rundown-row-card')) {
            row.parentNode.insertBefore(row, row.previousElementSibling);
            syncRundownToTextarea(pfx);
        }
    });

    // Geser urutan ke bawah
    row.querySelector('.btn-row-down')?.addEventListener('click', () => {
        if (row.nextElementSibling && row.nextElementSibling.classList.contains('rundown-row-card')) {
            row.parentNode.insertBefore(row.nextElementSibling, row);
            syncRundownToTextarea(pfx);
        }
    });

    // Hapus baris ini
    row.querySelector('.btn-row-remove')?.addEventListener('click', () => {
        row.remove();
        checkRundownEmptyState(pfx);
        syncRundownToTextarea(pfx);
    });

    return row;
}

// Cek apakah ada baris jadwal, jika kosong tampilkan placeholder ramah
function checkRundownEmptyState(pfx) {
    const container = document.getElementById(pfx + '_rundown_rows');
    if (!container) return;
    const cards = container.querySelectorAll('.rundown-row-card');
    if (cards.length === 0) {
        container.innerHTML = `
        <div class="rundown-empty-state text-center p-4 border rounded-3 bg-light my-2">
            <div class="text-muted small mb-2">
                <i class="bi bi-calendar2-range text-success fs-3 d-block mb-1"></i>
                Belum ada jadwal kegiatan. Klik tombol di bawah untuk menambah baris atau gunakan template di atas.
            </div>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold" onclick="addRundownRow('${pfx}')">
                <i class="bi bi-plus-lg me-1"></i>Tambah Baris Pertama
            </button>
        </div>
        `;
    }
}

// Sinkron otomatis dari baris-baris visual ke hidden/manual textarea
function syncRundownToTextarea(pfx) {
    const container = document.getElementById(pfx + '_rundown_rows');
    const textarea = document.getElementById(pfx + '_itinerary');
    if (!container || !textarea) return;

    const cards = container.querySelectorAll('.rundown-row-card');
    const lines = [];
    cards.forEach(card => {
        const start = card.querySelector('.rundown-input-start')?.value.trim() || '';
        const end = card.querySelector('.rundown-input-end')?.value.trim() || '';
        const act = card.querySelector('.rundown-input-act')?.value.trim() || '';
        if (act || start || end) {
            let timeStr = start;
            if (end) timeStr += ' - ' + end;
            if (timeStr && act) {
                lines.push(`${timeStr} | ${act}`);
            } else if (timeStr) {
                lines.push(timeStr);
            } else {
                lines.push(act);
            }
        }
    });
    textarea.value = lines.join('\n');
}

// Tambah baris baru secara langsung (DOM-based dengan waktu cerdas berkelanjutan)
function addRundownRow(pfx, start = '', end = '', activity = '') {
    const container = document.getElementById(pfx + '_rundown_rows');
    if (!container) return null;

    // Hapus placeholder kosong jika ada
    const emptyState = container.querySelector('.rundown-empty-state');
    if (emptyState) emptyState.remove();

    // Hitung waktu jam otomatis bersambung jika waktu tidak ditentukan
    if (!start && !end) {
        const existingCards = container.querySelectorAll('.rundown-row-card');
        if (existingCards.length > 0) {
            const lastCard = existingCards[existingCards.length - 1];
            const lastEnd = lastCard.querySelector('.rundown-input-end')?.value.trim() || '';
            const lastStart = lastCard.querySelector('.rundown-input-start')?.value.trim() || '';

            if (lastEnd) {
                start = lastEnd;
                end = addHoursToTimeString(lastEnd, 1);
            } else if (lastStart) {
                start = addHoursToTimeString(lastStart, 1);
                end = addHoursToTimeString(start, 1);
            } else {
                start = '06:00';
                end = '07:00';
            }
        } else {
            start = '06:00';
            end = '07:00';
        }
    } else if (start && !end) {
        end = addHoursToTimeString(start, 1);
    }

    const rowEl = createRundownRowElement(pfx, start, end, activity);
    container.appendChild(rowEl);

    // Sync ke textarea
    syncRundownToTextarea(pfx);

    // Otomatis arahkan fokus kursor ke input aktivitas
    const actInput = rowEl.querySelector('.rundown-input-act');
    if (actInput) {
        setTimeout(() => actInput.focus(), 30);
    }

    return rowEl;
}

// Sisipkan aktivitas cepat ke baris baru dengan estimasi waktu berkelanjutan
function addQuickActivity(pfx, actTitle) {
    return addRundownRow(pfx, '', '', actTitle);
}

// Render baris visual builder dari isi textarea
function renderRundownBuilder(pfx, forceDefaultIfEmpty = false) {
    const textarea = document.getElementById(pfx + '_itinerary');
    const container = document.getElementById(pfx + '_rundown_rows');
    if (!textarea || !container) return;

    let items = parseRundown(textarea.value);
    if (items.length === 0 && forceDefaultIfEmpty) {
        items = [
            { start: '06:00', end: '07:00', activity: 'Registrasi Ulang & Safety Briefing' },
            { start: '07:00', end: '09:00', activity: 'Trekking & Aksi Penanaman Bibit Pohon' },
            { start: '09:00', end: '11:00', activity: 'Edukasi Pascapanen & Ramah Tamah Petani' },
            { start: '11:00', end: '12:00', activity: 'Makan Siang Petani & Penutupan' }
        ];
        const lines = items.map(it => `${it.start} - ${it.end} | ${it.activity}`);
        textarea.value = lines.join('\n');
    }

    container.innerHTML = '';
    if (items.length === 0) {
        checkRundownEmptyState(pfx);
    } else {
        items.forEach(it => {
            const row = createRundownRowElement(pfx, it.start, it.end, it.activity);
            container.appendChild(row);
        });
    }
}

// Toggle tampilan builder vs teks mentah
function toggleRundownViewMode(pfx, mode) {
    const builderWrap = document.getElementById(pfx + '_rundown_builder_wrap');
    const textWrap = document.getElementById(pfx + '_rundown_text_wrap');
    const btnBuilder = document.getElementById(pfx + '_btn_mode_builder');
    const btnText = document.getElementById(pfx + '_btn_mode_text');

    if (mode === 'builder') {
        renderRundownBuilder(pfx, false);
        if (builderWrap) builderWrap.style.display = 'block';
        if (textWrap) textWrap.style.display = 'none';
        if (btnBuilder) {
            btnBuilder.classList.add('active', 'btn-success');
            btnBuilder.classList.remove('btn-outline-secondary');
        }
        if (btnText) {
            btnText.classList.remove('active', 'btn-success');
            btnText.classList.add('btn-outline-secondary');
        }
    } else {
        syncRundownToTextarea(pfx);
        if (builderWrap) builderWrap.style.display = 'none';
        if (textWrap) textWrap.style.display = 'block';
        if (btnText) {
            btnText.classList.add('active', 'btn-success');
            btnText.classList.remove('btn-outline-secondary');
        }
        if (btnBuilder) {
            btnBuilder.classList.remove('active', 'btn-success');
            btnBuilder.classList.add('btn-outline-secondary');
        }
    }
}

// Terapkan template rundown utuh
function applyRundownTemplate(pfx, type) {
    const textarea = document.getElementById(pfx + '_itinerary');
    if (textarea && EXP_TEMPLATES[type]) {
        textarea.value = EXP_TEMPLATES[type];
        renderRundownBuilder(pfx, false);
        toggleRundownViewMode(pfx, 'builder');
    }
}

// ============================================================================
// 2. HELPER PRESET CEPAT FORM
// ============================================================================
function setCategory(pfx, val) {
    const input = document.getElementById(pfx + '_category');
    if (input) input.value = val;
}

function setFee(pfx, val) {
    const input = document.getElementById(pfx + '_registration_fee');
    if (input) input.value = val;
}

function setElevation(pfx, val) {
    const input = document.getElementById(pfx + '_elevation_masl');
    if (input) input.value = val;
}

function setQuota(pfx, val) {
    const el = document.getElementById(pfx + '_quota');
    if (el) el.value = val;
}

function addQuickItem(pfx, fieldName, value) {
    const el = document.getElementById(pfx + '_' + fieldName);
    if (!el) return;
    const current = el.value.trim();
    if (current === '') {
        el.value = value;
    } else {
        const parts = current.split(',').map(s => s.trim());
        if (!parts.includes(value)) {
            el.value = current + ', ' + value;
        }
    }
    el.focus();
}

function setCancelDeadlineOffset(pfx, daysBefore) {
    const dateInput = document.getElementById(pfx + '_event_date');
    const deadlineInput = document.getElementById(pfx + '_cancel_deadline');
    if (!dateInput || !deadlineInput || !dateInput.value) {
        alert('Silakan tentukan Tanggal Pelaksanaan terlebih dahulu di Tab 1.');
        // Buka tab info
        const infoTab = document.getElementById(pfx + '-tab-info');
        if (infoTab) bootstrap.Tab.getOrCreateInstance(infoTab).show();
        if (dateInput) dateInput.focus();
        return;
    }
    const d = new Date(dateInput.value);
    d.setDate(d.getDate() - daysBefore);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    deadlineInput.value = `${yyyy}-${mm}-${dd}`;
}

function toggleTshirtInput(pfx) {
    const chk = document.getElementById(pfx + '_includes_tshirt');
    const wrap = document.getElementById(pfx + '_tshirt_wrap');
    if (chk && wrap) {
        wrap.style.display = chk.checked ? 'block' : 'none';
    }
}

function nextModalTab(pfx) {
    const tabList = ['info', 'route', 'rundown', 'pic'];
    const currentActive = document.querySelector(`#${pfx}_expeditionTabs .nav-link.active`);
    if (!currentActive) return;
    
    let currentIndex = 0;
    tabList.forEach((t, i) => {
        if (currentActive.id.includes(t)) currentIndex = i;
    });

    const nextIndex = (currentIndex + 1) % tabList.length;
    const nextTabBtn = document.getElementById(`${pfx}-tab-${tabList[nextIndex]}`);
    if (nextTabBtn) {
        bootstrap.Tab.getOrCreateInstance(nextTabBtn).show();
    }
}

// Sinkronisasi otomatis saat form Tambah / Edit disubmit
['add', 'edit'].forEach(pfx => {
    const formId = pfx === 'edit' ? 'formEditEvent' : 'formTambahEvent';
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function() {
            syncRundownToTextarea(pfx);
        });
    }
});

// ============================================================================
// 3. PETA INTERAKTIF ADMIN (LEAFLET MAP PICKER & GPS NAVIGASI)
// ============================================================================
window.adminMaps = {};
window.adminMarkers = {};
window.adminMapLayers = {};

function initAdminMap(pfx, initialLat, initialLng) {
    const mapContainer = document.getElementById(pfx + '_admin_map');
    if (!mapContainer) return;

    let lat = parseFloat(initialLat);
    let lng = parseFloat(initialLng);
    if (isNaN(lat) || isNaN(lng) || lat === 0) {
        lat = -6.9833;
        lng = 107.8333;
    }

    if (window.adminMaps[pfx]) {
        const map = window.adminMaps[pfx];
        map.setView([lat, lng], 14);
        if (window.adminMarkers[pfx]) {
            window.adminMarkers[pfx].setLatLng([lat, lng]);
        }
        setTimeout(() => map.invalidateSize(), 150);
        updateAdminCoordInputs(pfx, lat, lng, false);
        return;
    }

    const streetLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });

    const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{x}/{y}', {
        maxZoom: 18,
        attribution: 'Esri Satellite'
    });

    const map = L.map(pfx + '_admin_map', {
        center: [lat, lng],
        zoom: 14,
        layers: [streetLayer]
    });

    const marker = L.marker([lat, lng], {
        draggable: true,
        title: 'Seret pin ini untuk menentukan titik kumpul'
    }).addTo(map);

    marker.bindPopup('<b>Titik Kumpul Ekspedisi</b><br>Seret pin atau klik peta untuk mengubah posisi koordinat.').openPopup();

    marker.on('dragend', function() {
        const pos = marker.getLatLng();
        updateAdminCoordInputs(pfx, pos.lat, pos.lng, true);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateAdminCoordInputs(pfx, e.latlng.lat, e.latlng.lng, true);
    });

    window.adminMaps[pfx] = map;
    window.adminMarkers[pfx] = marker;
    window.adminMapLayers[pfx] = { street: streetLayer, satellite: satLayer };

    updateAdminCoordInputs(pfx, lat, lng, false);
    setTimeout(() => map.invalidateSize(), 200);
}

function updateAdminCoordInputs(pfx, lat, lng, updateGmaps = true) {
    const latInput = document.getElementById(pfx + '_latitude');
    const lngInput = document.getElementById(pfx + '_longitude');
    const gmapsInput = document.getElementById(pfx + '_google_maps_link');
    const statusEl = document.getElementById(pfx + '_map_coord_status');

    const formattedLat = parseFloat(lat).toFixed(6);
    const formattedLng = parseFloat(lng).toFixed(6);

    if (latInput) latInput.value = formattedLat;
    if (lngInput) lngInput.value = formattedLng;
    if (gmapsInput && updateGmaps) {
        gmapsInput.value = `https://maps.google.com/?q=${formattedLat},${formattedLng}`;
    }
    if (statusEl) {
        statusEl.innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i>${formattedLat}, ${formattedLng}`;
    }
}

function onCoordInputChange(pfx) {
    const lat = parseFloat(document.getElementById(pfx + '_latitude')?.value);
    const lng = parseFloat(document.getElementById(pfx + '_longitude')?.value);
    if (!isNaN(lat) && !isNaN(lng)) {
        if (window.adminMaps[pfx] && window.adminMarkers[pfx]) {
            window.adminMarkers[pfx].setLatLng([lat, lng]);
            window.adminMaps[pfx].panTo([lat, lng]);
        }
        const gmapsInput = document.getElementById(pfx + '_google_maps_link');
        if (gmapsInput) {
            gmapsInput.value = `https://maps.google.com/?q=${lat.toFixed(6)},${lng.toFixed(6)}`;
        }
        const statusEl = document.getElementById(pfx + '_map_coord_status');
        if (statusEl) statusEl.innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i>${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}

function parseGmapsUrlToCoords(pfx) {
    const gmapsInput = document.getElementById(pfx + '_google_maps_link');
    if (!gmapsInput || !gmapsInput.value) return;
    const val = gmapsInput.value.trim();
    const m = val.match(/[?&]q=([-\d\.]+),([-\d\.]+)/) || val.match(/@([-\d\.]+),([-\d\.]+)/);
    if (m) {
        const lat = parseFloat(m[1]);
        const lng = parseFloat(m[2]);
        if (!isNaN(lat) && !isNaN(lng)) {
            setEventCoords(pfx, lat, lng);
        }
    }
}

function setEventCoords(pfx, lat, lng, gatheringPoint = null, elevation = null) {
    lat = parseFloat(lat);
    lng = parseFloat(lng);
    if (isNaN(lat) || isNaN(lng)) return;

    if (gatheringPoint) {
        const gpInput = document.getElementById(pfx + '_gathering_point');
        if (gpInput) gpInput.value = gatheringPoint;
    }
    if (elevation !== null) {
        const elevInput = document.getElementById(pfx + '_elevation_masl');
        if (elevInput) elevInput.value = elevation;
    }

    updateAdminCoordInputs(pfx, lat, lng, true);

    if (window.adminMaps[pfx] && window.adminMarkers[pfx]) {
        window.adminMarkers[pfx].setLatLng([lat, lng]);
        window.adminMaps[pfx].flyTo([lat, lng], 14, { duration: 0.8 });
    } else {
        initAdminMap(pfx, lat, lng);
    }
}

function detectAdminCurrentLocation(pfx) {
    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung oleh browser Anda.');
        return;
    }
    const statusEl = document.getElementById(pfx + '_map_coord_status');
    if (statusEl) statusEl.innerText = 'Mencari sinyal GPS...';

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            setEventCoords(pfx, lat, lng);
            alert(`Lokasi GPS terdeteksi!\nLatitude: ${lat.toFixed(6)}\nLongitude: ${lng.toFixed(6)}`);
        },
        (err) => {
            alert('Gagal mendeteksi lokasi GPS: ' + err.message);
            if (statusEl) statusEl.innerText = 'Gagal deteksi GPS';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

function searchAdminMapLocation(pfx) {
    const input = document.getElementById(pfx + '_map_search');
    if (!input || !input.value.trim()) return;
    const query = input.value.trim();

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                setEventCoords(pfx, lat, lng);
                const gpInput = document.getElementById(pfx + '_gathering_point');
                if (gpInput && !gpInput.value) {
                    gpInput.value = data[0].display_name.split(',')[0];
                }
            } else {
                alert('Lokasi "' + query + '" tidak ditemukan. Silakan gunakan nama daerah yang lebih umum atau klik langsung pada peta.');
            }
        })
        .catch(() => {
            alert('Layanan pencarian peta sedang sibuk. Silakan tentukan koordinat atau klik langsung pada peta.');
        });
}

function switchAdminMapLayer(pfx, layerType) {
    if (!window.adminMaps[pfx] || !window.adminMapLayers[pfx]) return;
    const map = window.adminMaps[pfx];
    const layers = window.adminMapLayers[pfx];

    if (layerType === 'satellite') {
        if (map.hasLayer(layers.street)) map.removeLayer(layers.street);
        layers.satellite.addTo(map);
        document.getElementById(pfx + '_btn_layer_sat')?.classList.add('active', 'btn-success');
        document.getElementById(pfx + '_btn_layer_sat')?.classList.remove('btn-outline-secondary');
        document.getElementById(pfx + '_btn_layer_street')?.classList.remove('active', 'btn-success');
        document.getElementById(pfx + '_btn_layer_street')?.classList.add('btn-outline-secondary');
    } else {
        if (map.hasLayer(layers.satellite)) map.removeLayer(layers.satellite);
        layers.street.addTo(map);
        document.getElementById(pfx + '_btn_layer_street')?.classList.add('active', 'btn-success');
        document.getElementById(pfx + '_btn_layer_street')?.classList.remove('btn-outline-secondary');
        document.getElementById(pfx + '_btn_layer_sat')?.classList.remove('active', 'btn-success');
        document.getElementById(pfx + '_btn_layer_sat')?.classList.add('btn-outline-secondary');
    }
}

function onLocationSelectChange(pfx, selectEl) {
    if (!selectEl) return;
    const selected = selectEl.options[selectEl.selectedIndex];
    if (!selected) return;
    const lat = selected.getAttribute('data-lat');
    const lng = selected.getAttribute('data-lng');
    const name = selected.getAttribute('data-name');
    if (lat && lng) {
        setEventCoords(pfx, lat, lng);
        const gp = document.getElementById(pfx + '_gathering_point');
        if (gp && (!gp.value || gp.value.trim() === '')) {
            gp.value = 'Basecamp ' + name;
        }
    }
}

function testGmapsUrl(pfx) {
    const link = document.getElementById(pfx + '_google_maps_link')?.value;
    if (link) {
        window.open(link, '_blank');
    } else {
        alert('Tautan Google Maps belum terisi.');
    }
}

// Invalidate Leaflet size saat tab Rute dibuka agar peta tidak abu-abu
document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function(e) {
        if (e.target.id && e.target.id.includes('route')) {
            const pfx = e.target.id.startsWith('add') ? 'add' : 'edit';
            setTimeout(() => {
                if (window.adminMaps[pfx]) {
                    window.adminMaps[pfx].invalidateSize();
                } else {
                    const lat = document.getElementById(pfx + '_latitude')?.value || -6.9833;
                    const lng = document.getElementById(pfx + '_longitude')?.value || 107.8333;
                    initAdminMap(pfx, lat, lng);
                }
            }, 180);
        }
    });
});

// Inisialisasi awal saat modal Tambah dibuka
const modalTambahEl = document.getElementById('modalTambahEvent');
if (modalTambahEl) {
    modalTambahEl.addEventListener('show.bs.modal', function () {
        renderRundownBuilder('add', true);
        setTimeout(() => {
            initAdminMap('add', -6.9833, 107.8333);
        }, 200);
    });
}

// Inisialisasi saat tombol Edit diklik
document.querySelectorAll('.btn-edit-event').forEach(btn => {
    btn.addEventListener('click', function() {
        const ev = JSON.parse(this.dataset.json);
        const form = document.getElementById('formEditEvent');
        form.action = '/admin/kegiatan/' + ev.id + '/update';
        
        const fields = [
            'title', 'category', 'difficulty_level', 'elevation_masl',
            'event_date', 'gathering_time', 'quota', 'location_id',
            'gathering_point', 'google_maps_link', 'latitude', 'longitude', 'registration_fee',
            'status', 'coordinator_name', 'coordinator_phone',
            'tree_species_info', 'items_to_bring', 'itinerary',
            'description', 'cover_image', 'cancel_deadline',
            'tshirt_included_note'
        ];
        
        fields.forEach(f => {
            const el = document.getElementById('edit_' + f);
            if (el) el.value = ev[f] ?? '';
        });
        
        const tshirtChk = document.getElementById('edit_includes_tshirt');
        if (tshirtChk) {
            tshirtChk.checked = (ev.includes_tshirt == 1);
            toggleTshirtInput('edit');
        }

        // Buka tab pertama saat modal dibuka
        const firstTab = document.getElementById('edit-tab-info');
        if (firstTab) {
            bootstrap.Tab.getOrCreateInstance(firstTab).show();
        }

        // Render baris visual builder untuk edit (tidak memaksa default dummy jika kosong)
        renderRundownBuilder('edit', false);

        // Inisialisasi peta edit dengan koordinat event
        const editLat = parseFloat(ev.latitude) || -6.9833;
        const editLng = parseFloat(ev.longitude) || 107.8333;
        setTimeout(() => {
            initAdminMap('edit', editLat, editLng);
        }, 200);

        new bootstrap.Modal(document.getElementById('modalEditEvent')).show();
    });
});

// Expose fungsi ke scope window global agar inline onclick di button berjalan aman
window.addRundownRow = addRundownRow;
window.addQuickActivity = addQuickActivity;
window.applyRundownTemplate = applyRundownTemplate;
window.toggleRundownViewMode = toggleRundownViewMode;
window.syncRundownToTextarea = syncRundownToTextarea;
window.renderRundownBuilder = renderRundownBuilder;
window.setCategory = setCategory;
window.setFee = setFee;
window.setElevation = setElevation;
window.setQuota = setQuota;
window.addQuickItem = addQuickItem;
window.setCancelDeadlineOffset = setCancelDeadlineOffset;
window.toggleTshirtInput = toggleTshirtInput;
window.nextModalTab = nextModalTab;

// Map exports
window.initAdminMap = initAdminMap;
window.updateAdminCoordInputs = updateAdminCoordInputs;
window.onCoordInputChange = onCoordInputChange;
window.parseGmapsUrlToCoords = parseGmapsUrlToCoords;
window.setEventCoords = setEventCoords;
window.detectAdminCurrentLocation = detectAdminCurrentLocation;
window.searchAdminMapLocation = searchAdminMapLocation;
window.switchAdminMapLayer = switchAdminMapLayer;
window.onLocationSelectChange = onLocationSelectChange;
window.testGmapsUrl = testGmapsUrl;
</script>
