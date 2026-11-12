<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Panel') ?> — Plant For Fun &bull; Kerenceng Coffee Expedition</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --admin-sidebar-w: 270px;
            --emerald-base: #12382c;
            --emerald-dark: #0a1f18;
            --emerald-darker: #061510;
            --emerald-light: #1b5341;
            --emerald-accent: #22c55e;
            --emerald-glow: rgba(34, 197, 94, 0.25);
            --sand-accent: #bfd254;
            --amber-accent: #f59e0b;
            --slate-bg: #f8faf8;
            --slate-border: rgba(18, 56, 44, 0.08);
            --card-shadow: 0 4px 20px -2px rgba(18, 56, 44, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            --card-hover-shadow: 0 12px 28px -4px rgba(18, 56, 44, 0.12), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
        }

        body { 
            background-color: var(--slate-bg); 
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            letter-spacing: -0.01em;
            overflow-x: hidden;
        }

        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .font-brand { font-weight: 800; letter-spacing: 0.04em; }

        /* ---- SIDEBAR STYLING ---- */
        #adminSidebar {
            position: fixed; top: 0; left: 0; height: 100vh;
            width: var(--admin-sidebar-w);
            background: linear-gradient(180deg, var(--emerald-dark) 0%, var(--emerald-darker) 100%);
            overflow-y: auto; z-index: 1050;
            transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            display: flex; flex-direction: column;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.1) transparent;
        }
        #adminSidebar::-webkit-scrollbar { width: 4px; }
        #adminSidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 4px; }

        #adminSidebar .brand {
            display: flex; align-items: center; gap: 12px;
            padding: 22px 20px 18px; 
            border-bottom: 1px solid rgba(255,255,255,.07);
            background: rgba(0, 0, 0, 0.15);
        }
        #adminSidebar .brand-icon-box {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, rgba(34,197,94,0.2) 0%, rgba(34,197,94,0.05) 100%);
            border: 1px solid rgba(34,197,94,0.3);
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        #adminSidebar .brand-text { 
            color: #fff; font-weight: 800; font-size: 1rem; line-height: 1.2; 
            letter-spacing: 0.5px;
        }
        #adminSidebar .brand-sub { 
            color: var(--sand-accent); font-size: 0.68rem; font-weight: 600; 
            letter-spacing: 1px; text-transform: uppercase;
        }

        #adminSidebar .nav-section {
            padding: 18px 20px 6px;
            color: rgba(255,255,255,.35);
            font-size: .65rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
        }
        #adminSidebar .admin-link {
            display: flex; align-items: center; gap: 12px;
            padding: 9px 20px; color: rgba(255,255,255,.7);
            text-decoration: none; font-size: .86rem; font-weight: 500;
            position: relative;
            transition: all .18s ease;
            margin: 1px 12px;
            border-radius: 10px;
        }
        #adminSidebar .admin-link:hover { 
            color: #fff; 
            background: rgba(255,255,255,.07);
            transform: translateX(3px);
        }
        #adminSidebar .admin-link.active {
            color: #fff; 
            background: linear-gradient(90deg, rgba(34, 197, 94, 0.22) 0%, rgba(34, 197, 94, 0.05) 100%);
            border: 1px solid rgba(34, 197, 94, 0.3);
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }
        #adminSidebar .admin-link.active::before {
            content: ''; position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; background: var(--emerald-accent);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px var(--emerald-accent);
        }
        #adminSidebar .admin-link .bi { 
            font-size: 1.05rem; min-width: 20px; 
            opacity: .8;
            transition: opacity .15s;
        }
        #adminSidebar .admin-link:hover .bi,
        #adminSidebar .admin-link.active .bi {
            opacity: 1; color: var(--emerald-accent);
        }
        #adminSidebar .badge-menu { 
            margin-left: auto; font-size: 10px; font-weight: 700;
            padding: 3px 7px;
        }

        /* Pulsing Dot Animation */
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background-color: var(--emerald-accent);
            display: inline-block;
            box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
            70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* User Profile in Sidebar Footer */
        .sidebar-user-footer {
            margin-top: auto;
            padding: 16px;
            background: rgba(0,0,0,0.25);
            border-top: 1px solid rgba(255,255,255,.07);
        }

        /* ---- MAIN CONTENT ---- */
        #adminMain {
            margin-left: var(--admin-sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin .25s ease;
        }

        /* Modern Glass Topbar */
        #adminTopbar {
            background: rgba(255, 255, 255, 0.88); 
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--slate-border);
            padding: 14px 28px; 
            display: flex; align-items: center; gap: 16px;
            position: sticky; top: 0; z-index: 100;
        }
        #adminTopbar .topbar-title { 
            font-weight: 800; font-size: 1.1rem; color: #0f172a;
            display: flex; align-items: center; gap: 8px;
        }

        .admin-content { 
            padding: 28px; 
            flex-grow: 1;
        }

        /* ---- COOL CARD STYLES ---- */
        .card {
            border: 1px solid var(--slate-border);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all .2s ease;
        }
        .card:hover {
            box-shadow: var(--card-hover-shadow);
        }

        /* Stat Card */
        .stat-card-pro {
            border-radius: 16px;
            border: 1px solid rgba(18,56,44,0.08);
            background: #fff;
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .stat-card-pro:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-hover-shadow);
        }
        .stat-card-pro .stat-glow {
            position: absolute; right: -20px; top: -20px;
            width: 100px; height: 100px; border-radius: 50%;
            filter: blur(40px); opacity: 0.15;
            pointer-events: none;
        }
        .stat-card-pro .stat-icon-wrap {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }
        .stat-card-pro .stat-val {
            font-size: 1.75rem; font-weight: 800;
            color: #0f172a; line-height: 1.15;
            font-variant-numeric: tabular-nums;
        }
        .stat-card-pro .stat-lbl {
            font-size: 0.82rem; font-weight: 600;
            color: #64748b; margin-top: 4px;
        }
        .stat-card-pro .stat-trend {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.72rem; font-weight: 700;
            padding: 2px 8px; border-radius: 6px;
        }

        /* ---- TABLE ENHANCEMENTS ---- */
        .admin-table th { 
            font-size: .72rem; text-transform: uppercase; 
            letter-spacing: .08em; font-weight: 700; color: #64748b;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .admin-table td { 
            vertical-align: middle; 
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .admin-table tr:hover td {
            background-color: #f8faf8;
        }

        /* ---- BADGE STATUS ---- */
        .st-menunggu_pembayaran { background: #fef3c7; color: #92400e; font-weight: 600; }
        .st-menunggu_verifikasi { background: #e0f2fe; color: #0369a1; font-weight: 600; }
        .st-berhasil { background: #dcfce7; color: #15803d; font-weight: 600; }
        .st-ditolak  { background: #fee2e2; color: #b91c1c; font-weight: 600; }
        .st-kedaluwarsa, .st-dibatalkan { background: #f1f5f9; color: #475569; font-weight: 600; }
        .st-diproses { background: #ede9fe; color: #6d28d9; font-weight: 600; }
        .st-dikirim  { background: #e0f2fe; color: #0284c7; font-weight: 600; }
        .st-selesai  { background: #dcfce7; color: #15803d; font-weight: 600; }

        /* Mobile Overlay */
        @media (max-width: 991px) {
            #adminSidebar { transform: translateX(calc(-1 * var(--admin-sidebar-w))); }
            #adminSidebar.show { transform: translateX(0); }
            #adminMain { margin-left: 0 !important; }
        }

        /* ============================================================
           CONTRAST FIX — Green-on-Green (WCAG AA)
           Bootstrap text-success=#198754 on bg-success-subtle=#d1e7dd
           only achieves ~2.9:1 contrast → FAILS WCAG AA (need 4.5:1).
           Fix: use dark forest green #0a3622 (contrast 10:1+ on #d1e7dd)
           ============================================================ */
        [class*="bg-success-subtle"][class*="text-success"],
        .badge.bg-success-subtle.text-success,
        span.bg-success-subtle.text-success,
        div.bg-success-subtle.text-success,
        .stat-trend.bg-success-subtle.text-success,
        .stat-icon.bg-success-subtle.text-success,
        .stat-pill-icon.bg-success-subtle.text-success,
        .metric-icon-wrap.bg-success-subtle.text-success,
        .avatar-circle.bg-success-subtle.text-success,
        .rounded-circle.bg-success-subtle.text-success {
            color: #0a3622 !important;
        }
        /* Partial-opacity green bg badges */
        .badge.bg-success.bg-opacity-15.text-success,
        .badge.bg-success.bg-opacity-10.text-success,
        .badge.bg-success.bg-opacity-25.text-success {
            color: #064e1e !important;
        }
        /* Stat trend pill – slightly lighter allowed since it's small UI chrome */
        .stat-trend.bg-success-subtle { color: #15803d !important; }
    </style>
    <?= $extraHead ?? '' ?>
</head>
<body>

<!-- Sidebar -->
<nav id="adminSidebar">
    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon-box">
            <img src="/assets/images/logo-emblem.svg" alt="Plant For Fun" style="width: 28px; height: 28px;">
        </div>
        <div>
            <div class="brand-text">Plant For Fun</div>
            <div class="brand-sub">Kerenceng &bull; 1.550 mdpl</div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="py-2 flex-grow-1">
        <?php $cu = $_SERVER['REQUEST_URI'] ?? ''; ?>

        <!-- RINGKASAN -->
        <div class="nav-section">Ringkasan Eksekutif</div>
        <a href="/admin" class="admin-link <?= $cu === '/admin' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <!-- PEMBAYARAN -->
        <div class="nav-section mt-2">Keuangan & Pembayaran</div>
        <a href="/admin/verifikasi-pembayaran"
           class="admin-link <?= str_starts_with($cu, '/admin/verifikasi-pembayaran') ? 'active' : '' ?>">
            <i class="bi bi-patch-check"></i> Verifikasi Transfer
            <?php if (!empty($pendingPaymentsCount) && $pendingPaymentsCount > 0): ?>
                <span class="badge bg-danger rounded-pill badge-menu shadow-sm">
                    <?= $pendingPaymentsCount ?> baru
                </span>
            <?php endif; ?>
        </a>
        <a href="/admin/rekening"
           class="admin-link <?= str_starts_with($cu, '/admin/rekening') ? 'active' : '' ?>">
            <i class="bi bi-bank"></i> Rekening Bank & QRIS
        </a>

        <!-- DATA & PENANAMAN -->
        <div class="nav-section mt-2">Konservasi & Penanaman</div>
        <a href="/admin/donasi"
           class="admin-link <?= str_starts_with($cu, '/admin/donasi') ? 'active' : '' ?>">
            <i class="bi bi-heart"></i> Donasi Pohon
        </a>
        <a href="/admin/kegiatan"
           class="admin-link <?= str_starts_with($cu, '/admin/kegiatan') ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i> Jadwal Ekspedisi
        </a>
        <a href="/admin/lokasi"
           class="admin-link <?= str_starts_with($cu, '/admin/lokasi') ? 'active' : '' ?>">
            <i class="bi bi-geo-alt"></i> Titik Lokasi & Peta
        </a>
        <a href="/admin/pohon"
           class="admin-link <?= str_starts_with($cu, '/admin/pohon') ? 'active' : '' ?>">
            <i class="bi bi-tree"></i> Pohon & Spesies
        </a>
        <a href="/admin/peserta"
           class="admin-link <?= str_starts_with($cu, '/admin/peserta') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Relawan & Peserta
        </a>
        <a href="/admin/checkin-qr"
           class="admin-link <?= str_starts_with($cu, '/admin/checkin-qr') ? 'active' : '' ?>">
            <i class="bi bi-qr-code-scan"></i> Pemindai Check-in QR
        </a>

        <!-- TOKO & KOPI SPESIALTI -->
        <div class="nav-section mt-2">Toko Kopi & Logistik</div>
        <a href="/admin/produk"
           class="admin-link <?= str_starts_with($cu, '/admin/produk') ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Katalog Produk & Kopi
        </a>
        <a href="/admin/pesanan"
           class="admin-link <?= str_starts_with($cu, '/admin/pesanan') ? 'active' : '' ?>">
            <i class="bi bi-bag-check"></i> Pesanan Pelanggan
        </a>
        <a href="/admin/ongkir"
           class="admin-link <?= str_starts_with($cu, '/admin/ongkir') ? 'active' : '' ?>">
            <i class="bi bi-truck"></i> Mesin Ongkir Mandiri
        </a>

        <!-- KONTEN -->
        <div class="nav-section mt-2">Komunikasi & Konten</div>
        <a href="/admin/postingan"
           class="admin-link <?= ($cu === '/admin/postingan' || str_starts_with($cu, '/admin/postingan/tambah') || preg_match('#^/admin/postingan/\d+/edit#', $cu)) ? 'active' : '' ?>">
            <i class="bi bi-newspaper"></i> Berita & Kabar Lapangan
        </a>
        <a href="/admin/postingan/komentar"
           class="admin-link <?= str_starts_with($cu, '/admin/postingan/komentar') ? 'active' : '' ?>">
            <i class="bi bi-chat-left-text"></i> Moderasi Komentar
        </a>
        <a href="/admin/faq"
           class="admin-link <?= str_starts_with($cu, '/admin/faq') ? 'active' : '' ?>">
            <i class="bi bi-question-circle"></i> Tanya Jawab (FAQ)
        </a>

        <!-- SERTIFIKAT DIGITAL -->
        <a href="/admin/sertifikat"
           class="admin-link <?= str_starts_with($cu, '/admin/sertifikat') ? 'active' : '' ?>">
            <i class="bi bi-patch-check"></i> Sertifikat Digital
        </a>

        <!-- SISTEM -->
        <div class="nav-section mt-2">Sistem & Konfigurasi</div>
        <a href="/admin/laporan"
           class="admin-link <?= str_starts_with($cu, '/admin/laporan') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Keuangan
        </a>
        <a href="/admin/pengaturan"
           class="admin-link <?= str_starts_with($cu, '/admin/pengaturan') ? 'active' : '' ?>">
            <i class="bi bi-gear"></i> Pengaturan & Cron Job
        </a>
    </div>

    <!-- User Mini Profile Footer -->
    <div class="sidebar-user-footer">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                 style="width: 36px; height: 36px; background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);">
                <?= strtoupper(substr(auth('name') ?? 'A', 0, 1)) ?>
            </div>
            <div class="overflow-hidden flex-grow-1">
                <div class="text-white fw-bold small text-truncate"><?= htmlspecialchars(auth('name') ?? 'Super Admin') ?></div>
                <div class="text-white-50" style="font-size: 11px;">Administrator</div>
            </div>
            <a href="/keluar" class="btn btn-sm btn-outline-danger p-1 rounded-circle" style="width: 30px; height: 30px;"
               onclick="return confirm('Keluar dari sesi Admin Panel?')" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="d-lg-none" style="display:none!important;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1040;"
     onclick="document.getElementById('adminSidebar').classList.remove('show');this.style.display='none'"></div>

<!-- Main Container -->
<div id="adminMain">
    
    <!-- Topbar -->
    <div id="adminTopbar">
        <button class="btn btn-sm btn-light border d-lg-none me-2" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>

        <!-- Current Section Title -->
        <div class="topbar-title">
            <span><?= htmlspecialchars($pageTitle ?? 'Admin Panel') ?></span>
        </div>

        <!-- Live Server Status Indicator -->
        <div class="d-none d-xl-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-subtle border border-success-subtle small font-mono ms-3" style="color:#0a3622; font-size: 11px;">
            <span class="pulse-dot"></span>
            <span>SYSTEM ONLINE &bull; KEBON KOPI KERENCENG (1.550 MDPL)</span>
        </div>

        <!-- Action Items (Right) -->
        <div class="ms-auto d-flex align-items-center gap-2">
            <!-- Direct Link to Public Site -->
            <a href="/" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-semibold text-secondary small d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-globe2 text-success"></i>
                <span>Lihat Situs Publik</span>
                <i class="bi bi-arrow-up-right small text-muted"></i>
            </a>

            <!-- Cek Ongkir Quick Tool -->
            <a href="/cek-ongkir" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 py-1 font-semibold text-secondary small d-none d-md-inline-flex align-items-center gap-1">
                <i class="bi bi-truck text-primary"></i>
                <span>Cek Ongkir</span>
            </a>

            <!-- Time Clock -->
            <div class="d-none d-md-flex align-items-center text-muted small font-mono px-2">
                <i class="bi bi-clock me-1 text-success"></i><?= date('H:i') ?> WIB
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown ms-2">
                <button class="btn btn-sm btn-light border rounded-pill d-flex align-items-center gap-2 py-1 px-2" data-bs-toggle="dropdown">
                    <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center"
                         style="width: 26px; height: 26px; font-size: 11px; background: #12382c;">
                        <?= strtoupper(substr(auth('name') ?? 'A', 0, 1)) ?>
                    </div>
                    <span class="d-none d-sm-inline small fw-semibold"><?= htmlspecialchars(auth('name') ?? 'Admin') ?></span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" style="min-width: 200px;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold small"><?= htmlspecialchars(auth('name') ?? 'Admin') ?></div>
                        <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars(auth('email') ?? 'admin@tanampohon.id') ?></div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 small" href="/admin/pengaturan">
                            <i class="bi bi-gear me-2 text-muted"></i>Pengaturan Sistem
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 small" href="/">
                            <i class="bi bi-arrow-left-circle me-2 text-muted"></i>Kembali ke Beranda
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 small text-danger" href="/keluar" onclick="return confirm('Keluar dari sesi Admin Panel?')">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar (Logout)
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages (Floating Alerts) -->
    <?php if (Session::has('success') || Session::has('error') || Session::has('warning')): ?>
        <div class="px-4 pt-3">
            <?php if (Session::has('success')): ?>
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div><?= Session::flash('success') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (Session::has('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-octagon-fill fs-5 text-danger"></i>
                    <div><?= Session::flash('error') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (Session::has('warning')): ?>
                <div class="alert alert-warning border-0 shadow-sm alert-dismissible fade show rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning-emphasis"></i>
                    <div><?= Session::flash('warning') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Main Dynamic Content -->
    <main class="admin-content">
        <?= $content ?>
    </main>

    <!-- Admin Footer -->
    <footer class="py-3 px-4 border-top bg-white d-flex flex-wrap justify-content-between align-items-center small text-muted">
        <div>
            &copy; <?= date('Y') ?> <strong>Plant For Fun &bull; Kerenceng Coffee Planting & Agroforestry Expedition</strong>.
        </div>
        <div class="d-flex align-items-center gap-3">
            <span>Kolaborasi: <strong>andu &bull; Karsa Kerentjeng &bull; Ethera</strong></span>
            <span>&bull;</span>
            <span class="font-mono">v2.5.0-production</span>
        </div>
    </footer>
</div>

<!-- Mobile Toggle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('adminSidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.getElementById('sidebarToggle');

if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
    });
}
</script>
</body>
</html>
