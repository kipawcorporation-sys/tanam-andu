<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="<?= url('/kegiatan') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    ← Kembali ke Daftar Kegiatan
                </a>
                <button type="button" class="btn btn-success btn-sm rounded-pill fw-semibold" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak / Simpan Tiket
                </button>
            </div>

            <!-- Tiket Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white" id="printableTicketArea">
                <!-- Header Tiket Hijau -->
                <div class="bg-success text-white p-4 text-center position-relative">
                    <span class="fs-1">🌱</span>
                    <h4 class="fw-bold mb-1">E-TICKET RESMI RELAWAN</h4>
                    <p class="small mb-0 text-white-50">Plant For Fun &bull; Kerenceng Coffee Planting & Agroforestry Expedition</p>
                </div>

                <!-- Kode Tiket & Status -->
                <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small d-block">Nomor Tiket:</span>
                        <h4 class="fw-bold text-dark mb-0 font-monospace"><?= e($ticket['ticket_code']) ?></h4>
                    </div>
                    <div>
                        <?php if ($ticket['status'] === 'used'): ?>
                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                                <i class="bi bi-check-all me-1"></i> SUDAH CHECK-IN
                            </span>
                        <?php elseif ($ticket['status'] === 'active'): ?>
                            <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
                                <i class="bi bi-ticket-perforated me-1"></i> TIKET AKTIF
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6">
                                <?= strtoupper($ticket['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 align-items-center">
                        <!-- QR Code Area -->
                        <div class="col-md-5 text-center border-end-md">
                            <div class="p-3 bg-white border rounded-4 d-inline-block shadow-sm mb-2">
                                <!-- QR Visual Generator -->
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode($ticket['ticket_code']) ?>" alt="QR Code Tiket" class="img-fluid" style="width: 170px; height: 170px;">
                            </div>
                            <div class="small text-muted font-monospace mt-1">Scan saat registrasi hari-H</div>
                        </div>

                        <!-- Data Peserta & Kegiatan -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <span class="text-muted small d-block">Nama Peserta:</span>
                                <h5 class="fw-bold text-dark mb-0"><?= e($ticket['participant_name']) ?></h5>
                                <small class="text-muted"><?= e($ticket['participant_email']) ?> &bull; <?= e($ticket['participant_phone']) ?></small>
                            </div>

                            <div class="mb-3">
                                <span class="text-muted small d-block">Ukuran Kaos Relawan:</span>
                                <span class="badge bg-success-subtle border border-success-subtle px-3 py-1 fs-6 fw-bold" style="color:#0a3622;">
                                    Ukuran <?= $ticket['tshirt_size'] ?>
                                </span>
                            </div>

                            <div class="mb-3">
                                <span class="text-muted small d-block">Kegiatan:</span>
                                <strong class="text-dark"><?= e($ticket['event_title']) ?></strong>
                            </div>

                            <div class="mb-2 small text-secondary">
                                <i class="bi bi-calendar-check text-success me-1"></i> <?= format_date($ticket['event_date'], 'l, d F Y') ?>
                            </div>
                            <div class="mb-2 small text-secondary">
                                <i class="bi bi-clock text-success me-1"></i> Pukul <?= substr($ticket['gathering_time'], 0, 5) ?> WIB
                            </div>
                            <div class="small text-secondary">
                                <i class="bi bi-geo-alt-fill text-success me-1"></i> Titik Kumpul: <?= e($ticket['gathering_point']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk Kedatangan -->
                    <div class="alert alert-light border rounded-3 p-3 mt-4 small text-secondary mb-0">
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-info-circle text-success me-1"></i> Petunjuk Hari-H:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Tunjukkan QR Code tiket ini pada petugas registrasi di lokasi penanaman.</li>
                            <li>Ambil perlengkapan kerja relawan dan kaos di meja registrasi.</li>
                            <li>Hubungi koordinator <strong><?= e($ticket['coordinator_name']) ?> (<?= e($ticket['coordinator_phone']) ?>)</strong> jika membutuhkan panduan rute.</li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Tiket -->
                <div class="bg-light p-3 text-center border-top small text-muted">
                    Simpan screenshot tiket ini atau cetak di kertas untuk ditunjukkan kepada petugas di lokasi.
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableTicketArea, #printableTicketArea * {
        visibility: visible;
    }
    #printableTicketArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

