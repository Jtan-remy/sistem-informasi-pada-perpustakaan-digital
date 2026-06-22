<?php
// app/Models/Peminjaman.php — Model transaksi peminjaman

namespace App\Models;

require_once __DIR__ . '/User.php';

class Peminjaman implements Manageable
{
    private string  $id;
    private string  $userId;
    private string  $bukuId;
    private string  $tglPinjam;
    private string  $tglKembali;
    private ?string $tglAktual;
    private string  $status;    // menunggu | dipinjam | dikembalikan | ditolak
    private string  $catatan;
    private string  $created;

    public static array $STATUS_LABEL = [
        'menunggu'     => ['label' => 'Menunggu',    'badge' => 'warning'],
        'dipinjam'     => ['label' => 'Dipinjam',    'badge' => 'primary'],
        'dikembalikan' => ['label' => 'Dikembalikan','badge' => 'success'],
        'ditolak'      => ['label' => 'Ditolak',     'badge' => 'danger'],
    ];

    public function __construct(array $data = [])
    {
        $this->id         = $data['id']          ?? '';
        $this->userId     = $data['user_id']     ?? '';
        $this->bukuId     = $data['buku_id']     ?? '';
        $this->tglPinjam  = $data['tgl_pinjam']  ?? date('Y-m-d');
        $this->tglKembali = $data['tgl_kembali'] ?? date('Y-m-d', strtotime('+14 days'));
        $this->tglAktual  = $data['tgl_aktual']  ?? null;
        $this->status     = $data['status']      ?? 'menunggu';
        $this->catatan    = $data['catatan']      ?? '';
        $this->created    = $data['created']      ?? date('Y-m-d');
    }

    // ── Getters ──────────────────────────────────────────────────────────
    public function getId(): string         { return $this->id; }
    public function getUserId(): string     { return $this->userId; }
    public function getBukuId(): string     { return $this->bukuId; }
    public function getTglPinjam(): string  { return $this->tglPinjam; }
    public function getTglKembali(): string { return $this->tglKembali; }
    public function getTglAktual(): ?string { return $this->tglAktual; }
    public function getStatus(): string     { return $this->status; }
    public function getCatatan(): string    { return $this->catatan; }

    // ── Static: generate ID ──────────────────────────────────────────────
    public static function generateId(): string
    {
        $existing = $_SESSION['peminjaman'] ?? [];
        return User::generateId('P', $existing);
    }

    // ── Manageable ───────────────────────────────────────────────────────
    public function getAll(): array
    {
        return $_SESSION['peminjaman'] ?? [];
    }

    public function findById(string $id): ?array
    {
        return $_SESSION['peminjaman'][$id] ?? null;
    }

    public function save(array $data): bool
    {
        try {
            $id = $data['id'] ?? self::generateId();
            $_SESSION['peminjaman'][$id] = array_merge($data, ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(string $id): bool
    {
        if (isset($_SESSION['peminjaman'][$id])) {
            unset($_SESSION['peminjaman'][$id]);
            return true;
        }
        return false;
    }

    // ── Filter per user ──────────────────────────────────────────────────
    public function getByUser(string $userId, string $status = ''): array
    {
        $semua  = $_SESSION['peminjaman'] ?? [];
        $hasil  = [];
        foreach ($semua as $p) {
            if ($p['user_id'] !== $userId) continue;
            if (!empty($status) && $p['status'] !== $status) continue;
            $hasil[] = $p;
        }
        // Sort: terbaru dulu
        usort($hasil, fn($a, $b) => strcmp($b['created'], $a['created']));
        return $hasil;
    }

    // ── Cek apakah buku sedang dipinjam user ─────────────────────────────
    public function isSedangDipinjam(string $userId, string $bukuId): bool
    {
        $semua = $_SESSION['peminjaman'] ?? [];
        foreach ($semua as $p) {
            if ($p['user_id'] === $userId
                && $p['buku_id'] === $bukuId
                && in_array($p['status'], ['menunggu', 'dipinjam'], true)) {
                return true;
            }
        }
        return false;
    }

    // ── Update status ────────────────────────────────────────────────────
    public function updateStatus(string $id, string $status): bool
    {
        if (!isset($_SESSION['peminjaman'][$id])) return false;
        $_SESSION['peminjaman'][$id]['status'] = $status;
        if ($status === 'dikembalikan') {
            $_SESSION['peminjaman'][$id]['tgl_aktual'] = date('Y-m-d');
        }
        return true;
    }

    // ── Cek keterlambatan ────────────────────────────────────────────────
    public function isTerlambat(array $pinjam): bool
    {
        if ($pinjam['status'] !== 'dipinjam') return false;
        return strtotime($pinjam['tgl_kembali']) < strtotime(date('Y-m-d'));
    }

    public function getSelisihHari(array $pinjam): int
    {
        $tenggat = strtotime($pinjam['tgl_kembali']);
        $sekarang = strtotime(date('Y-m-d'));
        return (int)(($tenggat - $sekarang) / 86400);
    }

    // ── Statistik bulanan ────────────────────────────────────────────────
    public static function getStatistikBulanan(): array
    {
        $semua = $_SESSION['peminjaman'] ?? [];
        $stat  = [];
        foreach ($semua as $p) {
            $bulan = date('M Y', strtotime($p['tgl_pinjam']));
            $stat[$bulan] = ($stat[$bulan] ?? 0) + 1;
        }
        return $stat;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->userId,
            'buku_id'     => $this->bukuId,
            'tgl_pinjam'  => $this->tglPinjam,
            'tgl_kembali' => $this->tglKembali,
            'tgl_aktual'  => $this->tglAktual,
            'status'      => $this->status,
            'catatan'     => $this->catatan,
            'created'     => $this->created,
        ];
    }
}
