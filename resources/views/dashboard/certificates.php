<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-award-fill text-warning me-2"></i>Sertifikat & Pencapaian Saya
        </h3>
        <p class="text-muted mb-0">Pantau lencana level pelestari alam, total dampak lingkungan, dan unduh sertifikat resmi terverifikasi QR Code.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= url('/leaderboard') ?>" class="btn btn-outline-success rounded-pill px-4 shadow-sm">
            <i class="bi bi-trophy-fill text-warning me-1"></i> Lihat Leaderboard Publik
        </a>
    </div>
</div>

<!-- 1. Ringkasan Dampak Lingkungan & Leaderboard Rank -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-globe-americas text-success me-2"></i>Estimasi Dampak Lingkungan Nyata
                    </h5>
                    <span class="badge bg-success-subtle border border-success-subtle rounded-pill px-3 py-1" style="color:#0a3622;">
                        <?= $totalTrees ?> Pohon Berkontribusi
                    </span>
                </div>
                <p class="text-muted small mb-4">
                    Setiap pohon yang Anda donasikan dan tanam dirawat secara berkelanjutan di kawasan konservasi untuk memulihkan ekosistem bumi Indonesia.
                </p>

                <div class="row g-3 text-center">
                    <div class="col-sm-4">
                        <div class="p-3 rounded-4 bg-light">
                            <div class="fs-1 text-success mb-1">🌱</div>
                            <div class="fs-4 fw-bold text-dark"><?= number_format($impact['total_co2_kg'], 1) ?> <span class="fs-6 fw-normal text-muted">kg</span></div>
                            <div class="text-muted small fw-semibold">CO₂ Terserap / Tahun</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 rounded-4 bg-light">
                            <div class="fs-1 text-primary mb-1">💨</div>
                            <div class="fs-4 fw-bold text-dark"><?= number_format($impact['total_o2_kg'], 1) ?> <span class="fs-6 fw-normal text-muted">kg</span></div>
                            <div class="text-muted small fw-semibold">Oksigen Diproduksi / Thn</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 rounded-4 bg-light">
                            <div class="fs-1 text-warning mb-1">🏡</div>
                            <div class="fs-4 fw-bold text-dark"><?= number_format($impact['green_area_sqm']) ?> <span class="fs-6 fw-normal text-muted">m²</span></div>
                            <div class="text-muted small fw-semibold">Luas Tutupan Hijau</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Peringkat Leaderboard -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #198754 0%, #0d5935 100%);">
            <div class="card-body p-4 d-flex flex-column justify-content-between position-relative" style="z-index: 2;">
                <div>
                    <div class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-3">
                        <i class="bi bi-star-fill me-1"></i> Peringkat Penanam
                    </div>
                    <h5 class="fw-bold mb-1">Posisi Anda di Leaderboard</h5>
                    <p class="text-white text-opacity-75 small">Dihitung dari total pohon tertanam aktif di seluruh Indonesia.</p>
                </div>

                <div class="my-3 text-center">
                    <div class="display-3 fw-bold text-warning font-monospace">#<?= $leaderboard['rank'] ?></div>
                    <div class="text-white text-opacity-75 small">dari <?= $leaderboard['total_participants'] ?> Pejuang Lingkungan</div>
                </div>

                <a href="<?= url('/leaderboard') ?>" class="btn btn-light rounded-pill w-100 fw-bold text-success shadow-sm">
                    <i class="bi bi-list-ol me-1"></i> Buka Leaderboard Lengkap
                </a>
            </div>
            <!-- Background Deco -->
            <div class="position-absolute end-0 bottom-0 text-white opacity-10" style="font-size: 10rem; transform: translate(20%, 20%); pointer-events: none;">
                🌳
            </div>
        </div>
    </div>
</div>

<!-- 2. Lencana Pencapaian (Badges) -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-shield-check text-warning me-2"></i>Lencana Level Penanam
                </h5>
                <p class="text-muted small mb-0">Raih lencana eksklusif seiring bertambahnya pohon yang Anda donasikan.</p>
            </div>
            <div>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fs-6">
                    <i class="bi bi-patch-check-fill text-success me-1"></i> <?= count($earnedBadgeIds) ?> dari <?= count($allBadges) ?> Lencana Terbuka
                </span>
            </div>
        </div>

        <!-- Progres ke Lencana Berikutnya -->
        <?php if ($nextBadgeProgress): ?>
            <div class="bg-light rounded-4 p-3 mt-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-dark">
                        <i class="bi bi-arrow-up-circle-fill text-success me-1"></i> Lencana Berikutnya: <strong><?= e($nextBadgeProgress['badge']['name']) ?></strong>
                    </span>
                    <span class="small text-muted font-monospace fw-bold">
                        <?= $totalTrees ?> / <?= $nextBadgeProgress['badge']['min_trees'] ?> Pohon (<?= $nextBadgeProgress['percentage'] ?>%)
                    </span>
                </div>
                <div class="progress rounded-pill" style="height: 10px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $nextBadgeProgress['percentage'] ?>%" aria-valuenow="<?= $nextBadgeProgress['percentage'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-muted small mt-1">
                    Tanam <strong><?= $nextBadgeProgress['remaining'] ?> pohon lagi</strong> untuk membuka lencana ini!
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-success border-0 rounded-4 mt-3 mb-0 p-3">
                <i class="bi bi-trophy-fill text-warning me-2"></i>
                <strong>Luar Biasa!</strong> Anda telah mencapai lencana tertinggi: <strong>Legenda Hijau 🌟</strong>. Terima kasih atas dedikasi tanpa batas untuk bumi Indonesia.
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">
            <?php foreach ($allBadges as $badge): ?>
                <?php 
                    $isEarned = in_array((int)$badge['id'], $earnedBadgeIds);
                    $earnedDate = $badgeEarnedDates[$badge['id']] ?? null;
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 rounded-4 border <?= $isEarned ? 'border-success bg-success-subtle bg-opacity-25' : 'border-light-subtle bg-light opacity-75' ?> transition-all">
                        <div class="card-body p-3 text-center">
                            <div class="avatar-badge mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle <?= $isEarned ? 'bg-warning text-dark shadow-sm' : 'bg-secondary-subtle text-muted' ?>" style="width: 64px; height: 64px; font-size: 28px;">
                                <?php if ($isEarned): ?>
                                    🏆
                                <?php else: ?>
                                    🔒
                                <?php endif; ?>
                            </div>

                            <h6 class="fw-bold mb-1 <?= $isEarned ? 'text-success' : 'text-dark' ?>">
                                <?= e($badge['name']) ?>
                            </h6>
                            <p class="text-muted small mb-2" style="min-height: 38px;">
                                <?= e($badge['description']) ?>
                            </p>

                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <?php if ($isEarned): ?>
                                    <span class="badge bg-success text-white rounded-pill px-3 py-1 small">
                                        <i class="bi bi-check-circle-fill me-1"></i> Diraih <?= format_date($earnedDate, 'd M Y') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-white text-muted border rounded-pill px-3 py-1 small">
                                        <i class="bi bi-lock-fill me-1"></i> Butuh <?= $badge['min_trees'] ?> Pohon
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 3. Daftar Sertifikat Resmi Saya -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Sertifikat Resmi Penghargaan
                </h5>
                <p class="text-muted small mb-0">Semua sertifikat digital donasi pohon dan partisipasi relawan Anda tersimpan aman dan dapat dicetak sewaktu-waktu.</p>
            </div>
            <div>
                <span class="badge bg-success text-white rounded-pill px-3 py-2 fs-6">
                    <?= count($certificates) ?> Sertifikat Diterbitkan
                </span>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <?php if (empty($certificates)): ?>
            <div class="text-center py-5">
                <div class="display-3 text-muted mb-3">📜</div>
                <h6 class="fw-bold text-dark">Belum ada sertifikat yang diterbitkan</h6>
                <p class="text-muted small mb-4">Sertifikat akan otomatis diterbitkan begitu donasi Anda terverifikasi lunas atau setelah Anda menghadiri kegiatan relawan.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= url('/donasi') ?>" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-heart-fill me-1"></i> Donasi Pohon Sekarang
                    </a>
                    <a href="<?= url('/kegiatan') ?>" class="btn btn-outline-success rounded-pill px-4">
                        <i class="bi bi-calendar-event me-1"></i> Gabung Relawan
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($certificates as $cert): ?>
                    <?php 
                        $isVolunteer = (($cert['type'] ?? '') === 'partisipasi' || ($cert['certifiable_type'] ?? '') === 'participant');
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 rounded-4 border-0 shadow-sm overflow-hidden position-relative hover-shadow transition-all">
                            <!-- Visual Header Sertifikat -->
                            <div class="p-3 text-center position-relative" style="background: linear-gradient(135deg, <?= $isVolunteer ? '#0d6efd' : '#198754' ?> 0%, #1a3c34 100%); color: #ffffff;">
                                <div class="fs-1 mb-1"><?= $isVolunteer ? '🎖️' : '🌿' ?></div>
                                <div class="badge bg-warning text-dark font-monospace fw-bold mb-1">
                                    <?= e($cert['certificate_number']) ?>
                                </div>
                                <div class="small text-white text-opacity-75">
                                    <?= $isVolunteer ? 'Sertifikat Relawan Aksi Hijau' : 'Sertifikat Donasi Pelestarian Alam' ?>
                                </div>
                            </div>

                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="fw-bold text-dark fs-6 mb-1">
                                        <?= e($cert['data']['recipient_name'] ?? 'Sahabat Peduli Bumi') ?>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        <?php if ($isVolunteer): ?>
                                            Partisipasi pada kegiatan: <strong><?= e($cert['data']['event_title'] ?? 'Aksi Penanaman') ?></strong>
                                        <?php else: ?>
                                            Donasi <strong><?= $cert['data']['total_trees'] ?? 1 ?> Batang Pohon</strong> (Serapan: <?= number_format($cert['data']['total_co2_kg'] ?? 25, 1) ?> kg CO₂/thn)
                                        <?php endif; ?>
                                    </p>
                                    <div class="text-muted small mb-3">
                                        <i class="bi bi-calendar3 me-1"></i> Diterbitkan: <?= format_date($cert['issued_at'] ?? $cert['created_at'], 'd M Y') ?>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="<?= url('/dashboard/sertifikat/' . $cert['id'] . '/unduh') ?>" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill btn-sm">
                                        <i class="bi bi-printer-fill me-1"></i> Buka & Cetak PDF
                                    </a>
                                    <a href="<?= url('/verifikasi-sertifikat/' . e($cert['certificate_number'])) ?>" target="_blank" class="btn btn-outline-secondary rounded-pill btn-sm">
                                        <i class="bi bi-qr-code me-1"></i> Verifikasi Publik
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

