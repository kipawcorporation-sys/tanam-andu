<?php
/**
 * View: admin/locations.php — Manajemen Lokasi Penanaman & Titik Konservasi (Pro)
 */

// ───────── Leaflet CSS via $extraHead ─────────
$extraHead = <<<HTML
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
HTML;

// ───────── Stats agregat ─────────
$totalLokasi  = count($locations);
$totalTarget  = array_sum(array_column($locations, 'total_trees_target'));
$totalTanam   = array_sum(array_column($locations, 'real_trees_planted'));
$totalAktif   = count(array_filter($locations, fn($l) => $l['is_active']));
$pctGlobal    = $totalTarget > 0 ? min(100, round($totalTanam / $totalTarget * 100, 1)) : 0;
$totalEvents  = array_sum(array_column($locations, 'total_events'));

// GeoJSON & data lengkap untuk peta overview dan modal detail
$locGeoJson = json_encode(array_map(fn($l) => [
    'id'          => $l['id'],
    'name'        => $l['name'],
    'city'        => $l['city'],
    'province'    => $l['province'],
    'lat'         => (float)($l['latitude']  ?? 0),
    'lng'         => (float)($l['longitude'] ?? 0),
    'planted'     => (int)$l['real_trees_planted'],
    'target'      => (int)$l['total_trees_target'],
    'active'      => (bool)$l['is_active'],
    'events'      => (int)$l['total_events'],
    'events_list' => $l['events_list'] ?? [],
    'description' => $l['description'] ?? '',
    'photo'       => !empty($l['photo']) ? '/' . ltrim($l['photo'], '/') : '/assets/images/coffee-plantation.jpg',
], $locations));
?>

<!-- ══════════════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════════════ -->
<style>
/* ── Hero section ── */
.loc-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 55%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(6, 21, 16, 0.2);
}
.loc-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -70px;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.18) 0%, transparent 70%);
    pointer-events: none;
}
.loc-hero::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: 45%;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(21, 128, 61, 0.12) 0%, transparent 70%);
    pointer-events: none;
}
.loc-hero h4 {
    color: #fff;
    font-weight: 800;
    font-size: 1.45rem;
    margin: 0 0 6px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.loc-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    margin: 0;
    max-width: 650px;
}
.hero-badge-tag {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.3);
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* ── KPI Cards ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 992px) {
    .kpi-row { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 576px) {
    .kpi-row { grid-template-columns: repeat(2, 1fr); }
}
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
    border-color: rgba(34, 197, 94, 0.3);
}
.kpi-icon {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.kpi-val {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 2px;
}
.kpi-lbl {
    font-size: 0.74rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* ── Section Cards ── */
.section-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    margin-bottom: 22px;
    overflow: hidden;
}
.section-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

/* ── Overview Map ── */
#lokasi-overview-map {
    height: 420px;
    width: 100%;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    z-index: 1;
}
.map-wrap-relative {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
}
.map-floating-panel {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 999;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.map-floating-search {
    position: absolute;
    top: 14px;
    left: 60px;
    z-index: 999;
    width: 280px;
    max-width: calc(100% - 140px);
}
.map-layer-btn-group .btn {
    font-size: 0.73rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #cbd5e1;
    color: #334155;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(4px);
    transition: all 0.2s;
}
.map-layer-btn-group .btn.active {
    background: #15803d;
    color: #fff;
    border-color: #15803d;
}
.map-layer-btn-group .btn:hover:not(.active) {
    background: #f8fafc;
}
.map-control-btn {
    font-size: 0.73rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #cbd5e1;
    color: #1e293b;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(4px);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.map-control-btn:hover {
    background: #f1f5f9;
}

/* ── Custom Pulse Animation for Map ── */
@keyframes loc-pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 12px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* ── Toolbar ── */
.loc-toolbar {
    background: #fff;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
}
.view-toggle .btn {
    border-radius: 8px;
    font-size: 0.8rem;
    padding: 6px 14px;
    font-weight: 600;
}

/* ── Table Styling ── */
.loc-table thead th {
    background: #f8fafc;
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    padding: 14px 16px;
    white-space: nowrap;
}
.loc-table td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.loc-table tbody tr {
    transition: background 0.15s;
}
.loc-table tbody tr:hover {
    background: #f8faf8;
}
.loc-photo {
    width: 54px;
    height: 54px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}
.progress-loc {
    height: 7px;
    border-radius: 6px;
    background: #e2e8f0;
    overflow: hidden;
}

/* ── Card Grid View ── */
.loc-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}
.loc-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
}
.loc-card:hover {
    box-shadow: 0 12px 28px rgba(18, 56, 44, 0.1);
    transform: translateY(-3px);
    border-color: rgba(34, 197, 94, 0.3);
}
.loc-card-photo {
    height: 175px;
    object-fit: cover;
    width: 100%;
}
.loc-card-body {
    padding: 18px 20px;
    flex-grow: 1;
}
.loc-card-actions {
    padding: 12px 20px;
    border-top: 1px solid #f1f5f9;
    background: #fafafa;
    display: flex;
    gap: 8px;
}

/* ── Buttons ── */
.btn-new-item {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.3);
}
.btn-new-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45);
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

/* ── Modals Pro ── */
.modal-loc-header {
    background: linear-gradient(135deg, #061510 0%, #12382c 100%);
    color: #fff;
    padding: 20px 26px;
    border-radius: 14px 14px 0 0;
}
.modal-loc-header .btn-close {
    filter: invert(1);
}
.modal-tabs .nav-link {
    border-radius: 8px 8px 0 0;
    font-size: 0.84rem;
    font-weight: 600;
    color: #64748b;
    border: 1px solid transparent;
    padding: 9px 18px;
}
.modal-tabs .nav-link.active {
    color: #15803d;
    border-color: #e2e8f0 #e2e8f0 #fff;
    background: #fff;
}
.modal-form-wrap {
    max-height: 65vh;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 22px 26px;
}
.map-picker-container {
    height: 320px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    position: relative;
    margin-top: 10px;
    overflow: hidden;
}
.map-preset-btn {
    font-size: 0.74rem;
    padding: 5px 12px;
    border-radius: 8px;
    font-weight: 600;
}
.coord-display {
    background: #f8fafc;
    border-radius: 10px;
    padding: 10px 16px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.83rem;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

/* ── Toast Feedback ── */
.toast-container-custom {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
}
.toast-gps {
    background: #0f172a;
    color: #fff;
    padding: 12px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.86rem;
    border-left: 4px solid #22c55e;
    animation: toastSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes toastSlideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* ── Empty State ── */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.empty-state i {
    font-size: 3.2rem;
    margin-bottom: 12px;
    display: block;
    opacity: 0.35;
}
</style>

<!-- ══════════════════════════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════════════════════ -->
<div class="loc-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="hero-badge-tag"><i class="bi bi-shield-check me-1"></i>GIS &amp; Titik Konservasi</span>
                <span class="badge bg-white bg-opacity-10 text-white font-mono" style="font-size: 11px;">
                    <?= $totalLokasi ?> Kawasan Terdaftar
                </span>
            </div>
            <h4>
                <i class="bi bi-geo-alt-fill text-success"></i>
                Pusat Manajemen Lokasi Penanaman
            </h4>
            <p>
                Monitoring koordinat GPS, sebaran kawasan konservasi, kapasitas bibit pohon, serta progres realisasi penghijauan secara spasial.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/admin/lokasi/export-csv" class="btn-hero-action" title="Download data tabular CSV">
                <i class="bi bi-file-earmark-spreadsheet"></i>Unduh CSV
            </a>
            <button type="button" class="btn-hero-action" onclick="exportGeoJson()" title="Export format GeoJSON untuk SIG / QGIS">
                <i class="bi bi-map"></i>Export GeoJSON
            </button>
            <button class="btn-new-item" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                <i class="bi bi-plus-lg"></i>Tambah Lokasi Baru
            </button>
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

<?php if (!empty($_SESSION['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<?php if (!empty($_SESSION['flash_warning'])): ?>
<div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_warning']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_warning']); endif; ?>

<!-- ══════════════════════════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════════════════════════ -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">
            <i class="bi bi-geo-alt-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-dark"><?= $totalLokasi ?></div>
            <div class="kpi-lbl">Total Kawasan</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-primary"><?= $totalAktif ?></div>
            <div class="kpi-lbl">Kawasan Aktif</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="bi bi-bullseye"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#ea580c;"><?= number_format($totalTarget) ?></div>
            <div class="kpi-lbl">Target Pohon</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#16a34a;">
            <i class="bi bi-tree-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-success"><?= number_format($totalTanam) ?></div>
            <div class="kpi-lbl">Pohon Tertanam</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#9333ea;"><?= $pctGlobal ?>%</div>
            <div class="kpi-lbl">% Realisasi</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     PETA OVERVIEW INTERAKTIF SEMUA KAWASAN
══════════════════════════════════════════════════════════════ -->
<div class="section-card">
    <div class="section-card-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-compass text-success fs-5"></i>
            <div>
                <span class="fw-bold text-dark">Peta Sebaran &amp; Geospasial Konservasi</span>
                <span class="text-muted small ms-2 d-none d-md-inline">&bull; Klik pin marker untuk melihat rincian kawasan</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fitOverviewBounds()" title="Reset Tampilan Peta">
                <i class="bi bi-arrows-fullscreen me-1"></i>Fit Bounds
            </button>
            <span class="badge bg-light text-secondary border font-mono">
                <?= $totalAktif ?> aktif / <?= $totalLokasi ?> total
            </span>
        </div>
    </div>
    <div class="p-3">
        <div class="map-wrap-relative">
            <!-- Peta Leaflet -->
            <div id="lokasi-overview-map"></div>

            <!-- Floating Search Box on Map -->
            <div class="map-floating-search">
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="map-quick-search" class="form-control border-start-0" 
                           placeholder="Cari kawasan di peta..." 
                           oninput="filterMapMarkers(this.value)">
                </div>
            </div>

            <!-- Floating Controls (Layer Switcher + Reset) -->
            <div class="map-floating-panel">
                <div class="btn-group-vertical map-layer-btn-group shadow-sm" id="overviewLayerSwitcher">
                    <button type="button" class="btn active" onclick="switchOverviewLayer('street')" id="btn-layer-street">
                        🗺 Street
                    </button>
                    <button type="button" class="btn" onclick="switchOverviewLayer('satellite')" id="btn-layer-sat">
                        🛰 Satelit
                    </button>
                    <button type="button" class="btn" onclick="switchOverviewLayer('topo')" id="btn-layer-topo">
                        ⛰ Topo
                    </button>
                </div>
            </div>
        </div>

        <!-- Legend & Quick Info -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 px-2">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="d-flex align-items-center gap-1 small text-muted">
                    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#22c55e;"></span>
                    Kawasan Aktif
                </span>
                <span class="d-flex align-items-center gap-1 small text-muted">
                    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#94a3b8;"></span>
                    Kawasan Nonaktif
                </span>
                <span class="d-flex align-items-center gap-1 small text-muted">
                    <i class="bi bi-cursor-fill text-primary"></i>
                    Klik marker untuk navigasi Waze / Maps
                </span>
            </div>
            <div class="small text-muted">
                <i class="bi bi-info-circle me-1"></i>Peta berbasis OpenStreetMap &amp; Esri Satellite High-Res
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     TOOLBAR FILTER + VIEW TOGGLE
══════════════════════════════════════════════════════════════ -->
<div class="loc-toolbar d-flex flex-wrap align-items-center gap-3">
    <div class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
        <div class="input-group" style="max-width:300px;">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="loc-search" class="form-control border-start-0" placeholder="Cari nama lokasi, kota...">
        </div>
        <select id="filter-province" class="form-select" style="max-width:180px;">
            <option value="">Semua Provinsi</option>
            <?php
            $provinces = array_unique(array_filter(array_column($locations, 'province')));
            sort($provinces);
            foreach ($provinces as $prov):
            ?>
            <option value="<?= htmlspecialchars($prov) ?>"><?= htmlspecialchars($prov) ?></option>
            <?php endforeach; ?>
        </select>
        <select id="filter-status" class="form-select" style="max-width:150px;">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="btn-group view-toggle" role="group" id="viewToggle">
            <button type="button" class="btn btn-outline-secondary active" data-view="table" title="Tampilan Tabel">
                <i class="bi bi-table me-1"></i>Tabel
            </button>
            <button type="button" class="btn btn-outline-secondary" data-view="card" title="Tampilan Kartu">
                <i class="bi bi-grid-3x3-gap me-1"></i>Kartu
            </button>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 ms-2 font-mono" id="loc-count-label">
            <?= $totalLokasi ?> lokasi
        </span>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     TABEL VIEW
══════════════════════════════════════════════════════════════ -->
<div class="section-card" id="view-table">
    <div class="section-card-body p-0">
        <div class="table-responsive">
            <table class="table loc-table align-middle mb-0" id="locTable">
                <thead>
                    <tr>
                        <th style="width:70px;">Foto</th>
                        <th>Nama Kawasan</th>
                        <th>Wilayah</th>
                        <th>Koordinat GPS</th>
                        <th style="width:210px;">Target &amp; Realisasi</th>
                        <th style="width:90px;" class="text-center">Kegiatan</th>
                        <th style="width:100px;" class="text-center">Status</th>
                        <th style="width:140px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="locTableBody">
                <?php if (empty($locations)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-geo-alt"></i>
                                Belum ada lokasi penanaman terdaftar
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($locations as $loc):
                    $pct = ($loc['total_trees_target'] > 0)
                        ? min(100, round($loc['real_trees_planted'] / $loc['total_trees_target'] * 100))
                        : 0;
                    $photoUrl = !empty($loc['photo'])
                        ? '/' . ltrim($loc['photo'], '/')
                        : '/assets/images/coffee-plantation.jpg';
                    $pctColor = $pct >= 80 ? '#22c55e' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
                ?>
                    <tr class="loc-row"
                        data-name="<?= strtolower(htmlspecialchars($loc['name'])) ?>"
                        data-city="<?= strtolower(htmlspecialchars($loc['city'])) ?>"
                        data-province="<?= htmlspecialchars($loc['province']) ?>"
                        data-status="<?= $loc['is_active'] ?>">
                        <td>
                            <img src="<?= htmlspecialchars($photoUrl) ?>"
                                 alt="<?= htmlspecialchars($loc['name']) ?>"
                                 class="loc-photo cursor-pointer"
                                 onclick="showDetailModal(<?= htmlspecialchars(json_encode($loc)) ?>)"
                                 onerror="this.src='/assets/images/coffee-plantation.jpg'">
                        </td>
                        <td>
                            <div class="fw-bold text-dark cursor-pointer hover-underline" 
                                 onclick="showDetailModal(<?= htmlspecialchars(json_encode($loc)) ?>)">
                                <?= htmlspecialchars($loc['name']) ?>
                            </div>
                            <div class="text-muted small text-truncate" style="max-width:280px;">
                                <?= htmlspecialchars($loc['description'] ?? 'Belum ada catatan deskripsi.') ?>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold small text-dark"><?= htmlspecialchars($loc['city']) ?></div>
                            <div class="text-muted" style="font-size:11px;"><?= htmlspecialchars($loc['province']) ?></div>
                        </td>
                        <td>
                            <?php if (!empty($loc['latitude']) && !empty($loc['longitude'])): ?>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="https://maps.google.com/?q=<?= $loc['latitude'] ?>,<?= $loc['longitude'] ?>"
                                       target="_blank"
                                       class="badge bg-success-subtle text-success text-decoration-none font-mono"
                                       style="font-size:11px;"
                                       title="Buka di Google Maps">
                                        <i class="bi bi-pin-map-fill me-1"></i><?= round($loc['latitude'], 4) ?>, <?= round($loc['longitude'], 4) ?>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light p-0 px-1 border"
                                            title="Salin Koordinat GPS"
                                            onclick="copyGpsCoords(<?= $loc['latitude'] ?>, <?= $loc['longitude'] ?>)">
                                        <i class="bi bi-clipboard text-secondary" style="font-size: 11px;"></i>
                                    </button>
                                </div>
                                <button class="btn btn-link btn-sm p-0 text-muted mt-1"
                                        style="font-size:11px; text-decoration:none;"
                                        onclick="focusOverviewMapOn(<?= $loc['latitude'] ?>, <?= $loc['longitude'] ?>)">
                                    <i class="bi bi-crosshair me-1"></i>Sorot di peta
                                </button>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Belum diset</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-bold text-dark"><?= number_format($loc['real_trees_planted']) ?></span>
                                <span class="text-muted">/ <?= number_format($loc['total_trees_target']) ?></span>
                            </div>
                            <div class="progress progress-loc">
                                <div class="progress-bar" style="width:<?= $pct ?>%; background:<?= $pctColor ?>;"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted mt-1" style="font-size:10px;">
                                <span><?= $pct ?>% tercapai</span>
                                <span>sisa <?= number_format(max(0, $loc['total_trees_target'] - $loc['real_trees_planted'])) ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info border border-info-subtle font-mono">
                                <i class="bi bi-calendar-event me-1"></i><?= (int)$loc['total_events'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="/admin/lokasi/<?= $loc['id'] ?>/toggle-status" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" 
                                        class="badge border-0 cursor-pointer <?= $loc['is_active'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>"
                                        title="Klik untuk beralih status Aktif / Nonaktif"
                                        style="font-size: 11px; padding: 5px 10px;">
                                    <?= $loc['is_active'] ? '● Aktif' : '○ Nonaktif' ?>
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="showDetailModal(<?= htmlspecialchars(json_encode($loc)) ?>)"
                                        title="Rincian Lengkap Kawasan">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-edit-loc"
                                        data-json="<?= htmlspecialchars(json_encode($loc)) ?>"
                                        title="Edit Lokasi">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="/admin/lokasi/<?= $loc['id'] ?>/hapus" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus lokasi penanaman \'<?= htmlspecialchars(addslashes($loc['name'])) ?>\'?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Lokasi">
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
</div>

<!-- ══════════════════════════════════════════════════════════════
     CARD VIEW
══════════════════════════════════════════════════════════════ -->
<div id="view-card" style="display:none;">
    <?php if (empty($locations)): ?>
        <div class="empty-state section-card p-5">
            <i class="bi bi-geo-alt"></i>
            Belum ada lokasi penanaman terdaftar
        </div>
    <?php else: ?>
    <div class="loc-card-grid" id="locCardGrid">
    <?php foreach ($locations as $loc):
        $pct = ($loc['total_trees_target'] > 0)
            ? min(100, round($loc['real_trees_planted'] / $loc['total_trees_target'] * 100))
            : 0;
        $photoUrl = !empty($loc['photo']) ? '/' . ltrim($loc['photo'], '/') : '/assets/images/coffee-plantation.jpg';
        $pctColor = $pct >= 80 ? '#22c55e' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
    ?>
        <div class="loc-card loc-row"
             data-name="<?= strtolower(htmlspecialchars($loc['name'])) ?>"
             data-city="<?= strtolower(htmlspecialchars($loc['city'])) ?>"
             data-province="<?= htmlspecialchars($loc['province']) ?>"
             data-status="<?= $loc['is_active'] ?>">
            <div style="position:relative;">
                <img src="<?= htmlspecialchars($photoUrl) ?>"
                     class="loc-card-photo cursor-pointer"
                     onclick="showDetailModal(<?= htmlspecialchars(json_encode($loc)) ?>)"
                     onerror="this.src='/assets/images/coffee-plantation.jpg'"
                     alt="<?= htmlspecialchars($loc['name']) ?>">
                <div style="position:absolute; top:12px; right:12px;">
                    <form action="/admin/lokasi/<?= $loc['id'] ?>/toggle-status" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="badge border-0 shadow-sm cursor-pointer <?= $loc['is_active'] ? 'bg-success' : 'bg-secondary' ?>"
                                style="font-size:11px; padding: 5px 10px;"
                                title="Klik untuk mengubah status">
                            <?= $loc['is_active'] ? '● Aktif' : '○ Nonaktif' ?>
                        </button>
                    </form>
                </div>
                <div style="position:absolute; bottom:10px; left:12px;">
                    <span class="badge bg-dark bg-opacity-75 text-white backdrop-blur" style="font-size:11px;">
                        <i class="bi bi-calendar-event me-1"></i><?= (int)$loc['total_events'] ?> Kegiatan
                    </span>
                </div>
            </div>
            <div class="loc-card-body">
                <div class="fw-bold text-dark fs-6 mb-1 cursor-pointer" 
                     onclick="showDetailModal(<?= htmlspecialchars(json_encode($loc)) ?>)">
                    <?= htmlspecialchars($loc['name']) ?>
                </div>
                <div class="text-muted small mb-2 d-flex align-items-center gap-1">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                    <span><?= htmlspecialchars($loc['city']) ?>, <?= htmlspecialchars($loc['province']) ?></span>
                </div>
                <?php if (!empty($loc['description'])): ?>
                <div class="text-muted small mb-3" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; font-size:12px; line-height:1.4;">
                    <?= htmlspecialchars($loc['description']) ?>
                </div>
                <?php endif; ?>
                <!-- Progress Target -->
                <div class="d-flex justify-content-between small mb-1">
                    <span class="fw-bold text-dark"><?= number_format($loc['real_trees_planted']) ?> pohon</span>
                    <span class="text-muted">target <?= number_format($loc['total_trees_target']) ?></span>
                </div>
                <div class="progress mb-1" style="height:8px; border-radius:6px;">
                    <div class="progress-bar" style="width:<?= $pct ?>%; background:<?= $pctColor ?>;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted" style="font-size:11px;">
                    <span><?= $pct ?>% tercapai</span>
                    <span>Sisa: <?= number_format(max(0, $loc['total_trees_target'] - $loc['real_trees_planted'])) ?></span>
                </div>
                <?php if (!empty($loc['latitude']) && !empty($loc['longitude'])): ?>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                    <span class="badge bg-light text-secondary border font-mono" style="font-size:11px;">
                        <?= round($loc['latitude'], 4) ?>, <?= round($loc['longitude'], 4) ?>
                    </span>
                    <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none small text-success fw-bold"
                            onclick="focusOverviewMapOn(<?= $loc['latitude'] ?>, <?= $loc['longitude'] ?>)">
                        <i class="bi bi-crosshair me-1"></i>Peta
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="loc-card-actions">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="showDetailModal(<?= htmlspecialchars(json_encode($loc)) ?>)"
                        title="Rincian Kawasan">
                    <i class="bi bi-eye"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-edit-loc"
                        data-json="<?= htmlspecialchars(json_encode($loc)) ?>">
                    <i class="bi bi-pencil me-1"></i>Edit
                </button>
                <form action="/admin/lokasi/<?= $loc['id'] ?>/hapus" method="POST" class="d-inline"
                      onsubmit="return confirm('Hapus lokasi penanaman ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Lokasi">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL DETAIL LOKASI PRO (Lihat Rincian & Kegiatan)
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetailLokasi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <!-- Header Image -->
            <div style="position:relative; height:180px; overflow:hidden; background:#061510;">
                <img id="detail-loc-photo" src="" alt="Cover"
                     style="width:100%; height:100%; object-fit:cover; opacity:0.65;">
                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(6,21,16,0.95) 0%, rgba(6,21,16,0.3) 100%);"></div>
                <div style="position:absolute; top:16px; right:16px;">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div style="position:absolute; bottom:16px; left:22px; right:22px;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span id="detail-loc-status-badge" class="badge bg-success">Aktif</span>
                        <span class="badge bg-white bg-opacity-20 text-white font-mono" id="detail-loc-id-badge">#ID</span>
                    </div>
                    <h4 class="fw-bold text-white mb-0" id="detail-loc-name">Nama Lokasi</h4>
                    <div class="text-white-50 small" id="detail-loc-region">Kota, Provinsi</div>
                </div>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Stats Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded-3 border text-center">
                            <div class="text-muted small">Pohon Tertanam</div>
                            <div class="fs-4 fw-bold text-success" id="detail-loc-planted">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded-3 border text-center">
                            <div class="text-muted small">Target Bibit</div>
                            <div class="fs-4 fw-bold text-dark" id="detail-loc-target">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded-3 border text-center">
                            <div class="text-muted small">Pencapaian</div>
                            <div class="fs-4 fw-bold text-primary" id="detail-loc-pct">0%</div>
                        </div>
                    </div>
                </div>

                <!-- Coordinates & Navigation -->
                <div class="bg-white p-3 rounded-3 border mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <span class="fw-bold text-dark small"><i class="bi bi-pin-map text-success me-1"></i>Koordinat Geografis</span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-copy-detail-coords">
                                <i class="bi bi-clipboard me-1"></i>Salin GPS
                            </button>
                            <a id="btn-detail-gmaps" href="#" target="_blank" class="btn btn-sm btn-success">
                                <i class="bi bi-google me-1"></i>Google Maps
                            </a>
                            <a id="btn-detail-waze" href="#" target="_blank" class="btn btn-sm btn-primary">
                                <i class="bi bi-car-front me-1"></i>Waze
                            </a>
                        </div>
                    </div>
                    <div class="font-mono small text-secondary" id="detail-loc-coords-text">Lat: -, Lng: -</div>
                </div>

                <!-- Deskripsi Lahan -->
                <div class="bg-white p-3 rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Karakteristik &amp; Deskripsi Lahan</h6>
                    <p class="text-muted small mb-0" id="detail-loc-description" style="white-space:pre-line;"></p>
                </div>

                <!-- Daftar Kegiatan Terkait -->
                <div class="bg-white p-3 rounded-3 border">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-calendar2-event text-success me-2"></i>Kegiatan Penanaman Terkait</span>
                        <span class="badge bg-light text-dark border" id="detail-loc-events-count">0 Kegiatan</span>
                    </h6>
                    <div id="detail-loc-events-list">
                        <!-- Populated via JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-detail-edit-now">
                    <i class="bi bi-pencil me-1"></i>Edit Kawasan Ini
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL TAMBAH LOKASI (PRO MULTI-TAB)
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalTambahLokasi" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <form action="/admin/lokasi/simpan" method="POST" enctype="multipart/form-data" id="formTambahLokasi">
                <?= csrf_field() ?>
                <div class="modal-loc-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Lokasi Penanaman</h5>
                        <small style="color:rgba(255,255,255,.65);">Lengkapi formulir metadata dan tentukan titik koordinat di peta</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <!-- Nav Tabs -->
                <ul class="nav modal-tabs px-4 pt-3 bg-light border-bottom" id="tabsTambah" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-add-info" type="button">
                            <i class="bi bi-info-circle me-1"></i>1. Informasi Kawasan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-add-map" type="button" id="btn-tab-add-map">
                            <i class="bi bi-geo-alt me-1"></i>2. Titik GPS &amp; Peta
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-add-photo" type="button">
                            <i class="bi bi-image me-1"></i>3. Foto &amp; Media
                        </button>
                    </li>
                </ul>
                <div class="tab-content bg-light">
                    <!-- Tab 1: Info -->
                    <div class="tab-pane fade show active" id="tab-add-info">
                        <div class="modal-form-wrap">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nama Lokasi Penanaman <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kebon Kopi Kerenceng" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kabupaten / Kota <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" placeholder="Contoh: Kabupaten Bandung" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" name="province" class="form-control" placeholder="Contoh: Jawa Barat" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Target Pohon (Bibit)</label>
                                    <div class="input-group">
                                        <input type="number" name="total_trees_target" class="form-control" value="1000" min="1">
                                        <span class="input-group-text">pohon</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status Lokasi</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="add_loc_active" value="1" checked>
                                        <label class="form-check-label fw-semibold" for="add_loc_active">Aktif &amp; Ditampilkan di Peta Publik</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Deskripsi Lokasi &amp; Karakteristik Lahan</label>
                                    <textarea name="description" class="form-control" rows="4"
                                              placeholder="Informasi elevasi mdpl, kondisi tanah, akses kendaraan, sumber mata air, vegetasi pelindung..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tab 2: Map Picker -->
                    <div class="tab-pane fade" id="tab-add-map">
                        <div class="modal-form-wrap">
                            <div class="coord-display mb-3" id="add-coord-display">
                                <i class="bi bi-crosshair me-2"></i>
                                <span id="add-coord-text">Belum ada koordinat — klik pada peta atau gunakan preset di bawah</span>
                            </div>
                            <!-- Preset & GPS Buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="text-muted small me-1 align-self-center">Preset:</span>
                                <button type="button" class="btn btn-sm btn-outline-success map-preset-btn"
                                        onclick="setLocCoords('add', -6.3200, 106.6800, 'Hutan Tangsel')">
                                    <i class="bi bi-tree me-1"></i>Hutan Tangsel
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary map-preset-btn"
                                        onclick="setLocCoords('add', -6.3300, 108.3200, 'Pantai Indramayu')">
                                    <i class="bi bi-water me-1"></i>Pantai Indramayu
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning map-preset-btn"
                                        onclick="setLocCoords('add', -6.9833, 107.8333, 'Kebon Kerenceng')">
                                    <i class="bi bi-cup-hot me-1"></i>Kebon Kerenceng
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary map-preset-btn"
                                        onclick="detectLocGPS('add')">
                                    <i class="bi bi-cursor-fill me-1"></i>GPS Saya
                                </button>
                            </div>
                            <!-- Geocoding Search -->
                            <div class="search-map-wrap mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="add-map-search" class="form-control"
                                           placeholder="Cari lokasi: desa, kecamatan, pegunungan..."
                                           onkeydown="if(event.key==='Enter'){event.preventDefault();searchLocMap('add');}">
                                    <button type="button" class="btn btn-success btn-search-exec"
                                            onclick="searchLocMap('add')">Cari</button>
                                </div>
                            </div>
                            <!-- Layer Switcher -->
                            <div class="d-flex gap-2 mb-2">
                                <span class="text-muted small align-self-center">Layer:</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary active" id="add-btn-layer-street"
                                        onclick="switchLocMapLayer('add','street')">🗺 Street</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="add-btn-layer-sat"
                                        onclick="switchLocMapLayer('add','satellite')">🛰 Satelit</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="add-btn-layer-topo"
                                        onclick="switchLocMapLayer('add','topo')">⛰ Topo</button>
                            </div>
                            <!-- Map Container -->
                            <div class="map-picker-container" id="add-map-picker"></div>
                            <!-- Hidden Coordinates -->
                            <input type="hidden" name="latitude" id="add_lat_hidden">
                            <input type="hidden" name="longitude" id="add_lng_hidden">
                            <!-- Manual Coordinates Fallback -->
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Latitude (desimal)</label>
                                    <input type="number" step="0.0000001" id="add_lat_manual" class="form-control form-control-sm font-mono"
                                           placeholder="-6.3200" oninput="syncLocManualCoords('add')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Longitude (desimal)</label>
                                    <input type="number" step="0.0000001" id="add_lng_manual" class="form-control form-control-sm font-mono"
                                           placeholder="106.6800" oninput="syncLocManualCoords('add')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tab 3: Photo -->
                    <div class="tab-pane fade" id="tab-add-photo">
                        <div class="modal-form-wrap">
                            <label class="form-label fw-semibold">Foto Banner Kawasan</label>
                            <div class="border border-dashed rounded-3 p-4 text-center mb-3" style="border-color:#dee2e6!important; background:#fafafa;">
                                <i class="bi bi-image-fill fs-2 text-muted d-block mb-2"></i>
                                <p class="text-muted small mb-3">Upload foto lanskap kawasan untuk tampilan kartu &amp; rincian publik</p>
                                <input type="file" name="photo" id="add_photo_input" class="form-control" accept="image/*"
                                       onchange="previewLocPhoto(this,'add-photo-preview')">
                            </div>
                            <div id="add-photo-preview" style="display:none;">
                                <label class="form-label small fw-semibold text-muted">Pratinjau Foto</label>
                                <img id="add-photo-preview-img" src="" alt="Preview"
                                     style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid #dee2e6;">
                            </div>
                            <div class="alert alert-info border-0 mt-3" style="font-size:.82rem;">
                                <i class="bi bi-info-circle me-2"></i>
                                Disarankan resolusi lanskap 1200×800 px (format JPG, PNG, atau WebP).
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i>Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL EDIT LOKASI (PRO MULTI-TAB)
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditLokasi" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditLokasi" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-loc-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square me-2 text-info"></i>Edit Lokasi Penanaman</h5>
                        <small id="edit-loc-subtitle" style="color:rgba(255,255,255,.65);">Perbarui informasi dan koordinat titik konservasi</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <ul class="nav modal-tabs px-4 pt-3 bg-light border-bottom" id="tabsEdit" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-edit-info" type="button">
                            <i class="bi bi-info-circle me-1"></i>1. Informasi Kawasan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edit-map" type="button" id="btn-tab-edit-map">
                            <i class="bi bi-geo-alt me-1"></i>2. Titik GPS &amp; Peta
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edit-photo" type="button">
                            <i class="bi bi-image me-1"></i>3. Foto &amp; Media
                        </button>
                    </li>
                </ul>
                <div class="tab-content bg-light">
                    <!-- Tab 1: Info -->
                    <div class="tab-pane fade show active" id="tab-edit-info">
                        <div class="modal-form-wrap">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="edit_loc_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kabupaten / Kota <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="edit_loc_city" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" name="province" id="edit_loc_province" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Target Pohon (Bibit)</label>
                                    <div class="input-group">
                                        <input type="number" name="total_trees_target" id="edit_loc_target" class="form-control" min="1">
                                        <span class="input-group-text">pohon</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status Lokasi</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_loc_active" value="1">
                                        <label class="form-check-label fw-semibold" for="edit_loc_active">Aktif &amp; Ditampilkan di Peta</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Deskripsi Lokasi &amp; Karakteristik Lahan</label>
                                    <textarea name="description" id="edit_loc_desc" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tab 2: Map Picker -->
                    <div class="tab-pane fade" id="tab-edit-map">
                        <div class="modal-form-wrap">
                            <div class="coord-display mb-3" id="edit-coord-display">
                                <i class="bi bi-crosshair me-2"></i>
                                <span id="edit-coord-text">Memuat koordinat...</span>
                            </div>
                            <!-- Preset Buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="text-muted small me-1 align-self-center">Preset:</span>
                                <button type="button" class="btn btn-sm btn-outline-success map-preset-btn"
                                        onclick="setLocCoords('edit', -6.3200, 106.6800, 'Hutan Tangsel')">
                                    <i class="bi bi-tree me-1"></i>Hutan Tangsel
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary map-preset-btn"
                                        onclick="setLocCoords('edit', -6.3300, 108.3200, 'Pantai Indramayu')">
                                    <i class="bi bi-water me-1"></i>Pantai Indramayu
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning map-preset-btn"
                                        onclick="setLocCoords('edit', -6.9833, 107.8333, 'Kebon Kerenceng')">
                                    <i class="bi bi-cup-hot me-1"></i>Kebon Kerenceng
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary map-preset-btn"
                                        onclick="detectLocGPS('edit')">
                                    <i class="bi bi-cursor-fill me-1"></i>GPS Saya
                                </button>
                            </div>
                            <!-- Geocoding Search -->
                            <div class="search-map-wrap mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="edit-map-search" class="form-control"
                                           placeholder="Cari lokasi baru..."
                                           onkeydown="if(event.key==='Enter'){event.preventDefault();searchLocMap('edit');}">
                                    <button type="button" class="btn btn-success btn-search-exec"
                                            onclick="searchLocMap('edit')">Cari</button>
                                </div>
                            </div>
                            <!-- Layer Switcher -->
                            <div class="d-flex gap-2 mb-2">
                                <span class="text-muted small align-self-center">Layer:</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary active" id="edit-btn-layer-street"
                                        onclick="switchLocMapLayer('edit','street')">🗺 Street</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="edit-btn-layer-sat"
                                        onclick="switchLocMapLayer('edit','satellite')">🛰 Satelit</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="edit-btn-layer-topo"
                                        onclick="switchLocMapLayer('edit','topo')">⛰ Topo</button>
                            </div>
                            <div class="map-picker-container" id="edit-map-picker"></div>
                            <input type="hidden" name="latitude" id="edit_lat_hidden">
                            <input type="hidden" name="longitude" id="edit_lng_hidden">
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Latitude</label>
                                    <input type="number" step="0.0000001" id="edit_lat_manual" class="form-control form-control-sm font-mono"
                                           oninput="syncLocManualCoords('edit')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Longitude</label>
                                    <input type="number" step="0.0000001" id="edit_lng_manual" class="form-control form-control-sm font-mono"
                                           oninput="syncLocManualCoords('edit')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tab 3: Photo Edit -->
                    <div class="tab-pane fade" id="tab-edit-photo">
                        <div class="modal-form-wrap">
                            <div id="edit-current-photo-wrap" class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Foto Saat Ini</label>
                                <img id="edit-current-photo" src="" alt="Foto Lokasi"
                                     style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid #dee2e6;">
                            </div>
                            <label class="form-label fw-semibold">Ganti Foto (Opsional)</label>
                            <div class="border border-dashed rounded-3 p-4 text-center" style="border-color:#dee2e6!important; background:#fafafa;">
                                <i class="bi bi-arrow-repeat fs-2 text-muted d-block mb-2"></i>
                                <input type="file" name="photo" id="edit_photo_input" class="form-control" accept="image/*"
                                       onchange="previewLocPhoto(this,'edit-photo-preview')">
                            </div>
                            <div id="edit-photo-preview" style="display:none; margin-top:12px;">
                                <label class="form-label small fw-semibold text-muted">Preview Foto Baru</label>
                                <img id="edit-photo-preview-img" src="" alt="Preview Baru"
                                     style="width:100%; max-height:200px; object-fit:cover; border-radius:12px; border:1px solid #dee2e6;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Perbarui Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     TOAST CONTAINER
══════════════════════════════════════════════════════════════ -->
<div class="toast-container-custom" id="toastContainer"></div>

<!-- ══════════════════════════════════════════════════════════════
     SCRIPTS & LEAFLET ENGINE
══════════════════════════════════════════════════════════════ -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
// ───────── DATA LOCATIONS ─────────
const LOC_DATA = <?= $locGeoJson ?>;
let overviewMap = null;
let clusterGroup = null;
let markerMap = {};

// ───────── TILE LAYERS ─────────
const tileLayers = {
    street: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri World Imagery',
        maxZoom: 19
    }),
    topo: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenTopoMap',
        maxZoom: 17
    })
};

// ───────── CUSTOM PIN ICON GENERATOR ─────────
function createPinIcon(isActive) {
    const color = isActive ? '#22c55e' : '#94a3b8';
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="42" viewBox="0 0 34 42">
            <ellipse cx="17" cy="40" rx="7" ry="2.5" fill="rgba(0,0,0,0.2)"/>
            <path d="M17 2C9.8 2 4 7.8 4 15c0 9.8 13 25 13 25s13-15.2 13-25c0-7.2-5.8-13-13-13z"
                  fill="${color}" stroke="#ffffff" stroke-width="2.5"/>
            <circle cx="17" cy="15" r="6.5" fill="#ffffff"/>
            <circle cx="17" cy="15" r="3.8" fill="${color}"/>
        </svg>
    `;
    return L.divIcon({
        html: svg,
        className: 'custom-leaflet-pin',
        iconSize: [34, 42],
        iconAnchor: [17, 42],
        popupAnchor: [0, -42]
    });
}

// ───────── INITIALIZE OVERVIEW MAP ─────────
function initOverviewMap() {
    overviewMap = L.map('lokasi-overview-map', {
        center: [-6.8, 107.6],
        zoom: 7,
        layers: [tileLayers.street],
        zoomControl: true
    });

    clusterGroup = L.markerClusterGroup({
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        maxClusterRadius: 50,
        iconCreateFunction: function(cluster) {
            const count = cluster.getChildCount();
            return L.divIcon({
                html: `<div style="background:#15803d; color:#fff; width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; border:3px solid #fff; box-shadow:0 3px 10px rgba(0,0,0,0.25);">${count}</div>`,
                className: '',
                iconSize: [38, 38]
            });
        }
    });

    LOC_DATA.forEach(loc => {
        if (!loc.lat || !loc.lng) return;

        const pct = loc.target > 0 ? Math.min(100, Math.round(loc.planted / loc.target * 100)) : 0;
        const marker = L.marker([loc.lat, loc.lng], { icon: createPinIcon(loc.active) });

        const popupContent = `
            <div style="min-width:240px; font-family:'Plus Jakarta Sans', sans-serif;">
                <img src="${loc.photo}" alt="${loc.name}"
                     style="width:100%; height:110px; object-fit:cover; border-radius:8px; margin-bottom:10px;"
                     onerror="this.src='/assets/images/coffee-plantation.jpg'">
                <div style="font-weight:800; font-size:.95rem; color:#0f172a; margin-bottom:2px;">${loc.name}</div>
                <div style="font-size:.78rem; color:#64748b; margin-bottom:8px;">
                    <i class="bi bi-geo-alt-fill text-success me-1"></i>${loc.city}, ${loc.province}
                </div>
                <div style="background:#f8fafc; border-radius:8px; padding:8px 10px; margin-bottom:8px; border:1px solid #e2e8f0; font-size:.8rem;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="color:#64748b;">Realisasi</span>
                        <strong style="color:#15803d;">${loc.planted.toLocaleString('id-ID')} / ${loc.target.toLocaleString('id-ID')}</strong>
                    </div>
                    <div style="height:6px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                        <div style="height:100%; width:${pct}%; background:#22c55e;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:.72rem; color:#64748b; margin-top:4px;">
                        <span>${pct}% tercapai</span>
                        <span>${loc.events} Kegiatan</span>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                    <a href="https://maps.google.com/?q=${loc.lat},${loc.lng}" target="_blank"
                       style="background:#15803d; color:#fff; text-align:center; padding:6px; border-radius:6px; text-decoration:none; font-size:.75rem; font-weight:700;">
                        🗺 Maps
                    </a>
                    <a href="https://waze.com/ul?ll=${loc.lat},${loc.lng}&navigate=yes" target="_blank"
                       style="background:#2563eb; color:#fff; text-align:center; padding:6px; border-radius:6px; text-decoration:none; font-size:.75rem; font-weight:700;">
                        🚗 Waze
                    </a>
                </div>
                <button type="button" class="btn btn-sm btn-light w-100 mt-2 border" style="font-size:.74rem; font-weight:700;"
                        onclick="showDetailModalById(${loc.id})">
                    <i class="bi bi-info-circle me-1"></i>Lihat Rincian Kawasan
                </button>
            </div>
        `;

        marker.bindPopup(popupContent, { maxWidth: 270 });
        clusterGroup.addLayer(marker);
        markerMap[loc.id] = marker;
    });

    overviewMap.addLayer(clusterGroup);
    fitOverviewBounds();
}

// ───────── FIT BOUNDS ─────────
function fitOverviewBounds() {
    const validCoords = LOC_DATA.filter(l => l.lat && l.lng).map(l => [l.lat, l.lng]);
    if (validCoords.length > 0 && overviewMap) {
        overviewMap.fitBounds(validCoords, { padding: [50, 50], maxZoom: 14 });
    }
}

// ───────── SWITCH OVERVIEW MAP LAYER ─────────
function switchOverviewLayer(type) {
    if (!overviewMap) return;
    Object.values(tileLayers).forEach(l => overviewMap.removeLayer(l));
    overviewMap.addLayer(tileLayers[type]);
    document.querySelectorAll('#overviewLayerSwitcher .btn').forEach(b => b.classList.remove('active'));
    document.getElementById('btn-layer-' + (type === 'satellite' ? 'sat' : type)).classList.add('active');
}

// ───────── FOCUS OVERVIEW ON SPECIFIC LOCATION ─────────
function focusOverviewMapOn(lat, lng) {
    if (!overviewMap) return;
    overviewMap.setView([lat, lng], 14, { animate: true, duration: 1 });
    window.scrollTo({
        top: document.getElementById('lokasi-overview-map').getBoundingClientRect().top + window.scrollY - 80,
        behavior: 'smooth'
    });
}

// ───────── FILTER MAP MARKERS VIA QUICK SEARCH ─────────
function filterMapMarkers(query) {
    const q = query.toLowerCase().trim();
    if (!clusterGroup) return;

    clusterGroup.clearLayers();
    LOC_DATA.forEach(loc => {
        if (!loc.lat || !loc.lng) return;
        const match = !q || loc.name.toLowerCase().includes(q) || loc.city.toLowerCase().includes(q);
        if (match && markerMap[loc.id]) {
            clusterGroup.addLayer(markerMap[loc.id]);
        }
    });

    if (q) {
        const matchedLocs = LOC_DATA.filter(l => l.lat && l.lng && (l.name.toLowerCase().includes(q) || l.city.toLowerCase().includes(q)));
        if (matchedLocs.length === 1) {
            overviewMap.setView([matchedLocs[0].lat, matchedLocs[0].lng], 13);
        } else if (matchedLocs.length > 1) {
            overviewMap.fitBounds(matchedLocs.map(l => [l.lat, l.lng]), { padding: [50, 50] });
        }
    }
}

// ───────── DETAIL MODAL ENGINE ─────────
function showDetailModal(loc) {
    document.getElementById('detail-loc-photo').src = loc.photo ? ('/' + loc.photo.replace(/^\//, '')) : '/assets/images/coffee-plantation.jpg';
    document.getElementById('detail-loc-name').textContent = loc.name || 'Detail Kawasan';
    document.getElementById('detail-loc-region').textContent = `${loc.city || ''}, ${loc.province || ''}`;
    document.getElementById('detail-loc-id-badge').textContent = `#ID-${loc.id}`;

    const statusBadge = document.getElementById('detail-loc-status-badge');
    if (loc.is_active == 1) {
        statusBadge.textContent = 'Aktif';
        statusBadge.className = 'badge bg-success';
    } else {
        statusBadge.textContent = 'Nonaktif';
        statusBadge.className = 'badge bg-secondary';
    }

    const planted = parseInt(loc.real_trees_planted || loc.planted || 0);
    const target = parseInt(loc.total_trees_target || loc.target || 1000);
    const pct = target > 0 ? Math.min(100, Math.round(planted / target * 100)) : 0;

    document.getElementById('detail-loc-planted').textContent = planted.toLocaleString('id-ID');
    document.getElementById('detail-loc-target').textContent = target.toLocaleString('id-ID');
    document.getElementById('detail-loc-pct').textContent = pct + '%';

    const lat = parseFloat(loc.latitude || loc.lat || 0);
    const lng = parseFloat(loc.longitude || loc.lng || 0);
    if (lat && lng) {
        document.getElementById('detail-loc-coords-text').textContent = `Latitude: ${lat.toFixed(7)}, Longitude: ${lng.toFixed(7)}`;
        document.getElementById('btn-detail-gmaps').href = `https://maps.google.com/?q=${lat},${lng}`;
        document.getElementById('btn-detail-waze').href = `https://waze.com/ul?ll=${lat},${lng}&navigate=yes`;
        document.getElementById('btn-copy-detail-coords').onclick = () => copyGpsCoords(lat, lng);
        document.getElementById('btn-detail-gmaps').style.display = 'inline-flex';
        document.getElementById('btn-detail-waze').style.display = 'inline-flex';
    } else {
        document.getElementById('detail-loc-coords-text').textContent = 'Koordinat belum ditentukan.';
        document.getElementById('btn-detail-gmaps').style.display = 'none';
        document.getElementById('btn-detail-waze').style.display = 'none';
    }

    document.getElementById('detail-loc-description').textContent = loc.description || 'Belum ada catatan deskripsi atau karakteristik lahan.';

    // Render Events List
    const eventsList = loc.events_list || [];
    const eventsContainer = document.getElementById('detail-loc-events-list');
    document.getElementById('detail-loc-events-count').textContent = eventsList.length + ' Kegiatan';

    if (eventsList.length === 0) {
        eventsContainer.innerHTML = '<div class="text-muted small py-2">Belum ada kegiatan penanaman yang dijadwalkan di kawasan ini.</div>';
    } else {
        eventsContainer.innerHTML = eventsList.map(ev => `
            <div class="d-flex align-items-center justify-content-between p-2 rounded-2 mb-2 bg-light border">
                <div>
                    <div class="fw-bold small text-dark">${ev.title}</div>
                    <div class="text-muted" style="font-size:11px;">
                        <i class="bi bi-calendar me-1"></i>${ev.event_date || '-'} &bull; Kuota: ${ev.registered_count || 0}/${ev.quota || 0}
                    </div>
                </div>
                <span class="badge ${ev.status === 'upcoming' ? 'bg-success' : 'bg-secondary'}" style="font-size:10px;">
                    ${ev.status}
                </span>
            </div>
        `).join('');
    }

    // Edit button inside detail modal
    document.getElementById('btn-detail-edit-now').onclick = () => {
        const modalDetail = bootstrap.Modal.getInstance(document.getElementById('modalDetailLokasi'));
        if (modalDetail) modalDetail.hide();
        openEditModal(loc);
    };

    new bootstrap.Modal(document.getElementById('modalDetailLokasi')).show();
}

function showDetailModalById(id) {
    const loc = LOC_DATA.find(l => l.id == id);
    if (loc) showDetailModal(loc);
}

// ───────── MAP PICKER (ADD & EDIT MODAL) ─────────
const locPickerMaps = {};
const locPickerMarkers = {};
const locPickerLayers = {
    street: () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM', maxZoom: 19 }),
    satellite: () => L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: '© Esri', maxZoom: 19 }),
    topo: () => L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { attribution: '© OTM', maxZoom: 17 })
};
const pickerCurrentLayer = { add: 'street', edit: 'street' };

function initPickerMap(mode, lat, lng) {
    if (locPickerMaps[mode]) {
        locPickerMaps[mode].remove();
        delete locPickerMaps[mode];
    }

    const defaultLat = lat || -6.8;
    const defaultLng = lng || 107.6;
    const defaultZoom = (lat && lng) ? 14 : 8;

    const map = L.map(mode + '-map-picker', {
        center: [defaultLat, defaultLng],
        zoom: defaultZoom
    });
    locPickerLayers[pickerCurrentLayer[mode]]().addTo(map);
    locPickerMaps[mode] = map;

    const pinIcon = createPinIcon(true);

    if (lat && lng) {
        const marker = L.marker([lat, lng], { draggable: true, icon: pinIcon }).addTo(map);
        locPickerMarkers[mode] = marker;
        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            updatePickerCoordDisplay(mode, pos.lat, pos.lng);
        });
        updatePickerCoordDisplay(mode, lat, lng);
    }

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        if (locPickerMarkers[mode]) locPickerMarkers[mode].remove();
        const marker = L.marker([lat, lng], { draggable: true, icon: pinIcon }).addTo(map);
        locPickerMarkers[mode] = marker;
        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            updatePickerCoordDisplay(mode, pos.lat, pos.lng);
        });
        updatePickerCoordDisplay(mode, lat, lng);
    });

    setTimeout(() => map.invalidateSize(), 350);
}

function updatePickerCoordDisplay(mode, lat, lng) {
    document.getElementById(mode + '_lat_hidden').value = lat.toFixed(7);
    document.getElementById(mode + '_lng_hidden').value = lng.toFixed(7);
    document.getElementById(mode + '_lat_manual').value = lat.toFixed(7);
    document.getElementById(mode + '_lng_manual').value = lng.toFixed(7);
    document.getElementById(mode + '-coord-text').textContent = `📍 Latitude: ${lat.toFixed(6)}, Longitude: ${lng.toFixed(6)}`;
}

function setLocCoords(mode, lat, lng, label) {
    if (!locPickerMaps[mode]) {
        initPickerMap(mode, lat, lng);
        return;
    }
    locPickerMaps[mode].setView([lat, lng], 14);
    if (locPickerMarkers[mode]) locPickerMarkers[mode].remove();
    const pinIcon = createPinIcon(true);
    const marker = L.marker([lat, lng], { draggable: true, icon: pinIcon }).addTo(locPickerMaps[mode]);
    locPickerMarkers[mode] = marker;
    marker.on('dragend', function() {
        const pos = marker.getLatLng();
        updatePickerCoordDisplay(mode, pos.lat, pos.lng);
    });
    updatePickerCoordDisplay(mode, lat, lng);
}

function detectLocGPS(mode) {
    if (!navigator.geolocation) {
        alert('Fitur geolokasi tidak didukung oleh browser Anda.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        pos => setLocCoords(mode, pos.coords.latitude, pos.coords.longitude, 'Posisi GPS'),
        err => alert('Gagal mendeteksi lokasi GPS: ' + err.message)
    );
}

function switchLocMapLayer(mode, type) {
    if (!locPickerMaps[mode]) return;
    locPickerMaps[mode].eachLayer(l => {
        if (l instanceof L.TileLayer) locPickerMaps[mode].removeLayer(l);
    });
    locPickerLayers[type]().addTo(locPickerMaps[mode]);
    pickerCurrentLayer[mode] = type;
    ['street','satellite','topo'].forEach(t => {
        const btnId = `${mode}-btn-layer-${t === 'satellite' ? 'sat' : t}`;
        const el = document.getElementById(btnId);
        if (el) el.classList.toggle('active', t === type);
    });
}

async function searchLocMap(mode) {
    const input = document.getElementById(mode + '-map-search');
    const query = input.value.trim();
    if (!query) return;

    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`, {
            headers: { 'Accept-Language': 'id' }
        });
        const data = await res.json();
        if (data && data[0]) {
            setLocCoords(mode, parseFloat(data[0].lat), parseFloat(data[0].lon), data[0].display_name);
        } else {
            alert('Lokasi tidak ditemukan. Coba gunakan nama desa, kecamatan, atau kota.');
        }
    } catch (e) {
        alert('Gagal mencari lokasi: ' + e.message);
    }
}

function syncLocManualCoords(mode) {
    const lat = parseFloat(document.getElementById(mode + '_lat_manual').value);
    const lng = parseFloat(document.getElementById(mode + '_lng_manual').value);
    if (!isNaN(lat) && !isNaN(lng)) {
        setLocCoords(mode, lat, lng, 'Manual Input');
    }
}

function previewLocPhoto(input, previewWrapId) {
    const wrap = document.getElementById(previewWrapId);
    const img = document.getElementById(previewWrapId + '-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ───────── EDIT MODAL OPENER ─────────
function openEditModal(l) {
    document.getElementById('formEditLokasi').action = '/admin/lokasi/' + l.id + '/update';
    document.getElementById('edit_loc_name').value = l.name || '';
    document.getElementById('edit_loc_city').value = l.city || '';
    document.getElementById('edit_loc_province').value = l.province || '';
    document.getElementById('edit_loc_target').value = l.total_trees_target || l.target || 1000;
    document.getElementById('edit_loc_desc').value = l.description || '';
    document.getElementById('edit_loc_active').checked = (l.is_active == 1 || l.active === true);
    document.getElementById('edit-loc-subtitle').textContent = 'Mengedit: ' + (l.name || '');

    const lat = parseFloat(l.latitude || l.lat || 0);
    const lng = parseFloat(l.longitude || l.lng || 0);
    document.getElementById('edit_lat_hidden').value = lat || '';
    document.getElementById('edit_lng_hidden').value = lng || '';
    document.getElementById('edit_lat_manual').value = lat || '';
    document.getElementById('edit_lng_manual').value = lng || '';
    document.getElementById('edit-coord-text').textContent = (lat && lng)
        ? `📍 Latitude: ${lat.toFixed(6)}, Longitude: ${lng.toFixed(6)}`
        : 'Belum ada koordinat';

    const photoSrc = l.photo ? ('/' + l.photo.replace(/^\//, '')) : '/assets/images/coffee-plantation.jpg';
    document.getElementById('edit-current-photo').src = photoSrc;

    new bootstrap.Modal(document.getElementById('modalEditLokasi')).show();
}

document.querySelectorAll('.btn-edit-loc').forEach(btn => {
    btn.addEventListener('click', function() {
        const l = JSON.parse(this.dataset.json);
        openEditModal(l);
    });
});

// Map Tab Triggers
document.getElementById('btn-tab-add-map').addEventListener('click', function() {
    setTimeout(() => {
        const lat = parseFloat(document.getElementById('add_lat_hidden').value) || null;
        const lng = parseFloat(document.getElementById('add_lng_hidden').value) || null;
        initPickerMap('add', lat, lng);
    }, 350);
});

document.getElementById('btn-tab-edit-map').addEventListener('click', function() {
    setTimeout(() => {
        const lat = parseFloat(document.getElementById('edit_lat_hidden').value) || null;
        const lng = parseFloat(document.getElementById('edit_lng_hidden').value) || null;
        initPickerMap('edit', lat, lng);
    }, 350);
});

// ───────── VIEW TOGGLE (TABEL VS KARTU) ─────────
document.querySelectorAll('#viewToggle .btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#viewToggle .btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const v = this.dataset.view;
        document.getElementById('view-table').style.display = v === 'table' ? 'block' : 'none';
        document.getElementById('view-card').style.display  = v === 'card'  ? 'block' : 'none';
    });
});

// ───────── FILTERING / SEARCH ─────────
function applyLocFilters() {
    const searchTerm = document.getElementById('loc-search').value.toLowerCase();
    const province   = document.getElementById('filter-province').value;
    const status     = document.getElementById('filter-status').value;

    document.querySelectorAll('.loc-row').forEach(row => {
        const name     = (row.dataset.name || '').toLowerCase();
        const city     = (row.dataset.city || '').toLowerCase();
        const rowProv  = row.dataset.province || '';
        const rowStat  = row.dataset.status || '';

        const matchSearch   = !searchTerm || name.includes(searchTerm) || city.includes(searchTerm);
        const matchProvince = !province || rowProv === province;
        const matchStatus   = status === '' || rowStat === status;

        const show = matchSearch && matchProvince && matchStatus;
        row.style.display = show ? '' : 'none';
    });

    const visibleCount = document.querySelectorAll('#locTableBody .loc-row:not([style*="display: none"])').length;
    document.getElementById('loc-count-label').textContent = visibleCount + ' lokasi';
}

document.getElementById('loc-search').addEventListener('input', applyLocFilters);
document.getElementById('filter-province').addEventListener('change', applyLocFilters);
document.getElementById('filter-status').addEventListener('change', applyLocFilters);

// ───────── UTILS: SALIN KOORDINAT GPS + TOAST ─────────
function copyGpsCoords(lat, lng) {
    const text = `${lat},${lng}`;
    navigator.clipboard.writeText(text).then(() => {
        showToast(`Koordinat GPS [${text}] disalin ke clipboard!`);
    }).catch(() => {
        showToast('Gagal menyalin koordinat.');
    });
}

function showToast(message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast-gps';
    toast.innerHTML = `<i class="bi bi-check-circle-fill text-success fs-5"></i><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}

// ───────── EXPORT GEOJSON FORMAT ─────────
function exportGeoJson() {
    const features = LOC_DATA.filter(l => l.lat && l.lng).map(loc => ({
        type: "Feature",
        geometry: {
            type: "Point",
            coordinates: [loc.lng, loc.lat]
        },
        properties: {
            id: loc.id,
            name: loc.name,
            city: loc.city,
            province: loc.province,
            target_trees: loc.target,
            planted_trees: loc.planted,
            is_active: loc.active,
            total_events: loc.events,
            description: loc.description
        }
    }));

    const geoJson = {
        type: "FeatureCollection",
        crs: {
            type: "name",
            properties: { name: "urn:ogc:def:crs:OGC:1.3:CRS84" }
        },
        features: features
    };

    const blob = new Blob([JSON.stringify(geoJson, null, 2)], { type: "application/geo+json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `lokasi_penanaman_${new Date().toISOString().slice(0,10)}.geojson`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('File GeoJSON berhasil diunduh!');
}

// ───────── INITIALIZE ON DOM READY ─────────
document.addEventListener('DOMContentLoaded', function() {
    initOverviewMap();

    // Flash message auto dismiss
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);
});
</script>
