<?php
/**
 * TanamPohon.id - Front Controller
 * 
 * File bootstrap utama aplikasi.
 * Semua request HTTP masuk melalui file ini.
 */

// Tampilkan error saat development (matikan di production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Global exception handler — log ke file dan tampilkan halaman error ramah pengguna
set_exception_handler(function (Throwable $e) {
    $logDir  = dirname(__DIR__) . '/storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    $logFile = $logDir . '/app.log';
    $ts      = date('Y-m-d H:i:s');
    $uri     = $_SERVER['REQUEST_URI'] ?? '-';
    $msg     = "[{$ts}] EXCEPTION on {$uri}: " . $e->getMessage()
             . " in " . $e->getFile() . ":" . $e->getLine()
             . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);

    http_response_code(500);
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
    // Tampilkan detail hanya di mode debug
    $debug = ($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? 'false') === 'true';
    if ($debug) {
        echo '<pre style="font-family:monospace;background:#1a1a1a;color:#f88;padding:2em;">';
        echo htmlspecialchars($e->getMessage()) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">'
           . '<title>Terjadi Kesalahan</title>'
           . '<meta name="viewport" content="width=device-width,initial-scale=1">'
           . '<style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f4f7f4}'
           . '.box{text-align:center;padding:2em;max-width:500px}'
           . 'h1{color:#12382c;font-size:3rem;margin-bottom:.5rem}p{color:#666}'
           . 'a{color:#12382c;font-weight:bold}</style></head>'
           . '<body><div class="box"><h1>😕 500</h1>'
           . '<p>Terjadi kesalahan pada sistem. Tim kami sudah diberitahu.</p>'
           . '<p><a href="/">← Kembali ke Beranda</a></p>'
           . '</div></body></html>';
    }
    exit;
});

// Definisikan path dasar aplikasi
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('VIEW_PATH', BASE_PATH . '/resources/views');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', __DIR__);
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

// Muat autoloader Composer jika ada
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

// Muat file .env
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Lewati komentar
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        // Parse variabel environment
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Hapus kutip jika ada
            $value = trim($value, '"\'');
            // Set ke environment
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Muat helper global
require_once APP_PATH . '/Helpers/helpers.php';

// Set timezone ke WIB (Waktu Indonesia Barat)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi session yang aman
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
if (env('APP_ENV') === 'production') {
    ini_set('session.cookie_secure', 1);
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Muat file inti framework
require_once CORE_PATH . '/App.php';
require_once CORE_PATH . '/Router.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Request.php';
require_once CORE_PATH . '/Response.php';
require_once CORE_PATH . '/Session.php';
require_once CORE_PATH . '/Validator.php';

// Inisialisasi aplikasi
$app = App::getInstance();

// Inisialisasi router
$router = new Router();

// Muat definisi route
require_once BASE_PATH . '/routes/web.php';

// Jalankan routing berdasarkan request saat ini
$router->dispatch();
