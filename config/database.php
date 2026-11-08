<?php
/**
 * TanamPohon.id - Konfigurasi Database
 * 
 * Pengaturan koneksi database MySQL/MariaDB.
 * Semua nilai diambil dari file .env.
 */

return [
    // Driver database (hanya MySQL/MariaDB yang didukung)
    'driver' => 'mysql',

    // Host database
    'host' => env('DB_HOST', 'localhost'),

    // Port database
    'port' => env('DB_PORT', '3306'),

    // Nama database
    'database' => env('DB_DATABASE', 'tanampohon_db'),

    // Username database
    'username' => env('DB_USERNAME', 'root'),

    // Password database
    'password' => env('DB_PASSWORD', ''),

    // Charset koneksi
    'charset' => env('DB_CHARSET', 'utf8mb4'),

    // Collation
    'collation' => 'utf8mb4_unicode_ci',

    // Prefix tabel (opsional, untuk shared hosting)
    'prefix' => env('DB_PREFIX', ''),

    // Opsi PDO tambahan
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
