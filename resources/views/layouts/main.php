<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title><?= e($pageTitle ?? config('app.name') . ' - ' . config('app.tagline')) ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="<?= e($metaDescription ?? 'Platform donasi pohon, relawan penanaman, dan merchandise untuk lingkungan Indonesia yang lebih hijau.') ?>">
    <meta property="og:title" content="<?= e($pageTitle ?? config('app.name')) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? config('app.tagline')) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= url() ?>">

    <!-- Google Fonts: Bitter (Slab-Serif Poster), Space Mono & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@500;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Header & Navigation -->
    <?php require VIEW_PATH . '/partials/navbar.php'; ?>

    <!-- Main Content Body -->
    <main class="flex-grow-1">
        <!-- Flash Messages Global Container -->
        <div class="container mt-3">
            <?php require VIEW_PATH . '/partials/flash-messages.php'; ?>
        </div>

        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php require VIEW_PATH . '/partials/footer.php'; ?>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <?= $extraJs ?>
    <?php endif; ?>

<?php if (!empty($showPromoModal) && !empty($promoPopupProduct)): ?>
<?php
    /* ── Siapkan variabel produk promo ── */
    $pName  = e($promoPopupProduct['name']);
    $pSlug  = e($promoPopupProduct['slug'] ?? '');
    $pCat   = $promoPopupProduct['category'] ?? 'kaos';
    $pImg   = !empty($promoPopupProduct['primary_image'])
                ? '/' . ltrim($promoPopupProduct['primary_image'], '/')
                : asset('assets/images/kaos-plant-for-fun.jpg');
    $pPrice = !empty($promoPopupProduct['min_price'])
                ? 'Rp ' . number_format((float)$promoPopupProduct['min_price'], 0, ',', '.')
                : 'Rp 125.000';
    $pLink  = '/toko/' . $pSlug;
    $pBadge = $pCat === 'bundle' ? '🎁 Bundle Hemat' : '👕 Official Merch';
    $pTag   = $pCat === 'bundle'
                ? 'Kaos + Donasi 2 Pohon Mangga atas namamu!'
                : '100% Organic Cotton · Water-Based Ink · Ramah Lingkungan';
?>
<!-- ══════════════════════════════════════════════════════
     Popup Promo Merchandise — tampil otomatis di homepage
     Hanya muncul 1× per sesi (localStorage flag)
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="merch-promo-modal" tabindex="-1" aria-labelledby="promoModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px">
        <div class="modal-content border-0 overflow-hidden" style="border-radius:20px; background:#0e2b20;">

            <!-- Lencana pojok kiri atas -->
            <div class="position-absolute top-0 start-0 m-3 z-1">
                <span class="badge fw-semibold px-3 py-2" style="background:#bfd254;color:#0e2b20;border-radius:50px;font-size:.72rem;letter-spacing:.03em;">
                    <?= $pBadge ?>
                </span>
            </div>

            <!-- Tombol close pojok kanan atas -->
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-1"
                    data-bs-dismiss="modal" aria-label="Tutup popup promo"></button>

            <!-- Gambar produk dengan overlay gradien bawah -->
            <div class="position-relative" style="height:260px;background:#0a1f17;overflow:hidden;">
                <img src="<?= $pImg ?>" alt="<?= $pName ?>"
                     style="width:100%;height:100%;object-fit:cover;object-position:center top;opacity:.88;transition:opacity .3s;">
                <!-- Gradien atas ke bawah supaya teks tetap terbaca -->
                <div class="position-absolute bottom-0 start-0 end-0"
                     style="height:120px;background:linear-gradient(to top, #0e2b20 0%, transparent 100%);"></div>
                <!-- Label promo terbatas -->
                <div class="position-absolute bottom-0 start-0 m-3">
                    <span class="badge" style="background:rgba(191,210,84,.15);color:#bfd254;border:1px solid rgba(191,210,84,.4);border-radius:6px;font-size:.68rem;letter-spacing:.05em;padding:4px 10px;">
                        ✦ EDISI TERBATAS
                    </span>
                </div>
            </div>

            <!-- Body teks + CTA -->
            <div class="modal-body px-4 pb-4 pt-3 text-white">
                <h4 id="promoModalLabel" class="font-poster mb-1 lh-sm" style="color:#f0f4e8;font-size:1.25rem;">
                    <?= $pName ?>
                </h4>
                <p class="text-sm mb-3" style="color:rgba(191,210,84,.85);font-size:.82rem;font-family:'Space Mono',monospace;">
                    <?= $pTag ?>
                </p>

                <!-- Harga -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="font-mono fw-bold" style="font-size:1.5rem;color:#bfd254;font-family:'Space Mono',monospace;">
                        <?= $pPrice ?>
                    </span>
                    <?php if ($pCat === 'bundle'): ?>
                    <span class="badge" style="background:rgba(245,158,11,.18);color:#f59e0b;border:1px solid rgba(245,158,11,.4);border-radius:6px;font-size:.68rem;padding:4px 8px;">
                        HEMAT 20%
                    </span>
                    <?php endif; ?>
                </div>

                <!-- CTA Buttons -->
                <div class="d-grid gap-2">
                    <a href="<?= $pLink ?>" class="btn fw-semibold py-3 btn-promo-primary" style="background:#bfd254;color:#0e2b20;border-radius:12px;font-size:.95rem;letter-spacing:.01em;border:none;" onclick="localStorage.setItem('merch_promo_seen','1')">
                        <i class="bi bi-bag-heart-fill me-2"></i>Beli Sekarang →
                    </a>
                    <button type="button" class="btn btn-link text-center py-2" style="color:rgba(255,255,255,.45);font-size:.8rem;text-decoration:none;" data-bs-dismiss="modal">
                        Nanti saja, terima kasih
                    </button>
                </div>
            </div>

            <!-- Footer strip branding -->
            <div class="px-4 py-2 d-flex align-items-center gap-2 border-top" style="border-color:rgba(255,255,255,.08)!important;background:rgba(0,0,0,.2);">
                <i class="bi bi-leaf-fill" style="color:#bfd254;font-size:.85rem;"></i>
                <span style="color:rgba(255,255,255,.4);font-size:.72rem;font-family:'Space Mono',monospace;">
                    Plant For Fun · Kerenceng Coffee Expedition
                </span>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    var STORAGE_KEY = 'merch_promo_seen_v2';
    var DELAY_MS    = 2800; // tampil 2.8 detik setelah halaman siap

    document.addEventListener('DOMContentLoaded', function () {
        // Jangan tampilkan lagi jika sudah pernah ditutup di sesi ini
        if (sessionStorage.getItem(STORAGE_KEY)) return;

        var modalEl = document.getElementById('merch-promo-modal');
        if (!modalEl) return;

        // Simpan flag saat modal ditutup (klik close / nanti saja / klik luar)
        modalEl.addEventListener('hidden.bs.modal', function () {
            sessionStorage.setItem(STORAGE_KEY, '1');
        });

        // Tampilkan setelah delay supaya halaman sudah sepenuhnya ter-render
        setTimeout(function () {
            try {
                var modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
                modal.show();
            } catch (err) {
                console.warn('[PromoModal] Gagal menampilkan:', err);
            }
        }, DELAY_MS);
    });
})();
</script>
<?php endif; ?>
</body>
</html>

