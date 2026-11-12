<?php
/**
 * TanamPohon.id - Base Controller
 * 
 * Semua controller mewarisi class ini.
 * Menyediakan method untuk rendering view, redirect,
 * JSON response, validasi input, CSRF, dan flash messages.
 */

class Controller
{
    /** @var Request Objek request saat ini */
    protected Request $request;

    /** @var string Layout default untuk rendering view */
    protected string $layout = 'layouts/main';

    /**
     * Konstruktor - inisialisasi request
     */
    public function __construct()
    {
        $this->request = new Request();

        // Bersihkan old input setelah request baru (bukan redirect)
        if ($this->request->method() === 'GET') {
            Session::clearOldInput();
        }
    }

    /**
     * Render view dengan data dan layout
     * View di-render di dalam layout menggunakan output buffering.
     *
     * @param string $view Path view relatif dari resources/views/ (tanpa .php)
     * @param array $data Data yang dikirim ke view
     * @param string|null $layout Layout yang digunakan, null untuk tanpa layout
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        // Ekstrak data agar bisa diakses sebagai variabel di view
        extract($data);

        // Path file view
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View '{$view}' tidak ditemukan: {$viewFile}");
        }

        // Render konten view ke buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Tentukan layout yang digunakan:
        // Jika argumen ke-3 diberikan secara eksplisit (misal null atau ''), jangan gunakan layout default ($this->layout).
        // Jika hanya 2 argumen dipanggil ($this->view($view, $data)), gunakan layout default ($this->layout).
        $layoutName = (func_num_args() >= 3) ? $layout : $this->layout;

        if ($layoutName) {
            $layoutFile = VIEW_PATH . '/' . str_replace('.', '/', $layoutName) . '.php';

            if (file_exists($layoutFile)) {
                // Render layout dengan konten view di dalamnya
                require $layoutFile;
            } else {
                // Layout tidak ditemukan, tampilkan konten saja
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Render view tanpa layout (untuk partial, email template, dsb)
     *
     * @param string $view Path view
     * @param array $data Data untuk view
     */
    protected function renderPartial(string $view, array $data = []): void
    {
        $this->view($view, $data, null);
    }

    /**
     * Redirect ke URL tertentu
     *
     * @param string $url URL tujuan
     * @param int $statusCode HTTP status code
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        Response::redirect($url, $statusCode);
    }

    /**
     * Redirect kembali ke halaman sebelumnya
     */
    protected function back(): void
    {
        Response::back();
    }

    /**
     * Redirect kembali dengan pesan error dan old input
     *
     * @param string $message Pesan error
     * @param array|null $errors Detail error validasi
     */
    protected function backWithError(string $message, ?array $errors = null): void
    {
        Session::flash('error', $message);

        if ($errors) {
            Session::set('_validation_errors', $errors);
        }

        // Simpan input lama
        Session::flashInput($this->request->all());

        $this->back();
    }

    /**
     * Redirect kembali dengan pesan sukses
     *
     * @param string $message Pesan sukses
     */
    protected function backWithSuccess(string $message): void
    {
        Session::flash('success', $message);
        $this->back();
    }

    /**
     * Kirim response JSON
     *
     * @param mixed $data Data response
     * @param int $statusCode HTTP status code
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }

    /**
     * Kirim JSON sukses
     *
     * @param mixed $data Data response
     * @param string $message Pesan sukses
     */
    protected function jsonSuccess(mixed $data = null, string $message = 'Berhasil'): void
    {
        Response::success($data, $message);
    }

    /**
     * Kirim JSON error
     *
     * @param string $message Pesan error
     * @param int $statusCode HTTP status code
     */
    protected function jsonError(string $message = 'Terjadi kesalahan', int $statusCode = 400): void
    {
        Response::error($message, $statusCode);
    }

    /**
     * Validasi input request
     * Jika gagal: redirect kembali dengan error (form) atau kirim JSON error (AJAX)
     *
     * @param array $rules Aturan validasi ['field' => 'required|email|min:6']
     * @param array $messages Pesan kustom (opsional)
     * @return array Data yang sudah tervalidasi
     */
    protected function validate(array $rules, array $messages = []): array
    {
        $validator = new Validator($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($this->request->isAjax() || $this->request->wantsJson()) {
                Response::error('Validasi gagal', 422, $validator->errors());
            }

            Session::set('_validation_errors', $validator->errors());
            Session::flashInput($this->request->all());
            Session::flash('error', 'Mohon periksa kembali data yang Anda masukkan.');
            $this->back();
        }

        return $validator->validated();
    }

    /**
     * Verifikasi CSRF token
     * Panggil di awal method POST/PUT/DELETE
     *
     * @throws Exception Jika token tidak valid
     */
    protected function verifyCsrf(): void
    {
        $token = $this->request->input('_token')
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!Session::validateCsrfToken($token)) {
            if ($this->request->isAjax()) {
                Response::error('Token keamanan tidak valid. Silakan muat ulang halaman.', 419);
            }

            Session::flash('error', 'Token keamanan kedaluwarsa. Silakan coba lagi.');
            $this->back();
        }
    }

    /**
     * Set flash message
     *
     * @param string $type Tipe pesan: success, error, warning, info
     * @param string $message Isi pesan
     */
    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    /**
     * Ambil data user yang sedang login
     *
     * @return array|null Data user
     */
    protected function auth(): ?array
    {
        return Session::getUser();
    }

    /**
     * Pastikan user sudah login, redirect ke login jika belum
     */
    protected function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('warning', 'Silakan login terlebih dahulu.');
            Session::set('_intended_url', $this->request->uri());
            $this->redirect(url('/login'));
        }
    }

    /**
     * Pastikan user memiliki role admin
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (!Session::hasRole('admin')) {
            Session::flash('error', 'Anda tidak memiliki akses ke halaman ini.');
            $this->redirect(url('/dashboard'));
        }
    }

    /**
     * Upload file dengan validasi
     *
     * @param string $fieldName Nama field file di form
     * @param string $uploadDir Subdirektori di storage/uploads/
     * @param array $allowedTypes MIME types yang diizinkan
     * @param int $maxSize Ukuran maksimal dalam bytes
     * @return string|null Nama file yang disimpan, null jika gagal
     */
    protected function uploadFile(
        string $fieldName,
        string $uploadDir,
        array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'],
        int $maxSize = 2097152 // 2MB default
    ): ?string {
        if (!$this->request->hasFile($fieldName)) {
            return null;
        }

        $file = $this->request->file($fieldName);

        // Validasi error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validasi ukuran
        if ($file['size'] > $maxSize) {
            return null;
        }

        // Validasi tipe MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return null;
        }

        // Buat nama file unik
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($extension);

        // Pastikan direktori tujuan ada
        $targetDir = UPLOAD_PATH . '/' . $uploadDir;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Pindahkan file
        $targetPath = $targetDir . '/' . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }

        return null;
    }

    /**
     * Hapus file upload
     *
     * @param string $fileName Nama file
     * @param string $uploadDir Subdirektori di storage/uploads/
     * @return bool
     */
    protected function deleteFile(string $fileName, string $uploadDir): bool
    {
        $filePath = UPLOAD_PATH . '/' . $uploadDir . '/' . $fileName;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    /**
     * Cek apakah request saat ini adalah POST.
     *
     * @return bool
     */
    protected function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
    }
}
