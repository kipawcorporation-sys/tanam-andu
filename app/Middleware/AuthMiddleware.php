<?php
/**
 * TanamPohon.id - Auth Middleware
 * 
 * Memastikan pengguna sudah login sebelum mengakses rute tertentu.
 * Jika belum login, redirect ke halaman login dengan pesan flash.
 */

class AuthMiddleware
{
    /**
     * Jalankan pengecekan autentikasi
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request): bool
    {
        if (!Session::isLoggedIn()) {
            // Simpan intended URL untuk diarahkan kembali setelah login
            $currentUri = $request->uri();
            Session::set('intended_url', $currentUri);

            Session::flash('warning', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
            Response::redirect('/masuk');
            return false;
        }

        return true;
    }
}

