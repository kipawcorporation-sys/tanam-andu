<?php
/**
 * Model Badge — TanamPohon.id
 * 
 * Mengelola lencana pencapaian penanaman pohon, sinkronisasi perolehan lencana otomatis,
 * estimasi kalkulasi dampak lingkungan hidup, dan peringkat leaderboard penanam.
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/Notification.php';

class Badge extends Model
{
    protected string $table = 'badges';
    protected string $primaryKey = 'id';

    /**
     * Ambil seluruh lencana aktif yang tersedia di platform
     *
     * @return array
     */
    public function getAllBadges(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE is_active = 1 
            ORDER BY min_trees ASC, sort_order ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil lencana yang telah berhasil diperoleh pengguna
     *
     * @param int $userId
     * @return array
     */
    public function getUserBadges(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*, ub.earned_at
            FROM user_badges ub
            JOIN {$this->table} b ON ub.badge_id = b.id
            WHERE ub.user_id = :user_id AND b.is_active = 1
            ORDER BY b.min_trees ASC, ub.earned_at ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Sinkronisasi perolehan lencana pengguna berdasarkan total pohon yang ditanam.
     * Jika ada lencana baru yang memenuhi syarat, otomatis simpan ke user_badges
     * dan kirimkan notifikasi selamat ke akun pengguna.
     *
     * @param int $userId
     * @param int $totalTrees
     * @return array Daftar lencana yang baru saja diperoleh (jika ada)
     */
    public function syncUserBadges(int $userId, int $totalTrees): array
    {
        $allBadges = $this->getAllBadges();
        $userBadges = $this->getUserBadges($userId);
        $ownedBadgeIds = array_column($userBadges, 'id');

        $newlyEarned = [];
        $notifModel = new Notification();

        foreach ($allBadges as $badge) {
            // Jika memenuhi syarat pohon dan belum pernah dimiliki
            if ($totalTrees >= (int)$badge['min_trees'] && !in_array((int)$badge['id'], $ownedBadgeIds)) {
                $stmtInsert = $this->db->prepare("
                    INSERT INTO user_badges (user_id, badge_id, earned_at)
                    VALUES (:user_id, :badge_id, NOW())
                ");
                $stmtInsert->execute([
                    'user_id'  => $userId,
                    'badge_id' => $badge['id'],
                ]);

                // Berikan notifikasi selamat dalam aplikasi
                $notifModel->createNotification(
                    $userId,
                    'badge',
                    'Selamat! Lencana Baru Terbuka 🏆',
                    "Kamu telah berhasil membuka lencana \"{$badge['name']}\" atas kontribusi menanam {$badge['min_trees']} pohon demi bumi Indonesia!",
                    ['badge_id' => $badge['id'], 'badge_name' => $badge['name']]
                );

                $newlyEarned[] = $badge;
            }
        }

        return $newlyEarned;
    }

    /**
     * Hitung progres menuju lencana berikutnya
     *
     * @param int $totalTrees
     * @return array|null Info lencana berikutnya, sisa pohon, dan persentase progres
     */
    public function getNextBadge(int $totalTrees): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE is_active = 1 AND min_trees > :trees
            ORDER BY min_trees ASC
            LIMIT 1
        ");
        $stmt->execute(['trees' => $totalTrees]);
        $next = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$next) {
            return null; // Pengguna sudah mencapai lencana tertinggi
        }

        $min = (int)$next['min_trees'];
        $remaining = max(0, $min - $totalTrees);
        $percent = min(100, round(($totalTrees / $min) * 100));

        return [
            'badge'      => $next,
            'remaining'  => $remaining,
            'percentage' => $percent,
        ];
    }

    /**
     * Hitung rincian dampak lingkungan hidup pengguna
     *
     * @param int $userId
     * @return array
     */
    public function getUserEnvironmentalImpact(int $userId): array
    {
        // Total pohon berhasil dari donasi
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(di.quantity), 0) as total_trees,
                   COALESCE(SUM(di.quantity * ts.co2_absorption_kg_per_year), 0) as total_co2,
                   COALESCE(SUM(di.quantity * ts.oxygen_production_kg_per_year), 0) as total_o2
            FROM donations d
            JOIN donation_items di ON di.donation_id = d.id
            JOIN tree_species ts ON di.tree_species_id = ts.id
            WHERE d.user_id = :user_id AND d.status = 'berhasil'
        ");
        $stmt->execute(['user_id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalTrees = (int)($data['total_trees'] ?? 0);
        $totalCo2 = (float)($data['total_co2'] ?? 0);
        $totalO2 = (float)($data['total_o2'] ?? 0);

        // Jika belum ada data dari jenis pohon spesifik, gunakan rata-rata default
        if ($totalTrees > 0 && $totalCo2 <= 0) {
            $totalCo2 = $totalTrees * 25.0; // 25 kg CO2/pohon/tahun
            $totalO2 = $totalTrees * 20.0;  // 20 kg O2/pohon/tahun
        }

        // Estimasi luas tutupan hijau (asumsi 1 pohon rata-rata membutuhkan 6-9 m² kanopi)
        $greenAreaSqm = $totalTrees * 8;

        return [
            'total_trees'    => $totalTrees,
            'total_co2_kg'   => $totalCo2,
            'total_o2_kg'    => $totalO2,
            'green_area_sqm' => $greenAreaSqm,
        ];
    }

    /**
     * Hitung peringkat leaderboard pengguna berdasarkan total pohon yang ditanam
     *
     * @param int $userId
     * @return array Info peringkat dan total penanam
     */
    public function getUserLeaderboardRank(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, COALESCE(SUM(d.total_trees), 0) as trees_count
            FROM users u
            LEFT JOIN donations d ON d.user_id = u.id AND d.status = 'berhasil'
            WHERE u.status = 'active'
            GROUP BY u.id
            ORDER BY trees_count DESC, u.id ASC
        ");
        $stmt->execute();
        $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userRank = 1;
        $userTrees = 0;
        $totalParticipants = count($ranks);

        foreach ($ranks as $index => $row) {
            if ((int)$row['id'] === $userId) {
                $userRank = $index + 1;
                $userTrees = (int)$row['trees_count'];
                break;
            }
        }

        return [
            'rank'               => $userRank,
            'user_trees'         => $userTrees,
            'total_participants' => $totalParticipants,
        ];
    }
}

