<?php
/**
 * View: public/news_detail.php — Plant For Fun
 * Detail membaca postingan artikel / berita lengkap editorial & profesional.
 */
?>

<div class="py-4 py-md-5" style="background: #f8fafc;">
    <div class="container">
        <!-- Breadcrumb Navigasi -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="/" class="text-success text-decoration-none"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                <li class="breadcrumb-item"><a href="/kabar" class="text-success text-decoration-none">Kabar &amp; Berita</a></li>
                <li class="breadcrumb-item"><a href="/kabar?kategori=<?= urlencode($post['category'] ?? 'Umum') ?>" class="text-success text-decoration-none"><?= htmlspecialchars($post['category'] ?? 'Umum') ?></a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 280px;" aria-current="page"><?= htmlspecialchars($post['title']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Konten Artikel Utama (8 Kolom) -->
            <div class="col-lg-8">
                <article class="card border-0 shadow-sm rounded-4 overflow-hidden p-4 p-md-5 bg-white mb-4">
                    <!-- Header Editorial -->
                    <header class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                            <span class="badge px-3 py-2 rounded-pill fw-bold font-mono" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:0.75rem;">
                                <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($post['category'] ?? 'Kabar Lapangan') ?>
                            </span>
                            <?php if (!empty($post['is_featured'])): ?>
                                <span class="badge px-3 py-2 rounded-pill fw-bold font-mono" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:0.75rem;">
                                    <i class="bi bi-star-fill me-1" style="color: #d97706;"></i>BERITA UTAMA
                                </span>
                            <?php endif; ?>
                        </div>

                        <h1 class="fw-bold text-dark mb-3" style="font-family: 'Bitter', serif; line-height: 1.25; font-size: clamp(1.75rem, 3.5vw, 2.5rem);">
                            <?= htmlspecialchars($post['title']) ?>
                        </h1>

                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="lead text-secondary mb-4" style="font-size: 1.1rem; line-height: 1.6;">
                                <?= htmlspecialchars($post['excerpt']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Author & Metadata Bar -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-3 border-top border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width:44px; height:44px; font-size:1.1rem;">
                                    <?= strtoupper(substr($post['author_name'] ?? 'Admin', 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($post['author_name'] ?? 'Tim Ekspedisi') ?></div>
                                    <div class="text-muted small" style="font-size: 0.76rem;">Penulis &bull; Plant For Fun Expedition</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 text-muted small">
                                <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?></span>
                                <span>&bull;</span>
                                <span><i class="bi bi-clock me-1"></i><?= $post['read_time_minutes'] ?? 3 ?> mnt baca</span>
                                <span>&bull;</span>
                                <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-eye text-primary me-1"></i><?= number_format($post['views_count'] ?? 1) ?> pembaca</span>
                            </div>
                        </div>
                    </header>

                    <!-- Foto Sampul Utama -->
                    <?php if (!empty($post['image'])): ?>
                        <div class="mb-5 text-center">
                            <img src="<?= htmlspecialchars($post['image']) ?>"
                                 alt="<?= htmlspecialchars($post['title']) ?>"
                                 class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover"
                                 style="max-height: 480px;">
                            <div class="text-muted small mt-2 fst-italic">
                                Dokumentasi Lapangan &bull; Plant For Fun Kerenceng Agroforestry Expedition
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Isi Artikel dengan Pemformatan Indah -->
                    <div class="article-body text-dark" style="font-size: 1.05rem; line-height: 1.85; word-break: break-word; overflow-wrap: break-word;">
                        <?php 
                        // Sederhana parser markdown untuk heading, quote, list
                        $raw = htmlspecialchars($post['content']);
                        $paragraphs = explode("\n\n", $raw);
                        foreach ($paragraphs as $para): 
                            $trimmed = trim($para);
                            if (str_starts_with($trimmed, '### ')) {
                                echo '<h3 class="fw-bold mt-4 mb-3 text-dark" style="font-family: \'Bitter\', serif; font-size:1.45rem;">' . substr($trimmed, 4) . '</h3>';
                            } elseif (str_starts_with($trimmed, '> ')) {
                                $quoteText = substr($trimmed, 2);
                                echo '<blockquote class="p-3 my-4 rounded-3 border-start border-4 border-success bg-light text-secondary fst-italic" style="font-size:1.05rem;">' . nl2br($quoteText) . '</blockquote>';
                            } else {
                                echo '<p class="mb-4 text-secondary">' . nl2br($trimmed) . '</p>';
                            }
                        endforeach; 
                        ?>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($post['tags'])): ?>
                        <div class="mt-4 pt-3 border-top">
                            <span class="small fw-bold text-muted text-uppercase me-2 font-mono">TAGS / LABEL:</span>
                            <div class="d-inline-flex flex-wrap gap-1 mt-2">
                                <?php 
                                $tags = explode(',', $post['tags']);
                                foreach ($tags as $t): 
                                    $tClean = trim($t);
                                ?>
                                    <a href="/kabar?q=<?= urlencode($tClean) ?>" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill font-mono" style="font-size: 0.74rem;">
                                        #<?= htmlspecialchars($tClean) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <!-- Bar Tombol Bagikan (Share Bar) -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 bg-light p-3 rounded-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold small text-dark"><i class="bi bi-share me-1"></i>Bagikan Artikel:</span>
                            
                            <!-- WhatsApp -->
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . "\n\n" . url('/kabar/' . $post['slug'])) ?>"
                               target="_blank" class="btn btn-sm btn-success rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="Bagikan via WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            
                            <!-- Twitter / X -->
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode(url('/kabar/' . $post['slug'])) ?>"
                               target="_blank" class="btn btn-sm btn-dark rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="Bagikan via Twitter / X">
                                <i class="bi bi-twitter-x"></i>
                            </a>

                            <!-- Telegram -->
                            <a href="https://t.me/share/url?url=<?= urlencode(url('/kabar/' . $post['slug'])) ?>&text=<?= urlencode($post['title']) ?>"
                               target="_blank" class="btn btn-sm btn-primary rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="Bagikan via Telegram">
                                <i class="bi bi-telegram"></i>
                            </a>

                            <!-- Salin Tautan -->
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="copyArticleLink(this)">
                                <i class="bi bi-link-45deg me-1"></i><span id="btnCopyText">Salin Tautan</span>
                            </button>
                        </div>

                        <a href="/kabar" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i>Semua Kabar
                        </a>
                    </div>

                    <!-- Author Bio Card -->
                    <div class="p-4 rounded-4 mt-4 border d-flex gap-3 align-items-center overflow-hidden" style="background: #fafaf9;">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:54px; height:54px; font-size:1.3rem;">
                            <?= strtoupper(substr($post['author_name'] ?? 'Admin', 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                            <div class="fw-bold text-dark"><?= htmlspecialchars($post['author_name'] ?? 'Tim Plant For Fun') ?></div>
                            <div class="small text-muted mb-1">Jurnalis &amp; Tim Riset Agroforestry Plant For Fun</div>
                            <p class="small text-secondary mb-0">
                                Berdedikasi mencatat denyut nadi konservasi di lereng Gunung Kerenceng serta mempromosikan kopi naungan berkualitas tinggi bersama para petani lokal.
                            </p>
                        </div>
                    </div>
                </article>

                <!-- Navigasi Artikel Sebelumnya & Berikutnya -->
                <div class="row g-3 mb-5">
                    <div class="col-sm-6">
                        <?php if (!empty($prevPost)): ?>
                            <a href="/kabar/<?= htmlspecialchars($prevPost['slug']) ?>" class="card border-0 shadow-sm rounded-4 p-3 h-100 text-decoration-none bg-white transition-card d-block">
                                <span class="small text-muted font-mono text-uppercase"><i class="bi bi-arrow-left me-1"></i>Artikel Sebelumnya</span>
                                <div class="fw-bold text-dark small text-truncate mt-1"><?= htmlspecialchars($prevPost['title']) ?></div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if (!empty($nextPost)): ?>
                            <a href="/kabar/<?= htmlspecialchars($nextPost['slug']) ?>" class="card border-0 shadow-sm rounded-4 p-3 h-100 text-decoration-none bg-white transition-card d-block text-sm-end">
                                <span class="small text-muted font-mono text-uppercase">Artikel Berikutnya <i class="bi bi-arrow-right ms-1"></i></span>
                                <div class="fw-bold text-dark small text-truncate mt-1"><?= htmlspecialchars($nextPost['title']) ?></div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bagian Komentar Pembaca (#komentar) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white" id="komentar">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <h4 class="fw-bold mb-0 text-dark" style="font-family: 'Bitter', serif;">
                            <i class="bi bi-chat-left-text text-success me-2"></i>Komentar &amp; Tanggapan
                        </h4>
                        <span class="badge bg-success-subtle px-3 py-2 rounded-pill font-mono" style="color:#0a3622;">
                            <?= count($comments) ?> Komentar
                        </span>
                    </div>

                    <!-- Flash Pesan -->
                    <?php if (Session::has('success')): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div><?= Session::flash('success') ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (Session::has('error')): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div><?= Session::flash('error') ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Form Kirim Komentar -->
                    <form action="/kabar/<?= htmlspecialchars($post['slug']) ?>/komentar" method="POST" class="mb-5 p-4 rounded-4 bg-light border">
                        <?= csrf_field() ?>
                        <h6 class="fw-bold mb-3 text-dark">Tulis Tanggapan Anda:</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Lengkap *</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Alamat Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required maxlength="150">
                                <div class="form-text small">Email tidak akan dipublikasikan.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Komentar / Pertanyaan *</label>
                            <textarea name="comment" class="form-control" rows="3" required placeholder="Bagikan opini atau pertanyaan Anda mengenai artikel ini..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-bold">
                            <i class="bi bi-send-fill me-1"></i>Kirim Komentar
                        </button>
                    </form>

                    <!-- Daftar Komentar -->
                    <?php if (empty($comments)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-chat-heart display-6 d-block mb-2 text-muted opacity-50"></i>
                            <div class="fw-semibold">Belum ada komentar untuk artikel ini.</div>
                            <small>Jadilah orang pertama yang membagikan tanggapan!</small>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($comments as $com): ?>
                                <div class="p-3 rounded-4 border bg-light d-flex gap-3 overflow-hidden">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:40px; height:40px; font-size:0.95rem;">
                                        <?= strtoupper(substr($com['name'], 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1 min-w-0" style="min-width: 0; word-break: break-word;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold text-dark small"><?= htmlspecialchars($com['name']) ?></span>
                                            <span class="text-muted small" style="font-size:0.75rem;"><?= time_ago($com['created_at']) ?></span>
                                        </div>
                                        <p class="small text-secondary mb-0" style="white-space:pre-line; line-height:1.6;">
                                            <?= htmlspecialchars($com['comment']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Kanan (4 Kolom) -->
            <div class="col-lg-4">
                <!-- Box Donasi Pohon -->
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white mb-4 text-center overflow-hidden position-relative"
                     style="background: linear-gradient(135deg, #061510 0%, #12382c 100%);">
                    <div style="position:absolute; top:-30px; right:-30px; width:140px; height:140px; background:radial-gradient(circle, rgba(34,197,94,0.2) 0%, transparent 70%);"></div>
                    <div class="rounded-circle bg-white bg-opacity-10 text-white mx-auto d-flex align-items-center justify-content-center mb-3" style="width:64px; height:64px;">
                        <i class="bi bi-tree-fill fs-2 text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Dukung Pohon Naungan Kopi</h5>
                    <p class="small text-white-50 mb-3" style="line-height: 1.6;">
                        Tanam satu bibit pohon pelindung di lereng Gunung Kerenceng atas nama Anda atau orang terkasih mulai dari Rp 35.000.
                    </p>
                    <a href="/donasi" class="btn btn-success fw-bold px-4 py-2 rounded-pill w-100 shadow-sm">
                        <i class="bi bi-heart-fill me-1"></i>Donasi Pohon Sekarang
                    </a>
                </div>

                <!-- Box Rekomendasi Kopi Konservasi -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cup-hot-fill text-warning me-2"></i>Kopi Pilihan Kurasi</h6>
                        <span class="badge bg-light text-muted border small font-mono">andu</span>
                    </div>
                    <div class="text-center mb-3">
                        <img src="/assets/images/coffee-anaerobic.jpg" alt="Kopi Kerenceng" class="img-fluid rounded-3 mb-2" style="max-height: 140px; width:100%; object-fit:cover;">
                        <div class="fw-bold text-dark small">Kerenceng Anaerobic Slow Dry 200g</div>
                        <div class="text-success fw-bold font-mono">Rp 95.000</div>
                    </div>
                    <a href="/toko" class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-semibold">
                        Lihat Toko Kopi <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <!-- Artikel Terkait Lainnya -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="bi bi-newspaper text-success me-2"></i>Artikel Terkait Lainnya
                    </h6>
                    <?php if (empty($relatedPosts)): ?>
                        <p class="text-muted small mb-0">Belum ada artikel rekomendasi.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($relatedPosts as $rec): ?>
                                <a href="/kabar/<?= htmlspecialchars($rec['slug']) ?>"
                                   class="list-group-item list-group-item-action px-0 py-3 border-bottom d-flex gap-3 align-items-center overflow-hidden">
                                    <?php if (!empty($rec['image'])): ?>
                                        <img src="<?= htmlspecialchars($rec['image']) ?>" alt="Thumbnail"
                                             class="rounded-3 object-fit-cover flex-shrink-0" style="width:68px;height:68px;">
                                    <?php else: ?>
                                        <div class="rounded-3 bg-light text-muted d-flex align-items-center justify-content-center flex-shrink-0" style="width:68px;height:68px;">
                                            <i class="bi bi-tree text-success fs-3"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                        <div class="badge bg-light text-success border small mb-1" style="font-size:0.65rem;">
                                            <?= htmlspecialchars($rec['category'] ?? 'Umum') ?>
                                        </div>
                                        <div class="fw-bold text-dark line-clamp-2 small mb-1" style="line-height:1.35; word-break: break-word;"><?= htmlspecialchars($rec['title']) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock me-1"></i><?= date('d M Y', strtotime($rec['published_at'])) ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyArticleLink(btn) {
    const url = window.location.href.split('#')[0];
    navigator.clipboard.writeText(url).then(() => {
        const textSpan = document.getElementById('btnCopyText');
        textSpan.textContent = 'Tautan Tersalin! ✓';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        setTimeout(() => {
            textSpan.textContent = 'Salin Tautan';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 3000);
    });
}
</script>

<style>
.transition-card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.transition-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06) !important;
}
.hover-success:hover {
    color: #22c55e !important;
}
</style>
