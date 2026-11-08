<div class="mb-4 text-center">
    <h4 class="fw-bold text-dark">Buat Kata Sandi Baru</h4>
    <p class="text-muted small">Silakan tentukan kata sandi baru untuk akun Anda.</p>
</div>

<form action="<?= url('/reset-password') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= e($token) ?>">

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold text-secondary small">Kata Sandi Baru</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-light border-start-0" id="password" name="password" placeholder="Min. 8 karakter" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Ulangi Kata Sandi Baru</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
            <input type="password" class="form-control bg-light border-start-0" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" required>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm py-2 fs-6 rounded-3">
            <i class="bi bi-check2-circle me-2"></i> Perbarui Kata Sandi
        </button>
    </div>
</form>

<div class="text-center mt-4 pt-2 border-top">
    <p class="text-muted small mb-0">
        Batal reset sandi? 
        <a href="<?= url('/masuk') ?>" class="text-success fw-bold text-decoration-none">Masuk</a>
    </p>
</div>

