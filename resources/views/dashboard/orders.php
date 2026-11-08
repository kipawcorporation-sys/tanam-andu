<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-bag-check text-success me-2"></i>Pesanan Kaos Saya
        </h3>
        <p class="text-muted mb-0">Pantau riwayat pembelian kaos organik & merchandise, upload bukti transfer, lacak resi kurir, dan konfirmasi barang diterima.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?= url('/toko') ?>" class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="bi bi-cart-plus me-1"></i> Beli Kaos Baru
        </a>
    </div>
</div>

<!-- Navigasi Filter Status Pesanan -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-2 p-md-3">
        <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $statusFilter === 'all' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/pesanan?status=all') ?>">
                    <i class="bi bi-grid-fill me-1"></i> Semua
                    <span class="badge <?= $statusFilter === 'all' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['all'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $statusFilter === 'unpaid' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/pesanan?status=unpaid') ?>">
                    <i class="bi bi-clock-history me-1"></i> Belum Bayar
                    <span class="badge <?= $statusFilter === 'unpaid' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['unpaid'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $statusFilter === 'processing' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/pesanan?status=processing') ?>">
                    <i class="bi bi-box-seam me-1"></i> Diproses
                    <span class="badge <?= $statusFilter === 'processing' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['processing'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $statusFilter === 'shipped' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/pesanan?status=shipped') ?>">
                    <i class="bi bi-truck me-1"></i> Dikirim
                    <span class="badge <?= $statusFilter === 'shipped' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['shipped'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $statusFilter === 'completed' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/pesanan?status=completed') ?>">
                    <i class="bi bi-check2-circle me-1"></i> Selesai
                    <span class="badge <?= $statusFilter === 'completed' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['completed'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill text-nowrap <?= $statusFilter === 'cancelled' ? 'active bg-success text-white' : 'text-secondary' ?>" href="<?= url('/dashboard/pesanan?status=cancelled') ?>">
                    <i class="bi bi-x-circle me-1"></i> Dibatalkan
                    <span class="badge <?= $statusFilter === 'cancelled' ? 'bg-white text-success' : 'bg-light text-dark' ?> ms-1 rounded-pill"><?= $counts['cancelled'] ?></span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Daftar Pesanan -->
<?php if (empty($orders)): ?>
    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
        <div class="card-body">
            <div class="display-3 text-muted mb-3">🛍️</div>
            <h5 class="fw-bold text-dark">Belum ada pesanan di kategori ini</h5>
            <p class="text-muted mb-4">Setiap pembelian kaos berarti Anda ikut menanam pohon dan menjaga kelestarian bumi.</p>
            <a href="<?= url('/toko') ?>" class="btn btn-outline-success rounded-pill px-4">
                <i class="bi bi-shop me-1"></i> Buka Katalog Kaos
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-4">
        <?php foreach ($orders as $order): ?>
            <?php
                $orderStatus = $order['status'];
                $paymentStatus = $order['payment_status'] ?? 'menunggu_pembayaran';
                $isUnpaid = ($orderStatus === 'menunggu_pembayaran');
                $isShipped = ($orderStatus === 'dikirim');
                $isCompleted = ($orderStatus === 'selesai');
                $isCancelled = ($orderStatus === 'dibatalkan');

                // Status Badge
                $statusBadgeClass = 'bg-secondary';
                $statusLabel = 'Menunggu Pembayaran';
                if ($isUnpaid) {
                    $statusBadgeClass = 'bg-warning text-dark';
                    $statusLabel = 'Menunggu Pembayaran';
                } elseif ($orderStatus === 'diproses') {
                    $statusBadgeClass = 'bg-info text-dark';
                    $statusLabel = 'Sedang Dikemas / Diproses';
                } elseif ($isShipped) {
                    $statusBadgeClass = 'bg-primary text-white';
                    $statusLabel = 'Dalam Pengiriman';
                } elseif ($isCompleted) {
                    $statusBadgeClass = 'bg-success text-white';
                    $statusLabel = 'Pesanan Selesai';
                } elseif ($isCancelled) {
                    $statusBadgeClass = 'bg-danger text-white';
                    $statusLabel = 'Dibatalkan';
                }

                $hasRejectedProof = (!empty($order['latest_proof']) && $order['latest_proof']['status'] === 'ditolak');
            ?>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Header Kartu Pesanan -->
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success-subtle p-2 rounded-3 fs-4" style="color:#0a3622;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark font-monospace fs-5">
                                    <?= e($order['order_number']) ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i> Dipesan: <?= format_date($order['created_at'], 'd M Y, H:i') ?> WIB
                                    <?php if (!empty($order['invoice_no'])): ?>
                                        &bull; <span class="font-monospace"><?= e($order['invoice_no']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge <?= $statusBadgeClass ?> px-3 py-2 rounded-pill fs-6">
                                <i class="bi bi-dot"></i> <?= $statusLabel ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <!-- Warning / Alasan Bukti Ditolak jika ada -->
                    <?php if ($hasRejectedProof): ?>
                        <div class="alert alert-danger border-0 rounded-3 p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i>
                                <strong>Bukti Transfer Sebelumnya Ditolak Admin:</strong>
                                <span class="d-block text-muted small mt-1"><?= e($order['latest_proof']['rejected_reason'] ?: 'Berkas tidak jelas atau nominal tidak sesuai.') ?></span>
                            </div>
                            <a href="<?= url('/pembayaran/' . e($order['invoice_no'])) ?>" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">
                                <i class="bi bi-arrow-repeat me-1"></i> Upload Ulang Bukti Transfer
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <!-- Kolom Kiri: Rincian Item Pesanan -->
                        <div class="col-lg-7">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-check text-success me-1"></i> Rincian Produk:</h6>
                            <div class="d-flex flex-column gap-2 mb-3">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-white border rounded-3 p-2 text-center" style="width: 48px; height: 48px;">
                                                <i class="bi bi-tshirt fs-4 text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= e($item['product_name']) ?></div>
                                                <div class="text-muted small">
                                                    <?= e($item['variant_info'] ?? ('Ukuran: ' . ($item['size'] ?? '-') . ' | Warna: ' . ($item['color'] ?? '-'))) ?>
                                                    &times; <span class="fw-bold text-dark"><?= $item['quantity'] ?> pcs</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-dark"><?= format_rupiah($item['subtotal']) ?></div>
                                            <div class="text-muted small">@ <?= format_rupiah($item['price']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Catatan Pesanan jika ada -->
                            <?php if (!empty($order['notes'])): ?>
                                <div class="bg-light p-3 rounded-3 small text-muted">
                                    <span class="fw-bold text-dark">Catatan:</span> <?= e($order['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Kolom Kanan: Pengiriman & Alamat -->
                        <div class="col-lg-5 border-start-lg">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-success me-1"></i> Info Pengiriman:</h6>

                            <?php if (!empty($order['pickup_at_event'])): ?>
                                <!-- Opsi Ambil di Acara -->
                                <div class="bg-success-subtle border border-success-subtle p-3 rounded-3 mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                                        <span class="badge bg-success text-white rounded-pill">Ambil di Lokasi Acara</span>
                                    </div>
                                    <div class="small text-muted mt-2">
                                        Kaos akan diserahkan langsung oleh panitia di titik kumpul saat registrasi kegiatan penanaman. Bebas ongkos kirim!
                                    </div>
                                    <?php if (!empty($order['event_title'])): ?>
                                        <div class="mt-2 pt-2 border-top border-success-subtle small">
                                            <div class="fw-bold text-dark"><?= e($order['event_title']) ?></div>
                                            <div class="text-muted"><?= format_date($order['event_date'], 'l, d F Y') ?></div>
                                            <div class="text-muted">Titik: <?= e($order['gathering_point']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Dikirim Kurir Ekspedisi -->
                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold text-dark">
                                            <i class="bi bi-box me-1 text-primary"></i> <?= e($order['shipping_method'] ?: 'Kurir Standar') ?>
                                        </div>
                                        <span class="badge bg-white text-dark border rounded-pill">
                                            Status: <?= ucfirst($order['shipment_status'] ?? 'diproses') ?>
                                        </span>
                                    </div>

                                    <?php if (!empty($order['tracking_number'])): ?>
                                        <div class="mt-2 p-2 bg-white rounded border">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="small">
                                                    <span class="text-muted d-block" style="font-size: 11px;">Nomor Resi:</span>
                                                    <span class="font-monospace fw-bold text-dark fs-6" id="resiText<?= $order['id'] ?>"><?= e($order['tracking_number']) ?></span>
                                                </div>
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill copy-btn" data-clipboard="<?= e($order['tracking_number']) ?>">
                                                    <i class="bi bi-clipboard"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="small text-muted fst-italic">
                                            Nomor resi akan otomatis terbit setelah admin mengirimkan paket.
                                        </div>
                                    <?php endif; ?>

                                    <!-- Snapshot Alamat Penerima -->
                                    <?php if (!empty($order['address'])): ?>
                                        <div class="mt-3 pt-2 border-top small text-muted">
                                            <div class="fw-bold text-dark"><?= e($order['address']['recipient_name'] ?? auth('name')) ?> (<?= e($order['address']['phone'] ?? auth('phone')) ?>)</div>
                                            <div><?= e($order['address']['street_address'] ?? '') ?></div>
                                            <div><?= e($order['address']['subdistrict'] ?? '') ?>, <?= e($order['address']['city'] ?? '') ?>, <?= e($order['address']['province'] ?? '') ?> <?= e($order['address']['postal_code'] ?? '') ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Rincian Biaya -->
                            <div class="border rounded-3 p-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Subtotal Produk:</span>
                                    <span><?= format_rupiah($order['subtotal']) ?></span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Ongkos Kirim:</span>
                                    <span><?= (float)$order['shipping_cost'] > 0 ? format_rupiah($order['shipping_cost']) : '<span class="text-success fw-bold">GRATIS</span>' ?></span>
                                </div>
                                <?php if (!empty($order['unique_code'])): ?>
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Kode Unik Transfer:</span>
                                        <span class="text-success font-monospace">+<?= $order['unique_code'] ?></span>
                                    </div>
                                <?php endif; ?>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                                    <span>Total Tagihan:</span>
                                    <span class="text-success"><?= format_rupiah($order['grand_total'] ?? $order['total_amount']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Kartu: Aksi Cepat -->
                <div class="card-footer bg-light border-top p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <?php if ($isUnpaid): ?>
                                <span class="text-muted small">
                                    <i class="bi bi-clock-history text-warning me-1"></i> Batas Waktu Bayar: <strong><?= format_date($order['payment_expires_at'] ?? date('Y-m-d H:i:s', strtotime('+24 hours')), 'd M Y, H:i') ?> WIB</strong>
                                </span>
                            <?php elseif ($isCompleted): ?>
                                <span class="text-success small fw-bold">
                                    <i class="bi bi-patch-check-fill me-1"></i> Pesanan telah diselesaikan dan diterima dengan baik.
                                </span>
                            <?php elseif ($isShipped): ?>
                                <span class="text-primary small fw-bold">
                                    <i class="bi bi-truck me-1"></i> Paket sedang dalam perjalanan ke alamat tujuan Anda.
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <!-- Batalkan Pesanan (hanya jika belum dibayar) -->
                            <?php if ($isUnpaid): ?>
                                <button type="button" class="btn btn-outline-danger rounded-pill px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#cancelOrderModal<?= $order['id'] ?>">
                                    <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                                </button>
                                <a href="<?= url('/pembayaran/' . e($order['invoice_no'])) ?>" class="btn btn-success rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                    <i class="bi bi-wallet2 me-1"></i> Bayar Sekarang / Upload Bukti
                                </a>
                            <?php endif; ?>

                            <!-- Konfirmasi Diterima (jika status dikirim) -->
                            <?php if ($isShipped): ?>
                                <form action="<?= url('/dashboard/pesanan/' . $order['id'] . '/diterima') ?>" method="POST" onsubmit="return confirm('Apakah Anda sudah menerima pesanan kaos ini dalam kondisi baik?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> Konfirmasi Pesanan Diterima (+20 Poin)
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- Beli Lagi (jika selesai / dibatalkan) -->
                            <?php if ($isCompleted || $isCancelled): ?>
                                <a href="<?= url('/toko') ?>" class="btn btn-outline-success rounded-pill px-3 btn-sm">
                                    <i class="bi bi-cart-plus me-1"></i> Beli Lagi
                                </a>
                            <?php endif; ?>

                            <!-- Tautan Lihat Invoice Publik jika sudah lunas -->
                            <?php if (!empty($order['invoice_no']) && $paymentStatus === 'berhasil'): ?>
                                <a href="<?= url('/pembayaran/' . e($order['invoice_no'])) ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                                    <i class="bi bi-receipt me-1"></i> Lihat Invoice Lunas
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Batalkan Pesanan -->
            <?php if ($isUnpaid): ?>
                <div class="modal fade" id="cancelOrderModal<?= $order['id'] ?>" tabindex="-1" aria-labelledby="cancelOrderLabel<?= $order['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="<?= url('/dashboard/pesanan/' . $order['id'] . '/batal') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger" id="cancelOrderLabel<?= $order['id'] ?>">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Batalkan Pesanan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <p class="text-muted">
                                        Apakah Anda yakin ingin membatalkan pesanan <strong><?= e($order['order_number']) ?></strong>?
                                    </p>
                                    <div class="alert alert-warning py-2 px-3 small rounded-3">
                                        <i class="bi bi-info-circle me-1"></i> Stok produk yang telah ditahan untuk Anda akan otomatis dikembalikan ke katalog toko.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-dark">Alasan Pembatalan:</label>
                                        <select name="reason" class="form-select rounded-3 mb-2" required>
                                            <option value="Ingin mengganti ukuran / varian kaos">Ingin mengganti ukuran / varian kaos</option>
                                            <option value="Ingin mengubah alamat pengiriman">Ingin mengubah alamat pengiriman</option>
                                            <option value="Berubah pikiran">Berubah pikiran</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Batalkan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy tracking number buttons
    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-clipboard');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check2"></i> Disalin!';
                    this.classList.replace('btn-outline-secondary', 'btn-success');
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.classList.replace('btn-success', 'btn-outline-secondary');
                    }, 2000);
                });
            }
        });
    });
});
</script>

