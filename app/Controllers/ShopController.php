<?php
/**
 * ShopController — TanamPohon.id
 * 
 * Mengelola etalase toko merchandise kaos, halaman rincian produk,
 * kalkulasi ongkos kirim (AJAX), dan checkout pesanan merchandise.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Services/ShippingService.php';

class ShopController extends Controller
{
    private Product $productModel;
    private Cart $cart;
    private Order $orderModel;
    private ShippingService $shippingService;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->cart = new Cart();
        $this->orderModel = new Order();
        $this->shippingService = new ShippingService();
    }

    /**
     * Tampilkan katalog produk merchandise
     */
    public function index(): void
    {
        $category = $this->request->input('kategori');
        $products = $this->productModel->getActiveProducts($category);

        $this->view('public/shop/index', [
            'pageTitle'        => 'Toko Kaos Merchandise - ' . config('app.name'),
            'products'         => $products,
            'selectedCategory' => $category,
            'cartItemCount'    => $this->cart->getItemCount(),
        ]);
    }

    /**
     * Tampilkan rincian produk
     *
     * @param string $slug Slug produk
     */
    public function show(string $slug): void
    {
        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            Session::flash('error', 'Produk tidak ditemukan atau tidak tersedia.');
            $this->redirect('/toko');
            return;
        }

        $this->view('public/shop/detail', [
            'pageTitle'     => e($product['name']) . ' - ' . config('app.name'),
            'product'       => $product,
            'cartItemCount' => $this->cart->getItemCount(),
        ]);
    }

    /**
     * Tampilkan halaman checkout pesanan
     */
    public function checkout(): void
    {
        $items = $this->cart->getItems();

        if (empty($items)) {
            Session::flash('warning', 'Keranjang belanja Anda masih kosong. Silakan pilih kaos terlebih dahulu.');
            $this->redirect('/toko');
            return;
        }

        $subtotal = $this->cart->getSubtotal();
        $totalWeight = $this->cart->getTotalWeight();
        $currentUser = auth();

        // Ambil daftar event mendatang untuk opsi ambil di lokasi acara
        $db = Database::getInstance()->getConnection();
        $upcomingEvents = [];
        try {
            $stmt = $db->query("SELECT id, title, event_date, gathering_point FROM planting_events WHERE status = 'upcoming' ORDER BY event_date ASC");
            $upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $this->view('public/shop/checkout', [
            'pageTitle'      => 'Checkout Pesanan - ' . config('app.name'),
            'items'          => $items,
            'subtotal'       => $subtotal,
            'totalWeight'    => $totalWeight,
            'currentUser'    => $currentUser,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    /**
     * Halaman Publik: Mesin Cek Ongkir Mandiri (Internal Shipping Rate Calculator)
     */
    public function shippingCalculator(): void
    {
        $defaultDestination = trim($_GET['tujuan'] ?? 'Kota Bandung');
        $defaultWeight = max(100, (int)($_GET['berat'] ?? 1000));
        
        $initialRates = $this->shippingService->calculateRates($defaultDestination, $defaultWeight);

        $this->view('public/shop/shipping_calculator', [
            'pageTitle'          => 'Mesin Cek Ongkir Mandiri — Plant For Fun',
            'origin'             => 'Cicalengka, Kabupaten Bandung, Jawa Barat (Kebon Kopi Kerenceng & andu)',
            'defaultDestination'=> $defaultDestination,
            'defaultWeight'     => $defaultWeight,
            'initialRates'       => $initialRates,
        ]);
    }

    /**
     * Endpoint API (AJAX) untuk mencari kota/kabupaten dengan autocomplete
     */
    public function searchCities(): void
    {
        $keyword = trim($this->request->input('q', '') ?: ($_GET['q'] ?? ''));
        $cities = $this->shippingService->searchCities($keyword, 15);
        Response::json([
            'success' => true,
            'cities'  => $cities,
        ]);
    }

    /**
     * Endpoint API (AJAX) untuk menghitung tarif ongkos kirim secara live
     */
    public function calculateShipping(): void
    {
        $destinationCity = trim($this->request->input('city', ''));
        $weightGram = (int)$this->request->input('weight', $this->cart->getTotalWeight());
        $subtotal = (float)$this->cart->getSubtotal();

        if (empty($destinationCity)) {
            Response::json(['success' => false, 'message' => 'Kota tujuan wajib diisi.'], 400);
            return;
        }

        $result = $this->shippingService->calculateRates($destinationCity, $weightGram, $subtotal);

        Response::json([
            'success' => true,
            'rates'   => $result['rates'],
            'info'    => $result,
            'weight'  => $weightGram,
        ]);
    }

    /**
     * Proses pembuatan pesanan dan tagihan pembayaran manual
     */
    public function processCheckout(): void
    {
        $items = $this->cart->getItems();

        if (empty($items)) {
            Session::flash('error', 'Keranjang belanja Anda kosong.');
            $this->redirect('/toko');
            return;
        }

        // 1. Validasi formulir checkout
        $isPickup = !empty($this->request->input('pickup_at_event'));

        $rules = [
            'recipient_name' => 'required|min:3|max:100',
            'phone'          => 'required|phone',
            'email'          => 'required|email',
        ];

        if (!$isPickup) {
            $rules['province']        = 'required|min:2';
            $rules['city']            = 'required|min:2';
            $rules['full_address']    = 'required|min:10';
            $rules['shipping_service']= 'required';
        }

        $validator = new Validator($this->request->all(), $rules, [
            'recipient_name.required'   => 'Nama penerima wajib diisi.',
            'phone.required'            => 'Nomor telepon/WhatsApp wajib diisi.',
            'email.required'            => 'Alamat email wajib diisi.',
            'province.required'         => 'Provinsi tujuan wajib diisi.',
            'city.required'             => 'Kota tujuan wajib diisi.',
            'full_address.required'     => 'Alamat lengkap pengiriman wajib diisi.',
            'shipping_service.required' => 'Silakan pilih layanan kurir pengiriman.',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::flashInput($this->request->all());
            $this->redirect('/checkout');
            return;
        }

        // 2. Ambil tarif ongkos kirim yang dipilih
        $totalWeight = $this->cart->getTotalWeight();
        $city = $this->request->input('city', '');

        if ($isPickup) {
            $pickupLoc = $this->request->input('pickup_location') ?: $this->request->input('event_id');
            $eventId = (is_numeric($pickupLoc) && (int)$pickupLoc > 0) ? (int)$pickupLoc : null;
            $locationLabel = $eventId 
                ? 'Ambil Langsung di Lokasi Acara Penanaman Hari-H' 
                : 'Ambil Sendiri ke Kedai / Roastery andu (Cicalengka)';

            $shippingRate = [
                'courier' => 'Ambil Sendiri ke Kedai / Acara',
                'service' => $locationLabel,
                'cost'    => 0,
            ];
        } else {
            $serviceCode = $this->request->input('shipping_service');
            $shippingRate = $this->shippingService->getRateByCode($serviceCode, $city, $totalWeight);

            if (!$shippingRate) {
                Session::flash('error', 'Layanan pengiriman tidak valid.');
                $this->redirect('/checkout');
                return;
            }
        }

        // 3. Susun data alamat pengiriman snapshot
        $shippingAddress = [
            'recipient_name' => $this->request->input('recipient_name'),
            'phone'          => $this->request->input('phone'),
            'email'          => $this->request->input('email'),
            'province'       => $isPickup ? 'Jawa Barat' : $this->request->input('province'),
            'city'           => $isPickup ? 'Kab. Bandung (Cicalengka)' : $this->request->input('city'),
            'postal_code'    => $isPickup ? '40395' : $this->request->input('postal_code', ''),
            'full_address'   => $isPickup ? ($locationLabel ?? 'Ambil Sendiri ke Kedai / Roastery andu') : $this->request->input('full_address'),
        ];

        $currentUser = auth();
        $userId = $currentUser ? (int)$currentUser['id'] : null;

        $orderData = [
            'user_id'          => $userId,
            'shipping_address' => $shippingAddress,
            'pickup_at_event'  => $isPickup ? 1 : 0,
            'event_id'         => $isPickup ? $eventId : null,
            'notes'            => $this->request->input('notes'),
        ];

        try {
            // 4. Buat pesanan, tahan stok (reserve), buat tagihan pembayaran manual
            $result = $this->orderModel->createOrderWithPayment($orderData, $items, $shippingRate);

            // Kirim notifikasi tagihan pesanan toko ke WhatsApp pembeli
            try {
                require_once __DIR__ . '/../Services/FonnteService.php';
                $fonnte = new FonnteService();
                if (!empty($result['payment_id'])) {
                    $fonnte->notifyByPaymentId((int)$result['payment_id'], 'created');
                }
            } catch (\Throwable $te) {
                error_log('[Fonnte Notification Error] ' . $te->getMessage());
            }

            // 5. Kosongkan keranjang belanja
            $this->cart->clear();

            Session::flash('success', 'Pesanan kaos berhasil dibuat! Silakan lakukan transfer manual sesuai nominal berikut.');
            $this->redirect('/pembayaran/' . $result['invoice_no']);
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
            Session::flashInput($this->request->all());
            $this->redirect('/checkout');
        }
    }
}

