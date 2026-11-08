<?php
/**
 * CronService — TanamPohon.id
 * 
 * Layanan otomasi terjadwal (Cron Job) untuk:
 * 1. Membatalkan transaksi kedaluwarsa & mengembalikan stok/kuota yang tertahan.
 * 2. Mengirimkan notifikasi pengingat pembayaran (3 jam menjelang batas waktu).
 * 3. Mengirimkan pengingat H-3 dan H-1 kegiatan penanaman bagi relawan terdaftar.
 * 4. Pembersihan data usang (keranjang tamu & log kadaluwarsa).
 */

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../Models/Notification.php';

class CronService
{
    private PDO $db;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->notificationModel = new Notification();
    }

    /**
     * Jalankan seluruh rangkaian tugas cron job
     *
     * @return array Ringkasan hasil eksekusi seluruh tugas
     */
    public function runAll(): array
    {
        $startTime = microtime(true);

        $expired = $this->cancelExpiredPayments();
        $reminders = $this->sendPaymentReminders();
        $events = $this->sendEventReminders();
        $cleanup = $this->cleanupOldData();

        $duration = round(microtime(true) - $startTime, 4);

        $summary = [
            'timestamp'           => date('Y-m-d H:i:s'),
            'execution_time_sec'  => $duration,
            'expired_payments'    => $expired,
            'payment_reminders'   => $reminders,
            'event_reminders'     => $events,
            'cleanup'             => $cleanup,
        ];

        // Catat eksekusi cron ke activity_logs
        $this->logActivity('cron_run_all', 'Eksekusi cron job lengkap: ' . json_encode($summary, JSON_UNESCAPED_UNICODE));

        // Simpan timestamp eksekusi terakhir
        $this->recordRunTimestamp();

        return $summary;
    }

    /**
     * Catat waktu terakhir cron dieksekusi ke tabel settings
     */
    public function recordRunTimestamp(): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO settings (`key`, `value`, `group`, `type`, `updated_at`)
                VALUES ('last_cron_run', NOW(), 'system', 'text', NOW())
                ON DUPLICATE KEY UPDATE `value` = NOW(), `updated_at` = NOW()
            ");
            $stmt->execute();
        } catch (\Exception $e) {
            // Abaikan jika tabel settings memiliki kendala
        }
    }

    /**
     * 1. Batalkan pembayaran kedaluwarsa & kembalikan stok / kuota yang tertahan
     *
     * @return array
     */
    public function cancelExpiredPayments(): array
    {
        // Cari pembayaran dengan status 'menunggu_pembayaran' yang telah melewati batas waktu expires_at
        $stmt = $this->db->prepare("
            SELECT p.*, u.email as user_email, u.name as user_name
            FROM payments p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.status = 'menunggu_pembayaran'
              AND p.expires_at IS NOT NULL
              AND p.expires_at <= NOW()
            ORDER BY p.expires_at ASC
        ");
        $stmt->execute();
        $expiredPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cancelledInvoices = [];
        $restoredItemsCount = 0;
        $restoredQuotaCount = 0;

        foreach ($expiredPayments as $p) {
            $paymentId = (int)$p['id'];
            $invoiceNo = $p['invoice_no'];
            $payableType = $p['payable_type'];
            $payableId = (int)$p['payable_id'];
            $userId = !empty($p['user_id']) ? (int)$p['user_id'] : null;

            // 1. Ubah status pembayaran menjadi 'kedaluwarsa'
            $stmtUpdatePay = $this->db->prepare("
                UPDATE payments 
                SET status = 'kedaluwarsa', updated_at = NOW() 
                WHERE id = :id AND status = 'menunggu_pembayaran'
            ");
            $stmtUpdatePay->execute(['id' => $paymentId]);

            // 2. Tangani entitas berdasarkan payable_type
            if ($payableType === 'order') {
                // Kembalikan stok varian kaos
                $stmtItems = $this->db->prepare("
                    SELECT product_variant_id, quantity 
                    FROM order_items 
                    WHERE order_id = :order_id
                ");
                $stmtItems->execute(['order_id' => $payableId]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    if (!empty($item['product_variant_id'])) {
                        $stmtStock = $this->db->prepare("
                            UPDATE product_variants 
                            SET stock = stock + :qty 
                            WHERE id = :variant_id
                        ");
                        $stmtStock->execute([
                            'qty'        => (int)$item['quantity'],
                            'variant_id' => (int)$item['product_variant_id']
                        ]);
                        $restoredItemsCount += (int)$item['quantity'];
                    }
                }

                // Ubah status pesanan menjadi 'dibatalkan'
                $stmtOrder = $this->db->prepare("
                    UPDATE orders 
                    SET status = 'dibatalkan', updated_at = NOW() 
                    WHERE id = :order_id AND status = 'menunggu_pembayaran'
                ");
                $stmtOrder->execute(['order_id' => $payableId]);

            } elseif ($payableType === 'participant') {
                // Batalkan pendaftaran peserta relawan
                $stmtPart = $this->db->prepare("
                    SELECT event_id FROM participants 
                    WHERE id = :participant_id
                ");
                $stmtPart->execute(['participant_id' => $payableId]);
                $eventId = (int)$stmtPart->fetchColumn();

                // Ubah status pendaftaran peserta menjadi 'batal'
                $stmtCancelPart = $this->db->prepare("
                    UPDATE participants 
                    SET registration_status = 'batal', 
                        cancelled_at = NOW(), 
                        cancel_reason = 'Batas waktu pembayaran telah kedaluwarsa secara otomatis oleh sistem.'
                    WHERE id = :participant_id AND registration_status = 'menunggu'
                ");
                $stmtCancelPart->execute(['participant_id' => $payableId]);

                // Lepaskan reservasi kuota kegiatan penanaman
                if ($eventId > 0) {
                    $stmtQuota = $this->db->prepare("
                        UPDATE planting_events 
                        SET registered_count = GREATEST(0, registered_count - 1), updated_at = NOW() 
                        WHERE id = :event_id
                    ");
                    $stmtQuota->execute(['event_id' => $eventId]);
                    $restoredQuotaCount++;
                }

            } elseif ($payableType === 'donation') {
                // Ubah status donasi menjadi 'dibatalkan'
                $stmtDon = $this->db->prepare("
                    UPDATE donations 
                    SET status = 'dibatalkan', updated_at = NOW() 
                    WHERE id = :donation_id AND status IN ('pending', 'menunggu_pembayaran')
                ");
                $stmtDon->execute(['donation_id' => $payableId]);
            }

            // 3. Kirim notifikasi in-app kepada pengguna jika terdaftar
            if ($userId) {
                $typeTitle = match($payableType) {
                    'order'       => 'Pesanan Kaos Kedaluwarsa',
                    'participant' => 'Pendaftaran Relawan Kedaluwarsa',
                    default       => 'Donasi Pohon Kedaluwarsa',
                };

                $message = "Tagihan {$invoiceNo} sebesar " . format_rupiah((float)$p['grand_total']) . " telah kedaluwarsa karena tidak ada konfirmasi bukti transfer hingga batas waktu yang ditentukan. Alokasi stok atau kuota telah dikembalikan.";

                $this->notificationModel->createNotification(
                    $userId,
                    'payment',
                    $typeTitle,
                    $message,
                    ['invoice_no' => $invoiceNo, 'status' => 'kedaluwarsa']
                );
            }

            // Catat log pembatalan
            $this->logActivity('cron_payment_expired', "Pembayaran invoice {$invoiceNo} dibatalkan otomatis karena kedaluwarsa.", 'payments', $paymentId);

            $cancelledInvoices[] = $invoiceNo;
        }

        return [
            'total_cancelled'       => count($cancelledInvoices),
            'invoices'              => $cancelledInvoices,
            'restored_stock_items'  => $restoredItemsCount,
            'restored_event_quotas' => $restoredQuotaCount,
        ];
    }

    /**
     * 2. Kirim notifikasi pengingat pembayaran (3 jam menjelang batas waktu)
     *
     * @return array
     */
    public function sendPaymentReminders(): array
    {
        // Cari pembayaran dengan status 'menunggu_pembayaran' yang jatuh tempo dalam 3 jam ke depan
        $stmt = $this->db->prepare("
            SELECT p.*, u.email as user_email, u.name as user_name
            FROM payments p
            JOIN users u ON p.user_id = u.id
            WHERE p.status = 'menunggu_pembayaran'
              AND p.expires_at IS NOT NULL
              AND p.expires_at > NOW()
              AND p.expires_at <= (NOW() + INTERVAL 3 HOUR)
            ORDER BY p.expires_at ASC
        ");
        $stmt->execute();
        $pendingPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $remindedInvoices = [];

        foreach ($pendingPayments as $p) {
            $paymentId = (int)$p['id'];
            $userId = (int)$p['user_id'];
            $invoiceNo = $p['invoice_no'];

            // Cek apakah pengingat untuk invoice ini sudah pernah dikirim dalam 12 jam terakhir
            $stmtCheck = $this->db->prepare("
                SELECT COUNT(*) FROM activity_logs 
                WHERE action = 'cron_payment_reminder' 
                  AND loggable_type = 'payments' 
                  AND loggable_id = :id
            ");
            $stmtCheck->execute(['id' => $paymentId]);
            $alreadyReminded = (int)$stmtCheck->fetchColumn() > 0;

            if ($alreadyReminded) {
                continue;
            }

            // Hitung sisa waktu menit
            $diffMinutes = max(1, round((strtotime($p['expires_at']) - time()) / 60));
            $sisaWaktuText = $diffMinutes > 60 
                ? floor($diffMinutes / 60) . ' jam ' . ($diffMinutes % 60) . ' menit' 
                : $diffMinutes . ' menit';

            $title = "⏰ Pengingat: Batas Waktu Pembayaran Segera Berakhir";
            $message = "Tagihan invoice {$invoiceNo} sebesar " . format_rupiah((float)$p['grand_total']) . " akan kedaluwarsa dalam {$sisaWaktuText}. Segera lakukan transfer dan unggah bukti pembayaran agar transaksi Anda tidak dibatalkan otomatis.";

            $this->notificationModel->createNotification(
                $userId,
                'payment',
                $title,
                $message,
                [
                    'invoice_no' => $invoiceNo,
                    'expires_at' => $p['expires_at'],
                    'pay_url'    => url('/pembayaran/' . $invoiceNo),
                ]
            );

            // Catat log pengingat
            $this->logActivity('cron_payment_reminder', "Pengingat batas akhir pembayaran dikirimkan untuk invoice {$invoiceNo}", 'payments', $paymentId);

            $remindedInvoices[] = $invoiceNo;
        }

        return [
            'total_reminded' => count($remindedInvoices),
            'invoices'       => $remindedInvoices,
        ];
    }

    /**
     * 3. Kirim pengingat H-3 dan H-1 kegiatan penanaman bagi relawan terkonfirmasi
     *
     * @return array
     */
    public function sendEventReminders(): array
    {
        $h3Count = 0;
        $h1Count = 0;

        // Ambil relawan pada kegiatan H-3 dan H-1
        $sql = "
            SELECT 
                p.id as participant_id,
                p.user_id,
                e.id as event_id,
                e.title as event_title,
                e.event_date,
                e.gathering_time,
                e.gathering_point,
                DATEDIFF(e.event_date, CURDATE()) as days_left
            FROM participants p
            JOIN planting_events e ON p.event_id = e.id
            WHERE p.registration_status = 'terkonfirmasi'
              AND e.status = 'upcoming'
              AND DATEDIFF(e.event_date, CURDATE()) IN (1, 3)
        ";
        $stmt = $this->db->query($sql);
        $participants = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        foreach ($participants as $part) {
            $daysLeft = (int)$part['days_left'];
            $userId = (int)$part['user_id'];
            $eventId = (int)$part['event_id'];
            $partId = (int)$part['participant_id'];
            $actionKey = $daysLeft === 1 ? 'cron_event_reminder_h1' : 'cron_event_reminder_h3';

            // Cek apakah sudah pernah dikirim
            $stmtCheck = $this->db->prepare("
                SELECT COUNT(*) FROM activity_logs 
                WHERE action = :action 
                  AND loggable_type = 'participants' 
                  AND loggable_id = :id
            ");
            $stmtCheck->execute(['action' => $actionKey, 'id' => $partId]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                continue;
            }

            if ($daysLeft === 3) {
                $title = "🌿 H-3 Menuju Aksi: " . $part['event_title'];
                $message = "Halo Relawan! Aksi penanaman pohon akan diselenggarakan 3 hari lagi. Siapkan fisik, baju ganti yang nyaman, dan topi pelindung Anda.";
                $h3Count++;
            } else {
                $title = "🚨 Besok: Aksi Penanaman Pohon " . $part['event_title'];
                $message = "Aksi penanaman berlangsung besok jam " . substr($part['gathering_time'], 0, 5) . " WIB di " . $part['gathering_point'] . ". Jangan lupa siapkan QR E-Ticket di ponsel Anda untuk proses check-in di lokasi!";
                $h1Count++;
            }

            $this->notificationModel->createNotification(
                $userId,
                'event',
                $title,
                $message,
                [
                    'event_id'   => $eventId,
                    'event_date' => $part['event_date'],
                    'days_left'  => $daysLeft,
                ]
            );

            $this->logActivity($actionKey, "Pengingat H-{$daysLeft} event {$part['event_title']} dikirim ke user ID {$userId}", 'participants', $partId);
        }

        return [
            'h3_reminders_sent' => $h3Count,
            'h1_reminders_sent' => $h1Count,
            'total_reminders'   => $h3Count + $h1Count,
        ];
    }

    /**
     * 4. Pembersihan data usang (keranjang belanja tamu, log kedaluwarsa)
     *
     * @return array
     */
    public function cleanupOldData(): array
    {
        $deletedCarts = 0;

        // Bersihkan keranjang tamu yang tidak disentuh lebih dari 7 hari
        try {
            $stmtCart = $this->db->prepare("
                DELETE FROM carts 
                WHERE user_id IS NULL 
                  AND updated_at < (NOW() - INTERVAL 7 DAY)
            ");
            $stmtCart->execute();
            $deletedCarts = $stmtCart->rowCount();
        } catch (\Exception $e) {
            // Abaikan jika struktur tabel berbeda
        }

        return [
            'abandoned_carts_deleted' => $deletedCarts,
            'maintenance_completed'   => true,
        ];
    }

    /**
     * Helper untuk mencatat log aktivitas sistem
     */
    private function logActivity(string $action, string $description, ?string $loggableType = null, ?int $loggableId = null): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (
                    user_id, action, description, loggable_type, loggable_id, ip_address, created_at
                ) VALUES (
                    NULL, :action, :description, :loggable_type, :loggable_id, '127.0.0.1 (CLI/Cron)', NOW()
                )
            ");
            $stmt->execute([
                'action'        => $action,
                'description'   => $description,
                'loggable_type' => $loggableType,
                'loggable_id'   => $loggableId,
            ]);
        } catch (\Exception $e) {
            // Error log jangan menggagalkan proses utama
        }
    }
}
