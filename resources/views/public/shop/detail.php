<?php
/**
 * View: Detail Produk & Kopi Spesialti
 * Tema: Botanical Outdoor & Agroforestry Expedition (Plant For Fun)
 */
$isCoffee = ($product['category'] === 'kopi');
?>
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small font-mono">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-success text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/toko') ?>" class="text-success text-decoration-none">Toko & Kopi</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Kolom Kiri: Galeri Foto & Visual Produk -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 mb-3 position-relative overflow-hidden" 
                 style="background: #ffffff; min-height: 420px; border: 1.5px solid rgba(18,56,44,0.12) !important;">
                
                <?php
                    $primaryImg = !empty($product['images']) ? $product['images'][0]['image_path'] : null;
                    if (!$primaryImg) {
                        $slugImgs = [
                            'arabica-kerenceng-honey-an-du' => 'assets/images/coffee-honey.jpg',
                            'kerenceng-natural-anaerobic-an-du' => 'assets/images/coffee-anaerobic.jpg',
                            'drip-bag-kerenceng-an-du-trail' => 'assets/images/coffee-drip-bag.jpg',
                            'cold-brew-concentrate-an-du' => 'assets/images/coffee-cold-brew.jpg',
                            'kaos-organik-penjaga-bumi' => 'assets/images/kaos-plant-for-fun.jpg',
                            'bundle-kaos-donasi-2-pohon' => 'assets/images/kaos-plant-for-fun.jpg',
                        ];
                        $primaryImg = $slugImgs[$product['slug']] ?? null;
                    }
                    if (!$primaryImg) {
                        if ($isCoffee) {
                            $primaryImg = 'assets/images/coffee-honey.jpg';
                        } elseif ($product['category'] === 'kaos' || $product['category'] === 'bundle' || $product['category'] === 'merchandise') {
                            $primaryImg = 'assets/images/kaos-plant-for-fun.jpg';
                        }
                    }
                ?>

                <?php if ($primaryImg): ?>
                    <div class="position-relative w-100" style="height: 440px; background: #f8faf8;">
                        <img src="<?= asset($primaryImg) ?>" id="mainProductImg" alt="<?= e($product['name']) ?>" class="w-100 h-100" style="object-fit: cover;">
                        
                        <?php if ($isCoffee): ?>
                            <!-- andu Co-branding Banner Overlay -->
                            <div class="position-absolute top-0 start-0 m-3 p-2 px-3 rounded-3 bg-white shadow-sm border d-flex align-items-center gap-2">
                                <img src="<?= asset('assets/images/logo-andu.png') ?>" alt="andu" style="height: 20px; width: auto;">
                                <span class="fw-bold font-mono text-dark" style="font-size: 11px;">ROASTED BY andu</span>
                            </div>

                            <div class="position-absolute bottom-0 start-0 m-3 d-flex gap-2 flex-wrap">
                                <span class="badge-altitude shadow">Elevasi: 1.550 mdpl</span>
                                <span class="badge bg-success font-mono rounded-pill px-3 py-1 text-white shadow">Arabica Specialty</span>
                            </div>
                        <?php else: ?>
                            <div class="position-absolute top-0 start-0 m-3 d-flex gap-2 flex-wrap">
                                <span class="badge bg-dark bg-opacity-75 font-mono rounded-pill px-3 py-2 text-white shadow-sm">
                                    Official Merchandise
                                </span>
                                <?php if ($product['category'] === 'bundle'): ?>
                                    <span class="badge bg-warning text-dark font-poster rounded-pill px-3 py-2 shadow-sm">
                                        ⭐ Bundle Hemat
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="display-1 text-success text-center py-5" id="mainProductVisual">👕</div>
                <?php endif; ?>
            </div>
            
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                    <?php foreach ($product['images'] as $idx => $img): ?>
                        <img src="<?= asset($img['image_path']) ?>" 
                             alt="Thumbnail <?= $idx + 1 ?>" 
                             class="rounded-3 border <?= $idx === 0 ? 'border-2 border-success' : 'border' ?> shadow-sm product-thumb" 
                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                             onclick="switchMainProductImg(this.src, this)">
                    <?php endforeach; ?>
                <?php elseif ($isCoffee): ?>
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= asset('assets/images/coffee-honey.jpg') ?>" alt="Beans" class="rounded-3 border border-2 border-success shadow-sm product-thumb" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" onclick="switchMainProductImg(this.src, this)">
                        <img src="<?= asset('assets/images/coffee-anaerobic.jpg') ?>" alt="Cherries" class="rounded-3 border shadow-sm product-thumb" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" onclick="switchMainProductImg(this.src, this)">
                        <img src="<?= asset('assets/images/coffee-plantation.jpg') ?>" alt="Kebon Kopi Kerenceng" class="rounded-3 border shadow-sm product-thumb" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" onclick="switchMainProductImg(this.src, this)">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Pilihan Varian & Tambah ke Keranjang -->
        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge <?= $isCoffee ? 'bg-success text-white' : 'bg-success-subtle' ?> text-uppercase fw-bold px-3 py-2 rounded-pill font-mono" style="color:#0a3622;">
                    <?= $isCoffee ? 'Kopi Spesialti Kerenceng x andu' : e($product['category']) ?>
                </span>
                <?php if ($isCoffee): ?>
                    <span class="badge bg-dark text-white rounded-pill px-3 py-2 font-mono" style="font-size: 11px;">
                        Gn. Kerenceng 1.742m
                    </span>
                <?php endif; ?>
            </div>

            <h2 class="fw-bold font-poster mb-2" style="color: #12382c;"><?= e($product['name']) ?></h2>

            <div class="d-flex align-items-baseline gap-3 mb-3">
                <h3 class="fw-bold font-mono mb-0" id="displayVariantPrice" style="color: #12382c;">
                    <?= format_rupiah($product['base_price']) ?>
                </h3>
                <span class="badge bg-light text-muted border font-mono">Berat: <?= $product['weight_gram'] ?> gram</span>
            </div>

            <p class="text-secondary lh-base mb-4" style="font-size: 0.95rem;">
                <?= nl2br(e($product['description'])) ?>
            </p>

            <form action="<?= url('/keranjang/tambah') ?>" method="POST" id="addToCartForm">
                <?= csrf_field() ?>

                <!-- Pilihan Varian Ukuran / Gilingan & Kemasan -->
                <div class="mb-4">
                    <label class="form-label fw-bold font-poster mb-2" style="color: #12382c;">
                        <?= $isCoffee ? 'Pilih Bentuk / Gilingan & Kemasan:' : 'Pilih Varian (Ukuran & Warna):' ?>
                    </label>
                    <div class="d-flex flex-wrap gap-2" id="variantSelectorGroup">
                        <?php foreach ($product['variants'] as $i => $v): ?>
                            <?php 
                                $available = max(0, $v['stock'] - $v['reserved_stock']);
                                $isOutOfStock = ($available <= 0);
                                $variantLabel = ($v['size'] ? e($v['size']) . ' &bull; ' : '') . e($v['color']);
                            ?>
                            <label class="btn btn-outline-secondary p-2 px-3 text-start variant-option-btn <?= $i === 0 && !$isOutOfStock ? 'active btn-success text-white' : '' ?> <?= $isOutOfStock ? 'disabled opacity-50' : '' ?>" style="border-radius: 12px; cursor: pointer;">
                                <input type="radio" name="variant_id" value="<?= $v['id'] ?>" class="d-none variant-radio" <?= $i === 0 && !$isOutOfStock ? 'checked' : '' ?> data-price="<?= $v['price'] ?>" data-stock="<?= $available ?>" data-sku="<?= e($v['sku']) ?>" <?= $isOutOfStock ? 'disabled' : '' ?>>
                                <div class="fw-bold font-poster" style="font-size: 13px;"><?= $variantLabel ?></div>
                                <small class="d-block font-mono" style="font-size: 11px;">
                                    <?= $isOutOfStock ? 'Stok Habis' : 'Tersedia: ' . $available ?> &bull; <?= format_rupiah($v['price']) ?>
                                </small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Jumlah Pembelian -->
                <div class="mb-4">
                    <label class="form-label fw-bold font-poster mb-2" style="color: #12382c;">Jumlah Pesanan:</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                            <input type="number" class="form-control text-center fw-bold font-mono" id="quantityInput" name="quantity" value="1" min="1" max="50">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                        </div>
                        <span class="small text-muted font-mono" id="stockAlertText">
                            Pilih varian untuk melihat ketersediaan stok
                        </span>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-3 flex-wrap">
                    <button type="submit" class="btn btn-success btn-lg fw-bold px-4 py-3 rounded-pill shadow-sm flex-grow-1 font-poster" id="addToCartBtn">
                        <i class="bi bi-cart-plus me-2"></i> <?= $isCoffee ? 'Beli Kopi Sekarang' : 'Tambah ke Keranjang' ?>
                    </button>
                    <a href="<?= url('/keranjang') ?>" class="btn btn-outline-success btn-lg fw-bold px-4 py-3 rounded-pill font-poster">
                        Lihat Keranjang
                    </a>
                </div>
            </form>

            <!-- Benefit Card -->
            <div class="card border-0 rounded-4 p-3 mt-4" style="background: #e6eee7; border: 1px solid #d6e4d7 !important;">
                <div class="d-flex align-items-center gap-3">
                    <span class="fs-2"><?= $isCoffee ? '☕' : '🌱' ?></span>
                    <div class="small" style="color: #12382c;">
                        <strong>Dampak Konservasi Nyata:</strong> <?= $isCoffee 
                            ? 'Setiap pembelian kopi ini mendukung langsung petani kopi agroforestri lereng Gunung Kerenceng untuk tetap menjaga kelestarian pohon penaung di hutan lindung.' 
                            : 'Minimal 30% dari keuntungan penjualan produk ini langsung dialokasikan ke pengadaan bibit pohon dan program perawatan di lapangan.' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('.variant-radio');
    const optionBtns = document.querySelectorAll('.variant-option-btn');
    const priceDisplay = document.getElementById('displayVariantPrice');
    const stockAlertText = document.getElementById('stockAlertText');
    const qtyInput = document.getElementById('quantityInput');
    const submitBtn = document.getElementById('addToCartBtn');

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function updateVariantSelection() {
        const checked = document.querySelector('.variant-radio:checked');
        if (!checked) return;

        optionBtns.forEach(btn => {
            btn.classList.remove('active', 'btn-success', 'text-white');
            btn.classList.add('btn-outline-secondary');
        });

        const activeLabel = checked.closest('.variant-option-btn');
        if (activeLabel) {
            activeLabel.classList.add('active', 'btn-success', 'text-white');
            activeLabel.classList.remove('btn-outline-secondary');
        }

        const price = checked.dataset.price;
        const stock = parseInt(checked.dataset.stock, 10);

        if (priceDisplay && price) {
            priceDisplay.textContent = formatRupiah(price);
        }

        if (stockAlertText) {
            if (stock <= 0) {
                stockAlertText.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i> Stok habis untuk varian ini</span>';
                if (submitBtn) submitBtn.disabled = true;
            } else if (stock <= 5) {
                stockAlertText.innerHTML = '<span class="text-warning-emphasis fw-bold"><i class="bi bi-exclamation-triangle me-1"></i> Sisa ' + stock + ' item lagi! Segera pesan</span>';
                if (submitBtn) submitBtn.disabled = false;
            } else {
                stockAlertText.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i> Stok siap kirim (' + stock + ' item)</span>';
                if (submitBtn) submitBtn.disabled = false;
            }
        }

        if (qtyInput) {
            qtyInput.max = Math.max(1, stock);
            if (parseInt(qtyInput.value, 10) > stock && stock > 0) {
                qtyInput.value = stock;
            }
        }
    }

    radios.forEach(r => {
        r.addEventListener('change', updateVariantSelection);
    });

    updateVariantSelection();
});

function adjustQty(delta) {
    const input = document.getElementById('quantityInput');
    if (!input) return;
    let val = parseInt(input.value, 10) || 1;
    let max = parseInt(input.max, 10) || 50;
    let min = parseInt(input.min, 10) || 1;
    val += delta;
    if (val < min) val = min;
    if (val > max) val = max;
    input.value = val;
}

function switchMainProductImg(src, el) {
    const mainImg = document.getElementById('mainProductImg');
    if (mainImg) {
        mainImg.src = src;
    }
    document.querySelectorAll('.product-thumb').forEach(t => {
        t.classList.remove('border-2', 'border-success');
        t.classList.add('border');
    });
    if (el) {
        el.classList.add('border-2', 'border-success');
        el.classList.remove('border');
    }
}
</script>
