<?php
/**
 * View: dashboard/profile.php — TanamPohon.id
 * Pengaturan Profil: Tab Info Diri | Keamanan | Alamat Pengiriman
 */
$user      = $user ?? [];
$addresses = $addresses ?? [];
$stats     = $stats ?? [];

// Tentukan tab aktif berdasarkan flash/hash (default: info)
$activeTab = Session::getFlash('active_tab') ?? 'info';
if (!empty($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'tab-keamanan')) {
    $activeTab = 'keamanan';
}

$avatarSrc = !empty($user['avatar'])
    ? htmlspecialchars($user['avatar'])
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'U') . '&background=2d6a4f&color=fff&size=128';

$tshirtSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-person-gear me-2 text-success"></i>Pengaturan Profil</h4>
        <p class="text-muted small mb-0">Kelola informasi akun dan preferensi Anda</p>
    </div>
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
<?php if ($pwErr = Session::getFlash('password_error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-lock me-2"></i><?= htmlspecialchars($pwErr) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- SIDEBAR AVATAR -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-4">
                <!-- Avatar Upload -->
                <div class="position-relative d-inline-block mb-3">
                    <img id="avatarPreview"
                         src="<?= $avatarSrc ?>"
                         alt="Foto Profil"
                         class="rounded-circle border border-3 border-success shadow-sm"
                         style="width:110px;height:110px;object-fit:cover;">
                    <label for="avatarInput"
                           class="position-absolute bottom-0 end-0 btn btn-sm btn-success rounded-circle p-1"
                           style="width:30px;height:30px;cursor:pointer;"
                           title="Ganti foto profil">
                        <i class="bi bi-camera-fill" style="font-size:.75rem;"></i>
                    </label>
                </div>

                <form action="/dashboard/profil/avatar" method="POST" enctype="multipart/form-data" id="avatarForm">
                    <?= csrf_field() ?>
                    <input type="file" id="avatarInput" name="avatar" class="d-none"
                           accept="image/jpeg,image/png,image/webp">
                </form>

                <h6 class="fw-bold mb-1"><?= htmlspecialchars($user['name'] ?? '') ?></h6>
                <p class="text-muted small mb-0"><?= htmlspecialchars($user['email'] ?? '') ?></p>

                <hr class="my-3">

                <!-- Stats ringkas -->
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="bg-light rounded p-2">
                            <div class="fw-bold text-success"><?= number_format($stats['total_trees'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.7rem;">Pohon</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded p-2">
                            <div class="fw-bold text-primary"><?= number_format($stats['total_donations'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.7rem;">Donasi</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded p-2 border">
                            <div class="fw-bold" style="color: #b45309;"><?= number_format($user['points'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.7rem;">Poin</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded p-2 border">
                            <div class="fw-bold" style="color: #0369a1;"><?= number_format($stats['total_events'] ?? 0) ?></div>
                            <div class="text-muted" style="font-size:.7rem;">Kegiatan</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <span class="badge bg-success-subtle px-3 py-2 rounded-pill" style="color:#0a3622;">
                        <i class="bi bi-patch-check-fill me-1"></i>
                        Kode: <?= htmlspecialchars($user['referral_code'] ?? '-') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS UTAMA -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'info' ? 'active' : '' ?>"
                                id="tab-info-btn" data-bs-toggle="tab" data-bs-target="#tab-info"
                                type="button" role="tab">
                            <i class="bi bi-person me-1"></i>Info Diri
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'keamanan' ? 'active' : '' ?>"
                                id="tab-keamanan-btn" data-bs-toggle="tab" data-bs-target="#tab-keamanan"
                                type="button" role="tab">
                            <i class="bi bi-shield-lock me-1"></i>Keamanan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $activeTab === 'alamat' ? 'active' : '' ?>"
                                id="tab-alamat-btn" data-bs-toggle="tab" data-bs-target="#tab-alamat"
                                type="button" role="tab">
                            <i class="bi bi-geo-alt me-1"></i>Alamat
                            <?php if (!empty($addresses)): ?>
                                <span class="badge bg-success rounded-pill ms-1" style="font-size:.65rem;">
                                    <?= count($addresses) ?>
                                </span>
                            <?php endif; ?>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="profileTabsContent">

                    <!-- ===== TAB INFO DIRI ===== -->
                    <div class="tab-pane fade <?= $activeTab === 'info' ? 'show active' : '' ?>" id="tab-info" role="tabpanel">
                        <h6 class="fw-bold mb-4 text-muted">INFORMASI PRIBADI</h6>
                        <form action="/dashboard/profil/update" method="POST" id="profileForm">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                           value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                           placeholder="Nama lengkap Anda" required minlength="2" maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control bg-light"
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                           disabled title="Email tidak dapat diubah">
                                    <div class="form-text">Email tidak dapat diubah.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="tel" name="phone" class="form-control"
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                           placeholder="08xx-xxxx-xxxx" maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kota / Kabupaten</label>
                                    <input type="text" name="city" class="form-control"
                                           value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                                           placeholder="Misal: Bandung, Surabaya" maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ukuran Kaos Default</label>
                                    <select name="default_tshirt_size" class="form-select">
                                        <option value="">— Pilih Ukuran —</option>
                                        <?php foreach ($tshirtSizes as $size): ?>
                                            <option value="<?= $size ?>"
                                                <?= ($user['default_tshirt_size'] ?? '') === $size ? 'selected' : '' ?>>
                                                <?= $size ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_anonymous_default" id="isAnonymous"
                                               <?= !empty($user['is_anonymous_default']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isAnonymous">
                                            <i class="bi bi-incognito me-1"></i>
                                            Sembunyikan nama saya di papan peringkat & halaman transparansi
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ===== TAB KEAMANAN ===== -->
                    <div class="tab-pane fade <?= $activeTab === 'keamanan' ? 'show active' : '' ?>" id="tab-keamanan" role="tabpanel">
                        <h6 class="fw-bold mb-4 text-muted">UBAH PASSWORD</h6>
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
                                    <i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>
                                    <div>
                                        <strong>Penting:</strong> Setelah mengubah password, Anda akan keluar secara otomatis
                                        dan diminta masuk kembali dengan password baru.
                                    </div>
                                </div>
                                <form action="/dashboard/profil/password" method="POST" id="passwordForm">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Password Lama <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="old_password" id="oldPass"
                                                   class="form-control" placeholder="Masukkan password saat ini" required>
                                            <button class="btn btn-outline-secondary toggle-pass" type="button"
                                                    data-target="oldPass">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="new_password" id="newPass"
                                                   class="form-control" placeholder="Minimal 8 karakter"
                                                   required minlength="8" maxlength="128">
                                            <button class="btn btn-outline-secondary toggle-pass" type="button"
                                                    data-target="newPass">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <!-- Password strength indicator -->
                                        <div class="mt-2">
                                            <div class="progress" style="height:4px;">
                                                <div class="progress-bar" id="strengthBar" style="width:0%"></div>
                                            </div>
                                            <small id="strengthText" class="text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="confirm_password" id="confirmPass"
                                                   class="form-control" placeholder="Ulangi password baru" required>
                                            <button class="btn btn-outline-secondary toggle-pass" type="button"
                                                    data-target="confirmPass">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div id="matchFeedback" class="form-text"></div>
                                    </div>
                                    <button type="submit" class="btn btn-danger px-4">
                                        <i class="bi bi-lock me-1"></i>Ubah Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TAB ALAMAT ===== -->
                    <div class="tab-pane fade <?= $activeTab === 'alamat' ? 'show active' : '' ?>" id="tab-alamat" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0 text-muted">ALAMAT PENGIRIMAN</h6>
                            <?php if (count($addresses) < 5): ?>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahAlamat">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah Alamat
                                </button>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Maks. 5 alamat</span>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($addresses)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-geo-alt text-muted" style="font-size:3rem;"></i>
                                <p class="text-muted mt-3">Belum ada alamat pengiriman yang tersimpan.</p>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahAlamat">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah Alamat Pertama
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($addresses as $addr): ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border <?= $addr['is_primary'] ? 'border-success border-2' : '' ?>">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <span class="badge bg-secondary me-1"><?= htmlspecialchars($addr['label']) ?></span>
                                                        <?php if ($addr['is_primary']): ?>
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-star-fill me-1"></i>Utama
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item btn-edit-addr" href="#"
                                                                   data-id="<?= $addr['id'] ?>"
                                                                   data-label="<?= htmlspecialchars($addr['label']) ?>"
                                                                   data-recipient="<?= htmlspecialchars($addr['recipient_name']) ?>"
                                                                   data-phone="<?= htmlspecialchars($addr['phone']) ?>"
                                                                   data-province="<?= htmlspecialchars($addr['province']) ?>"
                                                                   data-city="<?= htmlspecialchars($addr['city']) ?>"
                                                                   data-district="<?= htmlspecialchars($addr['district']) ?>"
                                                                   data-postal="<?= htmlspecialchars($addr['postal_code']) ?>"
                                                                   data-full="<?= htmlspecialchars($addr['full_address']) ?>">
                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <?php if (!$addr['is_primary']): ?>
                                                                <li>
                                                                    <form action="/dashboard/profil/alamat/<?= $addr['id'] ?>/utama" method="POST" class="d-inline">
                                                                        <?= csrf_field() ?>
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="bi bi-star me-2"></i>Jadikan Utama
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <button class="dropdown-item text-danger btn-hapus-addr"
                                                                            data-id="<?= $addr['id'] ?>"
                                                                            data-label="<?= htmlspecialchars($addr['label']) ?>">
                                                                        <i class="bi bi-trash me-2"></i>Hapus
                                                                    </button>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <p class="fw-semibold mb-1"><?= htmlspecialchars($addr['recipient_name']) ?></p>
                                                <p class="text-muted small mb-1">
                                                    <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($addr['phone']) ?>
                                                </p>
                                                <p class="small mb-0">
                                                    <?= htmlspecialchars($addr['full_address']) ?>,
                                                    <?= htmlspecialchars($addr['district']) ?>,
                                                    <?= htmlspecialchars($addr['city']) ?>,
                                                    <?= htmlspecialchars($addr['province']) ?>
                                                    <?= htmlspecialchars($addr['postal_code']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div><!-- /.tab-content -->
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL: Tambah Alamat ===== -->
<div class="modal fade" id="modalTambahAlamat" tabindex="-1" aria-labelledby="modalTambahAlamatLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/dashboard/profil/alamat/simpan" method="POST" id="formTambahAlamat">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahAlamatLabel">
                        <i class="bi bi-plus-circle me-2 text-success"></i>Tambah Alamat Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php echo renderAddressFields(null); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i>Simpan Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL: Edit Alamat ===== -->
<div class="modal fade" id="modalEditAlamat" tabindex="-1" aria-labelledby="modalEditAlamatLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditAlamat" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditAlamatLabel">
                        <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Alamat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editAlamatBody">
                    <?php echo renderAddressFields('edit'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i>Update Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL: Hapus Alamat ===== -->
<div class="modal fade" id="modalHapusAlamat" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="formHapusAlamat" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-trash me-2"></i>Hapus Alamat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="mb-0">Hapus alamat "<strong id="hapusLabelText"></strong>"?
                    Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Helper function to render address form fields
function renderAddressFields(?string $prefix): string
{
    $p = $prefix ?? '';
    $id = fn($name) => $p ? "edit_{$name}" : $name;
    return '
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Label Alamat <span class="text-danger">*</span></label>
            <select name="label" id="' . $id('label') . '" class="form-select" required>
                <option value="Rumah">Rumah</option>
                <option value="Kantor">Kantor</option>
                <option value="Kos">Kos</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
            <input type="text" name="recipient_name" id="' . $id('recipient_name') . '"
                   class="form-control" placeholder="Nama penerima paket" required maxlength="100">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
            <input type="tel" name="phone" id="' . $id('phone') . '"
                   class="form-control" placeholder="08xx-xxxx-xxxx" required maxlength="20">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Kode Pos <span class="text-danger">*</span></label>
            <input type="text" name="postal_code" id="' . $id('postal_code') . '"
                   class="form-control" placeholder="12345" required maxlength="10" pattern="[0-9]{5}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Provinsi <span class="text-danger">*</span></label>
            <input type="text" name="province" id="' . $id('province') . '"
                   class="form-control" placeholder="Jawa Barat" required maxlength="100">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Kota/Kabupaten <span class="text-danger">*</span></label>
            <input type="text" name="city" id="' . $id('city') . '"
                   class="form-control" placeholder="Bandung" required maxlength="100">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Kecamatan <span class="text-danger">*</span></label>
            <input type="text" name="district" id="' . $id('district') . '"
                   class="form-control" placeholder="Coblong" required maxlength="100">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="full_address" id="' . $id('full_address') . '"
                      class="form-control" rows="3"
                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan..." required></textarea>
        </div>
    </div>';
}
?>

<script>
// Auto-submit avatar form saat file dipilih
document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
        // Submit after short delay to show preview
        setTimeout(() => document.getElementById('avatarForm').submit(), 500);
    }
});

// Toggle password visibility
document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function() {
        const target = document.getElementById(this.dataset.target);
        const icon   = this.querySelector('i');
        if (target.type === 'password') {
            target.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            target.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
});

// Password strength indicator
const newPassInput = document.getElementById('newPass');
if (newPassInput) {
    newPassInput.addEventListener('input', function() {
        const val = this.value;
        const bar  = document.getElementById('strengthBar');
        const text = document.getElementById('strengthText');
        let strength = 0;
        if (val.length >= 8) strength++;
        if (/[A-Z]/.test(val)) strength++;
        if (/[0-9]/.test(val)) strength++;
        if (/[^A-Za-z0-9]/.test(val)) strength++;
        const levels = [
            { label: '', cls: '', pct: 0 },
            { label: 'Lemah', cls: 'bg-danger', pct: 25 },
            { label: 'Sedang', cls: 'bg-warning', pct: 50 },
            { label: 'Kuat', cls: 'bg-info', pct: 75 },
            { label: 'Sangat Kuat', cls: 'bg-success', pct: 100 },
        ];
        const lv = levels[strength] || levels[0];
        bar.className = 'progress-bar ' + lv.cls;
        bar.style.width = lv.pct + '%';
        text.textContent = lv.label ? 'Kekuatan: ' + lv.label : '';
        text.className = 'form-text ' + (strength < 2 ? 'text-danger' : strength < 3 ? 'text-warning' : 'text-success');
    });
}

// Confirm password match feedback
const confirmPassInput = document.getElementById('confirmPass');
if (confirmPassInput) {
    confirmPassInput.addEventListener('input', function() {
        const feedback = document.getElementById('matchFeedback');
        if (this.value === newPassInput.value) {
            feedback.textContent = '✓ Password cocok';
            feedback.className = 'form-text text-success';
        } else {
            feedback.textContent = '✗ Password tidak cocok';
            feedback.className = 'form-text text-danger';
        }
    });
}

// Activate tab from URL hash
const hash = window.location.hash;
if (hash) {
    const tabId = hash.replace('#', '');
    const tabBtn = document.getElementById('tab-' + tabId + '-btn');
    if (tabBtn) {
        new bootstrap.Tab(tabBtn).show();
    }
}

// Edit Alamat — populate modal
document.querySelectorAll('.btn-edit-addr').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.dataset.id;
        // Set form action
        document.getElementById('formEditAlamat').action = '/dashboard/profil/alamat/' + id + '/update';
        // Fill fields
        const fields = {
            'edit_label': this.dataset.label,
            'edit_recipient_name': this.dataset.recipient,
            'edit_phone': this.dataset.phone,
            'edit_province': this.dataset.province,
            'edit_city': this.dataset.city,
            'edit_district': this.dataset.district,
            'edit_postal_code': this.dataset.postal,
            'edit_full_address': this.dataset.full,
        };
        for (const [fieldId, value] of Object.entries(fields)) {
            const el = document.getElementById(fieldId);
            if (el) el.value = value;
        }
        new bootstrap.Modal(document.getElementById('modalEditAlamat')).show();
    });
});

// Hapus Alamat — populate modal
document.querySelectorAll('.btn-hapus-addr').forEach(btn => {
    btn.addEventListener('click', function() {
        const id    = this.dataset.id;
        const label = this.dataset.label;
        document.getElementById('hapusLabelText').textContent = label;
        document.getElementById('formHapusAlamat').action = '/dashboard/profil/alamat/' + id + '/hapus';
        new bootstrap.Modal(document.getElementById('modalHapusAlamat')).show();
    });
});
</script>

