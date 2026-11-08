<?php
/**
 * View: admin/payments.php — Plant For Fun
 * Antrian & Sistem Verifikasi Pembayaran Manual Transfer Bank Profesional.
 */
$statusLabels = [
    'menunggu_pembayaran'  => ['Menunggu Bayar',   'status-warning',  'bi-clock-history'],
    'menunggu_verifikasi'  => ['Menunggu Verif.',  'status-info',     'bi-hourglass-split'],
    'berhasil'             => ['Berhasil / Lunas', 'status-success',  'bi-check-circle-fill'],
    'ditolak'              => ['Ditolak',          'status-danger',   'bi-x-circle-fill'],
    'kedaluwarsa'          => ['Kedaluwarsa',      'status-muted',    'bi-slash-circle'],
    'dibatalkan'           => ['Dibatalkan',       'status-muted',    'bi-dash-circle'],
];
$statusOptions = [
    ''                     => 'Semua Status Pembayaran',
    'menunggu_verifikasi'  => 'Menunggu Verifikasi (Antrian)',
    'berhasil'             => 'Berhasil / Disetujui',
    'ditolak'              => 'Ditolak',
    'menunggu_pembayaran'  => 'Menunggu Pembayaran',
    'kedaluwarsa'          => 'Kedaluwarsa',
    'dibatalkan'           => 'Dibatalkan',
];
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 26px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-40px; width:220px; height:220px; background:radial-gradient(circle, rgba(34,197,94,.15) 0%, transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.35rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }
.hero-badge-queue { background:rgba(239,68,68,.2); border:1px solid rgba(239,68,68,.4); color:#fca5a5; padding:6px 16px; border-radius:50px; font-size:.8rem; font-weight:700; display:inline-flex; align-items:center; gap:8px; }
.hero-badge-safe { background:rgba(34,197,94,.15); border:1px solid rgba(34,197,94,.3); color:#86efac; padding:6px 16px; border-radius:50px; font-size:.8rem; font-weight:700; display:inline-flex; align-items:center; gap:8px; }

/* 4 Mini Stat Boxes */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 22px; }
.stat-card-verif { background:#fff; border-radius:14px; padding:16px 20px; border:1px solid rgba(0,0,0,.06); box-shadow:0 2px 8px rgba(0,0,0,.04); display:flex; align-items:center; gap:14px; }
.stat-card-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
.stat-card-icon.amber { background:#fef3c7; color:#d97706; }
.stat-card-icon.green { background:#dcfce7; color:#16a34a; }
.stat-card-icon.blue { background:#dbeafe; color:#2563eb; }
.stat-card-icon.red { background:#fee2e2; color:#dc2626; }
.stat-val { font-size:1.25rem; font-weight:800; color:#0f172a; line-height:1.1; font-family:'JetBrains Mono',monospace; }
.stat-lbl { font-size:.76rem; color:#64748b; font-weight:600; margin-top:2px; }

.filter-bar { background:#fff; border-radius:14px; padding:16px 20px; margin-bottom:20px; border:1px solid rgba(0,0,0,.06); box-shadow:0 1px 4px rgba(0,0,0,.04); display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.batch-bar { background:linear-gradient(135deg, #1e293b, #0f172a); color:#fff; border-radius:12px; padding:12px 20px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }

.pf-card { background:#fff; border-radius:16px; border:1px solid rgba(0,0,0,.07); box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
.pf-card-header { background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding:16px 24px; border-bottom:1px solid rgba(0,0,0,.06); display:flex; align-items:center; justify-content:space-between; }
.pf-card-header h6 { font-weight:700; color:#0f172a; margin:0; font-size:.9rem; }
table.pf-table { width:100%; border-collapse:collapse; }
table.pf-table thead th { background:#f8fafc; padding:12px 14px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
table.pf-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
table.pf-table tbody tr:hover { background:#f8fafc; }
table.pf-table tbody tr.dup-row { background:#fef9c3; }
table.pf-table tbody tr.dup-row:hover { background:#fef3c7; }
table.pf-table td { padding:14px; vertical-align:middle; font-size:.84rem; color:#374151; }

.invoice-code { font-family:'JetBrains Mono',monospace; font-size:.78rem; color:#1e40af; background:#eff6ff; padding:3px 8px; border-radius:6px; display:inline-block; font-weight:700; }
.type-badge-donation { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }
.type-badge-order { background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }
.type-badge-participant { background:#ede9fe; color:#5b21b6; border:1px solid #ddd6fe; font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:50px; }

.status-pill { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:.72rem; font-weight:700; white-space:nowrap; }
.status-success { background:#dcfce7; color:#15803d; }
.status-warning { background:#fef9c3; color:#a16207; }
.status-info    { background:#dbeafe; color:#1d4ed8; }
.status-danger  { background:#fee2e2; color:#b91c1c; }
.status-muted   { background:#f1f5f9; color:#64748b; }

.proof-thumb-box { width:52px; height:52px; border-radius:10px; overflow:hidden; border:2px solid #e2e8f0; position:relative; cursor:pointer; flex-shrink:0; background:#f8fafc; display:flex; align-items:center; justify-content:center; transition:.2s; }
.proof-thumb-box:hover { border-color:#22c55e; transform:scale(1.05); }
.proof-thumb-box img { width:100%; height:100%; object-fit:cover; }

.btn-inspect { background:linear-gradient(135deg, #0ea5e9, #0284c7); color:#fff; border:none; padding:6px 12px; border-radius:8px; font-size:.76rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px; text-decoration:none; }
.btn-inspect:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(14,165,233,.3); color:#fff; }
.btn-quick-approve { background:linear-gradient(135deg, #22c55e, #16a34a); color:#fff; border:none; padding:6px 12px; border-radius:8px; font-size:.76rem; font-weight:700; cursor:pointer; transition:.15s; }
.btn-quick-approve:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(34,197,94,.3); color:#fff; }
.btn-quick-reject { background:#fff; color:#ef4444; border:1px solid #fca5a5; padding:6px 10px; border-radius:8px; font-size:.76rem; font-weight:700; cursor:pointer; transition:.15s; }
.btn-quick-reject:hover { background:#fee2e2; border-color:#ef4444; }
.btn-wa { background:#25d366; color:#fff; border:none; padding:4px 8px; border-radius:6px; font-size:.72rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:3px; }
.btn-wa:hover { background:#128c7e; color:#fff; }

.match-badge-yes { background:#dcfce7; color:#15803d; font-size:.68rem; font-weight:800; padding:2px 7px; border-radius:4px; font-family:'JetBrains Mono',monospace; }
.match-badge-no { background:#fef3c7; color:#b45309; font-size:.68rem; font-weight:800; padding:2px 7px; border-radius:4px; font-family:'JetBrains Mono',monospace; }
.dup-badge { background:#ef4444; color:#fff; font-size:.65rem; font-weight:700; padding:2px 7px; border-radius:50px; }

.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state i { font-size:3rem; display:block; margin-bottom:12px; color:#cbd5e1; }
.pf-pagination { display:flex; gap:4px; justify-content:center; padding:16px 24px; }
.pf-pagination a { width:34px; height:34px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:.82rem; font-weight:600; text-decoration:none; border:1px solid #e2e8f0; color:#374151; }
.pf-pagination a:hover { background:#f1f5f9; }
.pf-pagination .active-page { background:#22c55e; color:#fff; border-color:#22c55e; }

/* Lightbox Modal */
.inspector-img-container { background:#0f172a; border-radius:12px; overflow:hidden; position:relative; min-height:420px; display:flex; align-items:center; justify-content:center; }
.inspector-img-container img { max-width:100%; max-height:480px; object-fit:contain; transition:transform 0.2s ease; }
.inspector-toolbar { position:absolute; bottom:12px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,0.75); backdrop-filter:blur(8px); border-radius:50px; padding:6px 16px; display:flex; gap:8px; z-index:10; }
.inspector-tool-btn { background:transparent; border:none; color:#fff; font-size:.9rem; cursor:pointer; padding:4px 8px; border-radius:4px; }
.inspector-tool-btn:hover { background:rgba(255,255,255,0.2); }
</style>

<!-- Page Hero -->
<div class="page-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-shield-check me-2"></i>Verifikasi Pembayaran Transfer Bank</h4>
            <p>Antrian manual transfer rekening bank, inspeksi bukti transfer, deteksi anti-fraud, dan aktivasi pesanan/donasi.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <?php if ($stats['pending_count'] > 0): ?>
                <span class="hero-badge-queue">
                    <span class="pulse-dot" style="width:8px;height:8px;border-radius:50%;background:#ef4444;animation:pulse 1.5s infinite;display:inline-block;"></span>
                    <?= $stats['pending_count'] ?> Menunggu Verifikasi
                </span>
            <?php else: ?>
                <span class="hero-badge-safe">
                    <i class="bi bi-check-circle-fill"></i> Semua Antrian Bersih
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 4 Mini Metrik Cards -->
<div class="stat-grid">
    <div class="stat-card-verif">
        <div class="stat-card-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-val"><?= number_format($stats['pending_count']) ?></div>
            <div class="stat-lbl">Antrian Menunggu Verif</div>
        </div>
    </div>
    <div class="stat-card-verif">
        <div class="stat-card-icon green"><i class="bi bi-wallet2"></i></div>
        <div>
            <div class="stat-val" style="font-size:1.15rem;">Rp <?= number_format($stats['pending_amount'], 0, ',', '.') ?></div>
            <div class="stat-lbl">Nominal Belum Terverifikasi</div>
        </div>
    </div>
    <div class="stat-card-verif">
        <div class="stat-card-icon blue"><i class="bi bi-patch-check-fill"></i></div>
        <div>
            <div class="stat-val"><?= number_format($stats['today_approved_count']) ?> <span style="font-size:0.75rem;font-weight:600;color:#64748b;">(Rp <?= number_format($stats['today_approved_amount'], 0, ',', '.') ?>)</span></div>
            <div class="stat-lbl">Disetujui Hari Ini</div>
        </div>
    </div>
    <div class="stat-card-verif">
        <div class="stat-card-icon red"><i class="bi bi-shield-exclamation"></i></div>
        <div>
            <div class="stat-val"><?= number_format($stats['duplicate_count']) ?></div>
            <div class="stat-lbl">Peringatan Hash Duplikat</div>
        </div>
    </div>
</div>

<!-- Filter & Pencarian Bar -->
<div class="filter-bar">
    <i class="bi bi-funnel text-muted"></i>
    <span class="text-muted small fw-semibold">Pencarian &amp; Filter:</span>
    <form method="GET" class="d-flex gap-2 mb-0 flex-wrap align-items-center flex-grow-1">
        <div class="input-group input-group-sm" style="max-width: 250px;">
            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" class="form-control" placeholder="No Invoice, Donatur, Pengirim..."
                   value="<?= htmlspecialchars($keyword ?? '') ?>">
        </div>

        <select name="status" class="form-select form-select-sm" style="min-width: 190px;" onchange="this.form.submit()">
            <?php foreach ($statusOptions as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= $statusFilter === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>

        <select name="type" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
            <option value="">Semua Tipe</option>
            <option value="donation" <?= ($typeFilter ?? '') === 'donation' ? 'selected' : '' ?>>Donasi Pohon</option>
            <option value="order" <?= ($typeFilter ?? '') === 'order' ? 'selected' : '' ?>>Pesanan Kopi</option>
            <option value="participant" <?= ($typeFilter ?? '') === 'participant' ? 'selected' : '' ?>>Tiket Event</option>
        </select>

        <select name="bank" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
            <option value="">Semua Bank</option>
            <?php foreach ($banks as $b): ?>
                <option value="<?= htmlspecialchars($b) ?>" <?= ($bankFilter ?? '') === $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-sm btn-dark px-3">Terapkan</button>
        <?php if (!empty($keyword) || !empty($typeFilter) || !empty($bankFilter) || $statusFilter !== 'menunggu_verifikasi'): ?>
            <a href="/admin/verifikasi-pembayaran" class="btn btn-sm btn-outline-secondary" title="Reset filter">
                <i class="bi bi-x-circle me-1"></i>Reset
            </a>
        <?php endif; ?>
    </form>
    <div class="ms-auto text-muted small fw-semibold"><?= number_format($total) ?> pembayaran ditemukan</div>
</div>

<!-- Form Batch Approve -->
<form id="formBatchApprove" action="/admin/verifikasi-pembayaran/batch-setujui" method="POST">
    <?= csrf_field() ?>

    <!-- Batch Actions Floating Bar (Muncul bila ada checkbox dipilih) -->
    <div class="batch-bar" id="batchActionBar" style="display: none;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check2-square text-success fs-5"></i>
            <span class="fw-bold" id="selectedCountText">0 pembayaran dipilih</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-light" onclick="deselectAll()">Batal Pilih</button>
            <button type="submit" class="btn btn-sm btn-success fw-bold" onclick="return confirm('Setujui seluruh pembayaran yang dipilih secara masal?')">
                <i class="bi bi-check-all me-1"></i>Setujui Masal Terpilih
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="pf-card">
        <div class="pf-card-header">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" class="form-check-input" id="checkAll" title="Pilih Semua di Halaman Ini">
                <label for="checkAll" class="fw-bold small mb-0 cursor-pointer">Pilih Semua</label>
            </div>
            <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
        </div>

        <div class="table-responsive">
            <table class="pf-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Invoice &amp; Tipe</th>
                        <th>Pembayar / Donatur</th>
                        <th>Tagihan Sistem</th>
                        <th>Bukti Transfer &amp; Rekening</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th style="width: 170px;">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <div class="fw-semibold mb-1">Tidak ada transaksi dalam antrian ini</div>
                                <small>Semua pembayaran telah diproses atau tidak sesuai filter yang dipilih.</small>
                            </div>
                        </td></tr>
                    <?php endif; ?>

                    <?php foreach ($payments as $p):
                        $stLabel = $statusLabels[$p['status']] ?? [$p['status'], 'status-muted', 'bi-dash'];
                        $isDup   = !empty($p['file_hash']) && !empty($duplicateHashes[$p['file_hash']]);
                        
                        // WhatsApp Text Template
                        $waText = "Halo Kak " . ($p['payer_name'] ?? 'Donatur') . ", kami dari tim *Plant For Fun* mengonfirmasi transaksi invoice *" . $p['invoice_no'] . "* senilai *Rp " . number_format($p['grand_total'], 0, ',', '.') . "*.";
                        $waUrl = !empty($p['payer_phone']) ? "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $p['payer_phone']) . "&text=" . urlencode($waText) : null;
                    ?>
                        <tr class="<?= $isDup ? 'dup-row' : '' ?>">
                            <td>
                                <?php if ($p['status'] === 'menunggu_verifikasi'): ?>
                                    <input type="checkbox" name="payment_ids[]" value="<?= $p['id'] ?>" class="form-check-input payment-checkbox" onchange="updateBatchBar()">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span class="invoice-code"><?= htmlspecialchars($p['invoice_no']) ?></span>
                                    <?php if ($isDup): ?>
                                        <span class="dup-badge" title="Hash bukti sama dengan invoice lain!"><i class="bi bi-shield-exclamation"></i> DUP</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if ($p['payable_type'] === 'donation'): ?>
                                        <span class="type-badge-donation"><i class="bi bi-heart-fill me-1"></i>Donasi Pohon</span>
                                    <?php elseif ($p['payable_type'] === 'order'): ?>
                                        <span class="type-badge-order"><i class="bi bi-cup-hot-fill me-1"></i>Pesanan Kopi</span>
                                    <?php else: ?>
                                        <span class="type-badge-participant"><i class="bi bi-calendar-check-fill me-1"></i>Tiket Event</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['payer_name']) ?></div>
                                <div class="text-muted small" style="font-size:0.75rem;"><?= htmlspecialchars($p['payer_email']) ?></div>
                                <?php if (!empty($p['payer_phone'])): ?>
                                    <div class="mt-1">
                                        <a href="<?= $waUrl ?>" target="_blank" class="btn-wa">
                                            <i class="bi bi-whatsapp"></i> <?= htmlspecialchars($p['payer_phone']) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold font-mono text-dark" style="font-size:0.92rem;">
                                    Rp <?= number_format($p['grand_total'], 0, ',', '.') ?>
                                </div>
                                <?php if (!empty($p['unique_code'])): ?>
                                    <span class="text-muted" style="font-size:0.72rem; font-family:'JetBrains Mono',monospace;">
                                        Kode Unik: +<?= $p['unique_code'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['proof_file'])): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="proof-thumb-box" onclick="openInspector(<?= htmlspecialchars(json_encode($p)) ?>)" title="Klik untuk inspeksi gambar & perbesar">
                                            <img src="<?= htmlspecialchars($p['proof_url']) ?>" alt="Bukti Transfer">
                                        </div>
                                        <div class="small">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($p['sender_name'] ?? '—') ?></div>
                                            <div class="text-muted" style="font-size:0.74rem;"><?= htmlspecialchars($p['sender_bank'] ?? 'Bank') ?> &bull; Rp <?= number_format($p['amount_transferred'] ?? 0, 0, ',', '.') ?></div>
                                            <div class="mt-1">
                                                <?php if ($p['is_match']): ?>
                                                    <span class="match-badge-yes"><i class="bi bi-check2"></i> MATCH 100%</span>
                                                <?php else: ?>
                                                    <span class="match-badge-no" title="Selisih nominal">
                                                        <i class="bi bi-exclamation-triangle"></i> SELISIH <?= $p['nominal_diff'] > 0 ? '+' : '' ?>Rp <?= number_format($p['nominal_diff'], 0, ',', '.') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic"><i class="bi bi-dash-circle me-1"></i>Belum unggah bukti</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-pill <?= $stLabel[1] ?>">
                                    <i class="bi <?= $stLabel[2] ?>"></i><?= $stLabel[0] ?>
                                </span>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= date('d M Y', strtotime($p['updated_at'] ?? $p['created_at'])) ?></div>
                                <div class="text-muted" style="font-size:0.72rem;"><?= time_ago($p['updated_at'] ?? $p['created_at']) ?></div>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <?php if (!empty($p['proof_file'])): ?>
                                        <button type="button" class="btn-inspect" onclick="openInspector(<?= htmlspecialchars(json_encode($p)) ?>)" title="Inspeksi Bukti">
                                            <i class="bi bi-search"></i> Cek
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($p['status'] === 'menunggu_verifikasi'): ?>
                                        <form action="/admin/verifikasi-pembayaran/<?= $p['id'] ?>/setujui" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-quick-approve"
                                                    onclick="return confirm('Setujui transaksi #<?= htmlspecialchars($p['invoice_no']) ?>?')"
                                                    title="Setujui Pembayaran">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="btn-quick-reject btn-reject-modal"
                                                data-id="<?= $p['id'] ?>"
                                                data-invoice="<?= htmlspecialchars($p['invoice_no']) ?>"
                                                title="Tolak Pembayaran">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php endif; ?>

                                    <a href="/admin/verifikasi-pembayaran/<?= $p['id'] ?>" class="btn btn-sm btn-light border py-1 px-2" title="Detail Lengkap">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pf-pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>&type=<?= urlencode($typeFilter) ?>&bank=<?= urlencode($bankFilter) ?>&q=<?= urlencode($keyword) ?>"
                       class="<?= $i === $page ? 'active-page' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</form>

<!-- Modal Inspektor Bukti Transfer Interaktif (Lightbox & Komparasi) -->
<div class="modal fade" id="modalProofInspector" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header bg-dark text-white p-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success font-mono" id="inspInvoiceBadge">INV</span>
                    <h6 class="modal-title fw-bold mb-0 text-white">Inspektor Bukti Transfer Manual</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <!-- Sisi Kiri: Gambar Bukti Transfer dengan Kontrol Zoom & Putar -->
                    <div class="col-lg-7">
                        <div class="inspector-img-container" id="imgBox">
                            <img src="" id="inspImage" alt="Bukti Transfer">
                            <div class="inspector-toolbar">
                                <button type="button" class="inspector-tool-btn" onclick="zoomIn()" title="Perbesar"><i class="bi bi-zoom-in"></i></button>
                                <button type="button" class="inspector-tool-btn" onclick="zoomOut()" title="Perkecil"><i class="bi bi-zoom-out"></i></button>
                                <button type="button" class="inspector-tool-btn" onclick="rotateImg()" title="Putar 90 Derajat"><i class="bi bi-arrow-clockwise"></i></button>
                                <button type="button" class="inspector-tool-btn" onclick="resetImg()" title="Reset Tampilan"><i class="bi bi-arrows-fullscreen"></i></button>
                                <a href="" id="inspDownloadLink" target="_blank" class="inspector-tool-btn" title="Buka Gambar Asli"><i class="bi bi-box-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Panel Komparasi Nominal & Aksi Cepat -->
                    <div class="col-lg-5 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Komparasi Nominal -->
                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                                <span class="small fw-bold text-muted text-uppercase font-mono mb-2 d-block">Komparasi Nominal Transfer:</span>
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="p-2 rounded-3 bg-light border">
                                            <div class="text-muted small" style="font-size:0.7rem;">Tagihan Sistem</div>
                                            <div class="fw-bold font-mono text-dark" id="inspGrandTotal">Rp 0</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 rounded-3 bg-light border">
                                            <div class="text-muted small" style="font-size:0.7rem;">Klaim Bukti</div>
                                            <div class="fw-bold font-mono text-success" id="inspAmountTransferred">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 text-center" id="inspMatchStatus">
                                    <!-- Diisi via JS -->
                                </div>
                            </div>

                            <!-- Rincian Rekening Pengirim -->
                            <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                                <span class="small fw-bold text-muted text-uppercase font-mono mb-2 d-block">Rincian Pengirim:</span>
                                <table class="table table-sm table-borderless small mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 110px;">Nama Pengirim:</td>
                                        <td class="fw-bold text-dark" id="inspSenderName">—</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Bank Pengirim:</td>
                                        <td class="fw-bold text-dark" id="inspSenderBank">—</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Waktu Transfer:</td>
                                        <td class="text-dark" id="inspTransferTime">—</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Akun:</td>
                                        <td class="text-dark" id="inspPayerName">—</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Catatan Bukti:</td>
                                        <td class="text-secondary fst-italic" id="inspProofNote">—</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Tombol WhatsApp Hubungi Pembeli -->
                            <div class="mb-3" id="inspWaContainer" style="display: none;">
                                <a href="" id="inspWaLink" target="_blank" class="btn btn-outline-success btn-sm w-100 rounded-pill py-2 fw-semibold">
                                    <i class="bi bi-whatsapp me-1"></i>Konfirmasi via WhatsApp ke Pembeli
                                </a>
                            </div>
                        </div>

                        <!-- Tombol Aksi di Modal Inspector -->
                        <div class="pt-3 border-top" id="inspActionButtons">
                            <div class="d-flex gap-2">
                                <form id="inspApproveForm" method="POST" class="flex-grow-1">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success fw-bold w-100 rounded-pill py-2" onclick="return confirm('Setujui pembayaran ini sekarang?')">
                                        <i class="bi bi-check-circle-fill me-1"></i>Setujui Pembayaran
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger fw-bold rounded-pill px-3 py-2" id="inspRejectBtn">
                                    <i class="bi bi-x-circle me-1"></i>Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak Pembayaran -->
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formTolak" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pembayaran</h5>
                        <small class="text-muted">Invoice: <strong id="tolakInvoice"></strong></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <label class="form-label fw-bold small text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="4" required minlength="10"
                              placeholder="Misal: Bukti transfer tidak terbaca / terpotong, nominal yang ditransfer kurang Rp 500, rekening tidak ditemukan..."
                              style="border-radius:10px;"></textarea>
                    <div class="form-text small mt-1">Alasan penolakan akan dikirimkan dan dapat dibaca oleh pembeli di dashboard mereka. Minimal 10 karakter.</div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i>Tolak Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Select All & Batch Bar
const checkAll = document.getElementById('checkAll');
const checkboxes = document.querySelectorAll('.payment-checkbox');
const batchBar = document.getElementById('batchActionBar');
const selectedCountText = document.getElementById('selectedCountText');

checkAll.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = checkAll.checked);
    updateBatchBar();
});

function updateBatchBar() {
    const selected = document.querySelectorAll('.payment-checkbox:checked');
    if (selected.length > 0) {
        batchBar.style.display = 'flex';
        selectedCountText.textContent = selected.length + ' pembayaran dipilih';
    } else {
        batchBar.style.display = 'none';
        checkAll.checked = false;
    }
}

function deselectAll() {
    checkboxes.forEach(cb => cb.checked = false);
    checkAll.checked = false;
    updateBatchBar();
}

// Lightbox Inspector Controller
let currentScale = 1;
let currentRotation = 0;
const inspImg = document.getElementById('inspImage');

function openInspector(data) {
    resetImg();
    document.getElementById('inspInvoiceBadge').textContent = data.invoice_no;
    inspImg.src = data.proof_url;
    document.getElementById('inspDownloadLink').href = data.proof_url;

    document.getElementById('inspGrandTotal').textContent = 'Rp ' + Number(data.grand_total).toLocaleString('id-ID');
    document.getElementById('inspAmountTransferred').textContent = 'Rp ' + Number(data.amount_transferred || 0).toLocaleString('id-ID');

    const matchBox = document.getElementById('inspMatchStatus');
    if (data.is_match) {
        matchBox.innerHTML = '<span class="badge bg-success text-white px-3 py-2 rounded-pill font-mono"><i class="bi bi-check-circle-fill me-1"></i>NOMINAL SESUAI 100% (MATCH)</span>';
    } else {
        const diffText = (data.nominal_diff > 0 ? '+' : '') + 'Rp ' + Number(data.nominal_diff).toLocaleString('id-ID');
        matchBox.innerHTML = `<span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-mono"><i class="bi bi-exclamation-triangle-fill me-1"></i>SELISIH NOMINAL: ${diffText}</span>`;
    }

    document.getElementById('inspSenderName').textContent = data.sender_name || '—';
    document.getElementById('inspSenderBank').textContent = data.sender_bank || '—';
    document.getElementById('inspTransferTime').textContent = (data.transfer_date || '—') + ' ' + (data.transfer_time || '');
    document.getElementById('inspPayerName').textContent = data.payer_name + (data.payer_email ? ' (' + data.payer_email + ')' : '');
    document.getElementById('inspProofNote').textContent = data.proof_note || 'Tidak ada catatan tambahan';

    // WhatsApp Container
    const waBox = document.getElementById('inspWaContainer');
    const waLink = document.getElementById('inspWaLink');
    if (data.payer_phone) {
        const phone = data.payer_phone.replace(/[^0-9]/g, '');
        const text = encodeURIComponent(`Halo Kak ${data.payer_name}, kami dari tim Plant For Fun mengonfirmasi pembayaran invoice ${data.invoice_no} sebesar Rp ${Number(data.grand_total).toLocaleString('id-ID')}.`);
        waLink.href = `https://api.whatsapp.com/send?phone=${phone}&text=${text}`;
        waBox.style.display = 'block';
    } else {
        waBox.style.display = 'none';
    }

    // Action forms
    const actionBox = document.getElementById('inspActionButtons');
    if (data.status === 'menunggu_verifikasi') {
        actionBox.style.display = 'block';
        document.getElementById('inspApproveForm').action = `/admin/verifikasi-pembayaran/${data.id}/setujui`;
        document.getElementById('inspRejectBtn').onclick = function() {
            bootstrap.Modal.getInstance(document.getElementById('modalProofInspector')).hide();
            openRejectModal(data.id, data.invoice_no);
        };
    } else {
        actionBox.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('modalProofInspector')).show();
}

function zoomIn() {
    currentScale += 0.25;
    applyTransform();
}
function zoomOut() {
    if (currentScale > 0.5) {
        currentScale -= 0.25;
        applyTransform();
    }
}
function rotateImg() {
    currentRotation = (currentRotation + 90) % 360;
    applyTransform();
}
function resetImg() {
    currentScale = 1;
    currentRotation = 0;
    applyTransform();
}
function applyTransform() {
    inspImg.style.transform = `scale(${currentScale}) rotate(${currentRotation}deg)`;
}

// Modal Tolak
function openRejectModal(id, invoice) {
    document.getElementById('tolakInvoice').textContent = invoice;
    document.getElementById('formTolak').action = '/admin/verifikasi-pembayaran/' + id + '/tolak';
    new bootstrap.Modal(document.getElementById('modalTolak')).show();
}

document.querySelectorAll('.btn-reject-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        openRejectModal(this.dataset.id, this.dataset.invoice);
    });
});
</script>
