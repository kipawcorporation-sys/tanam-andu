<?php
/**
 * View: public/news.php — Plant For Fun
 * Kerenceng Coffee Planting & Agroforestry Expedition
 * Halaman daftar kabar & berita konservasi pohon & kopi publik.
 */
?>

<!-- Hero Banner Editorial -->
<section class="position-relative overflow-hidden text-white" style="background: linear-gradient(135deg, #061510 0%, #0d2b20 50%, #12382c 100%); padding: 70px 0 60px;">
    <!-- Ambient Glow Blobs -->
    <div style="position:absolute; top:-100px; right:-100px; width:450px; height:450px; background:radial-gradient(circle, rgba(191,210,84,0.12) 0%, transparent 70%); pointer-events:none;"></div>
    <div style="position:absolute; bottom:-120px; left:-80px; width:380px; height:380px; background:radial-gradient(circle, rgba(34,197,94,0.1) 0%, transparent 70%); pointer-events:none;"></div>

    <div class="container position-relative">
        <!-- Breadcrumb & Tagline -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge px-3 py-2 rounded-pill font-mono" style="background: rgba(191,210,84,0.15); color: #bfd254; border: 1px solid rgba(191,210,84,0.3); font-size: 0.76rem; letter-spacing: 0.05em;">
                <i class="bi bi-journal-richtext me-1"></i>PLANT FOR FUN JOURNAL &bull; KABAR &amp; RISET
            </span>
        </div>

        <div class="row align-items-end g-4">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3" style="font-family: 'Bitter', serif; line-height: 1.2;">
                    Kabar Lapangan, Cerita Kopi, &amp; Jejak Konservasi
                </h1>
                <p class="lead text-white-50 mb-0" style="max-width: 680px; font-size: 1.05rem;">
                    Dokumentasi autentik ekspedisi penanaman pohon lereng Gunung Kerenceng, riset agroforestry, kurasi biji kopi andu, dan dampak nyata aksi hijau bersama.
                </p>
            </div>
            <div class="col-lg-4">
                <!-- Search Box di Hero -->
                <form action="/kabar" method="GET" class="position-relative">
                    <div class="input-group input-group-lg shadow-sm" style="border-radius: 14px; overflow: hidden; border: 1px solid rgba(255,255,255,0.2);">
                        <input type="text" name="q" class="form-control border-0 px-4" 
                               placeholder="Cari kabar, kopi, ekspedisi..." 
                               value="<?= htmlspecialchars($searchKeyword ?? '') ?>"
                               style="background: rgba(255,255,255,0.95); font-size: 0.95rem;">
                        <button class="btn btn-success px-4" type="submit" style="background: #22c55e; border: none;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <?php if (!empty($selectedCat)): ?>
                        <input type="hidden" name="kategori" value="<?= htmlspecialchars($selectedCat) ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Konten Utama Kabar -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">

        <!-- Kategori Filter Tabs -->
        <div class="d-flex align-items-center gap-2 overflow-x-auto pb-3 mb-4" style="scrollbar-width: thin;">
            <a href="/kabar<?= !empty($searchKeyword) ? '?q=' . urlencode($searchKeyword) : '' ?>" 
               class="btn btn-sm px-4 py-2 rounded-pill fw-semibold text-nowrap transition-all <?= empty($selectedCat) ? 'btn-success shadow-sm' : 'btn-outline-secondary bg-white' ?>">
                <i class="bi bi-grid-fill me-1"></i>Semua Kabar (<?= $total ?>)
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="/kabar?kategori=<?= urlencode($cat['category']) ?><?= !empty($searchKeyword) ? '&q=' . urlencode($searchKeyword) : '' ?>" 
                   class="btn btn-sm px-4 py-2 rounded-pill fw-semibold text-nowrap transition-all <?= ($selectedCat ?? '') === $cat['category'] ? 'btn-success shadow-sm' : 'btn-outline-secondary bg-white' ?>">
                    <?= htmlspecialchars($cat['category']) ?>
                    <span class="badge ms-1 rounded-pill <?= ($selectedCat ?? '') === $cat['category'] ? 'bg-white text-success' : 'bg-light text-muted' ?>">
                        <?= $cat['total_posts'] ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($searchKeyword) || !empty($selectedCat)): ?>
            <div class="alert alert-light border shadow-sm rounded-4 d-flex align-items-center justify-content-between p-3 mb-4">
                <div class="small">
                    Menampilkan hasil untuk: 
                    <?php if (!empty($selectedCat)): ?>
                        <span class="badge bg-success text-white me-2">Kategori: <?= htmlspecialchars($selectedCat) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($searchKeyword)): ?>
                        <span class="badge bg-primary text-white">Kata Kunci: "<?= htmlspecialchars($searchKeyword) ?>"</span>
                    <?php endif; ?>
                    <span class="text-muted ms-2">(<?= $total ?> artikel)</span>
                </div>
                <a href="/kabar" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="bi bi-x-circle me-1"></i>Reset Filter
                </a>
            </div>
        <?php endif; ?>

        <!-- Featured Headline Banner (Hanya di Halaman 1 tanpa filter) -->
        <?php if (!empty($featuredPost) && empty($searchKeyword) && empty($selectedCat) && $page === 1): ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 transition-card bg-white">
                <div class="row g-0">
                    <div class="col-lg-7 position-relative">
                        <a href="/kabar/<?= htmlspecialchars($featuredPost['slug']) ?>">
                            <?php if (!empty($featuredPost['image'])): ?>
                                <img src="<?= htmlspecialchars($featuredPost['image']) ?>" 
                                     alt="<?= htmlspecialchars($featuredPost['title']) ?>"
                                     class="img-fluid w-100 h-100 object-fit-cover"
                                     style="min-height: 380px; max-height: 480px;">
                            <?php else: ?>
                                <div class="w-100 h-100 bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="min-height: 380px;">
                                    <i class="bi bi-tree text-success display-1"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="position-absolute top-0 start-0 p-3 d-flex gap-2">
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold shadow-sm font-mono" style="font-size: 0.74rem;">
                                <i class="bi bi-star-fill text-dark me-1"></i>BERITA UTAMA
                            </span>
                            <span class="badge bg-dark bg-opacity-75 text-white px-3 py-2 rounded-pill fw-bold shadow-sm font-mono" style="font-size: 0.74rem;">
                                <?= htmlspecialchars($featuredPost['category'] ?? 'Ekspedisi') ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-5 p-4 p-md-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                                <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($featuredPost['published_at'] ?? $featuredPost['created_at'])) ?></span>
                                <span>&bull;</span>
                                <span><i class="bi bi-clock me-1"></i><?= $featuredPost['read_time_minutes'] ?? 4 ?> menit baca</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-eye me-1"></i><?= number_format($featuredPost['views_count'] ?? 0) ?> views</span>
                            </div>

                            <h2 class="fw-bold mb-3" style="font-family: 'Bitter', serif; line-height: 1.3;">
                                <a href="/kabar/<?= htmlspecialchars($featuredPost['slug']) ?>" class="text-decoration-none text-dark hover-success">
                                    <?= htmlspecialchars($featuredPost['title']) ?>
                                </a>
                            </h2>

                            <p class="text-secondary small mb-4" style="line-height: 1.7;">
                                <?= htmlspecialchars($featuredPost['excerpt'] ?? mb_substr(strip_tags($featuredPost['content']), 0, 180) . '...') ?>
                            </p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width:36px; height:36px; font-size:0.85rem;">
                                    <?= strtoupper(substr($featuredPost['author_name'] ?? 'Admin', 0, 1)) ?>
                                </div>
                                <div class="small">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($featuredPost['author_name'] ?? 'Tim Ekspedisi') ?></div>
                                    <div class="text-muted" style="font-size:0.72rem;">Penulis &amp; Tim Riset</div>
                                </div>
                            </div>
                            <a href="/kabar/<?= htmlspecialchars($featuredPost['slug']) ?>" class="btn btn-success btn-sm px-4 rounded-pill fw-semibold">
                                Baca Liputan <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Grid Artikel & Sidebar Populer -->
        <div class="row g-4">
            <!-- Kolom Daftar Artikel (8 Kolom) -->
            <div class="col-lg-8">
                <?php if (empty($newsList)): ?>
                    <div class="card border-0 shadow-sm py-5 text-center rounded-4 bg-white">
                        <div class="card-body">
                            <i class="bi bi-search text-muted" style="font-size: 3.5rem;"></i>
                            <h5 class="fw-bold mt-3 text-dark">Tidak Ada Kabar Ditemukan</h5>
                            <p class="text-muted small">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                            <a href="/kabar" class="btn btn-success btn-sm px-4 rounded-pill">Lihat Semua Kabar</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($newsList as $item): ?>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden transition-card bg-white d-flex flex-column">
                                    <div class="position-relative">
                                        <a href="/kabar/<?= htmlspecialchars($item['slug']) ?>">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                                     alt="<?= htmlspecialchars($item['title']) ?>"
                                                     class="card-img-top object-fit-cover"
                                                     style="height: 220px;">
                                            <?php else: ?>
                                                <div class="bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 220px;">
                                                    <i class="bi bi-tree fs-1" style="color:#0a3622"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                        <div class="position-absolute top-0 start-0 p-3">
                                            <span class="badge bg-dark bg-opacity-80 text-white px-3 py-1 rounded-pill small font-mono">
                                                <?= htmlspecialchars($item['category'] ?? 'Kabar Lapangan') ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-body d-flex flex-column p-4 flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                                            <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?></span>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-clock me-1"></i><?= $item['read_time_minutes'] ?? 3 ?> mnt</span>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-eye me-1"></i><?= number_format($item['views_count'] ?? 0) ?></span>
                                        </div>

                                        <h5 class="card-title fw-bold text-dark mb-2 line-clamp-2" style="font-family: 'Bitter', serif; font-size: 1.15rem; line-height: 1.35;">
                                            <a href="/kabar/<?= htmlspecialchars($item['slug']) ?>" class="text-decoration-none text-dark hover-success">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </a>
                                        </h5>

                                        <p class="card-text text-secondary small mb-3 flex-grow-1 line-clamp-3" style="line-height: 1.6;">
                                            <?= htmlspecialchars($item['excerpt'] ?? mb_substr(strip_tags($item['content']), 0, 130) . '...') ?>
                                        </p>

                                        <?php if (!empty($item['tags'])): ?>
                                            <div class="d-flex flex-wrap gap-1 mb-3">
                                                <?php 
                                                $tArr = explode(',', $item['tags']);
                                                foreach (array_slice($tArr, 0, 3) as $t): 
                                                ?>
                                                    <span class="badge bg-light text-muted border small font-mono" style="font-size: 0.65rem;">
                                                        #<?= htmlspecialchars(trim($t)) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
                                            <span class="small text-muted">
                                                <i class="bi bi-chat-left-text me-1"></i><?= (int)($item['comments_count'] ?? 0) ?> komentar
                                            </span>
                                            <a href="/kabar/<?= htmlspecialchars($item['slug']) ?>" class="btn btn-outline-success btn-sm px-3 rounded-pill fw-semibold">
                                                Baca <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="d-flex justify-content-center mt-5">
                            <nav>
                                <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="/kabar?page=<?= $i ?><?= !empty($selectedCat) ? '&kategori=' . urlencode($selectedCat) : '' ?><?= !empty($searchKeyword) ? '&q=' . urlencode($searchKeyword) : '' ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Kolom Sidebar (4 Kolom) -->
            <div class="col-lg-4">
                <!-- Box Paling Banyak Dibaca (Trending) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-fire text-danger me-2"></i>Paling Banyak Dibaca</h6>
                        <span class="badge bg-danger-subtle text-danger rounded-pill small">Trending</span>
                    </div>

                    <?php if (empty($trendingPosts)): ?>
                        <p class="text-muted small mb-0">Belum ada statistik pembaca.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php $rank = 1; foreach ($trendingPosts as $tr): ?>
                                <a href="/kabar/<?= htmlspecialchars($tr['slug']) ?>" 
                                   class="list-group-item list-group-item-action px-0 py-3 border-bottom d-flex gap-3 align-items-start">
                                    <span class="fs-4 fw-bold font-mono text-muted opacity-50" style="line-height:1; min-width: 24px;">
                                        <?= sprintf('%02d', $rank++) ?>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="badge bg-light text-success border small mb-1" style="font-size:0.65rem;">
                                            <?= htmlspecialchars($tr['category'] ?? 'Umum') ?>
                                        </div>
                                        <div class="fw-bold text-dark text-truncate-2 small mb-1" style="line-height: 1.35;">
                                            <?= htmlspecialchars($tr['title']) ?>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="bi bi-eye me-1"></i><?= number_format($tr['views_count']) ?> views &bull; <?= $tr['read_time_minutes'] ?? 3 ?> mnt
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Banner Kolaborasi andu & Karsa Kerentjeng -->
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white mb-4 overflow-hidden position-relative"
                     style="background: linear-gradient(135deg, #12382c 0%, #1e5241 100%);">
                    <div style="position:absolute; top:-20px; right:-20px; width:120px; height:120px; background:radial-gradient(circle, rgba(191,210,84,0.15) 0%, transparent 70%);"></div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="/assets/images/logo-karsa-kerentjeng-white.png" alt="Karsa Kerentjeng" style="height:26px; object-fit:contain;">
                        <span class="text-white-50">&times;</span>
                        <img src="/assets/images/logo-andu-white.png" alt="andu" style="height:22px; object-fit:contain;">
                    </div>
                    <h5 class="fw-bold mb-2">Kopi Konservasi Kerenceng</h5>
                    <p class="small text-white-50 mb-3" style="line-height: 1.6;">
                        Tiap tegukan kopi specialty yang Anda nikmati turut mendanai penanaman bibit pohon naungan di lereng Gunung Kerenceng.
                    </p>
                    <a href="/toko" class="btn btn-warning text-dark fw-bold px-4 rounded-pill btn-sm">
                        <i class="bi bi-cup-hot-fill me-1"></i>Beli Kopi Konservasi
                    </a>
                </div>

                <!-- Widget Ikuti Ekspedisi Penanaman -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                    <div class="rounded-circle bg-success-subtle mx-auto d-flex align-items-center justify-content-center mb-3" style="color:#0a3622; width:60px; height:60px;">
                        <i class="bi bi-compass fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Ingin Terjun ke Lapangan?</h5>
                    <p class="text-muted small mb-3">
                        Daftar sebagai relawan atau donatur pohon dan rasakan langsung pengalaman mendaki serta menanam bibit bersama kami.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="/kegiatan" class="btn btn-success rounded-pill fw-semibold btn-sm py-2">
                            <i class="bi bi-calendar-event me-1"></i>Jadwal Ekspedisi Terdekat
                        </a>
                        <a href="/donasi" class="btn btn-outline-success rounded-pill fw-semibold btn-sm py-2">
                            <i class="bi bi-heart-fill me-1"></i>Donasi Pohon Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.transition-card {
    transition: transform .25s ease, box-shadow .25s ease;
}
.transition-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.08) !important;
}
.hover-success:hover {
    color: #22c55e !important;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.transition-all {
    transition: all 0.2s ease;
}
</style>
