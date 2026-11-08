<?php
/**
 * TanamPohon.id - Manajer Session
 * 
 * Mengelola session PHP dengan fitur:
 * set, get, flash messages, CSRF token, dan regenerasi session.
 */

class Session
{
    /**
     * Set nilai session
     *
     * @param string $key Kunci session
     * @param mixed $value Nilai yang disimpan
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Ambil nilai session
     *
     * @param string $key Kunci session
     * @param mixed $default Nilai default jika kunci tidak ada
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Cek apakah kunci session ada
     *
     * @param string $key Kunci session
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Hapus nilai session berdasarkan kunci
     *
     * @param string $key Kunci session
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Hapus semua data session
     */
    public static function clear(): void
    {
        session_unset();
    }

    /**
     * Hancurkan session sepenuhnya (untuk logout)
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();

        // Hapus cookie session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
    }

    /**
     * Regenerasi ID session (untuk keamanan, panggil setelah login)
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Simpan flash message (hanya tersedia untuk 1 request berikutnya)
     *
     * @param string $type Tipe pesan: success, error, warning, info
     * @param string $message Isi pesan
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash_messages'][$type][] = $message;
    }

    /**
     * Ambil flash messages dan hapus dari session
     *
     * @param string|null $type Tipe pesan spesifik, null untuk semua
     * @return array Flash messages
     */
    public static function getFlash(?string $type = null): array
    {
        if ($type !== null) {
            $messages = $_SESSION['_flash_messages'][$type] ?? [];
            unset($_SESSION['_flash_messages'][$type]);
            return $messages;
        }

        $messages = $_SESSION['_flash_messages'] ?? [];
        unset($_SESSION['_flash_messages']);
        return $messages;
    }

    public static function getAllFlash(): array
    {
        return self::getFlash(null);
    }

    /**
     * Cek apakah ada flash messages
     *
     * @param string|null $type Tipe pesan tertentu, null untuk cek semua
     * @return bool
     */
    public static function hasFlash(?string $type = null): bool
    {
        if ($type) {
            return !empty($_SESSION['_flash_messages'][$type]);
        }
        return !empty($_SESSION['_flash_messages']);
    }

    /**
     * Generate token CSRF baru dan simpan di session
     *
     * @return string Token CSRF
     */
    public static function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        $_SESSION['_csrf_token_time'] = time();
        return $token;
    }

    /**
     * Ambil token CSRF saat ini (generate jika belum ada)
     *
     * @return string Token CSRF
     */
    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            return self::generateCsrfToken();
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Validasi token CSRF
     * Token kedaluwarsa setelah 2 jam
     *
     * @param string $token Token dari form/request
     * @return bool True jika valid
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['_csrf_token'])) {
            return false;
        }

        // Cek kedaluwarsa token (2 jam)
        $tokenAge = time() - ($_SESSION['_csrf_token_time'] ?? 0);
        if ($tokenAge > 7200) {
            self::generateCsrfToken();
            return false;
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Simpan input lama untuk ditampilkan kembali di form (setelah validasi gagal)
     *
     * @param array $data Data input yang disimpan
     * @param array $except Field yang tidak perlu disimpan (misal: password)
     */
    public static function flashInput(array $data, array $except = ['password', 'password_confirmation']): void
    {
        foreach ($except as $key) {
            unset($data[$key]);
        }
        $_SESSION['_old_input'] = $data;
    }

    /**
     * Ambil nilai input lama
     *
     * @param string $key Nama field
     * @param mixed $default Nilai default
     * @return mixed
     */
    public static function oldInput(string $key, mixed $default = ''): mixed
    {
        $value = $_SESSION['_old_input'][$key] ?? $default;

        // Hapus setelah dibaca (hanya 1x pakai)
        if (isset($_SESSION['_old_input'][$key])) {
            // Jangan hapus dulu, biarkan sampai semua dibaca
            // Akan dihapus di clearOldInput()
        }

        return $value;
    }

    /**
     * Hapus semua old input dari session
     */
    public static function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }

    /**
     * Simpan data user ke session (setelah login)
     *
     * @param array $user Data user dari database
     */
    public static function setUser(array $user): void
    {
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'nama'     => $user['nama_lengkap'] ?? $user['nama'] ?? '',
            'email'    => $user['email'],
            'role'     => $user['role'] ?? 'user',
            'avatar'   => $user['avatar'] ?? null,
            'login_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Ambil data user yang sedang login
     *
     * @return array|null Data user atau null jika belum login
     */
    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Cek apakah user sudah login
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    /**
     * Cek apakah user memiliki role tertentu
     *
     * @param string $role Role yang dicek (admin, user)
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
    }
}
