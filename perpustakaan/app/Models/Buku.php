<?php
// app/Models/Buku.php — Model data buku

namespace App\Models;

require_once __DIR__ . '/User.php'; // untuk interface Manageable

class Buku implements Manageable
{
    // Encapsulation: properti private
    private string $id;
    private string $judul;
    private string $penulis;
    private string $penerbit;
    private string $tahun;
    private string $isbn;
    private string $kategori;
    private int    $stok;
    private string $deskripsi;
    private string $cover;
    private string $created;

    // Daftar kategori yang tersedia (static)
    public static array $KATEGORI = [
        'Teknologi', 'Novel', 'Sejarah', 'Bisnis',
        'Pengembangan Diri', 'Sains', 'Agama', 'Lainnya'
    ];

    public function __construct(array $data = [])
    {
        $this->id        = $data['id']        ?? '';
        $this->judul     = $data['judul']     ?? '';
        $this->penulis   = $data['penulis']   ?? '';
        $this->penerbit  = $data['penerbit']  ?? '';
        $this->tahun     = $data['tahun']     ?? '';
        $this->isbn      = $data['isbn']      ?? '';
        $this->kategori  = $data['kategori']  ?? '';
        $this->stok      = (int)($data['stok'] ?? 0);
        $this->deskripsi = $data['deskripsi'] ?? '';
        $this->cover     = $data['cover']     ?? '';
        $this->created   = $data['created']   ?? date('Y-m-d');
    }

    // ── Getters ──────────────────────────────────────────────────────────
    public function getId(): string       { return $this->id; }
    public function getJudul(): string    { return $this->judul; }
    public function getPenulis(): string  { return $this->penulis; }
    public function getPenerbit(): string { return $this->penerbit; }
    public function getTahun(): string    { return $this->tahun; }
    public function getIsbn(): string     { return $this->isbn; }
    public function getKategori(): string { return $this->kategori; }
    public function getStok(): int        { return $this->stok; }
    public function getDeskripsi(): string{ return $this->deskripsi; }
    public function getCover(): string    { return $this->cover; }

    // ── Static method: generate ID buku ─────────────────────────────────
    public static function generateId(): string
    {
        $existing = $_SESSION['buku'] ?? [];
        return User::generateId('B', $existing);
    }

    // ── Manageable implementation ────────────────────────────────────────
    public function getAll(): array
    {
        return $_SESSION['buku'] ?? [];
    }

    public function findById(string $id): ?array
    {
        return $_SESSION['buku'][$id] ?? null;
    }

    public function save(array $data): bool
    {
        try {
            $id = $data['id'] ?? self::generateId();
            $_SESSION['buku'][$id] = array_merge($data, ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(string $id): bool
    {
        if (isset($_SESSION['buku'][$id])) {
            unset($_SESSION['buku'][$id]);
            return true;
        }
        return false;
    }

    // ── Pencarian & filter ───────────────────────────────────────────────
    public function search(string $query, string $kategori = '', string $sort = 'judul'): array
    {
        $buku  = $_SESSION['buku'] ?? [];
        $query = strtolower(trim($query));
        $hasil = [];

        foreach ($buku as $b) {
            $cocok = empty($query)
                || str_contains(strtolower($b['judul']), $query)
                || str_contains(strtolower($b['penulis']), $query)
                || str_contains(strtolower($b['isbn']), $query);

            if (!$cocok) continue;
            if (!empty($kategori) && $b['kategori'] !== $kategori) continue;

            $hasil[] = $b;
        }

        // Sorting
        usort($hasil, function($a, $b) use ($sort) {
            return match($sort) {
                'penulis' => strcmp($a['penulis'], $b['penulis']),
                'tahun'   => strcmp($b['tahun'], $a['tahun']),    // terbaru dulu
                'stok'    => $b['stok'] - $a['stok'],
                default   => strcmp($a['judul'], $b['judul']),
            };
        });

        return $hasil;
    }

    // ── Kurangi/tambah stok ──────────────────────────────────────────────
    public function kurangiStok(string $id): bool
    {
        if (isset($_SESSION['buku'][$id]) && $_SESSION['buku'][$id]['stok'] > 0) {
            $_SESSION['buku'][$id]['stok']--;
            return true;
        }
        return false;
    }

    public function tambahStok(string $id): bool
    {
        if (isset($_SESSION['buku'][$id])) {
            $_SESSION['buku'][$id]['stok']++;
            return true;
        }
        return false;
    }

    // ── Statistik ────────────────────────────────────────────────────────
    public static function getStatistikKategori(): array
    {
        $buku = $_SESSION['buku'] ?? [];
        $stat = [];
        foreach ($buku as $b) {
            $stat[$b['kategori']] = ($stat[$b['kategori']] ?? 0) + 1;
        }
        arsort($stat);
        return $stat;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'judul'     => $this->judul,
            'penulis'   => $this->penulis,
            'penerbit'  => $this->penerbit,
            'tahun'     => $this->tahun,
            'isbn'      => $this->isbn,
            'kategori'  => $this->kategori,
            'stok'      => $this->stok,
            'deskripsi' => $this->deskripsi,
            'cover'     => $this->cover,
            'created'   => $this->created,
        ];
    }
}
