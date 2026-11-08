<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-calendar2-check text-success me-2"></i>Kegiatan Relawan Saya
        </h3>
        <p class="text-muted mb-0">Pantau jadwal aksi penanaman pohon, unduh E-Ticket QR, kontak koordinator lapangan, dan berikan ulasan.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= url('/kegiatan') ?>" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="bi bi-compass me-1"></i> Jelajahi Aksi Baru
        </a>
    </div>
</div>

<!-- Navigasi Filter Status -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-2 p-md-3">
        <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $filter === 'all' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/kegiatan?filter=all') ?>">
                    <i class="bi bi-grid-fill me-1"></i> Semua Kegiatan
                    <span class="badge <?= $filter === 'all' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['all'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $filter === 'upcoming' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/kegiatan?filter=upcoming') ?>">
                    <i class="bi bi-hourglass-split me-1"></i> Akan Datang
                    <span class="badge <?= $filter === 'upcoming' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['upcoming'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $filter === 'completed' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/kegiatan?filter=completed') ?>">
                    <i class="bi bi-check-circle-fill me-1"></i> Selesai / Hadir
                    <span class="badge <?= $filter === 'completed' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['completed'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $filter === 'cancelled' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/kegiatan?filter=cancelled') ?>">
                    <i class="bi bi-x-circle-fill me-1"></i> Dibatalkan
                    <span class="badge <?= $filter === 'cancelled' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['cancelled'] ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Daftar Kegiatan Relawan -->
<?php if (empty($participations)): ?>
    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
        <div class="card-body">
            <div class="display-3 text-muted mb-3">🌱</div>
            <h5 class="fw-bold text-dark">Tidak ada kegiatan di kategori ini</h5>
            <p class="text-muted mb-4">Anda belum memiliki jadwal kegiatan relawan dengan status yang dipilih.</p>
            <a href="<?= url('/kegiatan') ?>" class="btn btn-outline-success rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> Daftar Aksi Penanaman Sekarang
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($participations as $item): ?>
            <?php
                $eventDateTs = strtotime($item['event_date']);
                $todayTs = strtotime(date('Y-m-d'));
                $diffDays = (int)ceil(($eventDateTs - $todayTs) / 86400);

                // Status Pendaftaran Badge
                $statusBadgeClass = 'bg-secondary';
                $statusBadgeLabel = 'Menunggu';
                if ($item['registration_status'] === 'terkonfirmasi') {
                    $statusBadgeClass = 'bg-success';
                    $statusBadgeLabel = 'Terkonfirmasi';
                } elseif ($item['registration_status'] === 'hadir') {
                    $statusBadgeClass = 'bg-primary';
                    $statusBadgeLabel = 'Hadir di Lokasi';
                } elseif ($item['registration_status'] === 'batal') {
                    $statusBadgeClass = 'bg-danger';
                    $statusBadgeLabel = 'Dibatalkan';
                }
            ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                    <!-- Garis Indikator Kiri -->
                    <div class="position-absolute top-0 bottom-0 start-0 <?= $item['registration_status'] === 'batal' ? 'bg-danger' : ($item['is_past'] ? 'bg-secondary' : 'bg-success') ?>" style="width: 6px;"></div>

                    <div class="card-body p-4 ps-4 ps-md-5">
                        <div class="row g-4">
                            <!-- Kolom Utama Info Event -->
                            <div class="col-lg-8">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <span class="badge <?= $statusBadgeClass ?> px-3 py-1 rounded-pill">
                                        <i class="bi bi-dot"></i> <?= $statusBadgeLabel ?>
                                    </span>

                                    <?php if ($item['registration_status'] !== 'batal'): ?>
                                        <?php if ($diffDays > 0): ?>
                                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                                                <i class="bi bi-alarm me-1"></i> H-<?= $diffDays ?> Hari Lagi
                                            </span>
                                        <?php elseif ($diffDays === 0): ?>
                                            <span class="badge bg-danger text-white px-3 py-1 rounded-pill fw-bold animate-pulse">
                                                <i class="bi bi-geo-alt-fill me-1"></i> HARI INI!
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">
                                                <i class="bi bi-check-all me-1"></i> Telah Selesai
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($item['tshirt_size'])): ?>
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                            <i class="bi bi-tag-fill text-muted me-1"></i> Kaos: Size <?= e($item['tshirt_size']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h4 class="fw-bold text-dark mb-2">
                                    <a href="<?= url('/kegiatan/' . $item['event_id']) ?>" class="text-decoration-none text-dark hover-success">
                                        <?= e($item['event_title']) ?>
                                    </a>
                                </h4>

                                <!-- Info Grid -->
                                <div class="row g-3 my-2 text-secondary small">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-calendar-event text-success fs-5 me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Tanggal & Waktu</div>
                                                <div><?= format_date($item['event_date'], 'l, d F Y') ?></div>
                                                <div class="text-muted">Kumpul: <?= substr($item['gathering_time'], 0, 5) ?> WIB</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-geo-alt text-danger fs-5 me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Titik Kumpul & Lokasi</div>
                                                <div><?= e($item['gathering_point']) ?></div>
                                                <div class="text-muted"><?= e($item['location_name']) ?>, <?= e($item['location_city']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-person-badge text-primary fs-5 me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Koordinator Lapangan</div>
                                                <div><?= e($item['coordinator_name'] ?? 'Tim Panitia Lapangan') ?></div>
                                                <?php if (!empty($item['coordinator_phone'])): ?>
                                                    <?php $waPhone = preg_replace('/[^0-9]/', '', $item['coordinator_phone']); ?>
                                                    <a href="https://wa.me/<?= $waPhone ?>?text=<?= urlencode('Halo Kak, saya relawan ' . $item['event_title'] . ' (No Tiket: ' . ($item['ticket_code'] ?? '-') . ')') ?>" target="_blank" class="text-success text-decoration-none fw-bold">
                                                        <i class="bi bi-whatsapp"></i> Chat WhatsApp
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-map text-warning fs-5 me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Petunjuk Arah</div>
                                                <?php if (!empty($item['google_maps_link'])): ?>
                                                    <a href="<?= e($item['google_maps_link']) ?>" target="_blank" class="text-decoration-none text-primary fw-medium">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Google Maps
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Tersedia di lokasi acara</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alert Tagihan jika kegiatan berbayar dan belum lunas -->
                                <?php if (!empty($item['invoice_no']) && $item['payment_status'] !== 'berhasil'): ?>
                                    <div class="alert alert-warning border-0 rounded-3 p-3 my-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                            <strong>Biaya Komitmen Relawan: <?= format_rupiah($item['grand_total']) ?></strong>
                                            <span class="d-block text-muted small">No. Invoice: <?= e($item['invoice_no']) ?> (Status: <?= ucfirst(str_replace('_', ' ', $item['payment_status'])) ?>)</span>
                                        </div>
                                        <a href="<?= url('/pembayaran/' . e($item['invoice_no'])) ?>" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-upload me-1"></i> Bayar / Upload Bukti
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Catatan Pembatalan jika dibatalkan -->
                                <?php if ($item['registration_status'] === 'batal'): ?>
                                    <div class="alert alert-danger-subtle border border-danger-subtle rounded-3 p-3 mt-3 mb-0 small">
                                        <i class="bi bi-info-circle-fill text-danger me-1"></i>
                                        <strong>Pendaftaran telah dibatalkan</strong> pada <?= format_date($item['cancelled_at'] ?? $item['updated_at'], 'd M Y, H:i') ?> WIB.
                                        <?php if (!empty($item['cancel_reason'])): ?>
                                            <div class="mt-1 text-muted">Alasan: <?= e($item['cancel_reason']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Tampilan Ulasan jika sudah pernah diberikan -->
                                <?php if (!empty($item['feedback_id'])): ?>
                                    <div class="bg-light border rounded-3 p-3 mt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold small text-dark"><i class="bi bi-chat-quote-fill text-success me-1"></i> Ulasan Anda:</span>
                                            <div>
                                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                                    <i class="bi bi-star<?= $s <= $item['feedback_rating'] ? '-fill text-warning' : ' text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($item['feedback_comment'])): ?>
                                            <p class="mb-0 text-muted small fst-italic">"<?= e($item['feedback_comment']) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kolom Kanan: Aksi & Tiket -->
                            <div class="col-lg-4 border-start-md d-flex flex-column justify-content-between">
                                <div class="bg-light rounded-4 p-3 mb-3 text-center">
                                    <?php if ($item['registration_status'] !== 'batal' && !empty($item['ticket_code'])): ?>
                                        <div class="small text-muted mb-1">Nomor E-Ticket Resmi:</div>
                                        <div class="fw-bold font-monospace text-dark fs-6 bg-white py-1 px-2 rounded border mb-2">
                                            <?= e($item['ticket_code']) ?>
                                        </div>
                                        <a href="<?= url('/tiket/' . e($item['ticket_code'])) ?>" target="_blank" class="btn btn-outline-success btn-sm w-100 rounded-pill mb-2">
                                            <i class="bi bi-qr-code-scan me-1"></i> Buka & Cetak Tiket QR
                                        </a>
                                    <?php elseif ($item['registration_status'] === 'batal'): ?>
                                        <div class="text-muted small py-3">
                                            <i class="bi bi-ticket-perforated fs-1 d-block text-secondary mb-1"></i>
                                            Tiket tidak berlaku (Dibatalkan)
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted small py-3">
                                            <i class="bi bi-ticket-detailed fs-1 d-block text-warning mb-1"></i>
                                            Tiket terbit setelah verifikasi pembayaran
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons Bottom -->
                                <div class="d-grid gap-2">
                                    <!-- Unduh Sertifikat jika hadir atau selesai -->
                                    <?php if ($item['registration_status'] === 'hadir' || (!empty($item['certificate_id']))): ?>
                                        <?php if (!empty($item['certificate_id'])): ?>
                                            <a href="<?= url('/dashboard/sertifikat/' . $item['certificate_id'] . '/unduh') ?>" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill">
                                                <i class="bi bi-award-fill me-1"></i> Unduh Sertifikat Relawan
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Tombol Beri Ulasan (jika hadir atau event selesai & belum beri feedback & tidak batal) -->
                                    <?php if (($item['is_past'] || $item['registration_status'] === 'hadir') && empty($item['feedback_id']) && $item['registration_status'] !== 'batal'): ?>
                                        <button type="button" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#feedbackModal<?= $item['id'] ?>">
                                            <i class="bi bi-star-fill text-warning me-1"></i> Beri Rating & Ulasan (+10 Poin)
                                        </button>
                                    <?php endif; ?>

                                    <!-- Tombol Batalkan (Hanya jika belum hari-H dan belum batal/hadir) -->
                                    <?php if (!empty($item['is_cancellable'])): ?>
                                        <button type="button" class="btn btn-link text-danger text-decoration-none btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $item['id'] ?>">
                                            <i class="bi bi-x-circle me-1"></i> Batalkan Keikutsertaan
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Pembatalan -->
            <?php if (!empty($item['is_cancellable'])): ?>
                <div class="modal fade" id="cancelModal<?= $item['id'] ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?= $item['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="<?= url('/dashboard/kegiatan/' . $item['id'] . '/batal') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger" id="cancelModalLabel<?= $item['id'] ?>">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Pembatalan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <p class="text-muted">
                                        Apakah Anda yakin ingin membatalkan keikutsertaan dalam kegiatan <strong>"<?= e($item['event_title']) ?>"</strong>?
                                    </p>
                                    <div class="alert alert-warning py-2 px-3 small rounded-3">
                                        <i class="bi bi-info-circle me-1"></i> Kuota peserta Anda akan otomatis dialihkan kepada relawan lain yang membutuhkan.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-dark">Alasan Pembatalan (Opsional):</label>
                                        <textarea name="reason" rows="3" class="form-control rounded-3" placeholder="Contoh: Ada keperluan mendadak di luar kota..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Batalkan Pendaftaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Modal Ulasan / Feedback -->
            <?php if (($item['is_past'] || $item['registration_status'] === 'hadir') && empty($item['feedback_id']) && $item['registration_status'] !== 'batal'): ?>
                <div class="modal fade" id="feedbackModal<?= $item['id'] ?>" tabindex="-1" aria-labelledby="feedbackModalLabel<?= $item['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="<?= url('/dashboard/kegiatan/' . $item['id'] . '/feedback') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-dark" id="feedbackModalLabel<?= $item['id'] ?>">
                                        <i class="bi bi-star-fill text-warning me-2"></i>Ulasan Kegiatan Relawan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <p class="text-muted small mb-3">
                                        Bagikan pengalaman berharga Anda saat berpartisipasi dalam penanaman pohon di <strong><?= e($item['event_title']) ?></strong>. Dapatkan <strong>+10 Poin Kontribusi</strong>!
                                    </p>

                                    <!-- Rating Bintang -->
                                    <div class="mb-3 text-center">
                                        <label class="form-label fw-bold d-block text-dark">Berapa tingkat kepuasan Anda?</label>
                                        <div class="rating-stars d-inline-flex gap-2 fs-2 text-warning cursor-pointer" id="starsGroup<?= $item['id'] ?>">
                                            <i class="bi bi-star-fill star-item" data-value="1"></i>
                                            <i class="bi bi-star-fill star-item" data-value="2"></i>
                                            <i class="bi bi-star-fill star-item" data-value="3"></i>
                                            <i class="bi bi-star-fill star-item" data-value="4"></i>
                                            <i class="bi bi-star-fill star-item" data-value="5"></i>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput<?= $item['id'] ?>" value="5">
                                        <div class="small text-muted mt-1" id="ratingLabel<?= $item['id'] ?>">Luar Biasa (5/5)</div>
                                    </div>

                                    <!-- Komentar / Ulasan -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-dark">Komentar & Pesan untuk Panitia:</label>
                                        <textarea name="comment" rows="3" class="form-control rounded-3" placeholder="Ceritakan keseruan Anda, masukan untuk kegiatan selanjutnya, atau apresiasi untuk koordinator..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4">Kirim Ulasan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Interactive Star Rating inside feedback modals
    document.querySelectorAll('.rating-stars').forEach(function(starContainer) {
        const modal = starContainer.closest('.modal');
        const stars = starContainer.querySelectorAll('.star-item');
        const hiddenInput = modal.querySelector('input[name="rating"]');
        const ratingLabel = modal.querySelector('[id^="ratingLabel"]');

        const labels = {
            1: 'Kurang Memuaskan (1/5)',
            2: 'Cukup (2/5)',
            3: 'Baik (3/5)',
            4: 'Sangat Baik (4/5)',
            5: 'Luar Biasa (5/5)'
        };

        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-value'));
                hiddenInput.value = val;
                if (ratingLabel) ratingLabel.textContent = labels[val] || (val + '/5');

                stars.forEach(function(s) {
                    const sVal = parseInt(s.getAttribute('data-value'));
                    if (sVal <= val) {
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill');
                    } else {
                        s.classList.remove('bi-star-fill');
                        s.classList.add('bi-star');
                    }
                });
            });
        });
    });
});
</script>
