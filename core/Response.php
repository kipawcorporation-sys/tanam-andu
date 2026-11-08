<?php
/**
 * TanamPohon.id - Response Helper
 * 
 * Mengelola pengiriman response HTTP:
 * JSON, redirect, download, dan status code.
 */

class Response
{
    /**
     * Kirim response JSON
     *
     * @param mixed $data Data yang akan dikirim
     * @param int $statusCode HTTP status code
     * @param array $headers Header tambahan
     */
    public static function json(mixed $data, int $statusCode = 200, array $headers = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        // Set header tambahan
        foreach ($headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Kirim response JSON sukses
     *
     * @param mixed $data Data response
     * @param string $message Pesan sukses
     * @param int $statusCode HTTP status code
     */
    public static function success(mixed $data = null, string $message = 'Berhasil', int $statusCode = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Kirim response JSON error
     *
     * @param string $message Pesan error
     * @param int $statusCode HTTP status code
     * @param mixed $errors Detail error (opsional)
     */
    public static function error(string $message = 'Terjadi kesalahan', int $statusCode = 400, mixed $errors = null): void
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        self::json($response, $statusCode);
    }

    /**
     * Redirect ke URL tertentu
     *
     * @param string $url URL tujuan
     * @param int $statusCode HTTP status code (302 = temporary, 301 = permanent)
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect kembali ke halaman sebelumnya
     */
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        self::redirect($referer);
    }

    /**
     * Kirim file untuk didownload
     *
     * @param string $filePath Path absolut ke file
     * @param string|null $fileName Nama file saat didownload
     * @param string $mimeType MIME type file
     */
    public static function download(string $filePath, ?string $fileName = null, string $mimeType = 'application/octet-stream'): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File tidak ditemukan.';
            exit;
        }

        $fileName = $fileName ?? basename($filePath);
        $fileSize = filesize($filePath);

        // Set header untuk download
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        // Kirim file dalam chunk untuk hemat memori
        $handle = fopen($filePath, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);
        exit;
    }

    /**
     * Tampilkan file inline (bukan download, misalnya PDF di browser)
     *
     * @param string $filePath Path absolut ke file
     * @param string $mimeType MIME type file
     */
    public static function inline(string $filePath, string $mimeType): void
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File tidak ditemukan.';
            exit;
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);
        exit;
    }

    /**
     * Set HTTP status code tanpa mengirim body
     *
     * @param int $code HTTP status code
     */
    public static function status(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Kirim response kosong dengan status 204 No Content
     */
    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    /**
     * Set header HTTP
     *
     * @param string $name Nama header
     * @param string $value Nilai header
     */
    public static function header(string $name, string $value): void
    {
        header("{$name}: {$value}");
    }
}
