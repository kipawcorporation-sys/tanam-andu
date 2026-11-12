<?php
/**
 * TanamPohon.id - Definisi Rute Aplikasi (web.php)
 * 
 * Mendaftarkan semua rute publik, autentikasi, dashboard peserta,
 * dan panel admin.
 */

/** @var Router $router */

// ==============================================================================
// 1. RUTE PUBLIK (PENGUNJUNG)
// ==============================================================================
$router->get('/', 'HomeController@index');
$router->get('/tentang-kami', 'HomeController@about');
$router->get('/syarat-ketentuan', 'HomeController@terms');
$router->get('/kebijakan-privasi', 'HomeController@privacy');
$router->get('/faq', 'HomeController@faq');

// ==============================================================================
// 2. RUTE AUTENTIKASI (GUEST MIDDLEWARE)
// ==============================================================================
$router->get('/masuk', 'AuthController@showLogin');
$router->post('/masuk', 'AuthController@login');
$router->get('/daftar', 'AuthController@showRegister');
$router->post('/daftar', 'AuthController@register');
$router->post('/daftar/kirim-otp', 'AuthController@sendOtp');
$router->post('/daftar/verifikasi-otp', 'AuthController@verifyOtp');
$router->post('/keluar', 'AuthController@logout');
$router->get('/lupa-password', 'AuthController@showForgotPassword');
$router->post('/lupa-password', 'AuthController@forgotPassword');
$router->get('/reset-password/{token}', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');
$router->get('/verifikasi-email/{token}', 'AuthController@verifyEmail');

// ==============================================================================
// 3. RUTE DONASI & PEMBAYARAN MANUAL (MODUL 3)
// ==============================================================================
$router->get('/donasi', 'DonationController@index');
$router->post('/donasi/checkout', 'DonationController@checkout');
$router->get('/pembayaran/{invoice}', 'PaymentController@show');
$router->post('/pembayaran/{invoice}/konfirmasi', 'PaymentController@confirm');
$router->get('/pembayaran/bukti/{id}', 'PaymentController@viewProof');
$router->get('/cek-pembayaran', 'PaymentController@checkStatus');
$router->post('/cek-pembayaran', 'PaymentController@findInvoice');

// ==============================================================================
// 4. RUTE KEGIATAN RELAWAN & E-TICKET (MODUL 4)
// ==============================================================================
$router->get('/kegiatan', 'EventController@index');
$router->get('/kegiatan/{id}', 'EventController@show');
$router->get('/kegiatan/{id}/kalender', 'EventController@downloadIcs');
$router->post('/kegiatan/{id}/daftar', 'EventController@register');
$router->get('/tiket/{code}', 'EventController@showTicket');

// ==============================================================================
// RUTE KABAR LAPANGAN & BERITA PUBLIK
// ==============================================================================
$router->get('/kabar', 'NewsController@index');
$router->get('/kabar/{slug}', 'NewsController@show');
$router->post('/kabar/{slug}/komentar', 'NewsController@storeComment');

// ==============================================================================
// 5. RUTE TOKO KAOS & ONGKIR (MODUL 5)
// ==============================================================================
$router->get('/toko', 'ShopController@index');
$router->get('/toko/{slug}', 'ShopController@show');
$router->post('/keranjang/tambah', 'CartController@add');
$router->get('/keranjang', 'CartController@index');
$router->post('/keranjang/update', 'CartController@update');
$router->post('/keranjang/hapus', 'CartController@remove');
$router->get('/checkout', 'ShopController@checkout');
$router->post('/checkout/proses', 'ShopController@processCheckout');
$router->get('/cek-ongkir', 'ShopController@shippingCalculator');
$router->post('/api/ongkir', 'ShopController@calculateShipping');
$router->get('/api/cities', 'ShopController@searchCities');

// ==============================================================================
// 6. RUTE DASHBOARD PESERTA (AUTH MIDDLEWARE)
// ==============================================================================
$router->group(['prefix' => '/dashboard', 'middleware' => ['auth']], function ($r) {
    $r->get('/', 'DashboardController@index');
    $r->get('/donasi', 'DashboardController@donations');
    $r->get('/pohon', 'DashboardController@trees');
    $r->get('/kegiatan', 'DashboardController@events');
    $r->post('/kegiatan/{id}/batal', 'DashboardController@cancelEventRegistration');
    $r->post('/kegiatan/{id}/feedback', 'DashboardController@submitEventFeedback');
    $r->get('/pesanan', 'DashboardController@orders');
    $r->post('/pesanan/{id}/diterima', 'DashboardController@confirmOrderReceived');
    $r->post('/pesanan/{id}/terima', 'DashboardController@confirmOrderReceived');
    $r->post('/pesanan/{id}/batal', 'DashboardController@cancelOrder');
    $r->get('/sertifikat', 'DashboardController@certificates');
    $r->get('/sertifikat/{id}/unduh', 'DashboardController@downloadCertificate');
    $r->get('/pencapaian', 'DashboardController@achievements');
    $r->get('/referral', 'DashboardController@referral');
    $r->get('/notifikasi', 'DashboardController@notifications');
    $r->post('/notifikasi/{id}/baca', 'DashboardController@markNotificationAsRead');
    $r->post('/notifikasi/baca-semua', 'DashboardController@markAllNotificationsAsRead');
    $r->post('/notifikasi/{id}/hapus', 'DashboardController@deleteNotification');
    $r->get('/profil', 'DashboardController@profile');
    $r->post('/profil/update', 'DashboardController@updateProfile');
    $r->post('/profil/password', 'DashboardController@updatePassword');
    $r->post('/profil/avatar', 'DashboardController@uploadAvatar');
    $r->get('/profil/alamat', 'DashboardController@addresses');
    $r->post('/profil/alamat/simpan', 'DashboardController@storeAddress');
    $r->post('/profil/alamat/{id}/update', 'DashboardController@updateAddress');
    $r->post('/profil/alamat/{id}/hapus', 'DashboardController@deleteAddress');
    $r->post('/profil/alamat/{id}/utama', 'DashboardController@setPrimaryAddress');
    $r->get('/bantuan', 'DashboardController@support');
    $r->post('/bantuan/kirim', 'DashboardController@sendTicket');
    $r->get('/bantuan/{id}', 'DashboardController@showTicket');
    $r->post('/bantuan/{id}/balas', 'DashboardController@replyTicket');
    $r->post('/bantuan/{id}/tutup', 'DashboardController@closeTicket');
});

// ==============================================================================
// 7. RUTE ADMIN PANEL (ADMIN MIDDLEWARE)
// ==============================================================================
$router->group(['prefix' => '/admin', 'middleware' => ['admin']], function ($r) {
    $r->get('/', 'AdminDashboardController@index');
    $r->get('/verifikasi-pembayaran', 'AdminPaymentController@index');
    $r->post('/verifikasi-pembayaran/batch-setujui', 'AdminPaymentController@batchApprove');
    $r->get('/verifikasi-pembayaran/{id}', 'AdminPaymentController@show');
    $r->post('/verifikasi-pembayaran/{id}/setujui', 'AdminPaymentController@approve');
    $r->post('/verifikasi-pembayaran/{id}/tolak', 'AdminPaymentController@reject');
    
    $r->get('/rekening', 'AdminBankController@index');
    $r->post('/rekening/simpan', 'AdminBankController@store');
    $r->post('/rekening/{id}/update', 'AdminBankController@update');
    $r->post('/rekening/{id}/hapus', 'AdminBankController@delete');
    $r->post('/rekening/{id}/toggle', 'AdminBankController@toggleActive');
    $r->post('/rekening/{id}/set-primary', 'AdminBankController@setPrimary');

    $r->get('/donasi', 'AdminDonationController@index');
    $r->get('/donasi/export', 'AdminDonationController@export');
    $r->get('/donasi/{id}/detail-json', 'AdminDonationController@detailJson');
    $r->post('/donasi/{id}/update-status', 'AdminDonationController@updateStatus');

    $r->get('/peserta', 'AdminParticipantController@index');
    $r->get('/peserta/export-csv', 'AdminParticipantController@exportCsv');
    $r->post('/peserta/{id}/konfirmasi', 'AdminParticipantController@confirm');
    $r->post('/peserta/{id}/checkin', 'AdminParticipantController@manualCheckin');
    $r->post('/peserta/{id}/terbitkan-sertifikat', 'AdminParticipantController@issueCertificate');
    $r->post('/peserta/{id}/batal', 'AdminParticipantController@cancel');
    $r->get('/checkin-qr', 'AdminParticipantController@qrScanner');
    $r->post('/checkin-qr/proses', 'AdminParticipantController@processCheckin');

    $r->get('/kegiatan', 'AdminEventController@index');
    $r->post('/kegiatan/simpan', 'AdminEventController@store');
    $r->post('/kegiatan/{id}/update', 'AdminEventController@update');
    $r->post('/kegiatan/{id}/toggle-status', 'AdminEventController@toggleStatus');
    $r->post('/kegiatan/{id}/hapus', 'AdminEventController@delete');
    $r->get('/kegiatan/{id}/export-peserta', 'AdminEventController@exportParticipants');
    $r->get('/kegiatan/{id}/presensi', 'AdminEventController@printAttendance');

    $r->get('/lokasi', 'AdminLocationController@index');
    $r->get('/lokasi/export-csv', 'AdminLocationController@exportCsv');
    $r->post('/lokasi/simpan', 'AdminLocationController@store');
    $r->post('/lokasi/{id}/update', 'AdminLocationController@update');
    $r->post('/lokasi/{id}/toggle-status', 'AdminLocationController@toggleStatus');
    $r->post('/lokasi/{id}/hapus', 'AdminLocationController@delete');

    $r->get('/pohon', 'AdminTreeController@index');
    $r->get('/pohon/export-csv', 'AdminTreeController@exportCsv');
    $r->post('/pohon/{id}/update', 'AdminTreeController@updateTree');
    $r->post('/spesies-pohon/simpan', 'AdminTreeController@storeSpecies');
    $r->post('/spesies-pohon/{id}/update', 'AdminTreeController@updateSpecies');
    $r->post('/spesies-pohon/{id}/hapus', 'AdminTreeController@deleteSpecies');

    $r->get('/postingan', 'AdminNewsController@index');
    $r->get('/postingan/tambah', 'AdminNewsController@create');
    $r->post('/postingan/simpan', 'AdminNewsController@store');
    $r->get('/postingan/{id}/edit', 'AdminNewsController@edit');
    $r->post('/postingan/{id}/update', 'AdminNewsController@update');
    $r->post('/postingan/{id}/hapus', 'AdminNewsController@delete');
    $r->post('/postingan/{id}/toggle', 'AdminNewsController@togglePublish');
    $r->post('/postingan/{id}/toggle-featured', 'AdminNewsController@toggleFeatured');
    $r->get('/postingan/komentar', 'AdminNewsController@comments');
    $r->post('/postingan/komentar/{id}/status', 'AdminNewsController@updateCommentStatus');
    $r->post('/postingan/komentar/{id}/hapus', 'AdminNewsController@deleteComment');

    $r->get('/faq', 'AdminFaqController@index');
    $r->post('/faq/simpan', 'AdminFaqController@store');
    $r->post('/faq/{id}/update', 'AdminFaqController@update');
    $r->post('/faq/{id}/hapus', 'AdminFaqController@delete');
    $r->post('/faq/{id}/toggle', 'AdminFaqController@toggle');

    $r->get('/produk', 'AdminProductController@index');
    $r->get('/produk/export-csv', 'AdminProductController@exportCsv');
    $r->post('/produk/simpan', 'AdminProductController@store');
    $r->post('/produk/{id}/update', 'AdminProductController@update');
    $r->post('/produk/{id}/hapus', 'AdminProductController@delete');
    $r->post('/produk/{id}/varian/simpan', 'AdminProductController@storeVariant');
    $r->post('/produk/varian/{id}/stok', 'AdminProductController@updateStock');
    $r->post('/produk/varian/{id}/hapus', 'AdminProductController@deleteVariant');

    $r->get('/pesanan', 'AdminOrderController@index');
    $r->get('/pesanan/export-csv', 'AdminOrderController@exportCsv');
    $r->post('/pesanan/{id}/kirim', 'AdminOrderController@ship');
    $r->post('/pesanan/{id}/selesai', 'AdminOrderController@complete');

    $r->get('/ongkir', 'AdminShippingController@index');
    $r->post('/ongkir/{id}/update', 'AdminShippingController@updateZone');
    $r->post('/ongkir/kota/simpan', 'AdminShippingController@storeCity');

    // Sertifikat Digital
    $r->get('/sertifikat', 'AdminCertificateController@index');
    $r->get('/sertifikat/export-csv', 'AdminCertificateController@exportCsv');
    $r->post('/sertifikat/terbitkan', 'AdminCertificateController@reissueDonation');
    $r->post('/sertifikat/terbitkan-relawan', 'AdminCertificateController@reissueParticipant');
    $r->post('/sertifikat/terbitkan-semua', 'AdminCertificateController@batchIssue');
    $r->post('/sertifikat/{id}/cabut', 'AdminCertificateController@revoke');

    $r->get('/laporan', 'AdminReportController@index');

    $r->get('/pengaturan', 'AdminSettingController@index');
    $r->post('/pengaturan/update', 'AdminSettingController@update');
    $r->post('/pengaturan/cron/jalankan', 'AdminSettingController@runCron');
    $r->post('/pengaturan/fonnte/test', 'AdminSettingController@testFonnte');
});

// ==============================================================================
// 8. RUTE TRANSPARANSI & PUBLIK LAINNYA (MODUL 12)
// ==============================================================================
$router->get('/transparansi', 'TransparencyController@index');
$router->get('/transparansi/export-csv', 'TransparencyController@exportCsv');
$router->get('/peta-lokasi', 'TransparencyController@map');
$router->get('/api/peta-pohon', 'TransparencyController@apiTrees');
$router->get('/leaderboard', 'TransparencyController@leaderboard');
$router->get('/verifikasi-sertifikat', 'TransparencyController@verifyCertificate');
$router->get('/verifikasi-sertifikat/{number}', 'TransparencyController@checkCertificate');

// ==============================================================================
// 9. RUTE CRON JOB / AUTOMATION WEBHOOK (MODUL 13)
// ==============================================================================
$router->get('/cron/run', 'CronController@run');

