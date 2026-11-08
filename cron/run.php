<?php
/**
 * Runner Cron Job CLI — TanamPohon.id
 * 
 * Penggunaan di CLI / Crontab:
 *   php /path/to/cocok_tanam/cron/run.php          (Jalankan semua tugas)
 *   php /path/to/cocok_tanam/cron/run.php --expire (Hanya batalkan transaksi kedaluwarsa)
 *   php /path/to/cocok_tanam/cron/run.php --reminder (Hanya pengingat pembayaran)
 *   php /path/to/cocok_tanam/cron/run.php --events (Hanya pengingat event relawan)
 *   php /path/to/cocok_tanam/cron/run.php --cleanup (Hanya pembersihan data usang)
 */

if (php_sapi_name() !== 'cli' && empty($_SERVER['argv'])) {
    http_response_code(403);
    echo "Akses ditolak: File ini hanya dapat dieksekusi melalui terminal CLI.\n";
    exit(1);
}

// Inisialisasi dasar aplikasi
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('VIEW_PATH', BASE_PATH . '/resources/views');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Muat autoloader jika ada
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Muat environment (.env)
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($k, $_ENV)) {
                $_ENV[$k] = $v;
                putenv("$k=$v");
            }
        }
    }
}

// Muat helpers dan core
require_once APP_PATH . '/Helpers/helpers.php';
date_default_timezone_set('Asia/Jakarta');

require_once CORE_PATH . '/App.php';
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Model.php';
require_once APP_PATH . '/Services/CronService.php';

$app = App::getInstance();
$cron = new CronService();

// Parse opsi argumen CLI
$args = array_slice($argv ?? [], 1);
$mode = 'all';

if (in_array('--expire', $args)) {
    $mode = 'expire';
} elseif (in_array('--reminder', $args)) {
    $mode = 'reminder';
} elseif (in_array('--events', $args)) {
    $mode = 'events';
} elseif (in_array('--cleanup', $args)) {
    $mode = 'cleanup';
}

echo "=================================================================\n";
echo "🌱 TanamPohon.id — Task Runner & Automated Maintenance\n";
echo "Waktu Server : " . date('Y-m-d H:i:s') . " WIB\n";
echo "Mode Eksekusi: " . strtoupper($mode) . "\n";
echo "=================================================================\n\n";

$startTime = microtime(true);

switch ($mode) {
    case 'expire':
        echo "[1/1] Memproses pembatalan pembayaran kedaluwarsa & pelepasan stok/kuota...\n";
        $res = $cron->cancelExpiredPayments();
        echo "      > Transaksi dibatalkan : " . $res['total_cancelled'] . "\n";
        echo "      > Stok kaos dipulihkan : " . $res['restored_stock_items'] . "\n";
        echo "      > Kuota event dilepas  : " . $res['restored_event_quotas'] . "\n";
        break;

    case 'reminder':
        echo "[1/1] Memeriksa tagihan mendekati batas kedaluwarsa (<= 3 jam)...\n";
        $res = $cron->sendPaymentReminders();
        echo "      > Pengingat dikirimkan : " . $res['total_reminded'] . "\n";
        break;

    case 'events':
        echo "[1/1] Mengirimkan pengingat H-3 dan H-1 kegiatan penanaman relawan...\n";
        $res = $cron->sendEventReminders();
        echo "      > Pengingat H-3 terkirim: " . $res['h3_reminders_sent'] . "\n";
        echo "      > Pengingat H-1 terkirim: " . $res['h1_reminders_sent'] . "\n";
        echo "      > Total pengingat       : " . $res['total_reminders'] . "\n";
        break;

    case 'cleanup':
        echo "[1/1] Membersihkan data usang & log pemeliharaan...\n";
        $res = $cron->cleanupOldData();
        echo "      > Keranjang tamu dihapus: " . $res['abandoned_carts_deleted'] . "\n";
        break;

    case 'all':
    default:
        echo "[1/4] Membatalkan pembayaran kedaluwarsa & melepas reservasi...\n";
        $res1 = $cron->cancelExpiredPayments();
        echo "      > " . $res1['total_cancelled'] . " transaksi dibatalkan (" . $res1['restored_stock_items'] . " stok, " . $res1['restored_event_quotas'] . " kuota dipulihkan)\n\n";

        echo "[2/4] Mengirim pengingat batas waktu pembayaran (<= 3 jam)...\n";
        $res2 = $cron->sendPaymentReminders();
        echo "      > " . $res2['total_reminded'] . " pengingat pembayaran terkirim\n\n";

        echo "[3/4] Mengirim pengingat H-3 dan H-1 kegiatan relawan...\n";
        $res3 = $cron->sendEventReminders();
        echo "      > " . $res3['total_reminders'] . " pengingat jadwal kegiatan relawan terkirim\n\n";

        echo "[4/4] Pembersihan sampah data & keranjang tamu usang...\n";
        $res4 = $cron->cleanupOldData();
        echo "      > " . $res4['abandoned_carts_deleted'] . " keranjang tamu usang dibersihkan\n\n";
        break;
}

$cron->recordRunTimestamp();

$elapsed = round(microtime(true) - $startTime, 4);
echo "-----------------------------------------------------------------\n";
echo "✅ Cron Job Selesai dalam {$elapsed} detik.\n";
echo "=================================================================\n";
exit(0);
