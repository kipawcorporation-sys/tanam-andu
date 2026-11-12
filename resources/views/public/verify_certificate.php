<?php
/**
 * View: Verifikasi Keaslian Sertifikat Publik
 * Path: resources/views/public/verify_certificate.php
 */

$data = $certificate['data'] ?? [];
$isFound = !empty($certificate);
?>

<section class="py-5 text-white topo-pattern" style="background-color: #12382c; border-bottom: 3px solid #1c4b3c;">
    <div class="container text-center py-3">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25);">
            <i class="bi bi-patch-check-fill" style="color: #bfd254;"></i>
            <span class="font-poster text-uppercase small text-white" style="letter-spacing: 1.5px; font-size: 11px;">
                Sistem Validasi Resmi
            </span>
        </div>
        <h1 class="display-5 fw-bold font-poster mb-3">Verifikasi Keaslian Sertifikat</h1>
        <p class="lead text-light opacity-90 mx-auto mb-4" style="max-width: 650px;">
            Pastikan keabsahan sertifikat donasi pohon dan partisipasi relawan yang diterbitkan resmi oleh <strong>Plant For Fun &bull; Kerenceng Coffee Expedition</strong>.
        </p>

        <!-- Formulir Pencarian Cepat -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <form action="<?= url('/verifikasi-sertifikat') ?>" method="GET" class="card border-0 p-2 rounded-pill shadow-lg bg-white" onsubmit="return handleVerifySubmit(event);">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-3 text-muted">
                            <i class="bi bi-search fs-5"></i>
                        </span>
                        <input type="text" id="certNumberInput" name="nomor" class="form-control border-0 shadow-none ps-2 font-mono" placeholder="Masukkan nomor (contoh: CERT-20261105-...)" value="<?= e($searchedNumber ?? '') ?>" required>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm font-poster">
                            Verifikasi Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">

        <!-- KONDISI 1: HASIL DITEMUKAN (VALID) -->
        <?php if ($isFound): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Banner Status Terverifikasi -->
                    <div class="alert alert-success border-0 rounded-4 shadow-sm p-4 mb-4 d-flex align-items-center">
                        <div class="fs-1 text-success me-3">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1">Sertifikat Sah & Terdaftar Resmi!</h5>
                            <p class="mb-0 small text-success-emphasis">
                                Dokumen dengan nomor seri <strong><?= e($certificate['certificate_number']) ?></strong> terdata secara autentik dalam basis data resmi <strong>Plant For Fun &bull; Kerenceng Coffee Expedition</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Kartu Sertifikat Elegan -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white position-relative overflow-hidden mb-4 print-area">
                        <!-- Watermark latar belakang -->
                        <div class="position-absolute top-50 start-50 translate-middle opacity-10 pointer-events-none" style="z-index: 0; font-size: 260px; color: #198754;">
                            <i class="bi bi-tree"></i>
                        </div>

                        <div class="position-relative" style="z-index: 1;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-4 mb-4 gap-3">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <img src="<?= asset('assets/images/logo-plant-for-fun.svg') ?>" alt="Plant For Fun" style="height: 44px; width: auto;">
                                    <div class="d-none d-sm-block vr" style="height: 34px; opacity: 0.25;"></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small text-muted fw-bold font-monospace" style="font-size: 10px; text-transform: uppercase;">Presented by:</span>
                                        <img src="<?= asset('assets/images/logo-andu.png') ?>" alt="andu" title="andu" style="height: 22px; width: auto; object-fit: contain;">
                                        <img src="<?= asset('assets/images/logo-karsa-kerentjeng.png') ?>" alt="Karsa Kerentjeng" title="Karsa Kerentjeng" style="height: 24px; width: auto; object-fit: contain;">
                                        <img src="<?= asset('assets/images/logo-ethera.png') ?>" alt="Ethera" title="Ethera" style="height: 18px; width: auto; object-fit: contain;">
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-success-subtle border border-success-subtle rounded-pill px-3 py-2 fw-semibold" style="color:#0a3622;">
                                        <?= strtoupper($certificate['type'] === 'donasi' ? 'Sertifikat Donasi' : 'Sertifikat Relawan') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="text-center my-4">
                                <p class="text-uppercase text-muted fw-bold small mb-1" style="letter-spacing: 2px;">Sertifikat Penghargaan Diberikan Kepada</p>
                                <h2 class="display-6 fw-bold text-dark text-capitalize mb-2">
                                    <?= htmlspecialchars($data['recipient_name'] ?? 'Donatur Peduli Lingkungan') ?>
                                </h2>
                                <p class="text-secondary mx-auto" style="max-width: 550px;">
                                    <?php if ($certificate['type'] === 'donasi'): ?>
                                        Atas kontribusi nyata dan dedikasi menjaga kelestarian alam melalui penanaman pohon demi masa depan bumi Indonesia yang hijau dan lestari.
                                    <?php else: ?>
                                        Atas partisipasi aktif dan kerja keras sebagai relawan lapangan dalam aksi penanaman pohon demi kelestarian ekosistem lingkungan.
                                    <?php endif; ?>
                                </p>
                            </div>

                            <!-- Detail Rincian Kontribusi -->
                            <div class="bg-light-subtle rounded-4 p-4 my-4 border">
                                <h6 class="fw-bold text-dark mb-3">Rincian Kontribusi & Verifikasi Data:</h6>
                                <div class="row g-3 small">
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block">Nomor Sertifikat:</span>
                                        <strong class="font-monospace text-dark fs-6"><?= e($certificate['certificate_number']) ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted d-block">Tanggal Diterbitkan:</span>
                                        <strong class="text-dark"><?= date('d F Y', strtotime($certificate['issued_at'])) ?></strong>
                                    </div>

                                    <?php if ($certificate['type'] === 'donasi'): ?>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Jumlah Pohon Tertanam:</span>
                                            <strong class="text-success fs-6"><?= (int)($data['total_trees'] ?? 0) ?> Batang Bibit Pohon</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Spesies Pohon:</span>
                                            <strong class="text-dark"><?= htmlspecialchars($data['species_summary'] ?? 'Pohon Tropis') ?></strong>
                                        </div>
                                        <?php if (!empty($data['total_co2_kg'])): ?>
                                            <div class="col-sm-6">
                                                <span class="text-muted d-block">Estimasi Serapan CO₂:</span>
                                                <strong class="text-success"><?= number_format((float)$data['total_co2_kg'], 1) ?> kg / tahun</strong>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Status Penanaman:</span>
                                            <span class="badge bg-success-subtle rounded-pill" style="color:#0a3622;">Terkonfirmasi & Diawasi</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Kegiatan Relawan:</span>
                                            <strong class="text-dark"><?= htmlspecialchars($data['event_title'] ?? '-') ?></strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Lokasi Pelaksanaan:</span>
                                            <strong class="text-dark"><?= htmlspecialchars($data['location'] ?? '-') ?></strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Peran:</span>
                                            <strong class="text-primary"><?= htmlspecialchars($data['role'] ?? 'Relawan Penanaman') ?></strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Waktu Kegiatan:</span>
                                            <strong class="text-dark"><?= !empty($data['event_date']) ? date('d F Y', strtotime($data['event_date'])) : '-' ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Tanda Tangan & QR -->
                            <div class="d-flex justify-content-between align-items-end pt-3">
                                <div>
                                    <div class="small text-muted mb-1">Diverifikasi secara resmi oleh:</div>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($data['signer_name'] ?? 'Koordinator Ekspedisi Kerenceng') ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($data['signer_title'] ?? 'Plant For Fun &bull; Kerenceng Expedition') ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="p-2 bg-white border rounded-3 d-inline-block shadow-sm">
                                        <i class="bi bi-qr-code fs-1 text-dark"></i>
                                    </div>
                                    <div class="small text-muted mt-1 font-monospace" style="font-size: 11px;">VERIFIED ID</div>
                                </div>
                            </div>

                            <!-- Media Partner Footer Strip -->
                            <div class="border-top pt-3 mt-4 d-flex flex-wrap justify-content-between align-items-center small text-muted">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Media Partner:</span>
                                    <img src="<?= asset('assets/images/logo-clkpny.png') ?>" alt="CLK.PNY" style="height: 16px; width: auto; object-fit: contain;">
                                    <img src="<?= asset('assets/images/logo-cicalengka-go.png') ?>" alt="Cicalengka GO" style="height: 16px; width: auto; object-fit: contain;">
                                </div>
                                <div class="font-monospace text-success fw-bold" style="font-size: 11px;">
                                    &bull; 1.550 MDPL &bull; CICALENGKA, JAWA BARAT
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aksi Tombol -->
                    <div class="d-flex justify-content-center gap-2 mb-5">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="window.print();">
                            <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan Halaman
                        </button>
                        <a href="<?= url('/verifikasi-sertifikat') ?>" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Cek Sertifikat Lain
                        </a>
                    </div>
                </div>
            </div>

        <!-- KONDISI 2: TIDAK DITEMUKAN -->
        <?php elseif ($notFound): ?>
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center py-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <div class="avatar-circle mx-auto bg-danger-subtle text-danger rounded-circle fs-1 d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Sertifikat Tidak Ditemukan</h4>
                        <p class="text-secondary small mb-4">
                            Nomor sertifikat <strong class="text-danger font-monospace"><?= e($searchedNumber) ?></strong> tidak ditemukan dalam basis data resmi kami. Pastikan Anda mengetikkan nomor sertifikat dengan lengkap dan benar.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?= url('/verifikasi-sertifikat') ?>" class="btn btn-success rounded-pill px-4 fw-semibold">
                                Coba Nomor Lain
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <!-- KONDISI 3: BELUM MENCARI (PANDUAN & CONTOH) -->
        <?php else: ?>
            <div class="row g-4 align-items-center my-3">
                <div class="col-lg-6">
                    <h3 class="fw-bold text-dark mb-3">Bagaimana Cara Mengetahui Keaslian Sertifikat?</h3>
                    <p class="text-secondary mb-4">
                        Setiap sertifikat resmi yang dikeluarkan oleh Plant For Fun memiliki nomor seri unik yang tertera di bagian kanan atas atau bawah sertifikat, serta QR code yang mengarahkan langsung ke halaman ini.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Cari Kode Seri Sertifikat</h6>
                                <p class="small text-muted mb-0">Format nomor sertifikat diawali dengan <code>CERT-YYYYMMDD-...</code> (contoh: <code>CERT-20261105-767216</code>).</p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start">
                            <div class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Masukkan ke Kolom Pencarian</h6>
                                <p class="small text-muted mb-0">Ketik nomor tersebut pada formulir di atas, lalu tekan tombol <strong>Verifikasi Sekarang</strong>.</p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-start">
                            <div class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">3</div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Lihat Hasil & Rincian Donasi</h6>
                                <p class="small text-muted mb-0">Sistem akan menampilkan nama penerima, tanggal terbit, jumlah pohon, dan estimasi dampak ekologis yang valid.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Sampel Sertifikat Resmi -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                            <i class="bi bi-clock-history text-success me-2"></i> Contoh Sertifikat Terbit Baru-baru Ini
                        </h5>
                        <p class="small text-muted mb-3">Klik salah satu nomor sertifikat di bawah ini untuk menguji sistem verifikasi:</p>

                        <?php if (empty($recentCertificates)): ?>
                            <p class="text-muted small">Belum ada sertifikat yang diterbitkan.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recentCertificates as $rc): ?>
                                    <a href="<?= url('/verifikasi-sertifikat/' . $rc['certificate_number']) ?>" class="list-group-item list-group-item-action px-3 py-3 rounded-3 mb-2 border border-light-subtle d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="font-monospace fw-bold text-success mb-1">
                                                <?= e($rc['certificate_number']) ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($rc['recipient_name'] ?? 'Donatur') ?> &bull; 
                                                <?= date('d M Y', strtotime($rc['issued_at'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-secondary border">
                                            <?= ucfirst($rc['type']) ?> <i class="bi bi-chevron-right ms-1"></i>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<script>
function handleVerifySubmit(e) {
    e.preventDefault();
    let num = document.getElementById("certNumberInput").value.trim();
    if (num) {
        window.location.href = "<?= url('/verifikasi-sertifikat/') ?>/" + encodeURIComponent(num);
    }
    return false;
}
</script>

