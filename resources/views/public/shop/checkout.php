<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 text-center">
            <span class="badge bg-success-subtle fw-bold px-3 py-2 rounded-pill mb-2" style="color:#0a3622;">Langkah Akhir</span>
            <h1 class="fw-bold">Checkout & Pengiriman</h1>
            <p class="text-muted">Lengkapi alamat pengiriman merchandise Anda atau pilih opsi ambil di lokasi acara penanaman.</p>
        </div>
    </div>

    <form action="<?= url('/checkout/proses') ?>" method="POST" id="checkoutForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Kolom Kiri: Alamat & Opsi Pengiriman -->
            <div class="col-lg-8">

                <!-- Opsi Metode Pengiriman -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <span class="badge bg-success me-2">1</span> Metode Pengiriman
                    </h5>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="card h-100 p-3 border cursor-pointer delivery-method-card border-success bg-success-subtle" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="pickup_at_event" value="0" class="form-check-input mt-0" checked onchange="toggleDeliveryMethod(this)">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-truck text-success me-1"></i> Kirim ke Alamat</h6>
                                        <p class="text-muted small mb-0">Dikirim via kurir ekspedisi (JNE, POS, TIKI) ke rumah Anda.</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-sm-6">
                            <label class="card h-100 p-3 border cursor-pointer delivery-method-card" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="pickup_at_event" value="1" class="form-check-input mt-0" onchange="toggleDeliveryMethod(this)">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-geo-alt-fill text-success me-1"></i> Ambil ke Kedai (Gratis Ongkir)</h6>
                                        <p class="text-muted small mb-0">Gratis ongkir Rp 0 khusus ambil langsung di Kedai andu atau lokasi acara.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Bagian Pengambilan Langsung (Muncul jika dipilih) -->
                <div class="card border-0 shadow-sm p-4 mb-4 d-none" id="eventPickupBox">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shop text-success me-2"></i>Pilih Lokasi Pengambilan (Gratis Ongkir)</h5>
                    <div class="mb-3">
                        <label for="pickup_location" class="form-label fw-semibold small text-secondary">Titik Pengambilan:</label>
                        <select class="form-select" id="pickup_location" name="pickup_location">
                            <option value="kedai" selected>
                                ☕ Kedai / Roastery andu Coffee Lab &amp; Basecamp Kerenceng (Cicalengka, Kab. Bandung) — Siap Diambil di Kedai
                            </option>
                            <?php if (!empty($upcomingEvents)): ?>
                                <optgroup label="Ambil di Lokasi Acara Penanaman Hari-H:">
                                    <?php foreach ($upcomingEvents as $ev): ?>
                                        <option value="<?= $ev['id'] ?>">
                                            📍 <?= e($ev['title']) ?> (<?= format_date($ev['event_date'], 'd F Y') ?>) - <?= e($ev['gathering_point']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        <div class="form-text small text-success mt-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Bebas ongkos kirim (Rp 0). Pesanan dapat diambil langsung di kedai andu atau meja registrasi acara penanaman.
                        </div>
                    </div>
                </div>

                <!-- Data Penerima & Alamat Pengiriman -->
                <div class="card border-0 shadow-sm p-4 mb-4" id="shippingAddressBox">
                    <h5 class="fw-bold text-dark mb-3">
                        <span class="badge bg-success me-2">2</span> Data Penerima & Alamat Lengkap
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="recipient_name" class="form-label fw-semibold small text-secondary">Nama Penerima Paket</label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="<?= e($currentUser['nama'] ?? old('recipient_name')) ?>" placeholder="Nama lengkap penerima" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold small text-secondary">Nomor WhatsApp / HP</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= e($currentUser['phone'] ?? old('phone')) ?>" placeholder="081234567890" required>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email (Untuk Notifikasi Resi)</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= e($currentUser['email'] ?? old('email')) ?>" placeholder="nama@email.com" required>
                        </div>

                        <div class="col-md-6 address-field">
                            <label for="province" class="form-label fw-semibold small text-secondary">Provinsi</label>
                            <input type="text" class="form-control" id="province" name="province" value="<?= e(old('province', 'DKI Jakarta')) ?>" placeholder="Contoh: Jawa Barat" required>
                        </div>

                        <div class="col-md-6 address-field position-relative">
                            <label for="city" class="form-label fw-semibold small text-secondary">Kota / Kabupaten (Ketik untuk cari)</label>
                            <input type="text" class="form-control" id="city" name="city" value="<?= e(old('city', 'Jakarta Selatan')) ?>" placeholder="Contoh: Kota Bandung / Jakarta Selatan" autocomplete="off" required onchange="loadShippingRates()">
                            <div id="checkoutCityAutocomplete" class="list-group position-absolute w-100 shadow-lg rounded-3 mt-1 d-none" style="z-index: 1050; max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="col-12 address-field">
                            <label for="full_address" class="form-label fw-semibold small text-secondary">Alamat Lengkap (Jalan, RT/RW, Kelurahan, Kecamatan)</label>
                            <textarea class="form-control" id="full_address" name="full_address" rows="3" placeholder="Jl. Anggrek No. 12, RT 02 / RW 05, Kelurahan..." required><?= e(old('full_address')) ?></textarea>
                        </div>

                        <div class="col-md-6 address-field">
                            <label for="postal_code" class="form-label fw-semibold small text-secondary">Kode Pos (Opsional)</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?= e(old('postal_code')) ?>" placeholder="12345">
                        </div>
                    </div>
                </div>

                <!-- Pilihan Kurir & Layanan Pengiriman (Muncul jika kirim ke alamat) -->
                <div class="card border-0 shadow-sm p-4 mb-4" id="courierSelectionBox">
                    <h5 class="fw-bold text-dark mb-2">
                        <span class="badge bg-success me-2">3</span> Pilihan Layanan Kurir
                    </h5>
                    <p class="text-muted small mb-3">Tarif dihitung otomatis berdasarkan berat pesanan (<?= number_format($totalWeight / 1000, 2) ?> kg) dan kota tujuan Anda.</p>

                    <div id="courierOptionsContainer" class="d-flex flex-column gap-2">
                        <div class="text-center py-3 text-muted small">
                            <div class="spinner-border spinner-border-sm text-success me-1" role="status"></div>
                            Menghitung tarif ongkos kirim...
                        </div>
                    </div>
                </div>

                <!-- Catatan Pesanan -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <label for="notes" class="form-label fw-semibold small text-secondary">Catatan untuk Penjual / Kurir (Opsional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Tinggalkan di teras rumah jika tidak ada orang..."><?= e(old('notes')) ?></textarea>
                </div>

            </div>

            <!-- Kolom Kanan: Rincian Pesanan & Pembayaran -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top" style="top: 90px;">
                    <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Rincian Pembayaran</h5>

                    <!-- Item Ringkas -->
                    <div class="mb-3">
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex justify-content-between small text-secondary mb-2">
                                <div>
                                    <strong class="text-dark"><?= e($item['product_name']) ?></strong>
                                    <div class="text-muted" style="font-size: 11px;">Ukuran <?= $item['size'] ?> &bull; <?= $item['quantity'] ?> pcs</div>
                                </div>
                                <span class="fw-semibold"><?= format_rupiah($item['subtotal']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between mb-2 text-secondary small">
                        <span>Subtotal Produk:</span>
                        <strong class="text-dark" id="displaySubtotal"><?= format_rupiah($subtotal) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 text-secondary small">
                        <span>Ongkos Kirim:</span>
                        <strong class="text-success" id="displayShippingCost">Rp 0</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 text-secondary small">
                        <span>Kode Unik Verifikasi:</span>
                        <span class="badge bg-warning text-dark fw-bold">+3 Digit</span>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark">Estimasi Total:</span>
                        <h4 class="fw-bold text-success mb-0" id="displayGrandTotal"><?= format_rupiah($subtotal) ?></h4>
                    </div>

                    <div class="small bg-light p-2 rounded mb-3 text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i> Pembayaran manual via Transfer Bank/QRIS. Stok varian ditahan selama 24 jam.
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold rounded-pill py-3 shadow-sm">
                        Buat Pesanan & Bayar →
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const subtotalBase = <?= (float)$subtotal ?>;
let currentShippingCost = 0;

function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function toggleDeliveryMethod(radio) {
    const isPickup = (radio.value === '1');
    const eventBox = document.getElementById('eventPickupBox');
    const addressBox = document.getElementById('shippingAddressBox');
    const courierBox = document.getElementById('courierSelectionBox');
    const addressFields = document.querySelectorAll('.address-field input, .address-field textarea');

    // Update active visual card
    document.querySelectorAll('.delivery-method-card').forEach(c => c.classList.remove('border-success', 'bg-success-subtle'));
    radio.closest('.delivery-method-card').classList.add('border-success', 'bg-success-subtle');

    if (isPickup) {
        eventBox.classList.remove('d-none');
        courierBox.classList.add('d-none');
        addressFields.forEach(f => f.required = false);
        currentShippingCost = 0;
        updateTotals();
    } else {
        eventBox.classList.add('d-none');
        courierBox.classList.remove('d-none');
        addressFields.forEach(f => f.required = true);
        loadShippingRates();
    }
}

function updateTotals() {
    document.getElementById('displayShippingCost').textContent = formatRupiah(currentShippingCost);
    document.getElementById('displayGrandTotal').textContent = formatRupiah(subtotalBase + currentShippingCost);
}

function loadShippingRates() {
    const city = document.getElementById('city').value.trim();
    const container = document.getElementById('courierOptionsContainer');
    if (!city) return;

    container.innerHTML = '<div class="text-center py-2 text-muted small"><div class="spinner-border spinner-border-sm text-success me-1"></div> Mengambil tarif kurir...</div>';

    fetch('<?= url('/api/ongkir') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: 'city=' + encodeURIComponent(city) + '&weight=<?= (int)$totalWeight ?>&_csrf_token=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').content)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.rates.length > 0) {
            let html = '';
            data.rates.forEach((r, idx) => {
                const checked = (idx === 0) ? 'checked' : '';
                if (idx === 0) currentShippingCost = r.cost;

                const textColor = (r.badge_color === 'warning') ? 'text-warning-emphasis' : ((r.badge_color === 'info') ? 'text-info-emphasis' : `text-${r.badge_color}`);
                const badgeHtml = r.badge ? `<span class="badge bg-${r.badge_color}-subtle ${textColor} ms-2 font-mono" style="font-size: 10px;">${r.badge}</span>` : '';

                html += `
                    <label class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between cursor-pointer" style="cursor: pointer;">
                        <div class="d-flex align-items-center gap-3">
                            <input type="radio" name="shipping_service" value="${r.code}" class="form-check-input mt-0" ${checked} onchange="selectShippingRate(${r.cost})" required>
                            <div>
                                <div class="d-flex align-items-center">
                                    <strong class="text-dark">${r.courier}</strong>
                                    ${badgeHtml}
                                </div>
                                <div class="small text-secondary fw-semibold">${r.service}</div>
                                <div class="small text-muted"><i class="bi bi-clock me-1"></i>Estimasi: ${r.etd}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold ${r.cost === 0 ? 'text-success' : 'text-dark'}">${r.cost === 0 ? 'GRATIS' : formatRupiah(r.cost)}</span>
                        </div>
                    </label>
                `;
            });
            container.innerHTML = html;
            updateTotals();
        } else {
            container.innerHTML = '<div class="alert alert-warning small mb-0">Tidak dapat memuat tarif pengiriman. Silakan periksa nama kota.</div>';
        }
    })
    .catch(err => {
        container.innerHTML = '<div class="text-danger small">Gagal memuat ongkos kirim. Silakan ulangi.</div>';
    });
}

function selectShippingRate(cost) {
    currentShippingCost = cost;
    updateTotals();
}

// City Autocomplete in Checkout
const cityInput = document.getElementById('city');
const cityDropdown = document.getElementById('checkoutCityAutocomplete');
let checkoutCityTimer = null;

if (cityInput && cityDropdown) {
    cityInput.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(checkoutCityTimer);
        if (q.length < 2) {
            cityDropdown.classList.add('d-none');
            return;
        }
        checkoutCityTimer = setTimeout(() => {
            fetch('/api/cities?q=' + encodeURIComponent(q))
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.cities.length > 0) {
                        let html = '';
                        data.cities.forEach(c => {
                            html += `
                                <button type="button" class="list-group-item list-group-item-action py-2 px-3 checkout-city-item"
                                        data-city="${c.city_name}" data-province="${c.province}" data-postal="${c.postal_code || ''}">
                                    <div class="fw-bold small text-dark">${c.city_name}</div>
                                    <div class="text-muted" style="font-size: 11px;">${c.province}</div>
                                </button>
                            `;
                        });
                        cityDropdown.innerHTML = html;
                        cityDropdown.classList.remove('d-none');

                        document.querySelectorAll('.checkout-city-item').forEach(item => {
                            item.addEventListener('click', function() {
                                cityInput.value = this.dataset.city;
                                if (document.getElementById('province')) document.getElementById('province').value = this.dataset.province;
                                if (document.getElementById('postal_code') && this.dataset.postal) document.getElementById('postal_code').value = this.dataset.postal;
                                cityDropdown.classList.add('d-none');
                                loadShippingRates();
                            });
                        });
                    } else {
                        cityDropdown.classList.add('d-none');
                    }
                })
                .catch(() => cityDropdown.classList.add('d-none'));
        }, 200);
    });

    document.addEventListener('click', function(e) {
        if (!cityInput.contains(e.target) && !cityDropdown.contains(e.target)) {
            cityDropdown.classList.add('d-none');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    loadShippingRates();
});
</script>

