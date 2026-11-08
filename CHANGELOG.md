# 📋 CHANGELOG — Plant For Fun

> Catatan seluruh perubahan yang dilakukan selama pengembangan aplikasi.
> Format: `[YYYY-MM-DD HH:MM] Modul X — Deskripsi`

---

## [2026-11-07 20:35] Mesin Cek Ongkir Mandiri (Internal / Standalone Shipping Engine — 100% Bebas Ketergantungan API Luar)

### 1. Mesin Ongkir Mandiri (Self-Contained Logistics Engine)
- **Zero External API Dependency**: Tidak bergantung pada API pihak ketiga (RajaOngkir, Biteship, atau Shipper). Berjalan 100% lokal di dalam aplikasi, tanpa batasan kuota harian, tanpa biaya langganan, dan berkecepatan respon instan (< 10ms).
- **Titik Asal (*Origin*) Resmi**: Kebon Kopi Kerenceng & an du Coffee Lab, Cicalengka, Kabupaten Bandung, Jawa Barat (Kode Pos 40395).
- **Struktur Database Master Logistik**:
  - Tabel `shipping_zones`: Membagi wilayah pengiriman ke dalam 12 zona logistik komprehensif dari Cicalengka/Bandung (Zona Lokal Bandung Raya, Jabodetabek & Banten, Jawa Barat Luar, Jawa Tengah & DIY, Jawa Timur & Madura, Bali & NTB, Sumatera Bagian Selatan, Sumatera Bagian Utara, Kalimantan, Sulawesi, NTT & Maluku, Tanah Papua).
  - Tabel `shipping_cities`: Memuat 305+ data master Kota dan Kabupaten di 38 Provinsi Indonesia lengkap dengan tipe daerah, kode pos, dan pemetaan ke zona tarif.
- **Dukungan Multi-Kurir Lengkap**:
  - **JNE Express**: REG (Reguler), YES (Yakin Esok Sampai 24 jam), OKE (Ekonomis), dan JTR (JNE Trucking/Kargo untuk pesanan grosir $\ge$ 10 kg).
  - **J&T Express**: EZ (Reguler) dan J&T Super (Prioritas).
  - **SiCepat Ekspres**: REG (Reguler), BEST (Besok Sampai Tujuan), dan HALU (Harga Luar Biasa).
  - **POS Indonesia**: Pos Reguler Nusantara ke seluruh pelosok kecamatan.
  - **Kurir & Titik Ambil Lokal**: *Ambil Sendiri di Roastery an du / Basecamp Kerenceng* (Rp 0 / Bebas Ongkir) & *Kurir Lokal Kerenceng Express* (Rp 8.000 flat untuk Bandung Raya).
- **Kalkulasi Berat & Promo Otomatis**:
  - Toleransi berat pengiriman standar Indonesia (berat $\le$ 1.200 gr tetap dihitung 1 kg).
  - Fitur subsidi ongkir otomatis (diskon Rp 10.000 jika subtotal belanja $\ge$ Rp 200.000).

### 2. Antarmuka Publik & Checkout
- **Halaman Khusus Cek Ongkir Mandiri ([`/cek-ongkir`](http://localhost:8000/cek-ongkir))**:
  - Antarmuka interaktif dengan pencarian kota tujuan instan (*Live AJAX Autocomplete*), tombol tujuan populer, dan preset berat pesanan (1 pouch 200g, 3 pouch 600g, 1 kg, 2 kg, kargo 10 kg).
  - Menampilkan kartu perbandingan seluruh opsi kurir dengan estimasi hari tiba (ETD), badge keunggulan, serta tombol langsung order kopi ke toko.
- **Peningkatan Halaman Checkout ([`/checkout`](http://localhost:8000/checkout))**:
  - Kolom kota tujuan kini dilengkapi *search dropdown autocomplete* otomatis yang langsung mengisi provinsi dan kode pos.
  - Kalkulasi ongkir otomatis menghitung pilihan kurir secara live tanpa delay.
- **Tautan Navigasi**: Menu "Cek Ongkir" disematkan pada Navbar utama dan Footer situs.

### 3. Pengelolaan Tarif Ongkir di Panel Admin ([`/admin/ongkir`](http://localhost:8000/admin/ongkir))
- Menu baru **Mesin Ongkir Mandiri** di bawah kelompok *Toko & Kopi* pada sidebar admin.
- Admin dapat mengubah tarif dasar per kg, estimasi waktu antar, dan mengaktifkan/menonaktifkan ekspedisi tertentu per zona.
- Admin dapat menambahkan kota/kabupaten baru secara langsung.

---

## [2026-11-07 20:25] Integrasi Logo Asli Mitra & Pengelolaan Website Penuh di Admin Panel

### 1. Integrasi Logo Otentik Mitra Kolaborasi (Sesuai Referensi Pengguna)
- **Ekstraksi & Penajaman Presisi Gambar Referensi**:
  - Mengekstrak logo otentik dari citra acuan pengguna (`media_1794052332402_295c51dc.jpg`) yang memuat tiga logo mitra utama.
  - Memotong (*cropping*) dan membersihkan latar belakang menjadi format PNG transparan dengan resolusi tajam (*anti-aliasing gamma 0.85*):
    - **KARSA KERENTJENG**: Ilustrasi tangan artistik gaya woodcut memegang benih/tanah dengan tipografi tegas (`logo-karsa-kerentjeng.png`, `logo-karsa-kerentjeng-white.png`).
    - **an du**: Tipografi khas 2x2 geometris sudut lengkung modular (`logo-andu.png`, `logo-andu-white.png`).
    - **Ethera**: Tipografi elegan bergaya serif modern (`logo-ethera.png`, `logo-ethera-white.png`).
    - **Banner Trio Lengkap**: `partner-all-partners-dark.png` dan `partner-all-partners-white.png`.
  - Mengintegrasikan logo-logo ini secara otomatis ke seluruh komponen *Presented By*, header, footer, spotlight kopi an du, dan badge produk.

### 2. Panel Admin Pengelolaan Penuh (Full Control Website Management)
- **Katalog Produk & Kopi Spesialti (`/admin/produk`)**:
  - **Filter Kategori Cepat**: Tab filter instan (*Semua Produk*, *☕ Kopi Spesialti*, *👕 Kaos & Merchandise*, *🎁 Paket Bundle & Donasi*).
  - **Pratinjau Foto Produk**: Kolom foto thumbnail riil di tabel produk dengan batas rasio tajam.
  - **Modal Tambah & Edit Produk**: Form lengkap dengan unggah file foto asli (`multipart/form-data`), kategori, harga dasar, berat gram, urutan tampil, dan deskripsi notes rasa.
  - **Modal Kelola Varian & Stok**: Admin dapat melihat rincian seluruh varian per produk, memperbarui harga & stok gudang secara inline, menambahkan varian baru (misal: Biji Utuh, Giling Halus, Giling Kasar, 200gr, 500gr, 1kg), serta menghapus varian.
  - **Hapus Produk**: Tombol hapus dengan konfirmasi aman (otomatis beralih ke nonaktif jika produk memiliki riwayat pesanan).
- **Manajemen Tanya Jawab / FAQ (`/admin/faq`)**:
  - Pembuatan Controller baru `AdminFaqController.php` dan tampilan `admin/faqs.php`.
  - Full CRUD: Tambah FAQ baru, Edit FAQ, Hapus FAQ, dan tombol sakelar toggle status aktif/nonaktif.
  - Filter kategori FAQ (Umum, Donasi, Kopi, Event, Pembayaran, Pohon, Akun) dan pencarian kata kunci.
- **Manajemen Lokasi Penanaman & Titik Konservasi (`/admin/lokasi`)**:
  - Pembuatan Controller baru `AdminLocationController.php` dan tampilan `admin/locations.php`.
  - Full CRUD titik lokasi penanaman pohon: Nama lokasi, Kota/Kabupaten, Provinsi, Koordinat GPS (Latitude & Longitude), target pohon, unggah foto lokasi, dan deskripsi karakteristik lahan.
  - Indikator visual *progress bar* realisasi pohon tertanam terhadap target kuota.
- **Pembaruan Navigasi Sidebar Admin (`layouts/admin.php`)**:
  - Menata ulang menu navigasi menjadi kelompok tematik yang intuitif:
    - **Ringkasan**: Dashboard
    - **Pembayaran**: Verifikasi Pembayaran (dengan badge notifikasi tertunda), Rekening Bank
    - **Data & Penanaman**: Donasi Pohon, Kegiatan & Jadwal, Lokasi Penanaman, Peserta & Relawan, Check-in QR, Pohon & Spesies
    - **Konten & Informasi**: Postingan / Kabar, FAQ / Tanya Jawab
    - **Toko & Kopi**: Produk & Kopi, Pesanan Toko
    - **Sistem**: Laporan, Pengaturan Website

---

## [2026-11-07 20:10] Optimasi Tampilan Android/Mobile & Integrasi Foto Pohon Nyata

### 1. Optimasi Tampilan Mobile / Android (Touch Ergonomics & Responsive Layout)
- **Eliminasi Geser Horizontal**: Mengunci `overflow-x: hidden` pada `html, body` sehingga tidak ada pergeseran layar ke samping pada layar smartphone Android.
- **Tipografi Responsif Terukur**: Mengecilkan skala judul besar (`.display-1` s/d `.display-6`) saat diakses melalui viewport kecil (di bawah 768px) agar tidak terpotong.
- **Optimasi Bingkai Poster Hero (`.hero-poster-img`)**: Membatasi tinggi maksimal gambar poster di layar ponsel (maks 290px) agar tombol CTA utama tetap terlihat tanpa harus scroll terlalu jauh.
- **Target Sentuh Ramah Jari (Android Guidelines 48px)**:
  - Tombol CTA (`.btn-lg` & `.btn-cta-group`) melebar 100% dan memiliki tinggi sentuh minimum 48px yang nyaman ditekan ibu jari.
  - Menu navigasi mobile memiliki padding luas (44px+) dengan latar kartu mengambang berbayang halus.
  - Kartu logo partner resmi (`.partner-logo-box`) otomatis tersusun dalam grid rapi 2 kolom pada ponsel.
  - Top bar ticker ekspedisi otomatis memusat (*center*) di mobile tanpa tumpang tindih.

### 2. Integrasi Foto Pohon Asli (Agroforestri & Konservasi)
- **Aset Foto Pohon Beresolusi Tinggi (4:3)**:
  - `tree-kopi.jpg` (800x600): Pohon Kopi Arabika Kerenceng berbuah ranum di lereng pegunungan agroforestri.
  - `tree-alpukat.jpg` (800x600): Pohon Alpukat Mentega penaung kebun kopi berdaun lebat penahan erosi lereng.
  - `tree-trembesi.jpg` (800x600): Pohon Trembesi peneduh kanopi raksasa penyerap CO₂ tinggi.
  - `tree-mahoni.jpg` (800x600): Pohon Mahoni hutan konservasi tangkapan air pegunungan.
  - `tree-mangga.jpg` (800x600): Pohon Mangga buah tropis produktif.
- **Pembaruan Database & Tampilan**:
  - Memperbarui kolom `image` pada tabel `tree_species` untuk semua jenis pohon.
  - Menempatkan foto asli pohon pada etalase beranda (`home.php`) menggantikan ikon emoji statis.
  - Menyematkan thumbnail foto pohon asli pada formulir donasi (`donation.php`) agar donatur dapat melihat wujud pohon yang akan ditanam.

---

## [2026-11-07 20:05] Foto Produk Kopi Spesialti & Galeri Visual Realistis

### 1. Kurasi & Integrasi Foto Kopi Asli
- **Aset Fotografi Kopi Spesialti Beresolusi Tinggi (Standar 4:3)**:
  - `coffee-honey.jpg` (800x600): Kemasan pouch kopi artisan *Arabica Kerenceng Honey Process x an du* dengan hamparan biji kopi sangrai di atas meja kayu pedesaan.
  - `coffee-anaerobic.jpg` (800x600): Buah ceri kopi merah segar (*red cherries*) dipetik matang di kebun kopi lereng Kerenceng.
  - `coffee-drip-bag.jpg` (800x600): Seduhan kopi filter *Drip Bag Trail Edition* di alam bebas dengan cangkir enamel petualang.
  - `coffee-cold-brew.jpg` (800x600): Botol kaca farmasi 500ml *Cold Brew Concentrate Botanic Brew* dengan es batu kristal.
  - `coffee-plantation.jpg` (800x600): Lanskap hutan agroforestri Kebon Kopi Kerenceng berkabut di ketinggian 1.550 mdpl.
- **Pencatatan Database `product_images`**:
  - Mengaitkan gambar secara relasional ke seluruh ID produk kopi (Produk ID 4, 5, 6, 7).
- **Tampilan Galeri Visual**:
  - **Beranda (`/`)**: Kartu unggulan an du Spotlight menampilkan foto produk riil lengkap dengan strip mini galeri kopi.
  - **Etalase Kopi (`/`)**: Kartu produk kopi kini menampilkan foto kemasan & seduhan asli menggantikan ikon statis.
  - **Katalog Toko (`/toko`)**: Grid kartu menampilkan foto kemasan kopi bersudut tumpul estetis dengan badge overlay roastery.
  - **Detail Produk (`/toko/{slug}`)**: Tampilan foto produk utama dengan 3 galeri thumbnail foto biji, ceri, dan kebun kopi.

---

## [2026-11-07 20:00] Rilis Identitas Baru "Plant For Fun" & Logo Vektor Botanikal Outdoor

### 1. Pergantian Nama & Identitas Aplikasi ("Plant For Fun")
- **Penyelarasan Nama Resmi**: Menyelaraskan seluruh nama platform dengan tema poster kegiatan menjadi **Plant For Fun** (`.env`, `config/app.php`, meta tags, dan judul halaman).
- **Tagline Resmi**: *Kerenceng Coffee Planting & Agroforestry Expedition* &bull; *Plant Trees Preserve Tomorrow*.

### 2. Desain Logo Vektor Baru (Botanical Coffee Crest & Expedition Emblem)
- **Desain Emblem Botanikal (`logo-emblem.svg` & `favicon.svg`)**:
  - Mengambil inspirasi langsung dari karya ilustrasi poster resmi: pohon kopi dengan dedaunan berurat halus, buah ceri kopi matang (*cherries*), serta akar pohon yang menjulur kokoh.
  - Siluet kontur puncak Gunung Kerenceng (1.742 mdpl) di bagian dalam lingkaran.
  - Bingkai stempel ekspedisi alam bebas dengan garis jahitan kontur putus-putus (*dashed trail line*) dan tipografi melingkar: `PLANT FOR FUN ★ KERENCENG • 1.742M ★`.
- **Wordmark Horizontal Resmi**:
  - `logo-plant-for-fun.svg`: Varian warna Deep Pine Green (`#12382c`) dan Trail Lime (`#bfd254`) untuk navbar terang.
  - `logo-plant-for-fun-white.svg`: Varian kontras tinggi putih & lime untuk footer gelap.
- **Penerapan Terintegrasi**:
  - Navbar utama publik (`navbar.php`)
  - Footer situs publik (`footer.php`)
  - Favicon tab browser (`public/favicon.svg`)
  - Admin Panel sidebar (`layouts/admin.php`)

---

## [2026-11-07 19:55] Rilis Toko Kopi Spesialti Kerenceng, Spotlight "an du", & Redesain Landing Page Super Keren

### 1. Penjualan Kopi Spesialti Kerenceng x an du
- **Menambahkan Kategori `kopi` pada Katalog Produk**:
  - `Arabica Kerenceng Honey Process x an du` (Single origin 1.550 mdpl, Sigarar Utang, notes: Red Apple, Wild Honey, Jasmine).
  - `Kerenceng Natural Anaerobic Reserve x an du` (Micro-lot langka 72 jam anaerobik, notes: Dark Cherry, Winey Plum, Dark Chocolate).
  - `Drip Bag Coffee Box (Isi 10 Sachet) - an du Trail Edition` (Kopi filter praktis untuk petualang & pendaki di gunung).
  - `Cold Brew Concentrate 500ml - an du Botanic Brew` (Ekstraksi dingin 18 jam air pegunungan Kerenceng).
- **Varian Biji & Gilingan**:
  - Mengubah kolom `product_variants.size` menjadi fleksibel (`VARCHAR(50)`) untuk mendukung opsi: *200gr &bull; Biji Utuh (Whole Bean)*, *Giling Filter (Medium)*, *Giling Espresso (Fine)*, *500gr Pouch*, *Box 10 Sachet*, dan *Botol Kaca 500ml*.
- **Integrasi Keranjang & Checkout**:
  - Sistem keranjang belanja, perhitungan bobot gram, serta checkout pembayaran manual 100% teruji sukses mendukung pembelian produk kopi dan merchandise sekaligus.

### 2. Penonjolan Brand "an du" (Creative Studio & Roastery)
- **Top Bar Expedition Ticker**: Menampilkan kredit `PRESENTED BY: an du &bull; KARSA KERENTJENG &bull; ETHERA` lengkap dengan koordinat GPS `-6.9832, 107.8921` dan elevasi `1.742 mdpl`.
- **Navbar Co-Branding**: Sub-judul brand `COLLAB WITH an du` serta menu navigasi `Kopi & Toko` dengan badge penanda `an du ☕`.
- **Section Khusus "an du Spotlight"**:
  - Wadah visual modern (`.andu-spotlight`) bergradasi pine gelap dan mikro-pola dot-matrix.
  - Narasi peran **an du** sebagai roastery kurator biji kopi agroforestri lereng Kerenceng.
  - Kartu sorotan signature beans dengan tombol beli cepat.
- **Badge & Co-Branding pada Seluruh Produk Kopi**: Menampilkan logo an du transparan dan label `ROASTED BY an du`.

### 3. Landing Page Lebih Keren & Penawaran Beragam
- **Hero Section Poster Frame**: Tampilan berbingkai estetis dengan ilustrasi asli penanaman pohon kopi di atas telapak tangan manusia.
- **Real Impact Ticker**: Indikator pohon tertanam, elevasi puncak (1.742 mdpl), pemberdayaan petani, dan serapan CO₂.
- **Etalase Kopi Spesialti Kerenceng**: Grid produk kopi dengan chip tasting notes, badge elevasi, dan tombol pesan langsung.
- **Program Adopsi Pohon Penaung**: Pilihan donasi bibit pohon kopi, alpukat, kayu manis, dan trembesi.
- **Itinerary Ekspedisi**: Jadwal kegiatan *Coffee Morning by an du*, aksi tanam, dan sesi *manual brewing/cupping* di lereng gunung.
- **Transparansi & Peta GPS**: Cuplikan pelacakan posisi pohon di alam bebas.

---

## [2026-11-07 19:45] Desain Baru: Botanical Expedition & Ekstraksi Logo Resmi "Plant For Fun: Kerenceng"

### 1. Ekstraksi Logo Resmi (Transparan PNG & Versi Kontras Terang/Gelap)
- Mengekstrak 5 logo mitra dan sponsor resmi langsung dari gambar poster dan close-up aset:
  - **Presented By**:
    - `an du` (`logo-andu.png` & `logo-andu-white.png`) — Logo tipografi geometris 2x2 grid.
    - `Karsa Kerentjeng` (`logo-karsa-kerentjeng.png` & `logo-karsa-kerentjeng-white.png`) — Ilustrasi kepalan tangan/ikatan kopi & tipografi serif vintage.
    - `Ethera` (`logo-ethera.png` & `logo-ethera-white.png`) — Wordmark serif miring berkelas.
  - **Media Partner**:
    - `CLK.PNY (Tjitjalengka Poenja)` (`logo-clkpny.png` & `logo-clkpny-white.png`) — Lambang puncak gunung kembar & teks koordinat Cicalengka.
    - `Cicalengka GO` (`logo-cicalengka-go.png` & `logo-cicalengka-go-white.png`) — Emblem lingkaran badge outdoor & ikon sepeda.
- Seluruh logo diekstrak dengan saluran transparansi (alpha channel) yang bersih dan dibuat dalam 2 varian:
  - Varian Hijau Hutan Gelap (`#12382c`) untuk background cerah / kartu putih.
  - Varian Putih Bersih (`#ffffff`) untuk background footer gelap / hero banner malam.

### 2. Integrasi Komponen Parsial Logo (`resources/views/partials/partner-logos.php`)
- Menggantikan pendekatan perkiraan SVG manual dengan tag `<img>` berbasis PNG transparan beresolusi tinggi.
- Otomatis menyesuaikan proporsi tinggi, padding, serta kontras warna berdasarkan parameter `$logoTheme` (`'dark'` atau `'light'`).
- Terpasang rapi di:
  - Bagian bawah bingkai poster Hero Beranda (`/`)
  - Halaman Tentang Kami (`/tentang-kami`)
  - Halaman Indeks Kegiatan Relawan (`/kegiatan`)
  - Halaman Detail Kegiatan Kebon Kopi Kerenceng (`/kegiatan/3`)
  - Footer Utama di seluruh halaman publik (`footer.php`)

### 3. Penyatuan Tema Visual (Botanical Outdoor & Agroforestry Expedition)
- **Palet Warna**:
  - Warna Utama: Pine Forest Deep Green (`#12382c`)
  - Aksen Jalur: Topo Trail Chartreuse / Lime (`#bfd254`)
  - Latar Belakang: Warm Paper Cream (`#f4f7f4`) & Topo Sage (`#dbe7dc`)
- **Pola Kontur Peta Topografi**:
  - Menghasilkan SVG pola kontur topografi (`.topo-pattern` dan `.topo-pattern-light`) yang menyerupai peta pendakian Kebon Kopi Kerenceng (1.742 mdpl).
- **Tipografi**:
  - Heading & Display: Menggunakan font `Bitter` (vintage slab serif botanikal).
  - Data / Tanggal / Koordinat: Menggunakan `Space Mono` untuk nuansa ekspedisi lapangan.
  - Teks Isi: `Plus Jakarta Sans` yang ramah pembaca dan elegan.
- **Harmonisasi Seluruh Halaman Publik**:
  - `home.php`: Layout berbingkai menyerupai poster cetak, kartu sorotan Kerenceng, dan galeri bibit.
  - `about.php`: Visi agroforestri & etika konservasi pohon.
  - `events/index.php` & `events/detail.php`: Informasi jadwal, titik kumpul Kebon Kopi Kerenceng, dan tiket QR.
  - `donation.php`: Formulir donasi bertema ekspedisi konservasi dengan ringkasan serapan karbon.
  - `transparency.php`, `leaderboard.php`, `verify_certificate.php`, `check-payment.php`, `shop/index.php`: Seluruh header dan kartu telah menggunakan styling bertema konsisten.
  - `navbar.php`: Navigasi bersih dengan logo beraksen hijau dan penanda aktif yang selaras.

---

## [2026-11-05 16:51] Bugfix Model — Kompatibilitas Signature & Eksekusi PDO (`UserAddress.php` & `SupportTicket.php`)

### Masalah
- Muncul `Fatal error: Declaration of UserAddress::update(int $id, int $userId, array $data): bool must be compatible with Model::update(string|int $id, array $data): bool`.
- Terdapat pemanggilan metode non-eksis pada objek PDO langsung (`$this->db->fetchAll`, `$this->db->fetch`, `$this->db->execute`) pada `UserAddress.php` dan `SupportTicket.php`.

### Perbaikan
- **`app/Models/UserAddress.php`**:
  - Mengubah nama metode `update` menjadi `updateAddress` dan `delete` menjadi `deleteAddress` agar tidak bertabrakan dengan signature metode bawaan class induk `Model`.
  - Mengganti seluruh query raw agar menggunakan prepared statement PDO resmi: `$stmt = $this->db->prepare($sql); $stmt->execute($params);`.
- **`app/Models/SupportTicket.php`**:
  - Menstandarkan seluruh query baca/tulis menggunakan prepared statement PDO `$this->db->prepare()`.
- **`app/Controllers/DashboardController.php`**:
  - Menyesuaikan pemanggilan `updateAddress` dan `deleteAddress`.
- **Hasil**:
  - Seluruh 19 file Model diuji dan di-load 100% sukses tanpa galat.
  - Endpoint `GET /dashboard/profil` berhasil diakses (HTTP 200 OK, 45.6 KB).
  - Endpoint `GET /dashboard/bantuan` berhasil diakses (HTTP 200 OK, 28.4 KB).

---

## [2026-11-05 16:46] Fitur Baru — CRUD Jenis Pohon Bebas & Modul Postingan / Kabar Lapangan

### 1. Pengelolaan Bebas Jenis / Spesies Pohon & Data Pohon (`AdminTreeController.php`)
- **Navigasi 2 Tab di `/admin/pohon`**:
  - **Tab Pohon Tertanam**: Menampilkan seluruh data pohon dengan tombol modal **Edit Data Pohon** (Admin leluasa mengubah status perkembangan: pending/ditanam/tumbuh/dipantau, tanggal tanam, lokasi penanaman, jenis pohon, dan titik koordinat GPS latitude/longitude).
  - **Tab Kelola Jenis / Spesies Pohon** (`/admin/pohon?tab=spesies`): Menampilkan tabel master data jenis pohon (foto, nama, harga donasi/pohon, serapan CO₂ kg/thn, produksi O₂ kg/thn, total tertanam, dan status aktif).
- **Aksi CRUD Spesies Pohon**:
  - Tambah jenis pohon baru dengan upload foto/gambar (`storeSpecies`).
  - Edit jenis pohon (nama, harga, serapan karbon, oksigen, deskripsi, status aktif/nonaktif) (`updateSpecies`).
  - Hapus jenis pohon: jika sudah memiliki riwayat penanaman/donasi, sistem cerdas menonaktifkan statusnya agar integritas data historis tetap aman (`deleteSpecies`).
  - Edit data pohon individual (`updateTree`).

### 2. Modul Lengkap Postingan / Berita Konservasi
- **Controller Admin (`app/Controllers/AdminNewsController.php`)**:
  - `index()` — Daftar postingan di `/admin/postingan` dengan filter status (Diterbitkan / Draf), info penulis, slug, dan tanggal publikasi.
  - `create()` & `store()` — Form tulis artikel baru (`/admin/postingan/tambah`) dengan upload sampul featured image, konten artikel lengkap, dan opsi langsung terbit atau simpan sebagai draf.
  - `edit()` & `update()` — Form edit artikel (`/admin/postingan/{id}/edit`).
  - `delete()` — Hapus postingan (`/admin/postingan/{id}/hapus`).
  - `togglePublish()` — Tombol cepat 1-klik untuk beralih status antara Diterbitkan dan Draf.
- **Controller Publik (`app/Controllers/NewsController.php`)**:
  - `index()` — Halaman portal berita publik di `/kabar` dengan layout kartu responsif, pagination, dan ringkasan konten.
  - `show($slug)` — Halaman baca artikel lengkap di `/kabar/{slug}` dengan integrasi tombol bagikan (WhatsApp, Twitter/X), sampul hero, dan rekomendasi artikel terkini.
- **Layout & Seeder**:
  - Menambahkan menu `Postingan / Kabar` pada sidebar admin (`resources/views/layouts/admin.php`).
  - Menambahkan 3 data seeder artikel berita awal yang realistis dan informatif.

### Verifikasi Endpoint:
- `GET /admin/pohon?tab=spesies` → HTTP 200 OK (48 KB) ✅
- `GET /admin/pohon?tab=pohon` → HTTP 200 OK (38.4 KB) ✅
- `GET /admin/postingan` → HTTP 200 OK (23.6 KB) ✅
- `GET /admin/postingan/tambah` → HTTP 200 OK (14.6 KB) ✅
- `GET /admin/postingan/1/edit` → HTTP 200 OK (16.1 KB) ✅
- `GET /kabar` (Portal Berita Publik) → HTTP 200 OK (18.4 KB) ✅
- `GET /kabar/{slug}` (Baca Artikel Publik) → HTTP 200 OK (18.3 KB) ✅

---

## [2026-11-05 16:40] Bugfix Schema — Kolom `planted_at` & Relasi Tabel `trees` (`/admin/pohon`)

### Masalah
- Muncul `Fatal error: Uncaught PDOException: Unknown column 'tr.planting_date' in 'SELECT'` saat mengakses `/admin/pohon`.
- Nama kolom di skema basis data `trees` adalah `planted_at` (bukan `planting_date`).
- Tabel `trees` terhubung ke donatur via `donation_item_id -> donation_items -> donations -> users`, serta ke lokasi via `planting_location_id -> planting_locations`.

### Perbaikan
- Memperbaiki `app/Controllers/AdminTreeController.php`:
  - Mengubah query `SELECT` agar menggunakan kolom riil `tr.planted_at`, `tr.code`, `tr.status`, dan `tr.planting_location_id`.
  - Melakukan `LEFT JOIN` ke `donation_items`, `donations`, dan `users` untuk mendapatkan nama donatur secara akurat (`COALESCE(u.name, d.donor_name)`).
  - Mengambil foto monitoring pohon dari subquery `tree_trackings`.
- Memperbaiki `resources/views/admin/trees.php`:
  - Menyesuaikan penamaan kolom tabel: Kode Pohon, Spesies, Donatur/Penanam, Lokasi Tanam, Koordinat GPS, Tanggal Tanam, dan Status (`pending`, `ditanam`, `tumbuh`, `dipantau`).
- Hasil: `GET /admin/pohon` berhasil diakses (HTTP 200 OK, 19.8 KB) tanpa galat SQL.

---

## [2026-11-05 16:37] Bugfix UI — Halaman Check-in QR Scanner (`/admin/checkin-qr`)

### Masalah
- Kotak overlay viewfinder kamera menggunakan `box-shadow: 0 0 0 5000px rgba(0, 0, 0, 0.4)` tanpa pembatas `overflow: hidden` pada elemen induk.
- Hal ini menyebabkan bayangan hitam 5000px bocor menutupi seluruh card scanner, header, background, dan separuh halaman di sisi kiri sehingga tampilan menjadi abu-abu gelap/redup.

### Perbaikan
- Memperbaiki `resources/views/admin/qr_checkin.php`:
  - Menambahkan pembungkus `.scanner-viewport` dengan `overflow: hidden`, `border-radius: 16px`, dan ukuran responsif terkontrol.
  - Menambahkan placeholder visual elegan saat kamera belum dinyalakan (ikon kamera, instruksi jelas).
  - Overlay viewfinder target hanya ditampilkan saat kamera aktif, dilengkapi dengan animasi *laser scanning bar* hijau.
  - Memperbarui komponen input manual kode tiket menggunakan `input-group` Bootstrap yang bersih dan presisi.
- Hasil: Tampilan halaman kembali cerah, bersih, proporsional, dan tanpa kebocoran bayangan CSS.

---

## [2026-11-05 16:35] Modul 11 — Admin Panel & Verifikasi Pembayaran Manual

### File Baru
- `resources/views/layouts/admin.php` — Layout dashboard administrator
  - Sidebar dengan navigasi terstruktur, badge jumlah verifikasi pembayaran tertunda, responsive drawer mobile
  - Topbar dengan breadcrumb, jam waktu nyata (WIB), dropdown akun admin & navigasi cepat ke situs
- Controllers Admin:
  - `app/Controllers/AdminDashboardController.php` — Ringkasan metrik (donasi, pohon, pendapatan, user, kegiatan, pesanan), grafik Chart.js pendapatan 6 bulan, daftar antrian pembayaran & log aktivitas terbaru
  - `app/Controllers/AdminPaymentController.php` — Antrian verifikasi manual, pagination, filter status, deteksi duplikat SHA-256 hash bukti transfer, preview bukti pembayaran, verifikasi setujui (auto-update relasi donasi/pesanan/peserta), tolak dengan alasan wajib
  - `app/Controllers/AdminBankController.php` — Manajemen rekening bank tujuan transfer (CRUD, aktivasi & proteksi hapus rekening berelasi)
  - `app/Controllers/AdminEventController.php` — Manajemen kegiatan penanaman pohon (CRUD lengkap, kuota peserta, biaya pendaftaran, status kegiatan, koordinator)
  - `app/Controllers/AdminParticipantController.php` — Manajemen peserta relawan, konfirmasi manual, pemindai kamera HTML5 WebRTC QR check-in (`qrScanner`), prosesor check-in AJAX (`processCheckin`) dengan audio beeper & deteksi duplikasi kehadiran
  - `app/Controllers/AdminOrderController.php` — Manajemen pesanan merchandise kaos, pembaruan status ke 'dikirim' dengan input resi & kurir, konfirmasi pesanan selesai
  - `app/Controllers/AdminDonationController.php` — Monitoring donasi masuk, ringkasan agregat finansial & pohon terdanai
  - `app/Controllers/AdminProductController.php` — CRUD produk merchandise, slug generator otomatis, pemantauan varian & stok
  - `app/Controllers/AdminTreeController.php` — Inventaris pohon tertanam, geotagging koordinat Google Maps, status pertumbuhan & galeri foto
  - `app/Controllers/AdminReportController.php` — Laporan analitik platform: visualisasi Chart.js pendapatan tahunan, penyerapan CO2 per spesies pohon, klasemen 10 donatur teratas, breakdown status merchandise
  - `app/Controllers/AdminSettingController.php` — Pengaturan operasional platform (nama aplikasi, email admin, support WhatsApp, invoice timeout, bonus poin referral)
- Views Admin:
  - `resources/views/admin/dashboard.php`
  - `resources/views/admin/payments.php`
  - `resources/views/admin/payment_detail.php`
  - `resources/views/admin/banks.php`
  - `resources/views/admin/events.php`
  - `resources/views/admin/participants.php`
  - `resources/views/admin/qr_checkin.php`
  - `resources/views/admin/orders.php`
  - `resources/views/admin/donations.php`
  - `resources/views/admin/products.php`
  - `resources/views/admin/trees.php`
  - `resources/views/admin/reports.php`
  - `resources/views/admin/settings.php`

### File Dimodifikasi
- `routes/web.php`
  - Menambahkan 22 rute admin lengkap dengan proteksi middleware admin (`/admin`, CRUD rekening, verifikasi pembayaran setujui/tolak, CRUD kegiatan, konfirmasi peserta, check-in QR, CRUD produk, update stok varian, pengiriman pesanan, laporan, dan pengaturan)

### Verifikasi & Pengujian
- `GET /admin` → HTTP 200 OK ✅
- `GET /admin/verifikasi-pembayaran` → HTTP 200 OK ✅
- `GET /admin/verifikasi-pembayaran/1` → HTTP 200 OK ✅
- `GET /admin/rekening` → HTTP 200 OK ✅
- `GET /admin/donasi` → HTTP 200 OK ✅
- `GET /admin/peserta` → HTTP 200 OK ✅
- `GET /admin/kegiatan` → HTTP 200 OK ✅
- `GET /admin/pohon` → HTTP 200 OK ✅
- `GET /admin/produk` → HTTP 200 OK ✅
- `GET /admin/pesanan` → HTTP 200 OK ✅
- `GET /admin/laporan` → HTTP 200 OK ✅
- `GET /admin/pengaturan` → HTTP 200 OK ✅
- `GET /admin/checkin-qr` → HTTP 200 OK ✅
- Akses Guest/Unauthenticated `/admin` → HTTP 302 Redirect to `/masuk` (Middleware Admin Terverifikasi Aman) ✅

---

## [2026-11-05 16:18] Modul 10 — Profil & Pusat Bantuan

### File Baru
- `app/Models/UserAddress.php` — Model CRUD alamat pengiriman user
  - `getUserAddresses()` — Ambil semua alamat, utama diurutkan pertama
  - `findById()` — Anti-IDOR: selalu sertakan user_id
  - `create()` — Tambah alamat baru; otomatis jadi utama jika pertama
  - `update()` — Edit alamat (anti-IDOR)
  - `delete()` — Hapus alamat; tolak jika alamat utama & masih ada lain
  - `setPrimary()` — Set utama: lepas semua flag lain dulu
  - `countByUser()`, `getPrimary()`
- `app/Models/SupportTicket.php` — Model tiket bantuan (support_tickets + support_replies)
  - `getUserTickets()` — Daftar tiket dengan preview balasan terakhir
  - `getTicketWithReplies()` — Detail tiket + semua balasan (anti-IDOR)
  - `createTicket()` — Buat tiket baru
  - `addReply()` — Balas tiket; auto-update status (replied/open)
  - `closeTicket()` — Tutup tiket (user/admin)
  - `countByStatus()`, `findByIdAdmin()`
- `resources/views/dashboard/profile.php` — Halaman profil 3-tab
  - Tab Info Diri: upload avatar (preview instan, auto-submit), nama, telepon, kota, ukuran kaos, opsi anonim
  - Tab Keamanan: ganti password dengan strength indicator + match feedback + toggle visibility
  - Tab Alamat: CRUD alamat dengan modal tambah/edit/hapus, badge "Utama", dropdown aksi
- `resources/views/dashboard/support.php` — Halaman pusat bantuan
  - 3 kartu ringkasan status (open/dibalas/selesai)
  - Daftar tiket dengan bubble preview, time_ago, jumlah balasan
  - Modal kirim tiket baru dengan kategori pilihan (auto-prefix subjek) + character counter
  - FAQ Accordion 5 pertanyaan umum
- `resources/views/dashboard/ticket_detail.php` — Detail tiket + thread percakapan
  - Bubble chat: user (kiri, hijau) vs admin (kanan, biru)
  - Form balas tiket dengan counter karakter
  - Tombol tutup tiket (dengan konfirmasi JS)
  - Auto-scroll ke balasan terakhir

### File Dimodifikasi
- `app/Controllers/DashboardController.php` (+486 baris)
  - `profile()` — Full: load user + alamat + stats
  - `updateProfile()` — POST: validasi & update nama/telepon/kota/ukuran kaos/anonim
  - `updatePassword()` — POST: verifikasi password lama, bcrypt baru, auto-logout
  - `uploadAvatar()` — POST: validasi MIME strict (finfo), maks 2MB, hapus lama
  - `addresses()` — GET daftar alamat
  - `storeAddress()` — POST: tambah alamat (maks 5 per user)
  - `updateAddress(id)` — POST: edit alamat (anti-IDOR)
  - `deleteAddress(id)` — POST: hapus alamat
  - `setPrimaryAddress(id)` — POST: set utama
  - `support()` — GET: daftar tiket + count per status
  - `showTicket(id)` — GET: detail tiket (anti-IDOR)
  - `sendTicket()` — POST: buat tiket (maks 5 open sekaligus)
  - `replyTicket(id)` — POST: balas tiket
  - `closeTicket(id)` — POST: tutup tiket
- `app/Models/User.php`
  - `updateAvatar(userId, path)` — Method baru untuk update kolom avatar
- `core/Controller.php`
  - `isPost()` — Method pembantu cek REQUEST_METHOD === POST
- `routes/web.php`
  - Tambah 9 route baru: avatar, alamat CRUD, detail tiket, balas, tutup tiket

### Verifikasi
- `GET /dashboard/profil` → HTTP 200 ✅
- `GET /dashboard/bantuan` → HTTP 200 ✅

---

---

## Modul 1: Struktur Folder & Database
**Status**: ✅ Selesai (100%)

### [2026-11-05 11:15] Modul 1 — Inisialisasi Proyek
- ✅ Pilihan framework: **PHP Native MVC** (bukan Laravel) — alasan: kompatibilitas shared hosting/cPanel, ringan, kontrol penuh

### [2026-11-05 11:19] Modul 1 — Database Schema Selesai ✅
- ✅ `database.sql` — **1.035 baris SQL**, **38 tabel**, **47 foreign key**, **9 seeder INSERT**
- Tabel utama: users, roles, donations, donation_items, trees, tree_trackings, payments, payment_proofs, bank_accounts, planting_events, participants, tickets, products, product_variants, orders, order_items, shipments, certificates, badges, notifications, dll
- Pembayaran polimorfik: `payments.payable_type` + `payable_id` (donation/order/participant)
- Seeder: 5 roles, 1 admin (admin@tanampohon.id), 5 jenis pohon, 6 lencana, 30 settings, 2 rekening bank, 10 FAQ, 3 halaman statis, 2 lokasi tanam
- Semua ENUM menggunakan istilah Indonesia (menunggu_pembayaran, disetujui, dll)

### [2026-11-05 11:25] Modul 1 — Core MVC & Struktur Selesai ✅
- ✅ `core/` — App, Router, Controller, Model, Database, Request, Response, Session, Validator
- ✅ `app/Helpers/helpers.php` — fungsi pembantu: env, config, url, asset, csrf_token, csrf_field, old, flash, auth, format_rupiah, format_date, time_ago, generate_invoice_number, generate_unique_code, str_slug, e, sanitize
- ✅ `app/Middleware/` — AuthMiddleware, AdminMiddleware, GuestMiddleware, CsrfMiddleware
- ✅ `.env.example` & `.env` — konfigurasi environment lengkap
- ✅ `composer.json` — dependensi PHP 8.1+, dompdf, phpmailer, endroid/qr-code, phpspreadsheet
- ✅ `README.md` — panduan instalasi lokal (XAMPP/PHP CLI), deployment cPanel, arsitektur folder

---

## Modul 2: Autentikasi & Role
**Status**: ✅ Selesai (100%)

### [2026-11-05 11:30] Modul 2 — Autentikasi & Role Selesai ✅
- ✅ `app/Models/User.php` — Model pengguna lengkap: findByEmail, findWithRole, findByReferralCode, findByResetToken, findByVerifyToken, createUser (bcrypt), generateUniqueReferralCode, createResetToken, resetPasswordWithToken, verifyEmailToken, updateProfile, updatePassword, addPoints, getUserStats
- ✅ `app/Models/Role.php` — Model role (super_admin, admin_keuangan, admin_event, admin_konten, peserta)
- ✅ `app/Controllers/AuthController.php` — Kontroller autentikasi lengkap:
  - `showLogin()` & `login()`: rate limiting (maks 5x gagal dalam 15 menit), password_verify, proteksi akun banned, session regeneration, redirect sesuai role/intended URL
  - `showRegister()` & `register()`: validasi komprehensif, referral code auto-link & bonus poin, auto-login setelah pendaftaran
  - `logout()`: destroy session aman
  - `showForgotPassword()` & `forgotPassword()`: generate token reset 1 jam
  - `showResetPassword()` & `resetPassword()`: validasi token & password baru
  - `verifyEmail()`: verifikasi email token
- ✅ `resources/views/layouts/main.php` — Layout utama Bootstrap 5, Plus Jakarta Sans, Bootstrap Icons, SEO OpenGraph, CSRF meta
- ✅ `resources/views/layouts/auth.php` — Layout kartu autentikasi terpusat bernuansa hijau alami
- ✅ `resources/views/partials/navbar.php` — Navbar responsif: logo brand, menu navigasi, status login, dropdown profil & tombol logout aman
- ✅ `resources/views/partials/footer.php` — Footer informatif dengan legalitas, sosmed, dan catatan metode pembayaran manual
- ✅ `resources/views/partials/flash-messages.php` — Alert pesan interaktif (success, error, warning, info)
- ✅ `resources/views/auth/login.php` — Form login dengan toggle intip password & info akun demo admin
- ✅ `resources/views/auth/register.php` — Form register dengan opsi ukuran kaos default & input kode referral
- ✅ `resources/views/auth/forgot-password.php` & `reset-password.php` — Form reset kata sandi
- ✅ `public/assets/css/style.css` — Tema hijau alam, responsif mobile-first, card hover efek, tombol & badge
- ✅ `app/Controllers/HomeController.php` & Views publik (`home.php`, `about.php`, `terms.php`, `privacy.php`, `faq.php`)
- ✅ `routes/web.php` — Konfigurasi routing lengkap untuk guest, auth, donasi, event, toko, dashboard, dan admin

---

## Modul 3: Donasi & Pembayaran Manual
**Status**: ✅ Selesai (100%)

### [2026-11-05 11:38] Modul 3 — Donasi & Pembayaran Manual Selesai ✅
- ✅ `docker-compose.yml` — Container MariaDB 10.11 otomatis inisialisasi skema 38 tabel dan data seeder
- ✅ `app/Models/BankAccount.php` — Model rekening bank tujuan & QRIS aktif
- ✅ `app/Models/TreeSpecies.php` — Model katalog jenis pohon & harga
- ✅ `app/Models/Payment.php` — Model transaksi pembayaran polimorfik (donation, order, participant):
  - Nomor invoice unik `INV-YYYYMMDD-XXXX`
  - Kode unik 3 digit acak (100 - 999) untuk pencocokan mutasi bank
  - Batas waktu otomatis 24 jam dengan hitung mundur
  - Deteksi kedaluwarsa otomatis
  - Riwayat pengunggahan berkas bukti transfer
- ✅ `app/Models/PaymentProof.php` — Model bukti pembayaran:
  - Whitelist tipe MIME asli (JPG, PNG, PDF maks 2 MB)
  - Hash SHA-256 untuk mendeteksi duplikasi berkas transfer ganda di transaksi berbeda
  - Penyimpanan berkas privat di luar web root (`storage/uploads/payment_proofs/`)
- ✅ `app/Models/Donation.php` — Model donasi pohon dalam transaksi DB (atomic):
  - Penghitungan subtotal & total pohon
  - Opsi donasi anonim
  - Opsi donasi hadiah / in memoriam (nama penerima + pesan dedikasi)
  - Integrasi instan ke tabel `payments`
- ✅ `app/Controllers/DonationController.php` — Katalog paket donasi & pemrosesan checkout donatur
- ✅ `app/Controllers/PaymentController.php`:
  - `show()`: Halaman instruksi pembayaran, hitung mundur, rekening tujuan, dan salin nominal/rekening 1-klik
  - `confirm()`: Validasi upload bukti transfer, MIME inspection, hash detection, re-upload jika ditolak
  - `checkStatus()` & `findInvoice()`: Pelacakan tagihan publik untuk tamu (guest) via invoice + email
  - `viewProof()`: Penyajian berkas bukti transfer secara aman (Anti-IDOR) hanya untuk pemilik/admin
- ✅ `resources/views/public/donation.php` — Formulir donasi interaktif: kalkulasi live subtotal & serapan CO2
- ✅ `resources/views/public/payment-instruction.php` — Instruksi pembayaran lengkap dengan countdown timer, copy buttons, live file preview
- ✅ `resources/views/public/check-payment.php` — Formulir pelacakan tagihan tamu

---

## Modul 4: Pendaftaran Peserta & E-Ticket QR
**Status**: ✅ Selesai (100%)

### [2026-11-05 11:48] Modul 4 — Pendaftaran Peserta & E-Ticket QR Selesai ✅
- ✅ `app/Models/PlantingEvent.php` — Model kegiatan penanaman: lokasi, jadwal, koordinat GMaps, koordinator, dan pengecekan kuota
- ✅ `app/Models/Participant.php` — Model pendaftaran relawan: penanganan event gratis (langsung terbit tiket) vs event berbayar/bundle kaos (terhubung ke payment manual invoice)
- ✅ `app/Models/Ticket.php` — Model E-Ticket: kode unik `TIKET-YYYYMMDD-XXXX`, payload data QR Code, dan mekanisme check-in kehadiran di lokasi acara
- ✅ `app/Controllers/EventController.php` — Kontroller jadwal aksi relawan, pendaftaran peserta, dan tampilan e-ticket
- ✅ `resources/views/public/events/index.php` — Daftar kegiatan penanaman dengan progress bar kuota dan badge harga/gratis
- ✅ `resources/views/public/events/detail.php` — Detail kegiatan: titik kumpul, peta lokasi, perlengkapan yang dibawa, kontak koordinator, dan form registrasi
- ✅ `resources/views/public/events/ticket.php` — E-Ticket resmi relawan dengan visual QR Code, nomor tiket, identitas peserta, dan tombol cetak tiket (`window.print()`)

---

## Modul 5: Toko Kaos & Ongkir
**Status**: ✅ Selesai (100%)

### [2026-11-05 11:50] Modul 5 — Toko Kaos & Ongkir Selesai ✅
- ✅ Seeder produk merchandise: Kaos Organik Penjaga Bumi (Cotton Combed 30s), Kaos Relawan Napas Untuk Bumi, dan Paket Bundle Kaos + 2 Pohon Mangga
- ✅ `app/Models/Product.php` — Model produk merchandise: varian ukuran (S-XXL) dan warna, penahanan stok (`reserveStock`), pelepasan stok (`releaseStock`), dan pemotongan stok permanen saat lunas (`commitStock`)
- ✅ `app/Models/Cart.php` — Keranjang belanja interaktif: penambahan varian, update kuantitas, validasi batas stok, kalkulasi subtotal dan total berat paket
- ✅ `app/Services/ShippingService.php` — Layanan kalkulasi ongkir kurir Indonesia (JNE, POS Indonesia, TIKI) berbasis berat paket & zona kota tujuan, serta opsi khusus **Ambil di Lokasi Acara (Bebas Ongkir / Rp 0)**
- ✅ `app/Models/Order.php` — Model pesanan kaos: transaksi atomik, penahanan stok varian, snapshot alamat lengkap pengiriman dalam format JSON, pembuatan tagihan pembayaran manual polimorfik (`payable_type = 'order'`) dengan kode unik 3 digit, dan pembuatan data shipment
- ✅ `app/Controllers/CartController.php` — Pengelolaan isi keranjang belanja
- ✅ `app/Controllers/ShopController.php` — Katalog merchandise kaos, rincian produk dengan selector varian & stok langsung, live AJAX kalkulasi ongkos kirim (`/api/ongkir`), dan pemrosesan checkout pesanan
- ✅ `resources/views/public/shop/index.php` — Katalog kaos & bundle donasi dengan filter kategori
- ✅ `resources/views/public/shop/detail.php` — Halaman produk dengan pemilih ukuran & warna, indikator sisa stok live, dan tombol tambah ke keranjang
- ✅ `resources/views/public/shop/cart.php` — Keranjang belanja dengan stepper kuantitas dan kalkulasi berat
- ✅ `resources/views/public/shop/checkout.php` — Checkout alamat lengkap dengan pemilih kurir real-time dan opsi ambil di acara

---

## Modul 6: Layout Dashboard Peserta & Ringkasan
**Status**: ✅ Selesai (100%)

### [2026-11-05 11:58] Modul 6 — Layout Dashboard Peserta & Ringkasan Selesai ✅
- ✅ `resources/views/layouts/dashboard.php` — Layout khusus area member peserta:
  - Sidebar desktop dengan avatar inisial, info user, poin kontribusi, navigasi menu lengkap, dan tombol keluar
  - Bottom navigation bar mobile responsif (Beranda, Donasi, Relawan, Kaos, Profil)
  - Topbar dengan toggle drawer mobile, tombol aksi cepat (Donasi Lagi, Beli Kaos), notifikasi, dan user dropdown
- ✅ `app/Controllers/DashboardController.php`:
  - `index()`: Statistik ringkasan (Pohon, Donasi, Aksi Relawan, Pesanan Kaos), lencana level kontribusi otomatis (Penanam Pemula s/d Legenda Hijau), estimasi serapan CO2 tahunan, data kontribusi pohon 6 bulan terakhir untuk Chart.js, kegiatan terdekat dengan hitung mundur hari-H, dan riwayat donasi terbaru
- ✅ `resources/views/dashboard/index.php` — Tampilan ringkasan dashboard lengkap:
  - Banner personal sapaan user + serapan CO2
  - 4 kartu statistik utama dengan ikon bernuansa alam
  - Kartu kegiatan terdekat + hitung mundur hari-H (H-X)
  - Grafik batang kontribusi pohon interaktif dengan Chart.js
  - Kartu bagikan kode referral dengan tombol salin 1-klik
  - Tabel riwayat donasi terbaru dengan status badge dan tombol bayar jika belum lunas

---

## Modul 7: Donasi Saya, Pohon Saya, Sertifikat PDF
**Status**: ✅ Selesai (100%)

### [2026-11-05 12:03] Modul 7 — Donasi Saya, Pohon Saya, dan Sertifikat PDF Selesai ✅
- ✅ `app/Models/Tree.php` — Model pohon individu: pengambilan pohon milik user, pelacakan pohon via kode unik `TREE-YYYYMMDD-XXXX`, status pemantauan (`ditanam`, `tumbuh`, `dipantau`), foto dokumentasi, dan pembuatan pohon otomatis saat donasi berhasil (`generateTreesForDonation`)
- ✅ `app/Models/Certificate.php` — Model penerbitan sertifikat digital otomatis: nomor sertifikat unik `CERT-YYYYMMDD-XXXX`, snapshot data donatur, spesies pohon, estimasi serapan CO2, dan tautan verifikasi keaslian publik
- ✅ `app/Controllers/DashboardController.php`:
  - `donations()`: Riwayat donasi lengkap dengan filter status (Semua, Berhasil, Menunggu, Ditolak), tombol upload bukti jika belum lunas, dan tombol unduh sertifikat jika berhasil
  - `trees()`: Manajemen pohon saya, agregasi total CO2, dan plotting koordinat peta Leaflet.js
  - `downloadCertificate($id)`: Tampilan sertifikat digital dengan proteksi kepemilikan Anti-IDOR
- ✅ `resources/views/dashboard/donations.php` — Halaman Donasi Saya dengan filter status dan kartu interaktif
- ✅ `resources/views/dashboard/trees.php` — Halaman Pohon Saya dilengkapi peta interaktif Leaflet.js, popup koordinat penanaman, status pemantauan, dan tombol bagikan dampak ke WhatsApp / Facebook / Twitter
- ✅ `resources/views/dashboard/certificate.php` — Desain sertifikat penghargaan beresolusi tinggi (A4 landscape) bernuansa hijau botani & emas, lengkap dengan QR Code verifikasi publik, stempel resmi, tanda tangan direktur, dan tombol cetak/simpan PDF

---

## Modul 8: Kegiatan Saya & Pesanan Kaos
**Status**: ✅ Selesai (100%)

### [2026-11-05 12:15] Modul 8 — Kegiatan Saya & Pesanan Kaos Selesai ✅
- ✅ `app/Models/Participant.php`:
  - `getUserParticipationsWithDetails()`: Query relasional lengkap peserta ke event penanaman, lokasi, tiket QR, pembayaran manual, ulasan pasca kegiatan, dan sertifikat partisipasi
  - `cancelRegistration()`: Pembatalan pendaftaran aman dengan transaksi atomik, validasi batas waktu H-1, pengembalian kuota event otomatis (`registered_count - 1`), pembatalan tiket, dan kedaluwarsa tagihan unpaid
  - `submitFeedback()`: Pengiriman ulasan & rating bintang 1-5 untuk kegiatan yang dihadiri, proteksi duplikasi, serta pemberian reward apresiasi **+10 Poin Kontribusi**
- ✅ `app/Models/Order.php`:
  - `getUserOrdersWithDetails()`: Menampilkan daftar pesanan kaos lengkap dengan varian ukuran/warna, kuantitas, rincian biaya, status pengiriman ekspedisi / ambil di acara, dan riwayat status bukti transfer
  - `confirmReceived()`: Konfirmasi pesanan telah diterima oleh pembeli, otomatis memperbarui status shipment menjadi `diterima` dan memberi apresiasi **+20 Poin Kontribusi**
  - `cancelOrder()`: Pembatalan pesanan yang belum dibayar, otomatis mengembalikan penahanan stok varian produk (`releaseStock`) secara aman
- ✅ `app/Models/Certificate.php`:
  - `issueParticipantCertificate()` & `findByParticipantId()`: Penerbitan sertifikat digital resmi untuk relawan aksi penanaman yang telah berpartisipasi/hadir di lokasi acara
- ✅ `app/Controllers/DashboardController.php`:
  - `events()`: Manajemen kegiatan relawan dengan filter status (Semua, Akan Datang, Selesai/Hadir, Dibatalkan), badge hitung mundur hari-H (H-X / Hari Ini), dan auto-issue sertifikat relawan
  - `cancelEventRegistration()`: Endpoint pembatalan keikutsertaan relawan
  - `submitEventFeedback()`: Endpoint ulasan bintang & testimoni relawan
  - `orders()`: Manajemen pesanan kaos dengan filter status (Semua, Belum Bayar, Diproses, Dikirim, Selesai, Dibatalkan)
  - `confirmOrderReceived()`: Endpoint konfirmasi penerimaan barang oleh pembeli
  - `cancelOrder()`: Endpoint pembatalan pesanan kaos belum lunas
- ✅ `routes/web.php`: Penambahan rute aksi feedback kegiatan `/dashboard/kegiatan/{id}/feedback` dan alias `/dashboard/pesanan/{id}/terima`
- ✅ `resources/views/dashboard/events.php`:
  - Kartu kegiatan interaktif dengan indikator visual dinamis
  - Filter tab dengan penghitung jumlah kegiatan otomatis
  - Titik kumpul, alamat, dan link langsung Google Maps
  - Kontak koordinator lapangan dengan tombol chat WhatsApp langsung
  - Tombol akses dan cetak E-Ticket QR
  - Modal pembatalan dengan alasan
  - Modal ulasan interaktif dengan selektor rating bintang & pesan panitia
  - Tombol unduh sertifikat relawan resmi
- ✅ `resources/views/dashboard/orders.php`:
  - Kartu pesanan terperinci dengan nomor pesanan, tanggal, dan status berwarna
  - Rincian item produk kaos, ukuran, warna, kuantitas, dan subtotal
  - Informasi pengiriman: kurir, nomor resi dengan tombol salin 1-klik, atau badge Ambil di Lokasi Acara (bebas ongkir)
  - Alert penolakan bukti transfer beserta alasan dari admin & tombol upload ulang
  - Batas waktu pembayaran dengan hitung mundur
  - Tombol aksi bayar sekarang, batalkan pesanan (dengan modal konfirmasi alasan), dan konfirmasi barang diterima
- ✅ `resources/views/dashboard/certificate.php`: Template sertifikat diperbarui untuk mendukung dua mode dinamis: Sertifikat Donasi Pohon dan Sertifikat Relawan Aksi Hijau

---

## Modul 9: Pencapaian, Referral, Notifikasi
**Status**: ✅ Selesai (100%)

### [2026-11-05 12:25] Modul 9 — Pencapaian, Referral, & Notifikasi Selesai ✅
- ✅ `app/Models/Badge.php`:
  - `getAllBadges()`: Pengambilan seluruh 6 lencana pencapaian aktif platform
  - `getUserBadges()`: Pengambilan lencana yang berhasil dibuka peserta
  - `syncUserBadges()`: Pengecekan otomatis batas pohon peserta, pemberian lencana baru ke `user_badges`, dan pengiriman notifikasi ucapan selamat dalam aplikasi
  - `getNextBadge()`: Kalkulasi sisa pohon yang dibutuhkan dan persentase progres menuju lencana berikutnya
  - `getUserEnvironmentalImpact()`: Estimasi serapan CO2 tahunan (kg), produksi oksigen (kg), dan luas tutupan hijau (m²)
  - `getUserLeaderboardRank()`: Perhitungan posisi peringkat real-time peserta di antara seluruh penanam aktif
- ✅ `app/Models/Notification.php`:
  - Model notifikasi lengkap: `getUserNotifications()`, `getUnreadCount()`, `createNotification()`, `markAsRead()`, `markAllAsRead()`, `deleteNotification()`
  - Tipe terdukung: `payment`, `donation`, `event`, `order`, `badge`, `referral`, `system`
- ✅ `app/Models/User.php`:
  - `getReferralStats()`: Menghitung total teman diajak, total pohon dari referral, total poin bonus referral, serta daftar teman bergabung dengan sensor privasi nama
- ✅ `app/Controllers/DashboardController.php`:
  - `certificates()`: Menggabungkan estimasi dampak lingkungan, posisi rank leaderboard, galeri lencana pencapaian (terkunci vs terbuka), progres bar lencana berikutnya, dan seluruh sertifikat digital resmi
  - `achievements()`: Alias ramah rute pencapaian
  - `referral()`: Halaman program ajak teman dengan kode referral unik, tautan pendaftaran, tombol salin 1-klik, dan tombol bagikan langsung ke WhatsApp, Twitter/X, Telegram, Facebook
  - `notifications()`: Halaman pusat notifikasi dengan tab filter Semua / Belum Dibaca
  - `markNotificationAsRead()`, `markAllNotificationsAsRead()`, `deleteNotification()`: Pengelolaan status notifikasi
- ✅ `routes/web.php`: Rute aksi notifikasi (`/dashboard/notifikasi/{id}/baca`, `/dashboard/notifikasi/baca-semua`, `/dashboard/notifikasi/{id}/hapus`)
- ✅ `resources/views/dashboard/certificates.php`:
  - 3 Kartu dampak lingkungan: CO2, O2, Luas Tutupan Hijau
  - Kartu peringkat leaderboard dengan tautan ke halaman leaderboard publik
  - 6 Kartu lencana pencapaian interaktif dengan visual emas terbuka vs gembok terkunci
  - Progress bar dinamis menuju lencana berikutnya
  - Galeri kartu sertifikat resmi (Donasi Pohon & Relawan) siap unduh / cetak PDF dan verifikasi publik
- ✅ `resources/views/dashboard/referral.php`:
  - Banner hero hijau alami dengan nilai bonus +25 poin
  - Kode referral unik & link pendaftaran langsung dengan tombol salin interaktif
  - Tombol bagikan media sosial (WhatsApp, Twitter/X, Telegram, Facebook)
  - 3 Kartu statistik referral (Teman, Pohon, Poin)
  - Infografis 3 langkah cara kerja program
  - Tabel riwayat sahabat yang bergabung dengan nama tersensor untuk privasi
- ✅ `resources/views/dashboard/notifications.php`:
  - Ikon visual spesifik per tipe notifikasi (pembayaran, donasi, event, pesanan, lencana, referral)
  - Indikator badge BARU dan highlight latar kartu
  - Waktu relatif ramah pengguna (`time_ago`)
  - Aksi tandai dibaca per notifikasi & tandai semua dibaca
  - Informasi saluran preferensi pengingat email & WhatsApp

---

## Modul 10: Profil & Bantuan
**Status**: ✅ Selesai (2026-11-05)

### Detail Pengerjaan:
- ✅ Manajemen Profil Pengguna (`DashboardController@profile`, `DashboardController@updateProfile`, `DashboardController@updatePassword`, `DashboardController@uploadAvatar`):
  - Edit identitas (nama, bio, nomor telepon/WhatsApp, preferensi notifikasi)
  - Penggantian password dengan validasi password lama
  - Unggah foto profil avatar ke storage lokal
- ✅ Buku Alamat Pengiriman Pengguna (`DashboardController@addresses`, `DashboardController@storeAddress`, `DashboardController@updateAddress`, `DashboardController@deleteAddress`, `DashboardController@setPrimaryAddress`):
  - CRUD buku alamat pengiriman merchandise
  - Pengaturan alamat utama (primary)
- ✅ Tiket Layanan & Pusat Bantuan (`DashboardController@support`, `DashboardController@sendTicket`, `DashboardController@showTicket`, `DashboardController@replyTicket`, `DashboardController@closeTicket`):
  - Pengajuan tiket bantuan (pertanyaan/kendala donasi, event, pesanan)
  - Percakapan thread interaktif antara peserta dan admin support
  - Status tiket: open, replied, closed

---

## Modul 11: Admin Panel & Operasional
**Status**: ✅ Selesai (2026-11-06)

### Detail Pengerjaan:
- ✅ Layout Admin Responsif (`resources/views/layouts/admin.php`):
  - Navigasi sidebar dengan toggle drawer mobile, indikator badge antrean belum diverifikasi
  - Jam digital realtime WIB, info akun admin aktif
- ✅ Dashboard Ringkasan & Statistik (`AdminDashboardController@index`):
  - Metrik utama: Total donasi terkumpul, total pohon tertanam, total peserta event, antrean pembayaran pending
  - Grafik tren donasi 6 bulan terakhir dengan Chart.js
- ✅ Verifikasi Pembayaran Manual 100% (`AdminPaymentController`):
  - Antrean bukti transfer & QRIS masuk
  - Pratinjau bukti bayar pop-up / modal, deteksi duplikasi hash bukti (SHA-256)
  - Aksi setujui: otomatis aktivasi donasi / tiket kegiatan / pesanan merchandise, tambah poin & lencana
  - Aksi tolak: sertakan alasan penolakan dan kirim notifikasi ke pengguna
- ✅ Manajemen Rekening Bank Resmi (`AdminBankController`):
  - CRUD rekening bank transfer, e-wallet, dan QRIS statis TanamPohon.id
- ✅ Manajemen Kegiatan Relawan & Peserta (`AdminEventController` & `AdminParticipantController`):
  - CRUD event penanaman, target kuota, titik kumpul, dan status pelaksanaan
  - Daftar peserta terdaftar, filter per event
  - Fitur Scanner Check-in QR HTML5 / WebRTC camera scanner di lokasi penanaman (`AdminParticipantController@qrScanner`)
- ✅ Manajemen Pohon & Lokasi (`AdminTreeController`):
  - Data pohon individu, update status (pending, ditanam, tumbuh, dipantau), koordinat GPS
  - CRUD Spesies Pohon (`tree_species`): Tambah, edit serapan CO2, produksi O2, harga bibit, dan foto pohon
- ✅ Manajemen Produk Kaos & Stok (`AdminProductController` & `AdminOrderController`):
  - CRUD produk merchandise, varian ukuran/warna, dan pembaruan stok
  - Pengelolaan pesanan masuk, input nomor resi pengiriman kurir, update status selesai
- ✅ Modul Berita & Kabar Lapangan (`AdminNewsController` & `NewsController`):
  - CRUD postingan kabar penanaman, artikel edukasi, status terbit publik
- ✅ Laporan Keuangan & Dampak Lingkungan (`AdminReportController`):
  - Laporan donasi masuk, serapan CO2, O2, donor teraktif, ekspor CSV/Excel
- ✅ Pengaturan Sistem (`AdminSettingController`):
  - Konfigurasi profil organisasi, kontak bantuan, dan limit sistem
- 🛠️ Perbaikan Bug & Refactoring:
  - Fix overlay 5000px box-shadow pada scanner QR di browser (`resources/views/admin/qr_checkin.php`)
  - Fix query SQL kolom `planted_at` dan JOIN table di `AdminTreeController.php`
  - Fix kompatibilitas method signature `UserAddress::updateAddress()` dan `UserAddress::deleteAddress()`
  - Fix penanganan payable `participant` dan `order` pada tampilan tagihan pembayaran (`resources/views/public/payment-instruction.php`)
  - Dukungan method HTTP `HEAD` pada `core/Router.php` untuk health check

---

## Modul 12: Halaman Transparansi, Peta Interaktif & Verifikasi Publik
**Status**: ✅ Selesai (2026-11-07)

### Detail Pengerjaan:
- ✅ Controller Transparansi & Validasi Publik (`app/Controllers/TransparencyController.php`):
  - `index()`: Rekapitulasi dana donasi terkumpul vs target tahunan, persentase capaian finansial & bibit, total pohon tertanam, metrik serapan karbon riil ($CO_2$ kg/ton) & produksi oksigen ($O_2$ kg/ton), progres per wilayah konservasi, feed donasi masuk terbaru, dan kabar lapangan.
  - `map()`: Peta interaktif Leaflet.js dengan filter wilayah konservasi, jenis spesies pohon, dan status pertumbuhan.
  - `apiTrees()`: Endpoint REST JSON (`/api/peta-pohon`) untuk pembaruan marker pohon di peta secara asynchronous (AJAX) tanpa reload halaman.
  - `leaderboard()`: Papan peringkat donatur (Top Donors) dan relawan teladan (Top Volunteers) dengan opsi periode Semua Waktu, Tahun Ini, dan Bulan Ini. Proteksi privasi donatur anonim otomatis disamarkan sebagai "Sahabat Lestari".
  - `verifyCertificate()` & `checkCertificate()`: Mesin verifikasi publik keaslian sertifikat digital resmi TanamPohon.id untuk donatur pohon maupun relawan aksi lapangan.
- ✅ Tampilan Transparansi Publik (`resources/views/public/transparency.php`):
  - 4 Kartu metrik utama (Dana Terkumpul, Pohon Tertanam, Serapan Karbon, Produksi Oksigen) dengan progress bar.
  - Diagram visual persentase alokasi dana donasi: 70% bibit & penanaman, 15% pemeliharaan 3 tahun, 10% pemberdayaan petani lokal, 5% audit & operasional sistem.
  - Tabel dan kartu progres per kawasan konservasi dengan koordinat GPS.
  - Daftar donasi terkini dan postingan kabar lapangan terbaru.
- ✅ Peta Sebaran Pohon Interaktif Leaflet.js (`resources/views/public/map.php`):
  - OpenStreetMap tiles tanpa dependency API key berbayar.
  - Custom SVG pin marker dengan pewarnaan dinamis berdasarkan status pohon (Baru Ditanam, Tumbuh Sehat, Dipantau).
  - Pop-up interaktif: kode pohon unik, foto pohon, nama spesies, nama lokasi & kota, estimasi serapan $CO_2$, tanggal tanam, dan catatan tim pemantau.
  - Kontrol filter dropdown (Kawasan, Spesies, Status) dengan tombol reset dan counter titik pohon aktif.
  - Tombol aksi cepat untuk memfokuskan peta ke kawasan konservasi tertentu.
- ✅ Papan Peringkat / Hall of Fame (`resources/views/public/leaderboard.php`):
  - Desain podium 3 besar (Emas #1, Perak #2, Perunggu #3) dengan badge kehormatan dan mahkota juara.
  - Filter tab periode: Semua Waktu, Tahun Ini (2026), Bulan Ini.
  - Tab donatur (peringkat pohon & nominal) dan tab relawan (peringkat kehadiran event penanaman).
  - Info jaminan privasi donatur anonim.
- ✅ Halaman Verifikasi Keaslian Sertifikat (`resources/views/public/verify_certificate.php`):
  - Formulir input pencarian nomor seri sertifikat (`CERT-...`).
  - Tampilan sertifikat digital dengan stempel resmi, watermark pohon, QR verifikasi, detail penerima, spesies, dan penandatangan.
  - Penanganan sertifikat tidak valid / tidak ditemukan dengan panduan pencarian yang jelas.
  - Tombol cetak / simpan halaman bukti verifikasi.

---

## Modul 13: Cron Job, Otomasi Terjadwal & Finalisasi Sistem
**Status**: ✅ Selesai (2026-11-07)

### Detail Pengerjaan:
- ✅ Layanan Otomasi Terjadwal (`app/Services/CronService.php`):
  - `cancelExpiredPayments()`: Membatalkan otomatis transaksi dengan status `menunggu_pembayaran` yang telah melewati batas 24 jam (`expires_at < NOW()`).
  - Pemulihan Otomatis Reservasi: Mengembalikan stok varian kaos (`product_variants.stock += quantity`) dan kuota pendaftaran relawan (`planting_events.registered_count -= 1`), serta membatalkan entitas terkait (`orders`, `participants`, `donations`).
  - Notifikasi Otomatis: Mengirim in-app notification pembatalan transaksi kedaluwarsa kepada pengguna terkait.
  - `sendPaymentReminders()`: Mengirim pengingat proaktif bagi pembayaran yang mendekati batas waktu (<= 3 jam sebelum kedaluwarsa) dengan pencegahan spam pengingat berulang.
  - `sendEventReminders()`: Mengirimkan pengingat perlengkapan lapangan & QR e-ticket untuk event penanaman H-3 dan H-1 bagi relawan dengan status terkonfirmasi.
  - `cleanupOldData()`: Membersihkan data keranjang belanja tamu yang usang (> 7 hari) dan pemeliharaan token kedaluwarsa.
  - `recordRunTimestamp()` & Audit Log: Pencatatan waktu eksekusi terakhir ke tabel `settings` dan riwayat log terperinci di `activity_logs`.
- ✅ Task Runner CLI (`cron/run.php`):
  - Script CLI mandiri dengan argumen modular (`--all`, `--expire`, `--reminder`, `--events`, `--cleanup`).
  - Output log terminal terformat rapi dengan waktu server WIB, statistik data yang diproses, dan durasi eksekusi.
  - Siap diintegrasikan ke Linux Crontab maupun cPanel Cron Jobs (`*/15 * * * * php /path/to/cron/run.php`).
- ✅ Webhook Web Cron (`app/Controllers/CronController.php`):
  - Endpoint HTTP aman (`/cron/run?token=...`) dengan validasi `hash_equals` berbasis `CRON_SECRET_TOKEN` di `.env`.
  - Respon JSON dengan rincian tindakan dan waktu eksekusi, cocok untuk cron-job.org atau UptimeRobot.
- ✅ Integrasi Panel Admin (`app/Controllers/AdminSettingController.php` & `resources/views/admin/settings.php`):
  - Widget status cron aktif dengan waktu relatif (`time_ago`).
  - Perintah crontab CLI siap salin satu klik.
  - URL Webhook cron siap salin satu klik.
  - Tombol aksi cepat: "Jalankan Cron Job Sekarang (Manual Trigger)" langsung dari antarmuka Admin Panel dengan flash message hasil eksekusi.
  - Riwayat 5 log eksekusi cron terbaru.
- ✅ Dokumentasi & Konfigurasi Lengkap:
  - Pembaruan `.env` dan `.env.example` dengan variabel `CRON_SECRET_TOKEN`.
  - Pembaruan `README.md` dengan panduan instalasi lokal, deployment cPanel shared hosting, panduan crontab, dan tabel akun uji coba default.

---

## 🏆 Status Proyek: SEMUA 13 MODUL SELESAI 100%
Seluruh spesifikasi sistem **TanamPohon.id** (Modul 1 s.d. Modul 13) telah selesai dibangun, terintegrasi, dan diuji secara komprehensif.

---

## 🎨 Pembaruan Tema Desain Visual: "Plant For Fun & Kerenceng Agroforestry"
**Status**: ✅ Selesai (2026-11-07)

### Detail Pengerjaan:
- ✅ **Palet & Pola Topografi (Topographic Map Contour)**:
  - Mengadopsi palet warna poster: Deep Pine Forest Green (`#12382c`), Topo Sage (`#dbe7dc`), Warm Paper Cream (`#f4f7f4`), dan aksen Chartreuse Trail Lime (`#bfd254`).
  - Pola SVG garis kontur topografi otentik (`.topo-pattern` & `.topo-pattern-light`) untuk latar belakang hero dan kartu kegiatan.
- ✅ **Tipografi Poster**:
  - Integrasi font Google Fonts: **`Bitter`** (bold slab-serif poster untuk judul besar `PLANT FOR FUN`, `PLANT TREES PRESERVE TOMORROW`, `LET'S GET PLANTING!!`), **`Space Mono`** (koordinat GPS, tanggal & penanda teknis), serta **`Plus Jakarta Sans`** untuk keterbacaan teks utama.
- ✅ **Ekstraksi & Vektorisasi Seluruh Logo Mitra Resmi**:
  - Dibuat partial komponen SVG scalable terstruktur (`resources/views/partials/partner-logos.php`) yang mendukung tema terang (*light*) dan gelap (*dark*):
    - **Presented By**:
      1. **an du**: Monogram minimalis 4 lingkaran/kapsul geometris studio.
      2. **Karsa Kerentjeng**: Lambang kontur puncak Gunung Kerenceng dengan cabang kopi dan biji ceri.
      3. **Ethera**: Tipografi elegan high-contrast serif.
    - **Media Partner**:
      1. **CLK.PNY (Tjitjalengka Poenja)**: Siluet puncak gunung segitiga modern dengan matahari terbit.
      2. **Cicalengka GO**: Badge dinamis komunitas outdoor & pesepeda lereng gunung.
- ✅ **Integrasi Lintas Halaman Web**:
  - **Beranda (`home.php`)**: Hero showcase berbingkai (*framed box motif*) dengan foto poster resolusi tinggi, metadata bar jadwal/titik kumpul, tombol aksi langsung, QR register, dan jajaran logo sponsor.
  - **Footer Global (`footer.php`)**: Banner resmi kolaborasi aksi hijau yang menampilkan seluruh logo mitra di setiap halaman publik.
  - **Detail Kegiatan (`events/detail.php`)**: Tampilan poster acara dan logo mitra untuk event Kerenceng.
  - **Katalog Kegiatan (`events/index.php`)**: Penanda khusus (*badge*) untuk kegiatan unggulan dan footer logo kolaborator.
  - **Tentang Kami (`about.php`)**: Penyertaan filosofi *"Plant Trees Preserve Tomorrow"* dan showcase ekosistem mitra pendukung.



