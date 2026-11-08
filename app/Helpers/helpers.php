<?php
/**
 * TanamPohon.id - Global Helper Functions
 * 
 * Kumpulan fungsi bantuan global yang dapat dipanggil dari mana saja
 * (controller, model, view, dsb).
 */

if (!function_exists('env')) {
    /**
     * Ambil nilai dari environment variable
     *
     * @param string $key Kunci env
     * @param mixed $default Nilai default jika kunci tidak ditemukan
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        // Konversi tipe string ke boolean/null jika cocok
        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (!function_exists('config')) {
    /**
     * Ambil nilai konfigurasi menggunakan dot notation
     * Contoh: config('app.name') atau config('database.host')
     *
     * @param string $key Kunci konfigurasi
     * @param mixed $default Nilai default
     * @return mixed
     */
    function config(string $key, mixed $default = null): mixed
    {
        return App::getInstance()->config($key, $default);
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL lengkap aplikasi
     * Mendukung auto-detect domain, reverse proxy (Cloudflare/Nginx), dan HTTPS
     *
     * @param string $path Path relatif
     * @return string
     */
    function url(string $path = ''): string
    {
        $path = ltrim($path, '/');

        // Jika path sudah berupa URL absolut eksternal, kembalikan langsung
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $baseUrl = rtrim(env('APP_URL', ''), '/');

        // Auto-detect host & protokol jika:
        // 1. APP_URL kosong
        // 2. APP_URL masih placeholder (misal: IP_SERVER atau IP_PROXMOX)
        // 3. Request masuk via HTTP_HOST yang berbeda dari konfigurasi
        if (empty($baseUrl) || str_contains($baseUrl, 'IP_SERVER') || str_contains($baseUrl, 'IP_PROXMOX') || (isset($_SERVER['HTTP_HOST']) && !str_contains($baseUrl, $_SERVER['HTTP_HOST']))) {
            if (!empty($_SERVER['HTTP_HOST'])) {
                $isHttps = (
                    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                    ($_SERVER['SERVER_PORT'] ?? 80) == 443 ||
                    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
                    (!empty($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'))
                );
                $protocol = $isHttps ? 'https://' : 'http://';
                $baseUrl = $protocol . $_SERVER['HTTP_HOST'];
            } else {
                $baseUrl = 'http://localhost:8000';
            }
        }

        return $path === '' ? $baseUrl : $baseUrl . '/' . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL aset publik (CSS, JS, gambar)
     * Menggunakan path root-relatif (/assets/...) agar selalu valid
     * pada semua protokol (HTTP/HTTPS) dan bebas dari kendala Mixed Content.
     *
     * @param string $path Path relatif di dalam folder public/assets/
     * @return string
     */
    function asset(string $path): string
    {
        $path = ltrim($path, '/');

        // Jika URL eksternal (CDN), kembalikan langsung
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        // Jika path sudah diawali dengan 'assets/', kembalikan /assets/...
        if (str_starts_with($path, 'assets/')) {
            return '/' . $path;
        }

        return '/assets/' . $path;
    }
}

if (!function_exists('storage_url')) {
    /**
     * Generate URL untuk file yang dapat diakses publik dari storage
     * Catatan: bukti transfer TIDAK boleh menggunakan helper ini karena harus lewat route privat
     *
     * @param string $path Path relatif di dalam storage/uploads/
     * @return string
     */
    function storage_url(string $path): string
    {
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }

        return '/storage/' . $path;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Ambil atau generate token CSRF
     *
     * @return string
     */
    function csrf_token(): string
    {
        return Session::getCsrfToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate input hidden untuk CSRF token
     *
     * @return string HTML tag input
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Ambil nilai input lama dari request sebelumnya
     *
     * @param string $key Nama field
     * @param mixed $default Nilai default
     * @return mixed
     */
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::oldInput($key, $default);
    }
}

if (!function_exists('flash')) {
    /**
     * Tampilkan atau periksa flash message
     *
     * @param string|null $type Tipe pesan (success, error, warning, info)
     * @return array|string|null
     */
    function flash(?string $type = null): mixed
    {
        if ($type === null) {
            return Session::getAllFlash();
        }
        return Session::getFlash($type);
    }
}

if (!function_exists('auth')) {
    /**
     * Ambil data user yang sedang login
     *
     * @param string|null $field Kolom spesifik (id, nama, email, role, avatar)
     * @return mixed
     */
    function auth(?string $field = null): mixed
    {
        $user = Session::getUser();
        if (!$user) {
            return null;
        }
        return $field ? ($user[$field] ?? null) : $user;
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Cek apakah pengguna saat ini sudah login
     *
     * @return bool
     */
    function is_logged_in(): bool
    {
        return Session::isLoggedIn();
    }
}

if (!function_exists('has_role')) {
    /**
     * Cek apakah user memiliki peran tertentu
     *
     * @param string|array $roles Nama peran atau array nama peran
     * @return bool
     */
    function has_role(string|array $roles): bool
    {
        $user = auth();
        if (!$user || empty($user['role'])) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($user['role'], $roles, true);
        }

        return $user['role'] === $roles;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Cek apakah user memiliki peran admin apapun
     * (super_admin, admin_keuangan, admin_event, admin_konten)
     *
     * @return bool
     */
    function is_admin(): bool
    {
        return has_role(['super_admin', 'admin_keuangan', 'admin_event', 'admin_konten']);
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke format mata uang Rupiah
     * Contoh: 100000 -> Rp 100.000
     *
     * @param float|int $nominal
     * @param bool $withPrefix Tambahkan 'Rp ' di awal
     * @return string
     */
    function format_rupiah(float|int $nominal, bool $withPrefix = true): string
    {
        $formatted = number_format((float)$nominal, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format tanggal ke Bahasa Indonesia
     * Contoh: 2026-11-05 -> 05 November 2026
     *
     * @param string|DateTime|null $date
     * @param string $format
     * @return string
     */
    function format_date(string|DateTime|null $date, string $format = 'd F Y'): string
    {
        if (empty($date)) {
            return '-';
        }

        $timestamp = is_string($date) ? strtotime($date) : $date->getTimestamp();
        if (!$timestamp) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $hari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $d = date('d', $timestamp);
        $m = (int)date('m', $timestamp);
        $Y = date('Y', $timestamp);
        $H = date('H', $timestamp);
        $i = date('i', $timestamp);
        $dayName = $hari[date('l', $timestamp)] ?? '';

        if ($format === 'd F Y') {
            return "{$d} {$bulan[$m]} {$Y}";
        }

        if ($format === 'd F Y H:i') {
            return "{$d} {$bulan[$m]} {$Y} {$H}:{$i} WIB";
        }

        if ($format === 'l, d F Y') {
            return "{$dayName}, {$d} {$bulan[$m]} {$Y}";
        }

        if ($format === 'l, d F Y H:i') {
            return "{$dayName}, {$d} {$bulan[$m]} {$Y} {$H}:{$i} WIB";
        }

        return date($format, $timestamp);
    }
}

if (!function_exists('time_ago')) {
    /**
     * Format waktu relatif (X menit yang lalu, dsb)
     *
     * @param string|int $datetime
     * @return string
     */
    function time_ago(string|int $datetime): string
    {
        $timestamp = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'baru saja';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' menit yang lalu';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' jam yang lalu';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' hari yang lalu';
        } elseif ($diff < 2592000) {
            return floor($diff / 604800) . ' minggu yang lalu';
        } else {
            return format_date($datetime, 'd F Y');
        }
    }
}

if (!function_exists('generate_invoice_number')) {
    /**
     * Generate nomor invoice unik
     * Format: INV-YYYYMMDD-XXXX (contoh: INV-20261105-8942)
     *
     * @param string $prefix
     * @return string
     */
    function generate_invoice_number(string $prefix = 'INV'): string
    {
        $date = date('Ymd');
        $random = strtoupper(bin2hex(random_bytes(2))); // 4 karakter hex acak
        return "{$prefix}-{$date}-{$random}";
    }
}

if (!function_exists('generate_unique_code')) {
    /**
     * Generate 3 digit kode unik transfer manual
     * Rentang default 100 - 999
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    function generate_unique_code(int $min = 100, int $max = 999): int
    {
        return random_int($min, $max);
    }
}

if (!function_exists('str_slug')) {
    /**
     * Ubah string menjadi slug URL yang ramah SEO
     *
     * @param string $title
     * @param string $separator
     * @return string
     */
    function str_slug(string $title, string $separator = '-'): string
    {
        // Konversi ke lowercase
        $slug = mb_strtolower($title, 'UTF-8');
        // Ganti karakter non-alfanumerik dengan separator
        $slug = preg_replace('/[^\p{L}\p{Nd}]+/u', $separator, $slug);
        // Hapus separator berulang
        $slug = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $slug);
        // Trim separator dari awal dan akhir
        return trim($slug, $separator);
    }
}

if (!function_exists('e')) {
    /**
     * Escape string untuk mencegah XSS
     *
     * @param string|null $value
     * @return string
     */
    function e(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sanitize')) {
    /**
     * Sanitasi teks umum
     *
     * @param string $value
     * @return string
     */
    function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}

