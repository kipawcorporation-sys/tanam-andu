<?php
/**
 * View: dashboard/ticket_detail.php — TanamPohon.id
 * Detail tiket bantuan dengan thread percakapan bubble chat.
 */
$ticket  = $ticket ?? [];
$replies = $replies ?? [];

$currentUserId = (int)auth('id');

$statusConfig = [
    'open'    => ['class' => 'bg-warning text-dark', 'icon' => 'envelope-open',    'label' => 'Terbuka'],
    'replied' => ['class' => 'bg-success',           'icon' => 'reply-all',         'label' => 'Dibalas'],
    'closed'  => ['class' => 'bg-secondary',         'icon' => 'check2-circle',     'label' => 'Selesai'],
];
$st = $statusConfig[$ticket['status'] ?? 'open'] ?? $statusConfig['open'];
$isClosed = ($ticket['status'] === 'closed');
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard/bantuan" class="text-success text-decoration-none">Pusat Bantuan</a></li>
        <li class="breadcrumb-item active">Tiket #<?= $ticket['id'] ?></li>
    </ol>
</nav>

<?php if ($error = Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($success = Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Header Tiket -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge <?= $st['class'] ?> rounded-pill">
                        <i class="bi bi-<?= $st['icon'] ?> me-1"></i><?= $st['label'] ?>
                    </span>
                    <span class="text-muted small">#<?= $ticket['id'] ?></span>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($ticket['subject']) ?></h5>
                <div class="d-flex align-items-center gap-3 text-muted small">
                    <span><i class="bi bi-calendar3 me-1"></i>
                        <?= date('d M Y, H:i', strtotime($ticket['created_at'])) ?> WIB
                    </span>
                    <span><i class="bi bi-chat me-1"></i><?= count($replies) ?> balasan</span>
                </div>
            </div>
            <?php if (!$isClosed): ?>
                <form action="/dashboard/bantuan/<?= $ticket['id'] ?>/tutup" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                            onclick="return confirm('Tutup tiket ini? Anda tidak dapat membalas setelah tiket ditutup.')">
                        <i class="bi bi-x-circle me-1"></i>Tutup Tiket
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Thread Percakapan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold"><i class="bi bi-chat-square-text me-2 text-success"></i>Percakapan</h6>
    </div>
    <div class="card-body p-4">
        <!-- Pesan awal dari user -->
        <div class="d-flex gap-3 mb-4">
            <div class="flex-shrink-0">
                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width:40px;height:40px;font-size:.9rem;">
                    <?= strtoupper(substr(auth('name') ?? 'U', 0, 1)) ?>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="bg-success bg-opacity-10 border border-success-subtle rounded-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-success small">Anda (Pengirim)</span>
                        <span class="text-muted" style="font-size:.75rem;">
                            <?= date('d M Y, H:i', strtotime($ticket['created_at'])) ?> WIB
                        </span>
                    </div>
                    <p class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars($ticket['message']) ?></p>
                </div>
            </div>
        </div>

        <?php foreach ($replies as $reply): ?>
            <?php $isAdmin = !empty($reply['is_admin']); ?>
            <div class="d-flex gap-3 mb-4 <?= $isAdmin ? 'flex-row-reverse' : '' ?>">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <?php if ($isAdmin): ?>
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                             style="width:40px;height:40px;">
                            <i class="bi bi-headset"></i>
                        </div>
                    <?php else: ?>
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:40px;height:40px;font-size:.9rem;">
                            <?= strtoupper(substr($reply['replier_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Bubble -->
                <div class="flex-grow-1" style="max-width:85%;">
                    <div class="<?= $isAdmin
                        ? 'bg-primary text-white'
                        : 'bg-success bg-opacity-10 border border-success-subtle' ?> rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                            <span class="fw-semibold small <?= $isAdmin ? 'text-white-50' : 'text-success' ?>">
                                <?= $isAdmin
                                    ? '<i class="bi bi-shield-check me-1"></i>Admin Plant For Fun'
                                    : htmlspecialchars($reply['replier_name'] ?? 'Anda') ?>
                            </span>
                            <span class="<?= $isAdmin ? 'text-white-50' : 'text-muted' ?>" style="font-size:.75rem;white-space:nowrap;">
                                <?= date('d M Y, H:i', strtotime($reply['created_at'])) ?> WIB
                            </span>
                        </div>
                        <p class="mb-0" style="white-space:pre-wrap;"><?= htmlspecialchars($reply['message']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($isClosed): ?>
            <div class="text-center mt-2 py-3 border-top">
                <span class="badge bg-secondary px-3 py-2 rounded-pill">
                    <i class="bi bi-check2-circle me-1"></i>Tiket telah ditutup
                </span>
                <p class="text-muted small mt-2 mb-0">Butuh bantuan lagi?
                    <a href="/dashboard/bantuan" class="text-success text-decoration-none">Buka tiket baru</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Form Balas -->
<?php if (!$isClosed): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-bold"><i class="bi bi-reply me-2 text-success"></i>Tambah Balasan</h6>
        </div>
        <div class="card-body">
            <form action="/dashboard/bantuan/<?= $ticket['id'] ?>/balas" method="POST" id="replyForm">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <textarea name="message" class="form-control" rows="4" id="replyMessage"
                              placeholder="Tulis balasan atau informasi tambahan..."
                              required minlength="5"></textarea>
                    <div class="form-text">
                        <span id="replyCount">0</span> karakter · minimal 5 karakter
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-send me-1"></i>Kirim Balasan
                    </button>
                    <a href="/dashboard/bantuan" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="text-center">
        <a href="/dashboard/bantuan" class="btn btn-outline-success">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Tiket
        </a>
    </div>
<?php endif; ?>

<script>
// Character counter untuk reply
document.getElementById('replyMessage')?.addEventListener('input', function() {
    document.getElementById('replyCount').textContent = this.value.length;
});

// Auto-scroll ke bawah percakapan
window.addEventListener('DOMContentLoaded', () => {
    const lastReply = document.querySelectorAll('.card-body .d-flex.gap-3');
    if (lastReply.length) {
        lastReply[lastReply.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

