<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-bell-fill text-success me-2"></i>Notifikasi Saya
        </h3>
        <p class="text-muted mb-0">Informasi status verifikasi pembayaran, tiket kegiatan, lencana baru, dan pengingat jadwal aksi penanaman.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <?php if ($unreadCount > 0): ?>
            <form action="<?= url('/dashboard/notifikasi/baca-semua') ?>" method="POST" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-success rounded-pill px-3 shadow-sm btn-sm">
                    <i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Navigasi Filter Notifikasi -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-2 p-md-3">
        <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $filter === 'all' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/notifikasi?filter=all') ?>">
                    <i class="bi bi-inbox-fill me-1"></i> Semua Notifikasi
                    <span class="badge <?= $filter === 'all' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $allCount ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $filter === 'unread' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/notifikasi?filter=unread') ?>">
                    <i class="bi bi-envelope-fill me-1"></i> Belum Dibaca
                    <span class="badge <?= $filter === 'unread' ? 'bg-danger text-white' : 'bg-danger text-white' ?> ms-1 rounded-pill"><?= $unreadCount ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Daftar Notifikasi -->
<?php if (empty($notifications)): ?>
    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
        <div class="card-body">
            <div class="display-3 text-muted mb-3">🔔</div>
            <h5 class="fw-bold text-dark">Tidak ada notifikasi</h5>
            <p class="text-muted mb-0"><?= $filter === 'unread' ? 'Semua notifikasi telah Anda baca.' : 'Anda belum memiliki riwayat pemberitahuan.' ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-3 mb-4">
        <?php foreach ($notifications as $notif): ?>
            <?php
                $isUnread = empty($notif['is_read']);
                $type = $notif['type'] ?? 'system';

                // Tentukan ikon & warna sesuai tipe notifikasi
                $icon = 'bi-info-circle-fill';
                $iconColor = 'text-primary';
                $badgeBg = 'bg-primary-subtle';

                if ($type === 'payment') {
                    $icon = 'bi-wallet2';
                    $iconColor = 'text-success';
                    $badgeBg = 'bg-success-subtle';
                } elseif ($type === 'donation') {
                    $icon = 'bi-heart-fill';
                    $iconColor = 'text-danger';
                    $badgeBg = 'bg-danger-subtle';
                } elseif ($type === 'event') {
                    $icon = 'bi-calendar2-check-fill';
                    $iconColor = 'text-primary';
                    $badgeBg = 'bg-primary-subtle';
                } elseif ($type === 'order') {
                    $icon = 'bi-bag-check-fill';
                    $iconColor = 'text-info';
                    $badgeBg = 'bg-info-subtle';
                } elseif ($type === 'badge') {
                    $icon = 'bi-award-fill';
                    $iconColor = 'text-warning';
                    $badgeBg = 'bg-warning-subtle';
                } elseif ($type === 'referral') {
                    $icon = 'bi-people-fill';
                    $iconColor = 'text-success';
                    $badgeBg = 'bg-success-subtle';
                }
            ?>

            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden <?= $isUnread ? 'bg-white border-start border-4 border-success' : 'bg-light bg-opacity-75' ?>">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Icon Circle -->
                        <div class="rounded-circle <?= $badgeBg ?> <?= $iconColor ?> d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 20px;">
                            <i class="bi <?= $icon ?>"></i>
                        </div>

                        <!-- Content Body -->
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                <h6 class="mb-0 fw-bold <?= $isUnread ? 'text-dark' : 'text-secondary' ?>">
                                    <?= e($notif['title']) ?>
                                    <?php if ($isUnread): ?>
                                        <span class="badge bg-danger rounded-pill ms-1" style="font-size: 9px;">BARU</span>
                                    <?php endif; ?>
                                </h6>
                                <span class="text-muted small" style="font-size: 11px;">
                                    <i class="bi bi-clock me-1"></i> <?= time_ago($notif['created_at']) ?>
                                </span>
                            </div>

                            <p class="mb-2 text-secondary small">
                                <?= nl2br(e($notif['message'])) ?>
                            </p>

                            <!-- Tombol Aksi Notifikasi -->
                            <div class="d-flex align-items-center gap-2 pt-1">
                                <?php if ($isUnread): ?>
                                    <form action="<?= url('/dashboard/notifikasi/' . $notif['id'] . '/baca') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-link text-success p-0 text-decoration-none small fw-bold">
                                            <i class="bi bi-check2"></i> Tandai Dibaca
                                        </button>
                                    </form>
                                    <span class="text-muted small">&bull;</span>
                                <?php endif; ?>

                                <form action="<?= url('/dashboard/notifikasi/' . $notif['id'] . '/hapus') ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus notifikasi ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-link text-muted p-0 text-decoration-none small hover-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Info Preferensi Saluran Notifikasi -->
<div class="card border-0 shadow-sm rounded-4 bg-light">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-2 text-success">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-1">Preferensi Saluran Pemberitahuan</h6>
                <p class="text-muted small mb-0">
                    Sistem Plant For Fun otomatis mengirimkan pengingat batas transfer (3 jam sebelum kedaluwarsa) dan pengingat kegiatan relawan (H-3 dan H-1) via email & WhatsApp. Anda dapat mengatur preferensi kontak di <a href="<?= url('/dashboard/profil') ?>" class="text-success fw-bold text-decoration-none">Pengaturan Profil</a>.
                </p>
            </div>
        </div>
    </div>
</div>

