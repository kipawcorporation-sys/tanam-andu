<?php
/**
 * TanamPohon.id - Admin Middleware
 * 
 * Memastikan pengguna yang mengakses adalah admin
 * (super_admin, admin_keuangan, admin_event, atau admin_konten).
 */

class AdminMiddleware
{
    /**
     * Jalankan pengecekan role admin
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request): bool
    {
        if (!Session::isLoggedIn()) {
            Session::set('intended_url', $request->uri());
            Session::flash('warning', 'Silakan masuk dengan akun admin terlebih dahulu.');
            Response::redirect('/masuk');
            return false;
        }

        $user = Session::getUser();
        $adminRoles = ['super_admin', 'admin_keuangan', 'admin_event', 'admin_konten'];

        if (!in_array($user['role'] ?? '', $adminRoles, true)) {
            http_response_code(403);
            Session::flash('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman admin.');
            Response::redirect('/dashboard');
            return false;
        }

        return true;
    }
}

