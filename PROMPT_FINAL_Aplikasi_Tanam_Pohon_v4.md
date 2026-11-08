Kamu adalah senior full-stack PHP developer. Buatkan aplikasi web lengkap bernama "[NAMA PROGRAM, mis. TanamPohon.id]", platform untuk menghimpun peserta (relawan) dan donasi dalam program penanaman pohon, sekaligus menjual kaos (t-shirt) sebagai merchandise program. Pembayaran dilakukan MANUAL: transfer ke rekening penyelenggara lalu upload bukti transfer untuk diverifikasi admin.

# 1. TUJUAN
- Mengajak masyarakat berpartisipasi sebagai donatur, relawan penanam, atau keduanya.
- Menghimpun data peserta dan dana secara transparan dan akuntabel.
- Menjual kaos sebagai merchandise, dengan sebagian keuntungan masuk ke dana penanaman.
- Memberi pengalaman personal bagi peserta lewat dashboard (pantau pohon, tiket, sertifikat, pencapaian).

# 2. TECH STACK
- PHP 8.1+ dengan arsitektur MVC (pilih salah satu: PHP native terstruktur dengan router sendiri, atau Laravel 11; jelaskan alasan pilihanmu di awal).
- MySQL/MariaDB dengan PDO prepared statements (atau Eloquent jika Laravel).
- Frontend: HTML5, Bootstrap 5 atau Tailwind CSS, JavaScript vanilla/Alpine.js, mobile-first dan responsif.
- Pembayaran MANUAL: transfer ke rekening/e-wallet/QRIS statis milik penyelenggara, lalu peserta mengunggah bukti transfer dan admin memverifikasi. TIDAK menggunakan payment gateway, webhook, atau API pembayaran apa pun.
- Ongkir: RajaOngkir atau Biteship (pilih kurir dan layanan), atau opsi ongkir manual diatur admin.
- Email: PHPMailer (SMTP). Notifikasi WhatsApp: siapkan interface/adapter agar mudah dihubungkan ke provider (mis. Fonnte/WA Gateway).
- Library pendukung: Dompdf/TCPDF (PDF), endroid/qr-code (QR), Chart.js (grafik), Leaflet.js (peta), PhpSpreadsheet (ekspor Excel).
- Siap dijalankan di shared hosting/cPanel maupun XAMPP.

# 3. FITUR PUBLIK (PENGUNJUNG)
1. **Landing page**: hero banner, tujuan program, counter dampak (total pohon, total donasi, jumlah peserta), cara kerja, testimoni, FAQ, CTA "Donasi Sekarang" dan "Daftar Jadi Relawan".
2. **Donasi pohon**
   - Paket (1, 5, 10 pohon, nominal bebas) dengan harga per pohon yang diatur admin.
   - Pilihan jenis pohon (mangga, trembesi, mangrove, dll.) lengkap dengan deskripsi dan foto.
   - Opsi donasi anonim, pesan/doa, dan donasi atas nama orang lain (hadiah/in memoriam).
   - Checkout tamu (tanpa login) atau login, lalu diarahkan ke halaman instruksi pembayaran manual dan form konfirmasi (lihat Bagian 3B). Untuk tamu, sediakan halaman "Cek Status Pembayaran" memakai nomor invoice + email agar bisa upload bukti tanpa login.
   - Sertifikat donasi digital (PDF) terbit otomatis setelah pembayaran diverifikasi, dengan nomor unik dan QR verifikasi.
3. **Pendaftaran peserta/relawan**
   - Form: nama, email, WhatsApp, kota, ukuran kaos, status (individu/komunitas/perusahaan), pilihan kegiatan/lokasi/jadwal.
   - Kuota per kegiatan, status pendaftaran (menunggu/terkonfirmasi/hadir/batal).
   - E-ticket dengan QR code untuk check-in. Jika ada biaya/bundle kaos, tiket aktif setelah pembayaran diverifikasi; kegiatan gratis langsung terkonfirmasi.
4. **Toko kaos**
   - Katalog dengan foto, deskripsi, harga, varian ukuran (S-XXL) dan warna, stok per varian.
   - Keranjang, checkout, alamat pengiriman, ongkir via API atau opsi ambil di lokasi acara.
   - Bundle: "Donasi + Kaos" dan "Daftar Relawan + Kaos".
   - Pesanan baru berstatus "Menunggu Pembayaran" sampai bukti diverifikasi admin.
   - Pelacakan pesanan dengan nomor resi.
5. **Transparansi**
   - Halaman progres: target vs terkumpul (progress bar), donatur terbaru, leaderboard donatur/komunitas.
   - Peta lokasi penanaman (Leaflet.js): titik koordinat, foto dokumentasi, jumlah pohon per titik.
   - Laporan kegiatan (berita) dengan galeri foto.
   - Halaman verifikasi sertifikat publik berdasarkan nomor sertifikat.
6. Autentikasi: daftar, login, lupa/reset password, verifikasi email, login Google (opsional), dukungan kode referral saat daftar.
7. Halaman statis: Tentang Kami, Kontak, Syarat & Ketentuan, Kebijakan Privasi.

# 3B. ALUR PEMBAYARAN MANUAL (berlaku untuk donasi, pesanan kaos, dan pendaftaran berbayar)
1. Setelah checkout, sistem membuat transaksi berstatus **"Menunggu Pembayaran"** dengan nomor invoice unik (mis. INV-20250101-0001).
2. Halaman instruksi pembayaran menampilkan:
   - Total yang harus dibayar ditambah **kode unik 3 digit** (mis. Rp 100.000 menjadi Rp 100.123) agar mudah dicocokkan dengan mutasi rekening. Kode unik bisa diaktifkan/dinonaktifkan admin.
   - Daftar rekening tujuan (bank, nomor, atas nama, logo) dan gambar QRIS statis, dengan tombol "Salin nomor rekening" dan "Salin nominal".
   - Batas waktu pembayaran (default 24 jam, bisa diatur admin) dengan hitung mundur.
3. Form **Konfirmasi Pembayaran** (bisa juga dari dashboard):
   - Upload bukti transfer (JPG/PNG/PDF, maks 2 MB, ada preview sebelum kirim).
   - Pilih rekening tujuan, nama bank/e-wallet pengirim, nama pemilik rekening pengirim, tanggal dan jam transfer, nominal ditransfer, catatan (opsional).
4. Setelah dikirim, status menjadi **"Menunggu Verifikasi"**; peserta mendapat email/notifikasi bahwa bukti sudah diterima. Peserta boleh mengunggah ulang bukti selama belum diverifikasi.
5. Admin memeriksa bukti lalu memilih **Setujui** atau **Tolak** (wajib isi alasan penolakan, mis. nominal tidak sesuai/foto buram).
   - Setujui: status menjadi "Berhasil", lalu otomatis menerbitkan sertifikat donasi, tiket QR peserta, atau memproses pesanan kaos, serta mengirim notifikasi dan email.
   - Tolak: status menjadi "Ditolak", peserta diminta upload ulang bukti dan menerima notifikasi beserta alasannya.
6. Jika melewati batas waktu tanpa bukti, status otomatis menjadi **"Kedaluwarsa"** (cron job), dan stok kaos serta kuota kegiatan yang tertahan dikembalikan.
7. Status pembayaran: menunggu_pembayaran, menunggu_verifikasi, berhasil, ditolak, kedaluwarsa, dibatalkan.
8. Stok kaos dan kuota kegiatan ditahan (reserve) selama menunggu pembayaran, dan dilepas saat kedaluwarsa atau batal.

# 4. DASHBOARD PESERTA (AREA MEMBER, SETELAH LOGIN)
Layout sidebar (desktop) dan bottom navigation (mobile). Hanya bisa diakses setelah login dan hanya menampilkan data milik user tersebut.

1. **Ringkasan**
   - Sapaan personal dan avatar.
   - Kartu statistik: total pohon saya, total donasi (Rp), jumlah kegiatan diikuti, jumlah pesanan kaos, poin/level kontribusi.
   - Grafik kontribusi per bulan (Chart.js).
   - Kegiatan terdekat yang saya daftar dengan hitung mundur hari-H.
   - Notifikasi terbaru dan tombol cepat: "Donasi Lagi", "Daftar Kegiatan", "Beli Kaos".
2. **Donasi Saya**
   - Riwayat donasi: tanggal, nominal, jumlah pohon, jenis pohon, status pembayaran (badge berwarna).
   - Detail donasi; untuk yang belum lunas tampil tombol "Upload Bukti Transfer / Konfirmasi Pembayaran", sisa waktu pembayaran, dan instruksi rekening tujuan.
   - Riwayat bukti yang pernah diunggah, alasan penolakan jika ditolak, dan tombol "Upload Ulang".
   - Unduh invoice, bukti pembayaran, dan sertifikat PDF (hanya setelah status "Berhasil").
   - **Pohon Saya**: tiap pohon punya kode unik, lokasi penanaman (peta Leaflet), foto dokumentasi, dan status pemantauan (ditanam, tumbuh, dipantau) yang diinput admin.
   - Tombol bagikan dampak ke WhatsApp/Instagram/Facebook.
3. **Kegiatan Saya**
   - Daftar kegiatan: akan datang, berlangsung, selesai.
   - Detail: tanggal, jam kumpul, lokasi dan tautan Google Maps, jenis pohon, barang yang dibawa, kontak koordinator.
   - E-ticket QR (unduh PDF/gambar), status pendaftaran, pembatalan sebelum batas waktu.
   - Daftar kegiatan baru yang masih berkuota dengan pendaftaran cepat (data profil terisi otomatis).
   - Pasca kegiatan: sertifikat partisipasi, galeri foto, form feedback/rating.
4. **Pesanan Kaos**
   - Riwayat dan detail pesanan (ukuran, warna, jumlah, total, status).
   - Tombol "Upload Bukti Transfer" (dengan status, sisa waktu, alasan penolakan, upload ulang), lacak pengiriman (resi dan kurir), "Konfirmasi Diterima", "Batalkan" (hanya jika belum dibayar), "Beli Lagi", unduh invoice PDF (setelah lunas).
   - Status kaos bundle relawan (diambil di lokasi atau dikirim).
5. **Sertifikat & Pencapaian**
   - Semua sertifikat dalam satu halaman, unduh dan verifikasi publik.
   - Lencana/level: Penanam Pemula (1 pohon), Sahabat Hutan (10), Penjaga Bumi (50), Pahlawan Hijau (100+), ambang bisa diatur admin.
   - Estimasi dampak lingkungan (CO2 terserap, oksigen) dengan rumus yang bisa diatur admin.
   - Posisi di leaderboard (bisa disembunyikan).
6. **Ajak Teman (Referral)**: kode dan tautan unik, tombol bagikan, jumlah teman bergabung, total pohon dari referral, bonus/poin.
7. **Notifikasi**: notifikasi dalam aplikasi (ikon lonceng dan badge) untuk bukti diterima, pembayaran disetujui, pembayaran ditolak (beserta alasan), pengingat batas pembayaran (3 jam sebelum kedaluwarsa), pendaftaran terkonfirmasi, pengingat H-3 dan H-1 kegiatan (via cron), pesanan dikirim, pohon baru ditanam. Preferensi notifikasi email/WhatsApp.
8. **Profil & Pengaturan**: edit data diri, foto profil, WhatsApp, kota, ukuran kaos default, banyak alamat pengiriman (satu utama), ganti password, verifikasi email, opsi tampil anonim, nonaktifkan/hapus akun.
9. **Bantuan**: FAQ dan tiket bantuan/pengaduan ke admin dengan status balasan.

Ketentuan teknis dashboard:
- Middleware auth dan role "peserta"; cek kepemilikan data pada setiap akses (cegah IDOR, mis. user tidak boleh membuka `/donasi/123` milik orang lain).
- AJAX/fetch untuk filter, pencarian, dan pagination tanpa reload.
- Skeleton loading dan empty state ramah (mis. "Belum ada donasi, yuk tanam pohon pertamamu!").
- Dark mode (opsional).

# 5. FITUR ADMIN PANEL
1. Dashboard: statistik donasi, peserta, penjualan kaos, grafik harian/bulanan (Chart.js), dan jumlah antrian verifikasi pembayaran.
2. **Verifikasi Pembayaran** (menu utama): antrian bukti transfer berstatus "Menunggu Verifikasi" (diurutkan dari yang terlama) dengan badge jumlah antrian di sidebar. Tampilkan preview bukti (zoom, buka di tab baru), data transaksi, nominal tagihan vs nominal yang diisi peserta, tombol Setujui/Tolak (dengan alasan), dan catatan internal. Dukung persetujuan massal.
   - Peringatan otomatis jika nominal yang diisi tidak sama dengan tagihan, atau jika file bukti yang sama (hash) pernah diunggah di transaksi lain (deteksi bukti ganda).
3. **Manajemen Rekening Pembayaran**: CRUD rekening bank/e-wallet, upload gambar QRIS, urutan tampil, aktif/nonaktif.
4. Manajemen donasi: daftar, filter status pembayaran, ekspor Excel/CSV, dan fitur "tandai lunas manual" (khusus donasi tunai/offline, wajib catatan dan tercatat di log aktivitas).
5. Manajemen peserta: daftar, konfirmasi, check-in via scan QR (kamera HP), kirim email/WhatsApp massal, ekspor data.
6. Manajemen kegiatan/lokasi tanam: CRUD, kuota, tanggal, koordinat, status.
7. Manajemen jenis pohon dan harga paket.
8. **Pemantauan pohon**: input status dan foto per kode pohon, upload massal, pencatatan koordinat penanaman.
9. Manajemen produk kaos: CRUD, varian ukuran/warna, stok, upload banyak foto.
10. Manajemen pesanan: filter status pembayaran, ubah status, input resi, cetak invoice dan label pengiriman, ekspor.
11. Manajemen konten: banner, testimoni, FAQ, berita, galeri.
12. Manajemen lencana/level, rumus dampak lingkungan, dan notifikasi massal.
13. Tiket bantuan: lihat dan balas pesan peserta.
14. Laporan keuangan: pemasukan donasi, penjualan kaos, biaya, alokasi dana ke program tanam, serta rekonsiliasi pembayaran per rekening dan per tanggal.
15. Pengaturan: nama situs, logo, batas waktu pembayaran, aktif/nonaktif dan rentang kode unik, template email (bukti diterima, disetujui, ditolak), teks instruksi pembayaran, API key ongkir, batas waktu pembatalan.
16. Role-based access: Super Admin, Admin Keuangan, Admin Event, Admin Konten. Hanya Super Admin dan Admin Keuangan yang boleh memverifikasi pembayaran.
17. Log aktivitas admin (termasuk siapa yang menyetujui/menolak pembayaran dan kapan).

# 6. DATABASE
Rancang ERD lengkap dengan relasi dan foreign key. Minimal tabel:
users, roles, user_addresses, donations, donation_items, tree_species, trees (kode unik per pohon), tree_trackings, planting_events, planting_locations, participants, tickets, event_feedbacks, products, product_variants, product_images, carts, orders, order_items, shipments, payments, bank_accounts, payment_proofs, certificates, badges, user_badges, referrals, notifications, support_tickets, support_replies, news, galleries, testimonials, settings, activity_logs.

Detail pembayaran manual:
- bank_accounts: bank, no_rekening, atas_nama, qris_image, urutan, is_active.
- payment_proofs: id, payable_type, payable_id, bank_account_id, sender_bank, sender_name, amount_transferred, transfer_date, proof_file, file_hash, note, status, rejected_reason, verified_by, verified_at, created_at.
- payments/transaksi: invoice_no, total_amount, unique_code, grand_total, status, expires_at, paid_at.
- Relasi polimorfik (payable_type dan payable_id) agar satu mekanisme pembayaran dipakai oleh donations, orders, dan pendaftaran berbayar.
- TIDAK ada kolom/tabel untuk payment gateway (snap token, transaction_id gateway, dsb).

Kolom tambahan di users: avatar, referral_code, points, is_anonymous_default, email_verified_at.
Sertakan `database.sql` berisi struktur, index, dan data contoh (seeder, termasuk contoh rekening).

# 7. KEAMANAN
- password_hash/bcrypt, token CSRF, escaping XSS, PDO prepared statements.
- Validasi input di server dan klien, rate limiting login, session aman (httponly, secure, samesite), regenerasi session ID.
- Upload bukti transfer: whitelist MIME (jpg, png, pdf) dengan pengecekan isi file asli (bukan hanya ekstensi), maks 2 MB, rename acak, simpan di folder privat di luar public_html dan sajikan lewat script PHP yang mengecek kepemilikan/role admin (bukti tidak boleh bisa diakses lewat URL langsung).
- Hapus metadata EXIF, batasi jumlah upload per transaksi, rate limiting pada form konfirmasi.
- Proses verifikasi harus idempotent: transaksi yang sudah "Berhasil" tidak boleh diproses dua kali (gunakan transaksi database dan row locking).
- Upload lain (foto produk, avatar, dokumentasi) juga divalidasi MIME dan ukuran serta di-rename.
- Konfigurasi sensitif di `.env`.
- Proteksi IDOR pada seluruh route dashboard.

# 8. UI/UX
- Tema hijau natural, bersih, modern, bernuansa lingkungan.
- Bahasa Indonesia, format Rupiah, zona waktu WIB (Asia/Jakarta).
- Responsif, loading cepat (lazy load gambar), SEO dasar (meta tag, Open Graph, sitemap.xml).
- Tombol share ke WhatsApp/Instagram/Facebook setelah donasi atau pendaftaran.

# 9. OUTPUT YANG DIMINTA
1. Struktur folder proyek lengkap.
2. Skema database (`database.sql`).
3. Seluruh kode sumber, dikerjakan bertahap dengan urutan modul:
   (1) struktur folder dan database
   (2) autentikasi dan role
   (3) donasi dan pembayaran manual (instruksi rekening, kode unik, upload bukti, status, kedaluwarsa)
   (4) pendaftaran peserta dan e-ticket QR
   (5) toko kaos dan ongkir
   (6) layout dashboard peserta dan ringkasan
   (7) donasi saya, pohon saya, dan sertifikat PDF
   (8) kegiatan saya dan pesanan kaos
   (9) pencapaian, referral, dan notifikasi
   (10) profil dan bantuan
   (11) admin panel (termasuk verifikasi pembayaran, manajemen rekening, pemantauan pohon, dan check-in QR)
   (12) halaman transparansi (peta, leaderboard, verifikasi sertifikat)
   (13) cron job (kedaluwarsa otomatis transaksi, pelepasan stok/kuota, pengingat batas pembayaran, pengingat kegiatan) dan finalisasi
4. File `.env.example` dan `README.md` berisi panduan instalasi, pengaturan rekening pembayaran, pengaturan cron, dan deployment ke cPanel (tanpa konfigurasi payment gateway).
5. Komentar kode dalam Bahasa Indonesia yang jelas.

# 10. CARA KERJA
- Kerjakan satu modul per respons, tulis kode lengkap (bukan potongan) dan sebutkan nama file serta lokasinya.
- Setelah tiap modul, beri ringkasan singkat dan cara mengujinya, lalu tanyakan apakah lanjut ke modul berikutnya.
- Mulai sekarang dari modul 1: jelaskan pilihan framework, tampilkan struktur folder, dan tulis `database.sql`.
