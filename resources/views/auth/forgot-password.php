<div class="mb-4 text-center">
    <h4 class="fw-bold text-dark">Lupa Kata Sandi?</h4>
    <p class="text-muted small">Masukkan alamat email Anda untuk menerima instruksi pemulihan kata sandi.</p>
</div>

<form action="<?= url('/lupa-password') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email Terdaftar</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-light border-start-0" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="nama@email.com" required autofocus>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm py-2 fs-6 rounded-3">
            <i class="bi bi-send-fill me-2"></i> Kirim Instruksi Reset
        </button>
    </div>
</form>

<div class="text-center mt-4 pt-2 border-top">
    <p class="text-muted small mb-0">
        Ingat kata sandi Anda? 
        <a href="<?= url('/masuk') ?>" class="text-success fw-bold text-decoration-none">Kembali ke Halaman Masuk</a>
    </p>
</div>

