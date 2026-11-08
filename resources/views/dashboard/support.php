<?php
/**
 * View: dashboard/support.php — TanamPohon.id
 * Pusat Bantuan: Daftar tiket + modal kirim tiket baru
 */
$tickets      = $tickets ?? [];
$countOpen    = $countOpen ?? 0;
$countReplied = $countReplied ?? 0;
$countClosed  = $countClosed ?? 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-headset me-2 text-success"></i>Pusat Bantuan</h4>
        <p class="text-muted small mb-0">Kirim pertanyaan atau laporan masalah kepada tim kami</p>
    </div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalKirimTiket">
        <i class="bi bi-plus-lg me-1"></i>Tiket Baru
    </button>
</div>

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

<!-- Ringkasan Status -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-envelope-open text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $countOpen ?></div>
                    <div class="text-muted small">Tiket Terbuka</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-success-subtle rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-reply text-success fs-5"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $countReplied ?></div>
                    <div class="text-muted small">Sudah Dibalas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-check2-all text-secondary fs-5"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $countClosed ?></div>
                    <div class="text-muted small">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Tiket -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
        <i class="bi bi-ticket-perforated text-success"></i>
        <span class="fw-semibold">Riwayat Tiket Bantuan</span>
        <span class="badge bg-secondary ms-auto"><?= count($tickets) ?> tiket</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($tickets)): ?>
            <div class="text-center py-5">
                <i class="bi bi-headset text-muted" style="font-size:3rem;"></i>
                <p class="text-muted mt-3 mb-1">Belum ada tiket bantuan.</p>
                <p class="text-muted small">Jika mengalami kendala, klik <strong>"Tiket Baru"</strong> untuk menghubungi kami.</p>
                <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#modalKirimTiket">
                    <i class="bi bi-plus-lg me-1"></i>Kirim Tiket Pertama
                </button>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php
                $statusConfig = [
                    'open'    => ['class' => 'bg-warning text-dark', 'icon' => 'envelope-open',    'label' => 'Terbuka'],
                    'replied' => ['class' => 'bg-success',           'icon' => 'reply-all',         'label' => 'Dibalas'],
                    'closed'  => ['class' => 'bg-secondary',         'icon' => 'check2-circle',     'label' => 'Selesai'],
                ];
                foreach ($tickets as $ticket):
                    $st = $statusConfig[$ticket['status']] ?? $statusConfig['open'];
                    $lastMsg = $ticket['last_reply']
                        ? mb_substr(strip_tags($ticket['last_reply']), 0, 80) . '...'
                        : mb_substr($ticket['message'], 0, 80) . '...';
                    $isAdmin = !empty($ticket['last_reply_is_admin']);
                ?>
                    <a href="/dashboard/bantuan/<?= $ticket['id'] ?>"
                       class="list-group-item list-group-item-action border-bottom py-3 px-4 text-decoration-none">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge <?= $st['class'] ?> rounded-pill">
                                        <i class="bi bi-<?= $st['icon'] ?> me-1"></i><?= $st['label'] ?>
                                    </span>
                                    <span class="fw-semibold text-truncate"><?= htmlspecialchars($ticket['subject']) ?></span>
                                    <span class="badge bg-light text-muted border ms-auto">
                                        #<?= $ticket['id'] ?>
                                    </span>
                                </div>
                                <p class="text-muted small mb-1 text-truncate">
                                    <?php if ($ticket['last_reply']): ?>
                                        <i class="bi bi-<?= $isAdmin ? 'headset text-primary' : 'person' ?> me-1"></i>
                                        <?= htmlspecialchars($lastMsg) ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($lastMsg) ?>
                                    <?php endif; ?>
                                </p>
                                <div class="d-flex align-items-center gap-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= time_ago($ticket['last_reply_at'] ?? $ticket['created_at']) ?>
                                    </small>
                                    <?php if ($ticket['reply_count'] > 0): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-chat me-1"></i><?= $ticket['reply_count'] ?> balasan
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted flex-shrink-0 mt-1"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- FAQ Cepat -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-bold"><i class="bi bi-question-circle me-2 text-info"></i>Pertanyaan Umum</h6>
    </div>
    <div class="card-body">
        <div class="accordion accordion-flush" id="faqAccordion">
            <?php
            $faqs = [
                ['Bagaimana cara memverifikasi pembayaran saya?', 'Setelah transfer, unggah bukti pembayaran melalui halaman donasi atau checkout. Tim admin akan memverifikasi dalam 1×24 jam.'],
                ['Kapan sertifikat donasi diterbitkan?', 'Sertifikat diterbitkan otomatis setelah pohon berhasil ditanam dan admin menandai status kegiatan selesai.'],
                ['Bagaimana cara membatalkan pesanan kaos?', 'Pesanan dapat dibatalkan selama belum berstatus "Dikirim". Buka menu Pesanan Saya dan klik tombol Batalkan.'],
                ['Apakah ada biaya administrasi?', 'Tidak ada biaya administrasi tambahan. 100% donasi Anda digunakan untuk program penanaman pohon.'],
                ['Bagaimana cara menggunakan kode referral saya?', 'Bagikan kode unik Anda ke teman. Ketika mereka mendaftar dan berdonasi, Anda otomatis mendapat poin bonus.'],
            ];
            foreach ($faqs as $i => [$q, $a]):
            ?>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed px-0 fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq-<?= $i ?>">
                            <?= htmlspecialchars($q) ?>
                        </button>
                    </h2>
                    <div id="faq-<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body ps-0 text-muted">
                            <?= htmlspecialchars($a) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ===== MODAL: Kirim Tiket Baru ===== -->
<div class="modal fade" id="modalKirimTiket" tabindex="-1" aria-labelledby="modalKirimTiketLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/dashboard/bantuan/kirim" method="POST" id="formKirimTiket">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalKirimTiketLabel">
                        <i class="bi bi-send me-2 text-success"></i>Kirim Tiket Bantuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex gap-2 align-items-start mb-4">
                        <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
                        <div>Tim kami merespons dalam <strong>1×24 jam kerja</strong>.
                        Mohon berikan detail yang cukup agar kami dapat membantu dengan cepat.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subjek / Topik <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control"
                               placeholder="Misal: Pembayaran tidak terverifikasi, Tiket tidak muncul, dll."
                               required minlength="5" maxlength="200" id="subjectInput">
                        <div class="form-text"><span id="subjectCount">0</span>/200 karakter</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Masalah <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="6"
                                  placeholder="Jelaskan masalah Anda secara detail. Sertakan nomor transaksi, tanggal, atau informasi relevan lainnya."
                                  required minlength="20" id="messageInput"></textarea>
                        <div class="form-text"><span id="messageCount">0</span>/2000 karakter · minimal 20 karakter</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-muted small">KATEGORI (opsional)</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $categories = ['Pembayaran', 'Donasi', 'Kegiatan', 'Pesanan', 'Akun', 'Sertifikat', 'Lainnya'];
                            foreach ($categories as $cat):
                            ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary cat-btn"
                                        data-cat="<?= $cat ?>">
                                    <?= $cat ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="category" id="categoryInput" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-send me-1"></i>Kirim Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Character counter
document.getElementById('subjectInput')?.addEventListener('input', function() {
    document.getElementById('subjectCount').textContent = this.value.length;
});
document.getElementById('messageInput')?.addEventListener('input', function() {
    document.getElementById('messageCount').textContent = this.value.length;
});

// Category selection
document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.replace('btn-success', 'btn-outline-secondary'));
        this.classList.replace('btn-outline-secondary', 'btn-success');
        document.getElementById('categoryInput').value = this.dataset.cat;

        // Auto-fill subject prefix if empty
        const subjInput = document.getElementById('subjectInput');
        if (!subjInput.value) {
            subjInput.value = '[' + this.dataset.cat + '] ';
            document.getElementById('subjectCount').textContent = subjInput.value.length;
        }
    });
});
</script>

