<?php
/**
 * View: admin/dashboard.php — Dashboard Eksekutif Plant For Fun
 * Tema: Botanical Expedition & Specialty Coffee Operations
 */
?>

<!-- 1. EXECUTIVE WELCOME HERO BANNER -->
<div class="card border-0 rounded-4 mb-4 text-white overflow-hidden shadow-sm" 
     style="background: linear-gradient(135deg, #091e16 0%, #12382c 60%, #1b4d3c 100%); position: relative;">
    
    <!-- Ambient Glow Circles -->
    <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(191,210,84,0.2) 0%, transparent 70%); pointer-events: none;"></div>
    <div style="position: absolute; left: 30%; bottom: -50px; width: 260px; height: 260px; background: radial-gradient(circle, rgba(34,197,94,0.15) 0%, transparent 70%); pointer-events: none;"></div>

    <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center g-4">
            
            <div class="col-xl-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge rounded-pill font-mono px-3 py-1" style="background: rgba(191,210,84,0.2); color: #bfd254; border: 1px solid rgba(191,210,84,0.3);">
                        <i class="bi bi-compass me-1"></i>KERENCENG COFFEE EXPEDITION
                    </span>
                    <span class="badge rounded-pill bg-white bg-opacity-10 text-white font-mono px-3 py-1 border border-white border-opacity-10">
                        1.550 MDPL &bull; CICALENGKA
                    </span>
                    <span class="badge rounded-pill bg-success px-3 py-1 font-mono text-white">
                        <span class="pulse-dot me-1"></span>SISTEM AKTIF
                    </span>
                </div>

                <h2 class="display-6 fw-bold mb-2 font-brand text-white" style="letter-spacing: -0.5px;">
                    Selamat Bertugas, <?= htmlspecialchars(explode(' ', auth('name') ?? 'Admin')[0]) ?>! ☕
                </h2>
                <p class="text-white-50 lead fs-6 mb-4" style="max-width: 640px;">
                    Pantau realisasi bibit pohon di lereng Gunung Kerenceng, kurasi pesanan biji kopi <strong>andu</strong>, verifikasi transfer donasi, dan kelola tarif mesin ongkir mandiri dalam satu kendali terpusat.
                </p>

                <!-- Action Button Group -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="/admin/produk" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm text-dark font-brand d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i> Kelola Kopi & Produk
                    </a>
                    <a href="/admin/verifikasi-pembayaran" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 position-relative">
                        <i class="bi bi-patch-check"></i> Verifikasi Transfer
                        <?php if ($pendingPayments > 0): ?>
                            <span class="badge bg-danger rounded-pill"><?= $pendingPayments ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/cek-ongkir" target="_blank" class="btn btn-light bg-opacity-25 border-0 text-white rounded-pill px-3 py-2 fw-semibold d-none d-md-inline-flex align-items-center gap-1">
                        <i class="bi bi-truck text-warning"></i> Cek Ongkir Mandiri ↗
                    </a>
                </div>
            </div>

            <!-- Mini Widget Stats on Banner -->
            <div class="col-xl-4">
                <div class="p-3 rounded-4 bg-black bg-opacity-25 border border-white border-opacity-10 backdrop-blur">
                    <div class="small text-white-50 text-uppercase font-mono mb-2" style="font-size: 11px;">Ringkasan Operasional Lapangan:</div>
                    
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white border-opacity-10">
                        <span class="text-white small"><i class="bi bi-tree text-success me-2"></i>Pohon Tertanam</span>
                        <strong class="text-white font-mono"><?= number_format($totalTrees) ?> Bibit</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white border-opacity-10">
                        <span class="text-white small"><i class="bi bi-bag-check text-warning me-2"></i>Pesanan Toko</span>
                        <strong class="text-white font-mono"><?= number_format($totalOrders) ?> Order</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white border-opacity-10">
                        <span class="text-white small"><i class="bi bi-truck text-info me-2"></i>Mesin Ongkir</span>
                        <strong class="text-success font-mono">100% Mandiri</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-white small"><i class="bi bi-credit-card text-danger me-2"></i>Antrian Verifikasi</span>
                        <strong class="text-warning font-mono"><?= $pendingPayments ?> Tagihan</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 2. SIX STAT CARDS (PRO GLASSMORPHIC WITH SUBTLE GLOW) -->
<div class="row g-3 mb-4">
    
    <!-- Card 1: Total Pendapatan -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card-pro shadow-sm h-100">
            <div class="stat-glow" style="background: #22c55e;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrap" style="background: rgba(34, 197, 94, 0.12); color: #15803d;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <span class="stat-trend bg-success-subtle" style="color:#0a3622;">
                    <i class="bi bi-arrow-up-right"></i> Terverifikasi
                </span>
            </div>
            <div class="stat-val text-dark">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div>
            <div class="stat-lbl">Total Penerimaan Dana (Donasi & Toko)</div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: 85%;"></div>
            </div>
        </div>
    </div>

    <!-- Card 2: Pohon Tertanam -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card-pro shadow-sm h-100">
            <div class="stat-glow" style="background: #12382c;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrap" style="background: rgba(18, 56, 44, 0.12); color: #12382c;">
                    <i class="bi bi-tree-fill"></i>
                </div>
                <span class="stat-trend bg-success-subtle" style="color:#0a3622;">
                    <i class="bi bi-geo-alt"></i> Kerenceng
                </span>
            </div>
            <div class="stat-val text-dark"><?= number_format($totalTrees) ?> <span class="fs-6 fw-normal text-muted">Bibit</span></div>
            <div class="stat-lbl">Realisasi Penanaman Agroforestri</div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar" style="width: 48%; background-color: #12382c;"></div>
            </div>
        </div>
    </div>

    <!-- Card 3: Donasi Berhasil -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card-pro shadow-sm h-100">
            <div class="stat-glow" style="background: #ef4444;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrap" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <span class="stat-trend bg-danger-subtle text-danger">
                    <i class="bi bi-people"></i> Publik & Komunitas
                </span>
            </div>
            <div class="stat-val text-dark"><?= number_format($totalDonations) ?> <span class="fs-6 fw-normal text-muted">Aksi</span></div>
            <div class="stat-lbl">Donasi Pohon Berhasil Masuk</div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-danger" style="width: 72%;"></div>
            </div>
        </div>
    </div>

    <!-- Card 4: Pesanan Toko Kopi -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card-pro shadow-sm h-100">
            <div class="stat-glow" style="background: #f59e0b;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <span class="stat-trend bg-warning-subtle text-warning-emphasis">
                    <i class="bi bi-cup-hot"></i> andu Roastery
                </span>
            </div>
            <div class="stat-val text-dark"><?= number_format($totalOrders) ?> <span class="fs-6 fw-normal text-muted">Pesanan</span></div>
            <div class="stat-lbl">Total Pesanan Kopi & Merchandise</div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-warning" style="width: 60%;"></div>
            </div>
        </div>
    </div>

    <!-- Card 5: Peserta & Relawan -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card-pro shadow-sm h-100">
            <div class="stat-glow" style="background: #6366f1;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrap" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <span class="stat-trend bg-primary-subtle text-primary">
                    <i class="bi bi-person-check"></i> Relawan Aktif
                </span>
            </div>
            <div class="stat-val text-dark"><?= number_format($totalUsers) ?> <span class="fs-6 fw-normal text-muted">Akun</span></div>
            <div class="stat-lbl">Relawan Terdaftar di Platform</div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-primary" style="width: 65%;"></div>
            </div>
        </div>
    </div>

    <!-- Card 6: Antrian Verifikasi Pembayaran -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card-pro shadow-sm h-100" style="<?= ($pendingPayments > 0) ? 'border-color: rgba(239, 68, 68, 0.4) !important;' : '' ?>">
            <div class="stat-glow" style="background: #f97316;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrap" style="background: rgba(249, 115, 22, 0.12); color: #c2410c;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <?php if ($pendingPayments > 0): ?>
                    <span class="stat-trend bg-danger text-white">
                        <span class="pulse-dot me-1" style="background: #fff;"></span><?= $pendingPayments ?> Menunggu
                    </span>
                <?php else: ?>
                    <span class="stat-trend bg-success-subtle" style="color:#0a3622;">
                        <i class="bi bi-check-all"></i> Bersih
                    </span>
                <?php endif; ?>
            </div>
            <div class="stat-val <?= ($pendingPayments > 0) ? 'text-danger' : 'text-dark' ?>">
                <?= number_format($pendingPayments) ?> <span class="fs-6 fw-normal text-muted">Transaksi</span>
            </div>
            <div class="stat-lbl">Antrian Verifikasi Bukti Transfer</div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar <?= ($pendingPayments > 0) ? 'bg-danger' : 'bg-success' ?>" style="width: <?= min(100, $pendingPayments * 20) ?>%;"></div>
            </div>
        </div>
    </div>

</div>

<!-- 3. MAIN SECTION: GRAFIK PENDAPATAN & ANTRIAN PEMBAYARAN -->
<div class="row g-4 mb-4">
    
    <!-- Kolom Kiri (8 cols): Grafik Pendapatan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line text-success me-2"></i>Tren Pendapatan 6 Bulan Terakhir</h6>
                    <div class="text-muted small">Akumulasi donasi pohon dan penjualan kopi spesialti terverifikasi</div>
                </div>
                <span class="badge bg-light text-secondary border font-mono">
                    <i class="bi bi-calendar3 me-1"></i><?= date('Y') ?>
                </span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 260px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="card-footer bg-light border-top py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                <div class="text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i>Seluruh dana diverifikasi manual oleh rekening bank resmi.
                </div>
                <a href="/admin/laporan" class="text-success fw-bold text-decoration-none">
                    Buka Laporan Keuangan Lengkap →
                </a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan (4 cols): Antrian Bukti Pembayaran -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-credit-card-2-front text-danger me-2"></i>Antrian Verifikasi
                    </h6>
                    <div class="text-muted" style="font-size: 11px;">Bukti transfer yang baru diunggah</div>
                </div>
                <?php if ($pendingPayments > 0): ?>
                    <span class="badge bg-danger rounded-pill px-2 py-1 font-mono"><?= $pendingPayments ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentPending)): ?>
                    <div class="text-center py-5 text-muted">
                        <div class="stat-icon-wrap mx-auto mb-2" style="background: rgba(34, 197, 94, 0.1); color: #15803d; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Semua Pembayaran Beres</h6>
                        <p class="small text-muted mb-0">Tidak ada bukti transfer yang tertunda.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($recentPending, 0, 5) as $p): ?>
                            <a href="/admin/verifikasi-pembayaran/<?= $p['id'] ?>"
                               class="list-group-item list-group-item-action p-3 text-decoration-none border-bottom">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="badge bg-secondary-subtle text-secondary font-mono" style="font-size: 10px;">
                                                <?= htmlspecialchars($p['invoice_no']) ?>
                                            </span>
                                            <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">
                                                <?= ucfirst($p['payable_type']) ?>
                                            </span>
                                        </div>
                                        <div class="fw-bold text-dark small text-truncate" style="max-width: 170px;">
                                            <?= htmlspecialchars($p['payer']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <i class="bi bi-clock me-1"></i><?= time_ago($p['created_at']) ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success font-mono">
                                            Rp <?= number_format($p['grand_total'], 0, ',', '.') ?>
                                        </div>
                                        <span class="btn btn-sm btn-outline-success py-0 px-2 mt-1 rounded-pill" style="font-size: 11px;">
                                            Periksa →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-3 bg-light border-top text-center">
                        <a href="/admin/verifikasi-pembayaran" class="btn btn-sm btn-outline-dark rounded-pill w-100 fw-bold">
                            Lihat Semua Antrian (<?= $pendingPayments ?>) →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- 4. BOTTOM SECTION: AKTIVITAS TRANSAKSI TERBARU -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity text-info me-2"></i>Aktivitas Transaksi Terkini</h6>
            <div class="text-muted small">Pembaruan real-time seluruh donasi dan pemesanan kopi yang masuk</div>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/donasi" class="btn btn-sm btn-light border rounded-pill">Data Donasi</a>
            <a href="/admin/pesanan" class="btn btn-sm btn-light border rounded-pill">Data Pesanan</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">Jenis</th>
                        <th>ID / Kode Ref</th>
                        <th>Donatur / Pembeli</th>
                        <th>Nominal</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 130px;">Waktu</th>
                        <th style="width: 80px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada rekaman aktivitas terkini</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recentActivity as $act): ?>
                        <tr>
                            <td>
                                <?php if ($act['type'] === 'donasi'): ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 font-mono">
                                        <i class="bi bi-heart me-1"></i>DONASI
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-amber-subtle text-warning-emphasis border rounded-pill px-2 py-1 font-mono" style="background:#fff3cd;">
                                        <i class="bi bi-cup-hot me-1"></i>PESANAN
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code class="small text-dark fw-bold">#<?= $act['id'] ?></code>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small"><?= htmlspecialchars($act['label']) ?></div>
                            </td>
                            <td>
                                <span class="fw-bold font-mono text-dark">
                                    Rp <?= number_format($act['amount'], 0, ',', '.') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge st-<?= $act['status'] ?> rounded-pill px-2 py-1">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $act['status']))) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= time_ago($act['created_at']) ?></span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="<?= ($act['type'] === 'donasi') ? '/admin/donasi' : '/admin/pesanan' ?>" class="btn btn-sm btn-light border py-1 px-2 rounded-circle">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js 4 Initialization with Sleek Gradient -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const rawLabels = <?= json_encode(array_column($revenueChart, 'label')) ?>;
const rawData   = <?= json_encode(array_map(fn($r) => (float)$r['total'], $revenueChart)) ?>;

// Default dummy points if chart data is low for demonstration aesthetics
const labels = rawLabels.length > 0 ? rawLabels : ['Jun 2026', 'Jul 2026', 'Agt 2026', 'Sep 2026', 'Okt 2026', 'Nov 2026'];
const data = rawData.length > 0 ? rawData : [4500000, 7200000, 11800000, 16400000, 22100000, 28950000];

// Create smooth gradient fill
const gradient = ctx.createLinearGradient(0, 0, 0, 250);
gradient.addColorStop(0, 'rgba(18, 56, 44, 0.4)');
gradient.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: data,
            borderColor: '#12382c',
            borderWidth: 3,
            backgroundColor: gradient,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#22c55e',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                titleColor: '#bfd254',
                bodyColor: '#ffffff',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return ' ' + Number(context.parsed.y).toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        });
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 11 } }
            },
            y: {
                border: { dash: [4, 4] },
                grid: { color: '#f1f5f9' },
                ticks: {
                    color: '#64748b',
                    font: { family: 'Plus Jakarta Sans', size: 11 },
                    callback: function(val) {
                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                        return 'Rp ' + Number(val).toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
