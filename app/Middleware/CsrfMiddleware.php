<?php
/**
 * TanamPohon.id - CSRF Middleware
 * 
 * Melindungi aplikasi dari serangan Cross-Site Request Forgery (CSRF).
 * Memvalidasi token CSRF pada method POST, PUT, DELETE.
 */

class CsrfMiddleware
{
    /**
     * Jalankan validasi CSRF
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request): bool
    {
        $method = $request->method();

        // Hanya periksa method yang memodifikasi data
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            // Cek token dari POST atau Header (X-CSRF-TOKEN)
            $token = $_POST['_csrf_token'] 
                ?? $request->input('_csrf_token') 
                ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

            if (empty($token) || !Session::validateCsrfToken($token)) {
                if ($request->isAjax()) {
                    Response::json([
                        'success' => false,
                        'message' => 'Token CSRF tidak valid atau sudah kedaluwarsa. Silakan refresh halaman.',
                    ], 419);
                    return false;
                }

                Session::flash('error', 'Sesi Anda telah kedaluwarsa (CSRF token invalid). Silakan ulangi form.');
                Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
                return false;
            }
        }

        return true;
    }
}

