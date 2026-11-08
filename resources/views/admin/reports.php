<?php
/**
 * View: admin/reports.php — Pusat Laporan Finansial & Dampak Ekologis (Pro Analytics)
 */

$totalRevenueFmt = 'Rp ' . number_format($totalRevenue ?? 0, 0, ',', '.');
$totalTreesFmt   = number_format($totalTrees ?? 0);
$totalTxFmt      = number_format($totalTx ?? 0);
$totalDonorsFmt  = number_format($totalDonors ?? 0);
$totalCO2Fmt     = number_format($totalCO2 ?? 0, 1, ',', '.') . ' kg';

// Estimasi ekuivalen ekologis
$carKmEquivalent = round(($totalCO2 ?? 0) / 0.192); // 1 km mobil rata-rata = ~0.192 kg CO2
$humanO2Days     = round(($totalTrees ?? 0) * 1.5 * 365); // Estimasi suplai O2 harian
?>

<style>
/* ── Hero ── */
.rep-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 55%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(6, 21, 16, 0.2);
}
.rep-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -60px;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.2) 0%, transparent 70%);
    pointer-events: none;
}
.rep-hero h4 {
    color: #fff;
    font-weight: 800;
    font-size: 1.45rem;
    margin: 0 0 6px;
}
.rep-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    margin: 0;
}

/* ── KPI Grid ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 1200px) { .kpi-row { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px) { .kpi-row { grid-template-columns: repeat(2, 1fr); } }
.kpi-card {
    background: #fff;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.25s ease;
}
.kpi-card:hover {
    box-shadow: 0 8px 24px rgba(18, 56, 44, 0.09);
    transform: translateY(-2px);
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.kpi-val {
    font-size: 1.3rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 2px;
}
.kpi-lbl {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* ── Cards Pro ── */
.rep-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    height: 100%;
}
.rep-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* ── Leaderboard Medal Badges ── */
.rank-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.78rem;
}
.rank-gold   { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.rank-silver { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.rank-bronze { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
.rank-normal { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

/* ── Impact Box ── */
.impact-banner {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 22px;
}

.btn-hero-action {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 0.84rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    backdrop-filter: blur(4px);
    transition: all 0.2s;
    text-decoration: none;
    cursor: pointer;
}
.btn-hero-action:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
}
</style>

<!-- ══════════════════════════════════════════════════════════════
     PAGE HERO
══════════════════════════════════════════════════════════════ -->
<div class="rep-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white bg-opacity-15 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <i class="bi bi-graph-up me-1"></i>BUSINESS INTELLIGENCE &amp; AUDIT
                </span>
                <span class="badge bg-success bg-opacity-75 text-white font-mono px-3 py-1" style="font-size:11px;">
                    12 Bulan Terakhir
                </span>
            </div>
            <h4><i class="bi bi-bar-chart-fill me-2 text-success"></i>Laporan Analisis &amp; Audit Dampak</h4>
            <p>Evaluasi menyeluruh arus kas operasional, realisasi bibit pohon konservasi, dan audit penyerapan karbon dioksida.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn-hero-action" onclick="window.print()">
                <i class="bi bi-printer"></i>Cetak Laporan
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     5 KPI CARDS ROW
══════════════════════════════════════════════════════════════ -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">
            <i class="bi bi-wallet2"></i>
        </div>
        <div>
            <div class="kpi-val text-dark"><?= $totalRevenueFmt ?></div>
            <div class="kpi-lbl">Total Omzet Finansial</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-primary"><?= $totalTxFmt ?></div>
            <div class="kpi-lbl">Transaksi Berhasil</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#16a34a;">
            <i class="bi bi-tree-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-success"><?= $totalTreesFmt ?></div>
            <div class="kpi-lbl">Pohon Tertanam</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#ea580c;"><?= $totalDonorsFmt ?></div>
            <div class="kpi-lbl">Donatur Terdaftar</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
            <i class="bi bi-cloud-check-fill"></i>
        </div>
        <div>
            <div class="kpi-val" style="color:#9333ea;"><?= $totalCO2Fmt ?></div>
            <div class="kpi-lbl">Serapan CO₂ 10 Thn</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     ECOLOGICAL IMPACT BANNER
══════════════════════════════════════════════════════════════ -->
<div class="impact-banner shadow-sm">
    <div class="row align-items-center g-3">
        <div class="col-md-7">
            <h6 class="fw-bold text-success mb-1">
                <i class="bi bi-globe-americas me-2"></i>Dampak Ekologis Setara di Alam Bebas
            </h6>
            <p class="text-secondary small mb-0">
                Pohon yang tertanam setara dengan menetralkan jejak karbon kendaraan bermotor sejauh 
                <strong class="text-dark font-mono"><?= number_format($carKmEquivalent) ?> kilometer</strong>, 
                serta menghasilkan oksigen bersih untuk <strong class="text-dark font-mono"><?= number_format($humanO2Days) ?> hari</strong> kebutuhan napas manusia.
            </p>
        </div>
        <div class="col-md-5 text-md-end">
            <span class="badge bg-success px-3 py-2 font-mono">
                <i class="bi bi-patch-check-fill me-1"></i>VERIFIED REFORESTATION
            </span>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     CHARTS ROW 1: REVENUE TREND & REVENUE TYPE
══════════════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <!-- Trend Finansial -->
    <div class="col-lg-8">
        <div class="rep-card">
            <div class="rep-card-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-graph-up text-success me-2"></i>Tren Pendapatan &amp; Donasi (12 Bulan)</h6>
                    <small class="text-muted">Pergerakan total dana masuk dan frekuensi transaksi bulanan</small>
                </div>
            </div>
            <div class="p-3">
                <canvas id="monthlyChart" height="130"></canvas>
            </div>
        </div>
    </div>

    <!-- Komposisi Pendapatan -->
    <div class="col-lg-4">
        <div class="rep-card">
            <div class="rep-card-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>Sumber Pendapatan</h6>
                    <small class="text-muted">Donasi Pohon vs Toko Kopi</small>
                </div>
            </div>
            <div class="p-3 d-flex flex-column align-items-center justify-content-center" style="min-height: 240px;">
                <canvas id="revenueTypeChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     CHARTS ROW 2: SPESIES DISTRIBUTION & ORDER STATUS
══════════════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <!-- Sebaran Spesies Pohon -->
    <div class="col-lg-6">
        <div class="rep-card">
            <div class="rep-card-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-flower1 text-success me-2"></i>Distribusi Spesies Pohon</h6>
                    <small class="text-muted">Jumlah bibit pohon berdasarkan jenis</small>
                </div>
            </div>
            <div class="p-3">
                <canvas id="speciesChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Pesanan Kopi/Produk -->
    <div class="col-lg-6">
        <div class="rep-card">
            <div class="rep-card-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bag-check text-warning me-2"></i>Status Pesanan Toko Kopi</h6>
                    <small class="text-muted">Konversi transaksi pesanan pelanggan</small>
                </div>
            </div>
            <div class="p-3">
                <canvas id="orderStatusChart" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     TABLES ROW: TOP DONORS & SPASIAL LOCATIONS
══════════════════════════════════════════════════════════════ -->
<div class="row g-4">
    <!-- 10 Donatur Terbesar -->
    <div class="col-lg-6">
        <div class="rep-card">
            <div class="rep-card-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>10 Donatur Paling Berdampak</h6>
                    <small class="text-muted">Leaderboard kontributor penghijauan teratas</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th style="width:45px;">Peringkat</th>
                            <th>Nama Donatur</th>
                            <th>Total Donasi</th>
                            <th>Pohon</th>
                            <th class="text-center">Frekuensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topDonors)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada catatan donasi masuk.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($topDonors as $i => $d): 
                            $badgeClass = $i === 0 ? 'rank-gold' : ($i === 1 ? 'rank-silver' : ($i === 2 ? 'rank-bronze' : 'rank-normal'));
                        ?>
                            <tr>
                                <td><div class="rank-badge <?= $badgeClass ?>"><?= $i + 1 ?></div></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($d['name']) ?></div>
                                    <div class="text-muted font-mono" style="font-size:11px;"><?= htmlspecialchars($d['donor_email'] ?? '') ?></div>
                                </td>
                                <td><strong class="text-success font-mono">Rp <?= number_format($d['total'], 0, ',', '.') ?></strong></td>
                                <td><span class="badge bg-light text-dark border font-mono"><?= number_format($d['trees']) ?> pohon</span></td>
                                <td class="text-center"><span class="badge bg-info-subtle text-info font-mono"><?= $d['donations'] ?>x</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sebaran Pohon per Lokasi -->
    <div class="col-lg-6">
        <div class="rep-card">
            <div class="rep-card-header">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt text-success me-2"></i>Realisasi per Kawasan Konservasi</h6>
                    <small class="text-muted">Capaian penanaman pohon di tiap titik</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Kawasan Lokasi</th>
                            <th>Wilayah</th>
                            <th style="width:170px;">Target &amp; Realisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($treesByLocation)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada data penanaman di lokasi.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($treesByLocation as $loc): 
                            $target = (int)$loc['total_trees_target'];
                            $planted = (int)$loc['trees_planted'];
                            $pct = $target > 0 ? min(100, round(($planted / $target) * 100)) : 0;
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($loc['name']) ?></div>
                                </td>
                                <td>
                                    <div class="small text-muted"><?= htmlspecialchars($loc['city']) ?>, <?= htmlspecialchars($loc['province']) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-bold text-success"><?= number_format($planted) ?></span>
                                        <span class="text-muted">/ <?= number_format($target) ?></span>
                                    </div>
                                    <div class="progress" style="height:6px; border-radius:4px;">
                                        <div class="progress-bar bg-success" style="width:<?= $pct ?>%;"></div>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size:10px;"><?= $pct ?>% tercapai</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     SCRIPTS & CHART.JS ENGINE
══════════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// 1. Line Chart: Pendapatan Bulanan
const monthlyLabels = <?= json_encode(array_column($monthlyRevenue, 'label')) ?>;
const monthlyTotals = <?= json_encode(array_map(fn($r) => (float)$r['total'], $monthlyRevenue)) ?>;
const monthlyCounts = <?= json_encode(array_map(fn($r) => (int)$r['tx_count'], $monthlyRevenue)) ?>;

new Chart(document.getElementById('monthlyChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: [
            {
                label: 'Pendapatan (Rp)',
                data: monthlyTotals,
                borderColor: '#15803d',
                backgroundColor: 'rgba(21, 128, 61, 0.12)',
                tension: 0.35,
                fill: true,
                yAxisID: 'y'
            },
            {
                label: 'Transaksi',
                data: monthlyCounts,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                borderDash: [5, 5],
                tension: 0.35,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        if (ctx.datasetIndex === 0) {
                            return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                        }
                        return ctx.raw + ' Transaksi';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => 'Rp ' + (val >= 1000000 ? (val/1000000) + 'Jt' : (val/1000) + 'Rb')
                }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                grid: { drawOnChartArea: false }
            }
        }
    }
});

// 2. Donut Chart: Komposisi Pendapatan
const revTypeLabels = <?= json_encode(array_map(fn($r) => ucfirst(str_replace('_',' ', $r['payable_type'] ?? 'Lainnya')), $revenueByType)) ?>;
const revTypeTotals = <?= json_encode(array_map(fn($r) => (float)$r['total'], $revenueByType)) ?>;

new Chart(document.getElementById('revenueTypeChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: revTypeLabels.length ? revTypeLabels : ['Donasi', 'Belanja Kopi'],
        datasets: [{
            data: revTypeTotals.length ? revTypeTotals : [1, 0],
            backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#94a3b8'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// 3. Bar Chart: Distribusi Spesies Pohon
const speciesLabels = <?= json_encode(array_column($treesBySpecies, 'name')) ?>;
const speciesCounts = <?= json_encode(array_map(fn($s) => (int)$s['count'], $treesBySpecies)) ?>;

new Chart(document.getElementById('speciesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: speciesLabels,
        datasets: [{
            label: 'Bibit Ditanam',
            data: speciesCounts,
            backgroundColor: '#15803d',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// 4. Bar Chart: Status Pesanan Toko
const orderLabels = <?= json_encode(array_map(fn($o) => ucfirst(str_replace('_',' ', $o['status'])), $orderStats)) ?>;
const orderCounts = <?= json_encode(array_map(fn($o) => (int)$o['cnt'], $orderStats)) ?>;

new Chart(document.getElementById('orderStatusChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: orderLabels,
        datasets: [{
            label: 'Jumlah Pesanan',
            data: orderCounts,
            backgroundColor: ['#f59e0b', '#3b82f6', '#6366f1', '#22c55e', '#ef4444'],
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
</script>
