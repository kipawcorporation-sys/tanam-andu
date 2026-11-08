<?php
/**
 * View: Home Landing Page — TanamPohon.id
 * Tema: Botanical Outdoor Expedition & Specialty Coffee Conservation
 * Kolaborasi Utama: PLANT FOR FUN &bull; Presented by andu, Karsa Kerentjeng, Ethera
 */
?>

<!-- 1. TOP BAR EXPEDITION TICKER -->
<div class="py-2 text-white border-bottom" style="background-color: #0d2820; border-color: rgba(255,255,255,0.08) !important;">
    <div class="container d-flex flex-wrap justify-content-center justify-content-md-between align-items-center text-center text-md-start small font-mono gap-2" style="font-size: 11px;">
        <div class="d-flex align-items-center gap-3">
            <span class="badge rounded-pill px-2 py-1" style="background-color: #bfd254; color: #12382c; font-weight: 700;">
                EXPEDITION 2026
            </span>
            <span><i class="bi bi-geo-alt text-warning me-1"></i> Kebon Kopi Kerenceng (1.742 mdpl)</span>
            <span class="d-none d-md-inline text-white-50">&bull;</span>
            <span class="d-none d-md-inline">GPS: -6.9832, 107.8921</span>
        </div>
        <div class="d-flex align-items-center gap-3 mt-1 mt-md-0">
            <span class="text-white-50">PRESENTED BY:</span>
            <span class="fw-bold text-white letter-spacing-1">andu &bull; KARSA KERENTJENG &bull; ETHERA</span>
        </div>
    </div>
</div>

<!-- 2. HERO SECTION: POSTER FRAME THEME "PLANT FOR FUN" -->
<section class="topo-pattern py-5 position-relative overflow-hidden border-bottom" style="border-bottom: 3px solid #12382c !important;">
    <div class="container py-3 py-lg-4">
        
        <!-- Header Atas Poster -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 shadow-sm" style="background-color: #12382c; color: #bfd254;">
                <i class="bi bi-compass"></i>
                <span class="font-mono text-uppercase" style="font-size: 11px; letter-spacing: 2px;">
                    AGROFORESTRY & SPECIALTY COFFEE MOVEMENT
                </span>
            </div>
            <h1 class="display-3 font-poster text-uppercase mb-1" style="color: #12382c; letter-spacing: 3px; line-height: 1.05;">
                PLANT FOR FUN
            </h1>
            <div class="d-flex justify-content-center align-items-center gap-3 font-poster text-uppercase" style="color: #12382c; letter-spacing: 2px; font-size: 1.15rem;">
                <span class="fw-bold">KERENCENG</span>
                <span style="color: #638537;">&bull;</span>
                <span class="fw-bold">COFFEE PLANTING</span>
                <span style="color: #638537;">&bull;</span>
                <span class="badge-andu">BY andu</span>
            </div>
        </div>

        <!-- Poster Box Frame Utama -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-11 col-xl-10">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="border: 3px solid #12382c !important; background: #ffffff;">
                    
                    <!-- Isi Bingkai Poster -->
                    <div class="position-relative p-3 p-md-4 topo-pattern-light">
                        <div class="row g-4 align-items-center">
                            
                            <!-- Kolom Foto Poster Asli -->
                            <div class="col-md-5 text-center">
                                <div class="p-2 bg-white rounded-3 shadow d-inline-block position-relative" style="border: 2px solid #12382c;">
                                    <img src="<?= asset('assets/images/plant-for-fun-kerenceng.jpg') ?>" alt="Plant For Fun Poster" class="img-fluid rounded-2 shadow-sm hero-poster-img" style="max-height: 440px; width: auto; object-fit: contain;">
                                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 badge rounded-pill px-3 py-1 font-mono shadow-sm" style="background-color: #12382c; color: #bfd254; font-size: 10px; letter-spacing: 1px;">
                                        OFFICIAL POSTER
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Rincian Aksi & Penawaran -->
                            <div class="col-md-7 text-start ps-md-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill font-mono px-3 py-1" style="background: #e6eee7; color: #12382c; border: 1px solid #d6e4d7;">
                                        EXPEDITION & CONSERVATION 2026
                                    </span>
                                    <span class="badge-altitude">1.742 MDPL</span>
                                </div>

                                <h2 class="display-6 font-poster text-uppercase mb-3" style="color: #12382c; letter-spacing: 1px; line-height: 1.15;">
                                    PLANT TREES PRESERVE TOMORROW
                                </h2>

                                <p class="text-secondary small mb-3 lh-base">
                                    Gerakan penanaman pohon kopi dan pohon pelindung di lereng Gunung Kerenceng. Menjaga tangkapan air alami, mencegah erosi tanah pegunungan, sekaligus menghasilkan biji kopi Arabika berkualitas tinggi yang dikurasi langsung oleh <strong>andu</strong> bersama petani lokal Cicalengka.
                                </p>

                                <!-- Grid Metadata Poster (Date, Time, Location) -->
                                <div class="p-3 rounded-3 mb-4" style="background-color: #f4f8f4; border-left: 4px solid #12382c; border: 1px solid rgba(18,56,44,0.15);">
                                    <div class="row g-2 text-center text-sm-start font-poster small" style="color: #12382c;">
                                        <div class="col-sm-4 border-end border-success-subtle pe-2">
                                            <div class="text-muted font-mono" style="font-size: 9px; letter-spacing: 1px;">DATE</div>
                                            <div class="fw-bold" style="font-size: 12px;">SUNDAY, 29 NOV 2026</div>
                                        </div>
                                        <div class="col-sm-4 border-end border-success-subtle px-2">
                                            <div class="text-muted font-mono" style="font-size: 9px; letter-spacing: 1px;">TIME</div>
                                            <div class="fw-bold" style="font-size: 12px;">06.00 - 11.00 WIB</div>
                                        </div>
                                        <div class="col-sm-4 ps-2">
                                            <div class="text-muted font-mono" style="font-size: 9px; letter-spacing: 1px;">LOCATION</div>
                                            <div class="fw-bold text-truncate" style="font-size: 12px;">KEBON KOPI KERENCENG</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CTA Multi-Offering Buttons -->
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 btn-cta-group">
                                    <a href="<?= url('/kegiatan/3') ?>" class="btn btn-success btn-lg rounded-pill px-4 py-3 fw-bold font-poster text-uppercase shadow-sm" style="letter-spacing: 1px;">
                                        LET'S GET PLANTING!! <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                    <a href="<?= url('/toko?kategori=kopi') ?>" class="btn btn-trail btn-lg rounded-pill px-4 py-3 fw-bold font-poster text-uppercase shadow-sm">
                                        ☕ Beli Kopi andu
                                    </a>
                                    <a href="<?= url('/donasi') ?>" class="btn btn-outline-dark btn-lg rounded-pill px-3 py-3 fw-bold font-poster text-uppercase">
                                        🌱 Donasi Pohon
                                    </a>
                                </div>

                                <div class="d-flex align-items-center gap-2 text-muted small font-mono" style="font-size: 11px;">
                                    <i class="bi bi-qr-code text-success fs-5"></i>
                                    <span>SCAN FOR REGIST &bull; Kuota 100 Relawan &bull; Include Bibit & Kopi Seduh</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kotak Sponsor & Media Partner Resmi -->
                    <div class="p-3 p-md-4 border-top" style="border-top: 2px solid #12382c !important; background: #ffffff;">
                        <?php 
                            $logoTheme = 'dark'; 
                            require VIEW_PATH . '/partials/partner-logos.php'; 
                        ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>

<!-- 3. REAL IMPACT TICKER / STATS -->
<section class="py-5 border-bottom" style="background: linear-gradient(135deg, #f0f7f4 0%, #ffffff 50%, #f0f7f4 100%);">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge px-3 py-1 rounded-pill font-mono fw-bold mb-2 d-inline-block" style="background:#dcfce7;color:#0a3622;font-size:11px;letter-spacing:.08em;">DAMPAK NYATA DI LAPANGAN</span>
            <h2 class="font-poster fw-bold text-uppercase mb-0" style="color:#12382c;font-size:clamp(1.1rem,2.5vw,1.5rem);">Setiap Rupiah Tercatat, Setiap Pohon Dipantau</h2>
        </div>
        <div class="row g-3 g-md-4 text-center align-items-stretch">
            <!-- Pohon Tertanam -->
            <div class="col-6 col-md-3">
                <div class="h-100 rounded-4 p-3 p-md-4 position-relative overflow-hidden" style="background:#fff;border:1.5px solid rgba(18,56,44,0.10);box-shadow:0 2px 16px rgba(18,56,44,0.07);">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:44px;height:44px;background:rgba(18,56,44,0.08);color:#12382c;font-size:20px;">
                            <i class="bi bi-tree-fill"></i>
                        </span>
                    </div>
                    <div class="display-6 font-poster fw-bold mb-1 lh-1" style="color:#12382c;">
                        <?= number_format(max(2850, $impactStats['total_trees'])) ?>+
                    </div>
                    <div class="fw-bold font-mono text-uppercase" style="color:#3d6b58;font-size:10.5px;letter-spacing:.07em;">Pohon Tertanam</div>
                    <div class="mt-2" style="height:3px;background:linear-gradient(90deg,#12382c,#4ade80);border-radius:4px;"></div>
                </div>
            </div>
            <!-- Ketinggian -->
            <div class="col-6 col-md-3">
                <div class="h-100 rounded-4 p-3 p-md-4 position-relative overflow-hidden" style="background:#fff;border:1.5px solid rgba(18,56,44,0.10);box-shadow:0 2px 16px rgba(18,56,44,0.07);">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:44px;height:44px;background:rgba(96,165,250,0.12);color:#1d4ed8;font-size:20px;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>
                    </div>
                    <div class="display-6 font-poster fw-bold mb-1 lh-1" style="color:#1e3a5f;">
                        1.742 <span class="fs-6 font-mono" style="color:#64748b;font-weight:400;">mdpl</span>
                    </div>
                    <div class="fw-bold font-mono text-uppercase" style="color:#3b5998;font-size:10.5px;letter-spacing:.07em;">Puncak Gn. Kerenceng</div>
                    <div class="mt-2" style="height:3px;background:linear-gradient(90deg,#1d4ed8,#60a5fa);border-radius:4px;"></div>
                </div>
            </div>
            <!-- Petani Lokal -->
            <div class="col-6 col-md-3">
                <div class="h-100 rounded-4 p-3 p-md-4 position-relative overflow-hidden" style="background:#fff;border:1.5px solid rgba(18,56,44,0.10);box-shadow:0 2px 16px rgba(18,56,44,0.07);">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:44px;height:44px;background:rgba(245,158,11,0.12);color:#b45309;font-size:20px;">
                            <i class="bi bi-people-fill"></i>
                        </span>
                    </div>
                    <div class="display-6 font-poster fw-bold mb-1 lh-1" style="color:#b45309;">
                        100%
                    </div>
                    <div class="fw-bold font-mono text-uppercase" style="color:#92400e;font-size:10.5px;letter-spacing:.07em;">Petani Lokal Berdaya</div>
                    <div class="mt-2" style="height:3px;background:linear-gradient(90deg,#f59e0b,#fde68a);border-radius:4px;"></div>
                </div>
            </div>
            <!-- CO₂ Diserap -->
            <div class="col-6 col-md-3">
                <div class="h-100 rounded-4 p-3 p-md-4 position-relative overflow-hidden" style="background:#fff;border:1.5px solid rgba(18,56,44,0.10);box-shadow:0 2px 16px rgba(18,56,44,0.07);">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:44px;height:44px;background:rgba(6,182,212,0.12);color:#0369a1;font-size:20px;">
                            <i class="bi bi-clouds-fill"></i>
                        </span>
                    </div>
                    <div class="display-6 font-poster fw-bold mb-1 lh-1" style="color:#0369a1;">
                        <?= number_format(max(71250, $impactStats['co2_absorbed'])) ?> <span class="fs-6 font-mono" style="color:#64748b;font-weight:400;">kg</span>
                    </div>
                    <div class="fw-bold font-mono text-uppercase" style="color:#0284c7;font-size:10.5px;letter-spacing:.07em;">Serapan CO₂ / Tahun</div>
                    <div class="mt-2" style="height:3px;background:linear-gradient(90deg,#0369a1,#67e8f9);border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. SPOTLIGHT SPECIAL: andu CREATIVE & ROASTERY LAB -->
<section class="py-5 andu-spotlight text-white position-relative">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                    <img src="<?= asset('assets/images/logo-andu-white.png') ?>" alt="andu" style="height: 18px; width: auto;">
                    <span class="font-mono text-uppercase text-white small" style="letter-spacing: 2px; font-size: 11px;">
                        OFFICIAL COFFEE ROASTERY & CREATIVE LAB
                    </span>
                </div>
                
                <h2 class="display-5 fw-bold font-poster mb-3 text-white" style="letter-spacing: 0.5px;">
                    Kurasi Kopi Lereng Kerenceng &bull; Oleh <span style="color: #bfd254;">andu</span>
                </h2>
                
                <p class="lead text-light opacity-90 mb-4" style="font-size: 1.1rem; max-width: 620px;">
                    <strong>andu</strong> menghubungkan kultur kopi modern dengan misi pemulihan lereng gunung. Biji kopi dipetik dari tanaman agroforestri di bawah kanopi hutan lindung Cicalengka, lalu di-roast dengan profil khusus untuk menonjolkan keasaman manis alami buah dan madu hutan.
                </p>

                <div class="row g-3 mb-4 font-mono small">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                            <div class="fw-bold mb-1" style="color: #bfd254;"><i class="bi bi-fire me-1"></i> Artisan Small-Batch Roasting</div>
                            <div class="text-white-50" style="font-size: 11px;">Setiap batch disangrai dalam kuantitas terbatas demi kesegaran dan profil rasa optimal.</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);">
                            <div class="fw-bold mb-1" style="color: #bfd254;"><i class="bi bi-tree me-1"></i> Forest Shade Grown</div>
                            <div class="text-white-50" style="font-size: 11px;">Ditanam tanpa pestisida di bawah naungan pohon endemik Kerenceng.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= url('/toko?kategori=kopi') ?>" class="btn btn-trail btn-lg rounded-pill px-4 fw-bold font-poster">
                        <i class="bi bi-bag-heart-fill me-2"></i> Belanja Koleksi Kopi andu
                    </a>
                    <a href="<?= url('/tentang-kami') ?>" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-semibold font-poster">
                        Cerita Kolaborasi Kami →
                    </a>
                </div>
            </div>

            <div class="col-lg-5 text-center">
                <div class="p-3 rounded-4 position-relative" style="background: rgba(255,255,255,0.06); border: 2px solid rgba(255,255,255,0.18);">
                    
                    <!-- Featured Coffee Photo Card -->
                    <div class="bg-white rounded-3 shadow overflow-hidden mb-3 text-dark text-start">
                        <div class="position-relative">
                            <img src="<?= asset('assets/images/coffee-honey.jpg') ?>" alt="Arabica Kerenceng Honey by andu" class="img-fluid w-100" style="height: 220px; object-fit: cover;">
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge-andu shadow">
                                    <img src="<?= asset('assets/images/logo-andu-white.png') ?>" alt="andu" style="height: 12px; width: auto;">
                                    ROASTED BY andu
                                </span>
                            </div>
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 text-white font-mono" style="font-size: 10px;">
                                    Micro-Lot 1.550m
                                </span>
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold font-poster mb-0" style="color: #12382c;">Signature Beans: Kerenceng Honey</h6>
                                <span class="fw-bold font-mono" style="color: #12382c;">Rp 88.000</span>
                            </div>
                            <p class="small text-muted mb-3 font-mono" style="font-size: 11px;">
                                Notes: Red Apple, Wild Honey, Jasmine Floral &bull; Semi-Washed
                            </p>
                            <a href="<?= url('/toko/arabica-kerenceng-honey-an-du') ?>" class="btn btn-sm btn-success w-100 fw-bold font-poster rounded-pill py-2">
                                Beli Biji Kopi Kerenceng Sekarang →
                            </a>
                        </div>
                    </div>

                    <!-- Mini Gallery Strip of Coffee Products -->
                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <a href="<?= url('/toko/kerenceng-natural-anaerobic-an-du') ?>" class="d-block position-relative rounded-2 overflow-hidden shadow-sm border border-light">
                                <img src="<?= asset('assets/images/coffee-anaerobic.jpg') ?>" alt="Natural Anaerobic" class="img-fluid w-100" style="height: 65px; object-fit: cover;">
                                <div class="position-absolute bottom-0 start-0 end-0 p-1 text-center font-mono text-white bg-dark bg-opacity-75" style="font-size: 8px;">Anaerobic</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="<?= url('/toko/drip-bag-kerenceng-an-du-trail') ?>" class="d-block position-relative rounded-2 overflow-hidden shadow-sm border border-light">
                                <img src="<?= asset('assets/images/coffee-drip-bag.jpg') ?>" alt="Drip Bag Trail" class="img-fluid w-100" style="height: 65px; object-fit: cover;">
                                <div class="position-absolute bottom-0 start-0 end-0 p-1 text-center font-mono text-white bg-dark bg-opacity-75" style="font-size: 8px;">Drip Bag</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="<?= url('/toko/cold-brew-concentrate-an-du') ?>" class="d-block position-relative rounded-2 overflow-hidden shadow-sm border border-light">
                                <img src="<?= asset('assets/images/coffee-cold-brew.jpg') ?>" alt="Cold Brew" class="img-fluid w-100" style="height: 65px; object-fit: cover;">
                                <div class="position-absolute bottom-0 start-0 end-0 p-1 text-center font-mono text-white bg-dark bg-opacity-75" style="font-size: 8px;">Cold Brew</div>
                            </a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center gap-3 text-white-50 font-mono small" style="font-size: 11px;">
                        <span><i class="bi bi-box-seam me-1"></i> Pengiriman Se-Indonesia</span>
                        <span>&bull;</span>
                        <span><i class="bi bi-patch-check-fill me-1 text-warning"></i> 100% Arabica Murni</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 5. ETALASE KOPI SPESIALTI KERENCENG x andu -->
<?php if (!empty($featuredCoffee)): ?>
<section class="py-5 bg-white border-bottom">
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle px-3 py-1 rounded-pill font-mono" style="color:#0a3622;">
                        PRODUK LOKAL KERENCENG
                    </span>
                    <span class="badge-andu">
                        <img src="<?= asset('assets/images/logo-andu-white.png') ?>" alt="andu" style="height: 12px; width: auto;">
                        andu COLLECTION
                    </span>
                </div>
                <h2 class="display-6 font-poster text-uppercase mb-1" style="color: #12382c;">
                    Kopi Spesialti Kerenceng x andu
                </h2>
                <p class="text-muted mb-0">Nikmati seduhan kopi arabika pegunungan Cicalengka sambil berkontribusi menjaga hutan.</p>
            </div>
            <a href="<?= url('/toko?kategori=kopi') ?>" class="btn btn-outline-success fw-bold font-poster rounded-pill px-4 mt-3 mt-md-0">
                Lihat Semua Varian Kopi →
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredCoffee as $coffee): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden card-hover rounded-4" style="border: 1.5px solid rgba(18,56,44,0.12) !important;">
                        
                        <!-- Top Visual Photo -->
                        <div class="position-relative overflow-hidden" style="height: 190px;">
                            <?php 
                                $imgSrc = !empty($coffee['primary_image']) ? asset($coffee['primary_image']) : asset('assets/images/coffee-honey.jpg');
                            ?>
                            <img src="<?= $imgSrc ?>" alt="<?= e($coffee['name']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge-andu shadow-sm">
                                    <img src="<?= asset('assets/images/logo-andu-white.png') ?>" alt="andu" style="height: 11px;">
                                    by andu
                                </span>
                            </div>
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 text-white font-mono" style="font-size: 10px;">
                                    Kerenceng 1.550m
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column bg-white">
                            <span class="text-muted font-mono text-uppercase mb-1" style="font-size: 10px; letter-spacing: 1px;">
                                SINGLE ORIGIN ARABICA
                            </span>
                            <h5 class="fw-bold font-poster mb-2" style="color: #12382c; font-size: 15px;">
                                <a href="<?= url('/toko/' . $coffee['slug']) ?>" class="text-dark text-decoration-none">
                                    <?= e($coffee['name']) ?>
                                </a>
                            </h5>
                            
                            <p class="text-muted small flex-grow-1 line-clamp-2 mb-3" style="font-size: 12px;">
                                <?= e($coffee['description']) ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div>
                                    <span class="text-muted small d-block font-mono" style="font-size: 10px;">Mulai dari</span>
                                    <h6 class="fw-bold font-mono mb-0" style="color: #12382c;">
                                        <?= format_rupiah($coffee['min_price'] ?? $coffee['base_price']) ?>
                                    </h6>
                                </div>
                                <a href="<?= url('/toko/' . $coffee['slug']) ?>" class="btn btn-success fw-bold rounded-pill px-3 py-1 font-poster" style="font-size: 12px;">
                                    Pesan Kopi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. PROGRAM DONASI POHON PENAUNG (AGROFORESTRI) -->
<?php if (!empty($treeSpecies)): ?>
<section class="py-5 bg-light border-bottom">
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-success-subtle px-3 py-1 rounded-pill font-mono mb-2" style="color:#0a3622;">
                    PROGRAM ADOPSI & DONASI
                </span>
                <h2 class="display-6 font-poster text-uppercase mb-1" style="color: #12382c;">
                    Pohon Konservasi & Penaung Kebun Kopi
                </h2>
                <p class="text-muted mb-0">Setiap bibit pohon dirawat intensif bersama kelompok tani Karsa Kerentjeng.</p>
            </div>
            <a href="<?= url('/donasi') ?>" class="btn btn-outline-success fw-bold font-poster rounded-pill px-4 mt-3 mt-md-0">
                Pilih Seluruh Pohon →
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($treeSpecies as $species): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden card-hover rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                        <div class="position-relative overflow-hidden" style="height: 180px;">
                            <?php 
                                $treeImg = !empty($species['image']) ? asset($species['image']) : asset('assets/images/tree-kopi.jpg');
                            ?>
                            <img src="<?= $treeImg ?>" alt="<?= e($species['name']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 text-white font-mono" style="font-size: 10px;">
                                Siap Tanam
                            </span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column bg-white">
                            <h5 class="fw-bold font-poster mb-1" style="color: #12382c; font-size: 16px;"><?= e($species['name']) ?></h5>
                            <div class="fw-bold font-mono fs-5 mb-2" style="color: #12382c;">
                                <?= format_rupiah($species['price_per_tree']) ?> <span class="text-muted fw-normal fs-6">/pohon</span>
                            </div>
                            <p class="text-muted small flex-grow-1 line-clamp-3 mb-3">
                                <?= e($species['description']) ?>
                            </p>
                            <div class="small p-2 rounded mb-3 text-secondary font-mono" style="background-color: #f4f8f4; border: 1px solid rgba(18,56,44,0.08);">
                                <i class="bi bi-clouds text-success me-1"></i> Serap CO₂: <strong><?= number_format($species['co2_absorption_kg_per_year'], 1) ?> kg/thn</strong>
                            </div>
                            <a href="<?= url('/donasi?spesies=' . $species['id']) ?>" class="btn btn-success w-100 fw-bold font-poster rounded-pill" style="font-size: 13px;">
                                Donasikan Bibit Ini
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. EXPEDITION RUNDOWN & JADWAL KERENCENG -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 font-mono small" style="background: #e6eee7; color: #12382c;">
                    <i class="bi bi-calendar-event"></i>
                    <span>EXPEDITION ITINERARY</span>
                </div>
                <h2 class="display-6 font-poster text-uppercase mb-3" style="color: #12382c;">
                    Apa yang Akan Kita Lakukan di Gunung Kerenceng?
                </h2>
                <p class="text-secondary small mb-4 lh-base">
                    Kegiatan ini dirancang bagi siapa saja yang ingin bersenang-senang di alam terbuka sambil menanam kebaikan nyata. Tidak perlu pengalaman mendaki yang ekstrem, jalur aman dan dipandu oleh tim lokal berpengalaman.
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #f7f9f7; border-left: 4px solid #12382c;">
                        <span class="badge rounded-circle p-2 font-mono text-white" style="background: #12382c; width: 28px; height: 28px;">1</span>
                        <div>
                            <h6 class="fw-bold font-poster mb-1" style="color: #12382c;">06.00 WIB &bull; Briefing & Coffee Morning by andu</h6>
                            <p class="text-muted small mb-0">Berkumpul di Basecamp Kebon Kopi Kerenceng. Seduh kopi hangat bersama andu Coffee Lab.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #f7f9f7; border-left: 4px solid #638537;">
                        <span class="badge rounded-circle p-2 font-mono text-white" style="background: #638537; width: 28px; height: 28px;">2</span>
                        <div>
                            <h6 class="fw-bold font-poster mb-1" style="color: #12382c;">07.30 WIB &bull; Aksi Tanam Pohon Kopi & Penaung</h6>
                            <p class="text-muted small mb-0">Menanam bersama kelompok tani Karsa Kerentjeng. Setiap bibit dipasang tag identitas donatur & koordinat GPS.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #f7f9f7; border-left: 4px solid #bfd254;">
                        <span class="badge rounded-circle p-2 font-mono text-dark" style="background: #bfd254; width: 28px; height: 28px;">3</span>
                        <div>
                            <h6 class="fw-bold font-poster mb-1" style="color: #12382c;">10.00 WIB &bull; Cupping Session & Sertifikat Relawan</h6>
                            <p class="text-muted small mb-0">Edukasi pascapanen kopi, pembagian E-Sertifikat resmi, serta merchandise relawan.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="<?= url('/kegiatan/3') ?>" class="btn btn-success rounded-pill px-4 py-3 fw-bold font-poster">
                        Daftar Sebagai Relawan Sekarang →
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4 rounded-4 shadow-sm border" style="background: #f4f8f4; border-color: rgba(18,56,44,0.15) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-success font-mono">MAP OVERVIEW</span>
                        <span class="small font-mono text-muted">CICALENGKA TRAIL</span>
                    </div>
                    
                    <div class="p-3 bg-white rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-poster fw-bold text-dark">Titik Kumpul Lapangan</span>
                            <span class="badge-altitude">1.030 mdpl</span>
                        </div>
                        <p class="small text-secondary mb-0">
                            Tanjungwangi Trailhead &bull; Akses kendaraan roda dua & empat dapat parkir di area posko Karsa Kerentjeng.
                        </p>
                    </div>

                    <div class="p-3 bg-white rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-poster fw-bold text-dark">Zona Konservasi Penanaman</span>
                            <span class="badge-altitude">1.250 - 1.720 mdpl</span>
                        </div>
                        <p class="small text-secondary mb-0">
                            Camp Area & Edge of Fields &bull; Menghadap panorama perbukitan Bandung Timur dan lembah Cicalengka.
                        </p>
                    </div>

                    <div class="p-3 rounded-3 text-center" style="background: #12382c; color: #ffffff;">
                        <div class="font-poster fw-bold mb-1">Butuh Panduan Menuju Lokasi?</div>
                        <small class="text-white-50 d-block mb-2 font-mono">Hubungi koordinator lapangan via WhatsApp</small>
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Plant%20For%20Fun%20Kerenceng" target="_blank" class="btn btn-trail btn-sm rounded-pill px-4 fw-bold font-poster">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp Hotline
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 8. TRANSPARANSI & MONITORING GPS PETA -->
<section class="py-5 topo-pattern-light border-bottom">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <span class="badge rounded-pill font-mono px-3 py-1 mb-2" style="background: #12382c; color: #ffffff;">
                    OPEN ECO-DATA
                </span>
                <h3 class="display-6 font-poster text-uppercase mb-2" style="color: #12382c;">
                    Transparansi Terbuka & Pelacakan GPS Pohon
                </h3>
                <p class="text-secondary small mb-0">
                    Setiap donasi dan bibit pohon yang tertanam memiliki koordinat GPS presisi, foto kondisi berkala, serta sertifikat unik yang dapat Anda verifikasi mandiri secara publik.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="<?= url('/peta-lokasi') ?>" class="btn btn-success fw-bold font-poster rounded-pill px-4 py-2 me-2">
                    <i class="bi bi-geo-alt-fill me-1"></i> Buka Peta Digital
                </a>
                <a href="<?= url('/transparansi') ?>" class="btn btn-outline-dark fw-bold font-poster rounded-pill px-4 py-2 mt-2 mt-md-0">
                    Laporan Dampak
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 9. CARA KERJA & METODE PEMBAYARAN MANUAL -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-success-subtle px-3 py-1 rounded-pill font-mono mb-2" style="color:#0a3622;">PROSES MUDAH</span>
            <h2 class="display-6 font-poster text-uppercase" style="color: #12382c;">Bagaimana Cara Berpartisipasi?</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Berdampak nyata bagi kelestarian alam hanya dalam 3 langkah mudah, transparan, dan tanpa perantara rumit.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 card-hover rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="fs-1 mb-3">🌱</div>
                    <h5 class="fw-bold font-poster" style="color: #12382c;">1. Pilih Aksi / Kopi / Pohon</h5>
                    <p class="text-muted small">
                        Daftar relawan event Kerenceng, pesan biji kopi kurasi andu, atau donasikan bibit pohon konservasi.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 card-hover rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="fs-1 mb-3">💳</div>
                    <h5 class="fw-bold font-poster" style="color: #12382c;">2. Pembayaran Manual 100%</h5>
                    <p class="text-muted small">
                        Transfer manual ke rekening Bank / QRIS resmi dengan kode unik 3 digit, lalu unggah foto bukti transfer secara mandiri.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 card-hover rounded-4" style="border: 1px solid rgba(18,56,44,0.1) !important;">
                    <div class="fs-1 mb-3">📍</div>
                    <h5 class="fw-bold font-poster" style="color: #12382c;">3. Verifikasi & Pantau Peta</h5>
                    <p class="text-muted small">
                        Admin memverifikasi bukti Anda, pesanan kopi diproses kirim, tiket kegiatan terbit, dan bibit pohon dipantau di peta GPS.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 9.5 KABAR LAPANGAN & JURNAL EKSPEDISI -->
<?php if (!empty($latestNews)): ?>
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-5 gap-3">
            <div>
                <span class="badge bg-success-subtle px-3 py-1 rounded-pill font-mono mb-2" style="color:#0a3622;">JURNAL &amp; CERITA LAPANGAN</span>
                <h2 class="display-6 font-poster text-uppercase mb-1" style="color: #12382c;">Kabar Ekspedisi &amp; Riset Agroforestry</h2>
                <p class="text-muted mb-0">Ikuti catatan lapangan, proses kurasi kopi andu, dan cerita langsung dari lereng Gunung Kerenceng.</p>
            </div>
            <a href="/kabar" class="btn btn-outline-success rounded-pill px-4 fw-semibold">
                Lihat Semua Kabar <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($latestNews as $item): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover bg-white d-flex flex-column" style="border: 1px solid rgba(18,56,44,0.08) !important;">
                        <div class="position-relative">
                            <a href="/kabar/<?= htmlspecialchars($item['slug']) ?>">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"
                                         class="card-img-top object-fit-cover" style="height: 200px;">
                                <?php else: ?>
                                    <div class="bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-tree fs-1" style="color:#0a3622"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-dark bg-opacity-80 text-white px-3 py-1 rounded-pill small font-mono">
                                    <?= htmlspecialchars($item['category'] ?? 'Kabar Lapangan') ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                                <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?></span>
                                <span>&bull;</span>
                                <span><i class="bi bi-clock me-1"></i><?= $item['read_time_minutes'] ?? 3 ?> mnt</span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2" style="font-family: 'Bitter', serif; font-size: 1.15rem; line-height: 1.35;">
                                <a href="/kabar/<?= htmlspecialchars($item['slug']) ?>" class="text-decoration-none text-dark hover-success">
                                    <?= htmlspecialchars($item['title']) ?>
                                </a>
                            </h5>

                            <p class="text-secondary small mb-4 flex-grow-1" style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($item['excerpt'] ?? mb_substr(strip_tags($item['content']), 0, 130) . '...') ?>
                            </p>

                            <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
                                <span class="small text-muted"><i class="bi bi-eye me-1"></i><?= number_format($item['views_count'] ?? 0) ?> views</span>
                                <a href="/kabar/<?= htmlspecialchars($item['slug']) ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                                    Baca <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 10. FAQ TEASER -->
<?php if (!empty($faqs)): ?>
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill font-mono mb-2">FAQ</span>
            <h2 class="display-6 font-poster text-uppercase" style="color: #12382c;">Sering Ditanyakan</h2>
            <p class="text-muted">Jawaban cepat seputar pemesanan kopi andu, pendaftaran relawan, dan verifikasi donasi.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion shadow-sm rounded-4 overflow-hidden" id="accordionHomeFaq">
                    <?php foreach ($faqs as $idx => $faq): ?>
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header" id="heading<?= $idx ?>">
                                <button class="accordion-button <?= $idx > 0 ? 'collapsed' : '' ?> fw-semibold font-poster" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $idx ?>" style="color: #12382c;">
                                    <?= e($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#accordionHomeFaq">
                                <div class="accordion-body text-secondary small lh-base">
                                    <?= nl2br(e($faq['answer'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= url('/faq') ?>" class="text-success fw-bold text-decoration-none font-poster">
                        Lihat Semua Pertanyaan & Jawaban <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
