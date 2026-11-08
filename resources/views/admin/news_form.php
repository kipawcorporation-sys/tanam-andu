<?php
/**
 * View: admin/news_form.php — TanamPohon.id / Plant For Fun
 * Form Tambah / Edit Postingan & Berita Profesional.
 */
$isEdit = $isEdit ?? false;
$post   = $post ?? [];
$actionUrl = $isEdit ? "/admin/postingan/{$post['id']}/update" : "/admin/postingan/simpan";
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 24px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:220px; height:220px; background:radial-gradient(circle,rgba(34,197,94,.12) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.35rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }

.editor-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
.editor-card-header { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); padding:16px 20px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.editor-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.9rem; }
.editor-card-body { padding:24px; }

.editor-toolbar { display:flex; gap:6px; flex-wrap:wrap; background:#f8fafc; padding:10px 14px; border:1px solid #e2e8f0; border-radius:10px 10px 0 0; border-bottom:none; }
.tool-btn { background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:5px 10px; font-size:.78rem; font-weight:600; color:#334155; cursor:pointer; transition:.15s; }
.tool-btn:hover { background:#f1f5f9; color:#0f172a; border-color:#94a3b8; }

.content-textarea { border-radius:0 0 10px 10px !important; font-family:'Plus Jakarta Sans',sans-serif; font-size:.92rem; line-height:1.6; padding:16px; resize:vertical; }

.live-stats { font-size:.78rem; color:#64748b; display:flex; gap:16px; margin-top:8px; font-weight:500; }
.live-stats span { display:flex; align-items:center; gap:4px; }

.image-preview-container { border:2px dashed #cbd5e1; border-radius:12px; padding:14px; text-align:center; background:#f8fafc; position:relative; min-height:160px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.image-preview-container img { max-height:200px; width:100%; object-fit:cover; border-radius:8px; }
</style>

<!-- Page Hero -->
<div class="page-hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-pencil-square me-2"></i><?= $isEdit ? 'Edit Postingan Artikel' : 'Tulis Postingan Berita Baru' ?></h4>
            <p>Bagikan kabar ekspedisi, riset agroforestry, dan pembaruan kegiatan lapangan kepada publik.</p>
        </div>
        <a href="/admin/postingan" class="btn btn-outline-light btn-sm px-3" style="border-radius:8px;">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>
</div>

<form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <!-- Kolom Konten Utama -->
        <div class="col-lg-8">
            <div class="editor-card mb-4">
                <div class="editor-card-header">
                    <h6><i class="bi bi-file-text me-2 text-success"></i>Konten & Narasi Artikel</h6>
                    <span class="text-muted small">Editor Format Markdown & HTML</span>
                </div>
                <div class="editor-card-body">
                    <!-- Judul -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Judul Postingan / Berita <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="postTitle" class="form-control form-control-lg fw-bold"
                               placeholder="Contoh: Ekspedisi Penanaman Kopi Kerenceng #001: Membangun Ekosistem Agroforestry..."
                               value="<?= htmlspecialchars($post['title'] ?? '') ?>" required maxlength="255">
                        <div class="d-flex justify-content-between mt-1 text-muted small">
                            <span>Buat judul yang menarik dan mencerminkan esensi artikel.</span>
                            <span id="titleCharCount">0 / 255</span>
                        </div>
                    </div>

                    <!-- Kategori & Tags -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori Artikel <span class="text-danger">*</span></label>
                            <select name="category" id="catSelect" class="form-select">
                                <?php 
                                $presetCats = ['Ekspedisi Kerenceng', 'Kopi & Agroforestry', 'Kabar Lapangan', 'Panduan & Relawan', 'Riset & Dampak', 'Edukasi Lingkungan'];
                                $allCats = array_unique(array_merge($presetCats, $categories ?? []));
                                $currentCat = $post['category'] ?? 'Kabar Lapangan';
                                foreach ($allCats as $cat): 
                                ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= $currentCat === $cat ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="custom">+ Kategori Lainnya (Ketik Baru)...</option>
                            </select>
                            <input type="text" name="custom_category" id="customCatInput" class="form-control mt-2" 
                                   placeholder="Ketik nama kategori baru..." style="display:none;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Label / Tags</label>
                            <input type="text" name="tags" class="form-control"
                                   placeholder="Contoh: Kerenceng, andu, Agroforestry, Robusta"
                                   value="<?= htmlspecialchars($post['tags'] ?? '') ?>">
                            <div class="form-text small">Pisahkan dengan koma (opsional).</div>
                        </div>
                    </div>

                    <!-- Ringkasan Singkat (Excerpt) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Ringkasan Singkat (Excerpt / Cuplikan)</label>
                        <textarea name="excerpt" id="postExcerpt" class="form-control" rows="2"
                                  placeholder="Tulis 1-2 kalimat ringkasan yang akan tampil pada kartu preview beranda dan meta deskripsi mesin pencari..."><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
                        <div class="form-text small">Jika dikosongkan, sistem akan otomatis mengambil kalimat pembuka isi artikel.</div>
                    </div>

                    <!-- Toolbar Editor Cepat -->
                    <div class="mb-0">
                        <label class="form-label fw-bold">Isi Lengkap Artikel <span class="text-danger">*</span></label>
                        
                        <div class="editor-toolbar">
                            <button type="button" class="tool-btn" onclick="insertFormat('**', '**', 'Teks Tebal')"><i class="bi bi-type-bold"></i> Tebal</button>
                            <button type="button" class="tool-btn" onclick="insertFormat('*', '*', 'Teks Miring')"><i class="bi bi-type-italic"></i> Miring</button>
                            <button type="button" class="tool-btn" onclick="insertFormat('\n### ', '\n', 'Sub-Judul Bagian')"><i class="bi bi-type-h2"></i> Sub-Judul (H3)</button>
                            <button type="button" class="tool-btn" onclick="insertFormat('\n> ', '\n', 'Kutipan inspiratif atau pesan penting')"><i class="bi bi-quote"></i> Kutipan</button>
                            <button type="button" class="tool-btn" onclick="insertFormat('\n- ', '\n', 'Poin daftar')"><i class="bi bi-list-ul"></i> Poin</button>
                            <button type="button" class="tool-btn" onclick="insertFormat('\n1. ', '\n', 'Daftar urutan langkah')"><i class="bi bi-list-ol"></i> Nomor</button>
                            <button type="button" class="tool-btn" onclick="insertFormat('[', '](https://...)', 'Teks Tautan')"><i class="bi bi-link-45deg"></i> Link</button>
                            <button type="button" class="tool-btn text-success" onclick="insertCallout()"><i class="bi bi-info-circle"></i> Kotak Info</button>
                        </div>

                        <textarea name="content" id="postContent" class="form-control content-textarea" rows="18" required
                                  placeholder="Mulai ketikkan cerita, data, wawancara petani, atau laporan kegiatan penanaman pohon..."><?= htmlspecialchars($post['content'] ?? '') ?></textarea>

                        <div class="live-stats">
                            <span><i class="bi bi-file-earmark-word"></i> <b id="statWordCount">0</b> Kata</span>
                            <span>•</span>
                            <span><i class="bi bi-clock-history"></i> Estimasi baca: <b id="statReadTime">1</b> Menit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Pengaturan & Foto Sampul -->
        <div class="col-lg-4">
            <!-- Publikasi & Status -->
            <div class="editor-card mb-4">
                <div class="editor-card-header">
                    <h6><i class="bi bi-send-check me-2 text-success"></i>Pengaturan Publikasi</h6>
                </div>
                <div class="editor-card-body">
                    <!-- Status Terbit -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_published" id="switchPublished" role="switch"
                               <?= (!empty($post['is_published']) || !$isEdit) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="switchPublished">
                            Publikasikan Sekarang
                        </label>
                        <div class="text-muted small">Jika dinonaktifkan, artikel hanya tersimpan sebagai draf internal.</div>
                    </div>

                    <!-- Status Featured / Headline Utama -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="switchFeatured" role="switch"
                               <?= !empty($post['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-warning-emphasis" for="switchFeatured">
                            <i class="bi bi-star-fill me-1" style="color: #d97706;"></i>Jadikan Berita Utama (Headline)
                        </label>
                        <div class="text-muted small">Artikel ini akan ditampilkan dengan banner hero besar di halaman kabar.</div>
                    </div>

                    <?php if ($isEdit && !empty($post['published_at'])): ?>
                        <div class="bg-light p-3 rounded-3 small text-muted mb-3 border">
                            <div class="fw-semibold text-dark mb-1"><i class="bi bi-calendar-check me-1 text-success"></i>Waktu Terbit:</div>
                            <div><?= date('d M Y, H:i', strtotime($post['published_at'])) ?> WIB</div>
                            <div class="mt-1"><i class="bi bi-eye me-1"></i><?= number_format($post['views_count'] ?? 0) ?> pembaca sejauh ini</div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success py-2 fw-bold" style="border-radius:10px;">
                            <i class="bi bi-check-circle-fill me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Terbitkan Artikel Sekarang' ?>
                        </button>
                        <a href="/admin/postingan" class="btn btn-light py-2 text-secondary border" style="border-radius:10px;">
                            Batal &amp; Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gambar Sampul (Featured Image) -->
            <div class="editor-card">
                <div class="editor-card-header">
                    <h6><i class="bi bi-image me-2 text-primary"></i>Foto Sampul Utama</h6>
                </div>
                <div class="editor-card-body">
                    <div class="image-preview-container mb-3" id="imagePreviewContainer">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Preview" id="previewImg">
                        <?php else: ?>
                            <img src="" alt="Preview" id="previewImg" style="display:none;">
                            <div id="previewPlaceholder">
                                <i class="bi bi-camera text-muted" style="font-size: 2.5rem;"></i>
                                <div class="text-muted small fw-semibold mt-2">Belum ada foto sampul dipilih</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">
                            <?= !empty($post['image']) ? 'Ganti Foto Sampul:' : 'Unggah Foto Sampul:' ?>
                        </label>
                        <input type="file" name="image" id="imageInput" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text small">Rekomendasi rasio 16:9 (resolusi min. 1200x675px, JPG/PNG/WebP maks 2MB).</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Karakter counter judul
const titleInput = document.getElementById('postTitle');
const titleCounter = document.getElementById('titleCharCount');
function updateTitleCount() {
    titleCounter.textContent = titleInput.value.length + ' / 255';
}
titleInput.addEventListener('input', updateTitleCount);
updateTitleCount();

// Word & Reading time counter
const contentArea = document.getElementById('postContent');
const statWordCount = document.getElementById('statWordCount');
const statReadTime = document.getElementById('statReadTime');
function updateStats() {
    const text = contentArea.value.trim();
    const words = text ? text.split(/\s+/).length : 0;
    statWordCount.textContent = words;
    statReadTime.textContent = Math.max(1, Math.ceil(words / 200));
}
contentArea.addEventListener('input', updateStats);
updateStats();

// Kategori custom toggle
const catSelect = document.getElementById('catSelect');
const customCatInput = document.getElementById('customCatInput');
catSelect.addEventListener('change', function() {
    if (this.value === 'custom') {
        customCatInput.style.display = 'block';
        customCatInput.focus();
    } else {
        customCatInput.style.display = 'none';
        customCatInput.value = '';
    }
});

// Image Live Preview
const imageInput = document.getElementById('imageInput');
const previewImg = document.getElementById('previewImg');
const previewPlaceholder = document.getElementById('previewPlaceholder');

imageInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            if (previewPlaceholder) previewPlaceholder.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
});

// Insert formatting helpers
function insertFormat(startTag, endTag, defaultText) {
    const textarea = contentArea;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end) || defaultText;
    const replacement = startTag + selectedText + endTag;
    
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + startTag.length, start + startTag.length + selectedText.length);
    updateStats();
}

function insertCallout() {
    insertFormat('\n> 💡 **Catatan Lapangan:**\n> ', '\n\n', 'Informasi penting atau fakta menarik dari lapangan...');
}
</script>
