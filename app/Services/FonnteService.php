<?php
/**
 * FonnteService — TanamPohon.id
 * Layanan integrasi Fonnte API untuk pengiriman pesan WhatsApp:
 * - OTP verifikasi nomor saat pendaftaran
 * - Notifikasi transaksi (pembayaran berhasil/ditolak/menunggu)
 * - Pesan selamat datang & pengingat event
 *
 * Docs: https://fonnte.com/api
 */
class FonnteService
{
    private string $token;
    private string $apiUrl  = 'https://api.fonnte.com/send';
    private bool   $enabled = false;

    public function __construct()
    {
        // Ambil token dari settings DB (lazy — tidak butuh DB di konstruktor)
        $this->token   = $this->getSetting('fonnte_token', '');
        $this->enabled = !empty($this->token) && $this->getSetting('fonnte_enabled', '0') === '1';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Kirim OTP WhatsApp ke nomor yang mendaftar.
     * Menyimpan kode ke session dengan TTL 5 menit.
     *
     * @param  string $phone  Nomor dalam format internasional (628...)
     * @param  string $name   Nama calon pengguna
     * @return array{success:bool, message:string, code?:string}
     */
    public function sendOtp(string $phone, string $name = ''): array
    {
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $sapa = $name ? "Halo *{$name}*,\n\n" : '';

        $msg  = "{$sapa}Kode OTP verifikasi WhatsApp Anda untuk mendaftar di *TanamPohon.id* adalah:\n\n"
              . "🔐 *{$code}*\n\n"
              . "Kode berlaku selama *5 menit*. Jangan bagikan kode ini kepada siapapun.\n\n"
              . "_Jika Anda tidak mendaftar, abaikan pesan ini._";

        $result = $this->send($phone, $msg);

        if ($result['success'] || !$this->enabled) {
            // Simpan ke session: kode, phone, waktu kirim
            $_SESSION['wa_otp_code']  = password_hash($code, PASSWORD_BCRYPT, ['cost' => 8]);
            $_SESSION['wa_otp_phone'] = $phone;
            $_SESSION['wa_otp_time']  = time();
            $_SESSION['wa_otp_tries'] = 0;

            // Mode dev: kembalikan kode juga agar bisa ditampilkan
            if (!$this->enabled) {
                return ['success' => true, 'message' => 'Mode dev: OTP tidak terkirim.', 'dev_code' => $code];
            }
            return ['success' => true, 'message' => 'Kode OTP telah dikirim ke WhatsApp Anda.'];
        }

        return ['success' => false, 'message' => $result['message'] ?? 'Gagal mengirim OTP. Periksa nomor WhatsApp Anda.'];
    }

    /**
     * Verifikasi kode OTP yang dimasukkan user.
     * Maks 5 percobaan, TTL 5 menit.
     */
    public function verifyOtp(string $inputCode, string $phone): array
    {
        if (empty($_SESSION['wa_otp_code'])) {
            return ['success' => false, 'message' => 'Sesi OTP tidak ditemukan. Silakan minta kode baru.'];
        }

        // Cek kedaluwarsa (5 menit)
        if ((time() - ($_SESSION['wa_otp_time'] ?? 0)) > 300) {
            $this->clearOtpSession();
            return ['success' => false, 'message' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.'];
        }

        // Cek nomor cocok
        if (($_SESSION['wa_otp_phone'] ?? '') !== $phone) {
            return ['success' => false, 'message' => 'Nomor WhatsApp tidak cocok dengan yang diverifikasi.'];
        }

        // Cek batas percobaan (5x)
        $tries = (int)($_SESSION['wa_otp_tries'] ?? 0);
        if ($tries >= 5) {
            $this->clearOtpSession();
            return ['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan minta kode OTP baru.'];
        }

        $_SESSION['wa_otp_tries'] = $tries + 1;

        if (!password_verify(trim($inputCode), $_SESSION['wa_otp_code'])) {
            $sisa = 5 - ($_SESSION['wa_otp_tries']);
            return ['success' => false, 'message' => "Kode OTP salah. Sisa percobaan: {$sisa}x."];
        }

        // Sukses — tandai sesi terverifikasi
        $this->clearOtpSession();
        $_SESSION['wa_otp_verified'] = $phone;
        return ['success' => true, 'message' => 'Nomor WhatsApp berhasil diverifikasi!'];
    }

    /**
     * Cek apakah nomor sudah terverifikasi OTP di sesi ini.
     */
    public function isOtpVerified(string $phone): bool
    {
        return ($_SESSION['wa_otp_verified'] ?? '') === $phone;
    }

    /** Hapus status verified dari sesi (setelah register selesai) */
    public function clearVerified(): void
    {
        unset($_SESSION['wa_otp_verified']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // NOTIFIKASI TRANSAKSI
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Kirim notifikasi WhatsApp otomatis berdasarkan ID pembayaran dan jenis event.
     * $eventType: 'created' (invoice baru), 'proof_uploaded' (bukti diunggah), 'approved' (disetujui), 'rejected' (ditolak)
     */
    public function notifyByPaymentId(int $paymentId, string $eventType, array $extra = []): bool
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM payments WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $paymentId]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment) return false;

            $customerName  = '';
            $customerPhone = '';
            $itemDesc      = '';
            $ticketCode    = '';

            // Cari nama dan nomor telepon berdasarkan payable_type
            if ($payment['payable_type'] === 'donation') {
                $s = $db->prepare("SELECT * FROM donations WHERE id = :id LIMIT 1");
                $s->execute([':id' => $payment['payable_id']]);
                $d = $s->fetch(PDO::FETCH_ASSOC);
                if ($d) {
                    $customerName  = $d['donor_name'];
                    $customerPhone = $d['donor_phone'];
                    $itemDesc      = "Donasi " . $d['total_trees'] . " Pohon";
                }
            } elseif ($payment['payable_type'] === 'order') {
                $s = $db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
                $s->execute([':id' => $payment['payable_id']]);
                $o = $s->fetch(PDO::FETCH_ASSOC);
                if ($o) {
                    $snap = json_decode($o['shipping_address_snapshot'] ?? '{}', true);
                    $customerName  = $snap['recipient_name'] ?? '';
                    $customerPhone = $snap['phone'] ?? '';
                    $itemDesc      = "Pesanan Kaos/Produk #" . ($o['order_number'] ?? $o['id']);
                }
            } elseif ($payment['payable_type'] === 'participant') {
                $s = $db->prepare("
                    SELECT p.*, u.name, u.phone, e.title AS event_title, e.event_date, e.gathering_point, t.ticket_code
                    FROM participants p
                    JOIN users u ON u.id = p.user_id
                    JOIN planting_events e ON e.id = p.event_id
                    LEFT JOIN tickets t ON t.participant_id = p.id
                    WHERE p.id = :id
                    LIMIT 1
                ");
                $s->execute([':id' => $payment['payable_id']]);
                $p = $s->fetch(PDO::FETCH_ASSOC);
                if ($p) {
                    $customerName  = $p['name'];
                    $customerPhone = $p['phone'];
                    $itemDesc      = "Pendaftaran Event: " . $p['event_title'];
                    $ticketCode    = $p['ticket_code'] ?? '';
                }
            }

            // Fallback ke user_id jika phone masih kosong
            if (empty($customerPhone) && !empty($payment['user_id'])) {
                $su = $db->prepare("SELECT name, phone FROM users WHERE id = :id LIMIT 1");
                $su->execute([':id' => $payment['user_id']]);
                $u = $su->fetch(PDO::FETCH_ASSOC);
                if ($u) {
                    if (empty($customerName)) $customerName = $u['name'];
                    $customerPhone = $u['phone'];
                }
            }

            if (empty($customerPhone)) return false;

            $totalRp = 'Rp ' . number_format((float)$payment['grand_total'], 0, ',', '.');
            $siteUrl = function_exists('url') ? url('/') : 'http://localhost:8000';
            $invLink = $siteUrl . '/pembayaran/' . $payment['invoice_no'];

            $msg = '';
            if ($eventType === 'created') {
                // Ambil info rekening aktif
                $banks = $db->query("SELECT bank_name, account_number, account_holder FROM bank_accounts WHERE is_active = 1 LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
                $bankText = "";
                foreach ($banks as $b) {
                    $bankText .= "• *{$b['bank_name']}*: `{$b['account_number']}` a.n. {$b['account_holder']}\n";
                }

                $msg = "🌱 *Tagihan Pembayaran Baru*\n\n"
                     . "Halo *{$customerName}*,\n"
                     . "Transaksi Anda telah berhasil dibuat. Silakan transfer sesuai nominal berikut (termasuk kode unik jika ada):\n\n"
                     . "📋 *Rincian Tagihan:*\n"
                     . "• Invoice: `{$payment['invoice_no']}`\n"
                     . "• Keperluan: {$itemDesc}\n"
                     . "• Total Transfer: *{$totalRp}*\n"
                     . "• Batas Waktu: " . ($payment['expires_at'] ? date('d M Y H:i', strtotime($payment['expires_at'])) : '24 Jam') . " WIB\n\n"
                     . "💳 *Rekening Pembayaran:*\n{$bankText}\n"
                     . "Setelah transfer, mohon unggah bukti pembayaran di tautan berikut:\n"
                     . "🔗 {$invLink}\n\n"
                     . "_TanamPohon.id_";
            } elseif ($eventType === 'proof_uploaded') {
                $msg = "⏳ *Bukti Pembayaran Diterima*\n\n"
                     . "Halo *{$customerName}*,\n"
                     . "Bukti transfer untuk invoice `{$payment['invoice_no']}` ({$totalRp}) telah kami terima dan sedang diverifikasi oleh tim admin kami.\n\n"
                     . "Kami akan memberi tahu Anda setelah verifikasi selesai.\n\n"
                     . "🔗 Cek status: {$invLink}\n\n"
                     . "_TanamPohon.id_";
            } elseif ($eventType === 'approved') {
                $extraTicket = $ticketCode ? "\n🎫 *Kode Tiket Anda:* `{$ticketCode}`\n" : "";
                $msg = "✅ *Pembayaran Terverifikasi & Berhasil!*\n\n"
                     . "Halo *{$customerName}*,\n"
                     . "Pembayaran Anda untuk invoice `{$payment['invoice_no']}` ({$itemDesc}) senilai *{$totalRp}* telah *berhasil diverifikasi*!\n"
                     . $extraTicket . "\n"
                     . "Terima kasih atas kontribusi nyata Anda untuk kelestarian alam dan ekosistem hijau Indonesia! 🌱\n\n"
                     . "🔗 Lihat Invoice: {$invLink}\n\n"
                     . "_TanamPohon.id_";
            } elseif ($eventType === 'rejected') {
                $reason = $extra['reason'] ?? 'Bukti transfer tidak sesuai atau dana belum masuk.';
                $msg = "❌ *Pembayaran Belum Dapat Diverifikasi*\n\n"
                     . "Halo *{$customerName}*,\n"
                     . "Mohon maaf, bukti pembayaran untuk invoice `{$payment['invoice_no']}` ({$totalRp}) belum dapat disetujui.\n\n"
                     . "📝 *Alasan:* {$reason}\n\n"
                     . "Silakan periksa kembali dan unggah ulang bukti transfer yang valid melalui:\n"
                     . "🔗 {$invLink}\n\n"
                     . "Atau hubungi admin kami jika ada kendala.\n\n"
                     . "_TanamPohon.id_";
            }

            if (!empty($msg)) {
                return $this->send($customerPhone, $msg)['success'];
            }
            return false;
        } catch (\Throwable $e) {
            error_log("[FonnteService Error] " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifikasi pembayaran menunggu verifikasi (konfirmasi penerimaan bukti).
     */
    public function notifyPaymentPending(array $payment, array $user): bool
    {
        $msg = "⏳ *Bukti Pembayaran Diterima*\n\n"
             . "Halo *{$user['name']}*, bukti pembayaran Anda telah kami terima dan sedang dalam proses verifikasi.\n\n"
             . "📋 *Detail Transaksi:*\n"
             . "• Invoice: `{$payment['invoice_no']}`\n"
             . "• Nominal: Rp " . number_format((float)$payment['grand_total'], 0, ',', '.') . "\n"
             . "• Status: ⏳ *Menunggu Verifikasi*\n\n"
             . "Proses verifikasi biasanya selesai dalam 1×24 jam kerja.\n\n"
             . "_TanamPohon.id_";

        return $this->send($user['phone'], $msg)['success'];
    }

    /**
     * Notifikasi selamat datang setelah daftar berhasil.
     */
    public function notifyWelcome(string $phone, string $name): bool
    {
        $msg = "🌱 *Selamat datang di TanamPohon.id!*\n\n"
             . "Halo *{$name}*, akun Anda berhasil dibuat.\n\n"
             . "Bersama kami, Anda bisa:\n"
             . "🌳 Donasi penanaman pohon\n"
             . "🎒 Ikut kegiatan ekspedisi penanaman\n"
             . "☕ Belanja kopi spesialti Kerenceng\n"
             . "👕 Dapat merchandise eksklusif\n\n"
             . "Mari mulai perjalanan hijau Anda! 💚\n"
             . "🔗 " . (function_exists('url') ? url('/dashboard') : 'http://localhost:8000/dashboard') . "\n\n"
             . "_TanamPohon.id_";

        return $this->send($phone, $msg)['success'];
    }

    /**
     * Notifikasi peserta event terkonfirmasi (setelah pembayaran event disetujui).
     */
    public function notifyEventConfirmed(array $user, array $event, string $ticketCode): bool
    {
        $tgl = date('d M Y', strtotime($event['event_date'] ?? 'now'));
        $msg = "🎉 *Pendaftaran Event Dikonfirmasi!*\n\n"
             . "Halo *{$user['name']}*, pendaftaran Anda untuk kegiatan berikut telah DIKONFIRMASI!\n\n"
             . "📌 *" . ($event['title'] ?? 'Kegiatan Penanaman') . "*\n"
             . "📅 Tanggal: {$tgl}\n"
             . "📍 Lokasi: " . ($event['gathering_point'] ?? '-') . "\n\n"
             . "🎫 *Kode Tiket Anda:*\n"
             . "`{$ticketCode}`\n\n"
             . "Simpan kode tiket ini untuk check-in di hari kegiatan.\n\n"
             . "_TanamPohon.id_";

        return $this->send($user['phone'], $msg)['success'];
    }

    /**
     * Test koneksi — kirim pesan uji ke nomor admin.
     */
    public function testConnection(string $targetPhone): array
    {
        $msg = "✅ *Tes Koneksi Berhasil!*\n\n"
             . "Fonnte API terhubung dengan baik ke TanamPohon.id.\n"
             . "Waktu: " . date('d M Y H:i:s') . " WIB\n\n"
             . "_Pesan ini dikirim otomatis dari panel admin._";

        return $this->send($targetPhone, $msg);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CORE SEND
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Kirim pesan WhatsApp via Fonnte API.
     *
     * @param  string $phone  Nomor tujuan (628xxx atau 08xxx — auto-normalize)
     * @param  string $message Teks pesan (mendukung *bold*, _italic_)
     * @return array{success:bool, message:string, response?:array}
     */
    public function send(string $phone, string $message): array
    {
        if (!$this->enabled) {
            // Mode dev: log ke file, tidak benar-benar terkirim
            error_log("[FonnteService DEV] To: {$phone} | Msg: " . substr($message, 0, 80) . '...');
            return ['success' => true, 'message' => 'Mode dev: pesan tidak dikirim (Fonnte dinonaktifkan).'];
        }

        $normalized = $this->normalizePhone($phone);
        if (!$normalized) {
            return ['success' => false, 'message' => 'Nomor WhatsApp tidak valid: ' . $phone];
        }

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_POSTFIELDS     => http_build_query([
                'target'  => $normalized,
                'message' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->token,
            ],
        ]);

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['success' => false, 'message' => 'cURL error: ' . $curlErr];
        }

        $decoded = json_decode($raw, true);

        if ($httpCode === 200 && isset($decoded['status']) && $decoded['status'] === true) {
            return ['success' => true, 'message' => 'Pesan terkirim.', 'response' => $decoded];
        }

        $errMsg = $decoded['reason'] ?? $decoded['message'] ?? "HTTP {$httpCode}";
        return ['success' => false, 'message' => 'Gagal mengirim pesan: ' . $errMsg, 'response' => $decoded];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normalisasi nomor ke format internasional 628xxx tanpa +.
     */
    public static function normalizePhone(string $phone): ?string
    {
        $p = preg_replace('/\D/', '', $phone);
        if (empty($p)) return null;

        if (str_starts_with($p, '0')) {
            $p = '62' . substr($p, 1);
        } elseif (str_starts_with($p, '8')) {
            $p = '62' . $p;
        } elseif (!str_starts_with($p, '62')) {
            $p = '62' . $p;
        }

        // Panjang minimal 10 digit setelah 62
        return strlen($p) >= 10 ? $p : null;
    }

    private function clearOtpSession(): void
    {
        unset(
            $_SESSION['wa_otp_code'],
            $_SESSION['wa_otp_phone'],
            $_SESSION['wa_otp_time'],
            $_SESSION['wa_otp_tries']
        );
    }

    private function getSetting(string $key, string $default = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $db    = Database::getInstance()->getConnection();
                $rows  = $db->query("SELECT `key`, `value` FROM settings")->fetchAll(PDO::FETCH_ASSOC);
                $cache = array_column($rows, 'value', 'key');
            } catch (\Throwable $e) {
                $cache = [];
            }
        }
        return (string)($cache[$key] ?? $default);
    }
}

