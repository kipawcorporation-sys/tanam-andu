<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Presensi Lapangan - <?= htmlspecialchars($event['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; background: #fff; font-size: 12px; }
        .print-header { border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .meta-table td { padding: 3px 10px; font-size: 11px; }
        .table-attendance { width: 100%; border: 1px solid #cbd5e1; }
        .table-attendance th { background: #f1f5f9; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; }
        .table-attendance td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
        .checkbox-box { width: 22px; height: 22px; border: 1.5px solid #475569; display: inline-block; border-radius: 4px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            @page { margin: 1.2cm; size: A4 portrait; }
        }
    </style>
</head>
<body class="p-4">

    <!-- Top Action Toolbar (Hidden in print) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border">
        <div>
            <a href="/admin/kegiatan" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Admin
            </a>
            <span class="text-muted small">Cetak atau simpan lembar daftar hadir relawan lapangan</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-success btn-sm fw-bold">
                <i class="bi bi-printer me-1"></i>Cetak Sekarang (Print / PDF)
            </button>
        </div>
    </div>

    <!-- Header Dokumen Presensi Resmi -->
    <div class="print-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #12382c;">
                TanamPohon.id &bull; Lembar Presensi Lapangan
            </h4>
            <div class="text-muted small">Ekspedisi Penanaman Pohon & Konservasi Agroforestri Nusantara</div>
        </div>
        <div class="text-end">
            <div class="badge bg-dark px-3 py-2 text-uppercase font-monospace" style="font-size: 11px;">
                Dokumen Resmi Lapangan
            </div>
        </div>
    </div>

    <!-- Meta Informasi Event -->
    <div class="card border rounded-3 mb-3 p-3 bg-light">
        <div class="row g-2">
            <div class="col-8">
                <table class="meta-table w-100">
                    <tr>
                        <td class="text-muted" style="width: 140px;">Nama Ekspedisi:</td>
                        <td class="fw-bold text-dark fs-6"><?= htmlspecialchars($event['title']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori & Ketinggian:</td>
                        <td><strong><?= htmlspecialchars($event['category'] ?? 'Agroforestri') ?></strong> (<?= (int)$event['elevation_masl'] ?> mdpl)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal & Waktu:</td>
                        <td><strong><?= date('l, d F Y', strtotime($event['event_date'])) ?></strong> | Pukul <?= substr($event['gathering_time'], 0, 5) ?> WIB</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Titik Kumpul:</td>
                        <td><?= htmlspecialchars($event['gathering_point']) ?> (<?= htmlspecialchars($event['city'] ?? $event['location_name'] ?? '-') ?>)</td>
                    </tr>
                </table>
            </div>
            <div class="col-4 border-start ps-3">
                <table class="meta-table w-100">
                    <tr>
                        <td class="text-muted">Koordinator:</td>
                        <td class="fw-bold"><?= htmlspecialchars($event['coordinator_name'] ?? 'Tim Lapangan') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kontak Darurat:</td>
                        <td><?= htmlspecialchars($event['coordinator_phone'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Kuota:</td>
                        <td><strong><?= count($participants) ?> / <?= $event['quota'] ?> Peserta</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Hadir Relawan -->
    <table class="table-attendance mb-4">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 90px;">Kode Tiket</th>
                <th>Nama Lengkap Relawan</th>
                <th style="width: 110px;">No Telepon</th>
                <th style="width: 50px;">Kaos</th>
                <th style="width: 130px;">Catatan / Alergi</th>
                <th style="width: 55px;">Hadir</th>
                <th style="width: 120px;">Tanda Tangan / Paraf</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($participants)): ?>
                <?php $no = 1; foreach ($participants as $p): ?>
                    <tr>
                        <td class="text-center font-monospace"><?= $no++ ?></td>
                        <td class="text-center font-monospace fw-bold" style="font-size: 10.5px;"><?= htmlspecialchars($p['ticket_code'] ?? '-') ?></td>
                        <td>
                            <strong class="text-dark"><?= htmlspecialchars($p['name']) ?></strong>
                            <?php if ($p['checked_in_at']): ?>
                                <span class="badge bg-success-subtle text-success p-1 ms-1" style="font-size: 8px;">QR OK</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center font-monospace" style="font-size: 11px;"><?= htmlspecialchars($p['phone'] ?? '-') ?></td>
                        <td class="text-center fw-bold"><?= htmlspecialchars($p['tshirt_size'] ?? '-') ?></td>
                        <td class="small text-muted" style="font-size: 10px;"><?= htmlspecialchars($p['notes'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php if ($p['checked_in_at']): ?>
                                <span class="text-success fw-bold">✓</span>
                            <?php else: ?>
                                <div class="checkbox-box"></div>
                            <?php endif; ?>
                        </td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted fst-italic">
                        Belum ada peserta yang terkonfirmasi mendaftar pada kegiatan ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Penanggung Jawab Lapangan -->
    <div class="row mt-4 pt-2">
        <div class="col-8">
            <div class="small text-muted fst-italic" style="font-size: 10px;">
                * Lembar presensi ini wajib diisi oleh peserta/panitia di meja registrasi titik kumpul.<br>
                * Laporkan rekapitulasi kehadiran final ke sistem admin setelah acara penanaman selesai.
            </div>
        </div>
        <div class="col-4 text-center">
            <div class="small text-muted mb-5">
                <?= htmlspecialchars($event['city'] ?? 'Bandung') ?>, <?= date('d F Y', strtotime($event['event_date'])) ?><br>
                Koordinator Lapangan,
            </div>
            <div class="fw-bold border-bottom pb-1 mx-auto" style="width: 180px;">
                <?= htmlspecialchars($event['coordinator_name'] ?? 'Koordinator Ekspedisi') ?>
            </div>
            <div class="small text-muted" style="font-size: 10px;">Plant For Fun &bull; TanamPohon.id</div>
        </div>
    </div>

</body>
</html>

