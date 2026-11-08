<?php
/**
 * Model Donation — TanamPohon.id
 * 
 * Mengelola data donasi pohon, item jenis pohon yang didonasikan,
 * dan keterkaitannya dengan transaksi pembayaran manual.
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/Payment.php';

class Donation extends Model
{
    protected string $table = 'donations';
    protected string $primaryKey = 'id';

    /**
     * Buat donasi baru beserta item dan pembayaran (dalam transaksi database)
     *
     * @param array $donationData Data donatur dan opsi donasi
     * @param array $items Daftar jenis pohon dan jumlahnya
     * @return array Data donasi & invoice yang dibuat
     * @throws Exception Jika terjadi kesalahan transaksi
     */
    public function createDonationWithPayment(array $donationData, array $items): array
    {
        $this->db->beginTransaction();

        try {
            // 1. Hitung total pohon dan total nominal
            $totalTrees = 0;
            $totalAmount = 0.00;

            foreach ($items as $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['price_per_tree'];
                $totalTrees += $qty;
                $totalAmount += ($qty * $price);
            }

            // 2. Simpan record donasi
            $sqlDonation = "INSERT INTO {$this->table} (
                user_id, donor_name, donor_email, donor_phone, donor_message,
                is_anonymous, is_gift, gift_recipient_name, gift_message,
                total_amount, total_trees, status, created_at, updated_at
            ) VALUES (
                :user_id, :donor_name, :donor_email, :donor_phone, :donor_message,
                :is_anonymous, :is_gift, :gift_recipient_name, :gift_message,
                :total_amount, :total_trees, 'menunggu_pembayaran', NOW(), NOW()
            )";

            $stmtDonation = $this->db->prepare($sqlDonation);
            $stmtDonation->execute([
                'user_id'             => $donationData['user_id'] ?? null,
                'donor_name'          => trim($donationData['donor_name']),
                'donor_email'         => strtolower(trim($donationData['donor_email'])),
                'donor_phone'         => $donationData['donor_phone'] ?? null,
                'donor_message'       => $donationData['donor_message'] ?? null,
                'is_anonymous'        => !empty($donationData['is_anonymous']) ? 1 : 0,
                'is_gift'             => !empty($donationData['is_gift']) ? 1 : 0,
                'gift_recipient_name' => $donationData['gift_recipient_name'] ?? null,
                'gift_message'        => $donationData['gift_message'] ?? null,
                'total_amount'        => $totalAmount,
                'total_trees'         => $totalTrees,
            ]);

            $donationId = (int)$this->db->lastInsertId();

            // 3. Simpan item donasi
            $sqlItem = "INSERT INTO donation_items (
                donation_id, tree_species_id, quantity, price_per_tree, subtotal
            ) VALUES (
                :donation_id, :tree_species_id, :quantity, :price_per_tree, :subtotal
            )";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['price_per_tree'];
                $subtotal = $qty * $price;

                $stmtItem->execute([
                    'donation_id'     => $donationId,
                    'tree_species_id' => $item['tree_species_id'],
                    'quantity'        => $qty,
                    'price_per_tree'  => $price,
                    'subtotal'        => $subtotal,
                ]);
            }

            // 4. Hitung kode unik 3 digit (jika diaktifkan di konfigurasi)
            $isUniqueCodeActive = config('app.unique_code_enabled', true);
            $uniqueCode = $isUniqueCodeActive ? generate_unique_code(100, 999) : 0;
            $grandTotal = $totalAmount + $uniqueCode;

            // 5. Generate nomor invoice unik
            $invoiceNo = generate_invoice_number('INV');

            // 6. Buat record di tabel payments
            $paymentModel = new Payment();
            $paymentId = $paymentModel->createPayment([
                'invoice_no'   => $invoiceNo,
                'payable_type' => 'donation',
                'payable_id'   => $donationId,
                'user_id'      => $donationData['user_id'] ?? null,
                'guest_email'  => empty($donationData['user_id']) ? strtolower(trim($donationData['donor_email'])) : null,
                'total_amount' => $totalAmount,
                'unique_code'  => $uniqueCode,
                'grand_total'  => $grandTotal,
                'status'       => 'menunggu_pembayaran',
            ]);

            // 7. Update relasi payment_id pada tabel donations
            $stmtUpdateDonation = $this->db->prepare("UPDATE {$this->table} SET payment_id = :payment_id WHERE id = :id");
            $stmtUpdateDonation->execute([
                'payment_id' => $paymentId,
                'id'         => $donationId,
            ]);

            $this->db->commit();

            return [
                'donation_id'  => $donationId,
                'payment_id'   => $paymentId,
                'invoice_no'   => $invoiceNo,
                'total_amount' => $totalAmount,
                'unique_code'  => $uniqueCode,
                'grand_total'  => $grandTotal,
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Ambil data donasi lengkap beserta item jenis pohonnya
     *
     * @param int $donationId
     * @return array|false
     */
    public function getDonationWithItems(int $donationId): array|false
    {
        $stmtDonation = $this->db->prepare("
            SELECT d.*, p.invoice_no, p.grand_total, p.unique_code, p.status as payment_status, p.expires_at, p.paid_at
            FROM {$this->table} d
            LEFT JOIN payments p ON d.payment_id = p.id
            WHERE d.id = :id
            LIMIT 1
        ");
        $stmtDonation->execute(['id' => $donationId]);
        $donation = $stmtDonation->fetch(PDO::FETCH_ASSOC);

        if (!$donation) {
            return false;
        }

        $stmtItems = $this->db->prepare("
            SELECT di.*, ts.name as species_name, ts.co2_absorption_kg_per_year
            FROM donation_items di
            LEFT JOIN tree_species ts ON di.tree_species_id = ts.id
            WHERE di.donation_id = :donation_id
        ");
        $stmtItems->execute(['donation_id' => $donationId]);
        $donation['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        return $donation;
    }

    /**
     * Ambil daftar donasi milik user tertentu
     *
     * @param int $userId
     * @return array
     */
    public function getUserDonations(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT d.*, p.invoice_no, p.grand_total, p.status as payment_status, p.expires_at, p.paid_at
            FROM {$this->table} d
            LEFT JOIN payments p ON d.payment_id = p.id
            WHERE d.user_id = :user_id
            ORDER BY d.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil daftar donatur terbaru yang berhasil (untuk transparansi publik)
     *
     * @param int $limit
     * @return array
     */
    public function getLatestPublicDonors(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT d.donor_name, d.total_trees, d.total_amount, d.donor_message, d.is_anonymous, d.created_at
            FROM {$this->table} d
            WHERE d.status = 'berhasil'
            ORDER BY d.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

