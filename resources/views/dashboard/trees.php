<!-- Leaflet.js Map Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0">Pohon Saya</h3>
        <p class="text-muted small mb-0">Pantau pertumbuhan, titik koordinat geografis, dan foto dokumentasi pohon yang telah Anda donasikan.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-success fw-bold rounded-pill px-3 btn-sm" onclick="shareImpactWhatsApp()">
            <i class="bi bi-whatsapp me-1"></i> Bagikan Dampak
        </button>
        <a href="<?= url('/donasi') ?>" class="btn btn-success fw-bold rounded-pill px-4 btn-sm shadow-sm">
            🌱 Tambah Pohon
        </a>
    </div>
</div>

<!-- Kartu Ringkasan Dampak Hijau -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <span class="text-secondary small fw-semibold">Total Pohon Terdaftar</span>
            <h3 class="fw-bold text-success mb-0"><?= number_format($totalTrees) ?> Batang</h3>
            <small class="text-muted" style="font-size: 11px;">Aktif dipantau di lapangan</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <span class="text-secondary small fw-semibold">Variasi Spesies</span>
            <h3 class="fw-bold text-primary mb-0"><?= count($speciesCount) ?> Jenis Pohon</h3>
            <small class="text-muted" style="font-size: 11px;"><?= implode(', ', array_keys($speciesCount)) ?: 'Belum ada' ?></small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <span class="text-secondary small fw-semibold">Estimasi Serapan Karbon</span>
            <h3 class="fw-bold text-info mb-0"><?= number_format($totalCo2, 1) ?> kg / tahun</h3>
            <small class="text-muted" style="font-size: 11px;">Membantu membersihkan udara</small>
        </div>
    </div>
</div>

<!-- Peta Sebaran Pohon (Leaflet.js) -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Peta Sebaran Pohon Saya</h5>
            <small class="text-muted">Titik koordinat penanaman bibit pohon Anda</small>
        </div>
        <span class="badge bg-success-subtle border border-success-subtle px-3 py-1 rounded-pill" style="color:#0a3622;">
            <i class="bi bi-geo-alt-fill me-1"></i> <?= count($mapMarkers) ?> Titik Koordinat
        </span>
    </div>
    
    <div id="treesMap" style="height: 380px; width: 100%; border-radius: 1rem; z-index: 1;"></div>
</div>

<!-- Katalog Kartu Pohon Individu -->
<h5 class="fw-bold text-dark mb-3">Daftar Pohon Individu</h5>

<?php if (!empty($trees)): ?>
    <div class="row g-3">
        <?php foreach ($trees as $tree): ?>
            <?php 
                $statusMap = [
                    'ditanam'  => ['class' => 'bg-info text-dark', 'label' => '🌱 Baru Ditanam'],
                    'tumbuh'   => ['class' => 'bg-success text-white', 'label' => '🌿 Tumbuh Baik'],
                    'dipantau' => ['class' => 'bg-primary text-white', 'label' => '🔍 Dipantau'],
                ];
                $st = $statusMap[$tree['status']] ?? ['class' => 'bg-secondary', 'label' => ucfirst($tree['status'])];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white card-hover">
                    <div class="bg-light text-center py-4 position-relative" style="min-height: 140px;">
                        <span class="display-3">🌳</span>
                        <span class="position-absolute top-0 end-0 m-3 badge <?= $st['class'] ?> rounded-pill px-2 py-1 small">
                            <?= $st['label'] ?>
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="font-monospace fw-bold text-success" style="font-size: 13px;"><?= e($tree['code']) ?></span>
                            <small class="text-muted"><?= format_date($tree['planted_at'] ?? $tree['created_at'], 'd M Y') ?></small>
                        </div>

                        <h5 class="fw-bold text-dark mb-1">Pohon <?= e($tree['species_name']) ?></h5>
                        
                        <div class="small text-muted mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            <?= e($tree['location_name'] ?? 'Kawasan Konservasi') ?>, <?= e($tree['location_city'] ?? '') ?>
                        </div>

                        <div class="p-2 bg-light rounded-3 small text-secondary mb-3 flex-grow-1 border">
                            <strong>Catatan Pemantauan:</strong><br>
                            <?= e($tree['latest_notes'] ?? 'Pohon telah berakar dan dirawat rutin oleh tim kehutanan lokal.') ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small text-muted">
                                <i class="bi bi-clouds text-info me-1"></i> <?= number_format($tree['co2_absorption_kg_per_year'], 1) ?> kg CO₂/thn
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="shareTreeCode('<?= e($tree['code']) ?>', '<?= e($tree['species_name']) ?>')">
                                <i class="bi bi-share me-1"></i> Bagikan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4">
        <div class="display-1 text-muted mb-3">🌳</div>
        <h4 class="fw-bold text-dark">Belum Ada Pohon Tertanam</h4>
        <p class="text-muted small mb-4">Setelah donasi diverifikasi oleh tim kami, pohon Anda akan otomatis terbit di peta dengan kode unik pemantauan.</p>
        <div>
            <a href="<?= url('/donasi') ?>" class="btn btn-success fw-bold rounded-pill px-5 shadow-sm">
                Donasi Pohon Sekarang →
            </a>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const markersData = <?= json_encode($mapMarkers) ?>;
    
    // Titik pusat default (Indonesia / Jabodetabek)
    let defaultLat = -6.3200000;
    let defaultLng = 106.6800000;

    if (markersData.length > 0) {
        defaultLat = markersData[0].lat;
        defaultLng = markersData[0].lng;
    }

    const map = L.map('treesMap').setView([defaultLat, defaultLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    markersData.forEach(item => {
        const popupContent = `
            <div style="font-family: inherit; font-size: 13px;">
                <strong style="color: #198754; font-size: 14px;">Pohon ${item.species}</strong><br>
                <code style="font-size: 11px;">${item.code}</code><br>
                <strong>Status:</strong> ${item.status}<br>
                <strong>Lokasi:</strong> ${item.location}<br>
                <strong>Ditanam:</strong> ${item.planted}
            </div>
        `;
        L.marker([item.lat, item.lng]).addTo(map).bindPopup(popupContent);
    });
});

function shareImpactWhatsApp() {
    const text = 'Alhamdulillah, saya telah berdonasi <?= $totalTrees ?> pohon melalui Plant For Fun untuk menghijaukan Indonesia dan menyerap estimasi <?= number_format($totalCo2, 1) ?> kg CO2/tahun. Yuk ikut ambil bagian di: ' + window.location.origin;
    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
}

function shareTreeCode(code, species) {
    const text = 'Saya baru saja menanam pohon ' + species + ' dengan kode unik ' + code + ' di Plant For Fun! Pantau di: ' + window.location.origin + '/peta-lokasi';
    window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(text), '_blank');
}
</script>

