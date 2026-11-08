<?php
/**
 * Model User — TanamPohon.id
 * 
 * Mengelola data pengguna, autentikasi, referral, poin, dan profil.
 */

require_once __DIR__ . '/../../core/Model.php';

class User extends Model
{
    /** @var string Nama tabel */
    protected string $table = 'users';

    /** @var string Primary key */
    protected string $primaryKey = 'id';

    /**
     * Cari user berdasarkan email
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, r.slug as role_slug 
            FROM {$this->table} u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email 
            LIMIT 1
        ");
        $stmt->execute(['email' => strtolower(trim($email))]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cari user berdasarkan ID lengkap dengan relasi role
     *
     * @param int $id
     * @return array|false
     */
    public function findWithRole(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, r.slug as role_slug, r.description as role_description
            FROM {$this->table} u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cari user berdasarkan kode referral
     *
     * @param string $code
     * @return array|false
     */
    public function findByReferralCode(string $code): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE referral_code = :code LIMIT 1");
        $stmt->execute(['code' => strtoupper(trim($code))]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cari user berdasarkan token reset password yang masih berlaku
     *
     * @param string $token
     * @return array|false
     */
    public function findByResetToken(string $token): array|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE reset_token = :token 
              AND reset_expires > NOW()
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cari user berdasarkan token verifikasi email
     *
     * @param string $token
     * @return array|false
     */
    public function findByVerifyToken(string $token): array|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE email_verify_token = :token 
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buat user baru (pendaftaran)
     *
     * @param array $data Data pendaftaran
     * @return int ID user baru
     */
    public function createUser(array $data): int
    {
        $referralCode = $this->generateUniqueReferralCode();
        $verifyToken = bin2hex(random_bytes(32));

        $sql = "INSERT INTO {$this->table} (
            name, email, password, phone, city, role_id, 
            referral_code, referred_by, points, default_tshirt_size, 
            is_anonymous_default, status, email_verify_token, created_at, updated_at
        ) VALUES (
            :name, :email, :password, :phone, :city, :role_id,
            :referral_code, :referred_by, :points, :default_tshirt_size,
            :is_anonymous_default, :status, :email_verify_token, NOW(), NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name'                 => trim($data['name']),
            'email'                => strtolower(trim($data['email'])),
            'password'             => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone'                => $data['phone'] ?? null,
            'city'                 => $data['city'] ?? null,
            'role_id'              => $data['role_id'] ?? 5, // 5 = peserta
            'referral_code'        => $referralCode,
            'referred_by'          => $data['referred_by'] ?? null,
            'points'               => $data['points'] ?? 0,
            'default_tshirt_size'  => $data['default_tshirt_size'] ?? null,
            'is_anonymous_default' => !empty($data['is_anonymous_default']) ? 1 : 0,
            'status'               => $data['status'] ?? 'active',
            'email_verify_token'   => $verifyToken,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Generate kode referral unik (8 karakter alfanumerik)
     *
     * @return string
     */
    public function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $exists = $this->findByReferralCode($code);
        } while ($exists);

        return $code;
    }

    /**
     * Simpan token reset password (berlaku 1 jam)
     *
     * @param int $userId
     * @return string Token reset
     */
    public function createResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET reset_token = :token, reset_expires = :expires, updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt->execute([
            'token'   => $token,
            'expires' => $expires,
            'id'      => $userId,
        ]);

        return $token;
    }

    /**
     * Reset password menggunakan token
     *
     * @param string $token
     * @param string $newPassword
     * @return bool
     */
    public function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        $user = $this->findByResetToken($token);
        if (!$user) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET password = :password, reset_token = NULL, reset_expires = NULL, updated_at = NOW() 
            WHERE id = :id
        ");

        return $stmt->execute([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'id'       => $user['id'],
        ]);
    }

    /**
     * Verifikasi email user
     *
     * @param string $token
     * @return bool
     */
    public function verifyEmailToken(string $token): bool
    {
        $user = $this->findByVerifyToken($token);
        if (!$user) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET email_verified_at = NOW(), email_verify_token = NULL, updated_at = NOW() 
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $user['id']]);
    }

    /**
     * Update profil pengguna
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $fields = [];
        $bindings = ['id' => $userId];

        $allowedFields = ['name', 'phone', 'city', 'default_tshirt_size', 'is_anonymous_default', 'avatar'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = :{$field}";
                $bindings[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = "`updated_at` = NOW()";
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($bindings);
    }

    /**
     * Update kata sandi pengguna
     *
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET password = :password, updated_at = NOW() 
            WHERE id = :id
        ");

        return $stmt->execute([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'id'       => $userId,
        ]);
    }

    /**
     * Tambah poin ke pengguna
     *
     * @param int $userId
     * @param int $points
     * @return bool
     */
    public function addPoints(int $userId, int $points): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET points = points + :points, updated_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute(['points' => $points, 'id' => $userId]);
    }

    /**
     * Dapatkan statistik ringkasan pengguna untuk dashboard
     *
     * @param int $userId
     * @return array
     */
    public function getUserStats(int $userId): array
    {
        // 1. Total pohon yang didonasikan
        $stmtTrees = $this->db->prepare("
            SELECT COALESCE(SUM(total_trees), 0) as total_trees, COALESCE(SUM(total_amount), 0) as total_donation
            FROM donations
            WHERE user_id = :user_id AND status = 'berhasil'
        ");
        $stmtTrees->execute(['user_id' => $userId]);
        $donationStats = $stmtTrees->fetch(PDO::FETCH_ASSOC) ?: ['total_trees' => 0, 'total_donation' => 0];

        // 2. Jumlah kegiatan diikuti
        $stmtEvents = $this->db->prepare("
            SELECT COUNT(*) as total_events
            FROM participants
            WHERE user_id = :user_id AND registration_status IN ('terkonfirmasi', 'hadir')
        ");
        $stmtEvents->execute(['user_id' => $userId]);
        $eventStats = $stmtEvents->fetch(PDO::FETCH_ASSOC) ?: ['total_events' => 0];

        // 3. Jumlah pesanan kaos
        $stmtOrders = $this->db->prepare("
            SELECT COUNT(*) as total_orders
            FROM orders
            WHERE user_id = :user_id
        ");
        $stmtOrders->execute(['user_id' => $userId]);
        $orderStats = $stmtOrders->fetch(PDO::FETCH_ASSOC) ?: ['total_orders' => 0];

        // 4. Poin user saat ini
        $stmtUser = $this->db->prepare("SELECT points FROM {$this->table} WHERE id = :id");
        $stmtUser->execute(['id' => $userId]);
        $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

        return [
            'total_trees'    => (int)$donationStats['total_trees'],
            'total_donation' => (float)$donationStats['total_donation'],
            'total_events'   => (int)$eventStats['total_events'],
            'total_orders'   => (int)$orderStats['total_orders'],
            'points'         => (int)($userData['points'] ?? 0),
        ];
    }

    /**
     * Ambil statistik referral pengguna dan daftar teman yang diajak
     *
     * @param int $userId
     * @return array
     */
    public function getReferralStats(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email, u.created_at as joined_at,
                   COALESCE(SUM(d.total_trees), 0) as trees_planted,
                   r.bonus_points
            FROM {$this->table} u
            LEFT JOIN referrals r ON r.referred_id = u.id AND r.referrer_id = :user_id1
            LEFT JOIN donations d ON d.user_id = u.id AND d.status = 'berhasil'
            WHERE u.referred_by = :user_id2
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        $stmt->execute([
            'user_id1' => $userId,
            'user_id2' => $userId,
        ]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalTreesFromReferrals = 0;
        $totalBonusPoints = 0;

        foreach ($friends as &$f) {
            $totalTreesFromReferrals += (int)$f['trees_planted'];
            $totalBonusPoints += (int)($f['bonus_points'] ?? 25);
            $nameParts = explode(' ', trim($f['name']));
            if (count($nameParts) > 1) {
                $f['masked_name'] = $nameParts[0] . ' ' . substr($nameParts[1], 0, 1) . '***';
            } else {
                $f['masked_name'] = substr($f['name'], 0, 3) . '***';
            }
        }

        return [
            'friends'                    => $friends,
            'total_referred'             => count($friends),
            'total_trees_from_referrals' => $totalTreesFromReferrals,
            'total_bonus_points'         => $totalBonusPoints,
        ];
    }

    /**
     * Update kolom avatar user.
     *
     * @param int    $userId
     * @param string $avatarPath  Path relatif, misal '/storage/uploads/avatars/avatar_1_123.jpg'
     * @return bool
     */
    public function updateAvatar(int $userId, string $avatarPath): bool
    {
        $sql = "UPDATE users SET avatar = :avatar WHERE id = :id";
        $affected = $this->db->execute($sql, [':avatar' => $avatarPath, ':id' => $userId]);
        return $affected > 0;
    }
}

