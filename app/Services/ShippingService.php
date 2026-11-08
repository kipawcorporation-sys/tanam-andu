<?php
/**
 * ShippingService — Plant For Fun
 * 
 * Mesin Cek Ongkir Mandiri (Internal / Self-Contained Shipping Rate Engine)
 * 100% berjalan lokal tanpa ketergantungan pada API luar (tanpa pihak ketiga/RajaOngkir).
 * 
 * Origin Pengiriman:
 * Kebon Kopi Kerenceng & andu Coffee Lab
 * Cicalengka, Kabupaten Bandung, Jawa Barat (Kode Pos: 40395)
 */

class ShippingService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hitung opsi tarif pengiriman berdasarkan kota/wilayah tujuan dan berat barang
     *
     * @param string $destination Nama kota, kabupaten, atau kecamatan tujuan
     * @param int $weightGram Total berat dalam gram (default: 1000g)
     * @param float $subtotal Total belanja untuk kalkulasi subsidi/promo ongkir
     * @return array Daftar opsi kurir, layanan, tarif, dan estimasi waktu sampai (ETD)
     */
    public function calculateRates(string $destination, int $weightGram = 1000, float $subtotal = 0): array
    {
        $weightGram = max(100, $weightGram);
        // Standar logistik Indonesia: toleransi berat 1.200 gr masih dihitung 1 kg
        $weightKg = ($weightGram <= 1200) ? 1 : (int)ceil($weightGram / 1000);

        // Cari zona tarif dari database berdasarkan input tujuan
        $zone = $this->resolveDestinationZone($destination);

        $baseRate = (float)$zone['base_rate_per_kg'];
        $zoneCode = $zone['zone_code'];
        $zoneName = $zone['zone_name'];
        $matchedCity = $zone['matched_city'] ?? $destination;
        $matchedProvince = $zone['matched_province'] ?? 'Indonesia';

        $rates = [];

        // 1. OPSI AMBIL LANGSUNG KE KEDAI (Gratis / Rp 0) — Satu-satunya opsi Bebas Ongkir
        $rates[] = [
            'courier'     => 'Ambil Sendiri ke Kedai / Roastery andu',
            'courier_code'=> 'pickup',
            'service'     => 'Pick-up Point Kedai andu Coffee Lab & Basecamp Kerenceng',
            'cost'        => 0,
            'original_cost'=> 0,
            'is_free'     => true,
            'etd'         => 'Siap Diambil di Kedai',
            'code'        => 'pickup_kerenceng',
            'badge'       => 'GRATIS (AMBIL KE KEDAI)',
            'badge_color' => 'success',
            'description' => 'Gratis ongkir khusus ambil pesanan langsung di kedai andu atau basecamp Cicalengka.',
        ];

        // 2. KURIR LOKAL KERENCENG & CICALENGKA (Khusus Zona Lokal Bandung Raya)
        if ($zoneCode === 'ZONA_LOKAL' && (int)$zone['local_active'] === 1) {
            $rates[] = [
                'courier'     => 'Kurir Lokal Kerenceng Express',
                'courier_code'=> 'local',
                'service'     => 'Sameday / Nextday Bandung Timur & Raya',
                'cost'        => 8000,
                'original_cost'=> 8000,
                'is_free'     => false,
                'etd'         => 'Hari Ini / Esok',
                'code'        => 'local_cicalengka',
                'badge'       => 'TERCEPAT LOKAL',
                'badge_color' => 'primary',
                'description' => 'Dikirim langsung oleh kurir tim ekspedisi Kerenceng.',
            ];
        }

        // 3. JNE (Jalur Nugraha Ekakurir)
        if ((int)$zone['jne_active'] === 1) {
            // JNE REG (Reguler)
            $jneRegCost = (int)round($baseRate * $weightKg);
            $rates[] = [
                'courier'     => 'JNE Express',
                'courier_code'=> 'jne',
                'service'     => 'REG (Layanan Reguler)',
                'cost'        => $jneRegCost,
                'original_cost'=> $jneRegCost,
                'is_free'     => false,
                'etd'         => $zone['etd_regular'],
                'code'        => 'jne_reg',
                'badge'       => 'STANDAR REKOMENDASI',
                'badge_color' => 'secondary',
                'description' => 'Pengiriman reguler ke seluruh pelosok Nusantara.',
            ];

            // JNE YES (Yakin Esok Sampai) — Tersedia untuk zona terjangkau (Jawa, Bali, Sumatera Selatan/Utara)
            if (in_array($zoneCode, ['ZONA_LOKAL', 'ZONA_JABODETABEK', 'ZONA_JABAR_LAIN', 'ZONA_JATENG_DIY', 'ZONA_JATIM', 'ZONA_BALI_NTB', 'ZONA_SUMATERA_SELATAN', 'ZONA_SUMATERA_UTARA'])) {
                $jneYesCost = (int)round($baseRate * 1.7 * $weightKg);
                $rates[] = [
                    'courier'     => 'JNE Express',
                    'courier_code'=> 'jne',
                    'service'     => 'YES (Yakin Esok Sampai)',
                    'cost'        => $jneYesCost,
                    'original_cost'=> $jneYesCost,
                    'is_free'     => false,
                    'etd'         => $zone['etd_express'] ?: '1 Hari',
                    'code'        => 'jne_yes',
                    'badge'       => 'KILAT 24 JAM',
                    'badge_color' => 'danger',
                    'description' => 'Paling cocok untuk kopi sangrai fresh agar cepat tiba.',
                ];
            }

            // JNE OKE (Ongkos Kirim Ekonomis)
            $jneOkeCost = (int)round($baseRate * 0.85 * $weightKg);
            $rates[] = [
                'courier'     => 'JNE Express',
                'courier_code'=> 'jne',
                'service'     => 'OKE (Ongkos Kirim Ekonomis)',
                'cost'        => $jneOkeCost,
                'original_cost'=> $jneOkeCost,
                'is_free'     => false,
                'etd'         => $this->addDaysToEtd($zone['etd_regular'], 1),
                'code'        => 'jne_oke',
                'badge'       => 'HEMAT',
                'badge_color' => 'info',
                'description' => 'Pilihan ongkir lebih terjangkau.',
            ];

            // JTR (JNE Trucking / Kargo) untuk pesanan paket besar / grosir >= 10kg
            if ($weightGram >= 10000) {
                $jtrCost = (int)round(($baseRate * 0.45 * 10) + (($weightKg - 10) * 3000));
                $rates[] = [
                    'courier'     => 'JNE Trucking (JTR)',
                    'courier_code'=> 'jne',
                    'service'     => 'JTR (Kargo Truk Minimal 10kg)',
                    'cost'        => $jtrCost,
                    'original_cost'=> $jtrCost,
                    'is_free'     => false,
                    'etd'         => '3-6 Hari',
                    'code'        => 'jne_jtr',
                    'badge'       => 'KARGO GROSIR',
                    'badge_color' => 'dark',
                    'description' => 'Tarif termurah untuk pesanan borongan karung biji kopi & bibit.',
                ];
            }
        }

        // 4. J&T Express
        if ((int)$zone['jnt_active'] === 1) {
            $jntEzCost = (int)round($baseRate * $weightKg);
            $rates[] = [
                'courier'     => 'J&T Express',
                'courier_code'=> 'jnt',
                'service'     => 'EZ (Reguler Express)',
                'cost'        => $jntEzCost,
                'original_cost'=> $jntEzCost,
                'is_free'     => false,
                'etd'         => $zone['etd_regular'],
                'code'        => 'jnt_ez',
                'badge'       => 'POPULER',
                'badge_color' => 'danger',
                'description' => 'Antar jemput paket setiap hari tanpa libur.',
            ];

            if (in_array($zoneCode, ['ZONA_LOKAL', 'ZONA_JABODETABEK', 'ZONA_JABAR_LAIN', 'ZONA_JATENG_DIY', 'ZONA_JATIM'])) {
                $jntSuperCost = (int)round($baseRate * 1.6 * $weightKg);
                $rates[] = [
                    'courier'     => 'J&T Express',
                    'courier_code'=> 'jnt',
                    'service'     => 'J&T Super (Layanan Prioritas)',
                    'cost'        => $jntSuperCost,
                    'original_cost'=> $jntSuperCost,
                    'is_free'     => false,
                    'etd'         => '1-2 Hari',
                    'code'        => 'jnt_super',
                    'badge'       => 'PRIORITAS',
                    'badge_color' => 'warning',
                    'description' => 'Jalur penanganan khusus paket prioritas.',
                ];
            }
        }

        // 5. SiCepat Ekspres
        if ((int)$zone['sicepat_active'] === 1) {
            $sicepatRegCost = (int)round($baseRate * $weightKg);
            $rates[] = [
                'courier'     => 'SiCepat Ekspres',
                'courier_code'=> 'sicepat',
                'service'     => 'REG (SiCepat Reguler)',
                'cost'        => $sicepatRegCost,
                'original_cost'=> $sicepatRegCost,
                'is_free'     => false,
                'etd'         => $zone['etd_regular'],
                'code'        => 'sicepat_reg',
                'badge'       => 'CEPAT & HANDAL',
                'badge_color' => 'danger',
                'description' => 'Pelacakan akurat dengan armada sigap.',
            ];

            // SiCepat HALU (Harga Luar Biasa)
            $sicepatHaluCost = (int)round($baseRate * 0.85 * $weightKg);
            $rates[] = [
                'courier'     => 'SiCepat Ekspres',
                'courier_code'=> 'sicepat',
                'service'     => 'HALU (Harga Luar Biasa)',
                'cost'        => $sicepatHaluCost,
                'original_cost'=> $sicepatHaluCost,
                'is_free'     => false,
                'etd'         => $this->addDaysToEtd($zone['etd_regular'], 1),
                'code'        => 'sicepat_halu',
                'badge'       => 'HEMAT EKONOMIS',
                'badge_color' => 'primary',
                'description' => 'Ongkir ekonomis dengan kecepatan reguler.',
            ];
        }

        // 6. POS Indonesia
        if ((int)$zone['pos_active'] === 1) {
            $posRegCost = (int)round($baseRate * 0.9 * $weightKg);
            $rates[] = [
                'courier'     => 'POS Indonesia',
                'courier_code'=> 'pos',
                'service'     => 'Pos Reguler Nusantara',
                'cost'        => $posRegCost,
                'original_cost'=> $posRegCost,
                'is_free'     => false,
                'etd'         => $zone['etd_regular'],
                'code'        => 'pos_reg',
                'badge'       => 'JANGKAUAN LUAS',
                'badge_color' => 'warning',
                'description' => 'Menjangkau hingga ke pelosok kecamatan & desa terpencil.',
            ];
        }

        // Tidak ada promo gratis ongkir untuk kurir ekspedisi.
        // Gratis ongkir hanya berlaku untuk opsi Ambil Sendiri ke Kedai / Roastery andu.

        return [
            'origin'            => 'Cicalengka, Kabupaten Bandung, Jawa Barat (40395)',
            'destination_input' => $destination,
            'matched_city'      => $matchedCity,
            'matched_province'  => $matchedProvince,
            'zone_name'         => $zoneName,
            'weight_gram'       => $weightGram,
            'weight_kg'         => $weightKg,
            'base_rate_per_kg'  => $baseRate,
            'rates'             => $rates,
        ];
    }

    /**
     * Cari spesifik satu opsi ongkir berdasarkan kode layanan (misal: jne_reg)
     */
    public function getRateByCode(string $serviceCode, string $destinationCity, int $weightGram, float $subtotal = 0): ?array
    {
        if ($serviceCode === 'pickup_event' || $serviceCode === 'pickup_kerenceng') {
            return [
                'courier'      => 'Ambil Sendiri ke Kedai / Roastery andu',
                'courier_code' => 'pickup',
                'service'      => 'Pick-up Point Kedai andu Coffee Lab & Basecamp Kerenceng',
                'cost'         => 0,
                'original_cost'=> 0,
                'is_free'      => true,
                'etd'          => 'Siap Diambil di Kedai',
                'code'         => $serviceCode,
            ];
        }

        $result = $this->calculateRates($destinationCity, $weightGram, $subtotal);
        foreach ($result['rates'] as $rate) {
            if ($rate['code'] === $serviceCode) {
                return $rate;
            }
        }

        // Fallback jika kode tidak ditemukan: ambil opsi kurir reguler pertama
        return $result['rates'][1] ?? $result['rates'][0] ?? null;
    }

    /**
     * Pencarian kota/kabupaten tujuan dengan fitur autocomplete (AJAX)
     */
    public function searchCities(string $keyword, int $limit = 15): array
    {
        $keyword = trim($keyword);
        if (empty($keyword)) {
            $stmt = $this->db->prepare("
                SELECT c.id, c.city_name, c.province, c.city_type, c.postal_code, z.zone_name, z.base_rate_per_kg, z.etd_regular
                FROM shipping_cities c
                JOIN shipping_zones z ON z.id = c.zone_id
                ORDER BY c.id ASC
                LIMIT :lim
            ");
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->db->prepare("
            SELECT c.id, c.city_name, c.province, c.city_type, c.postal_code, z.zone_name, z.base_rate_per_kg, z.etd_regular
            FROM shipping_cities c
            JOIN shipping_zones z ON z.id = c.zone_id
            WHERE c.city_name LIKE :kw1 OR c.province LIKE :kw2 OR c.postal_code LIKE :kw3
            ORDER BY 
                CASE 
                    WHEN c.city_name LIKE :kw_start THEN 1
                    WHEN c.city_name LIKE :kw4 THEN 2
                    ELSE 3
                END, c.city_name ASC
            LIMIT :lim
        ");
        $stmt->bindValue(':kw1', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':kw2', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':kw3', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':kw4', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':kw_start', "{$keyword}%", PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Dapatkan daftar seluruh zona tarif untuk halaman admin
     */
    public function getAllZones(): array
    {
        return $this->db->query("
            SELECT z.*, COUNT(c.id) AS total_cities
            FROM shipping_zones z
            LEFT JOIN shipping_cities c ON c.zone_id = z.id
            GROUP BY z.id
            ORDER BY z.id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Pencocokan cerdas teks tujuan ke zona logistik
     */
    private function resolveDestinationZone(string $destination): array
    {
        $dest = trim($destination);
        $destLower = strtolower($dest);

        // 1. Coba cari di tabel shipping_cities
        if (!empty($dest)) {
            $stmt = $this->db->prepare("
                SELECT z.*, c.city_name AS matched_city, c.province AS matched_province
                FROM shipping_cities c
                JOIN shipping_zones z ON z.id = c.zone_id
                WHERE c.city_name LIKE :q OR :q2 LIKE CONCAT('%', c.city_name, '%')
                LIMIT 1
            ");
            $stmt->execute([':q' => "%{$dest}%", ':q2' => $dest]);
            $found = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($found) {
                return $found;
            }

            // Coba cari berdasarkan provinsi
            $stmtProv = $this->db->prepare("
                SELECT z.*, c.city_name AS matched_city, c.province AS matched_province
                FROM shipping_cities c
                JOIN shipping_zones z ON z.id = c.zone_id
                WHERE c.province LIKE :p
                LIMIT 1
            ");
            $stmtProv->execute([':p' => "%{$dest}%"]);
            $foundProv = $stmtProv->fetch(PDO::FETCH_ASSOC);
            if ($foundProv) {
                return $foundProv;
            }
        }

        // 2. Keyword matching fallback jika input teks bebas
        $zoneCode = 'ZONA_JABODETABEK'; // Default standar

        if (str_contains($destLower, 'cicalengka') || str_contains($destLower, 'bandung') || str_contains($destLower, 'cimahi') || str_contains($destLower, 'sumedang') || str_contains($destLower, 'garut')) {
            $zoneCode = 'ZONA_LOKAL';
        } elseif (str_contains($destLower, 'jakarta') || str_contains($destLower, 'bogor') || str_contains($destLower, 'depok') || str_contains($destLower, 'tangerang') || str_contains($destLower, 'bekasi') || str_contains($destLower, 'serang') || str_contains($destLower, 'cilegon') || str_contains($destLower, 'banten')) {
            $zoneCode = 'ZONA_JABODETABEK';
        } elseif (str_contains($destLower, 'karawang') || str_contains($destLower, 'cirebon') || str_contains($destLower, 'sukabumi') || str_contains($destLower, 'tasik') || str_contains($destLower, 'indramayu') || str_contains($destLower, 'subang') || str_contains($destLower, 'cianjur') || str_contains($destLower, 'jawa barat') || str_contains($destLower, 'jabar')) {
            $zoneCode = 'ZONA_JABAR_LAIN';
        } elseif (str_contains($destLower, 'semarang') || str_contains($destLower, 'solo') || str_contains($destLower, 'surakarta') || str_contains($destLower, 'jogja') || str_contains($destLower, 'yogyakarta') || str_contains($destLower, 'magelang') || str_contains($destLower, 'purwokerto') || str_contains($destLower, 'tegal') || str_contains($destLower, 'kudus') || str_contains($destLower, 'jawa tengah') || str_contains($destLower, 'jateng')) {
            $zoneCode = 'ZONA_JATENG_DIY';
        } elseif (str_contains($destLower, 'surabaya') || str_contains($destLower, 'malang') || str_contains($destLower, 'sidoarjo') || str_contains($destLower, 'kediri') || str_contains($destLower, 'jember') || str_contains($destLower, 'banyuwangi') || str_contains($destLower, 'madura') || str_contains($destLower, 'madiun') || str_contains($destLower, 'jawa timur') || str_contains($destLower, 'jatim')) {
            $zoneCode = 'ZONA_JATIM';
        } elseif (str_contains($destLower, 'bali') || str_contains($destLower, 'denpasar') || str_contains($destLower, 'kuta') || str_contains($destLower, 'ubud') || str_contains($destLower, 'lombok') || str_contains($destLower, 'mataram') || str_contains($destLower, 'ntb') || str_contains($destLower, 'bima')) {
            $zoneCode = 'ZONA_BALI_NTB';
        } elseif (str_contains($destLower, 'lampung') || str_contains($destLower, 'palembang') || str_contains($destLower, 'jambi') || str_contains($destLower, 'bengkulu') || str_contains($destLower, 'bangka') || str_contains($destLower, 'belitung')) {
            $zoneCode = 'ZONA_SUMATERA_SELATAN';
        } elseif (str_contains($destLower, 'medan') || str_contains($destLower, 'padang') || str_contains($destLower, 'pekanbaru') || str_contains($destLower, 'riau') || str_contains($destLower, 'batam') || str_contains($destLower, 'aceh') || str_contains($destLower, 'sumatera')) {
            $zoneCode = 'ZONA_SUMATERA_UTARA';
        } elseif (str_contains($destLower, 'kalimantan') || str_contains($destLower, 'balikpapan') || str_contains($destLower, 'samarinda') || str_contains($destLower, 'banjarmasin') || str_contains($destLower, 'pontianak') || str_contains($destLower, 'tarakan') || str_contains($destLower, 'ikn')) {
            $zoneCode = 'ZONA_KALIMANTAN';
        } elseif (str_contains($destLower, 'sulawesi') || str_contains($destLower, 'makassar') || str_contains($destLower, 'manado') || str_contains($destLower, 'palu') || str_contains($destLower, 'kendari') || str_contains($destLower, 'gorontalo') || str_contains($destLower, 'toraja')) {
            $zoneCode = 'ZONA_SULAWESI';
        } elseif (str_contains($destLower, 'ntt') || str_contains($destLower, 'kupang') || str_contains($destLower, 'labuan bajo') || str_contains($destLower, 'flores') || str_contains($destLower, 'ambon') || str_contains($destLower, 'maluku') || str_contains($destLower, 'ternate')) {
            $zoneCode = 'ZONA_NTT_MALUKU';
        } elseif (str_contains($destLower, 'papua') || str_contains($destLower, 'jayapura') || str_contains($destLower, 'sorong') || str_contains($destLower, 'merauke') || str_contains($destLower, 'timika') || str_contains($destLower, 'manokwari') || str_contains($destLower, 'biak')) {
            $zoneCode = 'ZONA_PAPUA';
        }

        // Ambil data zone dari database
        $stmtZone = $this->db->prepare("SELECT * FROM shipping_zones WHERE zone_code = :zc LIMIT 1");
        $stmtZone->execute([':zc' => $zoneCode]);
        $zoneData = $stmtZone->fetch(PDO::FETCH_ASSOC);

        if ($zoneData) {
            $zoneData['matched_city'] = $dest ?: 'Tujuan Pengiriman';
            $zoneData['matched_province'] = 'Indonesia';
            return $zoneData;
        }

        // Hardcoded safety fallback jika tabel kosong
        return [
            'id'               => 2,
            'zone_code'        => 'ZONA_JABODETABEK',
            'zone_name'        => 'Jabodetabek & Sekitarnya',
            'base_rate_per_kg' => 10000,
            'etd_regular'      => '1-2 Hari',
            'etd_express'      => '1 Hari',
            'jne_active'       => 1,
            'jnt_active'       => 1,
            'sicepat_active'   => 1,
            'pos_active'       => 1,
            'local_active'     => 0,
            'matched_city'     => $dest ?: 'Jabodetabek',
            'matched_province' => 'DKI Jakarta / Banten / Jawa Barat',
        ];
    }

    private function addDaysToEtd(string $etd, int $addDays): string
    {
        // Misal '2-3 Hari' jadi '3-4 Hari'
        if (preg_match('/(\d+)-(\d+)\s*Hari/i', $etd, $m)) {
            $start = (int)$m[1] + $addDays;
            $end = (int)$m[2] + $addDays;
            return "{$start}-{$end} Hari";
        }
        return $etd;
    }
}
