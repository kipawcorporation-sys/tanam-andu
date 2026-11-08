<?php
/**
 * TanamPohon.id - Manajer Koneksi Database
 * 
 * Singleton pattern untuk koneksi PDO.
 * Konfigurasi diambil dari file .env.
 */

class Database
{
    /** @var Database|null Instance singleton */
    private static ?Database $instance = null;

    /** @var PDO Koneksi PDO */
    private PDO $connection;

    /**
     * Konstruktor privat - buat koneksi database
     * Konfigurasi diambil dari environment variables
     */
    private function __construct()
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', 'tanampohon_db');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $charset = env('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

        try {
            $this->connection = new PDO($dsn, $username, $password, [
                // Mode error: lempar exception
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Mode fetch default: associative array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Nonaktifkan emulasi prepared statements (keamanan lebih baik)
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Gunakan koneksi persistent untuk performa
                PDO::ATTR_PERSISTENT         => false,
            ]);

            // Set timezone MySQL ke WIB
            $this->connection->exec("SET time_zone = '+07:00'");

        } catch (PDOException $e) {
            // Log error tanpa menampilkan detail sensitif ke user
            error_log("Koneksi database gagal: " . $e->getMessage());

            if (env('APP_DEBUG', 'false') === 'true') {
                die("Koneksi database gagal: " . $e->getMessage());
            } else {
                die("Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.");
            }
        }
    }

    /**
     * Mendapatkan instance singleton
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Mendapatkan objek koneksi PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Eksekusi query dengan prepared statement
     *
     * @param string $sql Query SQL
     * @param array $params Parameter untuk binding
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Mendapatkan ID terakhir yang diinsert
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Mulai transaksi database
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaksi
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback transaksi
     */
    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Cek apakah sedang dalam transaksi
     */
    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }

    /** Cegah cloning */
    private function __clone() {}

    /** Cegah unserialization */
    public function __wakeup()
    {
        throw new \Exception("Tidak dapat melakukan unserialize singleton Database");
    }
}
