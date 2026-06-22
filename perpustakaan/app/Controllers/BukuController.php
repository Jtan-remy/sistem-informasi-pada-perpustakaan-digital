<?php
// app/Controllers/BukuController.php

namespace App\Controllers;

use App\Models\Buku;
use App\Models\SessionManager;
use App\Models\Validator;
use App\Models\FileUpload;
use App\Models\Pagination;

require_once __DIR__ . '/../Models/Buku.php';
require_once __DIR__ . '/../Models/Helpers.php';

class BukuController
{
    private Buku $bukuModel;

    public function __construct()
    {
        $this->bukuModel = new Buku();
    }

    // ── Simpan (Tambah/Edit) ──────────────────────────────────────────────
    public function simpan(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id   = Validator::sanitize($_POST['id'] ?? '');
        $data = [
            'judul'     => Validator::sanitize($_POST['judul']    ?? ''),
            'penulis'   => Validator::sanitize($_POST['penulis']  ?? ''),
            'penerbit'  => Validator::sanitize($_POST['penerbit'] ?? ''),
            'tahun'     => Validator::sanitize($_POST['tahun']    ?? ''),
            'isbn'      => Validator::sanitize($_POST['isbn']     ?? ''),
            'kategori'  => Validator::sanitize($_POST['kategori'] ?? ''),
            'stok'      => (int)($_POST['stok'] ?? 0),
            'deskripsi' => Validator::sanitize($_POST['deskripsi'] ?? ''),
        ];

        $errors = Validator::validate($data, [
            'judul'    => 'required|max:200',
            'penulis'  => 'required|max:100',
            'penerbit' => 'required|max:100',
            'tahun'    => 'required|numeric',
            'isbn'     => 'required',
            'kategori' => 'required',
        ]);

        if (!empty($errors)) {
            SessionManager::flash('danger', implode('<br>', $errors));
            $redirect = $id ? "index.php?page=staff/edit-buku&id={$id}" : "index.php?page=staff/tambah-buku";
            header("Location: $redirect");
            exit;
        }

        // Handle upload cover
        try {
            $coverLama = '';
            if ($id) {
                $existing  = $this->bukuModel->findById($id);
                $coverLama = $existing['cover'] ?? '';
                $data['id'] = $id;
            } else {
                $data['id'] = Buku::generateId();
            }

            if (!empty($_FILES['cover']['name'])) {
                $upload = FileUpload::upload($_FILES['cover'], 'cover');
                if (!$upload['success']) {
                    throw new \RuntimeException($upload['error']);
                }
                if ($coverLama) FileUpload::delete($coverLama);
                $data['cover'] = $upload['filename'];
            } else {
                $data['cover'] = $coverLama;
            }

            $data['created'] = $id ? ($existing['created'] ?? date('Y-m-d')) : date('Y-m-d');
            $this->bukuModel->save($data);

            $aksi = $id ? 'diperbarui' : 'ditambahkan';
            SessionManager::flash('success', "Buku berhasil {$aksi}.");
            header('Location: index.php?page=staff/kelola-buku');
            exit;

        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
            $redirect = $id ? "index.php?page=staff/edit-buku&id={$id}" : "index.php?page=staff/tambah-buku";
            header("Location: $redirect");
            exit;
        }
    }

    // ── Hapus ─────────────────────────────────────────────────────────────
    public function hapus(): void
    {
        $id = Validator::sanitize($_GET['id'] ?? '');
        if (!$id) {
            SessionManager::flash('danger', 'ID buku tidak valid.');
            header('Location: index.php?page=staff/kelola-buku');
            exit;
        }

        try {
            $buku = $this->bukuModel->findById($id);
            if (!$buku) throw new \RuntimeException('Buku tidak ditemukan.');

            if ($buku['cover']) FileUpload::delete($buku['cover']);
            $this->bukuModel->delete($id);

            SessionManager::flash('success', 'Buku berhasil dihapus.');
        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
        }

        header('Location: index.php?page=staff/kelola-buku');
        exit;
    }

    // ── Data untuk view (dengan search, filter, sort, pagination) ─────────
    public function getListData(array $params = []): array
    {
        $query    = $params['q']        ?? '';
        $kategori = $params['kategori'] ?? '';
        $sort     = $params['sort']     ?? 'judul';
        $page     = (int)($params['p'] ?? 1);
        $perPage  = ITEMS_PER_PAGE;

        $semua    = $this->bukuModel->search($query, $kategori, $sort);
        $total    = count($semua);
        $paginator = new Pagination($total, $perPage, $page);
        $items    = Pagination::paginate($semua, $perPage, $page);

        return [
            'items'     => $items,
            'paginator' => $paginator,
            'total'     => $total,
            'query'     => $query,
            'kategori'  => $kategori,
            'sort'      => $sort,
        ];
    }
}
