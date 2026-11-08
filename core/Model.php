<?php
/**
 * TanamPohon.id - Base Model
 * 
 * Semua model mewarisi class ini.
 * Menyediakan CRUD dasar, query builder sederhana,
 * pagination, dan manajemen transaksi.
 */

class Model
{
    /** @var PDO Koneksi database PDO */
    protected PDO $db;

    /** @var string Nama tabel database */
    protected string $table = '';

    /** @var string Primary key tabel */
    protected string $primaryKey = 'id';

    /** @var array Kolom yang boleh diisi secara massal */
    protected array $fillable = [];

    /** @var array Kondisi WHERE yang dikumpulkan */
    private array $wheres = [];

    /** @var array Parameter binding untuk WHERE */
    private array $bindings = [];

    /** @var string|null ORDER BY clause */
    private ?string $orderByClause = null;

    /** @var int|null LIMIT clause */
    private ?int $limitValue = null;

    /** @var int|null OFFSET clause */
    private ?int $offsetValue = null;

    /** @var string|null SELECT kolom spesifik */
    private ?string $selectColumns = null;

    /**
     * Konstruktor - dapatkan koneksi database
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Reset semua kondisi query builder
     */
    private function resetQuery(): void
    {
        $this->wheres = [];
        $this->bindings = [];
        $this->orderByClause = null;
        $this->limitValue = null;
        $this->offsetValue = null;
        $this->selectColumns = null;
    }

    /**
     * Pilih kolom tertentu
     *
     * @param string|array $columns Kolom yang dipilih
     * @return self
     */
    public function select(string|array $columns): self
    {
        if (is_array($columns)) {
            $this->selectColumns = implode(', ', $columns);
        } else {
            $this->selectColumns = $columns;
        }
        return $this;
    }

    /**
     * Tambahkan kondisi WHERE
     *
     * @param string $column Nama kolom
     * @param mixed $operatorOrValue Operator (=, >, <, LIKE, dsb) atau nilai langsung
     * @param mixed $value Nilai (jika parameter kedua adalah operator)
     * @return self
     */
    public function where(string $column, mixed $operatorOrValue, mixed $value = null): self
    {
        if ($value === null) {
            // Format: where('status', 'aktif') -> WHERE status = 'aktif'
            $this->wheres[] = "{$column} = ?";
            $this->bindings[] = $operatorOrValue;
        } else {
            // Format: where('harga', '>', 10000) -> WHERE harga > 10000
            $this->wheres[] = "{$column} {$operatorOrValue} ?";
            $this->bindings[] = $value;
        }
        return $this;
    }

    /**
     * Tambahkan kondisi WHERE IN
     *
     * @param string $column Nama kolom
     * @param array $values Daftar nilai
     * @return self
     */
    public function whereIn(string $column, array $values): self
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "{$column} IN ({$placeholders})";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    /**
     * Tambahkan kondisi WHERE NULL
     *
     * @param string $column Nama kolom
     * @return self
     */
    public function whereNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }

    /**
     * Tambahkan kondisi WHERE NOT NULL
     *
     * @param string $column Nama kolom
     * @return self
     */
    public function whereNotNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }

    /**
     * Tambahkan kondisi WHERE LIKE
     *
     * @param string $column Nama kolom
     * @param string $pattern Pattern pencarian
     * @return self
     */
    public function whereLike(string $column, string $pattern): self
    {
        $this->wheres[] = "{$column} LIKE ?";
        $this->bindings[] = $pattern;
        return $this;
    }

    /**
     * Tambahkan kondisi WHERE BETWEEN
     *
     * @param string $column Nama kolom
     * @param mixed $start Nilai awal
     * @param mixed $end Nilai akhir
     * @return self
     */
    public function whereBetween(string $column, mixed $start, mixed $end): self
    {
        $this->wheres[] = "{$column} BETWEEN ? AND ?";
        $this->bindings[] = $start;
        $this->bindings[] = $end;
        return $this;
    }

    /**
     * Atur pengurutan hasil
     *
     * @param string $column Nama kolom
     * @param string $direction ASC atau DESC
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        if ($this->orderByClause) {
            $this->orderByClause .= ", {$column} {$direction}";
        } else {
            $this->orderByClause = "{$column} {$direction}";
        }
        return $this;
    }

    /**
     * Batasi jumlah hasil
     *
     * @param int $limit Jumlah maksimal hasil
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limitValue = $limit;
        return $this;
    }

    /**
     * Set offset untuk paginasi
     *
     * @param int $offset Offset mulai
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offsetValue = $offset;
        return $this;
    }

    /**
     * Bangun query SELECT lengkap dari kondisi yang dikumpulkan
     *
     * @return array [sql, bindings]
     */
    private function buildSelectQuery(): array
    {
        $columns = $this->selectColumns ?? '*';
        $sql = "SELECT {$columns} FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if ($this->orderByClause) {
            $sql .= ' ORDER BY ' . $this->orderByClause;
        }

        if ($this->limitValue !== null) {
            $sql .= ' LIMIT ' . $this->limitValue;
        }

        if ($this->offsetValue !== null) {
            $sql .= ' OFFSET ' . $this->offsetValue;
        }

        $bindings = $this->bindings;

        // Reset setelah build
        $this->resetQuery();

        return [$sql, $bindings];
    }

    // ========================================
    // CRUD Operations
    // ========================================

    /**
     * Cari record berdasarkan primary key
     *
     * @param int|string $id Nilai primary key
     * @return array|null Data record atau null
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Cari satu record berdasarkan kolom tertentu
     *
     * @param string $column Nama kolom
     * @param mixed $value Nilai yang dicari
     * @return array|null
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1"
        );
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Ambil semua record (dengan kondisi WHERE jika sudah di-chain)
     *
     * @return array Daftar record
     */
    public function all(): array
    {
        // Jika ada kondisi yang dikumpulkan, gunakan query builder
        if (!empty($this->wheres) || $this->orderByClause || $this->limitValue !== null) {
            return $this->get();
        }

        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /**
     * Eksekusi query builder dan ambil hasilnya
     *
     * @return array Daftar record
     */
    public function get(): array
    {
        [$sql, $bindings] = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Ambil satu record pertama dari query builder
     *
     * @return array|null
     */
    public function first(): ?array
    {
        $this->limitValue = 1;
        [$sql, $bindings] = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Hitung jumlah record
     *
     * @return int Jumlah record
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $bindings = $this->bindings;
        $this->resetQuery();

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Hitung total (SUM) kolom tertentu
     *
     * @param string $column Nama kolom
     * @return float Total
     */
    public function sum(string $column): float
    {
        $sql = "SELECT COALESCE(SUM({$column}), 0) as total FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $bindings = $this->bindings;
        $this->resetQuery();

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return (float) $stmt->fetch()['total'];
    }

    /**
     * Buat record baru
     *
     * @param array $data Data untuk diinsert ['kolom' => 'nilai']
     * @return int|false ID record baru atau false jika gagal
     */
    public function create(array $data): int|false
    {
        // Filter hanya kolom yang ada di fillable
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        if (empty($data)) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_values($data));

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Update record berdasarkan primary key
     *
     * @param int|string $id Nilai primary key
     * @param array $data Data yang diupdate
     * @return bool Berhasil atau tidak
     */
    public function update(int|string $id, array $data): bool
    {
        // Filter hanya kolom yang ada di fillable
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        if (empty($data)) {
            return false;
        }

        $setClause = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";

        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Update record berdasarkan kondisi WHERE yang dikumpulkan
     *
     * @param array $data Data yang diupdate
     * @return int Jumlah baris yang terpengaruh
     */
    public function updateWhere(array $data): int
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $setClause = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$setClause}";

        $bindings = array_values($data);

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
            $bindings = array_merge($bindings, $this->bindings);
        }

        $this->resetQuery();

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /**
     * Hapus record berdasarkan primary key
     *
     * @param int|string $id Nilai primary key
     * @return bool Berhasil atau tidak
     */
    public function delete(int|string $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Soft delete (set kolom deleted_at)
     *
     * @param int|string $id Nilai primary key
     * @return bool
     */
    public function softDelete(int|string $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }

    // ========================================
    // Pagination
    // ========================================

    /**
     * Paginasi hasil query
     *
     * @param int $page Halaman saat ini (mulai dari 1)
     * @param int $perPage Jumlah per halaman
     * @return array ['data' => [], 'pagination' => [...]]
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);

        // Hitung total record (duplikat kondisi WHERE)
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($this->wheres)) {
            $countSql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($this->bindings);
        $total = (int) $countStmt->fetch()['total'];

        // Hitung total halaman
        $totalPages = (int) ceil($total / $perPage);
        $page = min($page, max(1, $totalPages));

        // Set limit dan offset
        $this->limitValue = $perPage;
        $this->offsetValue = ($page - 1) * $perPage;

        // Ambil data
        [$sql, $bindings] = $this->buildSelectQuery();
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'total_pages'  => $totalPages,
                'has_prev'     => $page > 1,
                'has_next'     => $page < $totalPages,
                'from'         => $total > 0 ? ($page - 1) * $perPage + 1 : 0,
                'to'           => min($page * $perPage, $total),
            ],
        ];
    }

    // ========================================
    // Transaksi Database
    // ========================================

    /**
     * Mulai transaksi database
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit transaksi
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback transaksi
     */
    public function rollBack(): bool
    {
        return $this->db->rollBack();
    }

    // ========================================
    // Raw Query
    // ========================================

    /**
     * Eksekusi raw query dengan prepared statement
     *
     * @param string $sql Query SQL
     * @param array $params Parameter binding
     * @return PDOStatement
     */
    public function raw(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Eksekusi raw SELECT dan kembalikan semua baris
     *
     * @param string $sql Query SQL
     * @param array $params Parameter binding
     * @return array
     */
    public function rawSelect(string $sql, array $params = []): array
    {
        return $this->raw($sql, $params)->fetchAll();
    }

    /**
     * Eksekusi raw SELECT dan kembalikan baris pertama
     *
     * @param string $sql Query SQL
     * @param array $params Parameter binding
     * @return array|null
     */
    public function rawFirst(string $sql, array $params = []): ?array
    {
        $result = $this->raw($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Cek apakah record dengan kondisi tertentu ada
     *
     * @param string $column Nama kolom
     * @param mixed $value Nilai
     * @param int|null $excludeId ID yang dikecualikan (untuk update/unique check)
     * @return bool
     */
    public function exists(string $column, mixed $value, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = ?";
        $params = [$value];

        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'] > 0;
    }
}
