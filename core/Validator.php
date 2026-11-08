<?php
/**
 * TanamPohon.id - Validator Input
 * 
 * Validasi data input dari form dengan berbagai aturan:
 * required, email, min, max, numeric, in, unique, file, confirmed,
 * phone (format Indonesia), dsb.
 */

class Validator
{
    /** @var array Data yang akan divalidasi */
    private array $data;

    /** @var array Aturan validasi */
    private array $rules;

    /** @var array Pesan error kustom */
    private array $customMessages;

    /** @var array Daftar error yang ditemukan */
    private array $errors = [];

    /** @var array Data yang lolos validasi */
    private array $validatedData = [];

    /** @var array Label nama field dalam Bahasa Indonesia */
    private array $fieldLabels = [
        'nama_lengkap'          => 'Nama Lengkap',
        'email'                 => 'Email',
        'password'              => 'Kata Sandi',
        'password_confirmation' => 'Konfirmasi Kata Sandi',
        'no_hp'                 => 'Nomor HP',
        'alamat'                => 'Alamat',
        'kota'                  => 'Kota',
        'provinsi'              => 'Provinsi',
        'kode_pos'              => 'Kode Pos',
        'jumlah'                => 'Jumlah',
        'nominal'               => 'Nominal',
        'bukti_bayar'           => 'Bukti Pembayaran',
        'pesan'                 => 'Pesan',
        'judul'                 => 'Judul',
        'deskripsi'             => 'Deskripsi',
        'harga'                 => 'Harga',
        'stok'                  => 'Stok',
        'berat'                 => 'Berat',
        'kategori_id'           => 'Kategori',
        'gambar'                => 'Gambar',
        'avatar'                => 'Foto Profil',
        'tanggal'               => 'Tanggal',
        'waktu'                 => 'Waktu',
        'nama'                  => 'Nama',
        'subjek'                => 'Subjek',
    ];

    /**
     * Konstruktor - jalankan validasi otomatis
     *
     * @param array $data Data input yang akan divalidasi
     * @param array $rules Aturan validasi ['field' => 'required|email|min:6']
     * @param array $customMessages Pesan error kustom (opsional)
     */
    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;

        $this->run();
    }

    /**
     * Jalankan semua validasi
     */
    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;
            $isRequired = in_array('required', $rules);
            $isNullable = in_array('nullable', $rules);

            // Jika nullable dan value kosong/null, lewati validasi
            if ($isNullable && ($value === null || $value === '')) {
                continue;
            }

            foreach ($rules as $rule) {
                // Lewati modifier-only rules
                if (in_array($rule, ['nullable'])) {
                    continue;
                }

                // Parse rule dan parameter (contoh: min:6, in:aktif,nonaktif)
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramString] = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                }

                // Jalankan validasi sesuai aturan
                $methodName = 'validate' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $params);
                }
            }

            // Jika field lolos validasi (tidak ada error), simpan
            if (!isset($this->errors[$field])) {
                $this->validatedData[$field] = $value;
            }
        }
    }

    /**
     * Cek apakah validasi gagal
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Cek apakah validasi berhasil
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Ambil semua error
     *
     * @return array Errors ['field' => ['pesan error 1', 'pesan error 2']]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Ambil error pertama untuk field tertentu
     *
     * @param string $field Nama field
     * @return string|null Pesan error pertama
     */
    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Ambil data yang sudah lolos validasi
     *
     * @return array Data tervalidasi
     */
    public function validated(): array
    {
        return $this->validatedData;
    }

    /**
     * Tambahkan error ke daftar
     *
     * @param string $field Nama field
     * @param string $rule Nama aturan
     * @param string $defaultMessage Pesan default
     */
    private function addError(string $field, string $rule, string $defaultMessage): void
    {
        // Cek apakah ada pesan kustom
        $customKey = "{$field}.{$rule}";
        $message = $this->customMessages[$customKey] ?? $defaultMessage;

        $this->errors[$field][] = $message;
    }

    /**
     * Dapatkan label Bahasa Indonesia untuk field
     */
    private function getLabel(string $field): string
    {
        return $this->fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    // ========================================
    // Aturan Validasi
    // ========================================

    /**
     * Field wajib diisi
     */
    private function validateRequired(string $field, mixed $value, array $params): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $label = $this->getLabel($field);
            $this->addError($field, 'required', "{$label} wajib diisi.");
        }
    }

    /**
     * Harus format email yang valid
     */
    private function validateEmail(string $field, mixed $value, array $params): void
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'email', "{$label} harus berupa alamat email yang valid.");
        }
    }

    /**
     * Panjang minimal (string) atau nilai minimal (angka)
     */
    private function validateMin(string $field, mixed $value, array $params): void
    {
        $min = (int) ($params[0] ?? 0);

        if (is_string($value) && mb_strlen($value) < $min) {
            $label = $this->getLabel($field);
            $this->addError($field, 'min', "{$label} minimal {$min} karakter.");
        } elseif (is_numeric($value) && $value < $min) {
            $label = $this->getLabel($field);
            $this->addError($field, 'min', "{$label} minimal bernilai {$min}.");
        }
    }

    /**
     * Panjang maksimal (string) atau nilai maksimal (angka)
     */
    private function validateMax(string $field, mixed $value, array $params): void
    {
        $max = (int) ($params[0] ?? 255);

        if (is_string($value) && mb_strlen($value) > $max) {
            $label = $this->getLabel($field);
            $this->addError($field, 'max', "{$label} maksimal {$max} karakter.");
        } elseif (is_numeric($value) && $value > $max) {
            $label = $this->getLabel($field);
            $this->addError($field, 'max', "{$label} maksimal bernilai {$max}.");
        }
    }

    /**
     * Harus berupa angka
     */
    private function validateNumeric(string $field, mixed $value, array $params): void
    {
        if ($value && !is_numeric($value)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'numeric', "{$label} harus berupa angka.");
        }
    }

    /**
     * Harus berupa bilangan bulat
     */
    private function validateInteger(string $field, mixed $value, array $params): void
    {
        if ($value && !ctype_digit((string) $value)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'integer', "{$label} harus berupa bilangan bulat.");
        }
    }

    /**
     * Harus bernilai salah satu dari daftar
     * Contoh: in:aktif,nonaktif,pending
     */
    private function validateIn(string $field, mixed $value, array $params): void
    {
        if ($value && !in_array($value, $params)) {
            $label = $this->getLabel($field);
            $allowed = implode(', ', $params);
            $this->addError($field, 'in', "{$label} harus salah satu dari: {$allowed}.");
        }
    }

    /**
     * Harus unik di tabel database
     * Format: unique:nama_tabel,nama_kolom atau unique:nama_tabel,nama_kolom,exclude_id
     */
    private function validateUnique(string $field, mixed $value, array $params): void
    {
        if (!$value) return;

        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $excludeId = $params[2] ?? null;

        if (!$table) return;

        try {
            $db = Database::getInstance()->getConnection();

            $sql = "SELECT COUNT(*) as total FROM {$table} WHERE {$column} = ?";
            $bindings = [$value];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $bindings[] = $excludeId;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($bindings);
            $count = (int) $stmt->fetch()['total'];

            if ($count > 0) {
                $label = $this->getLabel($field);
                $this->addError($field, 'unique', "{$label} sudah terdaftar.");
            }
        } catch (\PDOException $e) {
            // Jika tabel belum ada, lewati validasi unique
            error_log("Validasi unique gagal: " . $e->getMessage());
        }
    }

    /**
     * Harus dikonfirmasi (ada field _confirmation yang cocok)
     * Contoh: password + password_confirmation
     */
    private function validateConfirmed(string $field, mixed $value, array $params): void
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;

        if ($value !== $confirmValue) {
            $label = $this->getLabel($field);
            $this->addError($field, 'confirmed', "Konfirmasi {$label} tidak cocok.");
        }
    }

    /**
     * Harus sama dengan field lain
     * Contoh: same:password
     */
    private function validateSame(string $field, mixed $value, array $params): void
    {
        $otherField = $params[0] ?? '';
        $otherValue = $this->data[$otherField] ?? null;

        if ($value !== $otherValue) {
            $label = $this->getLabel($field);
            $otherLabel = $this->getLabel($otherField);
            $this->addError($field, 'same', "{$label} harus sama dengan {$otherLabel}.");
        }
    }

    /**
     * Validasi format nomor HP Indonesia
     * Format yang diterima: 08xx, +628xx, 628xx
     */
    private function validatePhone(string $field, mixed $value, array $params): void
    {
        if (!$value) return;

        // Bersihkan karakter non-digit kecuali +
        $clean = preg_replace('/[^\d+]/', '', $value);

        // Pattern nomor HP Indonesia
        $pattern = '/^(\+62|62|0)8[1-9][0-9]{7,11}$/';

        if (!preg_match($pattern, $clean)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'phone', "{$label} harus berupa nomor HP Indonesia yang valid (contoh: 081234567890).");
        }
    }

    /**
     * Harus berupa URL yang valid
     */
    private function validateUrl(string $field, mixed $value, array $params): void
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'url', "{$label} harus berupa URL yang valid.");
        }
    }

    /**
     * Harus berupa tanggal yang valid
     */
    private function validateDate(string $field, mixed $value, array $params): void
    {
        if (!$value) return;

        $format = $params[0] ?? 'Y-m-d';
        $date = \DateTime::createFromFormat($format, $value);

        if (!$date || $date->format($format) !== $value) {
            $label = $this->getLabel($field);
            $this->addError($field, 'date', "{$label} harus berupa tanggal yang valid.");
        }
    }

    /**
     * Tanggal harus setelah tanggal tertentu
     * Contoh: after:today atau after:2024-01-01
     */
    private function validateAfter(string $field, mixed $value, array $params): void
    {
        if (!$value) return;

        $compareDate = $params[0] ?? 'today';
        if ($compareDate === 'today') {
            $compareDate = date('Y-m-d');
        }

        if (strtotime($value) <= strtotime($compareDate)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'after', "{$label} harus setelah {$compareDate}.");
        }
    }

    /**
     * Harus berupa alfanumerik saja
     */
    private function validateAlpha(string $field, mixed $value, array $params): void
    {
        if ($value && !preg_match('/^[\pL\pN\s]+$/u', $value)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'alpha', "{$label} hanya boleh berisi huruf, angka, dan spasi.");
        }
    }

    /**
     * Validasi file upload - ukuran maksimal
     * Format: file_max:2048 (dalam KB)
     */
    private function validateFile_max(string $field, mixed $value, array $params): void
    {
        $maxKb = (int) ($params[0] ?? 2048);
        $file = $_FILES[$field] ?? null;

        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return;

        $fileSizeKb = $file['size'] / 1024;

        if ($fileSizeKb > $maxKb) {
            $label = $this->getLabel($field);
            $maxMb = round($maxKb / 1024, 1);
            $this->addError($field, 'file_max', "Ukuran {$label} tidak boleh lebih dari {$maxMb} MB.");
        }
    }

    /**
     * Validasi file upload - tipe MIME yang diizinkan
     * Format: file_types:jpg,png,pdf
     */
    private function validateFile_types(string $field, mixed $value, array $params): void
    {
        $file = $_FILES[$field] ?? null;

        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return;

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $params)) {
            $label = $this->getLabel($field);
            $allowed = implode(', ', $params);
            $this->addError($field, 'file_types', "{$label} harus berformat: {$allowed}.");
        }
    }

    /**
     * Validasi file - wajib diupload
     */
    private function validateFile_required(string $field, mixed $value, array $params): void
    {
        $file = $_FILES[$field] ?? null;

        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $file['size'] === 0) {
            $label = $this->getLabel($field);
            $this->addError($field, 'file_required', "{$label} wajib diupload.");
        }
    }

    /**
     * Validasi gambar (cek MIME type gambar)
     */
    private function validateImage(string $field, mixed $value, array $params): void
    {
        $file = $_FILES[$field] ?? null;

        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return;

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'image', "{$label} harus berupa file gambar (JPG, PNG, GIF, WEBP).");
        }
    }

    /**
     * Harus cocok dengan regular expression
     * Contoh: regex:/^[A-Z]{2,3}$/
     */
    private function validateRegex(string $field, mixed $value, array $params): void
    {
        if (!$value) return;

        $pattern = implode(',', $params); // Gabungkan kembali jika ada koma di regex

        if (!preg_match($pattern, $value)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'regex', "Format {$label} tidak valid.");
        }
    }

    /**
     * Panjang tepat
     * Contoh: size:6 (harus tepat 6 karakter)
     */
    private function validateSize(string $field, mixed $value, array $params): void
    {
        $size = (int) ($params[0] ?? 0);

        if (is_string($value) && mb_strlen($value) !== $size) {
            $label = $this->getLabel($field);
            $this->addError($field, 'size', "{$label} harus tepat {$size} karakter.");
        }
    }

    /**
     * Harus berupa boolean (true/false, 1/0, ya/tidak)
     */
    private function validateBoolean(string $field, mixed $value, array $params): void
    {
        $accepted = [true, false, 0, 1, '0', '1', 'true', 'false', 'ya', 'tidak'];

        if ($value !== null && !in_array($value, $accepted, true)) {
            $label = $this->getLabel($field);
            $this->addError($field, 'boolean', "{$label} harus bernilai benar atau salah.");
        }
    }
}
