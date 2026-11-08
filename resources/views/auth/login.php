<div class="mb-4 text-center">
    <h4 class="fw-bold text-dark">Masuk ke Akun</h4>
    <p class="text-muted small">Kelola donasi, tiket relawan, dan pantau pohon Anda</p>
</div>

<form action="<?= url('/masuk') ?>" method="POST" autocomplete="on">
    <?= csrf_field() ?>

    <!-- Email Input -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-light border-start-0" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="nama@email.com" required autofocus>
        </div>
    </div>

    <!-- Password Input -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold text-secondary small mb-0">Kata Sandi</label>
            <a href="<?= url('/lupa-password') ?>" class="text-success small text-decoration-none fw-semibold">Lupa Sandi?</a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-light border-start-0 border-end-0" id="password" name="password" placeholder="••••••••" required>
            <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePassword('password', this)">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm py-2 fs-6 rounded-3">
            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
        </button>
    </div>
</form>

<!-- Demo Login Info Box -->
<div class="mt-4 p-3 rounded-3 bg-light border text-start small">
    <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle text-success me-1"></i> Akun Uji Coba:</div>
    <div class="text-muted"><strong>Admin:</strong> admin@tanampohon.id / Admin123!</div>
</div>

<!-- Switch to Register -->
<div class="text-center mt-4 pt-2 border-top">
    <p class="text-muted small mb-0">
        Belum memiliki akun? 
        <a href="<?= url('/daftar') ?>" class="text-success fw-bold text-decoration-none">Daftar Sekarang</a>
    </p>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

