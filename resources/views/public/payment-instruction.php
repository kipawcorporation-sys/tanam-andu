<?php
/**
 * View: Instruksi Pembayaran Manual & Konfirmasi Transfer
 */
$statusColors = [
    'menunggu_pembayaran' => ['badge' => 'bg-warning text-dark', 'label' => 'Menunggu Pembayaran', 'icon' => 'bi-clock-history'],
    'menunggu_verifikasi' => ['badge' => 'bg-info text-dark',    'label' => 'Menunggu Verifikasi Admin', 'icon' => 'bi-hourglass-split'],
    'berhasil'            => ['badge' => 'bg-success text-white', 'label' => 'Pembayaran Berhasil', 'icon' => 'bi-check-circle-fill'],
    'ditolak'             => ['badge' => 'bg-danger text-white',  'label' => 'Bukti Ditolak', 'icon' => 'bi-x-circle-fill'],
    'kedaluwarsa'         => ['badge' => 'bg-secondary text-white', 'label' => 'Tagihan Kedaluwarsa', 'icon' => 'bi-calendar-x'],
    'dibatalkan'          => ['badge' => 'bg-dark text-white',     'label' => 'Tagihan Dibatalkan', 'icon' => 'bi-slash-circle'],
];

$currStatus = $statusColors[$payment['status']] ?? ['badge' => 'bg-secondary', 'label' => ucfirst($payment['status']), 'icon' => 'bi-info-circle'];
$isPayable = in_array($payment['status'], ['menunggu_pembayaran', 'menunggu_verifikasi', 'ditolak']);
$lastProof = !empty($proofs) ? $proofs[0] : null;
?>

<div class="container py-5">
    <!-- Invoice Header -->
    <div class="card border-0 shadow-sm p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="text-muted small">Nomor Invoice</span>
                <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <?= e($payment['invoice_no']) ?>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill copy-btn" onclick="copyText('<?= e($payment['invoice_no']) ?>', this)">
                        <i class="bi bi-copy"></i>
                    </button>
                </h3>
                <div class="small text-muted mt-1">
                    Dibuat pada: <?= format_date($payment['created_at'], 'd F Y H:i') ?>
                </div>
            </div>

            <div class="text-md-end">
                <span class="text-muted small d-block mb-1">Status Pembayaran</span>
                <span class="badge <?= $currStatus['badge'] ?> px-3 py-2 fs-6 rounded-pill">
                    <i class="bi <?= $currStatus['icon'] ?> me-1"></i> <?= $currStatus['label'] ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Alert Status Khusus -->
    <?php if ($payment['status'] === 'berhasil'): ?>
        <div class="alert alert-success border-0 shadow-sm p-4 rounded-4 mb-4 text-center">
            <div class="fs-1 text-success mb-2">🎉</div>
            <h4 class="fw-bold text-success mb-2">Pembayaran Berhasil Diverifikasi!</h4>
            <p class="text-secondary mb-3">
                Terima kasih atas kontribusi nyata Anda untuk kelestarian alam. Dana donasi telah dialokasikan untuk program penanaman pohon.
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="<?= url('/dashboard/donasi') ?>" class="btn btn-success fw-bold px-4 rounded-pill">
                    <i class="bi bi-grid me-1"></i> Buka Dashboard Saya
                </a>
                <a href="<?= url('/donasi') ?>" class="btn btn-outline-success fw-bold px-4 rounded-pill">
                    🌱 Donasi Lagi
                </a>
            </div>
        </div>
    <?php elseif ($payment['status'] === 'ditolak'): ?>
        <div class="alert alert-danger border-0 shadow-sm p-4 rounded-4 mb-4">
            <h5 class="fw-bold text-danger mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Bukti Transfer Ditolak oleh Tim Keuangan
            </h5>
            <p class="mb-2">
                <strong>Alasan Penolakan:</strong> <?= e($lastProof['rejected_reason'] ?? 'Nominal tidak sesuai atau foto bukti transfer tidak terbaca.') ?>
            </p>
            <div class="small text-secondary">
                Jangan khawatir! Anda dapat mengunggah ulang bukti transfer yang benar melalui formulir di bawah ini selama tagihan belum kedaluwarsa.
            </div>
        </div>
    <?php elseif ($payment['status'] === 'kedaluwarsa'): ?>
        <div class="alert alert-secondary border-0 shadow-sm p-4 rounded-4 mb-4 text-center">
            <div class="fs-1 text-muted mb-2">⌛</div>
            <h5 class="fw-bold text-dark mb-2">Batas Waktu Pembayaran Telah Habis</h5>
            <p class="text-muted mb-3">
                Tagihan ini telah kedaluwarsa karena melewati batas waktu 24 jam tanpa bukti transfer yang diverifikasi.
            </p>
            <a href="<?= url('/donasi') ?>" class="btn btn-success fw-bold px-4 rounded-pill">
                Buat Donasi Baru →
            </a>
        </div>
    <?php endif; ?>

    <!-- Hitung Mundur Sisa Waktu Pembayaran (Hanya jika belum selesai/kedaluwarsa) -->
    <?php if ($isPayable && $remainingSeconds > 0): ?>
        <div class="alert alert-warning border-0 shadow-sm p-3 rounded-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="bi bi-stopwatch text-warning-emphasis fs-3 me-3"></i>
                <div>
                    <h6 class="fw-bold text-warning-emphasis mb-0">Selesaikan Pembayaran Sebelum:</h6>
                    <small class="text-secondary"><?= format_date($payment['expires_at'], 'l, d F Y H:i') ?></small>
                </div>
            </div>
            <div class="fw-bold text-danger fs-5 bg-white px-3 py-1 rounded-pill shadow-sm" id="countdownTimer">
                Menghitung waktu...
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Kolom Kiri: Rincian Nominal & Rekening Tujuan -->
        <div class="col-lg-6">
            
            <!-- Box Total Nominal & Kode Unik -->
            <div class="card border-0 shadow-sm p-4 mb-4 payment-instruction-box">
                <span class="text-muted small text-uppercase fw-semibold">Jumlah yang Wajib Ditransfer Tepat</span>
                <div class="d-flex align-items-baseline justify-content-between mt-1 mb-2">
                    <h2 class="fw-bold text-success mb-0" id="grandTotalDisplay">
                        <?= format_rupiah($payment['grand_total']) ?>
                    </h2>
                    <button class="btn btn-sm btn-success rounded-pill px-3 copy-btn" onclick="copyText('<?= (int)$payment['grand_total'] ?>', this)">
                        <i class="bi bi-copy me-1"></i> Salin Nominal
                    </button>
                </div>

                <?php if ($payment['unique_code'] > 0): ?>
                    <div class="p-3 bg-light rounded-3 mt-3 border">
                        <div class="d-flex justify-content-between small text-secondary mb-1">
                            <span>Subtotal Donasi:</span>
                            <span>
                                <?php
                                echo match($payment['payable_type']) {
                                    'order'       => 'Subtotal Pesanan:',
                                    'participant' => 'Biaya Pendaftaran:',
                                    default       => 'Subtotal Donasi:',
                                };
                                ?>
                            </span>
                            <span><?= format_rupiah($payment['total_amount']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between small text-secondary mb-2">
                            <span>Kode Unik Verifikasi:</span>
                            <span class="badge bg-warning text-dark fw-bold">+<?= $payment['unique_code'] ?></span>
                        </div>
                        <div class="small text-danger fw-semibold">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            PENTING: Mohon transfer <strong>PERSIS sampai 3 digit terakhir</strong> agar mutasi rekening Anda dapat diverifikasi dengan cepat.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rekening Tujuan Transfer -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-wallet2 text-success me-2"></i>Pilihan Kanal Transfer / QRIS</h5>
                    <span class="badge bg-success-subtle border border-success-subtle px-2 py-1 small" style="color:#0a3622;">Resmi & Terverifikasi</span>
                </div>
                <p class="text-muted small mb-3">Silakan transfer via ATM, m-Banking (BCA, Mandiri, BSI, BRI), atau Scan QRIS menggunakan seluruh aplikasi pembayaran nasional:</p>

                <div class="d-flex flex-column gap-3">
                    <?php foreach ($bankAccounts as $bank): ?>
                        <div class="p-3 border rounded-3 bg-light position-relative <?= !empty($bank['is_primary']) ? 'border-success border-2 shadow-sm' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark fs-6"><?= e($bank['bank_name']) ?></span>
                                    <?php if (!empty($bank['is_primary'])): ?>
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-1 small">
                                            <i class="bi bi-star-fill me-1"></i>REKOMENDASI UTAMA
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($bank['channel_type'] === 'qris'): ?>
                                        <span class="badge bg-danger text-white fw-bold px-2 py-1 small">
                                            <i class="bi bi-qr-code me-1"></i>QRIS GPN
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-sm btn-outline-success rounded-pill py-1 px-3 copy-btn" onclick="copyText('<?= e($bank['account_number']) ?>', this)">
                                    <i class="bi bi-copy me-1"></i> Salin <?= $bank['channel_type'] === 'qris' ? 'NMID' : 'No. Rekening' ?>
                                </button>
                            </div>

                            <div class="fs-5 fw-bold text-success font-monospace my-1">
                                <?= e($bank['account_number']) ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 small text-secondary">
                                <div>Atas Nama: <strong class="text-dark"><?= e($bank['account_holder']) ?></strong></div>
                                <?php if (!empty($bank['branch'])): ?>
                                    <div><i class="bi bi-geo-alt me-1"></i><?= e($bank['branch']) ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($bank['instructions'])): ?>
                                <div class="mt-2 pt-2 border-top small text-muted">
                                    <i class="bi bi-info-circle me-1 text-primary"></i><?= nl2br(e($bank['instructions'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($bank['channel_type'] === 'qris' && !empty($bank['qris_image_url'])): ?>
                                <div class="mt-3 p-3 bg-white rounded-3 border text-center">
                                    <div class="small fw-bold text-dark mb-2"><i class="bi bi-qr-code-scan me-1 text-danger"></i>Scan QRIS Langsung dari Ponsel:</div>
                                    <img src="<?= e($bank['qris_image_url']) ?>" alt="QRIS Code" class="img-fluid rounded border mb-2" style="max-height: 220px;">
                                    <div>
                                        <a href="<?= e($bank['qris_image_url']) ?>" download="QRIS_PlantForFun.png" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1">
                                            <i class="bi bi-download me-1"></i> Unduh Gambar QRIS
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Rincian Item Donasi -->
            <!-- Rincian Item Transaksi -->
            <?php if (!empty($payableDetails)): ?>
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3">Rincian Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <?php if ($payment['payable_type'] === 'participant'): ?>
                                    <tr class="border-bottom">
                                        <td class="py-2">
                                            <strong>Pendaftaran: <?= e($payableDetails['event_title'] ?? 'Kegiatan Penanaman') ?></strong>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= !empty($payableDetails['event_date']) ? format_date($payableDetails['event_date'], 'l, d F Y') : '-' ?>
                                                <?php if (!empty($payableDetails['gathering_point'])): ?>
                                                    · Titik Kumpul: <?= e($payableDetails['gathering_point']) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($payableDetails['tshirt_size'])): ?>
                                                    · Kaos: <span class="badge bg-primary"><?= e($payableDetails['tshirt_size']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-end py-2 fw-semibold">
                                            <?= format_rupiah($payment['total_amount']) ?>
                                        </td>
                                    </tr>
                                <?php elseif ($payment['payable_type'] === 'order'): ?>
                                    <?php foreach ($payableDetails as $item): ?>
                                        <tr class="border-bottom">
                                            <td class="py-2">
                                                <strong><?= e($item['product_name'] ?? 'Produk') ?></strong>
                                                <?php if (!empty($item['variant_info'])): ?>
                                                    <span class="badge bg-light text-dark border ms-1"><?= e($item['variant_info']) ?></span>
                                                <?php endif; ?>
                                                <div class="small text-muted mt-1">
                                                    <?= (int)($item['quantity'] ?? 1) ?> pcs &times; <?= format_rupiah($item['price'] ?? 0) ?>
                                                </div>
                                            </td>
                                            <td class="text-end py-2 fw-semibold">
                                                <?= format_rupiah($item['subtotal'] ?? 0) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: // donation ?>
                                    <?php foreach ($payableDetails as $item): ?>
                                        <tr class="border-bottom">
                                            <td class="py-2">
                                                <strong>Pohon <?= e($item['species_name'] ?? 'Pohon') ?></strong>
                                                <div class="small text-muted mt-1">
                                                    <?= (int)($item['quantity'] ?? 1) ?> Batang &times; <?= format_rupiah($item['price_per_tree'] ?? 0) ?>
                                                </div>
                                            </td>
                                            <td class="text-end py-2 fw-semibold">
                                                <?= format_rupiah($item['subtotal'] ?? 0) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (!empty($payment['unique_code'])): ?>
                                    <tr>
                                        <td class="pt-3 text-secondary">Kode Unik:</td>
                                        <td class="pt-3 text-end text-warning-emphasis fw-bold">+<?= $payment['unique_code'] ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="fw-bold text-dark fs-6">Grand Total:</td>
                                    <td class="text-end fw-bold text-success fs-6"><?= format_rupiah($payment['grand_total']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Kolom Kanan: Form Konfirmasi Pembayaran & Riwayat Bukti -->
        <div class="col-lg-6">

            <!-- Formulir Unggah Bukti Transfer -->
            <?php if ($isPayable): ?>
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-2">
                        <i class="bi bi-cloud-arrow-up-fill text-success me-2"></i>
                        <?= !empty($proofs) ? 'Unggah Ulang Bukti Transfer' : 'Konfirmasi Pembayaran' ?>
                    </h5>
                    <p class="text-muted small mb-4">
                        Setelah melakukan transfer, silakan lengkapi formulir di bawah ini dan unggah foto/tangkapan layar bukti transfer Anda.
                    </p>

                    <form action="<?= url('/pembayaran/' . $payment['invoice_no'] . '/konfirmasi') ?>" method="POST" enctype="multipart/form-data" id="confirmPaymentForm">
                        <?= csrf_field() ?>

                        <!-- Pilih Bank Tujuan -->
                        <div class="mb-3">
                            <label for="bank_account_id" class="form-label fw-semibold small text-secondary">Rekening / Kanal Tujuan</label>
                            <select class="form-select" id="bank_account_id" name="bank_account_id" required>
                                <option value="">-- Pilih Rekening / QRIS Tujuan --</option>
                                <?php foreach ($bankAccounts as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= old('bank_account_id') == $b['id'] ? 'selected' : '' ?>>
                                        <?= !empty($b['is_primary']) ? '⭐ [UTAMA] ' : ($b['channel_type'] === 'qris' ? '[QRIS] ' : '') ?><?= e($b['bank_name']) ?> — <?= e($b['account_number']) ?> (a.n <?= e($b['account_holder']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <!-- Bank Pengirim -->
                            <div class="col-sm-6">
                                <label for="sender_bank" class="form-label fw-semibold small text-secondary">Bank / E-Wallet Pengirim</label>
                                <input type="text" class="form-control" id="sender_bank" name="sender_bank" value="<?= e(old('sender_bank')) ?>" placeholder="Contoh: BCA / GoPay / OVO" required>
                            </div>
                            <!-- Nama Pengirim -->
                            <div class="col-sm-6">
                                <label for="sender_name" class="form-label fw-semibold small text-secondary">Nama Pemilik Rekening</label>
                                <input type="text" class="form-control" id="sender_name" name="sender_name" value="<?= e(old('sender_name', $payment['user_name'] ?? '')) ?>" placeholder="Sesuai buku tabungan / akun" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <!-- Nominal yang Ditransfer -->
                            <div class="col-sm-6">
                                <label for="amount_transferred" class="form-label fw-semibold small text-secondary">Nominal yang Ditransfer (Rp)</label>
                                <input type="number" class="form-control fw-bold text-success" id="amount_transferred" name="amount_transferred" value="<?= e(old('amount_transferred', (int)$payment['grand_total'])) ?>" required>
                            </div>
                            <!-- Tanggal Transfer -->
                            <div class="col-sm-6">
                                <label for="transfer_date" class="form-label fw-semibold small text-secondary">Tanggal Transfer</label>
                                <input type="date" class="form-control" id="transfer_date" name="transfer_date" value="<?= e(old('transfer_date', date('Y-m-d'))) ?>" required>
                            </div>
                        </div>

                        <!-- Upload Berkas Bukti -->
                        <div class="mb-3">
                            <label for="proof_file" class="form-label fw-semibold small text-secondary">
                                Berkas Bukti Transfer (JPG, PNG, atau PDF, Maks 2 MB)
                            </label>
                            <input type="file" class="form-control" id="proof_file" name="proof_file" accept="image/jpeg,image/png,application/pdf" required onchange="previewProofFile(this)">
                            <div class="form-text small">Pastikan nominal, tanggal, dan nomor transaksi terlihat jelas.</div>

                            <!-- Live Preview Area -->
                            <div id="proofPreviewContainer" class="mt-3 d-none text-center p-3 border rounded-3 bg-light">
                                <img id="imagePreview" src="#" alt="Preview Bukti" class="img-fluid rounded shadow-sm d-none" style="max-height: 250px;">
                                <div id="pdfPreview" class="d-none text-danger fs-1">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    <div class="small text-secondary mt-1" id="pdfFileName">Dokumen PDF Terpilih</div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div class="mb-4">
                            <label for="note" class="form-label fw-semibold small text-secondary">Catatan Tambahan (Opsional)</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Catatan opsional untuk admin..."><?= e(old('note')) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm rounded-pill py-2">
                            <i class="bi bi-send-check-fill me-2"></i> Kirim Bukti Transfer
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Riwayat Bukti yang Pernah Diunggah -->
            <?php if (!empty($proofs)): ?>
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-dark mb-3">Riwayat Bukti Diunggah</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($proofs as $idx => $p): ?>
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge <?= $p['status'] === 'disetujui' ? 'bg-success' : ($p['status'] === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') ?> px-2 py-1">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                    <span class="small text-muted"><?= format_date($p['created_at'], 'd F Y H:i') ?></span>
                                </div>
                                <div class="small text-secondary mb-1">
                                    Pengirim: <strong><?= e($p['sender_name']) ?></strong> (<?= e($p['sender_bank']) ?>)
                                </div>
                                <div class="small text-secondary mb-2">
                                    Nominal Ditransfer: <strong class="text-success"><?= format_rupiah($p['amount_transferred']) ?></strong>
                                </div>
                                <?php if (!empty($p['rejected_reason'])): ?>
                                    <div class="alert alert-danger py-2 px-3 small mb-2">
                                        <strong>Alasan Ditolak:</strong> <?= e($p['rejected_reason']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
// Hitung Mundur Sisa Waktu Pembayaran
(function() {
    let remaining = <?= (int)$remainingSeconds ?>;
    const timerElem = document.getElementById('countdownTimer');
    if (!timerElem || remaining <= 0) return;

    function updateTimer() {
        if (remaining <= 0) {
            timerElem.innerHTML = '<span class="text-danger">WAKTU HABIS</span>';
            setTimeout(() => window.location.reload(), 2000);
            return;
        }

        const hours = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;

        timerElem.textContent = 
            String(hours).padStart(2, '0') + ':' + 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');

        remaining--;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
})();

// Salin Teks ke Clipboard
function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i> Tersalin!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary', 'btn-outline-success');
        setTimeout(() => {
            btn.innerHTML = origHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(err => {
        alert('Gagal menyalin: ' + err);
    });
}

// Live Preview Berkas Bukti Transfer
function previewProofFile(input) {
    const container = document.getElementById('proofPreviewContainer');
    const img = document.getElementById('imagePreview');
    const pdf = document.getElementById('pdfPreview');
    const pdfName = document.getElementById('pdfFileName');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        container.classList.remove('d-none');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.classList.remove('d-none');
                pdf.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            img.classList.add('d-none');
            pdf.classList.remove('d-none');
            pdfName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        }
    } else {
        container.classList.add('d-none');
    }
}
</script>

