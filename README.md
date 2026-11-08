# 🌳 TanamPohon.id — Platform Donasi Pohon & Relawan Hijau

Platform web berbasis **PHP Native MVC (PHP 8.1+)** untuk menghimpun peserta (relawan) dan donasi penanaman pohon, sekaligus penjualan kaos merchandise dengan sistem **Pembayaran MANUAL** (Transfer Bank/E-Wallet/QRIS statis + Upload Bukti Transfer + Verifikasi Admin).

---

## 🚀 Fitur Utama

1. **Alur Pembayaran Manual 100%**:
   - Nomor invoice unik + kode unik 3 digit acak untuk mempermudah mutasi bank
   - Salin nomor rekening & nominal satu klik
   - Upload bukti transfer (JPG/PNG/PDF maks 2 MB) tersimpan di storage privat
   - Deteksi otomatis bukti transfer ganda (hash MD5/SHA256)
   - Verifikasi admin (Setujui / Tolak dengan alasan wajib)
   - Batas waktu pembayaran hitung mundur (24 jam) & cron auto-expire
2. **Donasi Pohon**:
   - Pilihan paket pohon & jenis pohon (Mangga, Trembesi, Mangrove, Mahoni, Jati)
   - Donasi anonim, pesan/doa, atau atas nama orang lain (hadiah/in memoriam)
   - Unduh sertifikat PDF otomatis dengan QR code verifikasi
3. **Pendaftaran Relawan Event**:
   - Pendaftaran kegiatan penanaman di berbagai lokasi
   - E-Ticket dengan QR Code untuk check-in via scanner kamera HP admin
4. **Toko Kaos Merchandise**:
   - Katalog varian (ukuran S-XXL, warna), stok tertahan saat checkout
   - Hitung ongkir kurir atau ambil di lokasi acara
5. **Dashboard Peserta**:
   - Pantau pohon yang ditanam (foto, peta koordinat Leaflet.js, status pemantauan)
   - Riwayat donasi, pesanan merchandise, tiket kegiatan, lencana level kontribusi
6. **Admin Panel Lengkap**:
   - Verifikasi bukti transfer antrean
   - Manajemen rekening bank & gambar QRIS
   - Pemantauan pohon & check-in peserta via QR scanner

---

## ⚙️ Persyaratan Sistem

- **PHP**: versi 8.1 atau lebih tinggi
- **Ekstensi PHP**: `pdo`, `pdo_mysql`, `mbstring`, `gd`, `curl`, `json`
- **Database**: MySQL 5.7+ atau MariaDB 10.3+
- **Web Server**: Apache dengan `mod_rewrite` aktif, Nginx, atau PHP Built-in Server

---

## 📦 Panduan Instalasi Lokal (XAMPP / PHP CLI)

### 1. Clone atau Ekstrak Proyek
Pastikan folder berada di document root atau direktori kerja Anda:
```bash
cd /path/to/cocok_tanam
```

### 2. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan pengaturan database di `.env`:
```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tanampohon_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Impor Database
Buat database baru bernama `tanampohon_db` di MySQL/phpMyAdmin, lalu impor skema dan seeder:
```bash
mysql -u root -p tanampohon_db < database.sql
```

### 4. Akun Default Seeder
- **Super Admin**:
  - Email: `admin@tanampohon.id`
  - Password: `Admin123!`

### 5. Menjalankan Aplikasi
Anda dapat menggunakan web server Apache (XAMPP) atau server bawaan PHP:
```bash
php -S localhost:8000 -t public
```
Buka browser di: `http://localhost:8000`

---

## 🌐 Panduan Deployment di cPanel / Shared Hosting

1. **Upload File**:
   - Ekstrak seluruh isi folder proyek ke direktori di luar `public_html` (misal: `/home/username/tanampohon_app/`).
   - Pindahkan seluruh isi folder `public/` ke dalam folder `public_html/`.
2. **Sesuaikan Path di `public_html/index.php`**:
   ```php
   define('BASE_PATH', dirname(__DIR__) . '/tanampohon_app');
   ```
3. **Database**:
   - Buat database dan user database di cPanel MySQL Databases.
   - Impor `database.sql` lewat phpMyAdmin cPanel.
   - Sesuaikan konfigurasi di file `.env`.
4. **Permissions Folder**:
   - Pastikan folder `storage/` dan seluruh subfoldernya memiliki hak akses tulis (`chmod 775` atau `755`).
5. **Pengaturan Otomasi Cron Job (Pilih Salah Satu)**:
   - **Metode A: Crontab CLI di cPanel (Rekomendasi)**:
     Buka menu **Cron Jobs** di cPanel, atur interval setiap 5 atau 15 menit (`*/15 * * * *`):
     ```bash
     /usr/bin/php /home/username/tanampohon_app/cron/run.php >/dev/null 2>&1
     ```
   - **Metode B: Web Cron (cron-job.org / UptimeRobot / curl)**:
     Gunakan URL webhook dengan token rahasia dari file `.env`:
     ```text
     https://domainanda.com/cron/run?token=tanampohon_secret_cron_token_2026
     ```
   - **Metode C: Manual Trigger dari Admin Panel**:
     Buka menu **Admin Panel > Pengaturan Sistem** lalu klik tombol **"Jalankan Cron Job Sekarang"**.

---

## 🔑 Akun Uji Coba Default (Seeder)

| Peran (Role) | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@tanampohon.id` | `Admin123!` | Akses penuh Panel Admin, verifikasi bukti bayar manual, dan kelola event |
| **Peserta / Donatur** | `budi@example.com` | `Budi123!` | Akses Dashboard Peserta, sertifikat, dan pesanan |

---

## 📁 Struktur Direktori

```text
├── app/
│   ├── Controllers/     # Controller logika bisnis
│   ├── Models/          # Model PDO terstruktur
│   ├── Middleware/      # Auth, Admin, Guest, Csrf middleware
│   ├── Helpers/         # Helper global (format_rupiah, e, auth, dsb)
│   └── Services/        # Layanan integrasi (Mailer, RajaOngkir, PDF)
├── config/              # Konfigurasi app & database
├── core/                # MVC Engine (Router, Model, Controller, Session)
├── cron/                # Script tugas berkala (auto-expire transaksi)
├── public/              # Document root web server
│   ├── assets/          # CSS, JS, Gambar publik
│   └── index.php        # Front controller
├── resources/views/     # Template tampilan PHP
│   ├── layouts/         # Layout utama (app, auth, dashboard, admin)
│   ├── auth/            # Halaman login, register, reset password
│   ├── public/          # Landing page, donasi, event, toko, peta
│   ├── dashboard/       # Area member peserta
│   └── admin/           # Area admin panel
└── storage/             # File upload & bukti pembayaran privat
```

