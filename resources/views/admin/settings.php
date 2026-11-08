<?php
/**
 * View: admin/settings.php — Pengaturan Umum Sistem & Cron Job.
 */
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 28px 32px; margin-bottom: 28px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:220px; height:220px; background:radial-gradient(circle,rgba(99,102,241,.1) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.4rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }
.settings-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; height:100%; }
.settings-card-header { background:linear-gradient(135deg,#f8fafc 0%,#f1f5f9 100%); padding:16px 24px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.settings-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.92rem; display:flex; align-items:center; gap:8px; }
.settings-card-body { padding:28px; }
.form-label { font-size:.82rem; font-weight:600; color:#374151; }
.form-control, .form-select { border-radius:10px; font-size:.87rem; border-color:#e2e8f0; }
.form-control:focus, .form-select:focus { border-color:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.12); }
.btn-save { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; padding:10px 28px; border-radius:10px; font-size:.87rem; font-weight:700; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:8px; }
.btn-save:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(34,197,94,.35); }
.cron-status { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; border-radius:50px; font-size:.76rem; font-weight:700; }
.cron-active { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
.cron-inactive { background:#fef9c3; color:#a16207; border:1px solid #fef08a; }
.btn-cron-run { background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff; border:none; padding:11px 20px; border-radius:10px; font-size:.87rem; font-weight:700; cursor:pointer; width:100%; transition:.2s; display:flex; align-items:center; justify-content:center; gap:8px; }
.btn-cron-run:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(79,70,229,.35); }
.cron-cmd-box { background:#0f172a; border-radius:10px; padding:14px 16px; font-family:'JetBrains Mono',monospace; font-size:.78rem; color:#86efac; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.cron-cmd-text { flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.btn-copy { background:rgba(255,255,255,.08); color:rgba(255,255,255,.7); border:1px solid rgba(255,255,255,.12); padding:5px 12px; border-radius:7px; font-size:.74rem; cursor:pointer; transition:.15s; white-space:nowrap; }
.btn-copy:hover { background:rgba(255,255,255,.15); color:#fff; }
.cron-log-item { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9; }
.cron-log-item:last-child { border-bottom:none; }
.cron-log-action { background:#1e293b; color:#94a3b8; font-family:'JetBrains Mono',monospace; font-size:.72rem; padding:3px 8px; border-radius:6px; white-space:nowrap; }
.cron-log-desc { font-size:.82rem; color:#374151; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.cron-log-time { color:#94a3b8; font-size:.74rem; white-space:nowrap; }
.section-divider { border-top:1px solid #f1f5f9; margin:24px 0; }
</style>

<!-- Page Hero -->
<div class="page-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-gear-wide-connected me-2"></i>Pengaturan Sistem</h4>
            <p>Konfigurasi parameter operasional platform dan otomasi latar belakang</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <?php if (!empty($lastCronRun)): ?>
                <span class="cron-status cron-active"><i class="bi bi-check-circle-fill"></i>Cron Aktif • <?= time_ago($lastCronRun) ?></span>
            <?php else: ?>
                <span class="cron-status cron-inactive"><i class="bi bi-exclamation-circle"></i>Cron Belum Dijalankan</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Konfigurasi Platform -->
    <div class="col-lg-6">
        <div class="settings-card">
            <div class="settings-card-header">
                <h6><i class="bi bi-sliders text-success"></i>Konfigurasi Platform</h6>
            </div>
            <div class="settings-card-body">
                <form action="/admin/pengaturan/update" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Platform</label>
                        <input type="text" name="app_name" class="form-control"
                               value="<?= htmlspecialchars($settings['app_name'] ?? 'Plant For Fun') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline Platform</label>
                        <input type="text" name="app_tagline" class="form-control"
                               value="<?= htmlspecialchars($settings['app_tagline'] ?? 'Kerenceng Coffee Planting & Agroforestry Expedition') ?>">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email Notifikasi Admin</label>
                            <input type="email" name="admin_email" class="form-control"
                                   value="<?= htmlspecialchars($settings['admin_email'] ?? 'admin@tanampohon.id') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">WhatsApp Layanan Bantuan</label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:10px 0 0 10px;border-color:#e2e8f0;">+62</span>
                                <input type="text" name="whatsapp_support" class="form-control" style="border-radius:0 10px 10px 0;"
                                       value="<?= htmlspecialchars($settings['whatsapp_support'] ?? '081234567890') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batas Waktu Pembayaran (Jam)</label>
                        <input type="number" name="payment_timeout_hours" class="form-control"
                               value="<?= htmlspecialchars($settings['payment_timeout_hours'] ?? '24') ?>" min="1" max="72">
                        <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>Durasi maksimal transfer manual sebelum invoice dibatalkan otomatis.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Bonus Poin Referral</label>
                        <input type="number" name="referral_bonus_points" class="form-control"
                               value="<?= htmlspecialchars($settings['referral_bonus_points'] ?? '25') ?>" min="0">
                        <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>Poin hadiah bagi pendaftar dan pemilik kode referral.</div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Integrasi WhatsApp Gateway (Fonnte API) -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0 text-success d-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp"></i> Integrasi WhatsApp Gateway (Fonnte)
                        </h6>
                        <span class="badge <?= ($settings['fonnte_enabled'] ?? '0') === '1' ? 'bg-success' : 'bg-secondary' ?> rounded-pill">
                            <?= ($settings['fonnte_enabled'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="fonnte_enabled" id="fonnte_enabled" value="1"
                                   <?= ($settings['fonnte_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="fonnte_enabled">
                                Aktifkan Pengiriman WhatsApp via Fonnte API
                            </label>
                        </div>
                        <div class="form-text small">Jika diaktifkan, sistem akan otomatis mengirim OTP pendaftaran dan notifikasi transaksi ke WhatsApp pengguna.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">API Token Fonnte *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key-fill text-muted"></i></span>
                            <input type="password" name="fonnte_token" id="fonnte_token" class="form-control border-start-0 border-end-0"
                                   value="<?= htmlspecialchars($settings['fonnte_token'] ?? '') ?>" placeholder="Masukkan API Token dari fonnte.com">
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleTokenVisibility()">
                                <i class="bi bi-eye" id="toggleTokenIcon"></i>
                            </button>
                        </div>
                        <div class="form-text small">Dapatkan API Token dari dashboard akun Anda di <a href="https://fonnte.com" target="_blank" class="text-success text-decoration-none fw-semibold">fonnte.com</a>.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Pengirim WhatsApp (Info Device)</label>
                        <input type="text" name="fonnte_sender" class="form-control"
                               value="<?= htmlspecialchars($settings['fonnte_sender'] ?? '') ?>" placeholder="08xxxxxxxxxx (Nomor HP WhatsApp yang tertaut di Fonnte)">
                    </div>

                    <!-- Otomasi Aktif Info -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="fw-bold small text-dark mb-2"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Otomasi Notifikasi Aktif:</div>
                        <ul class="list-unstyled mb-0 small text-secondary d-flex flex-column gap-1">
                            <li><i class="bi bi-check2-circle text-success me-1"></i> <strong>OTP Registrasi:</strong> Kode 6-digit verifikasi nomor WhatsApp saat pendaftaran baru.</li>
                            <li><i class="bi bi-check2-circle text-success me-1"></i> <strong>Tagihan Transaksi Baru:</strong> Rincian invoice, total nominal & petunjuk transfer saat donasi, belanja toko, atau event.</li>
                            <li><i class="bi bi-check2-circle text-success me-1"></i> <strong>Bukti Diterima:</strong> Notifikasi saat user mengunggah bukti pembayaran.</li>
                            <li><i class="bi bi-check2-circle text-success me-1"></i> <strong>Pembayaran Disetujui:</strong> Konfirmasi pembayaran berhasil beserta tiket/sertifikat.</li>
                            <li><i class="bi bi-check2-circle text-success me-1"></i> <strong>Pembayaran Ditolak:</strong> Pemberitahuan penolakan dengan alasan jelas.</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-save mb-4">
                        <i class="bi bi-save2-fill"></i>Simpan Pengaturan
                    </button>
                </form>

                <div class="section-divider"></div>

                <!-- Widget Uji Coba Kirim WA -->
                <div class="p-3 rounded-3" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <label class="form-label fw-bold text-success mb-2 d-flex align-items-center gap-1">
                        <i class="bi bi-send-check"></i> Uji Coba Kirim Pesan WhatsApp (Fonnte)
                    </label>
                    <div class="input-group mb-2">
                        <input type="tel" id="testPhone" class="form-control" placeholder="Contoh: 081234567890" value="<?= htmlspecialchars($settings['whatsapp_support'] ?? '') ?>">
                        <button type="button" id="btnTestFonnte" class="btn btn-success fw-semibold">
                            <i class="bi bi-send me-1"></i>Kirim Tes
                        </button>
                    </div>
                    <div id="testFonnteResult"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Cron Job & Maintenance -->
    <div class="col-lg-6">
        <div class="settings-card">
            <div class="settings-card-header">
                <h6><i class="bi bi-robot text-indigo" style="color:#6366f1;"></i>Otomasi Terjadwal (Cron Job)</h6>
                <?php if (!empty($lastCronRun)): ?>
                    <span class="cron-status cron-active" style="font-size:.7rem;padding:3px 10px;">
                        <i class="bi bi-check-circle-fill"></i>Aktif
                    </span>
                <?php else: ?>
                    <span class="cron-status cron-inactive" style="font-size:.7rem;padding:3px 10px;">Belum Aktif</span>
                <?php endif; ?>
            </div>
            <div class="settings-card-body">
                <p class="text-muted small mb-4">
                    Cron job menjalankan tugas rutin: membatalkan transaksi kadaluarsa, mengembalikan stok kaos & kuota relawan, mengirim pengingat pembayaran H-3 jam, dan pengingat event relawan H-3 & H-1.
                </p>

                <!-- Manual Trigger -->
                <form action="/admin/pengaturan/cron/jalankan" method="POST" class="mb-4">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-cron-run"
                            onclick="return confirm('Jalankan seluruh tugas cron job sekarang?')">
                        <i class="bi bi-play-circle-fill"></i>Jalankan Cron Job Sekarang
                    </button>
                </form>

                <!-- Crontab CLI -->
                <div class="mb-3">
                    <label class="form-label d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-terminal text-muted"></i>Perintah Crontab Linux / cPanel
                        <span class="text-muted fw-normal">(tiap 15 menit)</span>
                    </label>
                    <div class="cron-cmd-box">
                        <div class="cron-cmd-text" id="cronCmdText">*/15 * * * * <?= htmlspecialchars($cronCliCommand ?? '') ?></div>
                        <button class="btn-copy" onclick="navigator.clipboard.writeText(document.getElementById('cronCmdText').textContent); this.textContent='✓ Disalin!'">
                            <i class="bi bi-clipboard me-1"></i>Salin
                        </button>
                    </div>
                </div>

                <!-- Web Cron URL -->
                <div class="mb-4">
                    <label class="form-label d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-globe2 text-muted"></i>Web Cron URL
                        <span class="text-muted fw-normal">(cron-job.org / UptimeRobot)</span>
                    </label>
                    <div class="cron-cmd-box">
                        <div class="cron-cmd-text" id="cronUrlText"><?= htmlspecialchars($cronWebhookUrl ?? '') ?></div>
                        <button class="btn-copy" onclick="navigator.clipboard.writeText(document.getElementById('cronUrlText').textContent); this.textContent='✓ Disalin!'">
                            <i class="bi bi-clipboard me-1"></i>Salin
                        </button>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Riwayat Cron -->
                <h6 class="fw-700 mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;">
                    Riwayat Eksekusi Terakhir
                </h6>
                <?php if (empty($cronLogs)): ?>
                    <div class="text-muted small fst-italic text-center py-4">
                        <i class="bi bi-clock-history d-block fs-3 mb-2 text-muted opacity-50"></i>
                        Belum ada riwayat aktivitas cron tercatat
                    </div>
                <?php else: ?>
                    <div>
                        <?php foreach ($cronLogs as $cl): ?>
                            <div class="cron-log-item">
                                <span class="cron-log-action"><?= htmlspecialchars($cl['action']) ?></span>
                                <span class="cron-log-desc"><?= htmlspecialchars($cl['description']) ?></span>
                                <span class="cron-log-time"><?= time_ago($cl['created_at']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTokenVisibility() {
    const inp = document.getElementById('fonnte_token');
    const ico = document.getElementById('toggleTokenIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}

document.getElementById('btnTestFonnte')?.addEventListener('click', async function() {
    const phone = document.getElementById('testPhone').value.trim();
    const resultBox = document.getElementById('testFonnteResult');
    const btn = this;

    if (!phone) {
        resultBox.innerHTML = '<div class="alert alert-warning py-2 px-3 small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Masukkan nomor WhatsApp tujuan tes.</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
    resultBox.innerHTML = '<div class="text-muted small py-1"><span class="spinner-border spinner-border-sm me-1"></span>Menghubungi Fonnte API...</div>';

    try {
        const formData = new FormData();
        formData.append('phone', phone);
        formData.append('csrf_token', '<?= csrf_token() ?>');

        const resp = await fetch('/admin/pengaturan/fonnte/test', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();

        if (data.success) {
            resultBox.innerHTML = `<div class="alert alert-success py-2 px-3 small mb-0"><i class="bi bi-check-circle-fill me-1"></i><strong>Berhasil!</strong> ${data.message}</div>`;
        } else {
            resultBox.innerHTML = `<div class="alert alert-danger py-2 px-3 small mb-0"><i class="bi bi-x-circle-fill me-1"></i><strong>Gagal:</strong> ${data.message}</div>`;
        }
    } catch (err) {
        resultBox.innerHTML = `<div class="alert alert-danger py-2 px-3 small mb-0"><i class="bi bi-wifi-off me-1"></i>Koneksi bermasalah: ${err.message}</div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i>Kirim Tes';
    }
});
</script>
