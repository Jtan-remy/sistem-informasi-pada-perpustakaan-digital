<?php
// app/Models/Helpers.php — Notifikasi, SessionManager, Validator, Pagination, FileUpload

namespace App\Models;

// ═══════════════════════════════════════════════════════════════════════════
// Notifikasi
// ═══════════════════════════════════════════════════════════════════════════
class Notifikasi
{
    private string $id;
    private string $userId;
    private string $pesan;
    private string $tipe;    // info | tenggat | peringatan
    private bool   $dibaca;
    private string $created;

    public function __construct(array $data = [])
    {
        $this->id      = $data['id']      ?? '';
        $this->userId  = $data['user_id'] ?? '';
        $this->pesan   = $data['pesan']   ?? '';
        $this->tipe    = $data['tipe']    ?? 'info';
        $this->dibaca  = (bool)($data['dibaca'] ?? false);
        $this->created = $data['created'] ?? date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): string    { return $this->id; }
    public function getUserId(): string{ return $this->userId; }
    public function getPesan(): string { return $this->pesan; }
    public function getTipe(): string  { return $this->tipe; }
    public function isDibaca(): bool   { return $this->dibaca; }

    // Static: tambah notifikasi baru
    public static function tambah(string $userId, string $pesan, string $tipe = 'info'): void
    {
        $existing = $_SESSION['notifikasi'] ?? [];
        $id       = 'N' . str_pad(count($existing) + 1, 3, '0', STR_PAD_LEFT);
        $_SESSION['notifikasi'][$id] = [
            'id'      => $id,
            'user_id' => $userId,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'dibaca'  => false,
            'created' => date('Y-m-d H:i:s'),
        ];
    }

    // Ambil notifikasi milik user
    public static function getByUser(string $userId): array
    {
        $semua = $_SESSION['notifikasi'] ?? [];
        $hasil = [];
        foreach ($semua as $n) {
            if ($n['user_id'] === $userId) $hasil[] = $n;
        }
        usort($hasil, fn($a, $b) => strcmp($b['created'], $a['created']));
        return $hasil;
    }

    // Hitung belum dibaca
    public static function countUnread(string $userId): int
    {
        $semua = $_SESSION['notifikasi'] ?? [];
        $count = 0;
        foreach ($semua as $n) {
            if ($n['user_id'] === $userId && !$n['dibaca']) $count++;
        }
        return $count;
    }

    // Tandai dibaca
    public static function tandaiDibaca(string $userId): void
    {
        foreach ($_SESSION['notifikasi'] ?? [] as $id => $n) {
            if ($n['user_id'] === $userId) {
                $_SESSION['notifikasi'][$id]['dibaca'] = true;
            }
        }
    }

    // Cek peminjaman mendekati tenggat & buat notif otomatis
    public static function cekTenggat(): void
    {
        $pinjam = $_SESSION['peminjaman'] ?? [];
        $today  = strtotime(date('Y-m-d'));

        foreach ($pinjam as $p) {
            if ($p['status'] !== 'dipinjam') continue;

            $selisih = (int)(( strtotime($p['tgl_kembali']) - $today ) / 86400);
            $buku    = $_SESSION['buku'][$p['buku_id']] ?? null;
            if (!$buku) continue;

            $judul   = $buku['judul'];
            $kunciNotif = 'notif_cek_' . $p['id'];

            // Notif 3 hari sebelum (hanya sekali per pinjaman)
            if ($selisih <= 3 && $selisih >= 0 && !isset($_SESSION[$kunciNotif])) {
                self::tambah(
                    $p['user_id'],
                    "Pengingat: Buku \"{$judul}\" harus dikembalikan dalam {$selisih} hari.",
                    'tenggat'
                );
                $_SESSION[$kunciNotif] = true;
            }

            // Notif terlambat
            $kunciTlbt = 'notif_tlbt_' . $p['id'];
            if ($selisih < 0 && !isset($_SESSION[$kunciTlbt])) {
                $hari = abs($selisih);
                self::tambah(
                    $p['user_id'],
                    "Terlambat {$hari} hari! Segera kembalikan buku \"{$judul}\".",
                    'peringatan'
                );
                $_SESSION[$kunciTlbt] = true;
            }
        }
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// SessionManager — all static (Static Method requirement)
// ═══════════════════════════════════════════════════════════════════════════
class SessionManager
{
    public function __construct() {} // constructor wajib

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function getAuth(): ?array
    {
        return $_SESSION['auth'] ?? null;
    }

    public static function isAuth(): bool
    {
        return isset($_SESSION['auth']);
    }

    public static function getRole(): string
    {
        return $_SESSION['auth']['role'] ?? '';
    }

    public static function flash(string $type, string $msg): void
    {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    public static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Validator — static (Static Method #2)
// ═══════════════════════════════════════════════════════════════════════════
class Validator
{
    private array $errors = [];

    public function __construct() {}

    // Static method utama
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleStr) {
            $value = trim($data[$field] ?? '');
            $rules_list = explode('|', $ruleStr);

            foreach ($rules_list as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' wajib diisi.';
                    break;
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " minimal {$min} karakter.";
                        break;
                    }
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " maksimal {$max} karakter.";
                        break;
                    }
                }
                if ($rule === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Format email tidak valid.';
                    break;
                }
                if ($rule === 'numeric' && $value !== '' && !is_numeric($value)) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' harus berupa angka.';
                    break;
                }
            }
        }
        return $errors;
    }

    // Static: cek email unik
    public static function isEmailUnique(string $email, string $exceptId = ''): bool
    {
        $users = $_SESSION['users'] ?? [];
        foreach ($users as $u) {
            if ($u['email'] === $email && $u['id'] !== $exceptId) return false;
        }
        return true;
    }

    // Static: sanitize input
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Pagination — static (Static Method #3)
// ═══════════════════════════════════════════════════════════════════════════
class Pagination
{
    private int $totalItem;
    private int $perPage;
    private int $currentPage;
    private int $totalPage;

    public function __construct(int $totalItem, int $perPage = 6, int $currentPage = 1)
    {
        $this->totalItem   = $totalItem;
        $this->perPage     = $perPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPage   = (int) ceil($totalItem / $perPage);
    }

    // Static: slice data array sesuai halaman
    public static function paginate(array $data, int $perPage, int $page): array
    {
        $offset = ($page - 1) * $perPage;
        return array_slice(array_values($data), $offset, $perPage);
    }

    public function getTotalPage(): int   { return $this->totalPage; }
    public function getCurrentPage(): int { return $this->currentPage; }
    public function getTotalItem(): int   { return $this->totalItem; }

    public function hasPrev(): bool { return $this->currentPage > 1; }
    public function hasNext(): bool { return $this->currentPage < $this->totalPage; }

    public function getPageRange(): array
    {
        $start = max(1, $this->currentPage - 2);
        $end   = min($this->totalPage, $this->currentPage + 2);
        return range($start, $end);
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// FileUpload — static (Static Method #4)
// ═══════════════════════════════════════════════════════════════════════════
class FileUpload
{
    private static array $ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function __construct() {}

    // Static method upload gambar
    public static function upload(array $file, string $prefix = 'cover'): array
    {
        // Exception Handling (requirement)
        try {
            if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
                throw new \RuntimeException('File tidak valid atau upload gagal.');
            }
            if ($file['size'] > MAX_UPLOAD) {
                throw new \RuntimeException('Ukuran file maksimal 2MB.');
            }
            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, self::$ALLOWED, true)) {
                throw new \RuntimeException('Format file harus JPG, PNG, WEBP, atau GIF.');
            }

            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . strtolower($ext);
            $dest     = UPLOAD_DIR . $filename;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                throw new \RuntimeException('Gagal menyimpan file ke server.');
            }

            return ['success' => true, 'filename' => $filename];

        } catch (\RuntimeException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function delete(string $filename): void
    {
        $path = UPLOAD_DIR . $filename;
        if ($filename && file_exists($path)) {
            unlink($path);
        }
    }
}
