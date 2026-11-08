# Panduan Deployment ke Proxmox (Docker & Docker Compose)

Panduan ini menjelaskan cara memindahkan dan menjalankan project **Plant For Fun / TanamPohon.id** ke server **Proxmox VE** menggunakan Docker.

---

## 1. Persiapan di Proxmox VE

Anda dapat menjalankan project ini di Proxmox melalui dua pilihan:
- **Pilihan A (Rekomendasi - Ringan & Cepat):** Proxmox **LXC Container** (Ubuntu 22.04 / Debian 12).
- **Pilihan B (Isolasi Penuh):** Proxmox **KVM Virtual Machine (VM)** (Ubuntu Server).

### Jika Menggunakan LXC Container (Pilihan A)
Saat membuat LXC di Proxmox Web GUI:
1. Pilih template: `ubuntu-22.04-standard` atau `debian-12-standard`.
2. Di tab **Options** container:
   - Centang **Nesting: 1** (*Wajib untuk menjalankan Docker di dalam LXC*).
   - Centang **keyctl: 1** (jika ada).
3. Nyalakan container dan buka konsolnya.

---

## 2. Instalasi Docker di Container / VM Proxmox

Jalankan perintah ini di dalam shell container/VM Proxmox Anda:

```bash
# Update sistem
apt update && apt upgrade -y

# Pasang dependency & curl
apt install -y curl git rsync ufw

# Install Docker Engine & Docker Compose resmi
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Verifikasi instalasi
docker --version
docker compose version
```

---

## 3. Upload Project ke Server Proxmox

Dari komputer lokal Anda (komputer saat ini), kirim folder project ke Proxmox menggunakan `rsync` atau `scp`:

```bash
# Contoh menggunakan rsync (ganti IP dan user sesuai server Proxmox Anda):
rsync -avz --exclude 'node_modules' --exclude '.git' \
  /home/mhc/Pictures/cocok_tanam/ \
  root@IP_PROXMOX_ANDA:/opt/cocok_tanam/
```

Atau jika di-zip terlebih dahulu:
```bash
# Di komputer lokal:
cd /home/mhc/Pictures/cocok_tanam
tar -czvf /tmp/cocok_tanam.tar.gz .

# Kirim ke Proxmox:
scp /tmp/cocok_tanam.tar.gz root@IP_PROXMOX_ANDA:/opt/

# Di terminal Proxmox:
mkdir -p /opt/cocok_tanam
tar -xzvf /opt/cocok_tanam.tar.gz -C /opt/cocok_tanam/
```

---

## 4. Konfigurasi Lingkungan (`.env`)

Di dalam server Proxmox:

```bash
cd /opt/cocok_tanam

# Salin konfigurasi template Docker
cp .env.docker.example .env

# Edit .env sesuaikan IP Proxmox atau domain Anda
nano .env
```

Pastikan variabel berikut sudah sesuai:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=http://IP_PROXMOX_ANDA:8000
APP_PORT=8000

DB_HOST=db
DB_PORT=3306
DB_DATABASE=tanampohon_db
DB_USERNAME=root
DB_PASSWORD=root
```

---

## 5. Build & Jalankan Container

Cukup jalankan satu perintah:

```bash
docker compose up -d --build
```

Docker Compose akan otomatis:
1. Mem-build image **PHP 8.2-FPM** dengan semua ekstensi yang dibutuhkan (`pdo_mysql`, `gd`, `zip`, `mbstring`, `intl`, `exif`, `bcmath`, `opcache`).
2. Menjalankan container **MariaDB 10.11** dan otomatis mengimpor `database.sql` (41 tabel + data awal lengkap).
3. Menjalankan reverse proxy **Nginx** di port `8000`.
4. Menjalankan otomasi **Cron Job** tiap 15 menit di latar belakang.

---

## 6. Verifikasi & Akses Aplikasi

1. **Cek status container:**
   ```bash
   docker compose ps
   ```
   Pastikan keempat container (`tanampohon_app`, `tanampohon_web`, `tanampohon_db`, `tanampohon_cron`) berstatus `Up` / `healthy`.

2. **Cek log jika ada kendala:**
   ```bash
   docker compose logs -f
   ```

3. **Buka di Browser:**
   - Halaman Utama: `http://IP_PROXMOX_ANDA:8000`
   - Halaman Login Admin: `http://IP_PROXMOX_ANDA:8000/masuk`
     - **Email:** `admin@tanampohon.id`
     - **Password:** `admin123`
   - Pengaturan WhatsApp Fonnte: `http://IP_PROXMOX_ANDA:8000/admin/pengaturan`

---

## 7. Perintah Pemeliharaan Rutin

- **Restart layanan:**
  ```bash
  docker compose restart
  ```
- **Hentikan layanan:**
  ```bash
  docker compose down
  ```
- **Backup database sewaktu-waktu:**
  ```bash
  docker compose exec db mariadb-dump -u root -proot tanampohon_db > backup_$(date +%F).sql
  ```
- **Update kode setelah ada perubahan:**
  ```bash
  docker compose up -d --build
  ```

