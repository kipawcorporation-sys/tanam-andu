<?php
/**
 * View: admin/shipping.php — Manajemen Mesin Cek Ongkir Mandiri
 * Plant For Fun (Kerenceng Coffee Expedition)
 */
?>

<style>
.page-hero { background: linear-gradient(135deg, #061510 0%, #0d2b20 60%, #12382c 100%); border-radius: 20px; padding: 28px 32px; margin-bottom: 28px; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-40px; right:-60px; width:200px; height:200px; background:radial-gradient(circle,rgba(191,210,84,.08) 0%,transparent 70%); pointer-events:none; }
.page-hero h4 { color:#fff; font-weight:800; font-size:1.4rem; margin:0 0 4px; }
.page-hero p { color:rgba(255,255,255,.55); font-size:.85rem; margin:0; }
.btn-new-item { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; padding:9px 20px; border-radius:10px; font-size:.84rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; transition:.2s; text-decoration:none; }
.btn-new-item:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(34,197,94,.35); color:#fff; }
.btn-public-link { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:rgba(255,255,255,.85); padding:9px 18px; border-radius:10px; font-size:.84rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:7px; transition:.15s; }
.btn-public-link:hover { background:rgba(255,255,255,.18); color:#fff; }
.engine-badge { display:inline-flex; align-items:center; gap:5px; background:rgba(191,210,84,.2); border:1px solid rgba(191,210,84,.4); color:#bfd254; padding:4px 12px; border-radius:50px; font-size:.72rem; font-weight:700; }
</style>
<div class="page-hero">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="bi bi-truck me-2"></i>Mesin Cek Ongkir Mandiri</h4>
            <p>Kelola tarif dasar pengiriman per zona wilayah — 100% lokal &amp; offline-first</p>
            <div class="d-flex gap-2 mt-3">
                <span class="engine-badge"><i class="bi bi-lightning-charge-fill"></i>0ms Delay</span>
                <span class="engine-badge"><i class="bi bi-shield-check-fill"></i>Zero External API</span>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/cek-ongkir" target="_blank" class="btn-public-link">
                <i class="bi bi-box-arrow-up-right"></i>Halaman Publik
            </a>
            <button class="btn-new-item" data-bs-toggle="modal" data-bs-target="#modalTambahKota">
                <i class="bi bi-plus-lg"></i>Tambah Kota
            </button>
        </div>
    </div>
</div>

<!-- Info Banner Engine -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #12382c 0%, #1e5241 100%); color: #fff;">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark fw-bold mb-2 font-mono">0ms DELAY &bull; ZERO EXTERNAL DEPENDENCY</span>
                <h5 class="fw-bold mb-1 text-white">Sistem Kalkulator Ongkir Terintegrasi Penuh</h5>
                <p class="small text-white-50 mb-0">
                    Titik Asal (*Origin*): <strong><?= htmlspecialchars($origin) ?></strong>.<br>
                    Perhitungan ongkir dihitung secara instan berbasis zona logistik 38 provinsi di Indonesia untuk kurir <strong>JNE, J&T Express, SiCepat, POS Indonesia</strong>, dan <strong>Kurir Lokal Kerenceng</strong>.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex flex-column gap-1 bg-white bg-opacity-10 p-3 rounded-3 text-start">
                    <div class="small text-white-50">Total Kota & Kabupaten Terdaftar:</div>
                    <div class="fs-4 fw-bold font-mono text-warning"><?= number_format($totalCities) ?> Daerah</div>
                    <div class="small text-white-50">Total Zona Tarif: <strong><?= count($zones) ?> Zona</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Zona Tarif Logistik -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Zona & Wilayah Cakupan</th>
                        <th>Tarif Dasar (/kg)</th>
                        <th>Estimasi Sampai</th>
                        <th>Kurir Aktif</th>
                        <th>Total Kota</th>
                        <th style="width: 100px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($zones as $idx => $z): ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $idx + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($z['zone_name']) ?></div>
                                <div class="text-muted small text-truncate" style="max-width: 380px;">
                                    <?= htmlspecialchars($z['description']) ?>
                                </div>
                                <span class="badge bg-light text-secondary border font-mono" style="font-size: 10px;">
                                    <?= htmlspecialchars($z['zone_code']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-success fs-6">
                                    Rp <?= number_format($z['base_rate_per_kg'], 0, ',', '.') ?>
                                </div>
                                <span class="text-muted" style="font-size: 11px;">per 1 kg pertama</span>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark"><i class="bi bi-clock me-1 text-muted"></i><?= htmlspecialchars($z['etd_regular']) ?></div>
                                <?php if (!empty($z['etd_express'])): ?>
                                    <div class="text-muted" style="font-size: 11px;">Kilat: <?= htmlspecialchars($z['etd_express']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php if ($z['jne_active']): ?><span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">JNE</span><?php endif; ?>
                                    <?php if ($z['jnt_active']): ?><span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">J&T</span><?php endif; ?>
                                    <?php if ($z['sicepat_active']): ?><span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 10px;">SiCepat</span><?php endif; ?>
                                    <?php if ($z['pos_active']): ?><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size: 10px;">POS</span><?php endif; ?>
                                    <?php if ($z['local_active']): ?><span class="badge bg-success-subtle" style="color:#0a3622; font-size: 10px;">Lokal Kerenceng</span><?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-mono">
                                    <?= (int)$z['total_cities'] ?> daerah
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary btn-edit-zone"
                                        data-json="<?= htmlspecialchars(json_encode($z)) ?>"
                                        title="Ubah Tarif & Kurir">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Zona Tarif -->
<div class="modal fade" id="modalEditZone" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditZone" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold" id="editZoneTitle"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Tarif Zona</h5>
                        <div class="text-muted small" id="editZoneSubtitle">Sesuaikan tarif dasar per kg dan kurir yang aktif</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tarif Dasar per Kg (Rp) *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="base_rate_per_kg" id="edit_base_rate" class="form-control" required>
                            </div>
                            <div class="form-text">Tarif acuan layanan reguler untuk berat 1 kg pertama dari Cicalengka.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estimasi Reguler</label>
                            <input type="text" name="etd_regular" id="edit_etd_regular" class="form-control" placeholder="2-3 Hari" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estimasi Kilat / Express</label>
                            <input type="text" name="etd_express" id="edit_etd_express" class="form-control" placeholder="1 Hari">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-2">Kurir Ekspedisi yang Diaktifkan di Zona Ini:</label>
                            <div class="d-flex flex-column gap-2 p-3 bg-light rounded-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="jne_active" id="edit_jne_active" value="1">
                                    <label class="form-check-label fw-semibold" for="edit_jne_active">JNE Express (REG, YES, OKE, Trucking)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="jnt_active" id="edit_jnt_active" value="1">
                                    <label class="form-check-label fw-semibold" for="edit_jnt_active">J&T Express (EZ, Super)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sicepat_active" id="edit_sicepat_active" value="1">
                                    <label class="form-check-label fw-semibold" for="edit_sicepat_active">SiCepat Ekspres (REG, BEST, HALU)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="pos_active" id="edit_pos_active" value="1">
                                    <label class="form-check-label fw-semibold" for="edit_pos_active">POS Indonesia (Pos Reguler Nusantara)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="local_active" id="edit_local_active" value="1">
                                    <label class="form-check-label fw-semibold" for="edit_local_active">Kurir Lokal Kerenceng Express (Khusus Lokal)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan Tarif</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Kota Baru -->
<div class="modal fade" id="modalTambahKota" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/ongkir/kota/simpan" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-success me-2"></i>Tambah Kota / Kabupaten Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipe Wilayah *</label>
                            <select name="city_type" class="form-select" required>
                                <option value="Kabupaten">Kabupaten</option>
                                <option value="Kota">Kota</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Pos (Opsional)</label>
                            <input type="text" name="postal_code" class="form-control" placeholder="12345">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Kota / Kabupaten *</label>
                            <input type="text" name="city_name" class="form-control" placeholder="Contoh: Kabupaten Bandung Barat" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Provinsi *</label>
                            <input type="text" name="province" class="form-control" placeholder="Contoh: Jawa Barat" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilih Zona Tarif Logistik *</label>
                            <select name="zone_id" class="form-select" required>
                                <?php foreach ($zones as $z): ?>
                                    <option value="<?= $z['id'] ?>">
                                        <?= htmlspecialchars($z['zone_name']) ?> (Rp <?= number_format($z['base_rate_per_kg'], 0, ',', '.') ?>/kg)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan Kota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit-zone').forEach(btn => {
    btn.addEventListener('click', function() {
        const z = JSON.parse(this.dataset.json);
        document.getElementById('formEditZone').action = '/admin/ongkir/' + z.id + '/update';
        document.getElementById('editZoneTitle').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Tarif: ' + z.zone_name;
        document.getElementById('edit_base_rate').value = Math.round(z.base_rate_per_kg);
        document.getElementById('edit_etd_regular').value = z.etd_regular || '2-3 Hari';
        document.getElementById('edit_etd_express').value = z.etd_express || '';
        document.getElementById('edit_jne_active').checked = (z.jne_active == 1);
        document.getElementById('edit_jnt_active').checked = (z.jnt_active == 1);
        document.getElementById('edit_sicepat_active').checked = (z.sicepat_active == 1);
        document.getElementById('edit_pos_active').checked = (z.pos_active == 1);
        document.getElementById('edit_local_active').checked = (z.local_active == 1);
        new bootstrap.Modal(document.getElementById('modalEditZone')).show();
    });
});
</script>

