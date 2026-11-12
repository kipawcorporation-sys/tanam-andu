<?php
/**
 * DonationController — TanamPohon.id
 * 
 * Mengelola alur donasi pohon:
 * Katalog paket, pemilihan jenis pohon, donasi atas nama orang lain/hadiah,
 * dan pembuatan transaksi pembayaran manual.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Donation.php';
require_once __DIR__ . '/../Models/TreeSpecies.php';

class DonationController extends Controller
{
    private Donation $donationModel;
    private TreeSpecies $speciesModel;

    public function __construct()
    {
        parent::__construct();
        $this->donationModel = new Donation();
        $this->speciesModel = new TreeSpecies();
    }

    /**
     * Tampilkan formulir donasi pohon
     */
    public function index(): void
    {
        try {
            $species = $this->speciesModel->getActiveSpecies();
        } catch (\Throwable $e) {
            error_log('[DonationController] getActiveSpecies gagal: ' . $e->getMessage());
            $species = [];
        }

        $selectedSpeciesId = (int)$this->request->input('spesies', 0);

        // Jika tidak ada parameter spesifik, pilih yang pertama secara default
        if ($selectedSpeciesId === 0 && !empty($species)) {
            $selectedSpeciesId = (int)$species[0]['id'];
        }

        $currentUser = auth();

        $this->view('public/donation', [
            'pageTitle'         => 'Donasi Pohon - ' . config('app.name'),
            'species'           => $species,
            'selectedSpeciesId' => $selectedSpeciesId,
            'currentUser'       => $currentUser,
        ]);
    }

    /**
     * Proses checkout donasi dan buat transaksi pembayaran
     */
    public function checkout(): void
    {
        // 1. Validasi input
        $rules = [
            'tree_species_id' => 'required|numeric',
            'quantity'        => 'required|numeric|min:1',
            'donor_name'      => 'required|min:3|max:100',
            'donor_email'     => 'required|email',
            'donor_phone'     => 'required|phone',
        ];

        // Jika donasi sebagai hadiah
        if (!empty($this->request->input('is_gift'))) {
            $rules['gift_recipient_name'] = 'required|min:3|max:100';
        }

        $validator = new Validator($this->request->all(), $rules, [
            'tree_species_id.required'     => 'Silakan pilih jenis pohon yang ingin didonasikan.',
            'quantity.required'            => 'Jumlah pohon wajib diisi.',
            'quantity.min'                 => 'Jumlah pohon minimal 1.',
            'donor_name.required'          => 'Nama lengkap donatur wajib diisi.',
            'donor_email.required'         => 'Email donatur wajib diisi.',
            'donor_email.email'            => 'Format email tidak valid.',
            'donor_phone.required'         => 'Nomor telepon/WhatsApp wajib diisi.',
            'gift_recipient_name.required' => 'Nama penerima hadiah wajib diisi jika opsi hadiah dicentang.',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::flashInput($this->request->all());
            $this->redirect('/donasi');
            return;
        }

        $speciesId = (int)$this->request->input('tree_species_id');
        $quantity = (int)$this->request->input('quantity');

        // 2. Ambil data harga jenis pohon dari database untuk mencegah manipulasi form
        $selectedSpecies = $this->speciesModel->findById($speciesId);
        if (!$selectedSpecies || !$selectedSpecies['is_active']) {
            Session::flash('error', 'Jenis pohon yang dipilih tidak valid atau sedang tidak tersedia.');
            $this->redirect('/donasi');
            return;
        }

        $currentUser = auth();
        $userId = $currentUser ? (int)$currentUser['id'] : null;

        $donationData = [
            'user_id'             => $userId,
            'donor_name'          => $this->request->input('donor_name'),
            'donor_email'         => $this->request->input('donor_email'),
            'donor_phone'         => $this->request->input('donor_phone'),
            'donor_message'       => $this->request->input('donor_message'),
            'is_anonymous'        => !empty($this->request->input('is_anonymous')),
            'is_gift'             => !empty($this->request->input('is_gift')),
            'gift_recipient_name' => $this->request->input('gift_recipient_name'),
            'gift_message'        => $this->request->input('gift_message'),
        ];

        $items = [
            [
                'tree_species_id' => $speciesId,
                'quantity'        => $quantity,
                'price_per_tree'  => (float)$selectedSpecies['price_per_tree'],
            ]
        ];

        try {
            // 3. Simpan donasi & buat tagihan pembayaran manual
            $result = $this->donationModel->createDonationWithPayment($donationData, $items);

            // Kirim notifikasi tagihan donasi baru ke WhatsApp donatur
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                if (!empty($result['payment_id'])) {
                    $fonnte->notifyByPaymentId((int)$result['payment_id'], 'created');
                }
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }

            Session::flash('success', 'Donasi berhasil dibuat! Silakan lakukan transfer sesuai instruksi berikut.');
            $this->redirect('/pembayaran/' . $result['invoice_no']);
        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan sistem saat membuat donasi: ' . $e->getMessage());
            Session::flashInput($this->request->all());
            $this->redirect('/donasi');
        }
    }
}

