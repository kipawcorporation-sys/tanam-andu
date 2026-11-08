<?php
/**
 * TanamPohon.id - Guest Middleware
 * 
 * Memastikan pengguna belum login.
 * Jika sudah login, redirect ke dashboard atau admin sesuai role.
 */

class GuestMiddleware
{
    /**
     * Jalankan pengecekan status tamu
     *
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request): bool
    {
        if (Session::isLoggedIn()) {
            $user = Session::getUser();
            $adminRoles = ['super_admin', 'admin_keuangan', 'admin_event', 'admin_konten'];

            if (in_array($user['role'] ?? '', $adminRoles, true)) {
                Response::redirect('/admin');
            } else {
                Response::redirect('/dashboard');
            }
            return false;
        }

        return true;
    }
}

