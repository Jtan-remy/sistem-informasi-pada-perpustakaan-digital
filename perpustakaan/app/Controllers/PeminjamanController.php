<?php
// app/Controllers/PeminjamanController.php

namespace App\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\Notifikasi;
use App\Models\SessionManager;
use App\Models\Validator;
use App\Models\Pagination;

require_once __DIR__ . '/../Models/Peminjaman.php';
require_once __DIR__ . '/../Models/Buku.php';
require_once __DIR__ . '/../Models/Helpers.php';

class PeminjamanController
{
    private Peminjaman $pinjamModel;
    private Buku       $bukuModel;

    public function __construct()
    {
        $this->pinjamModel = new Peminjaman();
        $this->bukuModel   = new Buku();
    }

    // ── Ajukan reservasi (Client) ─────────────────────────────────────────
    public function ajukan(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $userId = SessionManager::getAuth()['id'] ?? '';
        $bukuId = Validator::sanitize($_POST['buku_id'] ?? '');
        $catatan = Validator::sanitize($_POST['catatan'] ?? '');

        try {
            $buku = $this->bukuModel->findById($bukuId);
            if (!$buku) throw new \RuntimeException('Buku tidak ditemukan.');
            if ((int)$buku['stok'] <= 0) throw new \RuntimeException('Stok buku habis.');
            if ($this->pinjamModel->isSedangDipinjam($userId, $bukuId)) {
                throw new \RuntimeException('Kamu sudah meminjam atau reservasi buku ini.');
            }

            $id = Peminjaman::generateId();
            $this->pinjamModel->save([
                'id'          => $id,
                'user_id'     => $userId,
                'buku_id'     => $bukuId,
                'tgl_pinjam'  => date('Y-m-d'),
                'tgl_kembali' => date('Y-m-d', strtotime('+14 days')),
                'tgl_aktual'  => null,
                'status'      => 'menunggu',
                'catatan'     => $catatan,
                'created'     => date('Y-m-d'),
            ]);

            Notifikasi::tambah($userId,
                "Reservasi buku \"{$buku['judul']}\" berhasil diajukan. Menunggu konfirmasi staff.",
                'info'
            );

            SessionManager::flash('success', 'Reservasi berhasil! Tunggu konfirmasi dari staff.');
            header('Location: index.php?page=client/riwayat');
            exit;

        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
            header("Location: index.php?page=client/reservasi&buku_id={$bukuId}");
            exit;
        }
    }

    // ── Update status (Staff) ─────────────────────────────────────────────
    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id     = Validator::sanitize($_POST['id']     ?? '');
        $status = Validator::sanitize($_POST['status'] ?? '');
        $allowed = ['dipinjam', 'dikembalikan', 'ditolak'];

        try {
            if (!in_array($status, $allowed, true)) {
                throw new \RuntimeException('Status tidak valid.');
            }
            $pinjam = $this->pinjamModel->findById($id);
            if (!$pinjam) throw new \RuntimeException('Data peminjaman tidak ditemukan.');

            $this->pinjamModel->updateStatus($id, $status);

            // Kembalikan stok jika buku dikembalikan
            if ($status === 'dikembalikan') {
                $this->bukuModel->tambahStok($pinjam['buku_id']);
                $buku = $this->bukuModel->findById($pinjam['buku_id']);
                Notifikasi::tambah($pinjam['user_id'],
                    "Pengembalian buku \"{$buku['judul']}\" telah dikonfirmasi.",
                    'info'
                );
            }

            // Kurangi stok jika disetujui dipinjam
            if ($status === 'dipinjam') {
                $this->bukuModel->kurangiStok($pinjam['buku_id']);
                $buku = $this->bukuModel->findById($pinjam['buku_id']);
                Notifikasi::tambah($pinjam['user_id'],
                    "Peminjaman buku \"{$buku['judul']}\" telah disetujui. Harap ambil dalam 1x24 jam.",
                    'info'
                );
            }

            SessionManager::flash('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
        }

        header('Location: index.php?page=staff/peminjaman');
        exit;
    }

    // ── Data untuk view staff: daftar semua peminjaman ────────────────────
    public function getListData(array $params = []): array
    {
        $status  = $params['status'] ?? '';
        $page    = (int)($params['page'] ?? 1);
        $perPage = ITEMS_PER_PAGE;

        $semua = $_SESSION['peminjaman'] ?? [];
        $hasil = [];

        foreach ($semua as $p) {
            if (!empty($status) && $p['status'] !== $status) continue;
            $p['_buku'] = $this->bukuModel->findById($p['buku_id']) ?? [];
            $p['_user'] = $_SESSION['users'][$p['user_id']] ?? [];
            $hasil[] = $p;
        }

        usort($hasil, fn($a, $b) => strcmp($b['created'], $a['created']));

        $total    = count($hasil);
        $paginator = new Pagination($total, $perPage, $page);
        $items    = Pagination::paginate($hasil, $perPage, $page);

        return [
            'items'     => $items,
            'paginator' => $paginator,
            'total'     => $total,
            'status'    => $status,
        ];
    }

    // ── Data riwayat client ───────────────────────────────────────────────
    public function getRiwayat(string $userId, array $params = []): array
    {
        $status  = $params['status'] ?? '';
        $page    = (int)($params['page'] ?? 1);
        $perPage = ITEMS_PER_PAGE;

        $semua = $this->pinjamModel->getByUser($userId, $status);
        $hasil = [];
        foreach ($semua as $p) {
            $p['_buku'] = $this->bukuModel->findById($p['buku_id']) ?? [];
            $p['_terlambat'] = $this->pinjamModel->isTerlambat($p);
            $p['_selisih']   = $this->pinjamModel->getSelisihHari($p);
            $hasil[] = $p;
        }

        $total    = count($hasil);
        $paginator = new Pagination($total, $perPage, $page);
        $items    = Pagination::paginate($hasil, $perPage, $page);

        return [
            'items'     => $items,
            'paginator' => $paginator,
            'total'     => $total,
            'status'    => $status,
        ];
    }
}
