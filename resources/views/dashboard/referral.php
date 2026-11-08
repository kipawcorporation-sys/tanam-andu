<?php 
    $user = !empty($user) ? $user : (auth() ?: []); 
?>
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-people-fill text-success me-2"></i>Ajak Teman (Program Referral)
        </h3>
        <p class="text-muted mb-0">Sebarkan semangat pelestarian alam ke keluarga dan sahabat. Dapatkan bonus poin untuk setiap teman yang mendaftar dan berdonasi.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6 fw-bold">
            <i class="bi bi-coin me-1"></i> Poin Saya: <?= number_format($user['points'] ?? 0) ?> Poin
        </span>
    </div>
</div>

<!-- Banner Hero & Bagikan Tautan -->
<div class="card border-0 shadow-sm rounded-4 mb-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #198754 0%, #0d5935 100%);">
    <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1 mb-3">
                    <i class="bi bi-gift-fill me-1"></i> Bonus +25 Poin Per Teman
                </span>
                <h3 class="fw-bold mb-2">Ajak Sahabat Menanam Pohon Bersama!</h3>
                <p class="text-white text-opacity-85 mb-4">
                    Setiap teman yang mendaftar menggunakan kode atau tautan referral Anda akan langsung terhubung ke jaringan kebaikan Anda. Bersama-sama, kita ciptakan hutan baru bagi Indonesia!
                </p>

                <!-- Input Tautan & Kode Referral -->
                <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
                    <label class="form-label text-muted small fw-bold mb-1">Kode Referral Anda:</label>
                    <div class="d-flex align-items-center justify-content-between mb-3 bg-light p-2 rounded-3 border">
                        <span class="font-monospace fw-bold fs-4 text-success ps-2" id="referralCodeText"><?= e($user['referral_code']) ?></span>
                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 copy-btn" data-clipboard="<?= e($user['referral_code']) ?>">
                            <i class="bi bi-clipboard"></i> Salin Kode
                        </button>
                    </div>

                    <label class="form-label text-muted small fw-bold mb-1">Tautan Pendaftaran Langsung:</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace small bg-light" id="referralUrlInput" value="<?= e($referralUrl) ?>" readonly>
                        <button class="btn btn-success px-4 copy-btn" type="button" data-clipboard="<?= e($referralUrl) ?>">
                            <i class="bi bi-clipboard-check me-1"></i> Salin Tautan
                        </button>
                    </div>
                </div>

                <!-- Tombol Bagikan Media Sosial -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="small text-white text-opacity-75 me-1">Bagikan cepat ke:</span>
                    <a href="https://api.whatsapp.com/send?text=<?= urlencode($shareText) ?>" target="_blank" class="btn btn-light btn-sm rounded-pill text-success fw-bold px-3 shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($shareText) ?>" target="_blank" class="btn btn-light btn-sm rounded-pill text-dark fw-bold px-3 shadow-sm">
                        <i class="bi bi-twitter-x me-1"></i> Twitter / X
                    </a>
                    <a href="https://t.me/share/url?url=<?= urlencode($referralUrl) ?>&text=<?= urlencode('Yuk tanam pohon bersama di TanamPohon.id!') ?>" target="_blank" class="btn btn-light btn-sm rounded-pill text-primary fw-bold px-3 shadow-sm">
                        <i class="bi bi-telegram me-1"></i> Telegram
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($referralUrl) ?>" target="_blank" class="btn btn-light btn-sm rounded-pill text-primary fw-bold px-3 shadow-sm">
                        <i class="bi bi-facebook me-1"></i> Facebook
                    </a>
                </div>
            </div>

            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="display-1 text-white opacity-25">🤝🌳</div>
                <div class="fw-bold fs-5 text-warning mt-2">Dampak Nyata Bersama Sahabat</div>
                <div class="small text-white text-opacity-75">1 Pohon Berarti Harapan Baru</div>
            </div>
        </div>
    </div>
</div>

<!-- 3 Kartu Statistik Referral -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 text-center">
                <div class="avatar-stat mx-auto mb-2 text-primary fs-2">👥</div>
                <div class="display-6 fw-bold text-dark"><?= number_format($referralStats['total_referred']) ?></div>
                <div class="text-muted small fw-semibold">Teman Bergabung</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 text-center">
                <div class="avatar-stat mx-auto mb-2 text-success fs-2">🌲</div>
                <div class="display-6 fw-bold text-success"><?= number_format($referralStats['total_trees_from_referrals']) ?></div>
                <div class="text-muted small fw-semibold">Pohon Ditanam Teman</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 text-center">
                <div class="avatar-stat mx-auto mb-2 text-warning-emphasis fs-2">🪙</div>
                <div class="display-6 fw-bold" style="color: #b45309;"><?= number_format($referralStats['total_bonus_points']) ?></div>
                <div class="text-muted small fw-semibold">Bonus Poin Didapat</div>
            </div>
        </div>
    </div>
</div>

<!-- Cara Kerja Program Referral -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4">
            <i class="bi bi-info-circle-fill text-success me-2"></i>Cara Kerja Program Ajak Teman
        </h5>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <div class="rounded-circle bg-success-subtle fw-bold d-inline-flex align-items-center justify-content-center mb-3" style="color:#0a3622; width: 50px; height: 50px; font-size: 20px;">
                        1
                    </div>
                    <h6 class="fw-bold text-dark">Bagikan Tautan</h6>
                    <p class="text-muted small mb-0">Salin dan kirimkan kode atau link pendaftaran unik Anda kepada teman, keluarga, atau komunitas Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="rounded-circle bg-warning-subtle text-warning fw-bold d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                        2
                    </div>
                    <h6 class="fw-bold text-dark">Teman Mendaftar</h6>
                    <p class="text-muted small mb-0">Teman Anda mendaftar akun di TanamPohon.id dengan kode referral Anda terisi secara otomatis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px; font-size: 20px;">
                        3
                    </div>
                    <h6 class="fw-bold text-dark">Raih Bonus Poin</h6>
                    <p class="text-muted small mb-0">Anda dan teman Anda otomatis mendapatkan bonus poin kontribusi yang dapat ditukarkan dengan merchandise.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Teman yang Bergabung -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Teman yang Bergabung
            </h5>
            <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                Total: <?= count($referralStats['friends']) ?> Orang
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($referralStats['friends'])): ?>
            <div class="text-center py-5">
                <div class="display-3 text-muted mb-3">🌱</div>
                <h6 class="fw-bold text-dark">Belum ada teman yang bergabung</h6>
                <p class="text-muted small mb-4">Ayo bagikan tautan referral Anda sekarang dan jadilah pelopor kebaikan lingkungan di lingkungan Anda!</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-4">Nama Sahabat</th>
                            <th>Tanggal Bergabung</th>
                            <th>Pohon Ditanam</th>
                            <th class="pe-4 text-end">Bonus Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($referralStats['friends'] as $f): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle rounded-circle bg-success-subtle d-flex align-items-center justify-content-center fw-bold" style="color:#0a3622; width: 36px; height: 36px; font-size: 14px;">
                                            <?= strtoupper(substr($f['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= e($f['masked_name']) ?></div>
                                            <div class="text-muted small">Peserta Terdaftar</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    <?= format_date($f['joined_at'], 'd M Y, H:i') ?> WIB
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle border border-success-subtle rounded-pill px-3 py-1 fw-bold" style="color:#0a3622;">
                                        <?= $f['trees_planted'] ?> Batang
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">
                                        +<?= $f['bonus_points'] ?? 25 ?> Poin
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-clipboard');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check2"></i> Disalin!';
                    this.classList.replace('btn-outline-success', 'btn-success');
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.classList.replace('btn-success', 'btn-outline-success');
                    }, 2000);
                });
            }
        });
    });
});
</script>
