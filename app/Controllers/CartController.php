<?php
/**
 * CartController — TanamPohon.id
 * 
 * Mengelola interaksi keranjang belanja (tambah, update, hapus, tampilkan).
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Cart.php';

class CartController extends Controller
{
    private Cart $cart;

    public function __construct()
    {
        parent::__construct();
        $this->cart = new Cart();
    }

    /**
     * Tampilkan halaman keranjang belanja
     */
    public function index(): void
    {
        $items = $this->cart->getItems();
        $subtotal = $this->cart->getSubtotal();
        $totalWeight = $this->cart->getTotalWeight();

        $this->view('public/shop/cart', [
            'pageTitle'   => 'Keranjang Belanja - ' . config('app.name'),
            'items'       => $items,
            'subtotal'    => $subtotal,
            'totalWeight' => $totalWeight,
        ]);
    }

    /**
     * Tambah varian produk ke keranjang
     */
    public function add(): void
    {
        $variantId = (int)$this->request->input('variant_id');
        $qty = max(1, (int)$this->request->input('quantity', 1));

        if ($variantId <= 0) {
            Session::flash('error', 'Silakan pilih varian ukuran dan warna terlebih dahulu.');
            $this->back();
            return;
        }

        try {
            $this->cart->addItem($variantId, $qty);
            Session::flash('success', 'Produk berhasil ditambahkan ke keranjang belanja!');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/keranjang');
    }

    /**
     * Perbarui jumlah pesanan varian di keranjang
     */
    public function update(): void
    {
        $variantId = (int)$this->request->input('variant_id');
        $qty = (int)$this->request->input('quantity');

        try {
            $this->cart->updateItem($variantId, $qty);
            Session::flash('success', 'Jumlah pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/keranjang');
    }

    /**
     * Hapus varian produk dari keranjang
     */
    public function remove(): void
    {
        $variantId = (int)$this->request->input('variant_id');
        $this->cart->removeItem($variantId);

        Session::flash('info', 'Produk dihapus dari keranjang.');
        $this->redirect('/keranjang');
    }
}

