<?php
// config/session.php — inisialisasi session & data awal (pengganti database)

namespace App\Config;

class SessionData
{
    /**
     * Inisialisasi session dan menyuntikkan data seed jika belum ada.
     * Dipanggil sekali dari index.php saat aplikasi boot.
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Seed data hanya dijalankan sekali per session baru
        if (!isset($_SESSION['__seeded'])) {
            self::seedUsers();
            self::seedBuku();
            self::seedPeminjaman();
            self::seedNotifikasi();
            $_SESSION['__seeded'] = true;
        }
    }

    // ── USERS ──────────────────────────────────────────────────────────
    private static function seedUsers(): void
    {
        $_SESSION['users'] = [
            'U001' => [
                'id'       => 'U001',
                'nama'     => 'Admin Utama',
                'email'    => 'admin@perpus.id',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
                'foto'     => '',
                'telp'     => '081200000001',
                'alamat'   => 'Jl. Perpustakaan No. 1',
                'status'   => 'aktif',
                'created'  => '2024-01-01',
            ],
            'U002' => [
                'id'       => 'U002',
                'nama'     => 'Budi Santoso',
                'email'    => 'staff@perpus.id',
                'password' => password_hash('staff123', PASSWORD_DEFAULT),
                'role'     => 'staff',
                'foto'     => '',
                'telp'     => '081200000002',
                'alamat'   => 'Jl. Melati No. 5',
                'status'   => 'aktif',
                'created'  => '2024-01-05',
            ],
            'U003' => [
                'id'       => 'U003',
                'nama'     => 'Siti Rahma',
                'email'    => 'siti@mail.com',
                'password' => password_hash('siti123', PASSWORD_DEFAULT),
                'role'     => 'client',
                'foto'     => '',
                'telp'     => '081300000003',
                'alamat'   => 'Jl. Mawar No. 12',
                'status'   => 'aktif',
                'created'  => '2024-02-10',
            ],
            'U004' => [
                'id'       => 'U004',
                'nama'     => 'Andi Wijaya',
                'email'    => 'andi@mail.com',
                'password' => password_hash('andi123', PASSWORD_DEFAULT),
                'role'     => 'client',
                'foto'     => '',
                'telp'     => '081400000004',
                'alamat'   => 'Jl. Kenanga No. 8',
                'status'   => 'pending',
                'created'  => '2024-03-15',
            ],
            'U005' => [
                'id'       => 'U005',
                'nama'     => 'Dewi Kusuma',
                'email'    => 'dewi@mail.com',
                'password' => password_hash('dewi123', PASSWORD_DEFAULT),
                'role'     => 'client',
                'foto'     => '',
                'telp'     => '081500000005',
                'alamat'   => 'Jl. Anggrek No. 3',
                'status'   => 'aktif',
                'created'  => '2024-04-20',
            ],
        ];
    }

    // ── BUKU ───────────────────────────────────────────────────────────
    private static function seedBuku(): void
    {
        $_SESSION['buku'] = [
            'B001' => [
                'id'        => 'B001',
                'judul'     => 'Pemrograman PHP Modern',
                'penulis'   => 'Eko Kurniawan',
                'penerbit'  => 'Elex Media',
                'tahun'     => '2023',
                'isbn'      => '978-602-123-001',
                'kategori'  => 'Teknologi',
                'stok'      => 4,
                'deskripsi' => 'Buku panduan lengkap pemrograman PHP dari dasar hingga tingkat lanjut.',
                'cover'     => '',
                'created'   => '2024-01-10',
            ],
            'B002' => [
                'id'        => 'B002',
                'judul'     => 'Belajar Laravel Framework',
                'penulis'   => 'Ahmad Zaini',
                'penerbit'  => 'Jasakom',
                'tahun'     => '2023',
                'isbn'      => '978-602-123-002',
                'kategori'  => 'Teknologi',
                'stok'      => 3,
                'deskripsi' => 'Panduan praktis membangun aplikasi web modern dengan Laravel 10.',
                'cover'     => '',
                'created'   => '2024-01-12',
            ],
            'B003' => [
                'id'        => 'B003',
                'judul'     => 'Algoritma dan Struktur Data',
                'penulis'   => 'Thomas H. Cormen',
                'penerbit'  => 'MIT Press',
                'tahun'     => '2022',
                'isbn'      => '978-026-046-304',
                'kategori'  => 'Teknologi',
                'stok'      => 2,
                'deskripsi' => 'Referensi klasik algoritma dan struktur data untuk ilmu komputer.',
                'cover'     => '',
                'created'   => '2024-01-15',
            ],
            'B004' => [
                'id'        => 'B004',
                'judul'     => 'Laskar Pelangi',
                'penulis'   => 'Andrea Hirata',
                'penerbit'  => 'Bentang Pustaka',
                'tahun'     => '2005',
                'isbn'      => '979-305-025-2',
                'kategori'  => 'Novel',
                'stok'      => 5,
                'deskripsi' => 'Novel inspiratif tentang perjuangan anak-anak Belitung meraih mimpi.',
                'cover'     => '',
                'created'   => '2024-01-18',
            ],
            'B005' => [
                'id'        => 'B005',
                'judul'     => 'Bumi Manusia',
                'penulis'   => 'Pramoedya Ananta Toer',
                'penerbit'  => 'Lentera Dipantara',
                'tahun'     => '1980',
                'isbn'      => '979-300-001-2',
                'kategori'  => 'Novel',
                'stok'      => 3,
                'deskripsi' => 'Tetralogi Buru bagian pertama, kisah Minke di era kolonial.',
                'cover'     => '',
                'created'   => '2024-01-20',
            ],
            'B006' => [
                'id'        => 'B006',
                'judul'     => 'Sapiens: Sejarah Singkat Umat Manusia',
                'penulis'   => 'Yuval Noah Harari',
                'penerbit'  => 'KPG',
                'tahun'     => '2015',
                'isbn'      => '978-602-481-001',
                'kategori'  => 'Sejarah',
                'stok'      => 2,
                'deskripsi' => 'Perjalanan panjang umat manusia dari zaman purba hingga era modern.',
                'cover'     => '',
                'created'   => '2024-02-01',
            ],
            'B007' => [
                'id'        => 'B007',
                'judul'     => 'Rich Dad Poor Dad',
                'penulis'   => 'Robert T. Kiyosaki',
                'penerbit'  => 'Gramedia',
                'tahun'     => '2000',
                'isbn'      => '978-602-012-001',
                'kategori'  => 'Bisnis',
                'stok'      => 4,
                'deskripsi' => 'Pelajaran keuangan yang tak diajarkan di sekolah.',
                'cover'     => '',
                'created'   => '2024-02-05',
            ],
            'B008' => [
                'id'        => 'B008',
                'judul'     => 'Atomic Habits',
                'penulis'   => 'James Clear',
                'penerbit'  => 'Gramedia',
                'tahun'     => '2019',
                'isbn'      => '978-602-012-002',
                'kategori'  => 'Pengembangan Diri',
                'stok'      => 0,
                'deskripsi' => 'Cara membangun kebiasaan baik dan menghilangkan kebiasaan buruk.',
                'cover'     => '',
                'created'   => '2024-02-10',
            ],
        ];
    }

    // ── PEMINJAMAN ─────────────────────────────────────────────────────
    private static function seedPeminjaman(): void
    {
        $_SESSION['peminjaman'] = [
            'P001' => [
                'id'            => 'P001',
                'user_id'       => 'U003',
                'buku_id'       => 'B001',
                'tgl_pinjam'    => '2024-05-01',
                'tgl_kembali'   => '2024-05-15',
                'tgl_aktual'    => null,
                'status'        => 'dipinjam',
                'catatan'       => 'Untuk tugas kuliah',
                'created'       => '2024-05-01',
            ],
            'P002' => [
                'id'            => 'P002',
                'user_id'       => 'U003',
                'buku_id'       => 'B004',
                'tgl_pinjam'    => '2024-04-10',
                'tgl_kembali'   => '2024-04-24',
                'tgl_aktual'    => '2024-04-22',
                'status'        => 'dikembalikan',
                'catatan'       => '',
                'created'       => '2024-04-10',
            ],
            'P003' => [
                'id'            => 'P003',
                'user_id'       => 'U005',
                'buku_id'       => 'B006',
                'tgl_pinjam'    => '2024-05-03',
                'tgl_kembali'   => '2024-05-17',
                'tgl_aktual'    => null,
                'status'        => 'dipinjam',
                'catatan'       => 'Referensi skripsi',
                'created'       => '2024-05-03',
            ],
            'P004' => [
                'id'            => 'P004',
                'user_id'       => 'U003',
                'buku_id'       => 'B002',
                'tgl_pinjam'    => date('Y-m-d'),
                'tgl_kembali'   => date('Y-m-d', strtotime('+14 days')),
                'tgl_aktual'    => null,
                'status'        => 'menunggu',
                'catatan'       => 'Belajar mandiri',
                'created'       => date('Y-m-d'),
            ],
        ];
    }

    // ── NOTIFIKASI ─────────────────────────────────────────────────────
    private static function seedNotifikasi(): void
    {
        $_SESSION['notifikasi'] = [
            'N001' => [
                'id'       => 'N001',
                'user_id'  => 'U003',
                'pesan'    => 'Pengingat: Buku "Pemrograman PHP Modern" harus dikembalikan dalam 3 hari.',
                'tipe'     => 'tenggat',
                'dibaca'   => false,
                'created'  => date('Y-m-d'),
            ],
            'N002' => [
                'id'       => 'N002',
                'user_id'  => 'U003',
                'pesan'    => 'Reservasi buku "Belajar Laravel Framework" berhasil diajukan.',
                'tipe'     => 'info',
                'dibaca'   => false,
                'created'  => date('Y-m-d'),
            ],
        ];
    }
}
