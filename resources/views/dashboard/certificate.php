<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #52796f;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #212529;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .action-toolbar {
            width: 100%;
            max-width: 1000px;
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
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-print {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-back {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        /* Certificate Canvas */
        .cert-container {
            width: 1000px;
            height: 700px;
            background: #ffffff;
            position: relative;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        /* Double Border Ornament */
        .cert-border-outer {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 3px solid #198754;
            pointer-events: none;
        }
        .cert-border-inner {
            position: absolute;
            top: 22px;
            left: 22px;
            right: 22px;
            bottom: 22px;
            border: 1px solid #d4af37;
            pointer-events: none;
        }
        .cert-header {
            text-align: center;
            margin-top: 15px;
        }
        .cert-logo {
            font-size: 28px;
            font-weight: bold;
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        .cert-title {
            font-family: 'Cinzel', serif;
            font-size: 24px;
            letter-spacing: 3px;
            color: #1b4332;
            text-transform: uppercase;
            font-weight: 800;
        }
        .cert-subtitle {
            font-size: 12px;
            letter-spacing: 2px;
            color: #856404;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .cert-body {
            text-align: center;
            padding: 0 40px;
        }
        .cert-presented-to {
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
        }
        .cert-recipient {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            color: #198754;
            font-style: italic;
            font-weight: 700;
            border-bottom: 2px solid #d4af37;
            display: inline-block;
            padding: 0 30px 6px;
            margin-bottom: 20px;
        }
        .cert-text {
            font-size: 14px;
            line-height: 1.7;
            color: #495057;
            max-width: 820px;
            margin: 0 auto;
        }
        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 30px 15px;
        }
        .cert-qr {
            text-align: center;
        }
        .cert-qr img {
            width: 85px;
            height: 85px;
            border: 1px solid #e9ecef;
            padding: 4px;
            border-radius: 6px;
        }
        .cert-meta {
            font-family: monospace;
            font-size: 11px;
            color: #6c757d;
            margin-top: 4px;
        }
        .cert-seal {
            text-align: center;
        }
        .seal-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px dashed #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            color: #198754;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 auto 5px;
        }
        .cert-signature {
            text-align: center;
            min-width: 220px;
        }
        .sig-line {
            width: 200px;
            border-bottom: 1px solid #333;
            margin: 0 auto 6px;
        }
        .sig-name {
            font-weight: bold;
            font-size: 14px;
            color: #212529;
        }
        .sig-title {
            font-size: 11px;
            color: #6c757d;
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
        <a href="<?= url('/dashboard/donasi') ?>" class="btn btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <button type="button" class="btn btn-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Sertifikat Kanvas -->
    <div class="cert-container">
        <div class="cert-border-outer"></div>
        <div class="cert-border-inner"></div>

        <!-- Header -->
        <div class="cert-header">
            <div class="cert-logo">
                <span>🌱</span>
                <span>TanamPohon.id</span>
            </div>
            <?php if (($cert['type'] ?? 'donasi') === 'partisipasi'): ?>
                <h1 class="cert-title">Sertifikat Relawan Penanaman Pohon</h1>
                <div class="cert-subtitle">Apresiasi Dedikasi & Aksi Nyata Pelestarian Bumi Nusantara</div>
            <?php else: ?>
                <h1 class="cert-title">Sertifikat Penghargaan Pelestarian Alam</h1>
                <div class="cert-subtitle">Penghijauan & Pemulihan Ekosistem Hutan Nusantara</div>
            <?php endif; ?>
        </div>

        <!-- Body -->
        <div class="cert-body">
            <div class="cert-presented-to">Diberikan dengan penuh rasa hormat & apresiasi kepada:</div>
            <div class="cert-recipient"><?= e($cert['data']['recipient_name'] ?? 'Sahabat Peduli Bumi') ?></div>
            
            <?php if (($cert['type'] ?? 'donasi') === 'partisipasi'): ?>
                <p class="cert-text">
                    Atas kontribusi tenaga, dedikasi, dan partisipasi aktif sebagai <strong>Relawan Aksi Hijau</strong> dalam kegiatan <strong>"<?= e($cert['data']['event_title'] ?? 'Aksi Penanaman Pohon') ?>"</strong> yang dilaksanakan di <?= e($cert['data']['location'] ?? 'Kawasan Konservasi Hutan') ?> pada tanggal <?= format_date($cert['data']['event_date'] ?? $cert['issued_at'], 'd F Y') ?> demi masa depan bumi yang lestari.
                </p>
            <?php else: ?>
                <p class="cert-text">
                    Atas kontribusi dan dedikasi nyata dalam mendonasikan <strong><?= $cert['data']['total_trees'] ?? 1 ?> Batang Pohon</strong> (<?= e($cert['data']['species_summary'] ?? 'Pohon Nusantara') ?>). Pohon ini dirawat secara berkelanjutan di kawasan konservasi hutan dan diperkirakan menyerap <strong><?= number_format($cert['data']['total_co2_kg'] ?? 25, 1) ?> kg CO₂</strong> setiap tahunnya demi memulihkan paru-paru bumi Indonesia.
                </p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="cert-footer">
            <!-- QR Code Verifikasi -->
            <div class="cert-qr">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($cert['data']['verify_url'] ?? url('/verifikasi-sertifikat/' . $cert['certificate_number'])) ?>" alt="QR Code Keaslian">
                <div class="cert-meta"><?= e($cert['certificate_number']) ?></div>
                <div class="cert-meta">Diterbitkan: <?= format_date($cert['issued_at'] ?? $cert['created_at'], 'd M Y') ?></div>
            </div>

            <!-- Stempel Resmi -->
            <div class="cert-seal">
                <div class="seal-circle">
                    Resmi<br>Terverifikasi
                </div>
                <small class="text-muted" style="font-size: 10px;">TanamPohon.id Indonesia</small>
            </div>

            <!-- Tanda Tangan Penyelenggara -->
            <div class="cert-signature">
                <div style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 20px; color: #198754; margin-bottom: 8px;">
                    <?= e($cert['data']['signer_name'] ?? 'Ahmad Hijau') ?>
                </div>
                <div class="sig-line"></div>
                <div class="sig-name"><?= e($cert['data']['signer_name'] ?? 'Ahmad Hijau') ?></div>
                <div class="sig-title"><?= e($cert['data']['signer_title'] ?? 'Direktur Program Penanaman') ?></div>
            </div>
        </div>
    </div>

</body>
</html>
