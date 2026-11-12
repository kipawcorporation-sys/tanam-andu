<?php
/**
 * TanamPohon.id - Konfigurasi Aplikasi
 * 
 * Pengaturan umum aplikasi.
 * Nilai default diambil dari file .env.
 */

return [
    // Nama aplikasi
    'name' => env('APP_NAME', 'Plant For Fun'),

    // Tagline aplikasi
    'tagline' => env('APP_TAGLINE', 'Donasi Pohon untuk Bumi yang Lebih Hijau'),

    // URL dasar aplikasi
    'url' => env('APP_URL', 'http://localhost/cocok_tanam/public'),

    // Environment: local, staging, production
    'env' => env('APP_ENV', 'local'),

    // Mode debug (tampilkan error detail)
    'debug' => env('APP_DEBUG', 'true') === 'true',

    // Timezone aplikasi
    'timezone' => 'Asia/Jakarta',

    // Locale default
    'locale' => 'id',

    // Mata uang
    'currency' => 'IDR',
    'currency_symbol' => 'Rp',

    // Versi aplikasi
    'version' => '1.0.0',

    // Penandatangan Sertifikat Resmi
    'certificate_signer_name'  => env('CERTIFICATE_SIGNER_NAME', 'andu coffee'),
    'certificate_signer_title' => env('CERTIFICATE_SIGNER_TITLE', 'Plant For Fun • Kerenceng Expedition'),

    // Email admin
    'admin_email' => env('ADMIN_EMAIL', 'admin@tanampohon.id'),

    // Nomor WhatsApp admin (untuk notifikasi)
    'admin_whatsapp' => env('ADMIN_WHATSAPP', ''),

    // Batas upload file (dalam bytes)
    'max_upload_size' => (int) env('MAX_UPLOAD_SIZE', 5242880), // 5MB

    // Tipe file gambar yang diizinkan
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp'],

    // Tipe file dokumen yang diizinkan
    'allowed_doc_types' => ['application/pdf', 'image/jpeg', 'image/png'],

    // Jumlah item per halaman (pagination)
    'per_page' => (int) env('PER_PAGE', 12),

    // Konfigurasi rekening bank untuk pembayaran
    'payment' => [
        'banks' => [
            [
                'bank'           => env('BANK_NAME_1', 'Bank BCA'),
                'nomor_rekening' => env('BANK_ACCOUNT_1', ''),
                'atas_nama'      => env('BANK_HOLDER_1', ''),
                'logo'           => 'bca.png',
            ],
            [
                'bank'           => env('BANK_NAME_2', 'Bank Mandiri'),
                'nomor_rekening' => env('BANK_ACCOUNT_2', ''),
                'atas_nama'      => env('BANK_HOLDER_2', ''),
                'logo'           => 'mandiri.png',
            ],
            [
                'bank'           => env('BANK_NAME_3', 'Bank BNI'),
                'nomor_rekening' => env('BANK_ACCOUNT_3', ''),
                'atas_nama'      => env('BANK_HOLDER_3', ''),
                'logo'           => 'bni.png',
            ],
        ],
        // Batas waktu pembayaran (dalam jam)
        'deadline_hours' => (int) env('PAYMENT_DEADLINE_HOURS', 24),
        // QRIS (jika tersedia)
        'qris_image' => env('QRIS_IMAGE', ''),
    ],

    // Konfigurasi email (SMTP)
    'mail' => [
        'host'       => env('MAIL_HOST', 'smtp.gmail.com'),
        'port'       => (int) env('MAIL_PORT', 587),
        'username'   => env('MAIL_USERNAME', ''),
        'password'   => env('MAIL_PASSWORD', ''),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'from_name'  => env('MAIL_FROM_NAME', 'Plant For Fun'),
        'from_email' => env('MAIL_FROM_ADDRESS', 'noreply@plantforfun.id'),
    ],

    // Konfigurasi WhatsApp Gateway
    'whatsapp' => [
        'api_url' => env('WA_API_URL', ''),
        'api_key' => env('WA_API_KEY', ''),
        'sender'  => env('WA_SENDER', ''),
    ],

    // Konfigurasi RajaOngkir (untuk kalkulasi ongkir)
    'rajaongkir' => [
        'api_key' => env('RAJAONGKIR_API_KEY', ''),
        'type'    => env('RAJAONGKIR_TYPE', 'starter'), // starter, basic, pro
    ],

    // Meta default untuk SEO
    'meta' => [
        'description' => 'Plant For Fun - Platform penanaman pohon dan agroforestri Kerenceng Coffee Planting & Expedition',
        'keywords'    => 'donasi pohon, tanam pohon, adopsi pohon, lingkungan, reboisasi, go green, Indonesia hijau',
        'author'      => 'Plant For Fun',
        'og_image'    => '/assets/images/og-image.jpg',
    ],
];
