<?php
/**
 * Model Cart — TanamPohon.id
 * 
 * Mengelola keranjang belanja pengguna (berbasis session dengan sinkronisasi database jika login).
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/Product.php';

class Cart
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Tambah varian produk ke dalam keranjang belanja
     *
     * @param int $variantId
     * @param int $qty
     * @return bool
     * @throws Exception jika stok tidak mencukupi
     */
    public function addItem(int $variantId, int $qty = 1): bool
    {
        $variant = $this->productModel->getVariantById($variantId);
        if (!$variant) {
            throw new \Exception('Varian produk tidak ditemukan atau tidak aktif.');
        }

        $availableStock = $variant['stock'] - $variant['reserved_stock'];
        $currentQtyInCart = $_SESSION['cart'][$variantId] ?? 0;
        $newTotalQty = $currentQtyInCart + $qty;

        if ($newTotalQty > $availableStock) {
            throw new \Exception("Stok tidak mencukupi. Sisa stok tersedia: {$availableStock}.");
        }

        $_SESSION['cart'][$variantId] = $newTotalQty;
        return true;
    }

    /**
     * Perbarui jumlah pesanan varian di keranjang
     *
     * @param int $variantId
     * @param int $qty
     * @return bool
     */
    public function updateItem(int $variantId, int $qty): bool
    {
        if ($qty <= 0) {
            return $this->removeItem($variantId);
        }

        $variant = $this->productModel->getVariantById($variantId);
        if (!$variant) {
            return false;
        }

        $availableStock = $variant['stock'] - $variant['reserved_stock'];
        if ($qty > $availableStock) {
            throw new \Exception("Stok tidak mencukupi. Sisa stok tersedia: {$availableStock}.");
        }

        $_SESSION['cart'][$variantId] = $qty;
        return true;
    }

    /**
     * Hapus varian produk dari keranjang
     *
     * @param int $variantId
     * @return bool
     */
    public function removeItem(int $variantId): bool
    {
        unset($_SESSION['cart'][$variantId]);
        return true;
    }

    /**
     * Kosongkan seluruh keranjang belanja
     */
    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    /**
     * Ambil seluruh item dalam keranjang beserta rincian lengkapnya
     *
     * @return array
     */
    public function getItems(): array
    {
        $items = [];
        foreach ($_SESSION['cart'] as $variantId => $qty) {
            $variant = $this->productModel->getVariantById((int)$variantId);
            if ($variant) {
                $subtotal = (float)$variant['price'] * $qty;
                $weightSubtotal = (int)$variant['weight_gram'] * $qty;

                $items[] = [
                    'variant_id'      => $variant['id'],
                    'product_id'      => $variant['product_id'],
                    'product_name'    => $variant['product_name'],
                    'product_slug'    => $variant['product_slug'],
                    'category'        => $variant['category'] ?? '',
                    'primary_image'   => $variant['primary_image'] ?? null,
                    'size'            => $variant['size'],
                    'color'           => $variant['color'],
                    'price'           => (float)$variant['price'],
                    'weight_gram'     => (int)$variant['weight_gram'],
                    'quantity'        => $qty,
                    'subtotal'        => $subtotal,
                    'weight_subtotal' => $weightSubtotal,
                ];
            }
        }
        return $items;
    }

    /**
     * Hitung subtotal seluruh pesanan di keranjang
     *
     * @return float
     */
    public function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->getItems() as $item) {
            $subtotal += $item['subtotal'];
        }
        return $subtotal;
    }

    /**
     * Hitung total berat pesanan (dalam gram)
     *
     * @return int
     */
    public function getTotalWeight(): int
    {
        $weight = 0;
        foreach ($this->getItems() as $item) {
            $weight += $item['weight_subtotal'];
        }
        return max(200, $weight); // Minimal 200 gram
    }

    /**
     * Hitung jumlah total kuantitas produk dalam keranjang
     *
     * @return int
     */
    public function getItemCount(): int
    {
        return array_sum($_SESSION['cart'] ?? []);
    }
}

