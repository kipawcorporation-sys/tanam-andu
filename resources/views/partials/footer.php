<?php
/**
 * Partial: Public Footer
 * Menggunakan Palet Pine Green, Topo Aesthetic & Partner Logos dari Poster "Plant For Fun"
 */
?>
<footer class="footer text-white pt-5 pb-4 mt-5 position-relative" style="background-color: #0f2e24; border-top: 4px solid #1c4b3c;">
    <div class="container">
        
        <!-- Baris Banner Partner & Kolaborator Resmi (dari Poster) -->
        <div class="p-4 rounded-4 mb-5" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                <span class="badge bg-warning text-dark font-poster px-3 py-1 rounded-pill mb-2">
                    <i class="bi bi-patch-check-fill me-1"></i> Kolaborasi Aksi Hijau Nusantara
                </span>
                <span class="small text-light opacity-75 font-mono">
                    EVENT UTAMA: PLANT FOR FUN &bull; KERENCENG 2026
                </span>
            </div>
            
            <?php 
                $logoTheme = 'light'; 
                require VIEW_PATH . '/partials/partner-logos.php'; 
            ?>
        </div>

        <div class="row g-4">
            <!-- Kolom 1: Profil Organisasi -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-3">
                    <img src="<?= asset('assets/images/logo-plant-for-fun-white.svg') ?>" alt="Plant For Fun" style="height: 48px; width: auto; object-fit: contain;">
                </div>
                <p class="text-secondary small mb-3">
                    Gerakan penanaman pohon kopi agroforestri dan pohon konservasi di lereng Gunung Kerenceng (1.742 mdpl). Berkolaborasi bersama andu, Karsa Kerentjeng, dan masyarakat lokal untuk masa depan bumi nusantara.
                </p>
                <div class="d-flex gap-2">
                    <a href="https://instagram.com/tanampohon.id" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px;"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com/tanampohon.id" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px;"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/tanampohon_id" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px;"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://youtube.com/@tanampohon" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px;"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Kolom 2: Navigasi Cepat -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold font-poster text-uppercase mb-3" style="color: #bfd254;">Program</h6>
                <ul class="list-unstyled text-secondary small mb-0">
                    <li class="mb-2"><a href="<?= url('/donasi') ?>" class="text-decoration-none text-light-emphasis">Donasi Pohon</a></li>
                    <li class="mb-2"><a href="<?= url('/kegiatan') ?>" class="text-decoration-none text-light-emphasis">Jadwal Relawan</a></li>
                    <li class="mb-2"><a href="<?= url('/toko') ?>" class="text-decoration-none text-light-emphasis">Kopi & Merchandise</a></li>
                    <li class="mb-2"><a href="<?= url('/cek-ongkir') ?>" class="text-decoration-none text-light-emphasis">Cek Ongkir Mandiri</a></li>
                    <li class="mb-2"><a href="<?= url('/transparansi') ?>" class="text-decoration-none text-light-emphasis">Peta Penanaman</a></li>
                    <li class="mb-2"><a href="<?= url('/cek-pembayaran') ?>" class="text-decoration-none text-light-emphasis">Cek Tagihan / Bukti</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Informasi & Legal -->
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold font-poster text-uppercase mb-3" style="color: #bfd254;">Informasi</h6>
                <ul class="list-unstyled text-secondary small mb-0">
                    <li class="mb-2"><a href="<?= url('/tentang-kami') ?>" class="text-decoration-none text-light-emphasis">Tentang Kami</a></li>
                    <li class="mb-2"><a href="<?= url('/faq') ?>" class="text-decoration-none text-light-emphasis">Tanya Jawab (FAQ)</a></li>
                    <li class="mb-2"><a href="<?= url('/syarat-ketentuan') ?>" class="text-decoration-none text-light-emphasis">Syarat & Ketentuan</a></li>
                    <li class="mb-2"><a href="<?= url('/kebijakan-privasi') ?>" class="text-decoration-none text-light-emphasis">Kebijakan Privasi</a></li>
                    <li class="mb-2"><a href="<?= url('/verifikasi-sertifikat') ?>" class="text-decoration-none text-light-emphasis">Cek Sertifikat</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Kontak & Pembayaran -->
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold font-poster text-uppercase mb-3" style="color: #bfd254;">Kontak & Lokasi</h6>
                <p class="text-secondary small mb-2">
                    <i class="bi bi-geo-alt-fill text-warning me-2"></i> Posko Kebon Kopi Kerenceng, Cicalengka, Kab. Bandung
                </p>
                <p class="text-secondary small mb-2">
                    <i class="bi bi-envelope-fill text-warning me-2"></i> info@tanampohon.id
                </p>
                <p class="text-secondary small mb-3">
                    <i class="bi bi-whatsapp text-warning me-2"></i> 0812-3456-7890
                </p>
                <div class="p-2 rounded bg-secondary bg-opacity-25 border border-secondary border-opacity-50 small text-light-emphasis">
                    <i class="bi bi-shield-check text-warning me-1"></i>
                    <strong>Pembayaran Manual 100%:</strong> Transfer Bank / E-Wallet / QRIS resmi diverifikasi oleh tim keuangan tanpa payment gateway otomatis.
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4 opacity-25">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start text-secondary small">
                &copy; <?= date('Y') ?> <strong>Plant For Fun</strong> &bull; Kerenceng Coffee Expedition &bull; Presented by andu.
            </div>
            <div class="col-md-6 text-center text-md-end text-secondary small mt-2 mt-md-0 font-mono">
                PLANT TREES PRESERVE TOMORROW
            </div>
        </div>
    </div>
</footer>
