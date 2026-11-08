<?php
/**
 * View: admin/qr_checkin.php — TanamPohon.id
 * QR Scanner check-in peserta — tampilan profesional dengan dashboard live stats,
 * riwayat real-time, daftar peserta belum hadir, dan pencarian manual.
 */
$todayCheckin        = $todayCheckin        ?? 0;
$totalCheckin        = $totalCheckin        ?? 0;
$totalConfirmed      = $totalConfirmed      ?? 0;
$totalHadir          = $totalHadir          ?? 0;
$recentCheckins      = $recentCheckins      ?? [];
$pendingParticipants = $pendingParticipants ?? [];
$upcomingEvents      = $upcomingEvents      ?? [];
$hadir_pct = $totalConfirmed > 0 ? round(($totalHadir / ($totalConfirmed + $totalHadir)) * 100) : 0;
?>

<style>
/* ── QR Scanner Page ── */
.qr-hero {
    background: linear-gradient(135deg, #061510 0%, #0a1f17 50%, #0d2b1e 100%);
    border-radius: 20px;
    padding: 24px 28px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.qr-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(46,204,113,.18) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

/* KPI cards */
.qr-kpi {
    background: #fff;
    border-radius: 16px;
    padding: 18px 20px;
    border: 1.5px solid #e9f5ee;
    transition: box-shadow .2s, transform .2s;
    position: relative;
    overflow: hidden;
}
.qr-kpi:hover { box-shadow: 0 8px 24px rgba(21,128,61,.1); transform: translateY(-2px); }
.qr-kpi .kpi-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.qr-kpi .kpi-val { font-size: 2rem; font-weight: 800; line-height: 1; }

/* Scanner box */
.scanner-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 4/3;
    max-width: 480px;
    background: #0a1a12;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,.35);
    margin: 0 auto;
}
.scanner-video {
    width: 100%; height: 100%;
    object-fit: cover;
    display: none;
}
.scanner-idle {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #fff; gap: 12px;
}
.scanner-idle .idle-icon {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(46,204,113,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: #2ecc71;
    animation: pulseGlow 2.5s ease-in-out infinite;
}
@keyframes pulseGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(46,204,113,.4); }
    50%      { box-shadow: 0 0 0 20px rgba(46,204,113,0); }
}

/* Viewfinder overlay */
.vf-overlay {
    position: absolute; inset: 0;
    pointer-events: none;
    display: none;
}
.vf-frame {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 200px; height: 200px;
}
/* Corner brackets */
.vf-frame::before, .vf-frame::after,
.vf-frame > span::before, .vf-frame > span::after {
    content: '';
    position: absolute;
    width: 28px; height: 28px;
    border-color: #2ecc71;
    border-style: solid;
}
.vf-frame::before  { top: 0; left: 0;  border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.vf-frame::after   { top: 0; right: 0; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
.vf-frame > span::before { bottom: 0; left: 0;  border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
.vf-frame > span::after  { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }
/* Dark vignette */
.vf-shade {
    position: absolute; inset: 0;
    box-shadow: inset 0 0 0 1000px rgba(0,0,0,.45);
}
/* Laser line */
.vf-laser {
    position: absolute;
    left: 50%; top: 50%;
    transform: translate(-50%, -50%);
    width: 194px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #2ecc71 30%, #2ecc71 70%, transparent);
    box-shadow: 0 0 10px #2ecc71, 0 0 20px rgba(46,204,113,.5);
    animation: laserMove 2s ease-in-out infinite;
}
@keyframes laserMove {
    0%,100% { margin-top: -97px; }
    50%      { margin-top:  97px; }
}

/* Scan canvas (hidden) */
#qrCanvas { display: none; }

/* Result card */
.result-card {
    border-radius: 16px;
    padding: 20px;
    display: none;
    animation: fadeSlideIn .3s ease;
}
@keyframes fadeSlideIn {
    from { opacity:0; transform:translateY(10px); }
    to   { opacity:1; transform:translateY(0); }
}
.result-card.show { display: block; }

/* Log list */
.log-list { max-height: 320px; overflow-y: auto; }
.log-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: background .15s;
}
.log-item:hover { background: #f8fffe; }
.log-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Pending table */
.pending-table-wrap { max-height: 340px; overflow-y: auto; }
.pending-table td, .pending-table th { vertical-align: middle; white-space: nowrap; }

/* Status badge */
.badge-hadir    { background: #dcfce7; color: #15803d; }
.badge-warning  { background: #fef9c3; color: #854d0e; }
.badge-danger   { background: #fee2e2; color: #991b1b; }

/* Event selector */
.event-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    border: 1.5px solid #d1fae5;
    background: #f0fdf4;
    font-size: 13px; font-weight: 600; color: #15803d;
    cursor: pointer; transition: all .2s;
}
.event-pill:hover, .event-pill.active {
    background: #15803d; color: #fff; border-color: #15803d;
}

/* Sound toggle */
.sound-btn { transition: all .2s; }
.sound-btn.muted { opacity: .45; }

/* Progress ring */
.ring-svg { transform: rotate(-90deg); }
.ring-bg { fill: none; stroke: #e9ecef; stroke-width: 6; }
.ring-fg { fill: none; stroke: #22c55e; stroke-width: 6; stroke-linecap: round; transition: stroke-dashoffset .6s ease; }
</style>

<!-- ── HERO ── -->
<div class="qr-hero mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill fw-semibold"
                      style="background:rgba(46,204,113,.18);color:#2ecc71;font-size:11px;letter-spacing:.5px;">
                    LIVE SCANNER
                </span>
            </div>
            <h4 class="text-white fw-bold mb-1">
                <i class="bi bi-qr-code-scan me-2 text-success"></i>QR Check-in Peserta
            </h4>
            <p class="text-white-50 mb-0 small">
                Pindai tiket QR atau ketik kode manual — konfirmasi kehadiran instan
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url('/admin/peserta') ?>" class="btn btn-sm btn-outline-light rounded-pill">
                <i class="bi bi-people me-1"></i>Daftar Peserta
            </a>
            <a href="<?= url('/admin/event') ?>" class="btn btn-sm btn-outline-light rounded-pill">
                <i class="bi bi-calendar-event me-1"></i>Kelola Event
            </a>
            <button id="soundToggle" class="btn btn-sm btn-outline-success rounded-pill sound-btn" title="Toggle suara">
                <i class="bi bi-volume-up-fill"></i>
            </button>
        </div>
    </div>
</div>

<!-- ── KPI STATS ── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="qr-kpi h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#dcfce7;">
                    <i class="bi bi-person-check-fill text-success"></i>
                </div>
                <div>
                    <div class="kpi-val text-success" id="statToday"><?= $todayCheckin ?></div>
                    <div class="text-muted small fw-semibold">Hadir Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="qr-kpi h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#dbeafe;">
                    <i class="bi bi-clipboard2-check-fill text-primary"></i>
                </div>
                <div>
                    <div class="kpi-val text-primary" id="statTotal"><?= $totalCheckin ?></div>
                    <div class="text-muted small fw-semibold">Total Check-in</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="qr-kpi h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#fef9c3;">
                    <i class="bi bi-hourglass-split text-warning"></i>
                </div>
                <div>
                    <div class="kpi-val text-warning"><?= $totalConfirmed ?></div>
                    <div class="text-muted small fw-semibold">Belum Hadir</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="qr-kpi h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="kpi-val text-dark"><?= $hadir_pct ?>%</div>
                    <div class="text-muted small fw-semibold">Tingkat Kehadiran</div>
                </div>
                <svg class="ring-svg" width="52" height="52" viewBox="0 0 52 52">
                    <circle class="ring-bg" cx="26" cy="26" r="20"/>
                    <?php
                    $circ = 2 * M_PI * 20;
                    $off  = $circ * (1 - $hadir_pct / 100);
                    ?>
                    <circle class="ring-fg" cx="26" cy="26" r="20"
                        stroke-dasharray="<?= round($circ) ?>"
                        stroke-dashoffset="<?= round($off) ?>"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- ── MAIN GRID ── -->
<div class="row g-4">

    <!-- ── KOLOM KIRI: Scanner ── -->
    <div class="col-lg-6 d-flex flex-column gap-4">

        <!-- Scanner Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-camera-video-fill text-success me-2"></i>Pemindai Kamera
                </h6>
                <span id="camStatusBadge" class="badge bg-secondary rounded-pill">Nonaktif</span>
            </div>
            <div class="card-body p-4">

                <!-- Viewport -->
                <div class="scanner-wrap mb-3">
                    <video id="qrVideo" class="scanner-video" autoplay playsinline muted></video>
                    <canvas id="qrCanvas"></canvas>

                    <!-- Idle placeholder -->
                    <div id="scannerIdle" class="scanner-idle">
                        <div class="idle-icon"><i class="bi bi-qr-code"></i></div>
                        <div class="fw-bold fs-6">Kamera Belum Aktif</div>
                        <p class="small text-white-50 text-center mb-0 px-4">
                            Klik <strong>Mulai Scan</strong> lalu arahkan kamera ke QR tiket peserta
                        </p>
                    </div>

                    <!-- Viewfinder -->
                    <div id="vfOverlay" class="vf-overlay">
                        <div class="vf-shade"></div>
                        <div class="vf-frame"><span></span></div>
                        <div class="vf-laser"></div>
                    </div>
                </div>

                <!-- Scan status alert -->
                <div id="scanStatus" class="alert d-none py-2 px-3 small rounded-3 mb-3"></div>

                <!-- Camera controls -->
                <div class="d-flex gap-2 mb-3">
                    <button id="btnStart" class="btn btn-success flex-grow-1 fw-semibold rounded-3">
                        <i class="bi bi-play-circle-fill me-1"></i>Mulai Scan
                    </button>
                    <button id="btnStop" class="btn btn-outline-secondary flex-grow-1 fw-semibold rounded-3 d-none">
                        <i class="bi bi-stop-circle me-1"></i>Hentikan
                    </button>
                    <button id="btnFlip" class="btn btn-outline-secondary rounded-3 d-none" title="Ganti kamera">
                        <i class="bi bi-camera-video-off"></i>
                    </button>
                </div>

                <!-- Manual input -->
                <div class="border-top pt-3">
                    <label class="form-label small fw-semibold text-muted mb-2">
                        <i class="bi bi-keyboard me-1"></i>Input Kode Tiket Manual
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-ticket-detailed text-muted"></i>
                        </span>
                        <input type="text" id="manualCode" class="form-control border-start-0 border-end-0"
                               placeholder="TKT-202611-XXXX..." maxlength="30"
                               style="text-transform:uppercase; letter-spacing:.5px;">
                        <button id="btnManual" class="btn btn-success px-3" type="button">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                    <div class="form-text">Tekan Enter atau klik kirim untuk proses check-in manual</div>
                </div>
            </div>
        </div>

        <!-- Result card -->
        <div id="resultBox" class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div id="resultInner" class="card-body p-0"></div>
        </div>

        <!-- Upcoming Events -->
        <?php if (!empty($upcomingEvents)): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar2-event-fill text-primary me-2"></i>Kegiatan Mendatang
                </h6>
            </div>
            <div class="card-body p-3 d-flex flex-column gap-2">
                <?php foreach ($upcomingEvents as $ev): ?>
                <div class="d-flex align-items-center gap-3 p-2 rounded-3 border border-light">
                    <div class="text-center flex-shrink-0" style="min-width:44px;">
                        <div class="fw-bold text-success" style="font-size:18px;line-height:1;"><?= date('d', strtotime($ev['event_date'])) ?></div>
                        <div class="text-muted" style="font-size:10px;text-transform:uppercase;"><?= date('M', strtotime($ev['event_date'])) ?></div>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-dark small text-truncate"><?= htmlspecialchars($ev['title']) ?></div>
                        <div class="text-muted" style="font-size:11px;">
                            <?= htmlspecialchars($ev['location_name'] ?? '') ?>
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="badge rounded-pill fw-semibold"
                             style="background:#dcfce7;color:#15803d;font-size:11px;">
                            <?= $ev['registered_count'] ?>/<?= $ev['quota'] ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── KOLOM KANAN: Log & Daftar Peserta ── -->
    <div class="col-lg-6 d-flex flex-column gap-4">

        <!-- Riwayat Scan Sesi Ini -->
        <div class="card border-0 shadow-sm rounded-4 h-auto">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history text-success me-2"></i>Log Sesi Ini
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill" id="logCount">0</span>
                    <button id="btnClearLog" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2"
                            style="font-size:11px;" title="Bersihkan log">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
            <div class="log-list" id="logList">
                <div id="logEmpty" class="text-center py-5 text-muted">
                    <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-25"></i>
                    <small>Belum ada aktivitas scan dalam sesi ini</small>
                </div>
            </div>
        </div>

        <!-- Riwayat Check-in dari DB -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i>Riwayat Check-in (DB)
                </h6>
                <span class="badge bg-primary rounded-pill"><?= count($recentCheckins) ?></span>
            </div>
            <?php if (empty($recentCheckins)): ?>
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                    <small>Belum ada yang check-in</small>
                </div>
            <?php else: ?>
                <div class="log-list">
                    <?php foreach ($recentCheckins as $ci): ?>
                    <div class="log-item">
                        <div class="log-dot bg-success"></div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-truncate"><?= htmlspecialchars($ci['participant_name']) ?></div>
                            <div class="text-muted" style="font-size:11px;">
                                <?= htmlspecialchars($ci['event_title']) ?>
                                <?php if ($ci['tshirt_size']): ?>
                                · <span class="badge bg-primary-subtle text-primary rounded-pill"><?= htmlspecialchars($ci['tshirt_size']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="font-monospace text-muted" style="font-size:11px;">
                                <?= $ci['checked_in_at'] ? date('H:i', strtotime($ci['checked_in_at'])) : '-' ?>
                            </div>
                            <code style="font-size:10px;"><?= htmlspecialchars($ci['ticket_code'] ?? '-') ?></code>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Peserta Terkonfirmasi Belum Hadir -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center gap-2">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>Belum Hadir
                </h6>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <input type="text" id="pendingSearch" class="form-control form-control-sm"
                           style="max-width:160px;" placeholder="Cari nama / tiket...">
                    <span class="badge bg-warning text-dark rounded-pill"><?= count($pendingParticipants) ?></span>
                </div>
            </div>
            <?php if (empty($pendingParticipants)): ?>
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-check2-all fs-2 d-block mb-2 text-success opacity-50"></i>
                    <small>Semua peserta sudah hadir! 🎉</small>
                </div>
            <?php else: ?>
                <div class="pending-table-wrap">
                    <table class="table table-sm table-hover mb-0 pending-table">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="font-size:12px;">Peserta</th>
                                <th style="font-size:12px;">Tiket</th>
                                <th style="font-size:12px;">Kaos</th>
                                <th style="font-size:12px;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pendingBody">
                            <?php foreach ($pendingParticipants as $pp): ?>
                            <tr class="pending-row"
                                data-name="<?= strtolower(htmlspecialchars($pp['participant_name'])) ?>"
                                data-code="<?= strtolower(htmlspecialchars($pp['ticket_code'] ?? '')) ?>">
                                <td>
                                    <div class="fw-semibold small"><?= htmlspecialchars($pp['participant_name']) ?></div>
                                    <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars($pp['event_title']) ?></div>
                                </td>
                                <td>
                                    <code style="font-size:11px;"><?= htmlspecialchars($pp['ticket_code'] ?? '-') ?></code>
                                </td>
                                <td>
                                    <?php if ($pp['tshirt_size']): ?>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill"><?= htmlspecialchars($pp['tshirt_size']) ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-xs btn-success py-0 px-2 rounded-pill btn-quick-checkin"
                                            style="font-size:11px;"
                                            data-code="<?= htmlspecialchars($pp['ticket_code'] ?? '') ?>"
                                            data-name="<?= htmlspecialchars($pp['participant_name']) ?>">
                                        <i class="bi bi-check2"></i> Check-in
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- /col-right -->
</div><!-- /row -->

<!-- jsQR -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
/* ── State ── */
let stream      = null;
let scanLoop    = null;
let lastCode    = '';
let lastCodeTs  = 0;
let logCount    = 0;
let soundOn     = true;
let useFront    = false;

/* ── DOM refs ── */
const video       = document.getElementById('qrVideo');
const canvas      = document.getElementById('qrCanvas');
const ctx         = canvas.getContext('2d');
const scannerIdle = document.getElementById('scannerIdle');
const vfOverlay   = document.getElementById('vfOverlay');
const scanStatus  = document.getElementById('scanStatus');
const resultBox   = document.getElementById('resultBox');
const resultInner = document.getElementById('resultInner');
const logList     = document.getElementById('logList');
const logEmpty    = document.getElementById('logEmpty');
const logCountEl  = document.getElementById('logCount');
const manualInput = document.getElementById('manualCode');
const statToday   = document.getElementById('statToday');
const statTotal   = document.getElementById('statTotal');
const camBadge    = document.getElementById('camStatusBadge');

/* ── Buttons ── */
document.getElementById('btnStart').addEventListener('click', startCamera);
document.getElementById('btnStop').addEventListener('click', stopCamera);
document.getElementById('btnFlip').addEventListener('click', flipCamera);
document.getElementById('btnManual').addEventListener('click', () => {
    const v = manualInput.value.trim().toUpperCase();
    if (v) processCheckin(v);
});
manualInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') { const v = manualInput.value.trim().toUpperCase(); if (v) processCheckin(v); }
});
document.getElementById('btnClearLog').addEventListener('click', () => {
    logList.innerHTML = '';
    logList.insertAdjacentHTML('beforeend',
        '<div id="logEmpty" class="text-center py-5 text-muted"><i class="bi bi-clock-history fs-2 d-block mb-2 opacity-25"></i><small>Log dibersihkan</small></div>');
    logCount = 0; logCountEl.textContent = '0';
});
document.getElementById('soundToggle').addEventListener('click', function() {
    soundOn = !soundOn;
    this.innerHTML = soundOn ? '<i class="bi bi-volume-up-fill"></i>' : '<i class="bi bi-volume-mute-fill"></i>';
    this.classList.toggle('muted', !soundOn);
});

/* Quick check-in dari tabel pending */
document.querySelectorAll('.btn-quick-checkin').forEach(btn => {
    btn.addEventListener('click', () => {
        const code = btn.dataset.code;
        const name = btn.dataset.name;
        if (!code) return;
        manualInput.value = code;
        processCheckin(code);
    });
});

/* Filter tabel pending */
document.getElementById('pendingSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.pending-row').forEach(row => {
        const match = row.dataset.name.includes(q) || row.dataset.code.includes(q);
        row.style.display = match ? '' : 'none';
    });
});

/* ── Camera ── */
async function startCamera() {
    try {
        const constraints = {
            video: { facingMode: useFront ? 'user' : 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        };
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = stream;
        video.style.display = 'block';
        scannerIdle.style.display = 'none';
        vfOverlay.style.display = 'block';
        document.getElementById('btnStart').classList.add('d-none');
        document.getElementById('btnStop').classList.remove('d-none');
        document.getElementById('btnFlip').classList.remove('d-none');
        camBadge.textContent = 'Aktif';
        camBadge.className = 'badge bg-success rounded-pill';
        showStatus('info', '<i class="bi bi-camera-video me-1"></i>Kamera aktif — arahkan ke QR tiket peserta');
        scanLoop = setInterval(scanFrame, 200);
    } catch (err) {
        showStatus('danger', '<i class="bi bi-exclamation-triangle me-1"></i>Gagal mengakses kamera: ' + err.message);
    }
}

function stopCamera() {
    clearInterval(scanLoop);
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    video.style.display = 'none';
    video.srcObject = null;
    scannerIdle.style.display = 'flex';
    vfOverlay.style.display = 'none';
    document.getElementById('btnStart').classList.remove('d-none');
    document.getElementById('btnStop').classList.add('d-none');
    document.getElementById('btnFlip').classList.add('d-none');
    camBadge.textContent = 'Nonaktif';
    camBadge.className = 'badge bg-secondary rounded-pill';
    showStatus('secondary', '<i class="bi bi-stop-circle me-1"></i>Kamera dihentikan.');
}

async function flipCamera() {
    useFront = !useFront;
    stopCamera();
    await new Promise(r => setTimeout(r, 300));
    startCamera();
}

/* ── Scan frame ── */
function scanFrame() {
    if (!video.readyState || video.readyState < 2) return;
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const img  = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });
    if (code && code.data) {
        const now = Date.now();
        if (code.data !== lastCode || now - lastCodeTs > 3500) {
            lastCode  = code.data;
            lastCodeTs = now;
            processCheckin(code.data);
        }
    }
}

/* ── Process check-in ── */
async function processCheckin(raw) {
    let code = raw.trim().toUpperCase();
    try { const p = JSON.parse(raw); code = (p.ticket_code || p.code || raw).toUpperCase(); } catch(e){}

    showStatus('info', `<div class="d-flex align-items-center gap-2"><div class="spinner-border spinner-border-sm text-primary"></div><span>Memproses <strong>${escHtml(code)}</strong>…</span></div>`);
    resultInner.innerHTML = '';

    try {
        const fd = new FormData();
        fd.append('ticket_code', code);
        fd.append('csrf_token', '<?= csrf_token() ?>');
        const resp = await fetch('/admin/checkin-qr/proses', { method: 'POST', body: fd });
        const data = await resp.json();

        if (data.success) {
            renderResult('success', data);
            appendLog('success', code, data.data?.participant_name ?? '');
            if (soundOn) playBeep(880);
            manualInput.value = '';
            // Update stat hari ini
            const cur = parseInt(statToday.textContent) || 0;
            statToday.textContent = cur + 1;
            const curT = parseInt(statTotal.textContent) || 0;
            statTotal.textContent = curT + 1;
            // Sembunyikan dari tabel pending
            document.querySelectorAll('.btn-quick-checkin').forEach(btn => {
                if (btn.dataset.code === code) btn.closest('tr').style.opacity = '.35';
            });
        } else if (data.duplicate) {
            renderResult('warning', data);
            appendLog('warning', code, data.data?.participant_name ?? '');
            if (soundOn) playBeep(440);
        } else {
            renderResult('danger', data);
            appendLog('danger', code, '');
            if (soundOn) playBeep(220);
        }
    } catch (err) {
        showStatus('danger', '<i class="bi bi-wifi-off me-1"></i>Gagal terhubung ke server: ' + err.message);
        if (soundOn) playBeep(180);
    }
}

/* ── Render result panel ── */
function renderResult(type, data) {
    const p     = data.data || {};
    const icons = { success: 'check-circle-fill', warning: 'exclamation-triangle-fill', danger: 'x-circle-fill' };
    const bgs   = { success: '#f0fdf4', warning: '#fefce8', danger: '#fef2f2' };
    const borders = { success: '#bbf7d0', warning: '#fde68a', danger: '#fecaca' };
    const colors  = { success: '#15803d', warning: '#854d0e', danger: '#991b1b' };

    resultInner.innerHTML = `
        <div style="background:${bgs[type]};border-left:4px solid ${borders[type]};padding:16px 20px;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-${icons[type]} fs-5" style="color:${colors[type]};"></i>
                <strong style="color:${colors[type]};">${escHtml(data.message)}</strong>
            </div>
            ${p.participant_name ? `
            <div class="row g-2 mt-1">
                <div class="col-sm-6">
                    <div class="bg-white rounded-3 p-3 border border-light">
                        <div class="text-muted small mb-1">Peserta</div>
                        <div class="fw-bold">${escHtml(p.participant_name)}</div>
                        <div class="text-muted small">${escHtml(p.email || '')}</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="bg-white rounded-3 p-3 border border-light">
                        <div class="text-muted small mb-1">Kegiatan</div>
                        <div class="fw-semibold small">${escHtml(p.event_title || '-')}</div>
                        ${p.tshirt_size ? `<span class="badge bg-primary mt-1">Kaos ${escHtml(p.tshirt_size)}</span>` : ''}
                    </div>
                </div>
                <div class="col-12">
                    <div class="bg-white rounded-3 p-2 border border-light d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kode Tiket</span>
                        <code class="fs-6">${escHtml(p.ticket_code || code)}</code>
                    </div>
                </div>
            </div>` : ''}
        </div>`;
    showStatus(type, data.message);
}

/* ── Append log ── */
function appendLog(type, code, name) {
    const el = document.getElementById('logEmpty');
    if (el) el.remove();
    logCount++;
    logCountEl.textContent = logCount;
    const colors = { success: '#22c55e', warning: '#f59e0b', danger: '#ef4444' };
    const labels = { success: 'Berhasil', warning: 'Duplikat', danger: 'Gagal' };
    const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    logList.insertAdjacentHTML('afterbegin', `
        <div class="log-item">
            <div class="log-dot" style="background:${colors[type]};"></div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill fw-semibold" style="background:${colors[type]}22;color:${colors[type]};font-size:10px;">${labels[type]}</span>
                    <code style="font-size:12px;">${escHtml(code)}</code>
                </div>
                ${name ? `<div class="text-dark small fw-semibold mt-1">${escHtml(name)}</div>` : ''}
            </div>
            <div class="text-muted font-monospace flex-shrink-0" style="font-size:11px;">${time}</div>
        </div>`);
}

/* ── Helpers ── */
function showStatus(type, msg) {
    scanStatus.className = `alert alert-${type} py-2 px-3 small rounded-3 mb-3`;
    scanStatus.innerHTML = msg;
    scanStatus.classList.remove('d-none');
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function playBeep(freq) {
    try {
        const ac  = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ac.createOscillator();
        const gain = ac.createGain();
        osc.connect(gain); gain.connect(ac.destination);
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(.4, ac.currentTime);
        gain.gain.exponentialRampToValueAtTime(.001, ac.currentTime + .25);
        osc.start(); osc.stop(ac.currentTime + .25);
    } catch(e) {}
}
</script>
