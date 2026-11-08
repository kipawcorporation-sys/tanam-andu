<div class="mb-4 text-center">
    <h4 class="fw-bold text-dark">Daftar Akun Baru</h4>
    <p class="text-muted small">Bergabunglah menjadi bagian dari pelestari alam Indonesia</p>
</div>

<form action="<?= url('/daftar') ?>" method="POST" autocomplete="on">
    <?= csrf_field() ?>

    <!-- Nama Lengkap -->
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control bg-light border-start-0" id="name" name="name" value="<?= e(old('name')) ?>" placeholder="Contoh: Budi Santoso" required autofocus>
        </div>
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-light border-start-0" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="nama@email.com" required>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <!-- Nomor WhatsApp & Tombol OTP -->
        <div class="col-sm-7">
            <label for="phone" class="form-label fw-semibold text-secondary small">
                Nomor WhatsApp <span class="badge bg-success text-white ms-1" id="otpStatusBadge" style="display:none;"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-whatsapp text-success"></i></span>
                <input type="tel" class="form-control bg-light border-start-0 border-end-0" id="phone" name="phone" value="<?= e(old('phone')) ?>" placeholder="081234567890" required>
                <button type="button" id="btnSendOtp" class="btn btn-outline-success fw-semibold btn-sm">
                    <span id="btnSendOtpText"><i class="bi bi-send me-1"></i>Kirim OTP</span>
                </button>
            </div>
            <div id="otpFeedback" class="form-text small text-muted">Wajib verifikasi nomor WhatsApp untuk aktivasi akun.</div>
        </div>

        <!-- Kota Domisili -->
        <div class="col-sm-5">
            <label for="city" class="form-label fw-semibold text-secondary small">Kota Domisili</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="city" name="city" value="<?= e(old('city')) ?>" placeholder="Contoh: Bandung" required>
            </div>
        </div>
    </div>

    <!-- Kotak Verifikasi Kode OTP WhatsApp -->
    <div class="mb-3 p-3 rounded-3 border" id="otpBox" style="display:none; background:#f0fdf4; border-color:#bbf7d0 !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <label for="otp_code" class="form-label fw-bold text-success small mb-0">
                <i class="bi bi-shield-lock-fill me-1"></i>Kode OTP WhatsApp (6 Digit) *
            </label>
            <span id="otpCountdown" class="badge bg-white text-muted border small font-monospace">05:00</span>
        </div>
        <div class="input-group">
            <input type="text" class="form-control font-monospace fw-bold text-center bg-white" id="otp_code" name="otp_code" maxlength="6" placeholder="• • • • • •" style="letter-spacing: 5px; font-size: 1.15rem;" inputmode="numeric">
            <button type="button" id="btnVerifyOtp" class="btn btn-success fw-bold px-3">
                <i class="bi bi-check2-circle me-1"></i>Verifikasi
            </button>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 small">
            <span id="otpVerifyMsg" class="text-muted">Kode OTP 6-digit dikirim via WhatsApp.</span>
            <button type="button" id="btnResendOtp" class="btn btn-link text-success p-0 text-decoration-none small fw-semibold" style="display:none;">
                <i class="bi bi-arrow-repeat me-1"></i>Kirim Ulang
            </button>
        </div>
    </div>

    <!-- Ukuran Kaos Default -->
    <div class="mb-3">
        <label for="default_tshirt_size" class="form-label fw-semibold text-secondary small">Ukuran Kaos Bawaan (Opsional)</label>
        <select class="form-select bg-light" id="default_tshirt_size" name="default_tshirt_size">
            <option value="">Pilih Ukuran Kaos Anda</option>
            <option value="S" <?= old('default_tshirt_size') === 'S' ? 'selected' : '' ?>>S (Small)</option>
            <option value="M" <?= old('default_tshirt_size') === 'M' ? 'selected' : '' ?>>M (Medium)</option>
            <option value="L" <?= old('default_tshirt_size') === 'L' ? 'selected' : '' ?>>L (Large)</option>
            <option value="XL" <?= old('default_tshirt_size') === 'XL' ? 'selected' : '' ?>>XL (Extra Large)</option>
            <option value="XXL" <?= old('default_tshirt_size') === 'XXL' ? 'selected' : '' ?>>XXL (Double XL)</option>
        </select>
        <div class="form-text small">Untuk mempermudah pemilihan ukuran saat memesan kaos merchandise atau event.</div>
    </div>

    <div class="row g-2 mb-3">
        <!-- Password -->
        <div class="col-sm-6">
            <label for="password" class="form-label fw-semibold text-secondary small">Kata Sandi</label>
            <input type="password" class="form-control bg-light" id="password" name="password" placeholder="Min. 8 karakter" required>
        </div>

        <!-- Password Confirmation -->
        <div class="col-sm-6">
            <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Ulangi Sandi</label>
            <input type="password" class="form-control bg-light" id="password_confirmation" name="password_confirmation" placeholder="Ulangi sandi" required>
        </div>
    </div>

    <!-- Referral Code (Collapsible or visible) -->
    <div class="mb-3">
        <label for="referral_code" class="form-label fw-semibold text-secondary small">
            Kode Referral / Ajak Teman (Opsional)
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-gift"></i></span>
            <input type="text" class="form-control bg-light border-start-0 text-uppercase" id="referral_code" name="referral_code" value="<?= e(old('referral_code', $referralCode ?? '')) ?>" placeholder="Contoh: REF12345">
        </div>
        <div class="form-text small text-success">Dapatkan bonus 5 poin dengan memasukkan kode referral teman!</div>
    </div>

    <!-- Terms Checkbox -->
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="agree_terms" required>
        <label class="form-check-label small text-secondary" for="agree_terms">
            Saya menyetujui <a href="<?= url('/syarat-ketentuan') ?>" target="_blank" class="text-success text-decoration-none">Syarat & Ketentuan</a> serta <a href="<?= url('/kebijakan-privasi') ?>" target="_blank" class="text-success text-decoration-none">Kebijakan Privasi</a> TanamPohon.id.
        </label>
    </div>

    <!-- Submit Button -->
    <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm py-2 fs-6 rounded-3">
            <i class="bi bi-person-check-fill me-2"></i> Daftar Sekarang
        </button>
    </div>
</form>

<!-- Switch to Login -->
<div class="text-center mt-4 pt-2 border-top">
    <p class="text-muted small mb-0">
        Sudah memiliki akun? 
        <a href="<?= url('/masuk') ?>" class="text-success fw-bold text-decoration-none">Masuk di sini</a>
    </p>
</div>

<script>
let otpTimer = null;
let otpSecondsLeft = 300;
let isPhoneVerified = false;

async function requestOtp() {
    const phoneInput = document.getElementById('phone');
    const nameInput  = document.getElementById('name');
    const feedback   = document.getElementById('otpFeedback');
    const btnSend    = document.getElementById('btnSendOtp');
    const btnText    = document.getElementById('btnSendOtpText');
    const otpBox     = document.getElementById('otpBox');
    const verifyMsg  = document.getElementById('otpVerifyMsg');

    const phone = phoneInput.value.trim();
    const name  = nameInput.value.trim() || 'Calon Relawan';

    const cleanPhone = phone.replace(/\D/g, '');
    if (cleanPhone.length < 9) {
        feedback.className = 'form-text small text-danger fw-semibold';
        feedback.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Masukkan nomor WhatsApp yang valid (minimal 10 digit).';
        phoneInput.focus();
        return;
    }

    btnSend.disabled = true;
    btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
    feedback.className = 'form-text small text-muted';
    feedback.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menghubungi gateway WhatsApp...';

    try {
        const formData = new FormData();
        formData.append('phone', phone);
        formData.append('name', name);
        formData.append('csrf_token', '<?= csrf_token() ?>');

        const resp = await fetch('<?= url('/daftar/kirim-otp') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();

        if (data.success) {
            otpBox.style.display = 'block';
            document.getElementById('otp_code').focus();

            let extraInfo = '';
            if (data.dev_code) {
                extraInfo = ` <span class="badge bg-warning text-dark font-monospace">[Mode Dev: ${data.dev_code}]</span>`;
            }

            feedback.className = 'form-text small text-success fw-semibold';
            feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>' + data.message + extraInfo;

            verifyMsg.className = 'text-success small';
            verifyMsg.innerHTML = '<i class="bi bi-whatsapp me-1"></i>Kode 6-digit terkirim ke WhatsApp Anda.' + extraInfo;

            startOtpCountdown();
        } else {
            feedback.className = 'form-text small text-danger fw-semibold';
            feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i>' + data.message;
            btnSend.disabled = false;
            btnText.innerHTML = '<i class="bi bi-send me-1"></i>Kirim OTP';
        }
    } catch (err) {
        feedback.className = 'form-text small text-danger fw-semibold';
        feedback.innerHTML = '<i class="bi bi-wifi-off me-1"></i>Gagal terhubung ke server: ' + err.message;
        btnSend.disabled = false;
        btnText.innerHTML = '<i class="bi bi-send me-1"></i>Kirim OTP';
    }
}

function startOtpCountdown() {
    clearInterval(otpTimer);
    otpSecondsLeft = 300;
    const cd = document.getElementById('otpCountdown');
    const btnResend = document.getElementById('btnResendOtp');
    const btnSend = document.getElementById('btnSendOtp');
    const btnText = document.getElementById('btnSendOtpText');

    btnResend.style.display = 'none';

    otpTimer = setInterval(() => {
        otpSecondsLeft--;
        if (otpSecondsLeft <= 0) {
            clearInterval(otpTimer);
            cd.textContent = 'Kedaluwarsa';
            cd.className = 'badge bg-danger text-white border small font-monospace';
            btnResend.style.display = 'inline-block';
            btnSend.disabled = false;
            btnText.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Kirim Ulang';
        } else {
            const m = String(Math.floor(otpSecondsLeft / 60)).padStart(2, '0');
            const s = String(otpSecondsLeft % 60).padStart(2, '0');
            cd.textContent = `${m}:${s}`;

            // Izinkan resend setelah 60 detik
            if (otpSecondsLeft <= 240) {
                btnResend.style.display = 'inline-block';
            }
        }
    }, 1000);
}

document.getElementById('btnSendOtp')?.addEventListener('click', requestOtp);
document.getElementById('btnResendOtp')?.addEventListener('click', requestOtp);

document.getElementById('btnVerifyOtp')?.addEventListener('click', async function() {
    const phoneInput = document.getElementById('phone');
    const otpInput   = document.getElementById('otp_code');
    const verifyMsg  = document.getElementById('otpVerifyMsg');
    const badge      = document.getElementById('otpStatusBadge');
    const btn        = this;

    const code = otpInput.value.trim();
    if (code.length !== 6) {
        verifyMsg.className = 'text-danger small fw-semibold';
        verifyMsg.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Masukkan 6 digit kode OTP.';
        otpInput.focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

    try {
        const formData = new FormData();
        formData.append('phone', phoneInput.value.trim());
        formData.append('otp_code', code);
        formData.append('csrf_token', '<?= csrf_token() ?>');

        const resp = await fetch('<?= url('/daftar/verifikasi-otp') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();

        if (data.success) {
            isPhoneVerified = true;
            badge.style.display = 'inline-block';
            verifyMsg.className = 'text-success small fw-semibold';
            verifyMsg.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i>' + data.message;
            otpInput.classList.remove('is-invalid');
            otpInput.classList.add('is-valid');
            otpInput.readOnly = true;
            phoneInput.readOnly = true;
            btn.className = 'btn btn-success fw-bold px-3 disabled';
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Valid';
            clearInterval(otpTimer);
        } else {
            verifyMsg.className = 'text-danger small fw-semibold';
            verifyMsg.innerHTML = '<i class="bi bi-x-circle me-1"></i>' + data.message;
            otpInput.classList.add('is-invalid');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Verifikasi';
        }
    } catch (err) {
        verifyMsg.className = 'text-danger small fw-semibold';
        verifyMsg.innerHTML = '<i class="bi bi-wifi-off me-1"></i>' + err.message;
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Verifikasi';
    }
});
</script>

