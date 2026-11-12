<?php
/**
 * View: Peta Sebaran Pohon Interaktif (Leaflet.js & OpenStreetMap)
 * Path: resources/views/public/map.php
 */
?>

<!-- Leaflet CSS & Map Custom Styling -->
<?php $extraCss = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #treeMap {
        height: 600px;
        width: 100%;
        border-radius: 1rem;
        z-index: 1;
    }
    .custom-tree-pin {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #198754;
        color: white;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        font-size: 16px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .custom-tree-pin:hover {
        transform: scale(1.15);
    }
    .custom-tree-pin.status-ditanam { background: #0d6efd; }
    .custom-tree-pin.status-tumbuh { background: #198754; }
    .custom-tree-pin.status-dipantau { background: #fd7e14; }
    .leaflet-popup-content-wrapper {
        border-radius: 16px;
        padding: 6px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .leaflet-popup-content {
        margin: 12px;
        line-height: 1.4;
    }
</style>
'; ?>

<section class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/transparansi') ?>" class="text-decoration-none text-muted">Transparansi</a></li>
                <li class="breadcrumb-item active text-success fw-semibold" aria-current="page">Peta Sebaran Pohon</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="h2 fw-bold text-dark mb-1">Peta Sebaran Pohon Konservasi</h1>
                <p class="text-secondary mb-0">Lacak titik koordinat GPS riil pohon-pohon yang telah Anda dan para donatur tanam di alam bebas.</p>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0">
                <a href="<?= url('/donasi') ?>" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm">
                    <i class="bi bi-heart-fill me-1"></i> Tanam Pohon Baru
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-4 bg-light">
    <div class="container">

        <!-- Baris Filter Interaktif -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <div class="row g-3 align-items-center">
                <!-- Filter Lokasi -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Kawasan / Lokasi</label>
                    <select id="filterLocation" class="form-select rounded-pill">
                        <option value="">Semua Wilayah Konservasi</option>
                        <?php foreach ($locationList as $loc): ?>
                            <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?> (<?= htmlspecialchars($loc['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Spesies -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Jenis / Spesies Pohon</label>
                    <select id="filterSpecies" class="form-select rounded-pill">
                        <option value="">Semua Spesies Pohon</option>
                        <?php foreach ($speciesList as $sp): ?>
                            <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Status Pertumbuhan</label>
                    <select id="filterStatus" class="form-select rounded-pill">
                        <option value="">Semua Status</option>
                        <option value="ditanam">Baru Ditanam</option>
                        <option value="tumbuh">Tumbuh Sehat</option>
                        <option value="dipantau">Dalam Pemantauan</option>
                    </select>
                </div>

                <!-- Tombol Reset & Counter -->
                <div class="col-md-3 d-flex align-items-end justify-content-between pt-md-3">
                    <button type="button" id="btnResetFilter" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                    <div class="text-end">
                        <span class="small text-muted d-block">Titik Pohon Aktif</span>
                        <span id="treeCounter" class="fw-bold text-success fs-5"><?= $totalPlanted ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wadah Peta Digital -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div id="treeMap"></div>
        </div>

        <!-- Legenda & Keterangan Pin -->
        <div class="d-flex flex-wrap justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <span class="small fw-bold text-dark text-uppercase">Legenda Pin:</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-primary p-2" style="width: 14px; height: 14px;"></span>
                    <span class="small text-secondary">Baru Ditanam</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-success p-2" style="width: 14px; height: 14px;"></span>
                    <span class="small text-secondary">Tumbuh Sehat</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle bg-warning p-2" style="width: 14px; height: 14px;"></span>
                    <span class="small text-secondary">Dalam Pemantauan Intensif</span>
                </div>
            </div>
            <div class="small text-muted mt-2 mt-md-0">
                <i class="bi bi-info-circle me-1"></i> Klik marker pin di peta untuk melihat foto & metrik pohon.
            </div>
        </div>

        <!-- Daftar Kawasan Hotspot Konservasi -->
        <div class="my-4">
            <h4 class="fw-bold text-dark mb-3">Kawasan Konservasi Utama</h4>
            <div class="row g-3">
                <?php foreach ($locationList as $loc): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($loc['name']) ?></h6>
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($loc['city'] . ', ' . $loc['province']) ?>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 btn-focus-loc" data-id="<?= $loc['id'] ?>">
                                    <i class="bi bi-crosshair me-1"></i> Fokuskan Peta
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<!-- Leaflet JS & Script Interaktivitas -->
<?php $extraJs = '
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const rawTrees = ' . $treesJson . ';
    let markersLayer = L.layerGroup();

    // Inisialisasi Peta Leaflet berpusat di Indonesia
    const map = L.map("treeMap", {
        scrollWheelZoom: true
    }).setView([-2.5489, 118.0149], 5);

    // Tambahkan layer Tile OpenStreetMap
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors | Plant For Fun"
    }).addTo(map);

    markersLayer.addTo(map);

    function createMarker(tree) {
        let statusClass = "status-" + (tree.status || "tumbuh");
        let iconHtml = `<div class="custom-tree-pin ${statusClass}"><i class="bi bi-tree-fill"></i></div>`;

        let customIcon = L.divIcon({
            html: iconHtml,
            className: "",
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -19]
        });

        let marker = L.marker([tree.latitude, tree.longitude], { icon: customIcon });

        let photoHtml = "";
        if (tree.latest_photo) {
            photoHtml = `<img src="/storage/uploads/${tree.latest_photo}" class="w-100 rounded-3 mb-2 object-fit-cover" style="height: 120px;" alt="Foto Pohon">`;
        }

        let statusBadge = "";
        if (tree.status === "tumbuh") {
            statusBadge = `<span class="badge bg-success-subtle border border-success-subtle rounded-pill" style="color:#0a3622;">Tumbuh Sehat</span>`;
        } else if (tree.status === "dipantau") {
            statusBadge = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Dipantau</span>`;
        } else {
            statusBadge = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Ditanam</span>`;
        }

        let popupContent = `
            <div style="min-width: 220px;">
                ${photoHtml}
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold font-monospace text-dark">${tree.code}</span>
                    ${statusBadge}
                </div>
                <h6 class="fw-bold text-success mb-1">${tree.species_name}</h6>
                <div class="small text-muted mb-2">
                    <i class="bi bi-geo-alt text-danger me-1"></i>${tree.location_name || "-"}
                </div>
                <div class="p-2 rounded bg-light small mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Serapan CO₂:</span>
                        <strong class="text-success">${tree.co2_absorption_kg_per_year} kg/thn</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Ditanam:</span>
                        <span class="text-dark">${tree.planted_at_formatted || "-"}</span>
                    </div>
                </div>
                ${tree.latest_notes ? `<div class="small text-secondary fst-italic">"${tree.latest_notes}"</div>` : ""}
            </div>
        `;

        marker.bindPopup(popupContent);
        return marker;
    }

    function renderMarkers(treeList) {
        markersLayer.clearLayers();
        let bounds = [];

        treeList.forEach(tree => {
            if (tree.latitude && tree.longitude) {
                let marker = createMarker(tree);
                markersLayer.addLayer(marker);
                bounds.push([tree.latitude, tree.longitude]);
            }
        });

        document.getElementById("treeCounter").textContent = treeList.length;

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
        }
    }

    // Render pohon awal
    renderMarkers(rawTrees);

    // Filter dinamis dengan fetch API
    function applyFilter() {
        let locId = document.getElementById("filterLocation").value;
        let spId = document.getElementById("filterSpecies").value;
        let stVal = document.getElementById("filterStatus").value;

        let query = new URLSearchParams({
            location_id: locId,
            species_id: spId,
            status: stVal
        });

        fetch("/api/peta-pohon?" + query.toString())
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    renderMarkers(res.data);
                }
            })
            .catch(err => {
                console.error("Gagal memuat filter pohon:", err);
            });
    }

    document.getElementById("filterLocation").addEventListener("change", applyFilter);
    document.getElementById("filterSpecies").addEventListener("change", applyFilter);
    document.getElementById("filterStatus").addEventListener("change", applyFilter);

    document.getElementById("btnResetFilter").addEventListener("click", function() {
        document.getElementById("filterLocation").value = "";
        document.getElementById("filterSpecies").value = "";
        document.getElementById("filterStatus").value = "";
        renderMarkers(rawTrees);
    });

    // Tombol fokuskan peta per lokasi
    document.querySelectorAll(".btn-focus-loc").forEach(btn => {
        btn.addEventListener("click", function() {
            let locId = this.dataset.id;
            document.getElementById("filterLocation").value = locId;
            applyFilter();
            window.scrollTo({ top: document.getElementById("treeMap").offsetTop - 100, behavior: "smooth" });
        });
    });

    // Tangani parameter URL jika ada ?location_id=X
    let urlParams = new URLSearchParams(window.location.search);
    let initialLocId = urlParams.get("location_id");
    if (initialLocId) {
        document.getElementById("filterLocation").value = initialLocId;
        applyFilter();
    }
});
</script>
'; ?>

