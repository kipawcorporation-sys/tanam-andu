<!-- 1. Personal Greeting Banner -->
<div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 mb-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #198754 0%, #0f5132 100%);">
    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge <?= $levelClass ?> px-3 py-1 rounded-pill fw-bold">
                    <?= $levelName ?>
                </span>
                <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill">
                    <?= number_format($stats['points']) ?> Poin Kontribusi
                </span>
            </div>
            <h2 class="fw-bold mb-2">
                Halo, <?= e(htmlspecialchars($user['nama'])) ?>! 🌱
            </h2>
            <p class="text-white-50 mb-3 small" style="max-width: 600px;">
                Terima kasih telah bersama-sama menjaga kelestarian bumi. Kontribusi pohon Anda menyerap estimasi <strong><?= number_format($co2AbsorptionKg, 1) ?> kg CO₂</strong> setiap tahunnya.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('/donasi') ?>" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">
                    🌱 Tanam Pohon Baru
                </a>
                <a href="<?= url('/kegiatan') ?>" class="btn btn-outline-light btn-sm fw-bold px-4 rounded-pill">
                    🤝 Ikuti Aksi Relawan
                </a>
            </div>
        </div>
        <div class="col-md-4 text-end d-none d-md-block">
            <span class="display-1 opacity-50">🌳</span>
        </div>
    </div>
</div>

<!-- 2. Empat Kartu Statistik Utama -->
<div class="row g-3 mb-4">
    <!-- Total Pohon -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 bg-white h-100 card-hover">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-secondary small fw-semibold">Pohon Saya</span>
                <div class="stat-icon bg-success-subtle" style="color:#0a3622;">
                    <i class="bi bi-tree"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['total_trees']) ?></h3>
            <small class="text-muted" style="font-size: 11px;">Batang Pohon Terdaftar</small>
        </div>
    </div>

    <!-- Total Donasi -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 bg-white h-100 card-hover">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-secondary small fw-semibold">Total Donasi</span>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= format_rupiah($stats['total_donation']) ?></h3>
            <small class="text-muted" style="font-size: 11px;">Dana Terhimpun</small>
        </div>
    </div>

    <!-- Kegiatan Diikuti -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 bg-white h-100 card-hover">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-secondary small fw-semibold">Aksi Relawan</span>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['total_events']) ?></h3>
            <small class="text-muted" style="font-size: 11px;">Kegiatan Terdaftar</small>
        </div>
    </div>

    <!-- Pesanan Kaos -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 p-md-4 rounded-4 bg-white h-100 card-hover">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-secondary small fw-semibold">Merchandise</span>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-bag"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['total_orders']) ?></h3>
            <small class="text-muted" style="font-size: 11px;">Pesanan Kaos</small>
        </div>
    </div>
</div>

<!-- 3. Kegiatan Terdekat (Jika Ada) -->
<?php if (!empty($nextEvent)): ?>
    <?php 
        $eventTs = strtotime($nextEvent['event_date']);
        $diffDays = ceil(($eventTs - time()) / 86400);
    ?>
    <div class="alert alert-success border-0 shadow-sm p-4 rounded-4 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-success text-white p-3 rounded-circle fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div>
                <span class="badge bg-warning text-dark fw-bold mb-1">
                    <?= $diffDays > 0 ? "H-{$diffDays} Hari Menuju Acara" : "Hari Ini!" ?>
                </span>
                <h5 class="fw-bold text-dark mb-0"><?= e($nextEvent['event_title']) ?></h5>
                <small class="text-secondary">
                    <i class="bi bi-geo-alt-fill text-success"></i> <?= e($nextEvent['gathering_point']) ?> &bull; 
                    <i class="bi bi-clock"></i> Pukul <?= substr($nextEvent['gathering_time'], 0, 5) ?> WIB
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if (!empty($nextEvent['ticket_code'])): ?>
                <a href="<?= url('/tiket/' . $nextEvent['ticket_code']) ?>" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
                    <i class="bi bi-qr-code me-1"></i> Buka E-Ticket
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <!-- 4. Grafik Kontribusi Bulanan (Chart.js) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Grafik Kontribusi Pohon</h5>
                    <small class="text-muted">Jumlah pohon yang didonasikan dalam 6 bulan terakhir</small>
                </div>
                <span class="badge bg-success-subtle border border-success-subtle px-3 py-1 rounded-pill" style="color:#0a3622;">
                    Tahun <?= date('Y') ?>
                </span>
            </div>
            <div style="height: 260px;">
                <canvas id="contributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 5. Ajak Teman & Kode Referral -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100 d-flex flex-column justify-content-between">
            <div>
                <span class="badge bg-warning-subtle text-dark fw-bold px-3 py-1 rounded-pill mb-2">Program Referral</span>
                <h5 class="fw-bold text-dark mb-2">Ajak Teman Tanam Pohon</h5>
                <p class="text-muted small">
                    Bagikan kode referral Anda. Teman yang mendaftar mendapat bonus <strong>5 poin</strong>, dan Anda mendapat <strong>10 poin</strong>!
                </p>

                <div class="p-3 bg-light rounded-3 border text-center mb-3">
                    <span class="small text-muted d-block mb-1">Kode Referral Anda:</span>
                    <h4 class="fw-bold text-success font-monospace mb-0" id="myReferralCode">
                        <?= e(auth('referral_code') ?? 'POHON' . auth('id')) ?>
                    </h4>
                </div>
            </div>

            <button type="button" class="btn btn-outline-success fw-bold w-100 rounded-pill py-2 copy-btn" onclick="copyReferralCode()">
                <i class="bi bi-copy me-1"></i> Salin Tautan Referral
            </button>
        </div>
    </div>
</div>

<!-- 6. Donasi Terbaru -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-0">Riwayat Donasi Terbaru</h5>
            <small class="text-muted">Daftar transaksi donasi pohon terakhir Anda</small>
        </div>
        <a href="<?= url('/dashboard/donasi') ?>" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
            Lihat Semua →
        </a>
    </div>

    <?php if (!empty($recentDonations)): ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th class="text-center">Jumlah Pohon</th>
                        <th class="text-end">Total Donasi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentDonations as $d): ?>
                        <tr>
                            <td>
                                <strong class="text-dark font-monospace"><?= e($d['invoice_no']) ?></strong>
                            </td>
                            <td class="small text-muted">
                                <?= format_date($d['created_at'], 'd M Y') ?>
                            </td>
                            <td class="text-center fw-bold">
                                <?= $d['total_trees'] ?> Batang
                            </td>
                            <td class="text-end fw-bold text-success">
                                <?= format_rupiah($d['grand_total']) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($d['payment_status'] === 'berhasil'): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-1">Berhasil</span>
                                <?php elseif ($d['payment_status'] === 'menunggu_verifikasi'): ?>
                                    <span class="badge bg-info text-dark rounded-pill px-3 py-1">Verifikasi</span>
                                <?php elseif ($d['payment_status'] === 'ditolak'): ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Menunggu Bayar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('/pembayaran/' . $d['invoice_no']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 12px;">
                                    Buka Invoice
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="fs-1 text-muted mb-2">🌱</div>
            <h6 class="fw-bold text-dark">Belum Ada Riwayat Donasi</h6>
            <p class="text-muted small mb-3">Yuk tanam pohon pertamamu dan bantu pulihkan hutan Indonesia!</p>
            <a href="<?= url('/donasi') ?>" class="btn btn-success fw-bold rounded-pill px-4">
                Mulai Donasi Pertama →
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Chart.js Script Initializer -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('contributionChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Jumlah Pohon (Batang)',
                data: <?= json_encode($chartData) ?>,
                backgroundColor: 'rgba(25, 135, 84, 0.75)',
                borderColor: '#198754',
                borderWidth: 1.5,
                borderRadius: 8,
                barThickness: 28,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});

function copyReferralCode() {
    const code = document.getElementById('myReferralCode').innerText.trim();
    const referralUrl = '<?= url('/daftar?ref=') ?>' + encodeURIComponent(code);
    navigator.clipboard.writeText(referralUrl).then(() => {
        alert('Tautan referral berhasil disalin ke clipboard:\n' + referralUrl);
    });
}
</script>

