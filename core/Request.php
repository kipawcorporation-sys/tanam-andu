<?php
/**
 * TanamPohon.id - Request Wrapper
 * 
 * Membungkus superglobal PHP ($_GET, $_POST, $_FILES, $_SERVER)
 * ke dalam interface yang bersih dan aman.
 */

class Request
{
    /** @var array Data dari $_GET dan $_POST yang sudah digabung */
    private array $data;

    /** @var array Data file upload dari $_FILES */
    private array $files;

    /** @var array Data server dari $_SERVER */
    private array $server;

    /**
     * Konstruktor - inisialisasi data request
     */
    public function __construct()
    {
        $this->data = array_merge($_GET, $_POST);
        $this->files = $_FILES;
        $this->server = $_SERVER;

        // Tangani PUT/DELETE dari form HTML (method spoofing)
        if (isset($this->data['_method'])) {
            $this->server['REQUEST_METHOD'] = strtoupper($this->data['_method']);
        }
    }

    /**
     * Ambil nilai input dari request (GET atau POST)
     * Otomatis membersihkan dari XSS
     *
     * @param string|null $key Nama field, null untuk semua data
     * @param mixed $default Nilai default jika field tidak ada
     * @return mixed
     */
    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->sanitizeArray($this->data);
        }

        $value = $this->data[$key] ?? $default;

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    /**
     * Ambil nilai input tanpa sanitasi (untuk password, dsb)
     *
     * @param string $key Nama field
     * @param mixed $default Nilai default
     * @return mixed
     */
    public function raw(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Alias untuk raw()
     */
    public function inputRaw(string $key, mixed $default = null): mixed
    {
        return $this->raw($key, $default);
    }

    /**
     * Ambil semua data input
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Ambil hanya field-field tertentu dari input
     *
     * @param array $keys Daftar nama field yang diambil
     * @return array
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    /**
     * Ambil semua input kecuali field tertentu
     *
     * @param array $keys Daftar nama field yang dikecualikan
     * @return array
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->data, array_flip($keys));
    }

    /**
     * Cek apakah field ada di input
     *
     * @param string $key Nama field
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]) && $this->data[$key] !== '';
    }

    /**
     * Ambil file upload
     *
     * @param string|null $key Nama field file, null untuk semua
     * @return array|null
     */
    public function file(?string $key = null): array|null
    {
        if ($key === null) {
            return $this->files;
        }

        return $this->files[$key] ?? null;
    }

    /**
     * Cek apakah ada file yang diupload
     *
     * @param string $key Nama field file
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key])
            && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE
            && $this->files[$key]['size'] > 0;
    }

    /**
     * Dapatkan HTTP method request (GET, POST, PUT, DELETE)
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Dapatkan URI yang di-request (tanpa query string)
     */
    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';

        // Hapus query string
        if (str_contains($uri, '?')) {
            $uri = strstr($uri, '?', true);
        }

        // Bersihkan URI
        $uri = '/' . trim($uri, '/');

        // Hapus base path jika ada (untuk subfolder deployment)
        $basePath = env('APP_BASE_PATH', '');
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
            $uri = '/' . ltrim($uri, '/');
        }

        return $uri ?: '/';
    }

    /**
     * Cek apakah request adalah AJAX/XHR
     */
    public function isAjax(): bool
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH'])
            && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Cek apakah request mengharapkan JSON response
     */
    public function wantsJson(): bool
    {
        $accept = $this->server['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }

    /**
     * Dapatkan IP address pengunjung
     */
    public function ip(): string
    {
        // Cek header proxy (CloudFlare, load balancer, dsb)
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // CloudFlare
            'HTTP_X_FORWARDED_FOR',      // Proxy umum
            'HTTP_X_REAL_IP',            // Nginx proxy
            'REMOTE_ADDR',              // Langsung
        ];

        foreach ($headers as $header) {
            if (!empty($this->server[$header])) {
                // Ambil IP pertama jika ada beberapa (X-Forwarded-For)
                $ip = explode(',', $this->server[$header])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Dapatkan User Agent browser
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Dapatkan URL sebelumnya (referer)
     */
    public function referer(): string
    {
        return $this->server['HTTP_REFERER'] ?? url('/');
    }

    /**
     * Cek apakah request menggunakan HTTPS
     */
    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || ($this->server['SERVER_PORT'] ?? 80) == 443
            || (!empty($this->server['HTTP_X_FORWARDED_PROTO']) && $this->server['HTTP_X_FORWARDED_PROTO'] === 'https');
    }

    /**
     * Sanitasi array input secara rekursif
     *
     * @param array $data Data yang akan disanitasi
     * @return array Data yang sudah bersih
     */
    private function sanitizeArray(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $clean[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $clean[$key] = trim($value);
            } else {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }
}
