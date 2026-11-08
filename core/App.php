<?php
/**
 * TanamPohon.id - Kelas Aplikasi Utama
 * 
 * Singleton pattern untuk mengelola instance aplikasi,
 * koneksi database, dan konfigurasi global.
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}
if (!defined('CORE_PATH')) {
    define('CORE_PATH', BASE_PATH . '/core');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', BASE_PATH . '/config');
}
if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', BASE_PATH . '/resources/views');
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', BASE_PATH . '/storage');
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
}

class App
{
    /** @var App|null Instance singleton */
    private static ?App $instance = null;

    /** @var Database|null Koneksi database */
    private ?Database $db = null;

    /** @var array Konfigurasi aplikasi */
    private array $config = [];

    /**
     * Konstruktor privat (singleton pattern)
     * Memuat semua konfigurasi saat inisialisasi
     */
    private function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Mendapatkan instance singleton aplikasi
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Muat semua file konfigurasi dari folder config/
     */
    private function loadConfig(): void
    {
        $configFiles = glob(CONFIG_PATH . '/*.php');
        foreach ($configFiles as $file) {
            $key = basename($file, '.php');
            $this->config[$key] = require $file;
        }
    }

    /**
     * Ambil nilai konfigurasi menggunakan dot notation
     * Contoh: config('app.name') atau config('database.host')
     *
     * @param string $key Kunci konfigurasi (dot notation)
     * @param mixed $default Nilai default jika kunci tidak ditemukan
     * @return mixed
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Mendapatkan koneksi database (lazy loading)
     */
    public function getDatabase(): Database
    {
        if ($this->db === null) {
            $this->db = Database::getInstance();
        }
        return $this->db;
    }

    /**
     * Mendapatkan koneksi PDO langsung
     */
    public function getConnection(): PDO
    {
        return $this->getDatabase()->getConnection();
    }

    /**
     * Cegah cloning instance
     */
    private function __clone() {}

    /**
     * Cegah unserialization instance
     */
    public function __wakeup()
    {
        throw new \Exception("Tidak dapat melakukan unserialize singleton");
    }
}
