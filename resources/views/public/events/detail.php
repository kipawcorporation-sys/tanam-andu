<?php
/**
 * View: public/events/detail.php — Halaman Rinci Ekspedisi & Formulir Pendaftaran Relawan.
 */
$diffInfo = [
    'mudah'     => ['bg-success-subtle', '#0a3622', 'bi-shield-check', 'Mudah / Pemula'],
    'sedang'    => ['bg-warning-subtle', '#92400e', 'bi-compass', 'Sedang / Trek Ringan'],
    'menantang' => ['bg-danger-subtle', '#991b1b', 'bi-lightning-charge-fill', 'Menantang / Gunung'],
][$event['difficulty_level'] ?? 'sedang'] ?? ['bg-warning-subtle', '#92400e', 'bi-compass', 'Sedang'];

$isFull = ($event['registered_count'] >= $event['quota']);
$perc = ($event['quota'] > 0) ? min(100, round(($event['registered_count'] / $event['quota']) * 100)) : 0;
$remainingSeats = max(0, $event['quota'] - $event['registered_count']);

$coverImg = !empty($event['cover_image']) 
    ? asset($event['cover_image']) 
    : (!empty($event['location_photo']) ? asset($event['location_photo']) : asset('assets/images/plant-for-fun-kerenceng.jpg'));

// Koordinat Presisi Titik Kumpul
$eventLat = !empty($event['latitude']) ? (float)$event['latitude'] : -6.9833;
$eventLng = !empty($event['longitude']) ? (float)$event['longitude'] : 107.8333;
if (empty($event['latitude']) && !empty($event['google_maps_link'])) {
    if (preg_match('/[?&]q=([-\d\.]+),([-\d\.]+)/', $event['google_maps_link'], $m) || preg_match('/@([-\d\.]+),([-\d\.]+)/', $event['google_maps_link'], $m)) {
        $eventLat = (float)$m[1];
        $eventLng = (float)$m[2];
    }
}
?>

<!-- Leaflet CSS for Professional Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
/* Expedition Detail Styles */
.exp-detail-header {
    background: #fff;
    border-radius: 24px;
    padding: 32px;
    border: 1px solid rgba(18,56,44,0.08);
    box-shadow: 0 4px 20px rgba(18,56,44,0.04);
    margin-bottom: 28px;
}

/* Professional Public Map Styles */
.exp-map-container {
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}
#publicEventMap {
    height: 380px;
    width: 100%;
    z-index: 1;
}
.map-pulse-marker {
    position: relative;
    width: 44px;
    height: 44px;
}
.map-pulse-pin {
    width: 40px;
    height: 40px;
    border-radius: 50% 50% 50% 0;
    background: linear-gradient(135deg, #16a34a, #0d532b);
    position: absolute;
    transform: rotate(-45deg);
    left: 2px;
    top: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 14px rgba(22,163,74,0.4);
    border: 2px solid #fff;
}
.map-pulse-pin i {
    transform: rotate(45deg);
    color: #fff;
    font-size: 1.15rem;
}
.map-pulse-ring {
    position: absolute;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(34, 197, 94, 0.4);
    animation: mapPulse 2s infinite ease-out;
    top: 0;
    left: 0;
}
@keyframes mapPulse {
    0% { transform: scale(0.9); opacity: 0.8; }
    100% { transform: scale(2.4); opacity: 0; }
}
.leaflet-popup-content-wrapper {
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    padding: 2px;
}
.leaflet-popup-content {
    margin: 12px 14px;
    line-height: 1.4;
}
.metric-strip-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    text-align: center;
    height: 100%;
}
.metric-strip-val {
    font-size: 1.25rem;
    font-weight: 800;
    color: #12382c;
    font-family: 'Space Mono', monospace;
    margin-bottom: 2px;
}
.metric-strip-lbl {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Timeline Rundown */
.rundown-timeline {
    position: relative;
    padding-left: 32px;
}
.rundown-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 6px;
    bottom: 6px;
    width: 2px;
    background: #22c55e;
}
.rundown-item {
    position: relative;
    margin-bottom: 20px;
}
.rundown-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #12382c;
    border: 2px solid #22c55e;
}
.rundown-time {
    font-family: 'Space Mono', monospace;
    font-weight: 700;
    color: #15803d;
    font-size: 0.85rem;
}

/* Checklist items */
.checklist-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: background 0.15s;
}
.checklist-item:hover {
    background: #f1f5f9;
}
.checklist-item input:checked + span {
    text-decoration: line-through;
    color: #94a3b8;
}

/* Share pill buttons */
.share-btn {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #334155;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s;
}
.share-btn:hover {
    background: #12382c;
    color: #fff;
    border-color: #12382c;
}
</style>

<div class="container py-4 py-md-5">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-success text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/kegiatan') ?>" class="text-success text-decoration-none">Jadwal Ekspedisi</a></li>
            <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 320px;"><?= htmlspecialchars($event['title']) ?></li>
        </ol>
    </nav>

    <!-- Alert jika sudah terdaftar -->
    <?php if ($userRegistered): ?>
        <div class="alert alert-success border-0 shadow-sm p-4 rounded-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background:#dcfce7; border-left: 5px solid #16a34a !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success fs-1 me-3"></i>
                <div>
                    <h5 class="fw-bold mb-1" style="color: #064e1e;">Anda Sudah Terdaftar dalam Ekspedisi Ini!</h5>
                    <p class="small mb-0 text-secondary">E-Ticket ber-QR Code Anda telah aktif dan siap digunakan saat registrasi ulang di titik kumpul.</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('/dashboard/kegiatan') ?>" class="btn btn-success fw-bold rounded-pill px-4">
                    <i class="bi bi-qr-code me-1"></i> Buka E-Ticket Saya
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- HEADER EKSPEDISI -->
    <div class="exp-detail-header">
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
            <span class="badge px-3 py-1 rounded-pill font-mono fw-bold" style="background:#12382c; color:#bfd254;">
                <i class="bi bi-compass me-1"></i> <?= htmlspecialchars($event['category'] ?? 'Agroforestri') ?>
            </span>
            <span class="badge <?= $diffInfo[0] ?> px-3 py-1 rounded-pill font-mono" style="color: <?= $diffInfo[1] ?>;">
                <i class="bi <?= $diffInfo[2] ?> me-1"></i><?= $diffInfo[3] ?>
            </span>
            <span class="badge bg-light text-dark border px-3 py-1 rounded-pill font-mono">
                <i class="bi bi-cloud-arrow-up text-primary me-1"></i><?= number_format($event['elevation_masl'] ?? 1550) ?> mdpl
            </span>
            <?php if ($event['status'] === 'upcoming'): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill font-mono ms-auto" style="color:#0a3622 !important;">
                    <i class="bi bi-check-circle-fill me-1"></i> Pendaftaran Dibuka
                </span>
            <?php else: ?>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill font-mono ms-auto">
                    Arsip Ekspedisi (<?= ucfirst($event['status']) ?>)
                </span>
            <?php endif; ?>
        </div>

        <h1 class="display-6 font-poster text-uppercase fw-bold mb-3" style="color: #12382c; line-height: 1.2;">
            <?= htmlspecialchars($event['title']) ?>
        </h1>

        <div class="d-flex align-items-center gap-3 text-secondary font-mono small flex-wrap mb-4">
            <div>
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                <strong><?= htmlspecialchars($event['gathering_point']) ?></strong>, <?= htmlspecialchars($event['city'] ?? $event['location_name'] ?? 'Bandung') ?>
            </div>
            <div>&bull;</div>
            <div>
                <i class="bi bi-calendar-event text-success me-1"></i>
                <?= date('l, d F Y', strtotime($event['event_date'])) ?>
            </div>
            <div>&bull;</div>
            <div>
                <i class="bi bi-clock text-primary me-1"></i>
                Pukul <?= substr($event['gathering_time'], 0, 5) ?> WIB
            </div>
        </div>

        <!-- Metric Strip Row -->
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <div class="metric-strip-card">
                    <div class="metric-strip-val"><?= date('d M Y', strtotime($event['event_date'])) ?></div>
                    <div class="metric-strip-lbl">Tanggal Ekspedisi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-strip-card">
                    <div class="metric-strip-val"><?= number_format($event['elevation_masl'] ?? 1550) ?> mdpl</div>
                    <div class="metric-strip-lbl">Ketinggian Jalur</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-strip-card">
                    <div class="metric-strip-val text-success">
                        <?= $event['registration_fee'] > 0 ? format_rupiah($event['registration_fee']) : 'Gratis' ?>
                    </div>
                    <div class="metric-strip-lbl">Biaya Registrasi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="metric-strip-card">
                    <div class="metric-strip-val <?= $remainingSeats <= 5 ? 'text-danger' : 'text-dark' ?>">
                        <?= $remainingSeats ?> Kursi
                    </div>
                    <div class="metric-strip-lbl">Sisa Kuota Relawan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT ROW -->
    <div class="row g-4">
        <!-- KOLOM KIRI: DETAIL EKSPEDISI, RUNDOWN, PETA, FASILITAS -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 mb-4 bg-white">
                
                <!-- Poster Banner Foto Ekspedisi -->
                <div class="mb-4 text-center rounded-4 overflow-hidden border" style="border-color: rgba(18,56,44,0.15) !important;">
                    <img src="<?= $coverImg ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="img-fluid w-100" style="max-height: 420px; object-fit: cover;">
                </div>

                <!-- Deskripsi Ekspedisi -->
                <h4 class="fw-bold font-poster text-uppercase mb-3" style="color: #12382c;">
                    Tentang Ekspedisi Penanaman Ini
                </h4>
                <div class="text-secondary lh-lg mb-4" style="font-size: 0.95rem;">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </div>

                <!-- Info Spesies Pohon Target -->
                <?php if (!empty($event['tree_species_info'])): ?>
                    <div class="p-4 rounded-4 mb-4" style="background:#f0fdf4; border: 1.5px solid #bbf7d0;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-tree-fill text-success fs-4"></i>
                            <h5 class="fw-bold font-poster text-uppercase mb-0" style="color: #12382c;">
                                Target Spesies Pohon Konservasi
                            </h5>
                        </div>
                        <p class="text-secondary small mb-0">
                            <?= htmlspecialchars($event['tree_species_info']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Linimasa & Rundown Kegiatan Interaktif -->
                <?php if (!empty($itineraryItems)): ?>
                    <h5 class="fw-bold font-poster text-dark mb-3 border-top pt-4">
                        <i class="bi bi-clock-history text-success me-2"></i>Rundown &amp; Linimasa Ekspedisi
                    </h5>
                    <div class="rundown-timeline mb-4">
                        <?php foreach ($itineraryItems as $it): ?>
                            <div class="rundown-item">
                                <?php if (!empty($it['time'])): ?>
                                    <div class="rundown-time"><?= htmlspecialchars($it['time']) ?></div>
                                <?php endif; ?>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($it['activity']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Peta Titik Kumpul & Navigasi Lapangan Profesional -->
                <h5 class="fw-bold font-poster text-dark mb-3 border-top pt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Titik Kumpul &amp; Peta Navigasi Lapangan
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono small px-3 py-1 rounded-pill">
                        <i class="bi bi-broadcast me-1 text-danger"></i>GPS: <?= number_format($eventLat, 4) ?>, <?= number_format($eventLng, 4) ?>
                    </span>
                </h5>

                <div class="exp-map-container rounded-4 overflow-hidden border mb-4 bg-white shadow-sm">
                    <!-- Map Header Strip -->
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="small text-muted font-mono text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                <i class="bi bi-pin-map-fill text-danger me-1"></i>TITIK TEMU (MEETING POINT)
                            </div>
                            <strong class="text-dark fs-6 d-block"><?= htmlspecialchars($event['gathering_point']) ?></strong>
                            <div class="small text-muted font-mono mt-1">
                                Kawasan: <strong><?= htmlspecialchars($event['location_name'] ?? 'Konservasi Alam') ?></strong> &bull; <?= htmlspecialchars($event['city'] ?? '-') ?>, <?= htmlspecialchars($event['province'] ?? 'Indonesia') ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Layer Switcher Buttons -->
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active fw-bold" id="btnMapStreet" onclick="switchPublicMapLayer('street')">
                                    <i class="bi bi-map me-1"></i>Peta Jalan
                                </button>
                                <button type="button" class="btn btn-outline-secondary fw-bold" id="btnMapSatellite" onclick="switchPublicMapLayer('satellite')">
                                    <i class="bi bi-globe-americas me-1"></i>Satelit
                                </button>
                                <button type="button" class="btn btn-outline-secondary fw-bold" id="btnMapTopo" onclick="switchPublicMapLayer('topo')">
                                    <i class="bi bi-triangle me-1"></i>Topografi
                                </button>
                            </div>
                            <!-- Recenter -->
                            <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 fw-bold" onclick="recenterPublicMap()" title="Pusatkan Peta ke Titik Kumpul">
                                <i class="bi bi-crosshair me-1 text-success"></i>Pusatkan
                            </button>
                        </div>
                    </div>

                    <!-- The Interactive Map Canvas -->
                    <div id="publicEventMap"></div>

                    <!-- Multi-App Direct Navigation Bar -->
                    <div class="p-3 bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <span class="text-muted small fw-bold d-block mb-1" style="font-size: 11px;">
                                    <i class="bi bi-cursor-fill text-primary me-1"></i>BUKA RUTE NAVIGASI LANGSUNG DI APLIKASI:
                                </span>
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Google Maps Navigation -->
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $eventLat ?>,<?= $eventLng ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-success rounded-pill fw-bold font-poster px-3 d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-google"></i> Google Maps
                                    </a>
                                    <!-- Waze Navigation -->
                                    <a href="https://waze.com/ul?ll=<?= $eventLat ?>,<?= $eventLng ?>&navigate=yes" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info rounded-pill fw-bold font-poster px-3 d-inline-flex align-items-center gap-2" style="color: #0891b2; border-color: #0891b2;">
                                        <i class="bi bi-compass"></i> Waze
                                    </a>
                                    <!-- Apple Maps -->
                                    <a href="https://maps.apple.com/?daddr=<?= $eventLat ?>,<?= $eventLng ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-secondary rounded-pill fw-bold font-poster px-3 d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-apple"></i> Apple Maps
                                    </a>
                                    <!-- Copy Coordinates Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-pill fw-bold font-mono px-3 d-inline-flex align-items-center gap-2"
                                            onclick="copyCoordinatesToClipboard('<?= $eventLat ?>, <?= $eventLng ?>')">
                                        <i class="bi bi-clipboard"></i> <span id="copyCoordBtnText">Salin Koordinat GPS</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Field Access & Parking Guidance Pill -->
                            <div class="p-2 px-3 rounded-3 bg-light border text-start" style="max-width: 320px;">
                                <div class="d-flex align-items-center gap-2 small fw-bold text-dark mb-1">
                                    <i class="bi bi-p-square-fill text-primary"></i> Akses &amp; Parkir Lapangan
                                </div>
                                <div class="text-secondary small" style="font-size: 11.5px; line-height: 1.4;">
                                    Area parkir motor &amp; mobil tersedia di titik kumpul. Disarankan tiba 15 menit sebelum jam <strong><?= substr($event['gathering_time'], 0, 5) ?> WIB</strong> untuk registrasi ulang.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Relawan (Included vs Excluded) -->
                <h5 class="fw-bold font-poster text-dark mb-3 border-top pt-4">
                    <i class="bi bi-box-seam text-primary me-2"></i>Fasilitas &amp; Perlengkapan
                </h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light border h-100">
                            <h6 class="fw-bold text-success mb-2" style="font-size: 0.85rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Disediakan Panitia:
                            </h6>
                            <ul class="list-unstyled small text-secondary mb-0">
                                <li class="mb-1"><i class="bi bi-check text-success me-1"></i> Bibit pohon siap tanam</li>
                                <li class="mb-1"><i class="bi bi-check text-success me-1"></i> Cangkul &amp; toolkit penanaman</li>
                                <li class="mb-1"><i class="bi bi-check text-success me-1"></i> Sertifikat digital resmi relawan</li>
                                <li class="mb-1"><i class="bi bi-check text-success me-1"></i> Tim pemandu &amp; tim medis P3K</li>
                                <?php if (!empty($event['includes_tshirt'])): ?>
                                    <li class="mb-1 fw-bold text-dark"><i class="bi bi-gift-fill text-primary me-1"></i> <?= htmlspecialchars($event['tshirt_included_note'] ?: 'Kaos eksklusif relawan') ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light border h-100">
                            <h6 class="fw-bold text-danger mb-2" style="font-size: 0.85rem;">
                                <i class="bi bi-x-circle-fill me-1"></i> Wajib Disiapkan Pribadi:
                            </h6>
                            <ul class="list-unstyled small text-secondary mb-0">
                                <li class="mb-1"><i class="bi bi-dot me-1"></i> Sepatu trekking / bot anti selip</li>
                                <li class="mb-1"><i class="bi bi-dot me-1"></i> Tumbler air minum isi ulang</li>
                                <li class="mb-1"><i class="bi bi-dot me-1"></i> Jas hujan ponco &amp; pakaian ganti</li>
                                <li class="mb-1"><i class="bi bi-dot me-1"></i> Obat pribadi bagi yang memiliki riwayat alergi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Interactive Checklist Perlengkapan -->
                <?php if (!empty($event['items_to_bring'])): ?>
                    <h6 class="fw-bold font-poster text-dark mb-2">
                        <i class="bi bi-card-checklist text-success me-1"></i>Checklist Mandiri Relawan:
                    </h6>
                    <div class="mb-4">
                        <?php 
                            $items = explode(',', $event['items_to_bring']);
                            foreach ($items as $idx => $it): 
                                $it = trim($it);
                                if (empty($it)) continue;
                        ?>
                            <label class="checklist-item">
                                <input type="checkbox" class="form-check-input mt-0" id="chk_<?= $idx ?>">
                                <span><?= htmlspecialchars($it) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Profil Koordinator Lapangan -->
                <div class="p-3 rounded-4 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; color: #0a3622;">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block" style="font-size: 11px;">Koordinator Lapangan (PIC):</span>
                            <strong class="text-dark"><?= htmlspecialchars($event['coordinator_name'] ?? 'Tim Koordinator Plant For Fun') ?></strong>
                        </div>
                    </div>
                    <?php if (!empty($event['coordinator_phone'])): ?>
                        <?php 
                            $waPhone = preg_replace('/[^0-9]/', '', $event['coordinator_phone']);
                            if (str_starts_with($waPhone, '0')) {
                                $waPhone = '62' . substr($waPhone, 1);
                            }
                        ?>
                        <a href="https://api.whatsapp.com/send?phone=<?= $waPhone ?>&text=<?= urlencode('Halo Kak ' . ($event['coordinator_name'] ?? '') . ', saya ingin bertanya mengenai Ekspedisi: ' . $event['title']) ?>" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-success rounded-pill fw-bold font-poster">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi Koordinator Lapangan
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Actions: Kalender & Bagikan -->
                <div class="pt-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= $gcalUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">
                            <i class="bi bi-google me-1"></i> Tambah ke Google Calendar
                        </a>
                        <a href="<?= url('/kegiatan/' . $event['id'] . '/kalender') ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold">
                            <i class="bi bi-calendar-plus me-1"></i> Unduh File iCal (.ics)
                        </a>
                    </div>

                    <!-- Share buttons -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Bagikan:</span>
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode('Ikuti Ekspedisi Penanaman Pohon: ' . $event['title'] . ' di ' . url('/kegiatan/' . $event['id'])) ?>" 
                           target="_blank" class="share-btn" title="WhatsApp">
                            <i class="bi bi-whatsapp text-success"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Ayo bergabung dalam aksi konservasi: ' . $event['title'] . ' bersama @tanampohon_id ' . url('/kegiatan/' . $event['id'])) ?>" 
                           target="_blank" class="share-btn" title="X / Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Bagian Logo Partner dan Kolaborator Resmi -->
            <div class="p-4 rounded-4 shadow-sm bg-white border mb-4" style="border-color: rgba(18,56,44,0.15) !important;">
                <div class="mb-2">
                    <h5 class="fw-bold font-poster text-uppercase mb-1" style="color: #12382c;">
                        Mitra Kolaborasi &amp; Media Partner Resmi
                    </h5>
                    <p class="text-muted small mb-0">Gerakan ekspedisi ini terselenggara berkat kolaborasi bersama para partner peduli alam:</p>
                </div>
                <?php 
                    $logoTheme = 'dark'; 
                    require VIEW_PATH . '/partials/partner-logos.php'; 
                ?>
            </div>
        </div>

        <!-- KOLOM KANAN: FLOATING FORM PENDAFTARAN RELAWAN -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top bg-white" style="top: 90px;">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3 font-poster text-uppercase">
                    Form Pendaftaran Relawan
                </h5>

                <!-- Status Kuota Kursi -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-secondary mb-1 font-mono">
                        <span>Sisa Kuota Relawan:</span>
                        <strong class="<?= $remainingSeats <= 5 ? 'text-danger' : 'text-dark' ?>">
                            <?= $remainingSeats ?> dari <?= $event['quota'] ?> Kursi
                        </strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-success' ?>" role="progressbar" style="width: <?= $perc ?>%;"></div>
                    </div>
                </div>

                <?php if ($isFull): ?>
                    <div class="alert alert-danger text-center small mb-3 rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon maaf, kuota relawan untuk kegiatan ekspedisi ini telah penuh. Pantau jadwal berikutnya!
                    </div>
                <?php elseif ($userRegistered): ?>
                    <div class="alert alert-success text-center small mb-3 rounded-3">
                        <i class="bi bi-check-circle-fill me-1"></i> Anda sudah terdaftar sebagai relawan dalam ekspedisi ini.
                    </div>
                    <a href="<?= url('/dashboard/kegiatan') ?>" class="btn btn-outline-success w-100 fw-bold rounded-pill">
                        Lihat E-Ticket Saya →
                    </a>
                <?php else: ?>
                    <form action="<?= url('/kegiatan/' . $event['id'] . '/daftar') ?>" method="POST">
                        <?= csrf_field() ?>

                        <?php if (!$currentUser): ?>
                            <div class="alert alert-warning small py-2 mb-3 rounded-3">
                                <i class="bi bi-info-circle me-1"></i> Anda harus <a href="<?= url('/masuk') ?>" class="alert-link fw-bold">masuk</a> atau <a href="<?= url('/daftar') ?>" class="alert-link fw-bold">daftar akun</a> untuk menyimpan e-ticket ber-QR code.
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-light rounded-3 small text-secondary mb-3 border">
                                <div class="text-muted" style="font-size: 11px;">Mendaftar sebagai:</div>
                                <strong class="text-dark fs-6"><?= htmlspecialchars($currentUser['nama']) ?></strong><br>
                                <span class="font-mono" style="font-size: 11px;"><?= htmlspecialchars($currentUser['email']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Pilihan Ukuran Kaos Relawan -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="tshirt_size" class="form-label fw-semibold small text-secondary mb-0">
                                    Ukuran Kaos Relawan *
                                </label>
                                <a href="javascript:void(0)" class="small text-success text-decoration-none" data-bs-toggle="modal" data-bs-target="#sizeChartModal" style="font-size: 11px;">
                                    <i class="bi bi-rulers me-1"></i>Size Chart
                                </a>
                            </div>
                            <select class="form-select" id="tshirt_size" name="tshirt_size" required>
                                <option value="S">S (Lebar 48 cm, Panjang 68 cm)</option>
                                <option value="M">M (Lebar 50 cm, Panjang 70 cm)</option>
                                <option value="L" selected>L (Lebar 52 cm, Panjang 72 cm)</option>
                                <option value="XL">XL (Lebar 54 cm, Panjang 74 cm)</option>
                                <option value="XXL">XXL (Lebar 56 cm, Panjang 76 cm)</option>
                            </select>
                        </div>

                        <!-- Catatan Pendaftaran -->
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold small text-secondary">
                                Asal Komunitas / Riwayat Alergi (Opsional)
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Nama kampus/komunitas, atau riwayat asma/alergi..."></textarea>
                        </div>

                        <!-- Checkbox Persetujuan Etika Lingkungan -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeSafety" required>
                            <label class="form-check-label text-secondary small" for="agreeSafety" style="font-size: 11px;">
                                Saya bersedia mematuhi arahan koordinator, menjaga kebersihan alam (Leave No Trace), dan bertanggung jawab atas keselamatan diri.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold font-poster rounded-pill py-3 shadow-sm">
                            <?php if ($event['registration_fee'] > 0): ?>
                                Daftar &amp; Lanjut Pembayaran (<?= format_rupiah($event['registration_fee']) ?>)
                            <?php else: ?>
                                Konfirmasi Jadi Relawan (Gratis)
                            <?php endif; ?>
                        </button>
                    </form>
                <?php endif; ?>

                <div class="text-center text-muted small mt-3" style="font-size: 11px;">
                    <i class="bi bi-shield-check text-success me-1"></i> E-Ticket resmi langsung terbit di akun Anda.
                </div>
            </div>
        </div>
    </div>

    <!-- EKSPEDISI LAINNYA -->
    <?php if (!empty($relatedEvents)): ?>
        <div class="mt-5 pt-4 border-top">
            <h4 class="fw-bold font-poster text-uppercase mb-4" style="color: #12382c;">
                Jadwal Ekspedisi Terdekat Lainnya
            </h4>
            <div class="row g-4">
                <?php foreach ($relatedEvents as $rel): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success-subtle text-success font-mono" style="color:#0a3622 !important;">
                                    <?= htmlspecialchars($rel['category'] ?? 'Agroforestri') ?>
                                </span>
                                <span class="small font-mono text-muted">
                                    <i class="bi bi-calendar me-1"></i><?= date('d M Y', strtotime($rel['event_date'])) ?>
                                </span>
                            </div>
                            <h5 class="fw-bold font-poster mb-2">
                                <a href="<?= url('/kegiatan/' . $rel['id']) ?>" class="text-decoration-none text-dark hover-success">
                                    <?= htmlspecialchars($rel['title']) ?>
                                </a>
                            </h5>
                            <p class="text-secondary small mb-3 line-clamp-2">
                                <?= htmlspecialchars($rel['description']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                <span class="small text-muted font-mono">
                                    <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($rel['city'] ?? '-') ?>
                                </span>
                                <a href="<?= url('/kegiatan/' . $rel['id']) ?>" class="btn btn-outline-success btn-sm rounded-pill fw-bold font-poster px-3">
                                    Detail Ekspedisi →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Modal Panduan Ukuran Kaos (Size Chart) -->
<div class="modal fade" id="sizeChartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header" style="background: #12382c; color: #fff; border-radius: 20px 20px 0 0;">
                <h6 class="modal-title fw-bold font-poster text-uppercase mb-0">
                    <i class="bi bi-rulers me-2"></i>Panduan Ukuran Kaos Relawan
                </h6>
                <button type="button" class="btn-close filter-invert" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-secondary mb-3">
                    Kaos relawan menggunakan bahan <em>Cotton Combed 24s</em> yang adem, menyerap keringat di lapangan, dan berpotongan standar unisex.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ukuran</th>
                                <th>Lebar Dada (cm)</th>
                                <th>Panjang Badan (cm)</th>
                                <th>Rekomendasi Berat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">S</td>
                                <td>48 cm</td>
                                <td>68 cm</td>
                                <td>45 - 55 kg</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">M</td>
                                <td>50 cm</td>
                                <td>70 cm</td>
                                <td>55 - 65 kg</td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bold">L</td>
                                <td>52 cm</td>
                                <td>72 cm</td>
                                <td>65 - 75 kg</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">XL</td>
                                <td>54 cm</td>
                                <td>74 cm</td>
                                <td>75 - 85 kg</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">XXL</td>
                                <td>56 cm</td>
                                <td>76 cm</td>
                                <td>85 - 95 kg</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS & Inisialisasi Peta Titik Kumpul Interaktif -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function() {
    const lat = <?= json_encode($eventLat) ?>;
    const lng = <?= json_encode($eventLng) ?>;
    const gatheringPoint = <?= json_encode($event['gathering_point']) ?>;
    const eventTitle = <?= json_encode($event['title']) ?>;
    const eventTime = <?= json_encode(substr($event['gathering_time'], 0, 5)) ?>;

    const streetLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{x}/{y}', {
        maxZoom: 18,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });

    const topoLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: 'Map data: &copy; OpenStreetMap contributors, SRTM | Map style: &copy; OpenTopoMap (CC-BY-SA)'
    });

    const map = L.map('publicEventMap', {
        center: [lat, lng],
        zoom: 14,
        layers: [streetLayer],
        scrollWheelZoom: false
    });

    window.publicExpeditionMap = map;
    window.publicMapLayers = { street: streetLayer, satellite: satLayer, topo: topoLayer };
    window.publicMapCoords = [lat, lng];

    // Custom Animated Pulsing Pin
    const pulseIcon = L.divIcon({
        className: 'custom-pulse-marker',
        html: `
            <div class="map-pulse-marker">
                <div class="map-pulse-ring"></div>
                <div class="map-pulse-pin">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
            </div>
        `,
        iconSize: [44, 44],
        iconAnchor: [22, 42],
        popupAnchor: [0, -42]
    });

    const marker = L.marker([lat, lng], { icon: pulseIcon }).addTo(map);

    const popupHtml = `
        <div style="font-family: inherit; font-size: 13px;">
            <div class="badge bg-success-subtle text-success font-mono mb-1" style="font-size: 10px;">TITIK KUMPUL RELAWAN</div>
            <strong style="color: #0f172a; display: block; font-size: 14px; margin-bottom: 4px;">${gatheringPoint}</strong>
            <div style="color: #64748b; font-size: 12px; margin-bottom: 8px;">
                <i class="bi bi-clock me-1 text-primary"></i>Kumpul jam <strong>${eventTime} WIB</strong>
            </div>
            <div class="d-flex gap-2 border-top pt-2 mt-2">
                <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" 
                   style="font-size: 11px; text-decoration: none; color: #16a34a; font-weight: 700;">
                    Buka Rute &rarr;
                </a>
            </div>
        </div>
    `;

    marker.bindPopup(popupHtml).openPopup();

    // Invalidate size agar render sempurna saat halaman selesai dimuat
    setTimeout(() => { map.invalidateSize(); }, 300);
})();

function switchPublicMapLayer(layerName) {
    if (!window.publicExpeditionMap || !window.publicMapLayers) return;
    const map = window.publicExpeditionMap;
    const layers = window.publicMapLayers;

    Object.values(layers).forEach(l => {
        if (map.hasLayer(l)) map.removeLayer(l);
    });

    if (layers[layerName]) {
        layers[layerName].addTo(map);
    }

    ['btnMapStreet', 'btnMapSatellite', 'btnMapTopo'].forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) btn.classList.remove('active', 'btn-secondary');
    });

    const activeBtnMap = { street: 'btnMapStreet', satellite: 'btnMapSatellite', topo: 'btnMapTopo' };
    const currBtn = document.getElementById(activeBtnMap[layerName]);
    if (currBtn) currBtn.classList.add('active');
}

function recenterPublicMap() {
    if (!window.publicExpeditionMap || !window.publicMapCoords) return;
    window.publicExpeditionMap.flyTo(window.publicMapCoords, 15, { duration: 1 });
}

function copyCoordinatesToClipboard(coords) {
    navigator.clipboard.writeText(coords).then(() => {
        const btnText = document.getElementById('copyCoordBtnText');
        if (btnText) {
            const original = btnText.innerText;
            btnText.innerText = 'Tersalin!';
            btnText.parentElement.classList.replace('btn-light', 'btn-success');
            btnText.parentElement.classList.add('text-white');
            setTimeout(() => {
                btnText.innerText = original;
                btnText.parentElement.classList.replace('btn-success', 'btn-light');
                btnText.parentElement.classList.remove('text-white');
            }, 2000);
        }
    }).catch(err => {
        alert('Koordinat: ' + coords);
    });
}
</script>
