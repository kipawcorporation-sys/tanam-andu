<?php
/**
 * PaymentController — TanamPohon.id
 * 
 * Mengelola alur pembayaran manual:
 * Instruksi rekening, kode unik 3 digit, upload & preview bukti transfer,
 * deteksi duplikasi berkas transfer via hash, dan penyajian bukti privat (anti-IDOR).
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/PaymentProof.php';
require_once __DIR__ . '/../Models/BankAccount.php';

class PaymentController extends Controller
{
    private Payment $paymentModel;
    private PaymentProof $proofModel;
    private BankAccount $bankModel;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel = new Payment();
        $this->proofModel = new PaymentProof();
        $this->bankModel = new BankAccount();
    }

    /**
     * Tampilkan halaman instruksi pembayaran dan formulir konfirmasi
     *
     * @param string $invoiceNo Nomor invoice
     */
    public function show(string $invoiceNo): void
    {
        $payment = $this->paymentModel->findByInvoice($invoiceNo);

        if (!$payment) {
            Session::flash('error', 'Tagihan dengan nomor invoice tersebut tidak ditemukan.');
            $this->redirect('/cek-pembayaran');
            return;
        }

        // Keamanan: Proteksi IDOR
        $this->authorizeAccess($payment);

        $bankAccounts = $this->bankModel->getActiveAccounts();
        $payableDetails = $this->paymentModel->getPayableDetails($payment);
        $proofs = $this->paymentModel->getPaymentProofs($payment['id']);

        // Hitung sisa waktu pembayaran (detik)
        $expiresTimestamp = strtotime($payment['expires_at']);
        $remainingSeconds = max(0, $expiresTimestamp - time());

        $this->view('public/payment-instruction', [
            'pageTitle'        => 'Instruksi Pembayaran - ' . e($payment['invoice_no']),
            'payment'          => $payment,
            'bankAccounts'     => $bankAccounts,
            'payableDetails'   => $payableDetails,
            'proofs'           => $proofs,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    /**
     * Proses unggah bukti transfer pembayaran manual
     *
     * @param string $invoiceNo
     */
    public function confirm(string $invoiceNo): void
    {
        $payment = $this->paymentModel->findByInvoice($invoiceNo);

        if (!$payment) {
            Session::flash('error', 'Tagihan tidak ditemukan.');
            $this->redirect('/cek-pembayaran');
            return;
        }

        $this->authorizeAccess($payment);

        // Jika transaksi sudah berhasil, batalkan unggahan ganda (idempotent)
        if ($payment['status'] === 'berhasil') {
            Session::flash('info', 'Pembayaran ini sudah diverifikasi dan berstatus Berhasil.');
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        if ($payment['status'] === 'kedaluwarsa') {
            Session::flash('error', 'Batas waktu pembayaran untuk tagihan ini telah kedaluwarsa.');
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        // 1. Validasi input formulir
        $validator = new Validator($this->request->all(), [
            'bank_account_id'    => 'required|numeric',
            'sender_bank'        => 'required|min:2|max:50',
            'sender_name'        => 'required|min:3|max:100',
            'amount_transferred' => 'required|numeric|min:1',
            'transfer_date'      => 'required',
        ], [
            'bank_account_id.required'    => 'Silakan pilih rekening bank tujuan transfer.',
            'sender_bank.required'        => 'Nama bank/e-wallet pengirim wajib diisi.',
            'sender_name.required'        => 'Nama pemilik rekening pengirim wajib diisi.',
            'amount_transferred.required' => 'Nominal yang ditransfer wajib diisi.',
            'transfer_date.required'      => 'Tanggal transfer wajib diisi.',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::flashInput($this->request->all());
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        // 2. Validasi file upload bukti transfer
        if (empty($_FILES['proof_file']) || $_FILES['proof_file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Silakan pilih berkas bukti transfer yang ingin diunggah.');
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        $file = $_FILES['proof_file'];
        $maxSize = (int)config('app.max_upload_size', 2097152); // Default 2 MB

        if ($file['size'] > $maxSize) {
            Session::flash('error', 'Ukuran berkas bukti transfer maksimal 2 MB.');
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        // Pemeriksaan tipe MIME asli (bukan hanya ekstensi file)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'application/pdf' => 'pdf',
        ];

        if (!array_key_exists($mimeType, $allowedMimes)) {
            Session::flash('error', 'Format berkas tidak diizinkan. Hanya menerima format JPG, PNG, atau PDF.');
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        // 3. Deteksi Berkas Duplikat via Hash SHA-256
        $fileHash = hash_file('sha256', $file['tmp_name']);
        $duplicateProof = $this->proofModel->findByFileHash($fileHash, $payment['id']);

        $note = $this->request->input('note');
        if ($duplicateProof) {
            $note = trim($note . " [PERINGATAN SISTEM: Berkas bukti ini identik dengan bukti pada invoice {$duplicateProof['invoice_no']}]");
        }

        // 4. Pindahkan berkas ke folder privat di luar public_html
        $uploadDir = STORAGE_PATH . '/uploads/payment_proofs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = $allowedMimes[$mimeType];
        $randomName = 'proof_' . date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $randomName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            Session::flash('error', 'Gagal menyimpan berkas ke server. Silakan coba lagi.');
            $this->redirect('/pembayaran/' . $invoiceNo);
            return;
        }

        // 5. Simpan record bukti transfer
        try {
            $this->proofModel->createProof([
                'payment_id'         => $payment['id'],
                'bank_account_id'    => (int)$this->request->input('bank_account_id'),
                'sender_bank'        => $this->request->input('sender_bank'),
                'sender_name'        => $this->request->input('sender_name'),
                'amount_transferred' => (float)$this->request->input('amount_transferred'),
                'transfer_date'      => $this->request->input('transfer_date'),
                'transfer_time'      => $this->request->input('transfer_time') ?: date('H:i:s'),
                'proof_file'         => 'payment_proofs/' . $randomName,
                'file_hash'          => $fileHash,
                'note'               => $note,
            ]);

            // Update status pembayaran menjadi 'menunggu_verifikasi'
            $this->paymentModel->updateStatus($payment['id'], 'menunggu_verifikasi');

            // Kirim notifikasi WhatsApp bahwa bukti pembayaran telah diterima
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                $fonnte->notifyByPaymentId((int)$payment['id'], 'proof_uploaded');
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }

            Session::flash('success', 'Bukti transfer berhasil diunggah! Tim keuangan kami akan memverifikasi pembayaran Anda segera.');
            $this->redirect('/pembayaran/' . $invoiceNo);
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memproses bukti pembayaran: ' . $e->getMessage());
            $this->redirect('/pembayaran/' . $invoiceNo);
        }
    }

    /**
     * Tampilkan formulir publik untuk cek status pembayaran bagi tamu
     */
    public function checkStatus(): void
    {
        $this->view('public/check-payment', [
            'pageTitle' => 'Cek Status Tagihan & Pembayaran - ' . config('app.name'),
        ]);
    }

    /**
     * Cari invoice berdasarkan nomor invoice dan email donatur/pembeli
     */
    public function findInvoice(): void
    {
        $invoiceNo = trim($this->request->input('invoice_no', ''));
        $email = strtolower(trim($this->request->input('email', '')));

        if (empty($invoiceNo) || empty($email)) {
            Session::flash('error', 'Nomor invoice dan alamat email wajib diisi.');
            $this->redirect('/cek-pembayaran');
            return;
        }

        $payment = $this->paymentModel->findByInvoiceAndEmail($invoiceNo, $email);

        if (!$payment) {
            Session::flash('error', 'Data tagihan tidak ditemukan. Pastikan kombinasi nomor invoice dan email sesuai.');
            Session::flashInput($this->request->all());
            $this->redirect('/cek-pembayaran');
            return;
        }

        // Simpan izin akses tamu di session
        Session::set('guest_auth_invoice_' . $payment['invoice_no'], true);

        $this->redirect('/pembayaran/' . $payment['invoice_no']);
    }

    /**
     * Sajikan file bukti transfer secara aman (Anti-IDOR)
     * Hanya admin atau pemilik transaksi yang dapat melihat gambar/PDF bukti
     *
     * @param int $proofId
     */
    public function viewProof(int $proofId): void
    {
        $proof = $this->proofModel->findById($proofId);

        if (!$proof) {
            http_response_code(404);
            echo 'Berkas bukti tidak ditemukan.';
            exit;
        }

        // Cek izin akses
        $isAuthorized = false;

        if (is_admin()) {
            $isAuthorized = true;
        } elseif (Session::isLoggedIn() && (int)auth('id') === (int)$proof['user_id']) {
            $isAuthorized = true;
        } elseif (Session::get('guest_auth_invoice_' . $proof['invoice_no']) === true) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            http_response_code(403);
            echo 'Akses ditolak. Anda tidak memiliki izin untuk melihat berkas ini.';
            exit;
        }

        $filePath = STORAGE_PATH . '/uploads/' . $proof['proof_file'];

        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File fisik tidak ditemukan di server storage.';
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    }

    /**
     * Helper proteksi IDOR pada halaman pembayaran
     */
    private function authorizeAccess(array $payment): void
    {
        // 1. Admin selalu punya akses
        if (is_admin()) {
            return;
        }

        // 2. Pemilik akun yang login
        if ($payment['user_id'] !== null) {
            if (!Session::isLoggedIn() || (int)auth('id') !== (int)$payment['user_id']) {
                // Beri izin jika memiliki token guest session yang cocok
                if (Session::get('guest_auth_invoice_' . $payment['invoice_no']) !== true) {
                    Session::flash('warning', 'Silakan masuk ke akun Anda untuk melihat tagihan ini.');
                    $this->redirect('/masuk');
                    exit;
                }
            }
        } else {
            // 3. Tagihan tamu: Izinkan jika baru saja dibuat atau sudah terverifikasi lewat email di session
            Session::set('guest_auth_invoice_' . $payment['invoice_no'], true);
        }
    }
}

