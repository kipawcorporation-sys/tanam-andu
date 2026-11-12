<?php
/**
 * View: Halaman Transparansi Publik & Dampak Ekologis — ENHANCED
 * Path: resources/views/public/transparency.php
 */

$totalRaised        = (float)($donationStats['total_raised'] ?? 0);
$totalTxCount       = (int)($donationStats['total_transactions'] ?? 0);
$totalDonatedTrees  = (int)($donationStats['total_trees_donated'] ?? 0);
$totalPlantedTrees  = (int)($treeStats['total_planted'] ?? 0);
$totalGrowingTrees  = (int)($treeStats['total_growing'] ?? 0);

$fundPercent = $targetAmount > 0 ? min(100, round(($totalRaised / $targetAmount) * 100, 1)) : 0;
$treePercent = $targetTrees > 0 ? min(100, round(($totalPlantedTrees / $targetTrees) * 100, 1)) : 0;

$co2Kg  = (float)($ecoStats['total_co2_kg'] ?? 0);
$co2Ton = round($co2Kg / 1000, 2);
$o2Kg   = (float)($ecoStats['total_o2_kg'] ?? 0);
$o2Ton  = round($o2Kg / 1000, 2);

$carKmEquiv      = $co2Kg > 0 ? number_format($co2Kg / 0.17, 0, ',', '.') : '0';
$breathingPeople = $o2Kg  > 0 ? number_format($o2Kg  / 700,  0, ',', '.') : '0';

$trendGranularity = $trendGranularity ?? 'monthly';
$trendTitle       = $trendGranularity === 'daily' ? 'Realisasi Dana &amp; Pohon per Hari' : 'Realisasi Dana &amp; Pohon per Bulan';
$trendSubtitle    = $trendGranularity === 'daily' ? 'Rincian per hari dalam bulan aktif' : 'Tren bulanan selama 12 bulan terakhir';

$trendLabels  = json_encode(array_column($donationTrends ?? [], 'month_label'), JSON_UNESCAPED_UNICODE);
$trendAmounts = json_encode(array_map(fn($r) => (float)$r['sum_amount'],  $donationTrends ?? []));
$trendTrees   = json_encode(array_map(fn($r) => (int)$r['sum_trees'],     $donationTrends ?? []));
$locJson      = $locationsJson ?? '[]';

$lastUpdated = date('d M Y, H:i') . ' WIB';
?>
<?php $extraCss = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
/* ================================================================
   TRANSPARENCY PAGE — ENHANCED
================================================================ */
.tp-hero {
    background: linear-gradient(135deg, #061510 0%, #12382c 55%, #1a4d3a 100%);
    position: relative; overflow: hidden;
}
.tp-hero::before {
    content: "";
    position: absolute; inset: 0;
    background-image:
        radial-gradient(ellipse at 10% 60%, rgba(34,197,94,.12) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 20%, rgba(191,210,84,.10) 0%, transparent 50%);
    pointer-events: none;
}
.tp-hero .topo-lines {
    position: absolute; inset: 0; opacity: .05;
    background-image:
        repeating-linear-gradient(0deg, transparent, transparent 24px, rgba(255,255,255,.5) 25px),
        repeating-linear-gradient(90deg, transparent, transparent 24px, rgba(255,255,255,.5) 25px);
}
.pulse-dot {
    display: inline-block; width: 10px; height: 10px; border-radius: 50%;
    background: #22c55e; box-shadow: 0 0 0 0 rgba(34,197,94,.5);
    animation: pulse-anim 1.6s infinite;
}
@keyframes pulse-anim {
    0%  { box-shadow: 0 0 0 0 rgba(34,197,94,.5); }
    70% { box-shadow: 0 0 0 10px rgba(34,197,94,0); }
    100%{ box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
/* KPI float */
.tp-kpi-row { margin-top: -55px; position: relative; z-index: 10; }
.tp-kpi-card {
    border: 0; border-radius: 20px;
    box-shadow: 0 8px 32px rgba(6,21,16,.13);
    transition: transform .22s, box-shadow .22s;
}
.tp-kpi-card:hover { transform: translateY(-5px); box-shadow: 0 18px 48px rgba(6,21,16,.18); }
.tp-kpi-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.tp-kpi-value { font-size: 1.5rem; font-weight: 800; line-height: 1.1; }
/* badges */
.tp-badge {
    display: inline-block; padding: 4px 14px; border-radius: 999px;
    font-size: 11.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase;
}
.tp-badge-green { background: rgba(34,197,94,.1); color: #15803d; border: 1px solid rgba(34,197,94,.22); }
.tp-badge-blue  { background: rgba(59,130,246,.09); color: #1d4ed8; border: 1px solid rgba(59,130,246,.2); }
.tp-badge-amber { background: rgba(245,158,11,.09); color: #b45309; border: 1px solid rgba(245,158,11,.2); }
/* map */
#transMap {
    height: 410px; border-radius: 18px; z-index: 1;
    box-shadow: 0 6px 24px rgba(0,0,0,.12);
}
.tp-map-pin {
    display: flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 50%;
    background: #22c55e; color: white; font-size: 15px;
    border: 3px solid white; box-shadow: 0 4px 14px rgba(0,0,0,.28);
    cursor: pointer; transition: transform .18s;
}
.tp-map-pin:hover { transform: scale(1.18); }
.leaflet-popup-content-wrapper { border-radius: 14px !important; padding: 4px !important; box-shadow: 0 10px 28px rgba(0,0,0,.14) !important; }
.tp-popup-prog { height: 6px; border-radius: 999px; background: #e9ecef; overflow: hidden; }
.tp-popup-prog-bar { height: 100%; border-radius: 999px; }
/* loc cards */
.tp-loc-card {
    border: 0; border-radius: 20px; overflow: hidden;
    transition: transform .22s, box-shadow .22s;
    box-shadow: 0 4px 18px rgba(0,0,0,.08);
}
.tp-loc-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,.13); }
/* ledger */
.tp-ledger { max-height: 530px; overflow-y: auto; }
.tp-ledger::-webkit-scrollbar { width: 5px; }
.tp-ledger::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 999px; }
.tp-ledger-row {
    padding: 12px 0; border-bottom: 1px solid #f1f5f9;
    transition: background .15s;
}
.tp-ledger-row:last-child { border-bottom: none; }
.tp-ledger-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: #dcfce7; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 15px; color: #15803d; flex-shrink: 0;
}
.tp-ledger-invoice { font-family: "Space Mono", monospace; font-size: 10px; color: #94a3b8; }
/* species */
.tp-species-pill {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; border-radius: 12px;
    background: #f8fafc; border: 1px solid #e9ecef;
    transition: all .18s;
}
.tp-species-pill:hover { background: #dcfce7; border-color: #bbf7d0; }
/* eco grid */
.tp-eco-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 14px; }
@media(max-width:575px){ .tp-eco-grid { grid-template-columns: 1fr; } }
.tp-eco-item { padding: 18px; border-radius: 16px; border: 1px solid #e9ecef; background: #fff; }
/* verify callout */
.tp-verify-callout {
    background: linear-gradient(135deg, #0f2d20 0%, #12382c 100%);
    border-radius: 24px; position: relative; overflow: hidden;
}
/* CTA */
.tp-cta {
    background: linear-gradient(135deg, #198754 0%, #0d5330 100%);
    border-radius: 28px; position: relative; overflow: hidden;
}
.tp-cta::before {
    content: ""; position: absolute; top: -60px; right: -60px;
    width: 250px; height: 250px; border-radius: 50%;
    background: rgba(255,255,255,.05); pointer-events: none;
}
/* audit banner */
.tp-audit-banner { background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px; }
.hover-green:hover { color: #16a34a !important; }
.ring-wrap { position: relative; display: inline-flex; align-items: center; justify-content: center; }
.ring-pct { position: absolute; font-size: 12px; font-weight: 800; color: #15803d; }

/* Utilities fix untuk flex item text-truncate & overflow */
.min-w-0 { min-width: 0 !important; }
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
'; ?>

<!-- HERO -->
<section class="tp-hero py-5 text-white position-relative" style="padding-bottom:100px !important;">
    <div class="topo-lines"></div>
    <div class="container py-4 position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="pulse-dot"></span>
                    <span style="font-size:12px;letter-spacing:1.4px;font-weight:700;text-transform:uppercase;color:#bbf7d0;">
                        Diperbarui <?= $lastUpdated ?>
                    </span>
                </div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3"
                     style="background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.22);">
                    <i class="bi bi-shield-check" style="color:#bfd254;"></i>
                    <span style="font-size:11px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">
                        Audit &amp; Transparansi 100% Terbuka
                    </span>
                </div>
                <h1 class="display-5 fw-bold font-poster mb-3 lh-sm">
                    Laporan Dampak Hijau<br>
                    <span style="color:#bfd254;">&amp; Realisasi Amanah</span> Donatur
                </h1>
                <p class="lead mb-4" style="color:rgba(255,255,255,.75);max-width:580px;">
                    Setiap rupiah dan setiap bibit yang Anda percayakan dicatat, diverifikasi GPS, dan dipantau
                    pertumbuhannya secara real-time untuk kelestarian bumi nusantara.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= url('/peta-lokasi') ?>" class="btn btn-trail fw-bold rounded-pill px-4 py-2 shadow-sm font-poster">
                        <i class="bi bi-map-fill me-1"></i> Peta Sebaran Pohon
                    </a>
                    <a href="<?= url('/transparansi/export-csv') ?>" class="btn btn-outline-light fw-semibold rounded-pill px-4 py-2">
                        <i class="bi bi-download me-1"></i> Unduh Laporan CSV
                    </a>
                    <a href="<?= url('/verifikasi-sertifikat') ?>" class="btn btn-outline-light fw-semibold rounded-pill px-4 py-2">
                        <i class="bi bi-patch-check-fill me-1"></i> Cek Sertifikat
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="p-4 rounded-4 shadow-lg"
                     style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);backdrop-filter:blur(10px);">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="fs-2 fw-black font-poster" style="color:#bfd254;"><?= $totalTxCount ?></div>
                            <div class="small" style="color:rgba(255,255,255,.7);">Transaksi Tervalidasi</div>
                        </div>
                        <div class="col-6">
                            <div class="fs-2 fw-black font-poster" style="color:#86efac;"><?= number_format($totalDonatedTrees, 0, ',', '.') ?></div>
                            <div class="small" style="color:rgba(255,255,255,.7);">Pohon Diamanahkan</div>
                        </div>
                        <div class="col-6">
                            <div class="fs-2 fw-black font-poster" style="color:#67e8f9;"><?= count($locations) ?></div>
                            <div class="small" style="color:rgba(255,255,255,.7);">Kawasan Konservasi</div>
                        </div>
                        <div class="col-6">
                            <div class="fs-2 fw-black font-poster" style="color:#fca5a5;"><?= $certCount ?></div>
                            <div class="small" style="color:rgba(255,255,255,.7);">Sertifikat Diterbitkan</div>
                        </div>
                    </div>
                    <div class="border-top mt-3 pt-3" style="border-color:rgba(255,255,255,.15) !important;">
                        <div class="d-flex justify-content-between small mb-1" style="color:rgba(255,255,255,.65);">
                            <span>Realisasi Dana</span>
                            <strong style="color:#bfd254;"><?= $fundPercent ?>%</strong>
                        </div>
                        <div style="height:6px;border-radius:999px;background:rgba(255,255,255,.18);">
                            <div style="height:100%;width:<?= $fundPercent ?>%;border-radius:999px;background:linear-gradient(90deg,#22c55e,#bfd254);"></div>
                        </div>
                        <div class="small mt-2" style="color:rgba(255,255,255,.5);">
                            Target: Rp 500.000.000 &bull; Terkumpul: <?= format_rupiah($totalRaised) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">

<!-- KPI CARDS -->
<div class="tp-kpi-row row g-3 mb-5">
    <div class="col-lg col-md-6">
        <div class="card tp-kpi-card p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="tp-kpi-icon" style="background:#dcfce7;"><i class="bi bi-cash-stack" style="color:#15803d;"></i></div>
                <span class="text-muted fw-semibold text-uppercase" style="letter-spacing:.8px;font-size:10.5px;">Dana Terkumpul</span>
            </div>
            <div class="tp-kpi-value text-dark"><?= format_rupiah($totalRaised) ?></div>
            <div class="d-flex justify-content-between small text-muted mt-2 mb-1">
                <span>Target Rp 500 Juta</span>
                <span class="fw-bold text-success"><?= $fundPercent ?>%</span>
            </div>
            <div class="progress" style="height:5px;"><div class="progress-bar bg-success rounded-pill" style="width:<?= $fundPercent ?>%;"></div></div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="card tp-kpi-card p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="tp-kpi-icon" style="background:#dbeafe;"><i class="bi bi-tree-fill" style="color:#1d4ed8;"></i></div>
                <span class="text-muted fw-semibold text-uppercase" style="letter-spacing:.8px;font-size:10.5px;">Pohon Tertanam</span>
            </div>
            <div class="tp-kpi-value text-dark"><?= number_format($totalPlantedTrees, 0, ',', '.') ?></div>
            <div class="d-flex justify-content-between small text-muted mt-2 mb-1">
                <span>Target <?= number_format($targetTrees, 0, ',', '.') ?> pohon</span>
                <span class="fw-bold text-primary"><?= $treePercent ?>%</span>
            </div>
            <div class="progress" style="height:5px;"><div class="progress-bar bg-primary rounded-pill" style="width:<?= $treePercent ?>%;"></div></div>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="card tp-kpi-card p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="tp-kpi-icon" style="background:#fef9c3;"><i class="bi bi-cloud-haze2" style="color:#a16207;"></i></div>
                <span class="text-muted fw-semibold text-uppercase" style="letter-spacing:.8px;font-size:10.5px;">Serapan CO&#x2082;/Thn</span>
            </div>
            <div class="tp-kpi-value text-dark"><?= number_format($co2Kg, 0, ',', '.') ?> <span class="fs-6 fw-normal text-muted">kg</span></div>
            <p class="small text-muted mb-0 mt-2">Setara <strong><?= $co2Ton ?> ton</strong> emisi karbon terserap</p>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="card tp-kpi-card p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="tp-kpi-icon" style="background:#cffafe;"><i class="bi bi-wind" style="color:#0e7490;"></i></div>
                <span class="text-muted fw-semibold text-uppercase" style="letter-spacing:.8px;font-size:10.5px;">Produksi O&#x2082;/Thn</span>
            </div>
            <div class="tp-kpi-value text-dark"><?= number_format($o2Kg, 0, ',', '.') ?> <span class="fs-6 fw-normal text-muted">kg</span></div>
            <p class="small text-muted mb-0 mt-2">Memasok O&#x2082; bersih untuk <strong><?= $breathingPeople ?> orang</strong>/tahun</p>
        </div>
    </div>
    <div class="col-lg col-md-6">
        <div class="card tp-kpi-card p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="tp-kpi-icon" style="background:#fce7f3;"><i class="bi bi-people-fill" style="color:#9d174d;"></i></div>
                <span class="text-muted fw-semibold text-uppercase" style="letter-spacing:.8px;font-size:10.5px;">Relawan Hadir</span>
            </div>
            <div class="tp-kpi-value text-dark"><?= number_format($volunteersCount, 0, ',', '.') ?></div>
            <p class="small text-muted mb-0 mt-2">Partisipan kehadiran nyata di lapangan dari seluruh kegiatan penanaman</p>
        </div>
    </div>
</div>

<!-- DAMPAK EKOLOGIS + ALOKASI ANGGARAN -->
<div class="row g-4 mb-5 align-items-start">
    <div class="col-lg-5">
        <span class="tp-badge tp-badge-green mb-3">Dampak Nyata</span>
        <h2 class="fw-bold text-dark mb-3">Apa yang Sudah Kita Capai Bersama?</h2>
        <p class="text-secondary mb-4">
            Angka-angka ini dihitung berdasarkan data pohon aktif di lapangan, menggunakan standar IPCC
            dan Kementerian LHK RI — bukan sekadar estimasi.
        </p>
        <div class="tp-eco-grid">
            <div class="tp-eco-item text-center">
                <div class="fs-1 mb-1">&#x1F697;</div>
                <div class="fw-bold text-dark fs-5"><?= $carKmEquiv ?> km</div>
                <div class="small text-muted">Perjalanan mobil diimbangi (ekivalensi CO&#x2082;)</div>
            </div>
            <div class="tp-eco-item text-center">
                <div class="fs-1 mb-1">&#x1F32C;&#xFE0F;</div>
                <div class="fw-bold text-dark fs-5"><?= $breathingPeople ?> orang</div>
                <div class="small text-muted">Dapat bernapas setahun dari O&#x2082; yang diproduksi</div>
            </div>
            <div class="tp-eco-item text-center">
                <div class="fs-1 mb-1">&#x1F333;</div>
                <div class="fw-bold text-dark fs-5"><?= number_format($totalGrowingTrees, 0, ',', '.') ?> pohon</div>
                <div class="small text-muted">Status &ldquo;Tumbuh&rdquo; &mdash; hidup dan berkembang</div>
            </div>
            <div class="tp-eco-item text-center">
                <div class="fs-1 mb-1">&#x1F3D4;&#xFE0F;</div>
                <div class="fw-bold text-dark fs-5"><?= count($locations) ?> kawasan</div>
                <div class="small text-muted">Area konservasi aktif di seluruh Indonesia</div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 rounded-4 shadow-sm p-4" style="background:#f8faf9;">
            <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart-fill text-success"></i> Alokasi Dana Donasi
            </h5>
            <p class="small text-muted mb-3">Ke mana setiap rupiah Anda disalurkan?</p>
            <div class="row align-items-center g-3">
                <div class="col-md-5 text-center">
                    <canvas id="donutChart" width="200" height="200"></canvas>
                </div>
                <div class="col-md-7">
                    <?php
                    $allocs = [
                        ['label'=>'Bibit &amp; Penanaman', 'icon'=>'bi-seedling', 'clr'=>'#22c55e', 'bg'=>'bg-success', 'pct'=>70,
                         'note'=>'Bibit, polibag, ajir, pupuk kandang, logistik lapangan'],
                        ['label'=>'Pemeliharaan &amp; Monitoring', 'icon'=>'bi-shield-shaded', 'clr'=>'#3b82f6', 'bg'=>'bg-primary', 'pct'=>15,
                         'note'=>'Penyulaman, penyiangan, foto tracking berkala'],
                        ['label'=>'Pemberdayaan Petani Lokal', 'icon'=>'bi-people-fill', 'clr'=>'#f59e0b', 'bg'=>'bg-warning', 'pct'=>10,
                         'note'=>'Upah penjaga, pelatihan agroforestri, bibit pekarangan'],
                        ['label'=>'Operasional &amp; Audit', 'icon'=>'bi-gear-fill', 'clr'=>'#94a3b8', 'bg'=>'bg-secondary', 'pct'=>5,
                         'note'=>'Server, audit publik, e-sertifikat, verifikasi'],
                    ];
                    foreach ($allocs as $a): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1 fw-semibold small">
                            <span><i class="bi <?= $a['icon'] ?> me-1" style="color:<?= $a['clr'] ?>;"></i><?= $a['label'] ?></span>
                            <span style="color:<?= $a['clr'] ?>;"><?= $a['pct'] ?>%</span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar <?= $a['bg'] ?> rounded-pill" style="width:<?= $a['pct'] ?>%;"></div>
                        </div>
                        <div class="text-muted" style="font-size:11px;margin-top:4px;"><?= $a['note'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TREN REALISASI -->
<div class="card border-0 rounded-4 shadow-sm p-4 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <span class="tp-badge tp-badge-blue mb-2">
                <?= $trendGranularity === 'daily' ? 'Rincian Harian' : 'Tren Bulanan' ?>
            </span>
            <h5 class="fw-bold text-dark mb-0"><?= $trendTitle ?></h5>
            <p class="small text-muted mb-0"><?= $trendSubtitle ?></p>
        </div>
        <div class="d-flex gap-3 small text-muted align-items-center">
            <span><span class="d-inline-block rounded-pill me-1" style="width:14px;height:4px;background:#22c55e;vertical-align:middle;"></span>Dana (Rp)</span>
            <span><span class="d-inline-block rounded-pill me-1" style="width:14px;height:4px;background:#3b82f6;vertical-align:middle;"></span>Pohon</span>
        </div>
    </div>
    <?php if (empty($donationTrends)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-bar-chart-line fs-1 d-block mb-2 opacity-25"></i>
        <span>Belum ada data donasi untuk ditampilkan.</span>
    </div>
    <?php else: ?>
    <canvas id="trendChart" height="130"></canvas>
    <?php endif; ?>
</div>

<!-- PETA INTERAKTIF -->
<div class="mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
        <div>
            <span class="tp-badge tp-badge-green mb-2">Wilayah Konservasi</span>
            <h2 class="fw-bold text-dark mb-0">Peta Sebaran Lokasi Penanaman</h2>
        </div>
        <a href="<?= url('/peta-lokasi') ?>" class="btn btn-outline-success fw-semibold rounded-pill">
            <i class="bi bi-fullscreen me-1"></i> Buka Peta Penuh
        </a>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div id="transMap"></div>
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
            <?php foreach ($locations as $loc):
                $planted = (int)($loc['actual_planted_count'] ?? $loc['total_trees_planted']);
                $target  = (int)$loc['total_trees_target'];
                $pct     = $target > 0 ? min(100, round(($planted / $target) * 100, 1)) : 0;
                $clr     = $pct >= 70 ? '#22c55e' : ($pct >= 30 ? '#f59e0b' : '#3b82f6');
                $r = 20; $circ = 2 * 3.14159 * $r;
                $dash = $circ; $offset = (1 - $pct/100) * $circ;
            ?>
            <div class="card tp-loc-card p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="ring-wrap flex-shrink-0">
                        <svg width="52" height="52" viewBox="0 0 52 52">
                            <circle cx="26" cy="26" r="<?= $r ?>" fill="none" stroke="#e9ecef" stroke-width="5"/>
                            <circle cx="26" cy="26" r="<?= $r ?>" fill="none" stroke="<?= $clr ?>" stroke-width="5"
                                stroke-dasharray="<?= round($dash) ?>"
                                stroke-dashoffset="<?= round($offset) ?>"
                                stroke-linecap="round"
                                transform="rotate(-90 26 26)"/>
                        </svg>
                        <span class="ring-pct" style="color:<?= $clr ?>;"><?= $pct ?>%</span>
                    </div>
                    <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                        <h6 class="fw-bold text-dark mb-1 lh-sm"><?= htmlspecialchars($loc['name']) ?></h6>
                        <div class="text-muted" style="font-size:11.5px;">
                            <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($loc['city'] . ', ' . $loc['province']) ?>
                        </div>
                        <div class="d-flex gap-2 mt-2 small flex-wrap">
                            <span class="badge bg-success-subtle text-success rounded-pill fw-semibold">
                                <i class="bi bi-tree me-1"></i><?= number_format($planted, 0, ',', '.') ?> tertanam
                            </span>
                            <span class="text-muted">/&nbsp;<?= number_format($target, 0, ',', '.') ?> target</span>
                        </div>
                        <div class="mt-2">
                            <a href="<?= url('/peta-lokasi?location_id=' . $loc['id']) ?>"
                               class="text-success fw-bold text-decoration-none" style="font-size:12px;">
                                Lihat di Peta <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- LOCATION CARDS GRID DETAIL -->
<div class="mb-5">
    <span class="tp-badge tp-badge-green mb-3">Progres Lapangan</span>
    <h2 class="fw-bold text-dark mb-4">Status Penanaman per Kawasan</h2>
    <div class="row g-4">
        <?php foreach ($locations as $loc):
            $planted = (int)($loc['actual_planted_count'] ?? $loc['total_trees_planted']);
            $target  = (int)$loc['total_trees_target'];
            $pct     = $target > 0 ? min(100, round(($planted / $target) * 100, 1)) : 0;
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="card tp-loc-card h-100">
                <?php if (!empty($loc['photo'])): ?>
                    <img src="<?= asset($loc['photo']) ?>" alt="<?= htmlspecialchars($loc['name']) ?>"
                         class="card-img-top object-fit-cover" style="height:140px;">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center"
                         style="height:130px;background:linear-gradient(135deg,#12382c,#1a4d3a);">
                        <i class="bi bi-tree-fill text-white opacity-50" style="font-size:48px;"></i>
                    </div>
                <?php endif; ?>
                <div class="p-4 flex-grow-1 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold text-dark mb-0 lh-sm"><?= htmlspecialchars($loc['name']) ?></h5>
                        <span class="badge rounded-pill fw-bold"
                              style="background:#dcfce7;color:#15803d;font-size:12px;padding:5px 10px;">
                            <?= $pct ?>%
                        </span>
                    </div>
                    <div class="text-muted small mb-2">
                        <i class="bi bi-geo-alt text-danger me-1"></i>
                        <?= htmlspecialchars($loc['city'] . ', ' . $loc['province']) ?>
                    </div>
                    <p class="text-secondary small mb-3 flex-grow-1">
                        <?= htmlspecialchars($loc['description'] ?? 'Kawasan konservasi penanaman pohon untuk pencegahan abrasi dan penguatan ekosistem alami.') ?>
                    </p>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Tertanam: <strong class="text-success"><?= number_format($planted, 0, ',', '.') ?></strong></span>
                            <span>Target: <?= number_format($target, 0, ',', '.') ?></span>
                        </div>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-success rounded-pill" style="width:<?= $pct ?>%;"></div>
                        </div>
                    </div>
                    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted font-monospace" style="font-size:11px;">
                            <i class="bi bi-compass me-1"></i>
                            <?= number_format((float)$loc['latitude'], 4) ?>, <?= number_format((float)$loc['longitude'], 4) ?>
                        </span>
                        <a href="<?= url('/peta-lokasi?location_id=' . $loc['id']) ?>"
                           class="btn btn-sm btn-success rounded-pill fw-semibold" style="font-size:12px;">
                            Lihat Peta <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- BUKU KAS DONASI + KABAR LAPANGAN -->
<div class="row g-4 mb-5">
    <!-- Ledger -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-2">
                <div>
                    <span class="tp-badge tp-badge-green mb-2">Buku Kas Publik</span>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-success"></i> Riwayat Donasi Tervalidasi
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= url('/leaderboard') ?>" class="btn btn-sm btn-outline-success rounded-pill fw-semibold">
                        <i class="bi bi-trophy-fill me-1"></i> Leaderboard
                    </a>
                    <a href="<?= url('/transparansi/export-csv') ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold">
                        <i class="bi bi-download me-1"></i> CSV
                    </a>
                </div>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="ledgerSearch" class="form-control border-start-0 bg-light"
                       placeholder="Cari nama, pesan, atau nomor invoice&#x2026;">
            </div>
            <div class="tp-ledger" id="ledgerBody">
                <?php if (empty($recentDonations)): ?>
                    <p class="text-muted text-center py-4">Belum ada data donasi tervalidasi.</p>
                <?php else: ?>
                    <?php foreach ($recentDonations as $i => $rd):
                        $donorName = !empty($rd['is_anonymous']) ? 'Sahabat Lestari (Anonim)' : htmlspecialchars($rd['donor_name']);
                        $initial   = mb_strtoupper(mb_substr(strip_tags($donorName), 0, 1));
                    ?>
                    <div class="tp-ledger-row d-flex align-items-center gap-3"
                         data-search="<?= htmlspecialchars(strtolower(strip_tags($donorName) . ' ' . ($rd['donor_message'] ?? '') . ' ' . ($rd['invoice_no'] ?? ''))) ?>">
                        <div class="text-muted text-center fw-bold" style="width:22px;font-size:12px;flex-shrink:0;"><?= $i + 1 ?></div>
                        <div class="tp-ledger-avatar"><?= $initial ?></div>
                        <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                            <div class="fw-bold text-dark text-truncate"><?= $donorName ?></div>
                            <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                <?php if (!empty($rd['invoice_no'])): ?>
                                    <span class="tp-ledger-invoice"><?= htmlspecialchars($rd['invoice_no']) ?></span>
                                    <span class="text-muted" style="font-size:11px;">&bull;</span>
                                <?php endif; ?>
                                <span class="small text-muted"><?= time_ago($rd['created_at']) ?></span>
                            </div>
                            <?php if (!empty($rd['donor_message'])): ?>
                                <div class="small text-secondary fst-italic mt-1 line-clamp-1"
                                     style="word-break: break-word;">&ldquo;<?= htmlspecialchars($rd['donor_message']) ?>&rdquo;</div>
                            <?php endif; ?>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="badge bg-success-subtle text-success rounded-pill fw-bold mb-1">
                                <i class="bi bi-tree me-1"></i><?= (int)$rd['total_trees'] ?> Pohon
                            </div>
                            <div class="small fw-bold text-dark"><?= format_rupiah((float)$rd['total_amount']) ?></div>
                            <div style="font-size:10px;" class="mt-1">
                                <i class="bi bi-patch-check-fill text-success"></i>
                                <span class="text-success fw-semibold">Terverifikasi</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-3">
                <a href="<?= url('/leaderboard') ?>" class="btn btn-sm btn-outline-success rounded-pill">
                    Lihat Semua Donatur <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Kabar Lapangan -->
    <div class="col-lg-5 min-w-0">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="tp-badge tp-badge-blue mb-2">Update Lapangan</span>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-newspaper text-primary"></i> Kabar Terbaru
                    </h5>
                </div>
                <a href="<?= url('/kabar') ?>" class="small text-success fw-bold text-decoration-none">
                    Arsip <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <?php if (empty($latestNews)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-newspaper fs-1 d-block mb-2 opacity-25"></i>
                    Belum ada kabar lapangan terbaru.
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($latestNews as $item): ?>
                    <div class="d-flex gap-3 align-items-start pb-3 border-bottom border-light" style="overflow:hidden;min-width:0;">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= asset($item['image']) ?>"
                                 alt="<?= htmlspecialchars($item['title']) ?>"
                                 class="rounded-3 object-fit-cover flex-shrink-0"
                                 style="width:88px;height:70px;min-width:88px;">
                        <?php else: ?>
                            <div class="rounded-3 bg-success-subtle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:88px;height:70px;min-width:88px;">
                                <i class="bi bi-camera text-success fs-4"></i>
                            </div>
                        <?php endif; ?>
                        <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                            <span class="small text-muted d-block"><?= date('d M Y', strtotime($item['published_at'])) ?></span>
                            <h6 class="fw-bold mb-1 lh-sm line-clamp-2" style="word-break: break-word;">
                                <a href="<?= url('/kabar/' . $item['slug']) ?>"
                                   class="text-dark text-decoration-none hover-green">
                                    <?= htmlspecialchars($item['title']) ?>
                                </a>
                            </h6>
                            <p class="small text-muted mb-0 line-clamp-2" style="word-break: break-word;"><?= htmlspecialchars($item['excerpt'] ?? '') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="tp-audit-banner p-3 mt-4">
                <h6 class="fw-bold text-success mb-3"><i class="bi bi-card-checklist me-2"></i>Prinsip Tata Kelola</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <?php foreach ([
                        ['bi-bank2',      'Verifikasi manual mutasi rekening sebelum dana dicairkan'],
                        ['bi-geo',        'GPS tagging setiap batang pohon (kode unik TREE-YYYYMMDD-XXXX)'],
                        ['bi-arrow-repeat','Garansi penyulaman bibit mati dalam 2 tahun pertama'],
                        ['bi-qr-code',    'Sertifikat digital ber-QR code anti-pemalsuan'],
                    ] as [$ic, $tx]): ?>
                    <li class="d-flex gap-2 align-items-start">
                        <i class="bi <?= $ic ?> text-success mt-1 flex-shrink-0"></i>
                        <span class="small text-dark"><?= $tx ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- SPESIES POHON -->
<?php if (!empty($treeSpecies)): ?>
<div class="mb-5">
    <span class="tp-badge tp-badge-amber mb-3">Biodiversitas</span>
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
        <h2 class="fw-bold text-dark mb-0">Jenis Pohon dalam Program Konservasi</h2>
        <span class="small text-muted"><?= count($treeSpecies) ?> spesies aktif</span>
    </div>
    <div class="row g-3">
        <?php foreach ($treeSpecies as $sp): ?>
        <div class="col-lg-4 col-md-6">
            <div class="tp-species-pill">
                <?php if (!empty($sp['image'])): ?>
                    <img src="<?= asset($sp['image']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>"
                         class="rounded-2 object-fit-cover flex-shrink-0" style="width:48px;height:48px;">
                <?php else: ?>
                    <div class="rounded-2 bg-success-subtle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-tree text-success fs-5"></i>
                    </div>
                <?php endif; ?>
                <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                    <div class="fw-bold text-dark"><?= htmlspecialchars($sp['name']) ?></div>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <?php if ((float)$sp['co2_absorption_kg_per_year'] > 0): ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill fw-semibold"
                              style="font-size:10px;">
                            CO&#x2082; <?= number_format((float)$sp['co2_absorption_kg_per_year'], 1) ?> kg/th
                        </span>
                        <?php endif; ?>
                        <?php if ((int)$sp['planted_count'] > 0): ?>
                        <span class="badge bg-success-subtle text-success rounded-pill fw-semibold"
                              style="font-size:10px;">
                            <?= $sp['planted_count'] ?> tertanam
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="small fw-bold text-dark"><?= format_rupiah((float)$sp['price_per_tree']) ?></div>
                    <div class="text-muted" style="font-size:10px;">/pohon</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- CEK SERTIFIKAT CALLOUT -->
<div class="tp-verify-callout p-5 mb-5 text-white">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <span class="tp-badge mb-3"
                  style="background:rgba(191,210,84,.15);color:#bfd254;border:1px solid rgba(191,210,84,.3);">
                Verifikasi Resmi
            </span>
            <h3 class="fw-bold mb-2">Pastikan Sertifikat Anda Asli &amp; Terverifikasi</h3>
            <p style="color:rgba(255,255,255,.70);" class="mb-0">
                Setiap sertifikat Plant For Fun dilengkapi nomor seri unik dan QR code. Masukkan nomor
                untuk mengecek keasliannya secara instan &mdash; terbuka untuk umum, tanpa login.
            </p>
        </div>
        <div class="col-lg-6">
            <form action="<?= url('/verifikasi-sertifikat') ?>" method="GET">
                <div class="input-group mb-2 shadow-sm">
                    <span class="input-group-text border-0 rounded-start-pill"
                          style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.7);">
                        <i class="bi bi-patch-check-fill" style="color:#bfd254;"></i>
                    </span>
                    <input type="text" name="nomor" class="form-control border-0"
                           style="background:rgba(255,255,255,.12);color:#fff;"
                           placeholder="Contoh: CERT-20261105-XXXX" required>
                    <button type="submit" class="btn btn-trail fw-bold rounded-end-pill px-4">
                        Cek Sekarang
                    </button>
                </div>
            </form>
            <div class="d-flex gap-3 small" style="color:rgba(255,255,255,.5);">
                <span><i class="bi bi-shield-check me-1" style="color:#bfd254;"></i>Tanpa login</span>
                <span><i class="bi bi-clock me-1" style="color:#bfd254;"></i>Instan</span>
                <span><i class="bi bi-infinity me-1" style="color:#bfd254;"></i>Selamanya valid</span>
            </div>
        </div>
    </div>
</div>

<!-- CTA BANNER -->
<div class="tp-cta p-5 mb-5 text-white text-center position-relative">
    <div class="position-relative" style="z-index:2;">
        <h2 class="display-6 fw-bold mb-3 font-poster">Siap Menanam Pohon Hari Ini?</h2>
        <p class="lead mx-auto mb-4" style="color:rgba(255,255,255,.85);max-width:600px;">
            Setiap pohon yang Anda tanam berkontribusi langsung pada penurunan suhu bumi,
            pencegahan erosi, dan udara segar bagi generasi penerus kita.
        </p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="<?= url('/donasi') ?>" class="btn btn-warning btn-lg text-dark fw-bold rounded-pill px-5 shadow">
                <i class="bi bi-heart-fill me-1"></i> Donasi Pohon Sekarang
            </a>
            <a href="<?= url('/kegiatan') ?>" class="btn btn-outline-light btn-lg fw-semibold rounded-pill px-5">
                <i class="bi bi-people-fill me-1"></i> Gabung Jadi Relawan
            </a>
        </div>
    </div>
</div>

</div><!-- /container -->

<?php $extraJs = '
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    /* ==================================================
       1. LEAFLET MINI MAP
    ================================================== */
    var mapEl = document.getElementById("transMap");
    if (mapEl && typeof L !== "undefined") {
        var locsData = ' . $locJson . ';

        var map = L.map("transMap", { scrollWheelZoom: false, zoomControl: true })
                   .setView([-6.5, 107.5], 7);

        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "\u00a9 OpenStreetMap contributors", maxZoom: 18
        });
        var sat = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
            attribution: "\u00a9 Esri Satellite", maxZoom: 18
        });
        osm.addTo(map);
        L.control.layers({ "Peta": osm, "Satelit": sat }).addTo(map);

        var bounds = [];
        locsData.forEach(function(loc) {
            if (!loc.latitude || !loc.longitude) return;
            var pct = loc.total_trees_target > 0
                ? Math.min(100, Math.round((loc.actual_planted_count / loc.total_trees_target) * 100))
                : 0;
            var pctColor = pct >= 70 ? "#22c55e" : pct >= 30 ? "#f59e0b" : "#3b82f6";

            var icon = L.divIcon({
                className: "",
                html: "<div class=\"tp-map-pin\" style=\"background:" + pctColor + "\">\uD83C\uDF33</div>",
                iconSize: [36, 36], iconAnchor: [18, 18]
            });

            var popup = "<div style=\"min-width:200px;padding:4px;\">" +
                "<h6 style=\"font-weight:800;margin-bottom:4px;\">" + loc.name + "</h6>" +
                "<div style=\"color:#6c757d;font-size:12px;margin-bottom:8px;\">" + loc.city + ", " + loc.province + "</div>" +
                "<div style=\"display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;\">" +
                    "<span>Tertanam <strong>" + loc.actual_planted_count.toLocaleString("id-ID") + "</strong></span>" +
                    "<span style=\"color:" + pctColor + ";font-weight:700;\">" + pct + "%</span>" +
                "</div>" +
                "<div class=\"tp-popup-prog\"><div class=\"tp-popup-prog-bar\" style=\"width:" + pct + "%;background:" + pctColor + ";\"></div></div>" +
                "<div style=\"font-size:11px;color:#6c757d;margin-top:4px;margin-bottom:10px;\">Target: " + loc.total_trees_target.toLocaleString("id-ID") + " pohon</div>" +
                "<a href=\"/peta-lokasi?location_id=" + loc.id + "\" " +
                   "style=\"display:block;text-align:center;padding:6px 12px;border-radius:999px;background:#22c55e;color:#fff;font-weight:700;font-size:12px;text-decoration:none;\">" +
                    "Lihat Detail Peta \u2192</a>" +
                "</div>";

            L.marker([loc.latitude, loc.longitude], { icon: icon })
                .addTo(map)
                .bindPopup(popup, { maxWidth: 240 });

            L.circle([loc.latitude, loc.longitude], {
                radius: 8000, fillColor: pctColor, fillOpacity: 0.08,
                color: pctColor, weight: 1.5, dashArray: "6,4"
            }).addTo(map);

            bounds.push([loc.latitude, loc.longitude]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [60, 60], maxZoom: 11 });
        }
    }

    /* ==================================================
       2. DONUT CHART — Alokasi Anggaran
    ================================================== */
    var donutEl = document.getElementById("donutChart");
    if (donutEl) {
        new Chart(donutEl, {
            type: "doughnut",
            data: {
                labels: ["Bibit & Penanaman 70%","Pemeliharaan 15%","Petani Lokal 10%","Operasional 5%"],
                datasets: [{
                    data: [70, 15, 10, 5],
                    backgroundColor: ["#22c55e","#3b82f6","#f59e0b","#94a3b8"],
                    borderWidth: 3, borderColor: "#fff", hoverOffset: 6
                }]
            },
            options: {
                cutout: "68%",
                plugins: { legend: { display: false } },
                animation: { duration: 900 }
            }
        });
    }

    /* ==================================================
       3. TREND LINE CHART
    ================================================== */
    var trendEl = document.getElementById("trendChart");
    var trendLabels  = ' . $trendLabels . ';
    var trendAmounts = ' . $trendAmounts . ';
    var trendTrees   = ' . $trendTrees . ';

    if (trendEl && trendLabels.length > 0) {
        // Hitung ukuran titik & lebar garis berdasarkan jumlah data
        var ptRadius   = trendLabels.length <= 7 ? 7 : 5;
        var ptHoverR   = ptRadius + 3;
        var lineWidth  = trendLabels.length <= 7 ? 2.5 : 2;

        new Chart(trendEl, {
            type: "line",
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: "Dana (Rp)", data: trendAmounts,
                        borderColor: "#22c55e",
                        backgroundColor: "rgba(34,197,94,.10)",
                        fill: true, tension: 0.35,
                        borderWidth: lineWidth,
                        pointBackgroundColor: "#22c55e",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        pointRadius: ptRadius,
                        pointHoverRadius: ptHoverR,
                        yAxisID: "y"
                    },
                    {
                        label: "Pohon", data: trendTrees,
                        borderColor: "#3b82f6",
                        backgroundColor: "rgba(59,130,246,.08)",
                        fill: true, tension: 0.35,
                        borderWidth: lineWidth,
                        pointBackgroundColor: "#3b82f6",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        pointRadius: ptRadius,
                        pointHoverRadius: ptHoverR,
                        yAxisID: "y1"
                    }
                ]
            },
            options: {
                responsive: true,
                animation: { duration: 800 },
                interaction: { mode: "index", intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#1e293b",
                        titleFont: { size: 12, weight: "bold" },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.dataset.yAxisID === "y") {
                                    return "  Dana: Rp " + ctx.parsed.y.toLocaleString("id-ID");
                                }
                                return "  Pohon: " + ctx.parsed.y.toLocaleString("id-ID") + " bibit";
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: "linear", position: "left",
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                if (v >= 1000000) return "Rp " + (v/1000000).toFixed(1) + " Jt";
                                if (v >= 1000)    return "Rp " + (v/1000).toFixed(0) + " Rb";
                                return "Rp " + v;
                            },
                            font: { size: 11 }, maxTicksLimit: 5
                        },
                        grid: { color: "rgba(0,0,0,.04)" }
                    },
                    y1: {
                        type: "linear", position: "right",
                        beginAtZero: true,
                        ticks: {
                            callback: function(v){ return v + " 🌳"; },
                            font: { size: 11 }, maxTicksLimit: 5
                        },
                        grid: { drawOnChartArea: false }
                    },
                    x: {
                        ticks: { font: { size: 11 }, maxRotation: 45 },
                        grid: { color: "rgba(0,0,0,.04)" }
                    }
                }
            }
        });
    }

    /* ==================================================
       4. LEDGER SEARCH
    ================================================== */
    var ledgerSearch = document.getElementById("ledgerSearch");
    if (ledgerSearch) {
        ledgerSearch.addEventListener("input", function() {
            var q = this.value.toLowerCase().trim();
            document.querySelectorAll("#ledgerBody .tp-ledger-row").forEach(function(row) {
                var text = row.dataset.search || "";
                row.style.display = (!q || text.includes(q)) ? "" : "none";
            });
        });
    }

});
</script>
'; ?>
