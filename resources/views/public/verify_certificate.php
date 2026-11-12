<?php
/**
 * View: Verifikasi Keaslian Sertifikat Publik
 * Path: resources/views/public/verify_certificate.php
 * Platform: Plant For Fun • Kerenceng Coffee Expedition
 */

$data = $certificate['data'] ?? [];
$isFound = !empty($certificate);
?>

<!-- Google Fonts Spesifik untuk Sertifikat Mewah -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bitter:wght@700;800;900&family=Cinzel:wght@600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<style>
/* Hero Header Verifikasi */
.verify-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%);
    border-bottom: 3px solid #1c4b3c;
    padding: 44px 0 36px;
    position: relative;
    overflow: hidden;
}
.verify-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(191,210,84,.15) 0%, transparent 70%);
    pointer-events: none;
}

/* Wrapper Sertifikat Responsif */
.cert-canvas-outer {
    background: #091a13;
    padding: 24px 16px;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 32px;
}
.cert-canvas-scroll {
    min-width: 980px;
    display: flex;
    justify-content: center;
}

/* Certificate Canvas */
.cert-container {
    width: 1000px;
    height: 720px;
    background: #ffffff;
    position: relative;
    padding: 36px 44px 26px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.4);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}

/* Watermark Background */
.cert-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.035;
    pointer-events: none;
    z-index: 1;
    width: 480px;
    height: auto;
}

/* Double Border Ornament */
.cert-border-outer {
    position: absolute;
    top: 14px;
    left: 14px;
    right: 14px;
    bottom: 14px;
    border: 3px solid #12382c;
    border-radius: 8px;
    pointer-events: none;
    z-index: 2;
}
.cert-border-inner {
    position: absolute;
    top: 20px;
    left: 20px;
    right: 20px;
    bottom: 20px;
    border: 1px solid #c59b27;
    border-radius: 5px;
    pointer-events: none;
    z-index: 2;
}
.corner-knot {
    position: absolute;
    width: 14px;
    height: 14px;
    background: #c59b27;
    border-radius: 50%;
    z-index: 3;
}
.ck-tl { top: 18px; left: 18px; }
.ck-tr { top: 18px; right: 18px; }
.ck-bl { bottom: 18px; left: 18px; }
.ck-br { bottom: 18px; right: 18px; }

.cert-content {
    position: relative;
    z-index: 5;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

/* Header & Logos */
.cert-header {
    text-align: center;
}
.cert-logos-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(18, 56, 44, 0.12);
    padding-bottom: 12px;
    margin-bottom: 14px;
}
.main-brand-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}
.presented-group {
    display: flex;
    align-items: center;
    gap: 14px;
}
.presented-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #64748b;
}
.partner-logo-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 28px;
}
.partner-logo-item img {
    height: 24px;
    width: auto;
    object-fit: contain;
}

.cert-title-group {
    margin-top: 2px;
}
.cert-title {
    font-family: 'Cinzel', serif;
    font-size: 25px;
    letter-spacing: 4px;
    color: #12382c;
    text-transform: uppercase;
    font-weight: 800;
    margin-bottom: 4px;
}
.cert-subtitle {
    font-family: 'Space Mono', monospace;
    font-size: 10.5px;
    letter-spacing: 2px;
    color: #926b14;
    font-weight: 700;
    text-transform: uppercase;
}

/* Body */
.cert-body {
    text-align: center;
    padding: 4px 26px;
}
.cert-presented-to {
    font-size: 12px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
    margin-bottom: 8px;
}
.cert-recipient {
    font-family: 'Playfair Display', serif;
    font-size: 40px;
    color: #12382c;
    font-style: italic;
    font-weight: 700;
    border-bottom: 2px solid #c59b27;
    display: inline-block;
    padding: 0 32px 5px;
    margin-bottom: 16px;
    letter-spacing: 0.5px;
}
.cert-text {
    font-size: 13.5px;
    line-height: 1.7;
    color: #334155;
    max-width: 840px;
    margin: 0 auto;
}
.cert-text strong {
    color: #12382c;
    font-weight: 700;
}

/* Footer */
.cert-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 10px 8px 0;
    border-top: 1px solid rgba(18, 56, 44, 0.1);
}
.cert-qr {
    text-align: center;
    min-width: 140px;
}
.cert-qr img {
    width: 76px;
    height: 76px;
    border: 1px solid #cbd5e1;
    padding: 3px;
    border-radius: 6px;
    background: #fff;
}
.cert-meta {
    font-family: 'Space Mono', monospace;
    font-size: 9px;
    color: #64748b;
    margin-top: 3px;
    letter-spacing: 0.5px;
}

.cert-seal {
    text-align: center;
}
.seal-circle {
    width: 82px;
    height: 82px;
    border-radius: 50%;
    border: 2px solid #c59b27;
    background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 10px rgba(197, 155, 39, 0.25);
    margin: 0 auto 4px;
}
.seal-star {
    color: #c59b27;
    font-size: 14px;
    margin-bottom: 1px;
}
.seal-text-top {
    font-size: 8.5px;
    font-weight: 800;
    color: #12382c;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}
.seal-text-bot {
    font-size: 7px;
    font-weight: 700;
    color: #926b14;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.seal-brand {
    font-family: 'Bitter', serif;
    font-size: 10.5px;
    font-weight: 800;
    color: #12382c;
    display: block;
    letter-spacing: 0.5px;
}

.cert-signature {
    text-align: center;
    min-width: 230px;
}
.sig-handwritten {
    font-family: 'Playfair Display', serif;
    font-style: italic;
    font-size: 21px;
    color: #12382c;
    margin-bottom: 5px;
    font-weight: 700;
}
.sig-line {
    width: 200px;
    border-bottom: 1.5px solid #12382c;
    margin: 0 auto 5px;
}
.sig-name {
    font-weight: 800;
    font-size: 12.5px;
    color: #0f172a;
}
.sig-title {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
}

/* Bottom Partner Strip */
.media-partners-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding-top: 8px;
    font-size: 9.5px;
    color: #94a3b8;
}
.media-partners-strip img {
    height: 16px;
    width: auto;
    object-fit: contain;
    opacity: 0.85;
}

/* =========================================================
   PRINT / SAVE AS PDF — Sertifikat A4 Landscape
   ========================================================= */
@page {
    size: A4 landscape;
    margin: 0;
}

@media print {
    /* Reset body */
    html, body {
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 297mm !important;
        height: 210mm !important;
        overflow: hidden !important;
    }

    /* Sembunyikan semua kecuali kanvas sertifikat */
    .verify-hero,
    .navbar,
    .footer,
    .alert,
    .cert-actions-bar,
    .cert-metadata-card,
    .cert-scroll-hint,
    section.py-5 > .container > .row:not(:first-child),
    .cert-canvas-outer ~ * {
        display: none !important;
    }

    /* Wrapper section jadi transparan */
    section.py-5 {
        background: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    section.py-5 > .container {
        padding: 0 !important;
        margin: 0 !important;
        max-width: none !important;
    }

    /* Canvas outer: hilangkan background gelap */
    .cert-canvas-outer {
        background: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        overflow: visible !important;
        margin: 0 !important;
        border-radius: 0 !important;
    }
    .cert-canvas-scroll {
        min-width: auto !important;
        justify-content: center !important;
    }

    /* Canvas sertifikat: pas A4 landscape */
    .cert-container {
        width: 281mm !important;
        height: 194mm !important;
        margin: 8mm auto !important;
        padding: 14mm 16mm 10mm !important;
        box-shadow: none !important;
        border-radius: 6px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        overflow: hidden !important;
    }

    /* Border ornament */
    .cert-border-outer {
        top: 5mm; left: 5mm; right: 5mm; bottom: 5mm;
        border-width: 2.5pt !important;
    }
    .cert-border-inner {
        top: 7mm; left: 7mm; right: 7mm; bottom: 7mm;
    }
    .corner-knot {
        width: 4mm !important;
        height: 4mm !important;
    }
    .ck-tl { top: 6.3mm !important; left: 6.3mm !important; }
    .ck-tr { top: 6.3mm !important; right: 6.3mm !important; }
    .ck-bl { bottom: 6.3mm !important; left: 6.3mm !important; }
    .ck-br { bottom: 6.3mm !important; right: 6.3mm !important; }

    /* Watermark */
    .cert-watermark { width: 140mm !important; }

    /* Typography */
    .cert-title      { font-size: 20pt !important; letter-spacing: 3px !important; }
    .cert-subtitle   { font-size: 8pt !important; }
    .cert-recipient  { font-size: 28pt !important; }
    .cert-text       { font-size: 9.5pt !important; line-height: 1.6 !important; }
    .cert-presented-to { font-size: 8pt !important; }

    /* Logos */
    .cert-logos-bar  { padding-bottom: 6px !important; margin-bottom: 8px !important; }
    .partner-logo-item img { height: 20px !important; }

    /* Footer */
    .cert-qr img     { width: 56px !important; height: 56px !important; }
    .seal-circle     { width: 60px !important; height: 60px !important; }
    .sig-handwritten { font-size: 15pt !important; }
    .sig-line        { width: 48mm !important; }
    .media-partners-strip img { height: 12px !important; }
    .cert-meta       { font-size: 7pt !important; }

    /* Paksa browser tampilkan warna & gambar saat print */
    img { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<!-- 1. HERO & SEARCH SECTION -->
<section class="verify-hero text-white">
    <div class="container text-center py-2">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25);">
            <i class="bi bi-patch-check-fill" style="color: #bfd254;"></i>
            <span class="font-poster text-uppercase small text-white" style="letter-spacing: 1.5px; font-size: 11px;">
                Sistem Validasi Kredensial Resmi
            </span>
        </div>
        <h1 class="display-6 fw-bold mb-2 font-poster">Verifikasi Keaslian Sertifikat</h1>
        <p class="lead text-white-50 mx-auto mb-4 fs-6" style="max-width: 650px;">
            Sistem autentikasi sertifikat digital resmi <strong>Plant For Fun &bull; Kerenceng Coffee Planting &amp; Agroforestry Expedition</strong>.
        </p>

        <!-- Formulir Pencarian Cepat -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <form action="<?= url('/verifikasi-sertifikat') ?>" method="GET" class="card border-0 p-2 rounded-pill shadow-lg bg-white" onsubmit="return handleVerifySubmit(event);">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-3 text-muted">
                            <i class="bi bi-search fs-5"></i>
                        </span>
                        <input type="text" id="certNumberInput" name="nomor" class="form-control border-0 shadow-none ps-2 font-mono" placeholder="Contoh: CERT-202611... atau CERT-REL-..." value="<?= e($searchedNumber ?? '') ?>" required>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm font-poster" style="background:#12382c; border:none;">
                            Verifikasi Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 2. HASIL VERIFIKASI -->
<section class="py-5 bg-light">
    <div class="container">

        <!-- KONDISI 1: SERTIFIKAT DITEMUKAN & VALID -->
        <?php if ($isFound): ?>
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">

                    <!-- Alert Autentikasi Hijau -->
                    <div class="alert border-0 rounded-4 shadow-sm p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background:#ecfdf5; border:1px solid #a7f3d0!important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-1 text-success">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading fw-bold mb-0 text-success" style="font-size: 1.15rem;">
                                    Sertifikat Sah &amp; Terverifikasi Resmi!
                                </h5>
                                <p class="mb-0 small text-success-emphasis">
                                    Dokumen nomor <strong class="font-mono"><?= e($certificate['certificate_number']) ?></strong> terdata secara autentik dalam basis data <strong>Plant For Fun &bull; Kerenceng Coffee Expedition</strong>.
                                </p>
                            </div>
                        </div>

                        <!-- Tombol Cetak / Simpan -->
                        <div class="cert-actions-bar d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-success fw-bold rounded-pill px-3 py-2 shadow-sm" onclick="window.print()" style="background:#12382c; border:none;">
                                <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
                            </button>
                            <a href="<?= url('/verifikasi-sertifikat') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2">
                                <i class="bi bi-arrow-left me-1"></i> Cek Nomor Lain
                            </a>
                        </div>
                    </div>

                    <!-- Petunjuk Geser Layar Kecil -->
                    <div class="d-block d-lg-none text-center text-muted small mb-2 cert-scroll-hint">
                        <i class="bi bi-arrows-expand me-1"></i> Geser ke samping untuk melihat seluruh sertifikat
                    </div>

                    <!-- KANVAS SERTIFIKAT RESMI PLANT FOR FUN (PERSIS KANVAS RESMI) -->
                    <div class="cert-canvas-outer">
                        <div class="cert-canvas-scroll">
                            <div class="cert-container" id="printableCert">
                                <!-- Background Watermark Emblem -->
                                <img src="<?= asset('assets/images/logo-emblem.svg') ?>" alt="" class="cert-watermark">

                                <!-- Ornamental Borders & Knots -->
                                <div class="cert-border-outer"></div>
                                <div class="cert-border-inner"></div>
                                <div class="corner-knot ck-tl"></div>
                                <div class="corner-knot ck-tr"></div>
                                <div class="corner-knot ck-bl"></div>
                                <div class="corner-knot ck-br"></div>

                                <div class="cert-content">
                                    <!-- 1. Header & Logos Bar -->
                                    <div class="cert-header">
                                        <div class="cert-logos-bar">
                                            <!-- Brand Utama: Plant For Fun -->
                                            <div class="main-brand-wrap">
                                                <img src="<?= asset('assets/images/logo-plant-for-fun.svg') ?>" alt="Plant For Fun" style="height: 48px; width: auto;">
                                            </div>

                                            <!-- Presented By Partners -->
                                            <div class="presented-group">
                                                <span class="presented-label">Presented by:</span>
                                                <div class="partner-logo-item" title="andu">
                                                    <img src="<?= asset('assets/images/logo-andu.png') ?>" alt="andu">
                                                </div>
                                                <div class="partner-logo-item" title="Karsa Kerentjeng">
                                                    <img src="<?= asset('assets/images/logo-karsa-kerentjeng.png') ?>" alt="Karsa Kerentjeng">
                                                </div>
                                                <div class="partner-logo-item" title="Ethera">
                                                    <img src="<?= asset('assets/images/logo-ethera.png') ?>" alt="Ethera">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cert-title-group">
                                            <?php if (($certificate['type'] ?? 'donasi') === 'partisipasi'): ?>
                                                <h1 class="cert-title">Sertifikat Relawan Aksi Hijau</h1>
                                                <div class="cert-subtitle">Plant For Fun &bull; Kerenceng Coffee Planting &amp; Agroforestry Expedition</div>
                                            <?php else: ?>
                                                <h1 class="cert-title">Sertifikat Penghargaan Pelestarian Alam</h1>
                                                <div class="cert-subtitle">Plant For Fun &bull; Kerenceng Coffee Planting &amp; Agroforestry Expedition</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- 2. Body -->
                                    <div class="cert-body">
                                        <div class="cert-presented-to">Diberikan dengan penuh rasa hormat &amp; apresiasi setinggi-tingginya kepada:</div>
                                        <div class="cert-recipient"><?= e($data['recipient_name'] ?? 'Sahabat Peduli Bumi') ?></div>
                                        
                                        <?php if (($certificate['type'] ?? 'donasi') === 'partisipasi'): ?>
                                            <p class="cert-text">
                                                Atas kerja keras, dedikasi tenaga, dan partisipasi aktif sebagai <strong>Relawan Aksi Lapangan</strong> dalam kegiatan <strong>"<?= e($data['event_title'] ?? 'Plant For Fun: Kerenceng Coffee Planting') ?>"</strong> yang diselenggarakan di <?= e($data['location'] ?? 'Lereng Gunung Kerenceng, Cicalengka (1.550 mdpl)') ?> pada tanggal <?= !empty($data['event_date']) ? format_date($data['event_date'], 'd F Y') : format_date($certificate['issued_at'], 'd F Y') ?> demi pemulihan ekosistem hutan dan penguatan ekonomi petani kopi lokal.
                                            </p>
                                        <?php else: ?>
                                            <p class="cert-text">
                                                Atas dedikasi dan kontribusi nyata dalam mengadopsi serta mendonasikan <strong><?= $data['total_trees'] ?? 1 ?> Batang Bibit Pohon</strong> (<?= e($data['species_summary'] ?? 'Kopi Arabika & Pohon Penaung Karsa') ?>) di kawasan agroforestri lereng Gunung Kerenceng (1.550 mdpl), Cicalengka, Jawa Barat. Pohon ini dirawat berkelanjutan dengan estimasi serapan karbon <strong><?= number_format((float)($data['total_co2_kg'] ?? 25), 1) ?> kg CO₂/tahun</strong>.
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- 3. Footer Elements -->
                                    <div>
                                        <div class="cert-footer">
                                            <!-- QR Code Verifikasi -->
                                            <div class="cert-qr">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($data['verify_url'] ?? url('/verifikasi-sertifikat/' . $certificate['certificate_number'])) ?>" alt="QR Code Keaslian">
                                                <div class="cert-meta"><?= e($certificate['certificate_number']) ?></div>
                                                <div class="cert-meta">Terbit: <?= format_date($certificate['issued_at'] ?? $certificate['created_at'], 'd M Y') ?></div>
                                            </div>

                                            <!-- Stempel Resmi Plant For Fun -->
                                            <div class="cert-seal">
                                                <div class="seal-circle">
                                                    <i class="bi bi-patch-check-fill seal-star"></i>
                                                    <span class="seal-text-top">RESMI</span>
                                                    <span class="seal-text-bot">TERVERIFIKASI</span>
                                                </div>
                                                <span class="seal-brand">Plant For Fun</span>
                                            </div>

                                            <!-- Tanda Tangan Penyelenggara -->
                                            <div class="cert-signature">
                                                <div class="sig-handwritten">
                                                    <?= e($data['signer_name'] ?? 'Koordinator Ekspedisi Kerenceng') ?>
                                                </div>
                                                <div class="sig-line"></div>
                                                <div class="sig-name"><?= e($data['signer_name'] ?? 'Koordinator Ekspedisi Kerenceng') ?></div>
                                                <div class="sig-title"><?= e($data['signer_title'] ?? 'Plant For Fun &bull; Kerenceng Expedition') ?></div>
                                            </div>
                                        </div>

                                        <!-- Media Partner Strip -->
                                        <div class="media-partners-strip">
                                            <span>Media Partner:</span>
                                            <img src="<?= asset('assets/images/logo-clkpny.png') ?>" alt="CLK.PNY (Tjitjalengka Poenja)" title="CLK.PNY (Tjitjalengka Poenja)">
                                            <img src="<?= asset('assets/images/logo-cicalengka-go.png') ?>" alt="Cicalengka GO" title="Cicalengka GO">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Metadata Verifikasi -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white cert-metadata-card">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-shield-check text-success me-1"></i> Rincian Kredensial Terverifikasi
                            </h6>
                            <span class="badge bg-success-subtle text-success px-3 py-1 font-mono">
                                STATUS: AKTIF &amp; VALID
                            </span>
                        </div>
                        <div class="row g-3 small">
                            <div class="col-md-3 col-6">
                                <span class="text-muted d-block">Nomor Dokumen:</span>
                                <strong class="font-mono text-dark"><?= e($certificate['certificate_number']) ?></strong>
                            </div>
                            <div class="col-md-3 col-6">
                                <span class="text-muted d-block">Kategori:</span>
                                <strong class="text-success"><?= strtoupper($certificate['type'] === 'donasi' ? '🌱 Donasi Pohon' : '🙋 Partisipasi Relawan') ?></strong>
                            </div>
                            <div class="col-md-3 col-6">
                                <span class="text-muted d-block">Nama Penerima:</span>
                                <strong class="text-dark"><?= e($data['recipient_name'] ?? '-') ?></strong>
                            </div>
                            <div class="col-md-3 col-6">
                                <span class="text-muted d-block">Tanggal Diterbitkan:</span>
                                <strong class="text-dark"><?= date('d F Y', strtotime($certificate['issued_at'])) ?></strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <!-- KONDISI 2: TIDAK DITEMUKAN -->
        <?php elseif ($notFound): ?>
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center py-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <div class="avatar-circle mx-auto bg-danger-subtle text-danger rounded-circle fs-1 d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Sertifikat Tidak Ditemukan</h4>
                        <p class="text-secondary small mb-4">
                            Nomor sertifikat <strong class="text-danger font-monospace"><?= e($searchedNumber) ?></strong> tidak ditemukan dalam basis data resmi kami. Pastikan nomor sertifikat diketik lengkap dan benar.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?= url('/verifikasi-sertifikat') ?>" class="btn btn-success rounded-pill px-4 fw-semibold" style="background:#12382c; border:none;">
                                Coba Nomor Lain
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <!-- KONDISI 3: BELUM MENCARI (PANDUAN & CONTOH) -->
        <?php else: ?>
            <div class="row g-4 align-items-center my-3">
                <div class="col-lg-6">
                    <h3 class="fw-bold text-dark mb-3">Bagaimana Cara Mengetahui Keaslian Sertifikat?</h3>
                    <p class="text-secondary mb-4">
                        Setiap sertifikat resmi yang dikeluarkan oleh <strong>Plant For Fun</strong> memiliki nomor seri unik yang tertera di bagian sertifikat, serta QR code yang mengarahkan langsung ke halaman validasi ini.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background:#12382c!important;">1</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Cari Kode Seri Sertifikat</h6>
                                <p class="small text-muted mb-0">Nomor sertifikat berformat <code>CERT-...</code> atau <code>CERT-REL-...</code> (contoh: <code>CERT-REL-20261112-1B5E69</code>).</p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start">
                            <div class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background:#12382c!important;">2</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Masukkan ke Kolom Pencarian</h6>
                                <p class="small text-muted mb-0">Ketik nomor tersebut pada formulir di atas, lalu tekan tombol <strong>Verifikasi Sekarang</strong>.</p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start">
                            <div class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background:#12382c!important;">3</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Lihat Hasil Autentikasi</h6>
                                <p class="small text-muted mb-0">Sistem akan menampilkan kanvas sertifikat resmi asli lengkap dengan rincian kontribusi donatur atau relawan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Sampel Sertifikat Resmi -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                            <i class="bi bi-clock-history text-success me-2"></i> Contoh Sertifikat Terbit Baru-baru Ini
                        </h5>
                        <p class="small text-muted mb-3">Klik salah satu nomor sertifikat di bawah ini untuk menguji sistem verifikasi:</p>

                        <?php if (empty($recentCertificates)): ?>
                            <p class="text-muted small">Belum ada sertifikat yang diterbitkan.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recentCertificates as $rc): ?>
                                    <a href="<?= url('/verifikasi-sertifikat/' . $rc['certificate_number']) ?>" class="list-group-item list-group-item-action px-3 py-3 rounded-3 mb-2 border border-light-subtle d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="font-monospace fw-bold text-success mb-1">
                                                <?= e($rc['certificate_number']) ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($rc['recipient_name'] ?? 'Donatur / Relawan') ?> &bull; 
                                                <?= date('d M Y', strtotime($rc['issued_at'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-secondary border">
                                            <?= ucfirst($rc['type']) ?> <i class="bi bi-chevron-right ms-1"></i>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<script>
function handleVerifySubmit(e) {
    e.preventDefault();
    let num = document.getElementById("certNumberInput").value.trim();
    if (num) {
        window.location.href = "<?= url('/verifikasi-sertifikat/') ?>/" + encodeURIComponent(num);
    }
    return false;
}

/**
 * Cetak sertifikat — tunggu gambar & font sebelum print
 */
function printCertificate() {
    const images = document.querySelectorAll('.cert-container img');
    const promises = Array.from(images).map(img => {
        if (img.complete) return Promise.resolve();
        return new Promise(resolve => {
            img.onload = resolve;
            img.onerror = resolve;
        });
    });
    document.fonts.ready.then(() => {
        Promise.all(promises).then(() => {
            setTimeout(() => window.print(), 150);
        });
    });
}

// Override tombol cetak
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[onclick="window.print()"]').forEach(btn => {
        btn.removeAttribute('onclick');
        btn.addEventListener('click', printCertificate);
    });
});
</script>
