<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0">Donasi Saya</h3>
        <p class="text-muted small mb-0">Kelola riwayat donasi pohon, tagihan pembayaran, dan unduh sertifikat penghargaan Anda.</p>
    </div>
    <a href="<?= url('/donasi') ?>" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
        🌱 Donasi Pohon Baru
    </a>
</div>

<!-- Filter Status Tabs -->
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="<?= url('/dashboard/donasi') ?>" class="btn rounded-pill px-3 py-1 btn-sm <?= $statusFilter === 'all' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
        Semua (<?= $totalCount ?>)
    </a>
    <a href="<?= url('/dashboard/donasi?status=berhasil') ?>" class="btn rounded-pill px-3 py-1 btn-sm <?= $statusFilter === 'berhasil' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
        ✅ Berhasil
    </a>
    <a href="<?= url('/dashboard/donasi?status=pending') ?>" class="btn rounded-pill px-3 py-1 btn-sm <?= $statusFilter === 'pending' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
        ⏳ Menunggu Bayar / Verifikasi
    </a>
    <a href="<?= url('/dashboard/donasi?status=ditolak') ?>" class="btn rounded-pill px-3 py-1 btn-sm <?= $statusFilter === 'ditolak' ? 'btn-success fw-bold shadow-sm' : 'btn-light border text-secondary' ?>">
        ❌ Ditolak
    </a>
</div>

<?php if (!empty($donations)): ?>
    <div class="row g-3">
        <?php foreach ($donations as $d): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white card-hover">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <!-- Info Utama Donasi -->
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="font-monospace fw-bold text-dark fs-6"><?= e($d['invoice_no']) ?></span>
                                &bull;
                                <span class="small text-muted"><?= format_date($d['created_at'], 'd F Y H:i') ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle border border-success-subtle px-3 py-1 rounded-pill fw-bold" style="color:#0a3622;">
                                    🌳 <?= $d['total_trees'] ?> Batang Pohon
                                </span>
                                <?php if (!empty($d['is_anonymous'])): ?>
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill">Anonim</span>
                                <?php endif; ?>
                                <?php if (!empty($d['is_gift'])): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border px-2 py-1 rounded-pill">
                                        🎁 Hadiah untuk: <?= e($d['gift_recipient_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status & Total -->
                        <div class="text-md-end">
                            <span class="text-muted small d-block">Total Donasi</span>
                            <h5 class="fw-bold text-success mb-1"><?= format_rupiah($d['grand_total']) ?></h5>
                            <div>
                                <?php if ($d['payment_status'] === 'berhasil'): ?>
                                    <span class="badge bg-success px-3 py-1 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Berhasil Diverifikasi
                                    </span>
                                <?php elseif ($d['payment_status'] === 'menunggu_verifikasi'): ?>
                                    <span class="badge bg-info text-dark px-3 py-1 rounded-pill">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi Admin
                                    </span>
                                <?php elseif ($d['payment_status'] === 'ditolak'): ?>
                                    <span class="badge bg-danger px-3 py-1 rounded-pill">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Bukti Ditolak
                                    </span>
                                <?php elseif ($d['payment_status'] === 'kedaluwarsa'): ?>
                                    <span class="badge bg-secondary px-3 py-1 rounded-pill">Kedaluwarsa</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                                        <i class="bi bi-clock me-1"></i> Menunggu Pembayaran
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Cepat Bawah -->
                    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-3 border-top flex-wrap">
                        <a href="<?= url('/pembayaran/' . $d['invoice_no']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-receipt me-1"></i> Rincian Tagihan
                        </a>

                        <?php if (in_array($d['payment_status'], ['menunggu_pembayaran', 'ditolak'])): ?>
                            <a href="<?= url('/pembayaran/' . $d['invoice_no']) ?>" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Bukti Transfer
                            </a>
                        <?php endif; ?>

                        <?php if ($d['payment_status'] === 'berhasil'): ?>
                            <a href="<?= url('/dashboard/pohon') ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-tree me-1"></i> Lihat Pohon Saya
                            </a>

                            <?php if (!empty($d['certificate'])): ?>
                                <a href="<?= url('/dashboard/sertifikat/' . $d['certificate']['id'] . '/unduh') ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="bi bi-award me-1"></i> Unduh Sertifikat PDF
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <!-- Empty State Ramah -->
    <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4 my-4">
        <div class="display-1 text-muted mb-3">🌱</div>
        <h4 class="fw-bold text-dark">Belum Ada Donasi yang Cocok</h4>
        <p class="text-muted small mb-4">Setiap pohon yang Anda tanam adalah warisan oksigen bagi generasi mendatang.</p>
        <div>
            <a href="<?= url('/donasi') ?>" class="btn btn-success btn-lg fw-bold rounded-pill px-5 shadow-sm">
                Tanam Pohon Pertamamu Sekarang →
            </a>
        </div>
    </div>
<?php endif; ?>

