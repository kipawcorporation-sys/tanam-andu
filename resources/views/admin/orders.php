<?php
/**
 * View: admin/orders.php — Manajemen Pesanan Kopi Spesialti & Merchandise (Pro)
 */

$statusOptions = [
    'menunggu_pembayaran' => ['Menunggu Bayar', 'bg-warning-subtle text-warning-emphasis', 'bi-clock-fill'],
    'diproses'            => ['Perlu Dikirim',   'bg-info-subtle text-info-emphasis',       'bi-gear-fill'],
    'dikirim'             => ['Dalam Pengiriman','bg-primary-subtle text-primary',          'bi-truck'],
    'selesai'             => ['Selesai',         'bg-success-subtle text-success',          'bi-check-circle-fill'],
    'dibatalkan'          => ['Dibatalkan',      'bg-danger-subtle text-danger',            'bi-x-circle-fill'],
];

$totalOrders = $orderStats['total'] ?? $total ?? 0;
$diproses    = $orderStats['diproses'] ?? 0;
$dikirim     = $orderStats['dikirim'] ?? 0;
$selesai     = $orderStats['selesai'] ?? 0;
$omzet       = $orderStats['total_omzet'] ?? 0;
?>

<style>
/* ── Hero ── */
.order-hero {
    background: linear-gradient(135deg, #061510 0%, #0d2b20 55%, #12382c 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(6, 21, 16, 0.2);
}
.order-hero::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(191, 210, 84, 0.15) 0%, transparent 70%);
    pointer-events: none;
}
.order-hero h4 {
    color: #fff;
    font-weight: 800;
    font-size: 1.45rem;
    margin: 0 0 6px;
}
.order-hero p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.88rem;
    margin: 0;
}

/* ── KPI Row ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 992px) { .kpi-row { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 576px) { .kpi-row { grid-template-columns: repeat(2, 1fr); } }
.kpi-card {
    background: #fff;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.25s ease;
}
.kpi-card:hover {
    box-shadow: 0 8px 24px rgba(18, 56, 44, 0.09);
    transform: translateY(-2px);
}
.kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.kpi-val {
    font-size: 1.3rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 2px;
}
.kpi-lbl {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* ── Filter Bar ── */
.order-filter-bar {
    background: #fff;
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

/* ── Order Table ── */
.order-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid rgba(18, 56, 44, 0.08);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    overflow: hidden;
}
.order-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.order-table thead th {
    background: #f8fafc;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 16px;
    white-space: nowrap;
}
.order-table td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.order-table tbody tr:hover {
    background: #f8faf8;
}

.order-code-pill {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    font-weight: 700;
    color: #1e40af;
    background: #eff6ff;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-block;
}
.items-chip {
    font-size: 0.74rem;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 6px;
    color: #334155;
    display: inline-block;
    max-width: 260px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-hero-action {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 0.84rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    backdrop-filter: blur(4px);
    transition: all 0.2s;
    text-decoration: none;
    cursor: pointer;
}
.btn-hero-action:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
}
</style>

<!-- ══════════════════════════════════════════════════════════════
     PAGE HERO
══════════════════════════════════════════════════════════════ -->
<div class="order-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white bg-opacity-15 text-white font-mono px-3 py-1" style="font-size:11px;">
                    <i class="bi bi-box-seam me-1"></i>SPECIALTY COFFEE &amp; MERCHANDISE
                </span>
                <?php if ($diproses > 0): ?>
                    <span class="badge bg-warning text-dark font-mono px-3 py-1" style="font-size:11px;">
                        <i class="bi bi-bell-fill me-1"></i><?= $diproses ?> Pesanan Perlu Dikirim
                    </span>
                <?php endif; ?>
            </div>
            <h4><i class="bi bi-bag-check-fill me-2 text-warning"></i>Manajemen Pesanan Toko</h4>
            <p>Kelola pemrosesan biji kopi Kerenceng, ekspedisi merchandise, cetak invoice, dan input resi logistik.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/admin/pesanan/export-csv" class="btn-hero-action">
                <i class="bi bi-file-earmark-spreadsheet"></i>Unduh CSV
            </a>
            <a href="/cek-ongkir" target="_blank" class="btn-hero-action">
                <i class="bi bi-truck"></i>Cek Tarif Ongkir ↗
            </a>
        </div>
    </div>
</div>

<!-- ── Flash Messages ── -->
<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- ══════════════════════════════════════════════════════════════
     KPI CARDS ROW
══════════════════════════════════════════════════════════════ -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="bi bi-receipt"></i>
        </div>
        <div>
            <div class="kpi-val text-dark"><?= number_format($totalOrders) ?></div>
            <div class="kpi-lbl">Total Pesanan</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-warning"><?= number_format($diproses) ?></div>
            <div class="kpi-lbl">Perlu Dikirim</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#3b82f6;">
            <i class="bi bi-truck"></i>
        </div>
        <div>
            <div class="kpi-val text-primary"><?= number_format($dikirim) ?></div>
            <div class="kpi-lbl">Dalam Pengiriman</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#ecfdf5; color:#16a34a;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <div class="kpi-val text-success"><?= number_format($selesai) ?></div>
            <div class="kpi-lbl">Selesai</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff; color:#9333ea;">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div>
            <div class="kpi-val text-dark" style="font-size:1.15rem;">Rp <?= number_format($omzet, 0, ',', '.') ?></div>
            <div class="kpi-lbl">Omzet Belanja</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     FILTER & SEARCH BAR
══════════════════════════════════════════════════════════════ -->
<div class="order-filter-bar">
    <div class="input-group input-group-sm" style="max-width:280px;">
        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" id="order-live-search" class="form-control border-start-0" 
               placeholder="Cari order #, nama, invoice..." oninput="filterOrderRows(this.value)">
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <a href="/admin/pesanan" class="btn btn-sm <?= empty($statusFilter) ? 'btn-success' : 'btn-outline-secondary' ?>">
            Semua (<?= $totalOrders ?>)
        </a>
        <a href="/admin/pesanan?status=diproses" class="btn btn-sm <?= $statusFilter === 'diproses' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?>">
            <i class="bi bi-box-seam me-1"></i>Perlu Dikirim (<?= $diproses ?>)
        </a>
        <a href="/admin/pesanan?status=dikirim" class="btn btn-sm <?= $statusFilter === 'dikirim' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <i class="bi bi-truck me-1"></i>Dikirim (<?= $dikirim ?>)
        </a>
        <a href="/admin/pesanan?status=selesai" class="btn btn-sm <?= $statusFilter === 'selesai' ? 'btn-success' : 'btn-outline-secondary' ?>">
            <i class="bi bi-check-circle me-1"></i>Selesai (<?= $selesai ?>)
        </a>
    </div>
    <div class="ms-auto text-muted small font-mono" id="order-count-label">
        <?= number_format($total) ?> pesanan ditemukan
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     TABLE CARD
══════════════════════════════════════════════════════════════ -->
<div class="order-card">
    <div class="order-card-header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-success"></i>
            <span class="fw-bold text-dark">Daftar Transaksi Pesanan</span>
        </div>
        <span class="text-muted small">Halaman <?= $page ?> dari <?= max(1, $totalPages) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table order-table align-middle mb-0" id="orderTable">
            <thead>
                <tr>
                    <th>Order # &amp; Invoice</th>
                    <th>Pembeli</th>
                    <th>Item Produk</th>
                    <th>Pengiriman</th>
                    <th>Total Tagihan</th>
                    <th>Status Pesanan</th>
                    <th>Tanggal</th>
                    <th style="width: 150px;" class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="8">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold">Tidak ada data pesanan</div>
                            <small class="text-muted">Pesanan baru akan muncul saat pelanggan checkout di toko</small>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($orders as $o):
                [$stLabel, $stBadge, $stIcon] = $statusOptions[$o['status']] ?? [$o['status'], 'bg-light text-dark', 'bi-dash'];
                $itemNames = array_map(fn($it) => $it['product_name'] . ' (' . $it['quantity'] . 'x)', $o['items'] ?? []);
                $itemsSummary = !empty($itemNames) ? implode(', ', $itemNames) : 'Lihat rincian';
            ?>
                <tr class="order-row"
                    data-order="<?= strtolower(htmlspecialchars($o['order_number'] ?? '')) ?>"
                    data-invoice="<?= strtolower(htmlspecialchars($o['invoice_no'] ?? '')) ?>"
                    data-buyer="<?= strtolower(htmlspecialchars($o['buyer_name'] ?? '')) ?>"
                    data-email="<?= strtolower(htmlspecialchars($o['buyer_email'] ?? '')) ?>">
                    <td>
                        <span class="order-code-pill"><?= htmlspecialchars($o['order_number']) ?></span>
                        <?php if (!empty($o['invoice_no'])): ?>
                            <div class="text-muted font-mono" style="font-size:11px;"><?= htmlspecialchars($o['invoice_no']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($o['buyer_name']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($o['buyer_email']) ?></div>
                        <?php if (!empty($o['buyer_phone'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $o['buyer_phone']) ?>" target="_blank" class="text-decoration-none text-success small">
                                <i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($o['buyer_phone']) ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="items-chip" title="<?= htmlspecialchars($itemsSummary) ?>">
                            <i class="bi bi-box me-1 text-primary"></i><?= htmlspecialchars($itemsSummary) ?>
                        </span>
                        <div class="text-muted" style="font-size:11px;"><?= count($o['items'] ?? []) ?> jenis item</div>
                    </td>
                    <td>
                        <?php if (!empty($o['pickup_at_event'])): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:11px;">
                                <i class="bi bi-geo-alt-fill me-1"></i>Ambil di Event
                            </span>
                        <?php else: ?>
                            <div class="small fw-semibold text-dark"><?= htmlspecialchars($o['shipping_method'] ?? 'Reguler') ?></div>
                            <div class="text-muted font-mono" style="font-size:11px;">Ongkir: Rp <?= number_format($o['shipping_cost'] ?? 0, 0, ',', '.') ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong class="text-success font-mono">Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></strong>
                    </td>
                    <td>
                        <span class="badge <?= $stBadge ?> rounded-pill px-3 py-1 font-mono" style="font-size:11px;">
                            <i class="bi <?= $stIcon ?> me-1"></i><?= $stLabel ?>
                        </span>
                    </td>
                    <td>
                        <div class="small text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
                        <div class="text-muted" style="font-size:11px;"><?= date('H:i', strtotime($o['created_at'])) ?> WIB</div>
                    </td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <?php if ($o['status'] === 'diproses'): ?>
                                <button type="button" class="btn btn-primary"
                                        onclick="openShipModal(<?= $o['id'] ?>, '<?= htmlspecialchars(addslashes($o['order_number'])) ?>')"
                                        title="Input Resi Pengiriman">
                                    <i class="bi bi-truck me-1"></i>Kirim
                                </button>
                            <?php endif; ?>

                            <?php if ($o['status'] === 'dikirim'): ?>
                                <form action="/admin/pesanan/<?= $o['id'] ?>/selesai" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tandai pesanan ini SELESAI?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-success" title="Tandai Selesai">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="showOrderDetail(<?= htmlspecialchars(json_encode($o)) ?>)"
                                    title="Lihat Rincian Pesanan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
            <small class="text-muted">Menampilkan <?= count($orders) ?> dari <?= number_format($total) ?> pesanan</small>
            <div class="btn-group btn-group-sm">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?status=<?= urlencode($statusFilter) ?>&page=<?= $i ?>"
                       class="btn <?= $i === $page ? 'btn-success' : 'btn-outline-secondary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL INPUT RESI PENGIRIMAN
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalKirimPesanan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <form id="formKirimPesanan" method="POST">
                <?= csrf_field() ?>
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-white bg-opacity-20 text-white font-mono" id="ship-modal-order-number">ORDER-#</span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <h5 class="fw-bold mb-0">Input Bukti Resi Pengiriman</h5>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ekspedisi / Kurir Logistik</label>
                        <select name="courier" class="form-select" required>
                            <option value="JNE">JNE Express</option>
                            <option value="J&T">J&amp;T Express</option>
                            <option value="SiCepat">SiCepat Ekspres</option>
                            <option value="Pos Indonesia">Pos Indonesia</option>
                            <option value="Anteraja">Anteraja</option>
                            <option value="GoSend">GoSend / GrabExpress (Instant)</option>
                            <option value="Ekspedisi Mandiri">Kurir Internal Kebon Kerenceng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nomor Resi / AWB <span class="text-danger">*</span></label>
                        <input type="text" name="tracking_code" class="form-control font-mono" placeholder="Contoh: JP1234567890" required>
                    </div>
                    <div class="alert alert-info border-0 small mb-0">
                        <i class="bi bi-info-circle me-1"></i>Pelanggan akan dapat melacak status paket dari dashboard akun mereka.
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-send me-1"></i>Simpan &amp; Ubah ke Dikirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL DETAIL PESANAN
══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetailPesanan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <div class="p-4 text-white" style="background: linear-gradient(135deg, #061510 0%, #12382c 100%);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-white bg-opacity-20 text-white font-mono" id="dtl-order-no">ORDER-#</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <h5 class="fw-bold mb-0" id="dtl-buyer-name">Nama Pembeli</h5>
                <div class="text-white-50 small" id="dtl-buyer-info">email@example.com</div>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Rincian Produk -->
                <div class="bg-white p-3 rounded-3 border mb-3">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bag text-success me-2"></i>Item Produk Belanja</h6>
                    <div id="dtl-items-list">
                        <!-- Populated via JS -->
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top small">
                        <span class="text-muted">Subtotal Produk</span>
                        <span class="fw-bold font-mono" id="dtl-subtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Ongkos Kirim</span>
                        <span class="font-mono" id="dtl-shipping-cost">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top fw-bold text-success">
                        <span>Total Tagihan</span>
                        <span class="font-mono fs-6" id="dtl-total-amount">Rp 0</span>
                    </div>
                </div>

                <!-- Info Pengiriman & Alamat -->
                <div class="bg-white p-3 rounded-3 border">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-geo-alt text-primary me-2"></i>Tujuan Pengiriman &amp; Ekspedisi</h6>
                    <div class="small text-muted mb-1">Metode: <strong class="text-dark" id="dtl-shipping-method">-</strong></div>
                    <div class="small text-secondary" id="dtl-address" style="white-space: pre-line;">-</div>
                    <div class="mt-2 pt-2 border-top small font-mono" id="dtl-notes-box"></div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function openShipModal(id, orderNumber) {
    document.getElementById('formKirimPesanan').action = '/admin/pesanan/' + id + '/kirim';
    document.getElementById('ship-modal-order-number').textContent = orderNumber;
    new bootstrap.Modal(document.getElementById('modalKirimPesanan')).show();
}

function showOrderDetail(o) {
    document.getElementById('dtl-order-no').textContent = o.order_number || 'ORDER';
    document.getElementById('dtl-buyer-name').textContent = o.buyer_name || 'Pembeli';
    document.getElementById('dtl-buyer-info').textContent = (o.buyer_email || '') + (o.buyer_phone ? ' • ' + o.buyer_phone : '');

    const itemsContainer = document.getElementById('dtl-items-list');
    const items = o.items || [];
    if (items.length === 0) {
        itemsContainer.innerHTML = '<div class="text-muted small py-2">Tidak ada data item.</div>';
    } else {
        itemsContainer.innerHTML = items.map(it => `
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <div class="fw-bold small text-dark">${it.product_name}</div>
                    <div class="text-muted" style="font-size:11px;">${it.variant_info || ''} &bull; ${it.quantity} x Rp ${Number(it.price).toLocaleString('id-ID')}</div>
                </div>
                <span class="font-mono small fw-bold text-dark">Rp ${Number(it.subtotal).toLocaleString('id-ID')}</span>
            </div>
        `).join('');
    }

    document.getElementById('dtl-subtotal').textContent = 'Rp ' + Number(o.subtotal || 0).toLocaleString('id-ID');
    document.getElementById('dtl-shipping-cost').textContent = 'Rp ' + Number(o.shipping_cost || 0).toLocaleString('id-ID');
    document.getElementById('dtl-total-amount').textContent = 'Rp ' + Number(o.total_amount || 0).toLocaleString('id-ID');
    document.getElementById('dtl-shipping-method').textContent = o.pickup_at_event ? 'Ambil Langsung di Lokasi Event' : (o.shipping_method || 'Ekspedisi Reguler');

    // Parse address snapshot
    let addrText = o.shipping_address_snapshot || 'Alamat tidak tersedia';
    try {
        const addrObj = JSON.parse(addrText);
        if (typeof addrObj === 'object' && addrObj !== null) {
            addrText = `${addrObj.address || ''}, ${addrObj.city || ''}, ${addrObj.province || ''} ${addrObj.postal_code || ''}\nPenerima: ${addrObj.recipient_name || ''} (${addrObj.phone || ''})`;
        }
    } catch(e) {}
    document.getElementById('dtl-address').textContent = addrText;

    // Resi / notes
    const notesBox = document.getElementById('dtl-notes-box');
    if (o.notes) {
        try {
            const n = JSON.parse(o.notes);
            notesBox.innerHTML = `<strong>Kurir:</strong> ${n.courier || '-'} | <strong>Resi:</strong> ${n.tracking_code || '-'} | <strong>Waktu Kirim:</strong> ${n.shipped_at || '-'}`;
        } catch(e) {
            notesBox.textContent = 'Catatan: ' + o.notes;
        }
    } else {
        notesBox.textContent = 'Belum ada catatan pengiriman.';
    }

    new bootstrap.Modal(document.getElementById('modalDetailPesanan')).show();
}

function filterOrderRows(query) {
    const q = query.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('#orderTableBody .order-row').forEach(row => {
        const order   = row.dataset.order || '';
        const invoice = row.dataset.invoice || '';
        const buyer   = row.dataset.buyer || '';
        const email   = row.dataset.email || '';
        const match   = !q || order.includes(q) || invoice.includes(q) || buyer.includes(q) || email.includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('order-count-label').textContent = visible + ' pesanan ditemukan';
}
</script>
