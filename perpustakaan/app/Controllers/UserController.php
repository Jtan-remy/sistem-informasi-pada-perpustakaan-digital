<?php
// app/Controllers/UserController.php

namespace App\Controllers;

use App\Models\User;
use App\Models\SessionManager;
use App\Models\Validator;
use App\Models\FileUpload;
use App\Models\Pagination;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Helpers.php';

class UserController
{
    public function __construct() {}

    // ── Verifikasi user oleh Admin ────────────────────────────────────────
    public function verifikasi(): void
    {
        $id     = Validator::sanitize($_POST['id']     ?? $_GET['id'] ?? '');
        $action = Validator::sanitize($_POST['action'] ?? '');

        try {
            if (!isset($_SESSION['users'][$id])) {
                throw new \RuntimeException('User tidak ditemukan.');
            }
            $status = ($action === 'approve') ? 'aktif' : 'ditolak';
            $_SESSION['users'][$id]['status'] = $status;

            $label = $action === 'approve' ? 'disetujui' : 'ditolak';
            SessionManager::flash('success', "Akun berhasil {$label}.");
        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
        }

        header('Location: index.php?page=admin/verifikasi');
        exit;
    }

    // ── Edit profil sendiri (Client/Staff) ────────────────────────────────
    public function updateProfil(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $auth = SessionManager::getAuth();
        $id   = $auth['id'] ?? '';

        $data = [
            'nama'   => Validator::sanitize($_POST['nama']   ?? ''),
            'telp'   => Validator::sanitize($_POST['telp']   ?? ''),
            'alamat' => Validator::sanitize($_POST['alamat'] ?? ''),
        ];

        $errors = Validator::validate($data, [
            'nama' => 'required|min:3|max:100',
            'telp' => 'required',
        ]);

        if (!empty($errors)) {
            SessionManager::flash('danger', implode('<br>', $errors));
            header('Location: index.php?page=client/profil');
            exit;
        }

        try {
            // Handle foto
            $fotoLama = $_SESSION['users'][$id]['foto'] ?? '';
            if (!empty($_FILES['foto']['name'])) {
                $upload = FileUpload::upload($_FILES['foto'], 'profil');
                if (!$upload['success']) throw new \RuntimeException($upload['error']);
                if ($fotoLama) FileUpload::delete($fotoLama);
                $_SESSION['users'][$id]['foto'] = $upload['filename'];
                $_SESSION['auth']['foto']       = $upload['filename'];
            }

            $_SESSION['users'][$id]['nama']   = $data['nama'];
            $_SESSION['users'][$id]['telp']   = $data['telp'];
            $_SESSION['users'][$id]['alamat'] = $data['alamat'];
            $_SESSION['auth']['nama']         = $data['nama'];

            // Ganti password (opsional)
            $pw = $_POST['password'] ?? '';
            if ($pw !== '') {
                if (strlen($pw) < 6) throw new \RuntimeException('Password minimal 6 karakter.');
                $_SESSION['users'][$id]['password'] = password_hash($pw, PASSWORD_DEFAULT);
            }

            SessionManager::flash('success', 'Profil berhasil diperbarui.');
        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
        }

        header('Location: index.php?page=client/profil');
        exit;
    }

    // ── CRUD user oleh Admin ──────────────────────────────────────────────
    public function simpanUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id   = Validator::sanitize($_POST['id'] ?? '');
        $data = [
            'nama'   => Validator::sanitize($_POST['nama']   ?? ''),
            'email'  => Validator::sanitize($_POST['email']  ?? ''),
            'role'   => Validator::sanitize($_POST['role']   ?? 'client'),
            'status' => Validator::sanitize($_POST['status'] ?? 'aktif'),
            'telp'   => Validator::sanitize($_POST['telp']   ?? ''),
            'alamat' => Validator::sanitize($_POST['alamat'] ?? ''),
        ];

        $errors = Validator::validate($data, [
            'nama'  => 'required|min:3|max:100',
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            SessionManager::flash('danger', implode('<br>', $errors));
            header('Location: index.php?page=admin/kelola-user');
            exit;
        }

        if (!Validator::isEmailUnique($data['email'], $id)) {
            SessionManager::flash('danger', 'Email sudah digunakan.');
            header('Location: index.php?page=admin/kelola-user');
            exit;
        }

        try {
            if ($id && isset($_SESSION['users'][$id])) {
                // Update
                foreach ($data as $key => $val) {
                    $_SESSION['users'][$id][$key] = $val;
                }
                $pw = $_POST['password'] ?? '';
                if ($pw !== '') {
                    $_SESSION['users'][$id]['password'] = password_hash($pw, PASSWORD_DEFAULT);
                }
                SessionManager::flash('success', 'Data user berhasil diperbarui.');
            } else {
                // Tambah baru
                $pw = $_POST['password'] ?? '';
                if (strlen($pw) < 6) throw new \RuntimeException('Password minimal 6 karakter.');

                $newId = User::generateId('U', $_SESSION['users'] ?? []);
                $_SESSION['users'][$newId] = array_merge($data, [
                    'id'       => $newId,
                    'password' => password_hash($pw, PASSWORD_DEFAULT),
                    'foto'     => '',
                    'created'  => date('Y-m-d'),
                ]);
                SessionManager::flash('success', 'User baru berhasil ditambahkan.');
            }
        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
        }

        header('Location: index.php?page=admin/kelola-user');
        exit;
    }

    public function hapusUser(): void
    {
        $id   = Validator::sanitize($_GET['id'] ?? '');
        $auth = SessionManager::getAuth();

        try {
            if ($id === $auth['id']) throw new \RuntimeException('Tidak bisa menghapus akun sendiri.');
            if (!isset($_SESSION['users'][$id])) throw new \RuntimeException('User tidak ditemukan.');
            unset($_SESSION['users'][$id]);
            SessionManager::flash('success', 'User berhasil dihapus.');
        } catch (\RuntimeException $e) {
            SessionManager::flash('danger', $e->getMessage());
        }

        header('Location: index.php?page=admin/kelola-user');
        exit;
    }

    // ── Data untuk view (dengan pagination) ──────────────────────────────
    public function getListData(array $params = []): array
    {
        $role    = $params['role'] ?? '';
        $status  = $params['status'] ?? '';
        $page    = (int)($params['page'] ?? 1);
        $perPage = ITEMS_PER_PAGE;

        $users = $_SESSION['users'] ?? [];
        $hasil = [];

        foreach ($users as $u) {
            if (!empty($role)   && $u['role']   !== $role)   continue;
            if (!empty($status) && $u['status'] !== $status) continue;
            $hasil[] = $u;
        }

        $total    = count($hasil);
        $paginator = new Pagination($total, $perPage, $page);
        $items    = Pagination::paginate($hasil, $perPage, $page);

        return [
            'items'     => $items,
            'paginator' => $paginator,
            'total'     => $total,
        ];
    }
}
