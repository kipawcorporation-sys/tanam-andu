<?php
/**
 * View: Halaman Donasi Pohon Nusantara
 * Tema: Botanical Outdoor & Agroforestry Expedition (Plant For Fun)
 */
?>
<div class="topo-pattern-light py-5 border-bottom" style="border-bottom: 2px solid rgba(18,56,44,0.1) !important;">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(18,56,44,0.08); border: 1px solid rgba(18,56,44,0.15);">
                    <span class="fs-6">🌱</span>
                    <span class="font-poster text-uppercase small" style="color: #12382c; letter-spacing: 2px; font-size: 11px;">
                        Ekspedisi Penghijauan &bull; Plant For Fun
                    </span>
                </div>
                <h1 class="display-5 fw-bold font-poster text-uppercase mb-2" style="color: #12382c; letter-spacing: 0.5px;">
                    Donasi Pohon Nusantara
                </h1>
                <p class="lead text-muted mb-0" style="font-size: 1.1rem;">
                    Setiap pohon yang Anda tanam dirawat bersama masyarakat lokal dan petani agroforestri, serta dapat dilacak titik koordinat GPS-nya di peta sebaran.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <form action="<?= url('/donasi/checkout') ?>" method="POST" id="donationForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Kolom Kiri: Pilihan Pohon & Data Donatur -->
            <div class="col-lg-8">

                <!-- 1. Pilihan Jenis Pohon -->
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center me-2 font-mono text-white" style="background: #12382c; width: 32px; height: 32px; font-size: 14px;">1</span>
                        <h5 class="fw-bold font-poster mb-0" style="color: #12382c;">Pilih Jenis Pohon Konservasi</h5>
                    </div>
                    
                    <div class="row g-3">
                        <?php foreach ($species as $sp): ?>
                            <div class="col-md-6">
                                <label class="card h-100 p-3 border cursor-pointer species-card rounded-3 transition <?= (int)$sp['id'] === $selectedSpeciesId ? 'border-success bg-success-subtle' : '' ?>" style="cursor: pointer; border-color: rgba(18,56,44,0.15) !important;">
                                    <div class="d-flex align-items-start gap-3">
                                        <input type="radio" name="tree_species_id" value="<?= $sp['id'] ?>" class="form-check-input mt-1 species-radio flex-shrink-0" <?= (int)$sp['id'] === $selectedSpeciesId ? 'checked' : '' ?> data-price="<?= $sp['price_per_tree'] ?>" data-name="<?= e($sp['name']) ?>" data-co2="<?= $sp['co2_absorption_kg_per_year'] ?>" required>
                                        <?php $spImg = !empty($sp['image']) ? asset($sp['image']) : asset('assets/images/tree-kopi.jpg'); ?>
                                        <img src="<?= $spImg ?>" alt="<?= e($sp['name']) ?>" class="rounded-3 shadow-sm flex-shrink-0" style="width: 58px; height: 58px; object-fit: cover;">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                                <h6 class="fw-bold font-poster mb-0" style="color: #12382c;"><?= e($sp['name']) ?></h6>
                                                <span class="fw-bold font-mono" style="color: #12382c;"><?= format_rupiah($sp['price_per_tree']) ?></span>
                                            </div>
                                            <p class="text-muted small mt-1 mb-2 line-clamp-2"><?= e($sp['description']) ?></p>
                                            <div class="small text-secondary font-mono">
                                                <i class="bi bi-clouds me-1" style="color: #638537;"></i> Serap CO₂: <strong><?= number_format($sp['co2_absorption_kg_per_year'], 1) ?> kg/thn</strong>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Pilihan Paket Jumlah Pohon -->
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center me-2 font-mono text-white" style="background: #12382c; width: 32px; height: 32px; font-size: 14px;">2</span>
                        <h5 class="fw-bold font-poster mb-0" style="color: #12382c;">Tentukan Jumlah Pohon</h5>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6 col-sm-3">
                            <button type="button" class="btn btn-outline-success w-100 py-2 fw-semibold package-btn rounded-pill font-mono" data-qty="1">1 Pohon</button>
                        </div>
                        <div class="col-6 col-sm-3">
                            <button type="button" class="btn btn-outline-success w-100 py-2 fw-semibold package-btn active rounded-pill font-mono" data-qty="5">5 Pohon</button>
                        </div>
                        <div class="col-6 col-sm-3">
                            <button type="button" class="btn btn-outline-success w-100 py-2 fw-semibold package-btn rounded-pill font-mono" data-qty="10">10 Pohon</button>
                        </div>
                        <div class="col-6 col-sm-3">
                            <button type="button" class="btn btn-outline-success w-100 py-2 fw-semibold package-btn rounded-pill font-mono" data-qty="custom">Lainnya</button>
                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted fw-semibold">Jumlah Pohon</span>
                        <input type="number" name="quantity" id="quantityInput" class="form-control form-control-lg fw-bold text-center font-mono" value="5" min="1" max="1000" required>
                        <span class="input-group-text bg-light text-muted">Batang Pohon</span>
                    </div>
                </div>

                <!-- 3. Informasi Donatur -->
                <div class="card border-0 shadow-sm p-4 mb-4 rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center me-2 font-mono text-white" style="background: #12382c; width: 32px; height: 32px; font-size: 14px;">3</span>
                        <h5 class="fw-bold font-poster mb-0" style="color: #12382c;">Data Donatur</h5>
                    </div>

                    <?php if (!$currentUser): ?>
                        <div class="alert py-2 small d-flex align-items-center mb-3 rounded-3" style="background: #e6eee7; border: 1px solid #d6e4d7; color: #12382c;">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                Anda berdonasi sebagai <strong>Tamu</strong>. Punya akun? <a href="<?= url('/masuk') ?>" class="fw-bold text-decoration-none" style="color: #12382c;">Masuk di sini</a> agar pohon tercatat di dashboard Anda.
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="donor_name" class="form-label fw-semibold small text-secondary">Nama Lengkap Donatur</label>
                            <input type="text" class="form-control" id="donor_name" name="donor_name" value="<?= e($currentUser['nama'] ?? old('donor_name')) ?>" placeholder="Nama lengkap Anda" required>
                        </div>
                        <div class="col-md-6">
                            <label for="donor_email" class="form-label fw-semibold small text-secondary">Alamat Email (Untuk Invoice & Sertifikat)</label>
                            <input type="email" class="form-control" id="donor_email" name="donor_email" value="<?= e($currentUser['email'] ?? old('donor_email')) ?>" placeholder="nama@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label for="donor_phone" class="form-label fw-semibold small text-secondary">Nomor WhatsApp</label>
                            <input type="tel" class="form-control" id="donor_phone" name="donor_phone" value="<?= e(old('donor_phone')) ?>" placeholder="081234567890" required>
                        </div>
                        <div class="col-md-6">
                            <label for="donor_message" class="form-label fw-semibold small text-secondary">Pesan / Doa Kebaikan (Opsional)</label>
                            <input type="text" class="form-control" id="donor_message" name="donor_message" value="<?= e(old('donor_message')) ?>" placeholder="Tuliskan harapan Anda untuk bumi...">
                        </div>
                    </div>

                    <!-- Opsi Donasi Anonim -->
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1" <?= old('is_anonymous') ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-secondary" for="is_anonymous">
                            Sembunyikan nama saya dari daftar donatur publik (Donasi Anonim / Hamba Allah)
                        </label>
                    </div>

                    <!-- Opsi Hadiah / In Memoriam -->
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_gift" name="is_gift" value="1" <?= old('is_gift') ? 'checked' : '' ?> onchange="toggleGiftFields(this)">
                        <label class="form-check-label small fw-semibold text-secondary" for="is_gift">
                            Donasi ini sebagai Hadiah / Atas Nama Orang Lain (In Memoriam / Kado)
                        </label>
                    </div>

                    <div id="giftFields" class="row g-3 mt-1 <?= old('is_gift') ? '' : 'd-none' ?>">
                        <div class="col-md-6">
                            <label for="gift_recipient_name" class="form-label fw-semibold small text-secondary">Nama Penerima / Orang yang Didedikasikan</label>
                            <input type="text" class="form-control" id="gift_recipient_name" name="gift_recipient_name" value="<?= e(old('gift_recipient_name')) ?>" placeholder="Contoh: Ibu Tercinta / Sahabat">
                        </div>
                        <div class="col-md-6">
                            <label for="gift_message" class="form-label fw-semibold small text-secondary">Pesan Hadiah</label>
                            <input type="text" class="form-control" id="gift_message" name="gift_message" value="<?= e(old('gift_message')) ?>" placeholder="Semoga pohon ini tumbuh subur membawa berkah...">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Ringkasan & Tombol Checkout -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 sticky-top rounded-4" style="top: 90px; border: 1px solid rgba(18,56,44,0.1) !important;">
                    <h5 class="fw-bold font-poster border-bottom pb-3 mb-3" style="color: #12382c;">Ringkasan Donasi</h5>

                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Jenis Pohon:</span>
                        <strong id="summarySpeciesName" class="font-poster" style="color: #12382c;">-</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Harga per Pohon:</span>
                        <span id="summarySpeciesPrice" class="font-mono">Rp 0</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Jumlah Pohon:</span>
                        <strong id="summaryQuantity" class="font-mono">0 Batang</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small text-secondary">
                        <span>Estimasi Serap CO₂:</span>
                        <strong id="summaryCo2" class="font-mono fw-bold" style="color: #1b5341;">0 kg/thn</strong>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold font-poster" style="color: #12382c;">Total Donasi:</span>
                        <h4 id="summaryTotalAmount" class="fw-bold font-mono mb-0" style="color: #12382c;">Rp 0</h4>
                    </div>

                    <div class="small p-3 rounded-3 mb-3" style="background: #e6eee7; color: #12382c; border: 1px solid #d6e4d7;">
                        <i class="bi bi-shield-check me-1"></i> Pembayaran manual via Transfer Bank / E-Wallet / QRIS resmi dengan verifikasi admin.
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold py-3 shadow-sm rounded-pill font-poster">
                        Lanjut ke Pembayaran →
                    </button>

                    <div class="mt-4 pt-3 border-top text-center">
                        <span class="small text-muted font-mono" style="font-size: 11px;">
                            PLANT TREES PRESERVE TOMORROW
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const speciesRadios = document.querySelectorAll('.species-radio');
    const quantityInput = document.getElementById('quantityInput');
    const packageBtns = document.querySelectorAll('.package-btn');
    
    const summarySpeciesName = document.getElementById('summarySpeciesName');
    const summarySpeciesPrice = document.getElementById('summarySpeciesPrice');
    const summaryQuantity = document.getElementById('summaryQuantity');
    const summaryCo2 = document.getElementById('summaryCo2');
    const summaryTotalAmount = document.getElementById('summaryTotalAmount');

    function formatRupiah(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID');
    }

    function updateSummary() {
        let selectedRadio = document.querySelector('.species-radio:checked');
        if (!selectedRadio && speciesRadios.length > 0) {
            selectedRadio = speciesRadios[0];
            selectedRadio.checked = true;
        }

        const price = Number(selectedRadio.dataset.price || 0);
        const name = selectedRadio.dataset.name || '-';
        const co2Rate = Number(selectedRadio.dataset.co2 || 0);
        const qty = Math.max(1, Number(quantityInput.value) || 1);

        const total = price * qty;
        const totalCo2 = (co2Rate * qty).toFixed(1);

        summarySpeciesName.textContent = name;
        summarySpeciesPrice.textContent = formatRupiah(price);
        summaryQuantity.textContent = qty + ' Batang';
        summaryCo2.textContent = totalCo2 + ' kg/thn';
        summaryTotalAmount.textContent = formatRupiah(total);

        // Update active class on species cards
        document.querySelectorAll('.species-card').forEach(card => {
            card.classList.remove('border-success', 'bg-success-subtle');
            card.style.borderColor = 'rgba(18,56,44,0.15)';
            card.style.backgroundColor = '#ffffff';
        });
        const activeCard = selectedRadio.closest('.species-card');
        if (activeCard) {
            activeCard.classList.add('border-success', 'bg-success-subtle');
            activeCard.style.borderColor = '#12382c';
            activeCard.style.backgroundColor = '#e6eee7';
        }
    }

    speciesRadios.forEach(radio => {
        radio.addEventListener('change', updateSummary);
    });

    quantityInput.addEventListener('input', function() {
        packageBtns.forEach(btn => btn.classList.remove('active'));
        updateSummary();
    });

    packageBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            packageBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const qty = this.dataset.qty;
            if (qty !== 'custom') {
                quantityInput.value = qty;
            } else {
                quantityInput.focus();
            }
            updateSummary();
        });
    });

    updateSummary();
});

function toggleGiftFields(checkbox) {
    const fields = document.getElementById('giftFields');
    if (checkbox.checked) {
        fields.classList.remove('d-none');
    } else {
        fields.classList.add('d-none');
    }
}
</script>
