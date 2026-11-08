<?php
/**
 * View: public/shop/shipping_calculator.php
 * Halaman Publik: Mesin Cek Ongkir Mandiri (Internal Shipping Rate Engine)
 * Plant For Fun (Kerenceng Coffee Expedition & andu)
 */
?>

<div class="py-4 py-md-5" style="background-color: #f6f8f5;">
    <div class="container">
        
        <!-- Header Banner -->
        <div class="text-center max-w-700 mx-auto mb-4 mb-md-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(18,56,44,0.08); border: 1px solid rgba(18,56,44,0.15);">
                <span class="badge bg-success rounded-pill font-mono" style="font-size: 10px;">STANDALONE ENGINE</span>
                <span class="font-poster text-uppercase small" style="color: #12382c; letter-spacing: 1.5px; font-size: 11px;">
                    100% MANDIRI &bull; TANPA API PIHAK KETIGA
                </span>
            </div>
            <h1 class="display-5 font-poster text-uppercase mb-2" style="color: #12382c; letter-spacing: 1px;">
                Mesin Cek Ongkir Mandiri
            </h1>
            <p class="text-secondary lead fs-6 mb-0">
                Hitung tarif pengiriman kopi spesialti Kerenceng dan bibit agroforestri dari Cicalengka, Kab. Bandung ke seluruh 38 provinsi di Indonesia secara instan dan akurat.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            
            <!-- Kolom Formulir Input Kalkulator -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 position-sticky" style="top: 90px; border: 1px solid rgba(18,56,44,0.1) !important;">
                    <h5 class="fw-bold font-poster text-uppercase mb-3" style="color: #12382c;">
                        <i class="bi bi-calculator text-success me-2"></i>Kalkulator Pengiriman
                    </h5>

                    <!-- Asal Pengiriman (Origin) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary font-mono" style="font-size: 11px;">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>Asal Pengiriman (Origin)
                        </label>
                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle p-2 rounded-circle" style="color:#0a3622;">
                                <i class="bi bi-shop fs-5"></i>
                            </span>
                            <div>
                                <div class="fw-bold text-dark small">Kebon Kopi Kerenceng & andu Coffee Lab</div>
                                <div class="text-muted" style="font-size: 11px;">Cicalengka, Kabupaten Bandung, Jawa Barat (40395)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kota Tujuan (Searchable with Autocomplete) -->
                    <div class="mb-3 position-relative">
                        <label class="form-label small fw-bold text-uppercase text-secondary font-mono" style="font-size: 11px;">
                            <i class="bi bi-pin-map-fill text-success me-1"></i>Kota / Kabupaten Tujuan *
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="destinationInput" class="form-control border-start-0 ps-0" 
                                   placeholder="Ketik nama kota, misal: Bandung, Jakarta, Surabaya..." 
                                   value="<?= htmlspecialchars($defaultDestination) ?>" 
                                   autocomplete="off">
                        </div>
                        
                        <!-- Dropdown Hasil Autocomplete -->
                        <div id="autocompleteList" class="list-group position-absolute w-100 shadow-lg rounded-3 mt-1 d-none" 
                             style="z-index: 1050; max-height: 250px; overflow-y: auto;">
                            <!-- Diisi via JavaScript -->
                        </div>

                        <!-- Quick Destination Pills -->
                        <div class="mt-2">
                            <span class="text-muted" style="font-size: 11px;">Tujuan populer:</span>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <?php 
                                $quickCities = ['Kota Bandung', 'Jakarta Selatan', 'Kota Surabaya', 'Kota Yogyakarta', 'Kota Semarang', 'Denpasar (Bali)', 'Kota Medan', 'Kota Makassar'];
                                foreach ($quickCities as $qc): 
                                ?>
                                    <button type="button" class="btn btn-sm btn-light border py-0 px-2 rounded-pill quick-city-btn" 
                                            data-city="<?= htmlspecialchars($qc) ?>" style="font-size: 11px;">
                                        <?= htmlspecialchars($qc) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Berat Kiriman (Gram / Kg) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-uppercase text-secondary font-mono mb-0" style="font-size: 11px;">
                                <i class="bi bi-box-seam text-primary me-1"></i>Berat Paket (Gram)
                            </label>
                            <span class="badge bg-secondary-subtle text-secondary font-mono" id="weightKgDisplay">
                                = <?= number_format($defaultWeight / 1000, 2) ?> kg
                            </span>
                        </div>
                        <input type="number" id="weightInput" class="form-control" value="<?= (int)$defaultWeight ?>" min="100" step="50">
                        
                        <!-- Preset Berat Paket Kopi & Merchandise -->
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill weight-preset-btn" data-weight="200" style="font-size: 11px;">
                                ☕ 1 Pouch (200g)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill weight-preset-btn" data-weight="600" style="font-size: 11px;">
                                ☕ 3 Pouch (600g)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill weight-preset-btn" data-weight="1000" style="font-size: 11px;">
                                📦 1 kg
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill weight-preset-btn" data-weight="2000" style="font-size: 11px;">
                                🌿 Bibit + Kopi (2kg)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill weight-preset-btn" data-weight="10000" style="font-size: 11px;">
                                🚚 Kargo (10kg)
                            </button>
                        </div>
                    </div>

                    <button type="button" id="btnHitung" class="btn btn-success btn-lg w-100 fw-bold font-poster text-uppercase py-3 rounded-pill shadow-sm" style="letter-spacing: 1px;">
                        <i class="bi bi-search me-1"></i>Hitung Ongkos Kirim
                    </button>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="bi bi-shield-check text-success me-1"></i>Perhitungan 100% lokal, cepat & tanpa kuota pihak ketiga.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Kolom Hasil Tarif Kurir -->
            <div class="col-lg-7">
                
                <!-- Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="text-muted small">Tujuan Terpilih:</span>
                            <h5 class="fw-bold mb-0 text-dark" id="displayDestination"><?= htmlspecialchars($initialRates['matched_city'] ?? $defaultDestination) ?></h5>
                            <div class="text-muted small" id="displayZone">
                                <?= htmlspecialchars($initialRates['zone_name'] ?? 'Jabodetabek & Sekitarnya') ?> &bull; Tarif Dasar Rp <?= number_format($initialRates['base_rate_per_kg'] ?? 10000, 0, ',', '.') ?>/kg
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle px-3 py-2 rounded-pill font-mono fs-6" style="color:#0a3622;" id="displayWeightBadge">
                                Berat: <?= number_format($defaultWeight / 1000, 2) ?> kg
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Container Hasil Kurir -->
                <div id="resultsContainer" class="d-flex flex-column gap-3">
                    <?php if (!empty($initialRates['rates'])): ?>
                        <?php foreach ($initialRates['rates'] as $r): ?>
                            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 card-hover bg-white" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="courier-icon-box p-2 rounded-3 text-center d-flex align-items-center justify-content-center" 
                                             style="width: 52px; height: 52px; background: #f4f7f4; border: 1px solid rgba(18,56,44,0.1);">
                                            <?php if ($r['courier_code'] === 'jne'): ?>
                                                <span class="fw-bold text-primary font-poster" style="font-size: 14px;">JNE</span>
                                            <?php elseif ($r['courier_code'] === 'jnt'): ?>
                                                <span class="fw-bold text-danger font-poster" style="font-size: 14px;">J&T</span>
                                            <?php elseif ($r['courier_code'] === 'sicepat'): ?>
                                                <span class="fw-bold text-warning-emphasis font-poster" style="font-size: 12px;">SICEPAT</span>
                                            <?php elseif ($r['courier_code'] === 'pos'): ?>
                                                <span class="fw-bold text-info-emphasis font-poster" style="font-size: 14px;">POS</span>
                                            <?php elseif ($r['courier_code'] === 'local'): ?>
                                                <span class="fs-4">🚚</span>
                                            <?php else: ?>
                                                <span class="fs-4">☕</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($r['courier']) ?></h6>
                                                <?php if (!empty($r['badge'])): ?>
                                                    <span class="badge bg-<?= $r['badge_color'] ?>-subtle text-<?= $r['badge_color'] ?> px-2 py-1 font-mono" style="font-size: 10px;">
                                                        <?= htmlspecialchars($r['badge']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="fw-semibold text-secondary small"><?= htmlspecialchars($r['service']) ?></div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                <i class="bi bi-clock me-1"></i>Estimasi Tiba: <strong><?= htmlspecialchars($r['etd']) ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-md-end mt-2 mt-md-0">
                                        <?php if ($r['cost'] === 0): ?>
                                            <div class="fs-4 fw-bold text-success font-poster">GRATIS</div>
                                            <span class="badge bg-success text-white font-mono" style="font-size: 10px;">Rp 0</span>
                                        <?php else: ?>
                                            <div class="fs-4 fw-bold text-dark font-poster">
                                                Rp <?= number_format($r['cost'], 0, ',', '.') ?>
                                            </div>
                                            <span class="text-muted" style="font-size: 11px;">Sudah termasuk asuransi dasar</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <span class="text-muted small" style="font-size: 12px;">
                                        <?= htmlspecialchars($r['description'] ?? '') ?>
                                    </span>
                                    <a href="/toko?kategori=kopi" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 font-poster">
                                        Pesan Kopi Kerenceng <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Info Ambil Sendiri ke Kedai (Satu-satunya Gratis Ongkir) -->
                <div class="card border-0 rounded-4 p-4 mt-4 text-white" style="background: linear-gradient(135deg, #12382c 0%, #205c48 100%);">
                    <div class="row align-items-center g-3">
                        <div class="col-md-8">
                            <span class="badge bg-warning text-dark fw-bold font-mono mb-2">GRATIS ONGKIR KHUSUS PICKUP</span>
                            <h5 class="fw-bold font-poster text-uppercase text-white mb-1">Ambil Langsung ke Kedai / Roastery andu</h5>
                            <p class="small text-white-50 mb-0">
                                Ingin bebas ongkos kirim (Rp 0)? Pilih opsi <strong>Ambil Sendiri ke Kedai</strong> saat checkout. Pesanan biji kopi &amp; merchandise dapat diambil langsung di kedai andu Coffee Lab / Basecamp Kerenceng Cicalengka sambil mencicipi seduhan segar!
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="/toko?kategori=kopi" class="btn btn-warning fw-bold font-poster rounded-pill px-4 py-2 text-dark shadow-sm">
                                Belanja Kopi Sekarang →
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
const destinationInput = document.getElementById('destinationInput');
const weightInput = document.getElementById('weightInput');
const weightKgDisplay = document.getElementById('weightKgDisplay');
const btnHitung = document.getElementById('btnHitung');
const autocompleteList = document.getElementById('autocompleteList');
const resultsContainer = document.getElementById('resultsContainer');
const displayDestination = document.getElementById('displayDestination');
const displayZone = document.getElementById('displayZone');
const displayWeightBadge = document.getElementById('displayWeightBadge');

// Update weight display
weightInput.addEventListener('input', function() {
    const val = parseInt(this.value) || 0;
    weightKgDisplay.textContent = '= ' + (val / 1000).toFixed(2) + ' kg';
});

// Preset Buttons
document.querySelectorAll('.weight-preset-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        weightInput.value = this.dataset.weight;
        weightInput.dispatchEvent(new Event('input'));
        triggerCalculation();
    });
});

// Quick City Buttons
document.querySelectorAll('.quick-city-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        destinationInput.value = this.dataset.city;
        triggerCalculation();
    });
});

// Autocomplete Logic
let searchTimeout = null;
destinationInput.addEventListener('input', function() {
    const q = this.value.trim();
    clearTimeout(searchTimeout);
    if (q.length < 2) {
        autocompleteList.classList.add('d-none');
        return;
    }
    searchTimeout = setTimeout(() => {
        fetch('/api/cities?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                if (data.success && data.cities.length > 0) {
                    let html = '';
                    data.cities.forEach(c => {
                        html += `
                            <button type="button" class="list-group-item list-group-item-action py-2 px-3 autocomplete-item"
                                    data-city-name="${c.city_name}">
                                <div class="fw-bold small text-dark">${c.city_name}</div>
                                <div class="text-muted" style="font-size: 11px;">${c.province} &bull; ${c.zone_name}</div>
                            </button>
                        `;
                    });
                    autocompleteList.innerHTML = html;
                    autocompleteList.classList.remove('d-none');

                    document.querySelectorAll('.autocomplete-item').forEach(item => {
                        item.addEventListener('click', function() {
                            destinationInput.value = this.dataset.cityName;
                            autocompleteList.classList.add('d-none');
                            triggerCalculation();
                        });
                    });
                } else {
                    autocompleteList.classList.add('d-none');
                }
            })
            .catch(() => autocompleteList.classList.add('d-none'));
    }, 250);
});

// Close autocomplete when clicking outside
document.addEventListener('click', function(e) {
    if (!destinationInput.contains(e.target) && !autocompleteList.contains(e.target)) {
        autocompleteList.classList.add('d-none');
    }
});

btnHitung.addEventListener('click', triggerCalculation);

function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function triggerCalculation() {
    const city = destinationInput.value.trim();
    const weight = parseInt(weightInput.value) || 1000;

    if (!city) {
        alert('Silakan masukkan nama kota atau kabupaten tujuan.');
        destinationInput.focus();
        return;
    }

    resultsContainer.innerHTML = `
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="spinner-border text-success mx-auto mb-3" role="status"></div>
            <div class="fw-bold text-dark">Menghitung tarif ongkos kirim...</div>
            <div class="text-muted small">0ms delay internal engine</div>
        </div>
    `;

    fetch('/api/ongkir', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'city=' + encodeURIComponent(city) + '&weight=' + encodeURIComponent(weight)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.rates.length > 0) {
            displayDestination.textContent = data.info.matched_city || city;
            displayZone.textContent = data.info.zone_name + ' • Tarif Dasar Rp ' + Number(data.info.base_rate_per_kg).toLocaleString('id-ID') + '/kg';
            displayWeightBadge.textContent = 'Berat: ' + (weight / 1000).toFixed(2) + ' kg';

            let html = '';
            data.rates.forEach(r => {
                let icon = '☕';
                if (r.courier_code === 'jne') icon = '<span class="fw-bold text-primary font-poster" style="font-size: 14px;">JNE</span>';
                else if (r.courier_code === 'jnt') icon = '<span class="fw-bold text-danger font-poster" style="font-size: 14px;">J&T</span>';
                else if (r.courier_code === 'sicepat') icon = '<span class="fw-bold text-warning-emphasis font-poster" style="font-size: 12px;">SICEPAT</span>';
                else if (r.courier_code === 'pos') icon = '<span class="fw-bold text-info-emphasis font-poster" style="font-size: 14px;">POS</span>';
                else if (r.courier_code === 'local') icon = '🚚';

                const badgeHtml = r.badge ? `<span class="badge bg-${r.badge_color}-subtle text-${r.badge_color} px-2 py-1 font-mono" style="font-size: 10px;">${r.badge}</span>` : '';
                const costHtml = (r.cost === 0) 
                    ? `<div class="fs-4 fw-bold text-success font-poster">GRATIS</div><span class="badge bg-success text-white font-mono" style="font-size: 10px;">Rp 0</span>`
                    : `<div class="fs-4 fw-bold text-dark font-poster">${formatRupiah(r.cost)}</div><span class="text-muted" style="font-size: 11px;">Sudah termasuk asuransi dasar</span>`;

                html += `
                    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 card-hover bg-white" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="courier-icon-box p-2 rounded-3 text-center d-flex align-items-center justify-content-center" 
                                     style="width: 52px; height: 52px; background: #f4f7f4; border: 1px solid rgba(18,56,44,0.1);">
                                    ${icon}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold mb-0 text-dark">${r.courier}</h6>
                                        ${badgeHtml}
                                    </div>
                                    <div class="fw-semibold text-secondary small">${r.service}</div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        <i class="bi bi-clock me-1"></i>Estimasi Tiba: <strong>${r.etd}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="text-md-end mt-2 mt-md-0">
                                ${costHtml}
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span class="text-muted small" style="font-size: 12px;">
                                ${r.description || ''}
                            </span>
                            <a href="/toko?kategori=kopi" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 font-poster">
                                Pesan Kopi Kerenceng <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                `;
            });
            resultsContainer.innerHTML = html;
        } else {
            resultsContainer.innerHTML = `
                <div class="alert alert-warning rounded-4 p-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>Tidak dapat menemukan tarif untuk wilayah tersebut. Silakan pilih dari saran kota yang muncul.
                </div>
            `;
        }
    })
    .catch(err => {
        resultsContainer.innerHTML = `
            <div class="alert alert-danger rounded-4 p-4">
                Terjadi kesalahan teknis saat menghitung tarif. Silakan coba lagi.
            </div>
        `;
    });
}
</script>

