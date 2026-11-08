<?php
/**
 * Controller CronController — TanamPohon.id
 * 
 * Endpoint HTTP Webhook untuk memicu tugas cron job terjadwal
 * secara aman dari cPanel Web Cron, UptimeRobot, atau cron-job.org.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Services/CronService.php';

class CronController extends Controller
{
    /**
     * Jalankan cron job melalui request HTTP GET dengan otentikasi token
     * URL: /cron/run?token=YOUR_CRON_SECRET_TOKEN
     */
    public function run(): void
    {
        $token = $_GET['token'] ?? '';
        $expectedToken = env('CRON_SECRET_TOKEN', 'tanampohon_secret_cron_token_2026');

        if (empty($token) || !hash_equals($expectedToken, $token)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Akses ditolak: Token otentikasi cron tidak valid atau tidak disertakan.'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        $cronService = new CronService();
        $summary = $cronService->runAll();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Tugas otomatisasi Cron Job berhasil dijalankan.',
            'result'  => $summary,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

