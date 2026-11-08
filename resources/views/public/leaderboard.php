<?php
/**
 * View: Papan Peringkat (Leaderboard) Donatur & Relawan
 * Path: resources/views/public/leaderboard.php
 */

$period = $period ?? 'all';
?>

<section class="py-5 text-white topo-pattern" style="background-color: #12382c; border-bottom: 3px solid #1c4b3c;">
    <div class="container text-center py-3">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25);">
            <i class="bi bi-trophy-fill" style="color: #bfd254;"></i>
            <span class="font-poster text-uppercase small text-white" style="letter-spacing: 1.5px; font-size: 11px;">
                Hall of Fame &bull; Pahlawan Hijau
            </span>
        </div>
        <h1 class="display-5 fw-bold font-poster mb-2">Papan Peringkat Penanam Kebaikan</h1>
        <p class="lead text-light opacity-90 mx-auto mb-4" style="max-width: 650px;">
            Apresiasi setinggi-tingginya bagi para individu, komunitas, dan korporasi yang mendedikasikan dukungan nyata untuk penghijauan Indonesia.
        </p>

        <!-- Filter Tab Periode -->
        <div class="d-inline-flex bg-black bg-opacity-25 p-1 rounded-pill border border-white border-opacity-20 font-mono">
            <a href="<?= url('/leaderboard?periode=all') ?>" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $period === 'all' ? 'btn-trail' : 'text-white' ?>">
                Semua Waktu
            </a>
            <a href="<?= url('/leaderboard?periode=year') ?>" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $period === 'year' ? 'btn-trail' : 'text-white' ?>">
                Tahun Ini (2026)
            </a>
            <a href="<?= url('/leaderboard?periode=month') ?>" class="btn btn-sm rounded-pill px-4 fw-semibold <?= $period === 'month' ? 'btn-trail' : 'text-white' ?>">
                Bulan Ini
            </a>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">

        <!-- Tabs Pilihan: Donatur vs Relawan -->
        <ul class="nav nav-pills justify-content-center mb-5 gap-2" id="leaderboardTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 py-2 fw-bold shadow-sm" id="donors-tab" data-bs-toggle="pill" data-bs-target="#donors" type="button" role="tab" aria-controls="donors" aria-selected="true">
                    <i class="bi bi-tree-fill me-1"></i> Top Donatur Pohon
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 py-2 fw-bold shadow-sm" id="volunteers-tab" data-bs-toggle="pill" data-bs-target="#volunteers" type="button" role="tab" aria-controls="volunteers" aria-selected="false">
                    <i class="bi bi-people-fill me-1"></i> Top Relawan Lapangan
                </button>
            </li>
        </ul>

        <div class="tab-content" id="leaderboardTabContent">

            <!-- TAB 1: TOP DONATUR POHON -->
            <div class="tab-pane fade show active" id="donors" role="tabpanel" aria-labelledby="donors-tab">
                <?php if (empty($topDonors)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        <p>Belum ada riwayat donasi untuk periode yang dipilih.</p>
                    </div>
                <?php else: ?>

                    <!-- Visual Podium Top 3 -->
                    <?php if (count($topDonors) >= 1): 
                        $rank1 = $topDonors[0] ?? null;
                        $rank2 = $topDonors[1] ?? null;
                        $rank3 = $topDonors[2] ?? null;
                    ?>
                        <div class="row g-4 justify-content-center align-items-end mb-5 text-center">
                            <!-- Juara 2 (Perak) -->
                            <?php if ($rank2): ?>
                                <div class="col-md-4 col-sm-6 order-2 order-md-1">
                                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white position-relative hover-shadow transition">
                                        <div class="position-absolute top-0 start-50 translate-middle">
                                            <span class="badge bg-secondary text-white rounded-pill px-3 py-1 shadow-sm fw-bold">
                                                🥈 Peringkat #2
                                            </span>
                                        </div>
                                        <div class="mt-3 mb-3">
                                            <div class="avatar-circle mx-auto bg-secondary-subtle text-secondary rounded-circle fw-bold fs-3 d-flex align-items-center justify-content-center border border-2 border-secondary" style="width: 70px; height: 70px;">
                                                <?= strtoupper(substr($rank2['display_name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($rank2['display_name']) ?></h5>
                                        <div class="badge bg-success-subtle border border-success-subtle rounded-pill px-3 py-1 mb-2" style="color:#0a3622;">
                                            <?= number_format((int)$rank2['total_trees'], 0, ',', '.') ?> Pohon
                                        </div>
                                        <div class="small text-muted fw-semibold">
                                            Total: <?= format_rupiah((float)$rank2['total_amount']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Juara 1 (Emas) -->
                            <?php if ($rank1): ?>
                                <div class="col-md-4 col-sm-6 order-1 order-md-2">
                                    <div class="card border-warning border-2 shadow rounded-4 p-4 bg-white position-relative hover-shadow transition" style="transform: scale(1.05); z-index: 2;">
                                        <div class="position-absolute top-0 start-50 translate-middle">
                                            <span class="badge bg-warning text-dark rounded-pill px-4 py-2 shadow fw-bold fs-6">
                                                👑 Juara #1
                                            </span>
                                        </div>
                                        <div class="mt-4 mb-3">
                                            <div class="avatar-circle mx-auto bg-warning-subtle text-warning-emphasis rounded-circle fw-bold fs-2 d-flex align-items-center justify-content-center border border-3 border-warning" style="width: 85px; height: 85px;">
                                                <?= strtoupper(substr($rank1['display_name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($rank1['display_name']) ?></h4>
                                        <div class="badge bg-success text-white rounded-pill px-3 py-2 mb-2 fs-6">
                                            🌳 <?= number_format((int)$rank1['total_trees'], 0, ',', '.') ?> Pohon
                                        </div>
                                        <div class="text-success fw-bold fs-5">
                                            <?= format_rupiah((float)$rank1['total_amount']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Juara 3 (Perunggu) -->
                            <?php if ($rank3): ?>
                                <div class="col-md-4 col-sm-6 order-3 order-md-3">
                                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white position-relative hover-shadow transition">
                                        <div class="position-absolute top-0 start-50 translate-middle">
                                            <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1 shadow-sm fw-bold">
                                                🥉 Peringkat #3
                                            </span>
                                        </div>
                                        <div class="mt-3 mb-3">
                                            <div class="avatar-circle mx-auto bg-dark-subtle text-dark rounded-circle fw-bold fs-3 d-flex align-items-center justify-content-center border border-2 border-dark-subtle" style="width: 70px; height: 70px;">
                                                <?= strtoupper(substr($rank3['display_name'], 0, 1)) ?>
                                            </div>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($rank3['display_name']) ?></h5>
                                        <div class="badge bg-success-subtle border border-success-subtle rounded-pill px-3 py-1 mb-2" style="color:#0a3622;">
                                            <?= number_format((int)$rank3['total_trees'], 0, ',', '.') ?> Pohon
                                        </div>
                                        <div class="small text-muted fw-semibold">
                                            Total: <?= format_rupiah((float)$rank3['total_amount']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Tabel Peringkat Lengkap -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-white py-3 border-light-subtle">
                            <h5 class="fw-bold text-dark mb-0">Daftar Peringkat Lengkap</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase text-muted">
                                    <tr>
                                        <th class="ps-4" style="width: 80px;">Peringkat</th>
                                        <th>Nama Donatur</th>
                                        <th class="text-center">Jumlah Pohon</th>
                                        <th class="text-end">Total Kontribusi</th>
                                        <th class="text-end pe-4">Donasi Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topDonors as $idx => $d): 
                                        $rank = $idx + 1;
                                    ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php if ($rank === 1): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 fw-bold">#1</span>
                                                <?php elseif ($rank === 2): ?>
                                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1 fw-bold">#2</span>
                                                <?php elseif ($rank === 3): ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1 fw-bold">#3</span>
                                                <?php else: ?>
                                                    <span class="text-muted fw-bold">#<?= $rank ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-success-subtle rounded-circle fw-bold me-3 d-flex align-items-center justify-content-center" style="color:#0a3622; width: 36px; height: 36px; font-size: 14px;">
                                                        <?= strtoupper(substr($d['display_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($d['display_name']) ?></div>
                                                        <?php if (!empty($d['is_anonymous'])): ?>
                                                            <span class="badge bg-light text-muted border small">Donasi Anonim</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle border border-success-subtle rounded-pill px-3 py-1 fw-bold" style="color:#0a3622;">
                                                    <?= number_format((int)$d['total_trees'], 0, ',', '.') ?> Pohon
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold text-dark">
                                                <?= format_rupiah((float)$d['total_amount']) ?>
                                            </td>
                                            <td class="text-end pe-4 small text-muted">
                                                <?= time_ago($d['last_donation_at']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <!-- TAB 2: TOP RELAWAN LAPANGAN -->
            <div class="tab-pane fade" id="volunteers" role="tabpanel" aria-labelledby="volunteers-tab">
                <?php if (empty($topVolunteers)): ?>
                    <div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm p-4">
                        <i class="bi bi-people fs-1 d-block mb-3 text-secondary"></i>
                        <h5>Belum Ada Data Kehadiran Relawan</h5>
                        <p class="small text-muted">Jadilah relawan pertama yang turun tangan langsung menanam pohon di lokasi konservasi!</p>
                        <a href="<?= url('/kegiatan') ?>" class="btn btn-success rounded-pill px-4 fw-semibold mt-2">
                            Lihat Jadwal Relawan
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-header bg-white py-3 border-light-subtle">
                            <h5 class="fw-bold text-dark mb-0">Pejuang Hijau: Relawan Paling Aktif</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase text-muted">
                                    <tr>
                                        <th class="ps-4" style="width: 80px;">Peringkat</th>
                                        <th>Nama Relawan</th>
                                        <th class="text-center">Aksi Penanaman Diikuti</th>
                                        <th class="text-end pe-4">Terakhir Ikut Serta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topVolunteers as $idx => $v): 
                                        $rank = $idx + 1;
                                    ?>
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-success-subtle rounded-pill px-2 py-1 fw-bold" style="color:#0a3622;">#<?= $rank ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary-subtle text-primary rounded-circle fw-bold me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                        <?= strtoupper(substr($v['display_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($v['display_name']) ?></div>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill small">
                                                            <i class="bi bi-award-fill me-1"></i> Relawan Teladan
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-success fs-6"><?= (int)$v['events_attended'] ?></span>
                                                <span class="text-muted small">Kegiatan</span>
                                            </td>
                                            <td class="text-end pe-4 small text-muted">
                                                <?= $v['last_attended_at'] ? date('d M Y', strtotime($v['last_attended_at'])) : '-' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Info Card Privasi -->
        <div class="card border-0 rounded-4 bg-white p-4 shadow-sm my-5">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-2 text-success">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Kebijakan Privasi Donatur</h6>
                    <p class="small text-muted mb-0">
                        Kami sangat menghargai privasi Anda. Jika Anda memilih opsi <strong>"Donasi sebagai Hamba Allah / Anonim"</strong> pada saat checkout donasi, nama Anda akan secara otomatis disamarkan di seluruh halaman publik dan papan peringkat ini demi menjaga kerahasiaan Anda.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>

