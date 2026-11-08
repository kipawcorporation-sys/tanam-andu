<?php
/**
 * View: admin/participants.php — Manajemen Peserta Kegiatan & Presensi Digital (Pro)
 */

$statusOptions = [
    'menunggu'      => ['Menunggu',       'bg-warning-subtle text-warning-emphasis', 'bi-clock-fill'],
    'terkonfirmasi' => ['Terkonfirmasi',  'bg-primary-subtle text-primary',          'bi-check-circle-fill'],
    'hadir'         => ['Hadir',          'bg-success-subtle text-success',          'bi-person-check-fill'],
    'batal'         => ['Batal',          'bg-danger-subtle text-danger',            'bi-x-circle-fill'],
];

$totalAll     = $stats['total'] ?? $total ?? 0;
$hadirCount   = $stats['hadir'] ?? 0;
$konfirmCount = $stats['terkonfirmasi'] ?? 0;
$menungguCount= $stats['menunggu'] ?? 0;
$batalCount   = $stats['batal'] ?? 0;
$pctHadir     = $totalAll > 0 ? round(($hadirCount / $totalAll) * 100, 1) : 0;
?>

<style>
/* ── Hero ── */
.part-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 55%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(6, 21, 16, 0.2);
}
.part-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, transparent 70%);
    pointer-events: none;
}
.part-hero h4 {
    color: #fff;
    font-weight: 800;
    font-size: 1.45rem;
    margin: 0 0 4px;
}
.part-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    margin: 0;
}

/* ── KPI Row ── */
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

/* ── Logistics Tshirt Row ── */
.tshirt-bar {
    background: #fff;
    border-radius: 14px;
    padding: 12px 20px;
    margin-bottom: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.tshirt-chip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.tshirt-chip strong {
    color: #15803d;
}

/* ── Filter Bar ── */
.filter-bar {
    background: #fff;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

/* ── Table Card ── */
.part-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    overflow: hidden;
}
.part-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.part-table thead th {
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
.part-table td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.part-table tbody tr:hover {
    background: #f8faf8;
}

/* ── Avatar Circle ── */
.part-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #15803d, #22c55e);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.88rem;
    flex-shrink: 0;
}
.ticket-code-pill {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.74rem;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #dbeafe;
    display: inline-block;
}

/* ── Buttons ── */
.btn-action-qr {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    border: none;
    padding: 9px 20px;
    border-radius: 12px;
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    transition: all 0.2s;
}
.btn-action-qr:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
    color: #fff;
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
<div class="part-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white bg-opacity-15 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <i class="bi bi-people-fill me-1"></i>PRESENSI &amp; TIKET EXPEDITION
                </span>
                <span class="badge bg-success bg-opacity-75 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <?= $hadirCount ?> Peserta Hadir (<?= $pctHadir ?>%)
                </span>
            </div>
            <h4><i class="bi bi-people me-2"></i>Manajemen Peserta &amp; Presensi Lapangan</h4>
            <p>Kelola registrasi peserta ekspedisi penanaman, log kehadiran QR code, verifikasi kaos, dan data kontak darurat.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/admin/peserta/export-csv" class="btn-hero-action" title="Unduh data peserta format CSV">
                <i class="bi bi-file-earmark-spreadsheet"></i>Unduh CSV
            </a>
            <a href="/admin/checkin-qr" class="btn-action-qr">
                <i class="bi bi-qr-code-scan"></i>Kamera QR Scanner
            </a>
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
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-dark"><?= number_format($totalAll) ?></div>
            <div class="kpi-lbl">Total Peserta</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#16a34a;">
            <i class="bi bi-person-check-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-success"><?= number_format($hadirCount) ?></div>
            <div class="kpi-lbl">Sudah Hadir</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-primary"><?= number_format($konfirmCount) ?></div>
            <div class="kpi-lbl">Terkonfirmasi</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#ea580c;"><?= number_format($menungguCount) ?></div>
            <div class="kpi-lbl">Menunggu</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
            <i class="bi bi-pie-chart-fill"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#9333ea;"><?= $pctHadir ?>%</div>
            <div class="kpi-lbl">Kehadiran</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     LOGISTIK KAOS EKSPEDISI
══════════════════════════════════════════════════════════════ -->
<?php if (!empty($tshirtStats)): ?>
<div class="tshirt-bar shadow-sm">
    <span class="text-muted small fw-bold"><i class="bi bi-bag-check text-success me-1"></i>Distribusi Ukuran Kaos:</span>
    <?php foreach ($tshirtStats as $ts): ?>
        <span class="tshirt-chip">
            Ukuran <?= htmlspecialchars($ts['tshirt_size']) ?>: <strong><?= $ts['count'] ?> pcs</strong>
        </span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════
     FILTER & SEARCH BAR
══════════════════════════════════════════════════════════════ -->
<div class="filter-bar">
    <div class="input-group input-group-sm" style="max-width:280px;">
        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" id="part-live-search" class="form-control border-start-0" 
               placeholder="Cari nama, email, tiket..." oninput="filterParticipantRows(this.value)">
    </div>
    <form class="d-flex gap-2 mb-0 flex-wrap align-items-center" method="GET">
        <select name="event_id" class="form-select form-select-sm" style="min-width:260px; border-radius:8px;" onchange="this.form.submit()">
            <option value="">Semua Kegiatan Penanaman</option>
            <?php foreach ($events as $ev): ?>
                <option value="<?= $ev['id'] ?>" <?= $eventFilter == $ev['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(mb_substr($ev['title'], 0, 42)) ?> (<?= date('d M Y', strtotime($ev['event_date'])) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="form-select form-select-sm" style="min-width:160px; border-radius:8px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <?php foreach ($statusOptions as $v => [$l, $c, $i]): ?>
                <option value="<?= $v ?>" <?= $statusFilter === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($eventFilter || $statusFilter): ?>
            <a href="/admin/peserta" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                <i class="bi bi-x-circle"></i>
            </a>
        <?php endif; ?>
    </form>
    <div class="ms-auto text-muted small font-mono" id="part-count-label">
        <?= number_format($total) ?> peserta terdaftar
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     TABLE CARD
══════════════════════════════════════════════════════════════ -->
<div class="part-card">
    <div class="part-card-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-success"></i>
            <span class="fw-bold text-dark">Data Peserta &amp; Status Presensi</span>
        </div>
        <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table part-table align-middle mb-0" id="participantTable">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Peserta</th>
                    <th>Kegiatan &amp; Jadwal</th>
                    <th>Status</th>
                    <th>Kaos</th>
                    <th>Kode Tiket</th>
                    <th>Waktu Presensi</th>
                    <th style="width: 150px;" class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="participantTableBody">
            <?php if (empty($participants)): ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold">Tidak ada data peserta ditemukan</div>
                            <small class="text-muted">Coba ubah filter kegiatan atau status</small>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($participants as $idx => $p):
                [$stLabel, $stBadge, $stIcon] = $statusOptions[$p['registration_status']] ?? [$p['registration_status'], 'bg-light text-dark', 'bi-dash'];
                $initial = strtoupper(mb_substr($p['participant_name'] ?? 'P', 0, 1));
            ?>
                <tr class="part-row"
                    data-name="<?= strtolower(htmlspecialchars($p['participant_name'] ?? '')) ?>"
                    data-email="<?= strtolower(htmlspecialchars($p['email'] ?? '')) ?>"
                    data-phone="<?= strtolower(htmlspecialchars($p['phone'] ?? '')) ?>"
                    data-ticket="<?= strtolower(htmlspecialchars($p['ticket_code'] ?? '')) ?>">
                    <td>
                        <div class="part-avatar"><?= $initial ?></div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['participant_name']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($p['email']) ?></div>
                        <?php if (!empty($p['phone'])): ?>
                            <div class="text-muted small">
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $p['phone']) ?>" target="_blank" class="text-decoration-none text-success">
                                    <i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($p['phone']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark small"><?= htmlspecialchars(mb_substr($p['event_title'], 0, 36)) ?></div>
                        <div class="text-muted" style="font-size:11px;">
                            <i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($p['event_date'])) ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?= $stBadge ?> rounded-pill px-3 py-1 font-mono" style="font-size:11px;">
                            <i class="bi <?= $stIcon ?> me-1"></i><?= $stLabel ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($p['tshirt_size'])): ?>
                            <span class="badge bg-light text-dark border font-mono">
                                <i class="bi bi-tag-fill text-primary me-1"></i><?= htmlspecialchars($p['tshirt_size']) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($p['ticket_code'])): ?>
                            <span class="ticket-code-pill"><?= htmlspecialchars($p['ticket_code']) ?></span>
                            <div>
                                <span class="badge <?= $p['ticket_status'] === 'used' ? 'bg-secondary' : 'bg-success' ?> mt-1" style="font-size:9px;">
                                    <?= strtoupper($p['ticket_status']) ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($p['checked_in_at'])): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check2-circle me-1"></i><?= date('d M H:i', strtotime($p['checked_in_at'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i>Belum Hadir</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <?php if ($p['registration_status'] !== 'hadir'): ?>
                                <form action="/admin/peserta/<?= $p['id'] ?>/checkin" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tandai peserta \'<?= htmlspecialchars(addslashes($p['participant_name'])) ?>\' sebagai HADIR?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-success" title="Tandai Hadir Manual">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($p['registration_status'] === 'menunggu'): ?>
                                <form action="/admin/peserta/<?= $p['id'] ?>/konfirmasi" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-primary" title="Konfirmasi Pendaftaran">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="showParticipantDetail(<?= htmlspecialchars(json_encode($p)) ?>)"
                                    title="Lihat Detail &amp; Tiket">
                                <i class="bi bi-eye"></i>
                            </button>

                            <?php if ($p['registration_status'] !== 'batal'): ?>
                                <form action="/admin/peserta/<?= $p['id'] ?>/batal" method="POST" class="d-inline"
                                      onsubmit="return confirm('Batalkan pendaftaran peserta ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Batalkan Pendaftaran">
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
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
            <small class="text-muted">Menampilkan <?= count($participants) ?> dari <?= number_format($total) ?> peserta</small>
            <div class="btn-group btn-group-sm">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?event_id=<?= $eventFilter ?>&status=<?= urlencode($statusFilter) ?>&page=<?= $i ?>"
                       class="btn <?= $i === $page ? 'btn-success' : 'btn-outline-secondary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL DETAIL PESERTA
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetailPeserta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <div class="p-4 text-white" style="background: linear-gradient(135deg, #061510 0%, #12382c 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-white bg-opacity-20 text-white font-mono" id="mdl-ticket-code">TICKET-CODE</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <h5 class="fw-bold mb-1" id="mdl-name">Nama Peserta</h5>
                <div class="text-white-50 small" id="mdl-email">email@example.com</div>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="bg-white p-3 rounded-3 border mb-3">
                    <div class="small text-muted mb-1">Kegiatan Ekspedisi</div>
                    <div class="fw-bold text-dark" id="mdl-event-title">-</div>
                    <div class="text-muted small" id="mdl-event-date">-</div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="bg-white p-3 rounded-3 border text-center">
                            <div class="text-muted small">Status</div>
                            <div class="fw-bold mt-1" id="mdl-status">-</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white p-3 rounded-3 border text-center">
                            <div class="text-muted small">Ukuran Kaos</div>
                            <div class="fw-bold mt-1" id="mdl-tshirt">-</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-3 border mb-3">
                    <div class="small text-muted mb-1">Waktu Presensi Masuk</div>
                    <div class="fw-bold text-dark font-mono" id="mdl-checkin-time">-</div>
                </div>
                <div class="bg-white p-3 rounded-3 border" id="mdl-notes-wrap">
                    <div class="small text-muted mb-1">Catatan Peserta / Medis</div>
                    <div class="text-secondary small" id="mdl-notes">-</div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showParticipantDetail(p) {
    document.getElementById('mdl-ticket-code').textContent = p.ticket_code || 'BELUM ADA TIKET';
    document.getElementById('mdl-name').textContent = p.participant_name || 'Peserta';
    document.getElementById('mdl-email').textContent = (p.email || '') + (p.phone ? ' • ' + p.phone : '');
    document.getElementById('mdl-event-title').textContent = p.event_title || '-';
    document.getElementById('mdl-event-date').textContent = p.event_date ? 'Jadwal: ' + p.event_date : '-';
    document.getElementById('mdl-status').textContent = (p.registration_status || '').toUpperCase();
    document.getElementById('mdl-tshirt').textContent = p.tshirt_size ? ('Size ' + p.tshirt_size) : 'Tidak pilih';
    document.getElementById('mdl-checkin-time').textContent = p.checked_in_at ? ('✓ Hadir pada ' + p.checked_in_at) : 'Belum Check-in';
    document.getElementById('mdl-notes').textContent = p.notes || 'Tidak ada catatan khusus.';
    new bootstrap.Modal(document.getElementById('modalDetailPeserta')).show();
}

function filterParticipantRows(query) {
    const q = query.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('#participantTableBody .part-row').forEach(row => {
        const name   = row.dataset.name || '';
        const email  = row.dataset.email || '';
        const phone  = row.dataset.phone || '';
        const ticket = row.dataset.ticket || '';
        const match  = !q || name.includes(q) || email.includes(q) || phone.includes(q) || ticket.includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('part-count-label').textContent = visible + ' peserta ditemukan';
}
</script>
