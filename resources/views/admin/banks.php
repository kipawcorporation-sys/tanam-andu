<?php
/**
 * View: admin/banks.php — Manajemen Rekening Bank & QRIS Pembayaran
 * Platform: Plant For Fun • Kerenceng Coffee Expedition
 */
?>

<style>
/* Header & Hero */
.bank-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px -5px rgba(6, 21, 16, 0.35);
}
.bank-hero::after {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.18) 0%, transparent 70%);
    pointer-events: none;
}
.btn-add-channel {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: 12px;
    font-size: .88rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(34, 197, 94, 0.3);
    transition: all .2s ease;
}
.btn-add-channel:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(34, 197, 94, 0.45);
    color: #fff;
}

/* Metric Stats Cards */
.stat-pill-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    height: 100%;
    transition: transform .15s ease;
}
.stat-pill-card:hover {
    transform: translateY(-2px);
}
.stat-pill-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

/* Atmospheric Debit / ATM Card Visual */
.card-wrapper {
    background: #fff;
    border-radius: 20px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all .2s ease;
}
.card-wrapper:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(18, 56, 44, 0.12);
}
.card-wrapper.is-inactive {
    opacity: 0.72;
    filter: grayscale(0.2);
}

.atm-card-surface {
    position: relative;
    padding: 24px 26px;
    border-radius: 18px 18px 0 0;
    color: #fff;
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}
.atm-card-surface::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 60%);
    pointer-events: none;
}
.atm-card-surface::after {
    content: '';
    position: absolute;
    bottom: -40px;
    left: -30px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
    pointer-events: none;
}

/* Card Themes */
.theme-bca {
    background: linear-gradient(135deg, #091b33 0%, #0d325e 50%, #15559a 100%);
}
.theme-mandiri {
    background: linear-gradient(135deg, #0d2137 0%, #16365a 50%, #854d0e 100%);
}
.theme-bsi {
    background: linear-gradient(135deg, #06392c 0%, #0f5945 50%, #0d9488 100%);
}
.theme-bri {
    background: linear-gradient(135deg, #0f2b5c 0%, #1b4b8a 50%, #2563eb 100%);
}
.theme-bni {
    background: linear-gradient(135deg, #1b3a4b 0%, #27526b 50%, #c2410c 100%);
}
.theme-qris {
    background: linear-gradient(135deg, #450a0a 0%, #881337 55%, #be123c 100%);
}
.theme-ewallet {
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #10b981 100%);
}
.theme-default {
    background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
}

/* EMV Chip and NFC */
.chip-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.emv-chip {
    width: 44px;
    height: 32px;
    background: linear-gradient(135deg, #fcd34d 0%, #d97706 70%, #b45309 100%);
    border-radius: 6px;
    position: relative;
    box-shadow: inset 0 1px 2px rgba(255,255,255,0.4), 0 2px 4px rgba(0,0,0,0.3);
    overflow: hidden;
}
.emv-chip::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(0,0,0,0.25);
}
.emv-chip::after {
    content: '';
    position: absolute;
    left: 45%;
    top: 0;
    bottom: 0;
    width: 1px;
    background: rgba(0,0,0,0.25);
}
.nfc-icon {
    font-size: 1.3rem;
    color: rgba(255,255,255,0.7);
    transform: rotate(90deg);
    display: inline-block;
}

/* Card Number & Copy */
.card-acct-display {
    margin: 18px 0 10px;
}
.card-number-text {
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.28rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: #fff;
    text-shadow: 0 1px 3px rgba(0,0,0,0.4);
    word-break: break-all;
}
.btn-copy-acct {
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    border-radius: 8px;
    padding: 3px 10px;
    font-size: .75rem;
    font-weight: 600;
    transition: all .15s ease;
    cursor: pointer;
}
.btn-copy-acct:hover {
    background: rgba(255,255,255,0.3);
    color: #fff;
}

/* Card Details Body */
.card-meta-panel {
    padding: 18px 22px;
    background: #fff;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.instructions-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: .8rem;
    color: #475569;
    line-height: 1.45;
    margin-bottom: 14px;
}

/* Preset Pill Buttons */
.preset-btn {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #334155;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
}
.preset-btn:hover {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
}

/* QRIS Stand Frame Modal */
.qris-stand-preview {
    background: #ffffff;
    border: 3px solid #0f172a;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}
@media print {
    body * { visibility: hidden !important; }
    #printableQrisArea, #printableQrisArea * { visibility: visible !important; }
    #printableQrisArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
        box-shadow: none;
        border: none;
    }
}
</style>

<!-- Hero Section -->
<div class="bank-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-3 py-1 rounded-pill">
                    <i class="bi bi-shield-check me-1"></i>Kanal Transaksi Resmi
                </span>
                <span class="badge px-3 py-1 rounded-pill" style="background: rgba(245, 158, 11, 0.2); color: #fde68a; border: 1px solid rgba(245, 158, 11, 0.5);">
                    <i class="bi bi-qr-code-scan me-1"></i>QRIS Nasional
                </span>
            </div>
            <h4 class="text-white fw-bold mb-1 fs-3">
                <i class="bi bi-bank2 me-2 text-success"></i>Katalog Rekening & Kanal Pembayaran
            </h4>
            <p class="text-white-50 mb-0 small" style="max-width: 680px;">
                Kelola nomor rekening bank, QRIS Standar Nasional (GPN), dan dompet digital untuk penerimaan transfer donasi bibit pohon & pemesanan kedai.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-add-channel" data-bs-toggle="modal" data-bs-target="#modalTambahBank">
                <i class="bi bi-plus-circle-fill"></i> Tambah Kanal Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- 4 Metrics Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-pill-card">
            <div class="stat-pill-icon bg-success-subtle" style="color:#0a3622;">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Total Kanal</div>
                <div class="fs-4 fw-bold text-dark"><?= $stats['total_accounts'] ?> <small class="text-muted fs-6 fw-normal">Akun</small></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-pill-card">
            <div class="stat-pill-icon bg-primary-subtle text-primary">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Kanal Aktif</div>
                <div class="fs-4 fw-bold text-dark"><?= $stats['active_accounts'] ?> <small class="text-muted fs-6 fw-normal">Kanal</small></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-pill-card">
            <div class="stat-pill-icon" style="background: #fef3c7; color: #b45309;">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Rekening Utama</div>
                <div class="fs-6 fw-bold text-truncate text-dark" style="max-width: 170px;" title="<?= e($stats['primary_bank']['bank_name'] ?? '-') ?>">
                    <?= e($stats['primary_bank']['bank_name'] ?? 'Belum Ditentukan') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-pill-card">
            <div class="stat-pill-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <div class="small text-muted fw-semibold">Dana Terverifikasi Masuk</div>
                <div class="fs-5 fw-bold" style="color:#15803d;">Rp <?= number_format($stats['total_received'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Cards Grid -->
<div class="row g-4 mb-5">
    <?php if (empty($banks)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm p-5 text-center">
                <div class="py-4">
                    <i class="bi bi-bank text-muted" style="font-size: 3.5rem;"></i>
                    <h5 class="fw-bold mt-3 mb-1">Belum Ada Rekening atau QRIS Terdaftar</h5>
                    <p class="text-muted small">Tambahkan kanal pembayaran pertama Anda agar donatur dan pelanggan dapat mentransfer dana.</p>
                    <button class="btn btn-success rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahBank">
                        <i class="bi bi-plus-lg me-1"></i> Buat Kanal Sekarang
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($banks as $bank): ?>
        <?php
            // Deteksi styling tema kartu ATM
            $bName = strtolower($bank['bank_name']);
            $themeClass = 'theme-default';
            if ($bank['channel_type'] === 'qris') {
                $themeClass = 'theme-qris';
            } elseif (str_contains($bName, 'bca')) {
                $themeClass = 'theme-bca';
            } elseif (str_contains($bName, 'mandiri')) {
                $themeClass = 'theme-mandiri';
            } elseif (str_contains($bName, 'bsi') || str_contains($bName, 'syariah')) {
                $themeClass = 'theme-bsi';
            } elseif (str_contains($bName, 'bri')) {
                $themeClass = 'theme-bri';
            } elseif (str_contains($bName, 'bni')) {
                $themeClass = 'theme-bni';
            } elseif ($bank['channel_type'] === 'e_wallet') {
                $themeClass = 'theme-ewallet';
            }
        ?>
        <div class="col-md-6 col-xl-6">
            <div class="card-wrapper <?= (int)$bank['is_active'] === 0 ? 'is-inactive' : '' ?>">
                <!-- Atmospheric ATM / Debit Card Visual -->
                <div class="atm-card-surface <?= $themeClass ?>">
                    <!-- Top Row -->
                    <div class="d-flex justify-content-between align-items-start position-relative z-1">
                        <div class="chip-group">
                            <div class="emv-chip"></div>
                            <i class="bi bi-wifi nfc-icon"></i>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold fs-6 text-white text-uppercase tracking-wider">
                                <?= e($bank['bank_name']) ?>
                            </div>
                            <div class="d-flex align-items-center justify-content-end gap-1 mt-1">
                                <?php if (!empty($bank['is_primary'])): ?>
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1 shadow-sm">
                                        <i class="bi bi-star-fill me-1"></i>UTAMA
                                    </span>
                                <?php endif; ?>
                                <?php if ($bank['channel_type'] === 'qris'): ?>
                                    <span class="badge bg-danger text-white fw-bold px-2 py-1">
                                        <i class="bi bi-qr-code me-1"></i>QRIS GPN
                                    </span>
                                <?php elseif ($bank['channel_type'] === 'e_wallet'): ?>
                                    <span class="badge bg-success text-white fw-bold px-2 py-1">
                                        <i class="bi bi-wallet2 me-1"></i>E-WALLET
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light bg-opacity-25 text-white fw-semibold px-2 py-1">
                                        BANK TRANSFER
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Row: Account Number -->
                    <div class="card-acct-display position-relative z-1">
                        <div class="small text-white-50 text-uppercase fw-semibold mb-1" style="font-size: .68rem; letter-spacing: .08em;">
                            <?= $bank['channel_type'] === 'qris' ? 'National Merchant ID (NMID)' : 'Nomor Rekening' ?>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span class="card-number-text">
                                <?= chunk_split(e($bank['account_number']), 4, ' ') ?>
                            </span>
                            <button type="button" class="btn-copy-acct" onclick="copyAcct('<?= e($bank['account_number']) ?>', this)">
                                <i class="bi bi-copy me-1"></i> Salin
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Row: Holder & Status -->
                    <div class="d-flex justify-content-between align-items-end position-relative z-1 mt-2">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-semibold" style="font-size: .65rem; letter-spacing: .08em;">
                                Atas Nama
                            </div>
                            <div class="fw-bold text-white text-uppercase" style="font-size: .88rem; letter-spacing: .04em;">
                                <?= e($bank['account_holder']) ?>
                            </div>
                            <?php if (!empty($bank['branch'])): ?>
                                <div class="text-white-50 small" style="font-size: .72rem;">
                                    <i class="bi bi-geo-alt me-1"></i><?= e($bank['branch']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <?php if ((int)$bank['is_active'] === 1): ?>
                                <span class="badge bg-success bg-opacity-75 text-white border border-success border-opacity-50 px-2 py-1 rounded-pill small">
                                    <i class="bi bi-circle-fill text-white me-1" style="font-size: 6px;"></i>Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary text-white px-2 py-1 rounded-pill small">
                                    Nonaktif
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Meta Details & Actions Bar -->
                <div class="card-meta-panel">
                    <div>
                        <!-- Instructions Box -->
                        <?php if (!empty($bank['instructions'])): ?>
                            <div class="instructions-box">
                                <div class="fw-bold text-dark mb-1 small">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>Petunjuk Transfer:
                                </div>
                                <div class="small text-muted"><?= nl2br(e($bank['instructions'])) ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Stats Bar -->
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                            <span class="small text-muted">
                                <i class="bi bi-receipt me-1"></i><?= (int)$bank['approved_count'] ?> Transaksi Berhasil
                            </span>
                            <span class="small fw-bold text-success">
                                Total Rp <?= number_format($bank['total_received'], 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1">
                        <div class="d-flex gap-2">
                            <!-- Tombol Set Utama -->
                            <?php if (empty($bank['is_primary'])): ?>
                                <form action="/admin/rekening/<?= $bank['id'] ?>/set-primary" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm fw-semibold" style="color: #92400e; border: 1px solid #f59e0b; background: #fffbeb;" title="Jadikan Rekening Utama">
                                        <i class="bi bi-star-fill me-1" style="color: #d97706;"></i>Set Utama
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- Tombol Lihat QRIS -->
                            <?php if (!empty($bank['qris_image_url'])): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold"
                                        onclick="showQrisModal('<?= e($bank['bank_name']) ?>', '<?= e($bank['account_number']) ?>', '<?= e($bank['account_holder']) ?>', '<?= e($bank['qris_image_url']) ?>')">
                                    <i class="bi bi-qr-code-scan me-1"></i>QRIS Barcode
                                </button>
                            <?php endif; ?>

                            <!-- Toggle Aktif / Nonaktif -->
                            <form action="/admin/rekening/<?= $bank['id'] ?>/toggle" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm <?= (int)$bank['is_active'] === 1 ? 'btn-outline-secondary' : 'btn-outline-success' ?> fw-semibold"
                                        title="<?= (int)$bank['is_active'] === 1 ? 'Nonaktifkan kanal ini' : 'Aktifkan kanal ini' ?>">
                                    <i class="bi bi-power me-1"></i><?= (int)$bank['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>
                        </div>

                        <div class="d-flex gap-1">
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-sm btn-light border fw-semibold text-primary btn-edit-bank"
                                    data-id="<?= $bank['id'] ?>"
                                    data-name="<?= e($bank['bank_name']) ?>"
                                    data-channel="<?= e($bank['channel_type']) ?>"
                                    data-number="<?= e($bank['account_number']) ?>"
                                    data-holder="<?= e($bank['account_holder']) ?>"
                                    data-branch="<?= e($bank['branch'] ?? '') ?>"
                                    data-instructions="<?= e($bank['instructions'] ?? '') ?>"
                                    data-sort="<?= (int)$bank['sort_order'] ?>"
                                    data-primary="<?= (int)$bank['is_primary'] ?>"
                                    data-active="<?= (int)$bank['is_active'] ?>"
                                    data-qris="<?= e($bank['qris_image_url'] ?? '') ?>">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>

                            <!-- Delete Button -->
                            <form action="/admin/rekening/<?= $bank['id'] ?>/hapus" method="POST" class="d-inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kanal <?= e($bank['bank_name']) ?>? Data yang sudah pernah menerima transaksi tidak bisa dihapus.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-light border text-danger fw-semibold" title="Hapus kanal">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ======================================================================= -->
<!-- MODAL TAMBAH REKENING / QRIS                                             -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalTambahBank" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form action="/admin/rekening/simpan" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #061510, #12382c); padding: 20px 28px;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">
                            <i class="bi bi-plus-circle-fill me-2 text-success"></i>Tambah Kanal Pembayaran Baru
                        </h5>
                        <p class="small text-white-50 mb-0">Lengkapi nomor rekening bank, kanal QRIS, atau akun dompet digital resmi.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Quick Presets -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted mb-2">Preset Cepat (Klik untuk mengisi otomatis):</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="preset-btn" onclick="applyPreset('BCA', 'bank_transfer', 'Bank Central Asia (BCA)', 'Yayasan Tanam Pohon andu', 'KCU Asia Afrika Bandung', 'Transfer via ATM, BCA Mobile, atau KlikBCA.')">BCA</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('Mandiri', 'bank_transfer', 'Bank Mandiri', 'Yayasan Konservasi Agroforestry Kerenceng', 'KCP Bandung Cicalengka', 'Transfer via Livin\' by Mandiri, ATM Mandiri, atau BI-Fast.')">Mandiri</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('BSI', 'bank_transfer', 'Bank Syariah Indonesia (BSI)', 'Tanam Pohon Karsa Kerentjeng', 'KC Bandung Dago', 'Transfer syariah via BSI Mobile atau ATM Bersama.')">BSI</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('BRI', 'bank_transfer', 'Bank Rakyat Indonesia (BRI)', 'Plant For Fun Kerenceng', 'KCP Rancaekek', 'Transfer via BRImo atau ATM BRI.')">BRI</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('BNI', 'bank_transfer', 'Bank Negara Indonesia (BNI)', 'Plant For Fun Kerenceng', 'KC Bandung', 'Transfer via BNI Mobile Banking.')">BNI</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('QRIS', 'qris', 'QRIS Standar Nasional (GPN)', 'PLANT FOR FUN - ANDU COFFEE LAB', 'NMID: ID102003004005 (Semua Bank & E-Wallet)', 'Scan menggunakan aplikasi m-Banking atau e-Wallet favorit Anda.')">QRIS GPN</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('GoPay', 'e_wallet', 'GoPay Merchant', 'andu Coffee Lab', 'Official Merchant QR / Wallet', 'Transfer langsung ke nomor GoPay Merchant atau scan kode QR.')">GoPay</button>
                            <button type="button" class="preset-btn" onclick="applyPreset('DANA', 'e_wallet', 'DANA Bisnis', 'andu Coffee Lab', 'Official DANA Bisnis', 'Kirim ke akun DANA Bisnis resmi yayasan.')">DANA</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-dark">Nama Bank / Kanal *</label>
                            <input type="text" name="bank_name" id="add_bank_name" class="form-control" placeholder="Contoh: Bank Central Asia (BCA)" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-dark">Tipe Kanal *</label>
                            <select name="channel_type" id="add_channel_type" class="form-select" required onchange="toggleQrisUpload('add')">
                                <option value="bank_transfer">Transfer Bank Konvensional/Syariah</option>
                                <option value="qris">QRIS Standar Pembayaran Nasional</option>
                                <option value="e_wallet">Dompet Digital (E-Wallet)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark" id="add_label_number">Nomor Rekening / NMID *</label>
                            <input type="text" name="account_number" id="add_account_number" class="form-control font-monospace" placeholder="1390-8821-44 atau ID102003004005" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Nama Pemilik Rekening (a.n) *</label>
                            <input type="text" name="account_holder" id="add_account_holder" class="form-control" placeholder="Contoh: Yayasan Tanam Pohon andu" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Kantor Cabang / Keterangan Lokasi</label>
                            <input type="text" name="branch" id="add_branch" class="form-control" placeholder="Contoh: KCU Asia Afrika Bandung / NMID: ID102003004005">
                        </div>

                        <div class="col-12" id="add_qris_container" style="display: none;">
                            <label class="form-label fw-bold small text-dark">Unggah Berkas Gambar Barcode QRIS</label>
                            <input type="file" name="qris_image" id="add_qris_image" class="form-control" accept="image/png,image/jpeg,image/webp" onchange="previewUploadImage(this, 'add_qris_preview')">
                            <div class="form-text small">Mendukung format PNG, JPG, WEBP. Barcode ini akan ditampilkan langsung kepada donatur / pelanggan.</div>
                            <div id="add_qris_preview_container" class="mt-2" style="display: none;">
                                <img id="add_qris_preview" src="" alt="Preview QRIS" class="img-thumbnail" style="max-height: 180px;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Petunjuk Pembayaran / Catatan</label>
                            <textarea name="instructions" id="add_instructions" rows="2" class="form-control" placeholder="Petunjuk khusus seperti 'Tambahkan kode unik 3 digit terakhir...'"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Urutan Tampil (Sort Order)</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="pt-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_primary" id="add_is_primary" value="1">
                                    <label class="form-check-label fw-bold small" for="add_is_primary">
                                        ⭐ Jadikan Rekening Utama
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1" checked>
                                    <label class="form-check-label fw-bold small" for="add_is_active">
                                        Aktifkan Sekarang
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-light border px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Simpan Kanal Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL EDIT REKENING / QRIS                                               -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalEditBank" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <form id="formEditBank" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #061510, #12382c); padding: 20px 28px;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">
                            <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Kanal Pembayaran
                        </h5>
                        <p class="small text-white-50 mb-0">Perbarui rincian rekening, informasi cabang, instruksi, atau barcode QRIS.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-dark">Nama Bank / Kanal *</label>
                            <input type="text" name="bank_name" id="edit_bank_name" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-dark">Tipe Kanal *</label>
                            <select name="channel_type" id="edit_channel_type" class="form-select" required onchange="toggleQrisUpload('edit')">
                                <option value="bank_transfer">Transfer Bank Konvensional/Syariah</option>
                                <option value="qris">QRIS Standar Pembayaran Nasional</option>
                                <option value="e_wallet">Dompet Digital (E-Wallet)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Nomor Rekening / NMID *</label>
                            <input type="text" name="account_number" id="edit_account_number" class="form-control font-monospace" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Nama Pemilik Rekening (a.n) *</label>
                            <input type="text" name="account_holder" id="edit_account_holder" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Kantor Cabang / Keterangan Lokasi</label>
                            <input type="text" name="branch" id="edit_branch" class="form-control">
                        </div>

                        <div class="col-12" id="edit_qris_container" style="display: none;">
                            <label class="form-label fw-bold small text-dark">Ganti Berkas Barcode QRIS</label>
                            <input type="file" name="qris_image" id="edit_qris_image" class="form-control" accept="image/png,image/jpeg,image/webp" onchange="previewUploadImage(this, 'edit_qris_preview')">
                            <div class="form-text small">Biarkan kosong jika tidak ingin mengubah barcode QRIS yang tersimpan.</div>
                            <div id="edit_qris_preview_container" class="mt-2" style="display: none;">
                                <img id="edit_qris_preview" src="" alt="Preview QRIS" class="img-thumbnail" style="max-height: 180px;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Petunjuk Pembayaran / Catatan</label>
                            <textarea name="instructions" id="edit_instructions" rows="2" class="form-control"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Urutan Tampil (Sort Order)</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="pt-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_primary" id="edit_is_primary" value="1">
                                    <label class="form-check-label fw-bold small" for="edit_is_primary">
                                        ⭐ Jadikan Rekening Utama
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                    <label class="form-check-label fw-bold small" for="edit_is_active">
                                        Kanal Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-light border px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL QRIS BARCODE PRINT & INSPECT                                      -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalLihatQris" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold" id="qrisModalTitle">
                    <i class="bi bi-qr-code-scan me-2 text-danger"></i>Barcode Stand QRIS Kedai
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <!-- Printable Container -->
                <div id="printableQrisArea" class="qris-stand-preview">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        <span class="badge bg-danger px-3 py-1 fs-6 fw-bold">QRIS</span>
                        <span class="small fw-bold text-dark">STANDAR PEMBAYARAN NASIONAL</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-0" id="qrisModalMerchant">PLANT FOR FUN - ANDU COFFEE LAB</h5>
                    <div class="small text-muted font-monospace mb-3" id="qrisModalNmid">NMID: ID102003004005</div>

                    <div class="p-3 bg-light rounded-3 d-inline-block border">
                        <img id="qrisModalImage" src="" alt="QRIS Code" class="img-fluid" style="max-height: 280px; border-radius: 8px;">
                    </div>

                    <div class="mt-3 small text-muted">
                        Mendukung seluruh m-Banking (BCA, Mandiri, BRI, BNI, BSI) & dompet digital (GoPay, OVO, Dana, ShopeePay).
                    </div>
                    <div class="small text-success fw-bold mt-1">
                        Basecamp & Kedai andu Kerenceng • Plant For Fun
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 justify-content-between">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <a id="qrisDownloadBtn" href="" download="QRIS_Stand_PlantForFun.png" class="btn btn-outline-dark px-3 fw-semibold">
                        <i class="bi bi-download me-1"></i> Unduh Gambar
                    </a>
                    <button type="button" class="btn btn-success px-4 fw-bold" onclick="printQris()">
                        <i class="bi bi-printer me-1"></i> Cetak Meja Kedai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Feedback Salin -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
    <div id="copyToast" class="toast align-items-center text-bg-dark border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="bi bi-check-circle-fill text-success me-2"></i> Nomor rekening berhasil disalin!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
// Fungsi Salin Nomor Rekening
function copyAcct(text, btnElement) {
    navigator.clipboard.writeText(text).then(() => {
        const toastEl = document.getElementById('copyToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
        toast.show();

        const originalText = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="bi bi-check2 me-1"></i> Tersalin';
        btnElement.classList.add('bg-success', 'border-success');
        setTimeout(() => {
            btnElement.innerHTML = originalText;
            btnElement.classList.remove('bg-success', 'border-success');
        }, 1500);
    });
}

// Preset Cepat Tambah Rekening
function applyPreset(shortName, channelType, bankName, holder, branch, instructions) {
    document.getElementById('add_bank_name').value = bankName;
    document.getElementById('add_channel_type').value = channelType;
    document.getElementById('add_account_holder').value = holder;
    document.getElementById('add_branch').value = branch;
    document.getElementById('add_instructions').value = instructions;
    toggleQrisUpload('add');

    if (channelType === 'qris') {
        document.getElementById('add_account_number').value = 'ID102003004005';
    } else {
        document.getElementById('add_account_number').focus();
    }
}

// Tampilkan / Sembunyikan Input QRIS berdasarkan Tipe Kanal
function toggleQrisUpload(prefix) {
    const channelSelect = document.getElementById(prefix + '_channel_type');
    const container = document.getElementById(prefix + '_qris_container');
    if (channelSelect.value === 'qris') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

// Preview Upload Gambar
function previewUploadImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.src = e.target.result;
            document.getElementById(previewId + '_container').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Setup Event Listener Tombol Edit
document.querySelectorAll('.btn-edit-bank').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = this.dataset;
        document.getElementById('formEditBank').action = '/admin/rekening/' + d.id + '/update';
        document.getElementById('edit_bank_name').value = d.name;
        document.getElementById('edit_channel_type').value = d.channel;
        document.getElementById('edit_account_number').value = d.number;
        document.getElementById('edit_account_holder').value = d.holder;
        document.getElementById('edit_branch').value = d.branch;
        document.getElementById('edit_instructions').value = d.instructions;
        document.getElementById('edit_sort_order').value = d.sort;
        document.getElementById('edit_is_primary').checked = (d.primary === '1');
        document.getElementById('edit_is_active').checked = (d.active === '1');

        toggleQrisUpload('edit');

        if (d.qris) {
            document.getElementById('edit_qris_preview').src = d.qris;
            document.getElementById('edit_qris_preview_container').style.display = 'block';
        } else {
            document.getElementById('edit_qris_preview_container').style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('modalEditBank')).show();
    });
});

// Modal Tampilkan QRIS Barcode
function showQrisModal(bankName, number, holder, imageUrl) {
    document.getElementById('qrisModalMerchant').innerText = holder;
    document.getElementById('qrisModalNmid').innerText = 'NMID: ' + number;
    document.getElementById('qrisModalImage').src = imageUrl;
    document.getElementById('qrisDownloadBtn').href = imageUrl;

    new bootstrap.Modal(document.getElementById('modalLihatQris')).show();
}

// Cetak Barcode Meja Kedai
function printQris() {
    window.print();
}
</script>
