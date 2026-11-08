<?php
/**
 * Model Order — TanamPohon.id
 * 
 * Mengelola pesanan merchandise kaos dan bundle donasi,
 * snapshot alamat pengiriman, dan pemotongan/penahanan stok.
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/Product.php';
require_once __DIR__ . '/Payment.php';

class Order extends Model
{
    protected string $table = 'orders';
    protected string $primaryKey = 'id';

    /**
     * Buat pesanan baru beserta rincian item, penahanan stok, dan tagihan pembayaran manual
     *
     * @param array $orderData
     * @param array $cartItems
     * @param array $shippingRate
     * @return array
     * @throws Exception
     */
    public function createOrderWithPayment(array $orderData, array $cartItems, array $shippingRate): array
    {
        $productModel = new Product();
        $this->db->beginTransaction();

        try {
            // 1. Validasi & Tahan Stok (Reserve Stock) untuk setiap item
            $subtotal = 0.0;
            foreach ($cartItems as $item) {
                $variantId = (int)$item['variant_id'];
                $qty = (int)$item['quantity'];
                $subtotal += ($item['price'] * $qty);

                $reserved = $productModel->reserveStock($variantId, $qty);
                if (!$reserved) {
                    throw new \Exception("Stok untuk produk '{$item['product_name']}' ukuran {$item['size']} tidak mencukupi.");
                }
            }

            $shippingCost = (float)($shippingRate['cost'] ?? 0);
            $totalAmount = $subtotal + $shippingCost;

            // 2. Generate nomor pesanan unik
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            // 3. Simpan data pesanan
            $sqlOrder = "INSERT INTO {$this->table} (
                user_id, order_number, subtotal, shipping_cost, total_amount,
                shipping_address_snapshot, shipping_method, pickup_at_event, event_id,
                notes, status, created_at, updated_at
            ) VALUES (
                :user_id, :order_number, :subtotal, :shipping_cost, :total_amount,
                :shipping_address_snapshot, :shipping_method, :pickup_at_event, :event_id,
                :notes, 'menunggu_pembayaran', NOW(), NOW()
            )";

            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                'user_id'                   => $orderData['user_id'] ?? null,
                'order_number'              => $orderNumber,
                'subtotal'                  => $subtotal,
                'shipping_cost'             => $shippingCost,
                'total_amount'              => $totalAmount,
                'shipping_address_snapshot' => json_encode($orderData['shipping_address'], JSON_UNESCAPED_UNICODE),
                'shipping_method'           => $shippingRate['courier'] . ' - ' . $shippingRate['service'],
                'pickup_at_event'           => !empty($orderData['pickup_at_event']) ? 1 : 0,
                'event_id'                  => $orderData['event_id'] ?? null,
                'notes'                     => $orderData['notes'] ?? null,
            ]);

            $orderId = (int)$this->db->lastInsertId();

            // 4. Simpan rincian order_items
            $sqlItem = "INSERT INTO order_items (
                order_id, product_variant_id, product_name, variant_info, quantity, price, subtotal
            ) VALUES (
                :order_id, :product_variant_id, :product_name, :variant_info, :quantity, :price, :subtotal
            )";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($cartItems as $item) {
                $variantInfo = 'Ukuran: ' . $item['size'] . ', Warna: ' . $item['color'];
                $itemSubtotal = $item['price'] * $item['quantity'];

                $stmtItem->execute([
                    'order_id'           => $orderId,
                    'product_variant_id' => $item['variant_id'],
                    'product_name'       => $item['product_name'],
                    'variant_info'       => $variantInfo,
                    'quantity'           => $item['quantity'],
                    'price'              => $item['price'],
                    'subtotal'           => $itemSubtotal,
                ]);
            }

            // 5. Generate kode unik 3 digit
            $isUniqueCodeActive = config('app.unique_code_enabled', true);
            $uniqueCode = $isUniqueCodeActive ? generate_unique_code(100, 999) : 0;
            $grandTotal = $totalAmount + $uniqueCode;

            // 6. Generate nomor invoice
            $invoiceNo = generate_invoice_number('INV');

            // 7. Simpan record di tabel payments
            $paymentModel = new Payment();
            $paymentId = $paymentModel->createPayment([
                'invoice_no'   => $invoiceNo,
                'payable_type' => 'order',
                'payable_id'   => $orderId,
                'user_id'      => $orderData['user_id'] ?? null,
                'guest_email'  => empty($orderData['user_id']) ? ($orderData['shipping_address']['email'] ?? null) : null,
                'total_amount' => $totalAmount,
                'unique_code'  => $uniqueCode,
                'grand_total'  => $grandTotal,
                'status'       => 'menunggu_pembayaran',
            ]);

            // 8. Hubungkan payment_id ke tabel orders
            $stmtUpdateOrder = $this->db->prepare("UPDATE {$this->table} SET payment_id = :payment_id WHERE id = :id");
            $stmtUpdateOrder->execute([
                'payment_id' => $paymentId,
                'id'         => $orderId,
            ]);

            // 9. Buat placeholder shipment
            $stmtShipment = $this->db->prepare("
                INSERT INTO shipments (order_id, courier, service, status, created_at, updated_at)
                VALUES (:order_id, :courier, :service, 'diproses', NOW(), NOW())
            ");
            $stmtShipment->execute([
                'order_id' => $orderId,
                'courier'  => $shippingRate['courier'],
                'service'  => $shippingRate['service'],
            ]);

            $this->db->commit();

            return [
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
                'payment_id'   => $paymentId,
                'invoice_no'   => $invoiceNo,
                'subtotal'     => $subtotal,
                'shipping_cost'=> $shippingCost,
                'unique_code'  => $uniqueCode,
                'grand_total'  => $grandTotal,
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Ambil pesanan lengkap beserta rincian item produknya
     *
     * @param int $orderId
     * @return array|false
     */
    public function getOrderWithItems(int $orderId): array|false
    {
        $stmtOrder = $this->db->prepare("
            SELECT o.*, p.invoice_no, p.grand_total, p.unique_code, p.status as payment_status, p.expires_at, p.paid_at,
                   s.courier, s.service, s.tracking_number, s.status as shipment_status
            FROM {$this->table} o
            LEFT JOIN payments p ON o.payment_id = p.id
            LEFT JOIN shipments s ON s.order_id = o.id
            WHERE o.id = :id
            LIMIT 1
        ");
        $stmtOrder->execute(['id' => $orderId]);
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return false;
        }

        $stmtItems = $this->db->prepare("
            SELECT oi.*, pv.size, pv.color
            FROM order_items oi
            LEFT JOIN product_variants pv ON oi.product_variant_id = pv.id
            WHERE oi.order_id = :order_id
        ");
        $stmtItems->execute(['order_id' => $orderId]);
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        $order['address'] = json_decode($order['shipping_address_snapshot'], true) ?: [];

        return $order;
    }

    /**
     * Ambil daftar pesanan milik pengguna secara lengkap beserta item, status pengiriman, & bukti pembayaran
     *
     * @param int $userId
     * @param string $statusFilter 'all'|'unpaid'|'processing'|'shipped'|'completed'|'cancelled'
     * @return array
     */
    public function getUserOrdersWithDetails(int $userId, string $statusFilter = 'all'): array
    {
        $sql = "
            SELECT o.*, 
                   p.invoice_no, p.grand_total, p.unique_code, p.status as payment_status, p.expires_at as payment_expires_at, p.paid_at,
                   s.id as shipment_id, s.courier, s.service, s.tracking_number, s.shipped_at, s.delivered_at, s.status as shipment_status,
                   e.title as event_title, e.event_date, e.gathering_point
            FROM {$this->table} o
            LEFT JOIN payments p ON o.payment_id = p.id
            LEFT JOIN shipments s ON s.order_id = o.id
            LEFT JOIN planting_events e ON o.event_id = e.id
            WHERE o.user_id = :user_id
            ORDER BY o.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($orders as $order) {
            // Ambil rincian items
            $stmtItems = $this->db->prepare("
                SELECT oi.*, pv.size, pv.color
                FROM order_items oi
                LEFT JOIN product_variants pv ON oi.product_variant_id = pv.id
                WHERE oi.order_id = :order_id
            ");
            $stmtItems->execute(['order_id' => $order['id']]);
            $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            $order['address'] = json_decode($order['shipping_address_snapshot'], true) ?: [];

            // Ambil bukti pembayaran terbaru jika ada (untuk deteksi status ditolak & alasannya)
            $order['latest_proof'] = null;
            if (!empty($order['payment_id'])) {
                $stmtProof = $this->db->prepare("
                    SELECT id, status, rejected_reason, created_at as proof_date
                    FROM payment_proofs
                    WHERE payment_id = :payment_id
                    ORDER BY id DESC
                    LIMIT 1
                ");
                $stmtProof->execute(['payment_id' => $order['payment_id']]);
                $order['latest_proof'] = $stmtProof->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            // Filter status
            $st = $order['status'];
            $match = false;
            if ($statusFilter === 'all') {
                $match = true;
            } elseif ($statusFilter === 'unpaid' && ($st === 'menunggu_pembayaran' || $order['payment_status'] === 'menunggu_pembayaran')) {
                $match = true;
            } elseif ($statusFilter === 'processing' && $st === 'diproses') {
                $match = true;
            } elseif ($statusFilter === 'shipped' && $st === 'dikirim') {
                $match = true;
            } elseif ($statusFilter === 'completed' && $st === 'selesai') {
                $match = true;
            } elseif ($statusFilter === 'cancelled' && $st === 'dibatalkan') {
                $match = true;
            }

            if ($match) {
                $result[] = $order;
            }
        }

        return $result;
    }

    /**
     * Konfirmasi pesanan telah diterima oleh pembeli
     *
     * @param int $orderId
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    public function confirmReceived(int $orderId, int $userId): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE id = :id AND user_id = :user_id 
                LIMIT 1
            ");
            $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new \Exception('Pesanan tidak ditemukan.');
            }

            if ($order['status'] === 'selesai') {
                throw new \Exception('Pesanan sudah diselesaikan sebelumnya.');
            }

            if (!in_array($order['status'], ['dikirim', 'diproses'])) {
                throw new \Exception('Pesanan belum dalam proses pengiriman.');
            }

            // Update status pesanan menjadi selesai
            $stmtUpdateOrder = $this->db->prepare("
                UPDATE {$this->table}
                SET status = 'selesai', updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpdateOrder->execute(['id' => $orderId]);

            // Update status shipment menjadi diterima
            $stmtUpdateShipment = $this->db->prepare("
                UPDATE shipments
                SET status = 'diterima', delivered_at = NOW(), updated_at = NOW()
                WHERE order_id = :order_id
            ");
            $stmtUpdateShipment->execute(['order_id' => $orderId]);

            // Beri poin apresiasi pembelian kaos ke pengguna (+20 poin)
            $stmtPoints = $this->db->prepare("
                UPDATE users SET contribution_points = contribution_points + 20 WHERE id = :id
            ");
            $stmtPoints->execute(['id' => $userId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Batalkan pesanan (hanya bisa jika status belum dibayar)
     *
     * @param int $orderId
     * @param int $userId
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function cancelOrder(int $orderId, int $userId, string $reason = 'Dibatalkan oleh pembeli'): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, p.status as payment_status 
                FROM {$this->table} o
                LEFT JOIN payments p ON o.payment_id = p.id
                WHERE o.id = :id AND o.user_id = :user_id 
                LIMIT 1
            ");
            $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new \Exception('Pesanan tidak ditemukan.');
            }

            if ($order['status'] === 'dibatalkan') {
                throw new \Exception('Pesanan sudah dibatalkan.');
            }

            if ($order['status'] !== 'menunggu_pembayaran' || $order['payment_status'] === 'berhasil') {
                throw new \Exception('Pesanan yang sudah diproses atau telah dibayar tidak dapat dibatalkan.');
            }

            // 1. Ambil order items untuk mengembalikan stok (release reserved stock)
            $stmtItems = $this->db->prepare("
                SELECT product_variant_id, quantity 
                FROM order_items 
                WHERE order_id = :order_id
            ");
            $stmtItems->execute(['order_id' => $orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $productModel = new Product();
            foreach ($items as $item) {
                if (!empty($item['product_variant_id']) && $item['quantity'] > 0) {
                    $productModel->releaseStock((int)$item['product_variant_id'], (int)$item['quantity']);
                }
            }

            // 2. Update status pesanan menjadi dibatalkan
            $stmtUpdateOrder = $this->db->prepare("
                UPDATE {$this->table}
                SET status = 'dibatalkan', 
                    notes = CONCAT(COALESCE(notes, ''), ' [Alasan Batal: ', :reason, ']'),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmtUpdateOrder->execute([
                'reason' => $reason,
                'id'     => $orderId,
            ]);

            // 3. Update payment status jika ada
            if (!empty($order['payment_id'])) {
                $stmtPay = $this->db->prepare("
                    UPDATE payments 
                    SET status = 'kedaluwarsa', updated_at = NOW()
                    WHERE id = :payment_id AND status = 'menunggu_pembayaran'
                ");
                $stmtPay->execute(['payment_id' => $order['payment_id']]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Ambil daftar pesanan milik pengguna tertentu
     *
     * @param int $userId
     * @return array
     */
    public function getUserOrders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT o.*, p.invoice_no, p.grand_total, p.status as payment_status, s.tracking_number, s.status as shipment_status
            FROM {$this->table} o
            LEFT JOIN payments p ON o.payment_id = p.id
            LEFT JOIN shipments s ON s.order_id = o.id
            WHERE o.user_id = :user_id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
