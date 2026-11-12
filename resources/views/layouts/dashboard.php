<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title><?= e($pageTitle ?? 'Dashboard Peserta - ' . config('app.name')) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
        }
        body {
            background-color: #f4f7f6;
        }
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.07);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .sidebar-nav {
            padding: 1rem 0.75rem;
            overflow-y: auto;
            flex-grow: 1;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            color: #495057;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 0.75rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            color: var(--primary);
            background-color: var(--primary-light);
        }
        .sidebar-link.active {
            color: #ffffff;
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.25);
        }
        .sidebar-link i {
            font-size: 1.15rem;
        }
        /* Main Content Container */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            padding-bottom: 70px; /* Space for mobile bottom nav */
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        /* Mobile Bottom Nav */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.1);
            z-index: 1030;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
        }
        .bottom-nav-item {
            flex: 1;
            text-align: center;
            padding: 0.5rem 0;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .bottom-nav-item i {
            font-size: 1.25rem;
        }
        .bottom-nav-item.active {
            color: var(--primary);
        }
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-bottom-nav {
                display: flex;
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }

        /* ============================================================
           CONTRAST FIX — Green-on-Green (WCAG AA)
           text-success (#198754) on bg-success-subtle (#d1e7dd) = ~2.9:1
           → FAILS WCAG AA. Fix: dark forest green #0a3622 (10:1+ ratio)
           ============================================================ */
        [class*="bg-success-subtle"][class*="text-success"],
        .badge.bg-success-subtle.text-success,
        span.bg-success-subtle.text-success,
        div.bg-success-subtle.text-success,
        .stat-icon.bg-success-subtle.text-success,
        .avatar-circle.bg-success-subtle.text-success,
        .rounded-circle.bg-success-subtle.text-success {
            color: #0a3622 !important;
        }
        .badge.bg-success.bg-opacity-15.text-success,
        .badge.bg-success.bg-opacity-10.text-success,
        .badge.bg-success.bg-opacity-25.text-success {
            color: #064e1e !important;
        }
    </style>
</head>
<body>

    <?php 
        $user = auth(); 
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/dashboard';
    ?>

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <div class="dashboard-wrapper">
        <!-- Desktop Sidebar -->
        <aside class="sidebar" id="dashboardSidebar">
            <!-- Brand Logo -->
            <div class="sidebar-brand d-flex align-items-center justify-content-between">
                <a href="<?= url('/dashboard') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                    <span class="fs-3">🌱</span>
                    <span class="fs-5 fw-bold text-success">Tanam<span class="text-dark">Pohon</span></span>
                </a>
                <button type="button" class="btn btn-sm btn-light d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- User Compact Info -->
            <div class="px-3 py-3 border-bottom bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-circle bg-success text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 18px;">
                        <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="fw-bold text-dark text-truncate mb-0"><?= e($user['nama'] ?? 'Peserta') ?></h6>
                        <span class="badge bg-success-subtle border border-success-subtle rounded-pill small" style="color:#0a3622; font-size: 11px;">
                            <?= e(ucfirst(str_replace('_', ' ', $user['role'] ?? 'Peserta'))) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="sidebar-nav">
                <a href="<?= url('/dashboard') ?>" class="sidebar-link <?= $currentUri === '/dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill"></i> Ringkasan
                </a>
                <a href="<?= url('/dashboard/donasi') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/donasi') ? 'active' : '' ?>">
                    <i class="bi bi-heart-fill"></i> Donasi Saya
                </a>
                <a href="<?= url('/dashboard/pohon') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/pohon') ? 'active' : '' ?>">
                    <i class="bi bi-tree-fill"></i> Pohon Saya
                </a>
                <a href="<?= url('/dashboard/kegiatan') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/kegiatan') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event-fill"></i> Kegiatan Saya
                </a>
                <a href="<?= url('/dashboard/pesanan') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/pesanan') ? 'active' : '' ?>">
                    <i class="bi bi-bag-check-fill"></i> Pesanan Kaos
                </a>
                <a href="<?= url('/dashboard/sertifikat') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/sertifikat') ? 'active' : '' ?>">
                    <i class="bi bi-award-fill"></i> Sertifikat & Lencana
                </a>
                <a href="<?= url('/dashboard/referral') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/referral') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i> Ajak Teman
                </a>
                <a href="<?= url('/dashboard/notifikasi') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/notifikasi') ? 'active' : '' ?>">
                    <i class="bi bi-bell-fill"></i> Notifikasi
                </a>
                <a href="<?= url('/dashboard/profil') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/profil') ? 'active' : '' ?>">
                    <i class="bi bi-person-gear"></i> Pengaturan Profil
                </a>
                <a href="<?= url('/dashboard/bantuan') ?>" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard/bantuan') ? 'active' : '' ?>">
                    <i class="bi bi-chat-dots-fill"></i> Bantuan & Tiket
                </a>

                <?php if (is_admin()): ?>
                    <hr class="my-2">
                    <a href="<?= url('/admin') ?>" class="sidebar-link text-danger">
                        <i class="bi bi-speedometer2"></i> Masuk Admin Panel
                    </a>
                <?php endif; ?>
            </div>

            <!-- Logout Link Bottom -->
            <div class="p-3 border-top">
                <form action="<?= url('/keluar') ?>" method="POST" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-semibold py-2">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar Akun
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Topbar Header -->
            <header class="topbar d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light d-lg-none" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <h5 class="fw-bold mb-0 text-dark d-none d-sm-block">Area Peserta Plant For Fun</h5>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?= url('/donasi') ?>" class="btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Donasi Lagi
                    </a>
                    <a href="<?= url('/toko') ?>" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 d-none d-md-inline-block">
                        <i class="bi bi-bag me-1"></i> Beli Kaos
                    </a>

                    <!-- Notifikasi Icon -->
                    <a href="<?= url('/dashboard/notifikasi') ?>" class="btn btn-sm btn-light rounded-circle position-relative p-2" title="Notifikasi" style="width: 38px; height: 38px;">
                        <i class="bi bi-bell text-secondary fs-6"></i>
                    </a>

                    <!-- User Menu Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill p-1 pe-3 d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                            <span class="avatar-circle bg-success text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 13px;">
                                <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                            </span>
                            <span class="small fw-semibold text-dark d-none d-sm-inline"><?= htmlspecialchars(explode(' ', $user['nama'] ?? 'Peserta')[0]) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                            <li><a class="dropdown-item py-2" href="<?= url('/dashboard/profil') ?>"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item py-2" href="<?= url('/dashboard/donasi') ?>"><i class="bi bi-heart me-2"></i> Riwayat Donasi</a></li>
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
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="container-fluid px-4 pt-3">
                <?php require VIEW_PATH . '/partials/flash-messages.php'; ?>
            </div>

            <!-- Page Body Content -->
            <main class="container-fluid px-4 py-3 flex-grow-1">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="mobile-bottom-nav">
        <a href="<?= url('/dashboard') ?>" class="bottom-nav-item <?= $currentUri === '/dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="<?= url('/dashboard/donasi') ?>" class="bottom-nav-item <?= str_starts_with($currentUri, '/dashboard/donasi') ? 'active' : '' ?>">
            <i class="bi bi-heart-fill"></i>
            <span>Donasi</span>
        </a>
        <a href="<?= url('/dashboard/kegiatan') ?>" class="bottom-nav-item <?= str_starts_with($currentUri, '/dashboard/kegiatan') ? 'active' : '' ?>">
            <i class="bi bi-calendar-event-fill"></i>
            <span>Relawan</span>
        </a>
        <a href="<?= url('/dashboard/pesanan') ?>" class="bottom-nav-item <?= str_starts_with($currentUri, '/dashboard/pesanan') ? 'active' : '' ?>">
            <i class="bi bi-bag-check-fill"></i>
            <span>Kaos</span>
        </a>
        <a href="<?= url('/dashboard/profil') ?>" class="bottom-nav-item <?= str_starts_with($currentUri, '/dashboard/profil') ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i>
            <span>Profil</span>
        </a>
    </nav>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('dashboardSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
    }
    </script>
</body>
</html>

