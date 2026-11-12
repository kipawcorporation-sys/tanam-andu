<?php
/**
 * AuthController — TanamPohon.id
 * 
 * Mengelola alur autentikasi pengguna:
 * Masuk (Login), Daftar (Register), Keluar (Logout),
 * Lupa Password, Reset Password, dan Verifikasi Email.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Role.php';

class AuthController extends Controller
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
        // Layout khusus untuk autentikasi (kartu terpusat)
        $this->layout = 'layouts/auth';
    }

    /**
     * Tampilkan halaman formulir login
     */
    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirectBasedOnRole();
            return;
        }

        $this->view('auth/login', [
            'pageTitle' => 'Masuk ke Akun - ' . config('app.name'),
        ]);
    }

    /**
     * Proses autentikasi login pengguna
     */
    public function login(): void
    {
        // 1. Rate limiting sederhana (maks 5 percobaan gagal dalam 15 menit)
        $failedAttempts = Session::get('login_failed_attempts', 0);
        $lastAttemptTime = Session::get('login_last_attempt_time', 0);

        if ($failedAttempts >= 5 && (time() - $lastAttemptTime) < 900) {
            $sisaMenit = ceil((900 - (time() - $lastAttemptTime)) / 60);
            Session::flash('error', "Terlalu banyak percobaan gagal. Silakan tunggu {$sisaMenit} menit sebelum mencoba lagi.");
            Session::flashInput($this->request->all());
            $this->redirect('/masuk');
            return;
        }

        // 2. Validasi input
        $validator = new Validator($this->request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::flashInput($this->request->all());
            $this->redirect('/masuk');
            return;
        }

        $email = $this->request->input('email');
        $password = $this->request->inputRaw('password');

        // 3. Cari pengguna berdasarkan email
        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            // Catat percobaan gagal
            Session::set('login_failed_attempts', $failedAttempts + 1);
            Session::set('login_last_attempt_time', time());

            Session::flash('error', 'Kombinasi email dan kata sandi tidak cocok.');
            Session::flashInput($this->request->all());
            $this->redirect('/masuk');
            return;
        }

        // 4. Periksa status akun
        if ($user['status'] === 'banned') {
            Session::flash('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
            $this->redirect('/masuk');
            return;
        }

        // 5. Login berhasil: reset counter percobaan gagal
        Session::remove('login_failed_attempts');
        Session::remove('login_last_attempt_time');

        // Regenerasi session ID untuk mencegah session fixation
        Session::regenerate();

        // Simpan data user ke dalam session
        Session::setUser([
            'id'           => $user['id'],
            'nama'         => $user['name'],
            'nama_lengkap' => $user['name'],
            'email'        => $user['email'],
            'role'         => $user['role_name'] ?? 'peserta',
            'role_slug'    => $user['role_slug'] ?? 'peserta',
            'avatar'       => $user['avatar'],
        ]);

        Session::flash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['name']) . '!');

        // Arahkan ke URL sebelumnya atau ke dashboard yang sesuai
        $intendedUrl = Session::get('intended_url');
        if ($intendedUrl) {
            Session::remove('intended_url');
            $this->redirect($intendedUrl);
            return;
        }

        $this->redirectBasedOnRole();
    }

    /**
     * Tampilkan formulir pendaftaran akun baru
     */
    public function showRegister(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirectBasedOnRole();
            return;
        }

        $referralCode = $this->request->input('ref', '');

        $this->view('auth/register', [
            'pageTitle'    => 'Daftar Akun Baru - ' . config('app.name'),
            'referralCode' => $referralCode,
        ]);
    }

    /**
     * Proses pendaftaran akun baru
     */
    public function register(): void
    {
        // 1. Validasi data formulir
        $validator = new Validator($this->request->all(), [
            'name'                  => 'required|min:3|max:100',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'required|phone',
            'city'                  => 'required|min:2',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'name.required'                  => 'Nama lengkap wajib diisi.',
            'name.min'                       => 'Nama minimal 3 karakter.',
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.unique'                   => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.',
            'phone.required'                 => 'Nomor telepon/WhatsApp wajib diisi.',
            'phone.phone'                    => 'Format nomor telepon tidak valid (contoh: 081234567890).',
            'city.required'                  => 'Kota domisili wajib diisi.',
            'password.required'              => 'Kata sandi wajib diisi.',
            'password.min'                   => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi kata sandi tidak cocok.',
            'password_confirmation.required' => 'Ulangi kata sandi wajib diisi.',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::flashInput($this->request->all());
            $this->redirect('/daftar');
            return;
        }

        // 1.b Validasi Kode OTP WhatsApp
        require_once __DIR__ . '/../Services/FonnteService.php';
        $fonnte = new FonnteService();
        $phoneInput = $this->request->input('phone');
        $otpCodeInput = trim($this->request->input('otp_code', ''));

        $isVerified = $fonnte->isOtpVerified($phoneInput);
        if (!$isVerified && !empty($otpCodeInput)) {
            $verifyRes = $fonnte->verifyOtp($otpCodeInput, $phoneInput);
            $isVerified = $verifyRes['success'];
        }

        if (!$isVerified) {
            Session::flash('error', 'Nomor WhatsApp wajib diverifikasi dengan kode OTP terlebih dahulu.');
            Session::flashInput($this->request->all());
            $this->redirect('/daftar');
            return;
        }

        // 2. Periksa kode referral (opsional)
        $referralCodeInput = trim($this->request->input('referral_code', ''));
        $referrerId = null;
        $initialPoints = 0;

        if (!empty($referralCodeInput)) {
            $referrer = $this->userModel->findByReferralCode($referralCodeInput);
            if ($referrer) {
                $referrerId = $referrer['id'];
                $initialPoints = 5; // Poin bonus pendaftar yang diajak
                // Beri poin bonus ke pengajak
                $this->userModel->addPoints($referrer['id'], 10);
            }
        }

        // 3. Simpan pengguna baru
        $userData = [
            'name'                => $this->request->input('name'),
            'email'               => $this->request->input('email'),
            'password'            => $this->request->inputRaw('password'),
            'phone'               => $this->request->input('phone'),
            'city'                => $this->request->input('city'),
            'default_tshirt_size' => $this->request->input('default_tshirt_size') ?: null,
            'role_id'             => 5, // Peserta
            'referred_by'         => $referrerId,
            'points'              => $initialPoints,
            'status'              => 'active',
        ];

        try {
            $newUserId = $this->userModel->createUser($userData);

            // 4. Catat referral jika ada
            if ($referrerId) {
                $db = Database::getInstance()->getConnection();
                $stmtRef = $db->prepare("
                    INSERT INTO referrals (referrer_id, referred_id, bonus_points, created_at)
                    VALUES (:referrer_id, :referred_id, 10, NOW())
                ");
                $stmtRef->execute([
                    'referrer_id' => $referrerId,
                    'referred_id' => $newUserId,
                ]);
            }

            // 5. Otomatis login setelah pendaftaran berhasil
            $newUser = $this->userModel->findWithRole($newUserId);
            Session::regenerate();
            Session::setUser([
                'id'           => $newUser['id'],
                'nama'         => $newUser['name'],
                'nama_lengkap' => $newUser['name'],
                'email'        => $newUser['email'],
                'role'         => $newUser['role_name'] ?? 'peserta',
                'role_slug'    => $newUser['role_slug'] ?? 'peserta',
                'avatar'       => null,
            ]);

            // Bersihkan sesi verifikasi OTP & kirim pesan selamat datang via WhatsApp
            $fonnte->clearVerified();
            try {
                $fonnte->notifyWelcome($userData['phone'], $userData['name']);
            } catch (\Throwable $te) {
                error_log('[Fonnte Welcome Notification Error] ' . $te->getMessage());
            }

            Session::flash('success', 'Pendaftaran berhasil! Selamat datang di keluarga Plant For Fun.');
            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal mendaftar: ' . $e->getMessage());
            Session::flashInput($this->request->all());
            $this->redirect('/daftar');
        }
    }

    /**
     * AJAX endpoint: Kirim kode OTP WhatsApp pendaftaran
     */
    public function sendOtp(): void
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit;
        }

        $phone = trim($this->request->input('phone', ''));
        $name  = trim($this->request->input('name', 'Calon Relawan'));

        if (empty($phone) || strlen(preg_replace('/\D/', '', $phone)) < 9) {
            echo json_encode(['success' => false, 'message' => 'Nomor WhatsApp tidak valid (minimal 10 digit).']);
            exit;
        }

        require_once __DIR__ . '/../Services/FonnteService.php';
        $fonnte = new FonnteService();
        $res = $fonnte->sendOtp($phone, $name);
        echo json_encode($res);
        exit;
    }

    /**
     * AJAX endpoint: Verifikasi kode OTP WhatsApp
     */
    public function verifyOtp(): void
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
            exit;
        }

        $phone = trim($this->request->input('phone', ''));
        $code  = trim($this->request->input('otp_code', ''));

        if (empty($code) || strlen($code) !== 6) {
            echo json_encode(['success' => false, 'message' => 'Kode OTP harus berupa 6 digit angka.']);
            exit;
        }

        require_once __DIR__ . '/../Services/FonnteService.php';
        $fonnte = new FonnteService();
        $res = $fonnte->verifyOtp($code, $phone);
        echo json_encode($res);
        exit;
    }

    /**
     * Proses keluar (Logout)
     */
    public function logout(): void
    {
        Session::destroy();
        session_start();
        Session::flash('info', 'Anda telah berhasil keluar.');
        $this->redirect('/masuk');
    }

    /**
     * Tampilkan formulir lupa password
     */
    public function showForgotPassword(): void
    {
        $this->view('auth/forgot-password', [
            'pageTitle' => 'Lupa Kata Sandi - ' . config('app.name'),
        ]);
    }

    /**
     * Kirim tautan reset password
     */
    public function forgotPassword(): void
    {
        $validator = new Validator($this->request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Session::flash('error', 'Silakan masukkan alamat email yang valid.');
            $this->redirect('/lupa-password');
            return;
        }

        $email = $this->request->input('email');
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            $token = $this->userModel->createResetToken($user['id']);
            $resetLink = url('reset-password/' . $token);

            // Dalam lingkungan produksi, kirim email via PHPMailer.
            // Di lingkungan pengembangan, tampilkan link atau simpan di session.
            Session::flash('info', "Tautan reset telah dibuat. (Mode Dev: <a href='{$resetLink}'>Klik di sini untuk reset</a>)");
        } else {
            // Tampilkan pesan sukses umum untuk alasan keamanan (mencegah user enumeration)
            Session::flash('info', 'Jika email terdaftar di sistem kami, instruksi reset kata sandi telah dikirimkan.');
        }

        $this->redirect('/lupa-password');
    }

    /**
     * Tampilkan formulir reset password dengan token
     */
    public function showResetPassword(string $token): void
    {
        $user = $this->userModel->findByResetToken($token);

        if (!$user) {
            Session::flash('error', 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.');
            $this->redirect('/lupa-password');
            return;
        }

        $this->view('auth/reset-password', [
            'pageTitle' => 'Buat Kata Sandi Baru - ' . config('app.name'),
            'token'     => $token,
        ]);
    }

    /**
     * Proses reset password baru
     */
    public function resetPassword(): void
    {
        $validator = new Validator($this->request->all(), [
            'token'                 => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            $this->redirect('/reset-password/' . $this->request->input('token'));
            return;
        }

        $token = $this->request->input('token');
        $newPassword = $this->request->inputRaw('password');

        $success = $this->userModel->resetPasswordWithToken($token, $newPassword);

        if ($success) {
            Session::flash('success', 'Kata sandi Anda berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
            $this->redirect('/masuk');
        } else {
            Session::flash('error', 'Gagal memperbarui kata sandi. Token tidak valid atau sudah kedaluwarsa.');
            $this->redirect('/lupa-password');
        }
    }

    /**
     * Verifikasi alamat email pengguna
     */
    public function verifyEmail(string $token): void
    {
        $success = $this->userModel->verifyEmailToken($token);

        if ($success) {
            Session::flash('success', 'Alamat email Anda berhasil diverifikasi. Terima kasih!');
        } else {
            Session::flash('error', 'Tautan verifikasi email tidak valid atau sudah digunakan.');
        }

        $this->redirect('/dashboard');
    }

    /**
     * Helper untuk redirect sesuai role pengguna
     */
    private function redirectBasedOnRole(): void
    {
        $user = Session::getUser();
        $adminRoles = ['super_admin', 'admin_keuangan', 'admin_event', 'admin_konten'];

        if (in_array($user['role'] ?? '', $adminRoles, true)) {
            $this->redirect('/admin');
        } else {
            $this->redirect('/dashboard');
        }
    }
}

