<?php
/**
 * View: Toko Kopi Spesialti & Merchandise Konservasi
 * Tema: Botanical Outdoor & Agroforestry Expedition (Plant For Fun)
 */
?>
<div class="topo-pattern-light py-5 border-bottom mb-4" style="border-bottom: 2px solid rgba(18,56,44,0.1) !important;">
    <div class="container">
        <!-- Header Section -->
        <div class="row justify-content-between align-items-center">
            <div class="col-md-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(18,56,44,0.08); border: 1px solid rgba(18,56,44,0.15);">
                    <img src="<?= asset('assets/images/logo-andu.png') ?>" alt="andu" style="height: 18px; width: auto;">
                    <span class="font-poster text-uppercase small" style="color: #12382c; letter-spacing: 1.5px; font-size: 11px;">
                        Curated Coffee &bull; by andu Roastery
                    </span>
                </div>
                <h1 class="display-5 fw-bold font-poster text-uppercase mb-2" style="color: #12382c;">
                    Toko Kopi Spesialti & Merchandise
                </h1>
                <p class="lead text-muted mb-0" style="font-size: 1.1rem;">
                    Biji kopi Arabika asli lereng Gunung Kerenceng hasil kolaborasi eksklusif <strong>andu</strong> bersama petani agroforestri, serta merchandise resmi untuk mendukung pendanaan pohon.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?= url('/keranjang') ?>" class="btn btn-outline-success fw-semibold rounded-pill px-4 py-2 position-relative shadow-sm font-poster">
                    <i class="bi bi-bag-fill me-2"></i> Keranjang Belanja
                    <?php if (!empty($cartItemCount)): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger font-mono">
                            <?= $cartItemCount ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    <!-- Filter Kategori Tabs -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="<?= url('/toko') ?>" class="btn rounded-pill px-4 font-poster <?= empty($selectedCategory) ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
            Semua Produk
        </a>
        <a href="<?= url('/toko?kategori=kopi') ?>" class="btn rounded-pill px-4 font-poster <?= $selectedCategory === 'kopi' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
            ☕ Kopi Spesialti Kerenceng x andu
        </a>
        <a href="<?= url('/toko?kategori=kaos') ?>" class="btn rounded-pill px-4 font-poster <?= $selectedCategory === 'kaos' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
            👕 Kaos Katun Organik
        </a>
        <a href="<?= url('/toko?kategori=bundle') ?>" class="btn rounded-pill px-4 font-poster <?= $selectedCategory === 'bundle' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
            🎁 Paket Bundle (Kaos + Donasi)
        </a>
    </div>

    <!-- Grid Produk Merchandise & Kopi -->
    <?php if (!empty($products)): ?>
        <div class="row g-4">
            <?php foreach ($products as $prod): ?>
                <?php $isCoffee = ($prod['category'] === 'kopi'); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden card-hover rounded-4" style="border: 1px solid rgba(18,56,44,0.12) !important;">
                        
                        <!-- Header Visual Produk -->
                        <?php if ($isCoffee): ?>
                            <?php
                                $primaryImg = !empty($prod['images']) ? $prod['images'][0]['image_path'] : null;
                                if (!$primaryImg) {
                                    $slugImgs = [
                                        'arabica-kerenceng-honey-an-du' => 'assets/images/coffee-honey.jpg',
                                        'kerenceng-natural-anaerobic-an-du' => 'assets/images/coffee-anaerobic.jpg',
                                        'drip-bag-kerenceng-an-du-trail' => 'assets/images/coffee-drip-bag.jpg',
                                        'cold-brew-concentrate-an-du' => 'assets/images/coffee-cold-brew.jpg',
                                    ];
                                    $primaryImg = $slugImgs[$prod['slug']] ?? 'assets/images/coffee-honey.jpg';
                                }
                            ?>
                            <div class="position-relative w-100" style="height: 230px; overflow: hidden;">
                                <img src="<?= asset($primaryImg) ?>" alt="<?= e($prod['name']) ?>" class="w-100 h-100" style="object-fit: cover;">
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge-andu shadow-sm">
                                        <img src="<?= asset('assets/images/logo-andu-white.png') ?>" alt="andu" style="height: 12px; width: auto;">
                                        ROASTED BY andu
                                    </span>
                                </div>
                                <div class="position-absolute bottom-0 end-0 m-2">
                                    <span class="badge bg-dark bg-opacity-75 text-white font-mono" style="font-size: 10px;">
                                        Kerenceng 1.550m
                                    </span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="position-relative text-center py-5" style="background: #f8faf8; min-height: 230px; display: flex; align-items: center; justify-content: center;">
                                <span class="display-1">👕</span>
                                <?php if ($prod['category'] === 'bundle'): ?>
                                    <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark fw-bold rounded-pill px-3 py-2 font-poster">
                                        ⭐ Bundle Hemat
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4 d-flex flex-column bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge <?= $isCoffee ? 'bg-success-subtle' : 'bg-secondary-subtle text-dark' ?> text-uppercase small px-2 py-1 font-mono" style="color:#0a3622;">
                                    <?= $isCoffee ? 'Kopi Kerenceng x andu' : e($prod['category']) ?>
                                </span>
                                <?php if ($isCoffee): ?>
                                    <span class="badge-altitude">Kerenceng 1.742m</span>
                                <?php endif; ?>
                            </div>

                            <h5 class="fw-bold font-poster mb-1">
                                <a href="<?= url('/toko/' . $prod['slug']) ?>" class="text-dark text-decoration-none">
                                    <?= e($prod['name']) ?>
                                </a>
                            </h5>
                            
                            <p class="text-muted small flex-grow-1 line-clamp-2 mt-2">
                                <?= e($prod['description']) ?>
                            </p>

                            <!-- Pilihan Varian Tersedia -->
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-3 pt-2">
                                <span class="small text-muted me-1 font-mono" style="font-size: 11px;"><?= $isCoffee ? 'Pilihan:' : 'Ukuran:' ?></span>
                                <?php 
                                    $variantsSummary = [];
                                    foreach ($prod['variants'] as $v) {
                                        $label = $v['size'] ?: $v['color'];
                                        if ($label && !in_array($label, $variantsSummary)) {
                                            $variantsSummary[] = $label;
                                        }
                                    }
                                    foreach (array_slice($variantsSummary, 0, 3) as $lbl): 
                                ?>
                                    <span class="coffee-flavor-chip"><?= e($lbl) ?></span>
                                <?php endforeach; ?>
                                <?php if (count($variantsSummary) > 3): ?>
                                    <span class="small text-muted font-mono">+<?= count($variantsSummary) - 3 ?> lainnya</span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div>
                                    <span class="text-muted small d-block font-mono" style="font-size: 11px;">Mulai dari</span>
                                    <h5 class="fw-bold font-mono mb-0" style="color: #12382c;"><?= format_rupiah($prod['base_price']) ?></h5>
                                </div>
                                <a href="<?= url('/toko/' . $prod['slug']) ?>" class="btn btn-success fw-bold rounded-pill px-4 font-poster" style="font-size: 13px;">
                                    <?= $isCoffee ? 'Pesan Kopi →' : 'Lihat Detail →' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <div class="fs-1 text-muted mb-2">🛍️</div>
            <h5 class="fw-bold font-poster">Belum Ada Produk</h5>
            <p class="text-muted small">Produk untuk kategori ini sedang disiapkan.</p>
        </div>
    <?php endif; ?>
</div>
