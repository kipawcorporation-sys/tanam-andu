<?php
/**
 * Partial: Public Navigation Bar
 * Tema: Botanical Outdoor & Agroforestry Expedition (Plant For Fun)
 */
$currentUser = auth();
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-2" style="border-bottom: 2px solid rgba(18, 56, 44, 0.08);">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center text-decoration-none py-1" href="<?= url('/') ?>" title="Plant For Fun &bull; Kerenceng Coffee Expedition">
            <img src="<?= asset('assets/images/logo-plant-for-fun.svg') ?>" alt="Plant For Fun" style="height: 48px; width: auto; object-fit: contain;">
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= $currentUri === '/' ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= $currentUri === '/' ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/') ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= str_starts_with($currentUri, '/donasi') ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= str_starts_with($currentUri, '/donasi') ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/donasi') ?>">Donasi Pohon</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= str_starts_with($currentUri, '/kegiatan') ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= str_starts_with($currentUri, '/kegiatan') ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/kegiatan') ?>">
                        Kegiatan Relawan
                        <span class="badge rounded-pill ms-1" style="background-color: #bfd254; color: #12382c; font-size: 10px;">Plant For Fun</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= str_starts_with($currentUri, '/toko') ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= str_starts_with($currentUri, '/toko') ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/toko') ?>">
                        Kopi & Toko
                        <span class="badge rounded-pill ms-1" style="background-color: #12382c; color: #ffffff; font-size: 10px;">andu ☕</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= str_starts_with($currentUri, '/cek-ongkir') ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= str_starts_with($currentUri, '/cek-ongkir') ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/cek-ongkir') ?>">
                        <i class="bi bi-truck me-1"></i>Cek Ongkir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= str_starts_with($currentUri, '/transparansi') ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= str_starts_with($currentUri, '/transparansi') ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/transparansi') ?>">Transparansi & Peta</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold px-3 <?= str_starts_with($currentUri, '/cek-pembayaran') ? 'active fw-bold' : 'text-secondary' ?>" 
                       style="<?= str_starts_with($currentUri, '/cek-pembayaran') ? 'color: #12382c !important;' : '' ?>" 
                       href="<?= url('/cek-pembayaran') ?>">Cek Status Tagihan</a>
                </li>
            </ul>

            <!-- Right User Navigation -->
            <div class="d-flex align-items-center gap-2">
                <?php if ($currentUser): ?>
                    <!-- Dropdown User Login -->
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle d-flex align-items-center gap-2 py-2 px-3 rounded-pill shadow-sm" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-circle text-white fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 13px; background-color: #12382c;">
                                <?= strtoupper(substr($currentUser['nama'] ?? 'U', 0, 1)) ?>
                            </span>
                            <span class="d-none d-md-inline fw-semibold"><?= htmlspecialchars(explode(' ', $currentUser['nama'])[0]) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="userMenuDropdown" style="min-width: 220px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($currentUser['nama']) ?></div>
                                <div class="small text-muted text-truncate"><?= htmlspecialchars($currentUser['email']) ?></div>
                                <span class="badge border mt-1 rounded-pill" style="background-color: #e6eee7; color: #12382c; border-color: #d6e4d7 !important;">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $currentUser['role'] ?? 'peserta'))) ?>
                                </span>
                            </li>
                            
                            <?php if (is_admin()): ?>
                                <li>
                                    <a class="dropdown-item py-2 text-danger fw-semibold" href="<?= url('/admin') ?>">
                                        <i class="bi bi-speedometer2 me-2"></i> Admin Panel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                            <?php endif; ?>

                            <li>
                                <a class="dropdown-item py-2" href="<?= url('/dashboard') ?>">
                                    <i class="bi bi-grid me-2 text-success"></i> Dashboard Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= url('/dashboard/donasi') ?>">
                                    <i class="bi bi-heart me-2 text-success"></i> Donasi Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= url('/dashboard/kegiatan') ?>">
                                    <i class="bi bi-calendar-check me-2 text-success"></i> Kegiatan Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= url('/dashboard/pesanan') ?>">
                                    <i class="bi bi-bag me-2 text-success"></i> Pesanan Kaos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= url('/dashboard/profil') ?>">
                                    <i class="bi bi-person me-2 text-success"></i> Pengaturan Akun
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="<?= url('/keluar') ?>" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Tombol Masuk & Daftar untuk Tamu -->
                    <a href="<?= url('/masuk') ?>" class="btn btn-outline-success fw-semibold px-4 rounded-pill">Masuk</a>
                    <a href="<?= url('/daftar') ?>" class="btn btn-success fw-semibold px-4 rounded-pill shadow-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
