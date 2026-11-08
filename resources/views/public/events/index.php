<?php
/**
 * View: public/events/index.php — Jadwal Ekspedisi & Aksi Relawan Penanaman Pohon.
 */
$currentView = $filters['view'] ?? 'grid';
$difficultyMap = [
    'mudah'     => ['bg-success-subtle', '#0a3622', 'bi-shield-check', 'Mudah / Ramah Pemula'],
    'sedang'    => ['bg-warning-subtle', '#92400e', 'bi-compass', 'Sedang / Trek Ringan'],
    'menantang' => ['bg-danger-subtle', '#991b1b', 'bi-lightning-charge-fill', 'Menantang / Gunung'],
];
?>

<style>
/* Expedition Public Header */
.expedition-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 50%, #12382c 100%);
    border-radius: 28px;
    padding: 44px 36px;
    color: #fff;
    margin-bottom: 36px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
}
.expedition-hero::after {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(34,197,94,0.2) 0%, transparent 70%);
    pointer-events: none;
}

/* Stat strip in hero */
.exp-stat-box {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 16px 20px;
    backdrop-filter: blur(8px);
}

/* Spotlight featured expedition */
.featured-expedition-card {
    background: linear-gradient(135deg, #ffffff 0%, #f4f8f4 100%);
    border: 2px solid #12382c !important;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(18,56,44,0.08);
    margin-bottom: 40px;
}
.countdown-box {
    background: #12382c;
    color: #fff;
    border-radius: 12px;
    padding: 10px 14px;
    text-align: center;
    min-width: 65px;
}
.countdown-num {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1;
    font-family: 'Space Mono', monospace;
    color: #bfd254;
}
.countdown-lbl {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255,255,255,0.7);
    margin-top: 2px;
}

/* Filter Console */
.filter-card {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    border: 1px solid rgba(18,56,44,0.08);
    box-shadow: 0 4px 18px rgba(18,56,44,0.04);
    margin-bottom: 32px;
}
.category-pill-btn {
    border: 1.5px solid rgba(18,56,44,0.15);
    background: #fff;
    color: #12382c;
    border-radius: 50px;
    padding: 8px 18px;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.category-pill-btn:hover, .category-pill-btn.active {
    background: #12382c;
    color: #fff;
    border-color: #12382c;
    box-shadow: 0 4px 12px rgba(18,56,44,0.2);
}

/* Event Grid Card */
.exp-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid rgba(18,56,44,0.08);
    box-shadow: 0 4px 16px rgba(18,56,44,0.04);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.exp-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(18,56,44,0.12);
}
.exp-img-wrap {
    height: 200px;
    position: relative;
    overflow: hidden;
    background: #12382c;
}
.exp-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.exp-card:hover .exp-img-wrap img {
    transform: scale(1.05);
}

/* Calendar Date Badge */
.date-badge {
    background: #fff;
    border-radius: 14px;
    padding: 8px 14px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    line-height: 1.1;
    min-width: 65px;
}
.date-badge-day {
    font-size: 1.45rem;
    font-weight: 800;
    color: #12382c;
    font-family: 'Space Mono', monospace;
}
.date-badge-mon {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #15803d;
}

/* Timeline Layout */
.timeline-container {
    position: relative;
    padding-left: 36px;
    margin-bottom: 40px;
}
.timeline-container::before {
    content: '';
    position: absolute;
    left: 14px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #22c55e 0%, #12382c 100%);
    border-radius: 2px;
}
.timeline-node {
    position: relative;
    margin-bottom: 32px;
}
.timeline-node::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 24px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #22c55e;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px rgba(34,197,94,0.3);
}

/* Facility Chips */
.facility-chip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
</style>

<div class="container py-4 py-md-5">

    <!-- 1. EXPEDITION PAGE HERO -->
    <div class="expedition-hero shadow">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge rounded-pill px-3 py-2 text-uppercase font-mono mb-3 shadow-sm d-inline-flex align-items-center gap-2" style="background-color: #bfd254; color: #12382c; font-size: 11px; letter-spacing: 1.5px; font-weight: 700;">
                    <i class="bi bi-compass-fill"></i> JADWAL &amp; KALENDER EKSPEDISI RELAWAN 2026/2027
                </span>
                <h1 class="display-5 font-poster text-uppercase fw-bold mb-3" style="letter-spacing: 1px; line-height: 1.15;">
                    Aksi Nyata Hijaukan Lereng &amp; Pesisir Nusantara
                </h1>
                <p class="text-white-50 mb-4" style="max-width: 580px; font-size: 1.05rem;">
                    Bergabunglah bersama ribuan relawan muda, komunitas pencinta alam, dan kelompok tani lokal Karsa Kerentjeng untuk memulihkan ekosistem lereng gunung dan kawasan pesisir rawan abrasi.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#daftar-ekspedisi" class="btn btn-success fw-bold font-poster rounded-pill px-4 py-2" style="background: #22c55e; border-color: #22c55e; color: #061510;">
                        <i class="bi bi-calendar-check me-2"></i>Lihat Semua Jadwal Ekspedisi
                    </a>
                    <a href="/kabar" class="btn btn-outline-light rounded-pill px-4 py-2 font-poster fw-bold">
                        <i class="bi bi-journal-text me-2"></i>Baca Kabar Lapangan
                    </a>
                </div>
            </div>

            <!-- 4 Quick Stats -->
            <div class="col-lg-5">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="exp-stat-box">
                            <div class="d-flex align-items-center gap-2 text-white-50 small mb-1">
                                <i class="bi bi-flag-fill text-success"></i> Total Ekspedisi
                            </div>
                            <div class="display-6 fw-bold font-poster text-white lh-1">
                                <?= number_format($stats['total_events'] ?? 0) ?>
                            </div>
                            <small class="text-white-50" style="font-size: 11px;">Aksi Konservasi Terjadwal</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="exp-stat-box">
                            <div class="d-flex align-items-center gap-2 text-white-50 small mb-1">
                                <i class="bi bi-people-fill text-warning"></i> Relawan Siap
                            </div>
                            <div class="display-6 fw-bold font-poster text-white lh-1">
                                <?= number_format($stats['total_registered'] ?? 0) ?>+
                            </div>
                            <small class="text-white-50" style="font-size: 11px;">Peserta Terkonfirmasi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="exp-stat-box">
                            <div class="d-flex align-items-center gap-2 text-white-50 small mb-1">
                                <i class="bi bi-geo-alt-fill text-info"></i> Titik Lokasi
                            </div>
                            <div class="display-6 fw-bold font-poster text-white lh-1">
                                <?= number_format($stats['total_locations'] ?? 3) ?>
                            </div>
                            <small class="text-white-50" style="font-size: 11px;">Kawasan Hutan & DAS</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="exp-stat-box">
                            <div class="d-flex align-items-center gap-2 text-white-50 small mb-1">
                                <i class="bi bi-cloud-snow text-danger"></i> Rata Elevasi
                            </div>
                            <div class="display-6 fw-bold font-poster text-white lh-1">
                                <?= number_format($stats['avg_elevation'] ?? 1550) ?>
                            </div>
                            <small class="text-white-50" style="font-size: 11px;">Meter di Atas Permukaan Laut</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SPOTLIGHT EKSPEDISI UNGGULAN (FEATURED EXPEDITION) -->
    <?php if (!empty($featuredEvent)): ?>
        <?php 
            $featPerc = ($featuredEvent['quota'] > 0) ? min(100, round(($featuredEvent['registered_count'] / $featuredEvent['quota']) * 100)) : 0;
            $featRemaining = max(0, $featuredEvent['quota'] - $featuredEvent['registered_count']);
            $featCover = !empty($featuredEvent['cover_image']) ? asset($featuredEvent['cover_image']) : asset('assets/images/plant-for-fun-kerenceng.jpg');
        ?>
        <div class="featured-expedition-card">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-5 position-relative" style="min-height: 280px;">
                    <img src="<?= $featCover ?>" alt="<?= htmlspecialchars($featuredEvent['title']) ?>" class="w-100 h-100 object-fit-cover">
                    <span class="position-absolute top-0 start-0 m-3 badge rounded-pill px-3 py-2 font-mono fw-bold" style="background:#12382c; color:#bfd254; font-size:11px;">
                        ⭐ EKSPEDISI UTAMA
                    </span>
                    <span class="position-absolute bottom-0 start-0 m-3 badge bg-dark bg-opacity-75 text-white rounded-pill px-3 py-2 font-mono small">
                        <i class="bi bi-geo-alt-fill text-warning me-1"></i><?= htmlspecialchars($featuredEvent['gathering_point']) ?>
                    </span>
                </div>
                <div class="col-lg-7 p-4 p-md-5 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill font-mono fw-bold" style="color:#0a3622 !important;">
                                <?= htmlspecialchars($featuredEvent['category'] ?? 'Agroforestri') ?>
                            </span>
                            <span class="badge bg-light text-dark border px-3 py-1 rounded-pill font-mono small">
                                <i class="bi bi-clock me-1 text-success"></i> Pukul <?= substr($featuredEvent['gathering_time'], 0, 5) ?> WIB
                            </span>
                        </div>

                        <h3 class="fw-bold font-poster text-uppercase mb-2" style="color: #12382c;">
                            <a href="<?= url('/kegiatan/' . $featuredEvent['id']) ?>" class="text-decoration-none" style="color: #12382c;">
                                <?= htmlspecialchars($featuredEvent['title']) ?>
                            </a>
                        </h3>

                        <p class="text-secondary small mb-3 line-clamp-2">
                            <?= htmlspecialchars($featuredEvent['description']) ?>
                        </p>

                        <!-- Progress Bar & Countdown -->
                        <div class="row align-items-center g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="d-flex justify-content-between small text-secondary mb-1 font-mono">
                                    <span>Kuota Relawan:</span>
                                    <strong class="text-dark"><?= $featuredEvent['registered_count'] ?> / <?= $featuredEvent['quota'] ?> Kursi</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?= $featPerc ?>%;"></div>
                                </div>
                                <small class="text-success fw-bold font-mono" style="font-size: 11px;">
                                    Sisa <?= $featRemaining ?> kursi tersedia!
                                </small>
                            </div>

                            <div class="col-sm-6">
                                <div class="d-flex gap-2 justify-content-sm-end" id="featuredCountdown" data-target="<?= $featuredEvent['event_date'] ?>T<?= $featuredEvent['gathering_time'] ?>">
                                    <div class="countdown-box">
                                        <div class="countdown-num" id="cdDays">--</div>
                                        <div class="countdown-lbl">Hari</div>
                                    </div>
                                    <div class="countdown-box">
                                        <div class="countdown-num" id="cdHours">--</div>
                                        <div class="countdown-lbl">Jam</div>
                                    </div>
                                    <div class="countdown-box">
                                        <div class="countdown-num" id="cdMins">--</div>
                                        <div class="countdown-lbl">Mnt</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 border-top">
                        <div class="font-mono small text-muted">
                            Ketinggian: <strong class="text-dark"><?= number_format($featuredEvent['elevation_masl'] ?? 1550) ?> mdpl</strong> &bull; 
                            Biaya: <strong class="text-success"><?= $featuredEvent['registration_fee'] > 0 ? format_rupiah($featuredEvent['registration_fee']) : 'Gratis Terbuka' ?></strong>
                        </div>
                        <a href="<?= url('/kegiatan/' . $featuredEvent['id']) ?>" class="btn btn-success fw-bold font-poster rounded-pill px-4 py-2">
                            Daftar Ekspedisi Ini →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. INTERACTIVE SEARCH & FILTER CONSOLE -->
    <div id="daftar-ekspedisi" class="filter-card">
        <form method="GET" action="<?= url('/kegiatan') ?>" id="expeditionFilterForm">
            <!-- Search & Dropdown Row -->
            <div class="row g-3 align-items-center mb-3">
                <div class="col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="form-control border-start-0" placeholder="Cari nama ekspedisi, titik kumpul, atau kota...">
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <select name="kesulitan" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Tingkat Kesulitan</option>
                        <option value="mudah" <?= ($filters['difficulty'] ?? '') === 'mudah' ? 'selected' : '' ?>>Mudah (Pemula &amp; Keluarga)</option>
                        <option value="sedang" <?= ($filters['difficulty'] ?? '') === 'sedang' ? 'selected' : '' ?>>Sedang (Trek Ringan)</option>
                        <option value="menantang" <?= ($filters['difficulty'] ?? '') === 'menantang' ? 'selected' : '' ?>>Menantang (Gunung)</option>
                    </select>
                </div>

                <div class="col-sm-6 col-lg-2">
                    <select name="biaya" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Biaya</option>
                        <option value="free" <?= ($filters['fee_type'] ?? '') === 'free' ? 'selected' : '' ?>>Gratis Terbuka</option>
                        <option value="paid" <?= ($filters['fee_type'] ?? '') === 'paid' ? 'selected' : '' ?>>Bundle Merchandise</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="upcoming" <?= ($filters['status'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Akan Datang</option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Arsip Selesai</option>
                        <option value="all" <?= ($filters['status'] ?? '') === 'all' ? 'selected' : '' ?>>Semua Arsip</option>
                    </select>
                </div>
            </div>

            <!-- Category Pills Bar & View Mode Switcher -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-secondary small fw-bold font-mono me-1">Kategori:</span>
                    <a href="<?= url('/kegiatan?' . http_build_query(array_merge($filters, ['kategori' => '']))) ?>" 
                       class="category-pill-btn <?= empty($filters['category']) ? 'active' : '' ?>">
                        Semua Kategori
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= url('/kegiatan?' . http_build_query(array_merge($filters, ['kategori' => $cat]))) ?>" 
                           class="category-pill-btn <?= ($filters['category'] ?? '') === $cat ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- View Switcher Toggle -->
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="<?= url('/kegiatan?' . http_build_query(array_merge($filters, ['view' => 'grid']))) ?>" 
                           class="btn <?= $currentView === 'grid' ? 'btn-success' : 'btn-outline-secondary' ?>" title="Tampilan Grid">
                            <i class="bi bi-grid-fill"></i> Grid
                        </a>
                        <a href="<?= url('/kegiatan?' . http_build_query(array_merge($filters, ['view' => 'timeline']))) ?>" 
                           class="btn <?= $currentView === 'timeline' ? 'btn-success' : 'btn-outline-secondary' ?>" title="Tampilan Linimasa">
                            <i class="bi bi-list-nested"></i> Linimasa
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 4. LISTING EKSPEDISI: GRID / TIMELINE -->
    <?php if (empty($events)): ?>
        <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4 mb-5">
            <div class="display-1 text-muted mb-2">🧭</div>
            <h4 class="fw-bold font-poster text-dark">Tidak Ada Ekspedisi yang Sesuai</h4>
            <p class="text-muted small mx-auto" style="max-width: 480px;">
                Belum ada kegiatan ekspedisi yang cocok dengan kriteria filter Anda. Coba reset filter untuk melihat semua agenda penanaman pohon.
            </p>
            <div>
                <a href="<?= url('/kegiatan') ?>" class="btn btn-outline-success rounded-pill fw-bold font-poster px-4">
                    Reset Semua Filter
                </a>
            </div>
        </div>
    <?php elseif ($currentView === 'timeline'): ?>
        <!-- ============================================================= -->
        <!-- TIMELINE / AGENDA VIEW -->
        <!-- ============================================================= -->
        <div class="timeline-container">
            <?php foreach ($events as $ev): ?>
                <?php 
                    $diffInfo = $difficultyMap[$ev['difficulty_level'] ?? 'sedang'] ?? $difficultyMap['sedang'];
                    $isFull = ($ev['registered_count'] >= $ev['quota']);
                    $perc = ($ev['quota'] > 0) ? min(100, round(($ev['registered_count'] / $ev['quota']) * 100)) : 0;
                ?>
                <div class="timeline-node">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 bg-white">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3 text-md-center border-end-md pb-2 pb-md-0">
                                <div class="badge px-3 py-1 rounded-pill font-mono mb-2" style="background:#12382c; color:#fff;">
                                    <?= date('d M Y', strtotime($ev['event_date'])) ?>
                                </div>
                                <div class="text-muted small font-mono">
                                    <i class="bi bi-clock me-1 text-success"></i><?= substr($ev['gathering_time'], 0, 5) ?> WIB
                                </div>
                                <div class="small fw-bold text-success font-mono mt-1">
                                    <?= number_format($ev['elevation_masl'] ?? 1550) ?> mdpl
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge bg-success-subtle text-success font-mono" style="color:#0a3622 !important; font-size:10px;">
                                        <?= htmlspecialchars($ev['category'] ?? 'Agroforestri') ?>
                                    </span>
                                    <span class="badge <?= $diffInfo[0] ?> rounded-pill" style="color:<?= $diffInfo[1] ?>; font-size:10px;">
                                        <i class="bi <?= $diffInfo[2] ?> me-1"></i><?= $diffInfo[3] ?>
                                    </span>
                                </div>
                                <h5 class="fw-bold font-poster mb-1">
                                    <a href="<?= url('/kegiatan/' . $ev['id']) ?>" class="text-decoration-none text-dark hover-success">
                                        <?= htmlspecialchars($ev['title']) ?>
                                    </a>
                                </h5>
                                <div class="small text-muted mb-2 font-mono">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($ev['gathering_point']) ?> (<?= htmlspecialchars($ev['city'] ?? $ev['location_name'] ?? '-') ?>)
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="facility-chip"><i class="bi bi-patch-check text-success"></i> Sertifikat</span>
                                    <?php if (!empty($ev['includes_tshirt'])): ?>
                                        <span class="facility-chip"><i class="bi bi-gift text-primary"></i> Termasuk Kaos</span>
                                    <?php endif; ?>
                                    <span class="facility-chip"><i class="bi bi-cup-hot text-warning"></i> Kopi Kerenceng</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <div class="small text-muted font-mono mb-1">
                                    Kuota: <strong><?= $ev['registered_count'] ?> / <?= $ev['quota'] ?></strong>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $perc ?>%;"></div>
                                </div>
                                <a href="<?= url('/kegiatan/' . $ev['id']) ?>" class="btn <?= $isFull ? 'btn-secondary disabled' : 'btn-success' ?> btn-sm fw-bold font-poster rounded-pill px-4">
                                    <?= $isFull ? 'Kuota Penuh' : 'Detail & Daftar →' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- ============================================================= -->
        <!-- GRID CARDS VIEW (DEFAULT) -->
        <!-- ============================================================= -->
        <div class="row g-4 mb-5">
            <?php foreach ($events as $ev): ?>
                <?php 
                    $diffInfo = $difficultyMap[$ev['difficulty_level'] ?? 'sedang'] ?? $difficultyMap['sedang'];
                    $isFull = ($ev['registered_count'] >= $ev['quota']);
                    $perc = ($ev['quota'] > 0) ? min(100, round(($ev['registered_count'] / $ev['quota']) * 100)) : 0;
                    $isKerenceng = str_contains(strtolower($ev['title']), 'kerenceng') || (int)$ev['id'] === 3;
                    $cardImg = !empty($ev['cover_image']) 
                        ? asset($ev['cover_image']) 
                        : (!empty($ev['location_photo']) ? asset($ev['location_photo']) : asset('assets/images/plant-for-fun-kerenceng.jpg'));
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="exp-card <?= $isKerenceng ? 'border-2' : '' ?>" style="<?= $isKerenceng ? 'border-color: #12382c !important;' : '' ?>">
                        
                        <!-- Visual Header & Badges -->
                        <div class="exp-img-wrap">
                            <img src="<?= $cardImg ?>" alt="<?= htmlspecialchars($ev['title']) ?>" loading="lazy">
                            
                            <!-- Date badge overlay -->
                            <div class="position-absolute top-0 start-0 m-3 date-badge">
                                <div class="date-badge-day"><?= date('d', strtotime($ev['event_date'])) ?></div>
                                <div class="date-badge-mon"><?= date('M Y', strtotime($ev['event_date'])) ?></div>
                            </div>

                            <!-- Elevation & Difficulty badge overlay -->
                            <div class="position-absolute top-0 end-0 m-3 d-flex flex-column gap-1 align-items-end">
                                <span class="badge bg-dark bg-opacity-75 text-white font-mono px-2 py-1 rounded-pill" style="font-size: 10px;">
                                    <i class="bi bi-cloud-arrow-up text-warning me-1"></i><?= number_format($ev['elevation_masl'] ?? 1550) ?> mdpl
                                </span>
                                <span class="badge <?= $diffInfo[0] ?> rounded-pill px-2 py-1" style="color: <?= $diffInfo[1] ?>; font-size: 10px;">
                                    <?= $diffInfo[3] ?>
                                </span>
                            </div>

                            <!-- Bottom category overlay -->
                            <div class="position-absolute bottom-0 start-0 m-3">
                                <span class="badge px-3 py-1 rounded-pill font-mono" style="background: rgba(6,21,16,0.85); color: #fff; font-size: 10.5px;">
                                    <?= htmlspecialchars($ev['category'] ?? 'Agroforestri') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            
                            <!-- Fee & Gathering time -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small text-muted font-mono">
                                    <i class="bi bi-clock me-1 text-success"></i><?= substr($ev['gathering_time'], 0, 5) ?> WIB
                                </div>
                                <?php if ($ev['registration_fee'] > 0): ?>
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill font-mono" style="font-size: 10.5px;">
                                        <?= format_rupiah($ev['registration_fee']) ?> (Kaos)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle px-2 py-1 rounded-pill font-mono fw-bold" style="color:#0a3622; font-size: 10.5px;">
                                        Gratis Terbuka
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Title -->
                            <h5 class="fw-bold font-poster text-uppercase mb-2" style="font-size: 1.05rem; line-height: 1.3;">
                                <a href="<?= url('/kegiatan/' . $ev['id']) ?>" class="text-dark text-decoration-none hover-success">
                                    <?= htmlspecialchars($ev['title']) ?>
                                </a>
                            </h5>

                            <!-- Meeting Point -->
                            <div class="small text-muted mb-3 font-mono line-clamp-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <span><?= htmlspecialchars($ev['gathering_point']) ?></span>
                            </div>

                            <!-- Description Snippet -->
                            <p class="text-secondary small flex-grow-1 line-clamp-2 mb-3">
                                <?= htmlspecialchars($ev['description']) ?>
                            </p>

                            <!-- Facility Chips -->
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="facility-chip"><i class="bi bi-patch-check text-success"></i> E-Ticket</span>
                                <?php if (!empty($ev['includes_tshirt'])): ?>
                                    <span class="facility-chip"><i class="bi bi-gift text-primary"></i> Kaos Relawan</span>
                                <?php endif; ?>
                                <span class="facility-chip"><i class="bi bi-shield-check text-warning"></i> Tim Medis P3K</span>
                            </div>

                            <!-- Progress Kuota Peserta -->
                            <div class="mt-auto pt-3 border-top">
                                <div class="d-flex justify-content-between small text-secondary mb-1 font-mono">
                                    <span>Sisa Kursi:</span>
                                    <strong class="text-dark"><?= max(0, $ev['quota'] - $ev['registered_count']) ?> dari <?= $ev['quota'] ?></strong>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-success' ?>" style="width: <?= $perc ?>%;"></div>
                                </div>

                                <!-- CTA Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="<?= url('/kegiatan/' . $ev['id']) ?>" class="btn <?= $isFull ? 'btn-secondary disabled' : 'btn-success' ?> w-100 fw-bold font-poster rounded-pill py-2">
                                        <?= $isFull ? 'Kuota Penuh' : 'Detail &amp; Daftar →' ?>
                                    </a>
                                    <!-- Share WhatsApp Button -->
                                    <a href="https://api.whatsapp.com/send?text=<?= urlencode('Yuk ikutan Ekspedisi Penanaman Pohon: ' . $ev['title'] . ' di ' . url('/kegiatan/' . $ev['id'])) ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0" 
                                       style="width: 38px; height: 38px;" 
                                       title="Bagikan ke WhatsApp">
                                        <i class="bi bi-whatsapp text-success"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 5. FAQ EKSPEDISI & PANDUAN RELAWAN -->
    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white mb-5">
        <div class="text-center mb-4">
            <span class="badge bg-success-subtle px-3 py-1 rounded-pill font-mono fw-bold mb-2" style="color: #0a3622;">
                PANDUAN RELAWAN
            </span>
            <h3 class="fw-bold font-poster text-uppercase mb-1" style="color: #12382c;">
                Pertanyaan Seputar Ekspedisi Penanaman
            </h3>
            <p class="text-muted small">Semua yang perlu Anda ketahui sebelum bergabung dalam aksi nyata konservasi kami.</p>
        </div>

        <div class="accordion accordion-flush" id="faqEkspedisiAccordion">
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-dark font-poster" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        1. Apakah pemula yang belum pernah mendaki gunung boleh ikut?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqEkspedisiAccordion">
                    <div class="accordion-body text-secondary small">
                        Tentu saja! Sebagian besar ekspedisi kami dirancang untuk semua kalangan. Setiap kegiatan memiliki indikator tingkat kesulitan (Mudah, Sedang, Menantang). Untuk pemula, kami sarankan memilih jalur dengan kategori <strong>Mudah</strong> atau <strong>Sedang</strong>, serta didampingi pemandu lapangan berpengalaman.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-dark font-poster" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        2. Apa saja perlengkapan wajib yang harus dibawa?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqEkspedisiAccordion">
                    <div class="accordion-body text-secondary small">
                        Perlengkapan standar: sepatu trekking / bot karet anti selip, jas hujan ponco, tumbler air minum isi ulang pribadi (bebas sampah plastik sekali pakai), pakaian ganti, sarung tangan kebun, dan obat-obatan pribadi bagi yang memiliki riwayat alergi.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-dark font-poster" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        3. Bagaimana cara mendapatkan E-Ticket dan sertifikat keikutsertaan?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqEkspedisiAccordion">
                    <div class="accordion-body text-secondary small">
                        Setelah mendaftar, E-Ticket ber-QR Code akan langsung diterbitkan di dashboard akun Anda. Di hari H, cukup tunjukkan QR Code tersebut kepada petugas di meja registrasi. Sertifikat digital resmi relawan akan otomatis terbit di akun Anda setelah acara selesai.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-dark font-poster" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        4. Bagaimana jika cuaca hujan di lokasi penanaman?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqEkspedisiAccordion">
                    <div class="accordion-body text-secondary small">
                        Hujan rintik justru sangat baik bagi bibit pohon yang baru ditanam! Namun jika cuaca ekstrem (badai petir atau angin kencang), tim keselamatan lapangan akan mengalihkan aktivitas ke posko teduh dan menyesuaikan jadwal demi keamanan bersama.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. MITRA SPONSOR & KOLABORATOR -->
    <div class="p-4 rounded-4 shadow-sm bg-white border" style="border-color: rgba(18,56,44,0.15) !important;">
        <div class="mb-2">
            <h5 class="fw-bold font-poster text-uppercase mb-1" style="color: #12382c;">
                Mitra Kolaborasi &amp; Media Partner Resmi
            </h5>
            <p class="text-muted small mb-0">Gerakan ekspedisi penanaman pohon ini terselenggara berkat kolaborasi bersama para partner peduli alam:</p>
        </div>
        <?php 
            $logoTheme = 'dark'; 
            require VIEW_PATH . '/partials/partner-logos.php'; 
        ?>
    </div>

</div>

<!-- Countdown Script for Featured Expedition -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cdWrap = document.getElementById('featuredCountdown');
    if (!cdWrap) return;
    const targetStr = cdWrap.dataset.target;
    if (!targetStr) return;
    const targetDate = new Date(targetStr).getTime();

    function updateCd() {
        const now = new Date().getTime();
        const dist = targetDate - now;
        if (dist <= 0) {
            document.getElementById('cdDays').textContent = '00';
            document.getElementById('cdHours').textContent = '00';
            document.getElementById('cdMins').textContent = '00';
            return;
        }
        const d = Math.floor(dist / (1000 * 60 * 60 * 24));
        const h = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const m = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));

        document.getElementById('cdDays').textContent = String(d).padStart(2, '0');
        document.getElementById('cdHours').textContent = String(h).padStart(2, '0');
        document.getElementById('cdMins').textContent = String(m).padStart(2, '0');
    }
    updateCd();
    setInterval(updateCd, 60000);
});
</script>
