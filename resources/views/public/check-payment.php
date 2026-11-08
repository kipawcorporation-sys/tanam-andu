<?php
/**
 * View: Cek Status Tagihan & Pembayaran
 * Tema: Botanical Outdoor & Agroforestry Expedition (Plant For Fun)
 */
?>
<div class="topo-pattern-light py-5 border-bottom mb-4" style="border-bottom: 2px solid rgba(18,56,44,0.1) !important;">
    <div class="container text-center">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(18,56,44,0.08); border: 1px solid rgba(18,56,44,0.15);">
            <i class="bi bi-receipt" style="color: #12382c;"></i>
            <span class="font-poster text-uppercase small" style="color: #12382c; letter-spacing: 2px; font-size: 11px;">
                Pelacakan Tagihan Mandiri
            </span>
        </div>
        <h1 class="display-6 fw-bold font-poster text-uppercase mb-2" style="color: #12382c;">Cek Status Tagihan & Pembayaran</h1>
        <p class="lead text-muted mx-auto mb-0" style="max-width: 600px; font-size: 1rem;">
            Masukkan nomor invoice dan alamat email untuk memantau status verifikasi serta mengunggah bukti transfer manual.
        </p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm p-4 p-sm-5 rounded-4" style="border: 1px solid rgba(18,56,44,0.15) !important;">
                <form action="<?= url('/cek-pembayaran') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="invoice_no" class="form-label fw-bold small font-poster" style="color: #12382c;">Nomor Invoice Tagihan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-receipt"></i></span>
                            <input type="text" class="form-control bg-light border-start-0 text-uppercase font-mono" id="invoice_no" name="invoice_no" value="<?= e(old('invoice_no')) ?>" placeholder="INV-20261105-XXXX" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold small font-poster" style="color: #12382c;">Alamat Email Donatur / Pemesan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-light border-start-0 font-mono" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm py-2 font-poster">
                            <i class="bi bi-search me-2"></i> Cari & Buka Tagihan
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">
                    Punya akun terdaftar? <a href="<?= url('/masuk') ?>" class="fw-bold text-decoration-none" style="color: #12382c;">Masuk ke Dashboard</a> untuk melihat seluruh riwayat donasi Anda secara lengkap.
                </p>
            </div>
        </div>
    </div>
</div>
