<?php
/**
 * View: admin/payment_detail.php — Plant For Fun
 * Detail transaksi & verifikasi transfer bank profesional.
 */
$p = $payment;
$statusLabels = [
    'menunggu_pembayaran'  => ['Menunggu Pembayaran', 'status-warning',  'bi-clock-history'],
    'menunggu_verifikasi'  => ['Menunggu Verifikasi', 'status-info',     'bi-hourglass-split'],
    'berhasil'             => ['Berhasil / Lunas',    'status-success',  'bi-check-circle-fill'],
    'ditolak'              => ['Ditolak',             'status-danger',   'bi-x-circle-fill'],
    'kedaluwarsa'          => ['Kedaluwarsa',         'status-muted',    'bi-slash-circle'],
    'dibatalkan'           => ['Dibatalkan',          'status-muted',    'bi-dash-circle'],
];
$stLabel = $statusLabels[$p['status']] ?? [$p['status'], 'status-muted', 'bi-dash'];

$diff = !empty($p['amount_transferred']) ? ((float)$p['amount_transferred'] - (float)$p['grand_total']) : 0;
$isMatch = (!empty($p['amount_transferred']) && abs($diff) < 1);

// WhatsApp URL
$waText = "Halo Kak " . ($p['payer_name'] ?? 'Donatur') . ", kami dari tim *Plant For Fun* mengonfirmasi transaksi invoice *" . $p['invoice_no'] . "* senilai *Rp " . number_format($p['grand_total'], 0, ',', '.') . "*.";
$waUrl = !empty($p['payer_phone']) ? "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $p['payer_phone']) . "&text=" . urlencode($waText) : null;
?>

<style>
.breadcrumb-modern { display:flex; align-items:center; gap:8px; margin-bottom:20px; }
.breadcrumb-modern a { color:#22c55e; text-decoration:none; font-size:.84rem; font-weight:600; display:flex; align-items:center; gap:5px; }
.breadcrumb-modern a:hover { color:#16a34a; }
.breadcrumb-modern span { color:#94a3b8; font-size:.84rem; }
.breadcrumb-modern .current { color:#374151; font-size:.84rem; font-weight:600; }

.page-hero-detail { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 24px 32px; margin-bottom: 24px; position: relative; overflow: hidden; color: #fff; }
.page-hero-detail::before { content:''; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background:radial-gradient(circle, rgba(34,197,94,.15) 0%, transparent 70%); pointer-events:none; }

.pf-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; margin-bottom:24px; }
.pf-card-header { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); padding:16px 20px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.pf-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.9rem; display:flex; align-items:center; gap:7px; }
.pf-card-body { padding:24px; }

.detail-row { display:flex; padding:10px 0; border-bottom:1px solid #f8fafc; gap:16px; align-items:flex-start; }
.detail-row:last-child { border-bottom:none; }
.detail-label { width:150px; flex-shrink:0; font-size:.78rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; }
.detail-value { font-size:.88rem; color:#374151; flex:1; }

.status-pill { display:inline-flex; align-items:center; gap:5px; padding:5px 14px; border-radius:50px; font-size:.76rem; font-weight:700; }
.status-success { background:#dcfce7; color:#15803d; }
.status-warning { background:#fef9c3; color:#a16207; }
.status-info    { background:#dbeafe; color:#1d4ed8; }
.status-danger  { background:#fee2e2; color:#b91c1c; }
.status-muted   { background:#f1f5f9; color:#64748b; }

.proof-img-box { background:#0f172a; border-radius:12px; overflow:hidden; position:relative; max-height:480px; display:flex; align-items:center; justify-content:center; }
.proof-img-box img { max-width:100%; max-height:460px; object-fit:contain; }

.btn-approve-lg { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; padding:12px 24px; border-radius:12px; font-size:.92rem; font-weight:800; cursor:pointer; width:100%; transition:.2s; display:flex; align-items:center; justify-content:center; gap:8px; }
.btn-approve-lg:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(34,197,94,.35); color:#fff; }
.btn-reject-lg { background:#fff; color:#ef4444; border:2px solid #fca5a5; padding:12px 24px; border-radius:12px; font-size:.92rem; font-weight:800; cursor:pointer; width:100%; transition:.2s; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:8px; }
.btn-reject-lg:hover { background:#fee2e2; border-color:#ef4444; }

.dup-alert { background:linear-gradient(135deg,#fee2e2,#fecaca); border:2px solid #fca5a5; border-radius:14px; padding:16px 20px; margin-bottom:20px; display:flex; gap:12px; align-items:flex-start; color:#991b1b; }
.dup-alert i { font-size:1.4rem; color:#dc2626; flex-shrink:0; }
</style>

<!-- Breadcrumb Modern -->
<div class="breadcrumb-modern">
    <a href="/admin/verifikasi-pembayaran"><i class="bi bi-arrow-left-circle"></i>Antrian Verifikasi Pembayaran</a>
    <span>/</span>
    <span class="current">Detail Invoice #<?= htmlspecialchars($p['invoice_no']) ?></span>
</div>

<!-- Header Hero Detail -->
<div class="page-hero-detail">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-dark font-mono px-3 py-1 fw-bold"><?= htmlspecialchars($p['invoice_no']) ?></span>
                <span class="status-pill <?= $stLabel[1] ?>"><i class="bi <?= $stLabel[2] ?>"></i><?= $stLabel[0] ?></span>
            </div>
            <h4 class="fw-bold mb-1">
                <?= match($p['payable_type']) {
                    'donation'    => 'Donasi Penanaman Pohon Konservasi',
                    'order'       => 'Pemesanan Kopi & Merchandise',
                    'participant' => 'Pendaftaran Peserta Ekspedisi',
                    default       => 'Transaksi Pembayaran',
                } ?>
            </h4>
            <div class="text-white-50 small">
                Dibuat: <?= date('d M Y, H:i', strtotime($p['created_at'])) ?> WIB &bull; Batas Kedaluwarsa: <?= !empty($p['expires_at']) ? date('d M Y, H:i', strtotime($p['expires_at'])) . ' WIB' : '—' ?>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-light btn-sm px-3 rounded-pill" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Cetak Rincian
            </button>
            <a href="/admin/verifikasi-pembayaran" class="btn btn-success btn-sm px-4 rounded-pill fw-bold">
                Kembali ke Antrian
            </a>
        </div>
    </div>
</div>

<?php if ($isDuplicateHash): ?>
    <div class="dup-alert">
        <i class="bi bi-shield-exclamation"></i>
        <div>
            <strong>⚠ Peringatan Anti-Fraud: File Bukti Duplikat Terdeteksi!</strong><br>
            File bukti transfer ini memiliki *checksum SHA-256* yang sama persis dengan transaksi lain di sistem. Kemungkinan bukti transfer digunakan ulang atau direkayasa. Mohon lakukan pengecekan mutasi bank secara manual sebelum menyetujui.
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Kolom Kiri: Rincian Tagihan Sistem & Pembeli -->
    <div class="col-lg-6">
        <!-- Rincian Tagihan Sistem -->
        <div class="pf-card">
            <div class="pf-card-header">
                <h6><i class="bi bi-receipt text-success"></i>Informasi Tagihan Sistem</h6>
                <span class="badge bg-light text-dark font-mono border">ID #<?= $p['id'] ?></span>
            </div>
            <div class="pf-card-body">
                <div class="detail-row">
                    <div class="detail-label">Nomor Invoice</div>
                    <div class="detail-value"><code class="fw-bold fs-6 text-primary"><?= htmlspecialchars($p['invoice_no']) ?></code></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tipe Transaksi</div>
                    <div class="detail-value fw-semibold text-uppercase font-mono">
                        <?= htmlspecialchars($p['payable_type']) ?> #<?= $p['payable_id'] ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Subtotal Tagihan</div>
                    <div class="detail-value font-mono">Rp <?= number_format($p['total_amount'] ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Kode Unik Transfer</div>
                    <div class="detail-value font-mono text-warning-emphasis fw-bold">+<?= $p['unique_code'] ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Tagihan (Grand Total)</div>
                    <div class="detail-value">
                        <span class="fw-bold fs-5 font-mono text-success">
                            Rp <?= number_format($p['grand_total'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>

                <!-- Informasi Entitas Terkait -->
                <?php if (!empty($relatedData)): ?>
                    <div class="mt-4 pt-3 border-top">
                        <span class="small fw-bold text-muted text-uppercase font-mono d-block mb-2">Item Terkait Transaksi:</span>
                        <div class="p-3 rounded-3 bg-light border small">
                            <?php if ($p['payable_type'] === 'donation'): ?>
                                <div><i class="bi bi-tree text-success me-1"></i><strong>Jumlah Pohon:</strong> <?= $relatedData['total_trees'] ?? 0 ?> Bibit Pohon</div>
                                <div><i class="bi bi-flower1 text-success me-1"></i><strong>Spesies:</strong> <?= htmlspecialchars($relatedData['species_name'] ?? 'Pohon Naungan Kerenceng') ?></div>
                                <?php if (!empty($relatedData['is_gift'])): ?>
                                    <div class="text-danger mt-1"><i class="bi bi-gift me-1"></i>Hadiah untuk: <?= htmlspecialchars($relatedData['gift_recipient_name'] ?? '') ?></div>
                                <?php endif; ?>
                            <?php elseif ($p['payable_type'] === 'order'): ?>
                                <div><i class="bi bi-bag text-success me-1"></i><strong>Pesanan Kaos/Kopi:</strong> <?= htmlspecialchars($relatedData['items_summary'] ?? 'Item Toko') ?></div>
                                <div><i class="bi bi-truck text-success me-1"></i><strong>Metode Kirim:</strong> <?= htmlspecialchars($relatedData['shipping_method'] ?? 'Kurir') ?></div>
                            <?php elseif ($p['payable_type'] === 'participant'): ?>
                                <div><i class="bi bi-calendar-event text-success me-1"></i><strong>Kegiatan:</strong> <?= htmlspecialchars($relatedData['event_title'] ?? '') ?></div>
                                <div><i class="bi bi-shirt text-success me-1"></i><strong>Ukuran Kaos:</strong> <?= htmlspecialchars($relatedData['tshirt_size'] ?? '—') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Data Pembeli / Donatur -->
        <div class="pf-card">
            <div class="pf-card-header">
                <h6><i class="bi bi-person text-primary"></i>Data Pembayar / Donatur</h6>
                <?php if (!empty($waUrl)): ?>
                    <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                        <i class="bi bi-whatsapp me-1"></i>Chat WhatsApp
                    </a>
                <?php endif; ?>
            </div>
            <div class="pf-card-body">
                <div class="detail-row">
                    <div class="detail-label">Nama Akun</div>
                    <div class="detail-value fw-bold text-dark"><?= htmlspecialchars($p['payer_name']) ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value text-muted"><?= htmlspecialchars($p['payer_email'] ?? '—') ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Nomor WhatsApp</div>
                    <div class="detail-value">
                        <?php if (!empty($p['payer_phone'])): ?>
                            <a href="<?= $waUrl ?>" target="_blank" class="text-success fw-bold text-decoration-none font-mono">
                                <i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($p['payer_phone']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Bukti Transfer & Aksi Verifikasi -->
    <div class="col-lg-6">
        <!-- Komparasi Bukti Transfer Rekening -->
        <div class="pf-card">
            <div class="pf-card-header">
                <h6><i class="bi bi-image text-success"></i>Bukti Transfer &amp; Klaim Pengirim</h6>
                <?php if ($isMatch): ?>
                    <span class="badge bg-success text-white font-mono px-3 py-1 rounded-pill"><i class="bi bi-check2"></i> NOMINAL MATCH</span>
                <?php elseif (!empty($p['amount_transferred'])): ?>
                    <span class="badge bg-warning text-dark font-mono px-3 py-1 rounded-pill"><i class="bi bi-exclamation-triangle"></i> SELISIH Rp <?= number_format($diff, 0, ',', '.') ?></span>
                <?php endif; ?>
            </div>
            <div class="pf-card-body">
                <?php if (!empty($p['proof_file'])): ?>
                    <!-- Pratinjau Foto Bukti -->
                    <div class="proof-img-box mb-4">
                        <img src="<?= htmlspecialchars($p['proof_url']) ?>" alt="Bukti Transfer">
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= htmlspecialchars($p['proof_url']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Buka Gambar Asli (Resolusi Penuh)
                        </a>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Bank Pengirim</div>
                        <div class="detail-value fw-bold text-dark"><?= htmlspecialchars($p['sender_bank']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Nama Pemilik Rekening</div>
                        <div class="detail-value fw-bold text-dark"><?= htmlspecialchars($p['sender_name']) ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Nominal Ditransfer</div>
                        <div class="detail-value font-mono fw-bold fs-6 text-success">
                            Rp <?= number_format($p['amount_transferred'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Waktu Transfer</div>
                        <div class="detail-value"><?= date('d M Y', strtotime($p['transfer_date'])) ?> <?= !empty($p['transfer_time']) ? 'Pukul ' . date('H:i', strtotime($p['transfer_time'])) . ' WIB' : '' ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Catatan Pengirim</div>
                        <div class="detail-value text-secondary fst-italic"><?= htmlspecialchars($p['proof_note'] ?? 'Tidak ada catatan') ?></div>
                    </div>
                    <?php if (!empty($p['file_hash'])): ?>
                        <div class="detail-row">
                            <div class="detail-label">File SHA-256</div>
                            <div class="detail-value font-mono small text-muted text-break" style="font-size:0.7rem;"><?= htmlspecialchars($p['file_hash']) ?></div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-hourglass-bottom display-4 d-block mb-3 text-muted opacity-50"></i>
                        <div class="fw-semibold">Pembeli belum mengunggah bukti transfer</div>
                        <small>Data rekening dan foto resi transfer akan tampil otomatis di sini setelah pembeli konfirmasi.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tombol Aksi Verifikasi -->
        <?php if ($p['status'] === 'menunggu_verifikasi'): ?>
            <div class="pf-card p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-check-circle text-success me-2"></i>Eksekusi Verifikasi Pembayaran</h6>
                
                <form action="/admin/verifikasi-pembayaran/<?= $p['id'] ?>/setujui" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-approve-lg" onclick="return confirm('Apakah Anda yakin data transfer ini sudah valid dan masuk ke mutasi rekening?')">
                        <i class="bi bi-check-circle-fill"></i>Setujui Pembayaran Sekarang
                    </button>
                </form>

                <button type="button" class="btn-reject-lg" data-bs-toggle="modal" data-bs-target="#modalTolakDetail">
                    <i class="bi bi-x-circle-fill"></i>Tolak Pembayaran Ini
                </button>
            </div>
        <?php elseif ($p['status'] === 'berhasil'): ?>
            <div class="alert alert-success rounded-4 p-4 d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill fs-2"></i>
                <div>
                    <h6 class="fw-bold mb-1">Transaksi Telah Diverifikasi &amp; Lunas</h6>
                    <div class="small text-success-emphasis">
                        Diverifikasi oleh: <strong><?= htmlspecialchars($p['verifier_name'] ?? 'Admin') ?></strong>
                        <?php if (!empty($p['verified_at'])): ?>
                            pada <?= date('d M Y, H:i', strtotime($p['verified_at'])) ?> WIB
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php elseif ($p['status'] === 'ditolak'): ?>
            <div class="alert alert-danger rounded-4 p-4">
                <h6 class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i>Pembayaran Ini Telah Ditolak</h6>
                <div class="small mt-2">
                    <strong>Alasan Penolakan:</strong> <?= htmlspecialchars($p['rejected_reason'] ?? 'Tidak memenuhi syarat transfer.') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tolak Pembayaran Detail -->
<div class="modal fade" id="modalTolakDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="/admin/verifikasi-pembayaran/<?= $p['id'] ?>/tolak" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <label class="form-label fw-bold small text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="4" required minlength="10"
                              placeholder="Misal: Bukti transfer tidak terbaca / terpotong, nominal kurang Rp 500, dana belum masuk ke mutasi..."
                              style="border-radius:10px;"></textarea>
                    <div class="form-text small mt-1">Alasan penolakan akan dikirimkan kepada pembeli. Minimal 10 karakter.</div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i>Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
