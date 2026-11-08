#!/usr/bin/env bash
# ==============================================================================
# Plant For Fun / TanamPohon.id — Script Pembaruan & Deployment Otomatis
# Digunakan di Proxmox (LXC / VM) atau Server Docker
# ==============================================================================

set -e

# Warna terminal
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}======================================================${NC}"
echo -e "${GREEN}  🌱 Plant For Fun — Script Pembaruan Docker (Proxmox)${NC}"
echo -e "${BLUE}======================================================${NC}"

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# 1. Cek Docker & Docker Compose
echo -e "\n${YELLOW}[1/6] Memeriksa Docker & Docker Compose...${NC}"
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker belum terpasang. Memasang Docker Engine...${NC}"
    curl -fsSL https://get.docker.com | sh
fi

if docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    echo -e "${RED}Error: Docker Compose tidak ditemukan!${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Docker & Docker Compose siap digunakan.${NC}"

# 2. Cek File Environment (.env)
echo -e "\n${YELLOW}[2/6] Memeriksa file konfigurasi lingkungan (.env)...${NC}"
if [ ! -f "$PROJECT_DIR/.env" ]; then
    if [ -f "$PROJECT_DIR/.env.docker.example" ]; then
        echo -e "${YELLOW}File .env belum ada. Menyalin dari .env.docker.example...${NC}"
        cp "$PROJECT_DIR/.env.docker.example" "$PROJECT_DIR/.env"
        echo -e "${GREEN}✓ File .env berhasil dibuat.${NC}"
    else
        echo -e "${RED}Warning: .env.docker.example tidak ditemukan!${NC}"
    fi
else
    echo -e "${GREEN}✓ File .env sudah ada.${NC}"
fi

# 3. Pastikan Struktur Direktori Storage & Permission
echo -e "\n${YELLOW}[3/6] Memeriksa permission direktori storage & uploads...${NC}"
mkdir -p "$PROJECT_DIR/storage/uploads/payment_proofs" \
         "$PROJECT_DIR/storage/uploads/qris" \
         "$PROJECT_DIR/storage/uploads/news" \
         "$PROJECT_DIR/storage/uploads/products" \
         "$PROJECT_DIR/storage/uploads/avatars" \
         "$PROJECT_DIR/storage/uploads/certificates" \
         "$PROJECT_DIR/storage/logs" \
         "$PROJECT_DIR/storage/cache"

chmod -R 775 "$PROJECT_DIR/storage"
# Jika root, ubah kepemilikan ke UID 1000 / 33 (www-data)
if [ "$(id -u)" -eq 0 ]; then
    chown -R 1000:1000 "$PROJECT_DIR/storage" 2>/dev/null || true
fi
echo -e "${GREEN}✓ Permission storage berhasil disetel.${NC}"

# 4. Backup Database Singkat (Opsional jika container DB sedang berjalan)
if [ "$1" == "--backup" ] || [ "$1" == "-b" ]; then
    echo -e "\n${YELLOW}[Bonus] Membuat backup database sebelum update...${NC}"
    BACKUP_FILE="$PROJECT_DIR/backup_$(date +%Y%m%d_%H%M%S).sql"
    if $DOCKER_COMPOSE ps -q db &>/dev/null && [ -n "$($DOCKER_COMPOSE ps -q db)" ]; then
        $DOCKER_COMPOSE exec -T db mariadb-dump -u root -proot tanampohon_db > "$BACKUP_FILE" 2>/dev/null || true
        echo -e "${GREEN}✓ Database dibackup ke: $BACKUP_FILE${NC}"
    fi
fi

# 5. Build Ulang & Jalankan Container
echo -e "\n${YELLOW}[4/6] Melakukan build & restart container...${NC}"
$DOCKER_COMPOSE up -d --build --remove-orphans

# 6. Verifikasi Health & Status
echo -e "\n${YELLOW}[5/6] Menunggu container aktif dan sehat...${NC}"
sleep 3

# Opsi re-import database jika diberikan argumen --import-db
if [ "$1" == "--import-db" ]; then
    echo -e "\n${YELLOW}[Opsional] Mengimpor ulang database.sql...${NC}"
    if [ -f "$PROJECT_DIR/database.sql" ]; then
        $DOCKER_COMPOSE exec -T db mysql -u root -proot tanampohon_db < "$PROJECT_DIR/database.sql"
        echo -e "${GREEN}✓ Database berhasil diimpor ulang.${NC}"
    fi
fi

echo -e "\n${YELLOW}[6/6] Status Container Saat Ini:${NC}"
$DOCKER_COMPOSE ps

echo -e "\n${BLUE}======================================================${NC}"
echo -e "${GREEN}  ✓ Pembaruan Berhasil Selesai!${NC}"
echo -e "${BLUE}======================================================${NC}"
echo -e "Aplikasi dapat diakses di: ${GREEN}http://IP_SERVER:8000${NC}"
echo -e "Login Admin: ${YELLOW}admin@tanampohon.id${NC} | Password: ${YELLOW}admin123${NC}"
echo -e "Log aktivitas: jalankan ${BLUE}docker compose logs -f${NC}\n"

