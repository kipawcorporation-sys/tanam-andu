<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Sertifikat Resmi — Plant For Fun') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@700;800;900&family=Cinzel:wght@600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #12382c;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
            min-height: 100vh;
        }
        .action-toolbar {
            width: 100%;
            max-width: 1040px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 13.5px;
            transition: all 0.2s;
        }
        .btn-print {
            background: #bfd254;
            color: #0d2b20;
            box-shadow: 0 4px 14px rgba(191, 210, 84, 0.35);
        }
        .btn-print:hover {
            background: #d4e865;
            transform: translateY(-1px);
        }
        .btn-back {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
        }
        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Certificate Canvas */
        .cert-container {
            width: 1040px;
            height: 740px;
            background: #ffffff;
            position: relative;
            padding: 38px 46px 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
            border-radius: 12px;
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
            width: 500px;
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
            width: 16px;
            height: 16px;
            background: #c59b27;
            border-radius: 50%;
            z-index: 3;
        }
        .ck-tl { top: 18px; left: 18px; }
        .ck-tr { top: 18px; right: 18px; }
        .ck-bl { bottom: 18px; left: 18px; }
        .ck-br { bottom: 18px; right: 18px; }

        /* Certificate Content Layers */
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
            padding-bottom: 14px;
            margin-bottom: 16px;
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
            height: 30px;
        }
        .partner-logo-item img {
            height: 26px;
            width: auto;
            object-fit: contain;
        }

        .cert-title-group {
            margin-top: 4px;
        }
        .cert-title {
            font-family: 'Cinzel', serif;
            font-size: 26px;
            letter-spacing: 4px;
            color: #12382c;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .cert-subtitle {
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            letter-spacing: 2px;
            color: #926b14;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Body */
        .cert-body {
            text-align: center;
            padding: 8px 30px;
        }
        .cert-presented-to {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .cert-recipient {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: #12382c;
            font-style: italic;
            font-weight: 700;
            border-bottom: 2px solid #c59b27;
            display: inline-block;
            padding: 0 36px 6px;
            margin-bottom: 18px;
            letter-spacing: 0.5px;
        }
        .cert-text {
            font-size: 14px;
            line-height: 1.75;
            color: #334155;
            max-width: 860px;
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
            padding: 10px 10px 0;
            border-top: 1px solid rgba(18, 56, 44, 0.1);
        }
        .cert-qr {
            text-align: center;
            min-width: 140px;
        }
        .cert-qr img {
            width: 80px;
            height: 80px;
            border: 1px solid #cbd5e1;
            padding: 3px;
            border-radius: 6px;
            background: #fff;
        }
        .cert-meta {
            font-family: 'Space Mono', monospace;
            font-size: 9.5px;
            color: #64748b;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }

        .cert-seal {
            text-align: center;
        }
        .seal-circle {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            border: 2px solid #c59b27;
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 10px rgba(197, 155, 39, 0.25);
            margin: 0 auto 5px;
        }
        .seal-star {
            color: #c59b27;
            font-size: 15px;
            margin-bottom: 1px;
        }
        .seal-text-top {
            font-size: 9px;
            font-weight: 800;
            color: #12382c;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }
        .seal-text-bot {
            font-size: 7.5px;
            font-weight: 700;
            color: #926b14;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .seal-brand {
            font-family: 'Bitter', serif;
            font-size: 11px;
            font-weight: 800;
            color: #12382c;
            display: block;
            letter-spacing: 0.5px;
        }

        .cert-signature {
            text-align: center;
            min-width: 240px;
        }
        .sig-handwritten {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 22px;
            color: #12382c;
            margin-bottom: 6px;
            font-weight: 700;
        }
        .sig-line {
            width: 210px;
            border-bottom: 1.5px solid #12382c;
            margin: 0 auto 5px;
        }
        .sig-name {
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
        }
        .sig-title {
            font-size: 11px;
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
            font-size: 10px;
            color: #94a3b8;
        }
        .media-partners-strip img {
            height: 18px;
            width: auto;
            object-fit: contain;
            opacity: 0.85;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .action-toolbar {
                display: none;
            }
            .cert-container {
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar Atas -->
    <div class="action-toolbar">
        <a href="<?= url('/dashboard/sertifikat') ?>" class="btn btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <button type="button" class="btn btn-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Sertifikat Kanvas -->
    <div class="cert-container">
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
                    <?php if (($cert['type'] ?? 'donasi') === 'partisipasi'): ?>
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
                <div class="cert-recipient"><?= e($cert['data']['recipient_name'] ?? 'Sahabat Peduli Bumi') ?></div>
                
                <?php if (($cert['type'] ?? 'donasi') === 'partisipasi'): ?>
                    <p class="cert-text">
                        Atas kerja keras, dedikasi tenaga, dan partisipasi aktif sebagai <strong>Relawan Aksi Lapangan</strong> dalam kegiatan <strong>"<?= e($cert['data']['event_title'] ?? 'Plant For Fun: Kerenceng Coffee Planting') ?>"</strong> yang diselenggarakan di <?= e($cert['data']['location'] ?? 'Lereng Gunung Kerenceng, Cicalengka (1.550 mdpl)') ?> pada tanggal <?= format_date($cert['data']['event_date'] ?? $cert['issued_at'], 'd F Y') ?> demi pemulihan ekosistem hutan dan penguatan ekonomi petani kopi lokal.
                    </p>
                <?php else: ?>
                    <p class="cert-text">
                        Atas dedikasi dan kontribusi nyata dalam mengadopsi serta mendonasikan <strong><?= $cert['data']['total_trees'] ?? 1 ?> Batang Bibit Pohon</strong> (<?= e($cert['data']['species_summary'] ?? 'Kopi Arabika & Pohon Penaung Karsa') ?>) di kawasan agroforestri lereng Gunung Kerenceng (1.550 mdpl), Cicalengka, Jawa Barat. Pohon ini dirawat berkelanjutan dengan estimasi serapan karbon <strong><?= number_format($cert['data']['total_co2_kg'] ?? 25, 1) ?> kg CO₂/tahun</strong>.
                    </p>
                <?php endif; ?>
            </div>

            <!-- 3. Footer Elements -->
            <div>
                <div class="cert-footer">
                    <!-- QR Code Verifikasi -->
                    <div class="cert-qr">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($cert['data']['verify_url'] ?? url('/verifikasi-sertifikat/' . $cert['certificate_number'])) ?>" alt="QR Code Keaslian">
                        <div class="cert-meta"><?= e($cert['certificate_number']) ?></div>
                        <div class="cert-meta">Terbit: <?= format_date($cert['issued_at'] ?? $cert['created_at'], 'd M Y') ?></div>
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
                            <?= e($cert['data']['signer_name'] ?? 'Koordinator Ekspedisi Kerenceng') ?>
                        </div>
                        <div class="sig-line"></div>
                        <div class="sig-name"><?= e($cert['data']['signer_name'] ?? 'Koordinator Ekspedisi Kerenceng') ?></div>
                        <div class="sig-title"><?= e($cert['data']['signer_title'] ?? 'Plant For Fun &bull; Kerenceng Expedition') ?></div>
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

</body>
</html>
