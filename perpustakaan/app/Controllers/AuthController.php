<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\SessionManager;
use App\Models\Validator;
use App\Models\Notifikasi;

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/UserSubclasses.php';
require_once __DIR__ . '/../Models/Helpers.php';

class AuthController
{
    private Client $clientModel;

    public function __construct()
    {
        $this->clientModel = new Client();
    }

    // ── Login ─────────────────────────────────────────────────────────────
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $email    = Validator::sanitize($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        $errors = Validator::validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required']
        );

        if (!empty($errors)) {
            SessionManager::flash('danger', implode(' ', $errors));
            header('Location: index.php?page=auth/login');
            exit;
        }

        // Gunakan method login dari abstract User (via Client instance)
        try {
            if ($this->clientModel->login($email, $password)) {
                $role = SessionManager::getRole();
                // Cek notif tenggat saat login
                Notifikasi::cekTenggat();
                SessionManager::flash('success', 'Selamat datang kembali!');
                header("Location: index.php?page={$role}/dashboard");
                exit;
            } else {
                SessionManager::flash('danger', 'Email atau password salah, atau akun belum aktif.');
                header('Location: index.php?page=auth/login');
                exit;
            }
        } catch (\Exception $e) {
            SessionManager::flash('danger', 'Terjadi kesalahan sistem. Coba lagi.');
            header('Location: index.php?page=auth/login');
            exit;
        }
    }

    // ── Logout ───────────────────────────────────────────────────────────
    public function logout(): void
    {
        $this->clientModel->logout();
        SessionManager::flash('success', 'Anda berhasil keluar.');
        header('Location: index.php?page=auth/login');
        exit;
    }

    // ── Register ─────────────────────────────────────────────────────────
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $data = [
            'nama'     => Validator::sanitize($_POST['nama']     ?? ''),
            'email'    => Validator::sanitize($_POST['email']    ?? ''),
            'password' => $_POST['password'] ?? '',
            'konfirm'  => $_POST['konfirm']  ?? '',
            'telp'     => Validator::sanitize($_POST['telp']     ?? ''),
            'alamat'   => Validator::sanitize($_POST['alamat']   ?? ''),
        ];

        $errors = Validator::validate($data, [
            'nama'     => 'required|min:3|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'telp'     => 'required',
        ]);

        if (!empty($errors)) {
            SessionManager::flash('danger', implode('<br>', $errors));
            header('Location: index.php?page=auth/register');
            exit;
        }

        if ($data['password'] !== $data['konfirm']) {
            SessionManager::flash('danger', 'Konfirmasi password tidak cocok.');
            header('Location: index.php?page=auth/register');
            exit;
        }

        if (!Validator::isEmailUnique($data['email'])) {
            SessionManager::flash('danger', 'Email sudah terdaftar.');
            header('Location: index.php?page=auth/register');
            exit;
        }

        try {
            $existing = $_SESSION['users'] ?? [];
            $id       = User::generateId('U', $existing);

            $_SESSION['users'][$id] = [
                'id'       => $id,
                'nama'     => $data['nama'],
                'email'    => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role'     => 'client',
                'foto'     => '',
                'telp'     => $data['telp'],
                'alamat'   => $data['alamat'],
                'status'   => 'pending',  // perlu verifikasi admin
                'created'  => date('Y-m-d'),
            ];

            SessionManager::flash('success', 'Pendaftaran berhasil! Tunggu verifikasi admin sebelum bisa login.');
            header('Location: index.php?page=auth/login');
            exit;
        } catch (\Exception $e) {
            SessionManager::flash('danger', 'Gagal mendaftar. Coba lagi.');
            header('Location: index.php?page=auth/register');
            exit;
        }
    }
}
