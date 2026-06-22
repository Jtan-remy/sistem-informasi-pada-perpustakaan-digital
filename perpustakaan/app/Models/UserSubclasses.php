<?php
// app/Models/UserSubclasses.php — Client, Staff, Admin (extends User)

namespace App\Models;

require_once __DIR__ . '/User.php';

// ═══════════════════════════════════════════════════════════════════════════
// Class Client — extends User (Inheritance #1)
// ═══════════════════════════════════════════════════════════════════════════
class Client extends User
{
    private string $nomorAnggota;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->role          = 'client';
        $this->nomorAnggota  = $data['nomor_anggota'] ?? 'MA-' . strtoupper(substr(md5($data['id'] ?? ''), 0, 6));
    }

    public function getNomorAnggota(): string { return $this->nomorAnggota; }

    // Polymorphism — override getDashboardData()
    public function getDashboardData(): array
    {
        $userId    = $_SESSION['auth']['id'] ?? '';
        $semua     = $_SESSION['peminjaman'] ?? [];
        $dipinjam  = 0;
        $menunggu  = 0;
        $selesai   = 0;
        $notifBelum = 0;

        foreach ($semua as $p) {
            if ($p['user_id'] !== $userId) continue;
            match ($p['status']) {
                'dipinjam'     => $dipinjam++,
                'menunggu'     => $menunggu++,
                'dikembalikan' => $selesai++,
                default        => null,
            };
        }

        $notifs = $_SESSION['notifikasi'] ?? [];
        foreach ($notifs as $n) {
            if ($n['user_id'] === $userId && !$n['dibaca']) $notifBelum++;
        }

        return [
            'dipinjam'    => $dipinjam,
            'menunggu'    => $menunggu,
            'selesai'     => $selesai,
            'notif_baru'  => $notifBelum,
        ];
    }

    // Polymorphism — override getMenuItems()
    public function getMenuItems(): array
    {
        return [
            ['icon' => 'bi-house-door',     'label' => 'Dashboard',  'url' => 'index.php?page=client/dashboard'],
            ['icon' => 'bi-book',           'label' => 'Katalog Buku','url' => 'index.php?page=client/katalog'],
            ['icon' => 'bi-bookmark-plus',  'label' => 'Reservasi',  'url' => 'index.php?page=client/reservasi'],
            ['icon' => 'bi-clock-history',  'label' => 'Riwayat',    'url' => 'index.php?page=client/riwayat'],
            ['icon' => 'bi-person-circle',  'label' => 'Profil',     'url' => 'index.php?page=client/profil'],
        ];
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), ['nomor_anggota' => $this->nomorAnggota]);
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Class Staff — extends User (Inheritance #2)
// ═══════════════════════════════════════════════════════════════════════════
class Staff extends User
{
    private string $jabatan;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->role    = 'staff';
        $this->jabatan = $data['jabatan'] ?? 'Staf Perpustakaan';
    }

    public function getJabatan(): string { return $this->jabatan; }

    // Polymorphism — override getDashboardData()
    public function getDashboardData(): array
    {
        $buku       = $_SESSION['buku']       ?? [];
        $pinjam     = $_SESSION['peminjaman'] ?? [];

        $totalBuku  = count($buku);
        $stokHabis  = 0;
        $aktif      = 0;
        $menunggu   = 0;

        foreach ($buku as $b) {
            if ((int)$b['stok'] === 0) $stokHabis++;
        }
        foreach ($pinjam as $p) {
            if ($p['status'] === 'dipinjam')  $aktif++;
            if ($p['status'] === 'menunggu')  $menunggu++;
        }

        return [
            'total_buku'  => $totalBuku,
            'stok_habis'  => $stokHabis,
            'dipinjam'    => $aktif,
            'menunggu'    => $menunggu,
        ];
    }

    // Polymorphism — override getMenuItems()
    public function getMenuItems(): array
    {
        return [
            ['icon' => 'bi-house-door',       'label' => 'Dashboard',   'url' => 'index.php?page=staff/dashboard'],
            ['icon' => 'bi-journal-bookmarks', 'label' => 'Kelola Buku', 'url' => 'index.php?page=staff/kelola-buku'],
            ['icon' => 'bi-arrow-left-right',  'label' => 'Peminjaman',  'url' => 'index.php?page=staff/peminjaman'],
            ['icon' => 'bi-file-earmark-text', 'label' => 'Laporan',     'url' => 'index.php?page=staff/laporan'],
        ];
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Class Admin — extends User (Inheritance #3)
// ═══════════════════════════════════════════════════════════════════════════
class Admin extends User
{
    private string $level;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->role  = 'admin';
        $this->level = $data['level'] ?? 'super';
    }

    public function getLevel(): string { return $this->level; }

    // Polymorphism — override getDashboardData()
    public function getDashboardData(): array
    {
        $users    = $_SESSION['users']      ?? [];
        $buku     = $_SESSION['buku']       ?? [];
        $pinjam   = $_SESSION['peminjaman'] ?? [];

        $totalUser    = 0;
        $userPending  = 0;
        $totalPinjam  = 0;
        $bulanIni     = date('Y-m');

        foreach ($users as $u) {
            if ($u['role'] === 'client') {
                $totalUser++;
                if ($u['status'] === 'pending') $userPending++;
            }
        }
        foreach ($pinjam as $p) {
            if ($p['status'] === 'dipinjam') $totalPinjam++;
        }

        // Data chart — peminjaman per kategori buku
        $chartData = [];
        foreach ($pinjam as $p) {
            $bid = $p['buku_id'];
            if (isset($buku[$bid])) {
                $kat = $buku[$bid]['kategori'];
                $chartData[$kat] = ($chartData[$kat] ?? 0) + 1;
            }
        }

        return [
            'total_user'   => $totalUser,
            'user_pending' => $userPending,
            'total_buku'   => count($buku),
            'dipinjam'     => $totalPinjam,
            'chart_data'   => $chartData,
        ];
    }

    // Polymorphism — override getMenuItems()
    public function getMenuItems(): array
    {
        return [
            ['icon' => 'bi-speedometer2',    'label' => 'Dashboard',    'url' => 'index.php?page=admin/dashboard'],
            ['icon' => 'bi-person-check',    'label' => 'Verifikasi',   'url' => 'index.php?page=admin/verifikasi'],
            ['icon' => 'bi-people',          'label' => 'Kelola User',  'url' => 'index.php?page=admin/kelola-user'],
            ['icon' => 'bi-graph-up',        'label' => 'Statistik',    'url' => 'index.php?page=admin/statistik'],
        ];
    }
}
