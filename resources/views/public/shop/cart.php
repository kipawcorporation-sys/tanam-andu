<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-success-subtle fw-bold px-3 py-2 rounded-pill mb-2" style="color:#0a3622;">Keranjang Belanja</span>
            <h2 class="fw-bold mb-0">Pesanan Merchandise Anda</h2>
        </div>
        <a href="<?= url('/toko') ?>" class="btn btn-outline-success rounded-pill fw-semibold">
            ← Tambah Kaos Lain
        </a>
    </div>

    <?php if (!empty($items)): ?>
        <div class="row g-4">
            <!-- Kolom Kiri: Tabel Produk di Keranjang -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th scope="col" style="min-width: 220px;">Produk</th>
                                    <th scope="col" class="text-center">Ukuran / Warna</th>
                                    <th scope="col" class="text-end">Harga</th>
                                    <th scope="col" class="text-center" style="width: 130px;">Jumlah</th>
                                    <th scope="col" class="text-end">Subtotal</th>
                                    <th scope="col" class="text-center" style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light p-2 rounded-3 text-center fs-3" style="width: 54px; height: 54px;">
                                                    👕
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0">
                                                        <a href="<?= url('/toko/' . $item['product_slug']) ?>" class="text-dark text-decoration-none">
                                                            <?= e($item['product_name']) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted"><?= $item['weight_gram'] ?>g / pcs</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light border text-dark fw-bold"><?= $item['size'] ?></span>
                                            <span class="d-block small text-muted mt-1"><?= e($item['color']) ?></span>
                                        </td>
                                        <td class="text-end fw-semibold text-secondary">
                                            <?= format_rupiah($item['price']) ?>
                                        </td>
                                        <td class="text-center">
                                            <form action="<?= url('/keranjang/update') ?>" method="POST" class="d-flex justify-content-center align-items-center gap-1">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="changeQty(this, -1)">-</button>
                                                <input type="number" name="quantity" class="form-control form-control-sm text-center fw-bold px-1" value="<?= $item['quantity'] ?>" min="1" max="50" style="width: 45px;" onchange="this.form.submit()">
                                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="changeQty(this, 1)">+</button>
                                            </form>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            <?= format_rupiah($item['subtotal']) ?>
                                        </td>
                                        <td class="text-center">
                                            <form action="<?= url('/keranjang/hapus') ?>" method="POST" onsubmit="return confirm('Hapus item ini dari keranjang?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Belanja & Tombol Checkout -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top" style="top: 90px;">
                    <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Ringkasan Belanja</h5>

                    <div class="d-flex justify-content-between mb-2 text-secondary small">
                        <span>Total Berat Paket:</span>
                        <strong><?= number_format($totalWeight / 1000, 2) ?> kg</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 text-secondary small">
                        <span>Subtotal Produk:</span>
                        <strong class="text-dark fs-6"><?= format_rupiah($subtotal) ?></strong>
                    </div>

                    <div class="p-3 bg-light rounded-3 small text-muted mb-3 border">
                        <i class="bi bi-truck text-success me-1"></i> Ongkos kirim kurir dihitung pada langkah checkout berikutnya sesuai kota tujuan Anda.
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark">Total Sementara:</span>
                        <h4 class="fw-bold text-success mb-0"><?= format_rupiah($subtotal) ?></h4>
                    </div>

                    <a href="<?= url('/checkout') ?>" class="btn btn-success btn-lg w-100 fw-bold rounded-pill py-3 shadow-sm">
                        Lanjut ke Checkout Alamat →
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4 my-4">
            <div class="display-1 text-muted mb-3">🛒</div>
            <h4 class="fw-bold text-dark">Keranjang Belanja Anda Kosong</h4>
            <p class="text-muted small mb-4">Yuk dukung pelestarian hutan dengan memiliki kaos merchandise berkualitas ramah lingkungan.</p>
            <div>
                <a href="<?= url('/toko') ?>" class="btn btn-success btn-lg fw-bold rounded-pill px-5 shadow-sm">
                    Mulai Belanja Kaos Sekarang
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function changeQty(btn, delta) {
    const form = btn.closest('form');
    const input = form.querySelector('input[name="quantity"]');
    const cur = Number(input.value) || 1;
    const newVal = Math.max(1, cur + delta);
    input.value = newVal;
    form.submit();
}
</script>

