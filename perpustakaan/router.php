<?php
// router.php — menangani semua routing aplikasi

use App\Models\SessionManager;
use App\Controllers\AuthController;
use App\Controllers\BukuController;
use App\Controllers\PeminjamanController;
use App\Controllers\UserController;

/**
 * Guard: pastikan user sudah login dan role sesuai.
 */
function requireAuth(string ...$roles): void
{
    if (!SessionManager::isAuth()) {
        SessionManager::flash('danger', 'Silakan login terlebih dahulu.');
        header('Location: index.php?page=auth/login');
        exit;
    }
    if (!empty($roles) && !in_array(SessionManager::getRole(), $roles, true)) {
        SessionManager::flash('danger', 'Akses ditolak.');
        $role = SessionManager::getRole();
        header("Location: index.php?page={$role}/dashboard");
        exit;
    }
}

/**
 * Render view file dengan variabel yang diinjeksi.
 */
function renderView(string $path, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    $file = __DIR__ . '/views/' . $path . '.php';
    if (!file_exists($file)) {
        echo "<div class='alert alert-danger m-4'>View tidak ditemukan: <code>{$path}</code></div>";
        return;
    }
    require $file;
}

// ── Tangani action POST sebelum render ───────────────────────────────────
$action = $_GET['action'] ?? '';

match ($action) {
    'login'          => (new AuthController())->login(),
    'logout'         => (new AuthController())->logout(),
    'register'       => (new AuthController())->register(),
    'simpan-buku'    => (function() { requireAuth('staff'); (new BukuController())->simpan(); })(),
    'hapus-buku'     => (function() { requireAuth('staff'); (new BukuController())->hapus(); })(),
    'ajukan-pinjam'  => (function() { requireAuth('client'); (new PeminjamanController())->ajukan(); })(),
    'update-status'  => (function() { requireAuth('staff'); (new PeminjamanController())->updateStatus(); })(),
    'update-profil'  => (function() { requireAuth('client','staff'); (new UserController())->updateProfil(); })(),
    'verifikasi-user'=> (function() { requireAuth('admin'); (new UserController())->verifikasi(); })(),
    'simpan-user'    => (function() { requireAuth('admin'); (new UserController())->simpanUser(); })(),
    'hapus-user'     => (function() { requireAuth('admin'); (new UserController())->hapusUser(); })(),
    'tandai-notif'   => (function() { requireAuth('client', 'staff', 'admin');
        \App\Models\Notifikasi::tandaiDibaca(\App\Models\SessionManager::getAuth()['id']);
        $role = \App\Models\SessionManager::getRole();
        header("Location: index.php?page={$role}/dashboard");
        exit;
    })(),
    default          => null,
};

// ── Routing halaman (GET) ─────────────────────────────────────────────────
$page = $_GET['page'] ?? 'auth/login';

// Redirect root ke login atau dashboard
if ($page === '' || $page === 'home') {
    if (SessionManager::isAuth()) {
        $role = SessionManager::getRole();
        header("Location: index.php?page={$role}/dashboard");
    } else {
        header('Location: index.php?page=auth/login');
    }
    exit;
}

// Peta halaman → [guard_roles, view_file, data_callback]
$routes = [
    // Auth (publik)
    'auth/login'    => [[], 'auth/login',    null],
    'auth/register' => [[], 'auth/register', null],

    // Client
    'client/dashboard' => [['client'], 'client/dashboard', function() {
        $client = new \App\Models\Client($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return ['stats' => $client->getDashboardData(), 'menu' => $client->getMenuItems()];
    }],
    'client/katalog' => [['client'], 'client/katalog', function() {
        $ctrl = new BukuController();
        $data = $ctrl->getListData($_GET);
        $client = new \App\Models\Client($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return array_merge($data, ['menu' => $client->getMenuItems()]);
    }],
    'client/reservasi' => [['client'], 'client/reservasi', function() {
        $bukuId = $_GET['buku_id'] ?? '';
        $buku   = (new \App\Models\Buku())->findById($bukuId);
        $client = new \App\Models\Client($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return ['buku' => $buku, 'menu' => $client->getMenuItems()];
    }],
    'client/riwayat' => [['client'], 'client/riwayat', function() {
        $userId = SessionManager::getAuth()['id'];
        $ctrl   = new PeminjamanController();
        $data   = $ctrl->getRiwayat($userId, $_GET);
        $client = new \App\Models\Client($_SESSION['users'][$userId] ?? []);
        return array_merge($data, ['menu' => $client->getMenuItems()]);
    }],
    'client/profil' => [['client'], 'client/profil', function() {
        $userId = SessionManager::getAuth()['id'];
        $client = new \App\Models\Client($_SESSION['users'][$userId] ?? []);
        return ['user' => $_SESSION['users'][$userId], 'menu' => $client->getMenuItems()];
    }],

    // Staff
    'staff/dashboard' => [['staff'], 'staff/dashboard', function() {
        $staff = new \App\Models\Staff($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return ['stats' => $staff->getDashboardData(), 'menu' => $staff->getMenuItems()];
    }],
    'staff/kelola-buku' => [['staff'], 'staff/kelola-buku', function() {
        $ctrl  = new BukuController();
        $data  = $ctrl->getListData($_GET);
        $staff = new \App\Models\Staff($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return array_merge($data, ['menu' => $staff->getMenuItems()]);
    }],
    'staff/tambah-buku' => [['staff'], 'staff/form-buku', function() {
        $staff = new \App\Models\Staff($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return ['buku' => null, 'menu' => $staff->getMenuItems()];
    }],
    'staff/edit-buku' => [['staff'], 'staff/form-buku', function() {
        $buku  = (new \App\Models\Buku())->findById($_GET['id'] ?? '');
        $staff = new \App\Models\Staff($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return ['buku' => $buku, 'menu' => $staff->getMenuItems()];
    }],
    'staff/peminjaman' => [['staff'], 'staff/peminjaman', function() {
        $ctrl  = new PeminjamanController();
        $data  = $ctrl->getListData($_GET);
        $staff = new \App\Models\Staff($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return array_merge($data, ['menu' => $staff->getMenuItems()]);
    }],
    'staff/laporan' => [['staff'], 'staff/laporan', function() {
        $staff = new \App\Models\Staff($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return [
            'stats'       => $staff->getDashboardData(),
            'peminjaman'  => $_SESSION['peminjaman'] ?? [],
            'buku'        => $_SESSION['buku'] ?? [],
            'users'       => $_SESSION['users'] ?? [],
            'menu'        => $staff->getMenuItems(),
        ];
    }],

    // Admin
    'admin/dashboard' => [['admin'], 'admin/dashboard', function() {
        $admin = new \App\Models\Admin($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return ['stats' => $admin->getDashboardData(), 'menu' => $admin->getMenuItems()];
    }],
    'admin/verifikasi' => [['admin'], 'admin/verifikasi', function() {
        $admin   = new \App\Models\Admin($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        $pending = array_filter($_SESSION['users'] ?? [], fn($u) => $u['status'] === 'pending');
        return ['pending' => $pending, 'menu' => $admin->getMenuItems()];
    }],
    'admin/kelola-user' => [['admin'], 'admin/kelola-user', function() {
        $ctrl  = new UserController();
        $data  = $ctrl->getListData($_GET);
        $admin = new \App\Models\Admin($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return array_merge($data, ['menu' => $admin->getMenuItems()]);
    }],
    'admin/statistik' => [['admin'], 'admin/statistik', function() {
        $admin = new \App\Models\Admin($_SESSION['users'][SessionManager::getAuth()['id']] ?? []);
        return [
            'stats'      => $admin->getDashboardData(),
            'katStat'    => \App\Models\Buku::getStatistikKategori(),
            'bulanStat'  => \App\Models\Peminjaman::getStatistikBulanan(),
            'menu'       => $admin->getMenuItems(),
        ];
    }],
];

// ── Dispatch ─────────────────────────────────────────────────────────────
if (!isset($routes[$page])) {
    renderView('shared/404');
    return;
}

[$roles, $viewFile, $dataFn] = $routes[$page];

if (!empty($roles)) requireAuth(...$roles);

$viewData = $dataFn ? $dataFn() : [];
$flash    = SessionManager::getFlash();

renderView('shared/layout', array_merge($viewData, [
    'page'     => $page,
    'viewFile' => $viewFile,
    'flash'    => $flash,
    'auth'     => SessionManager::getAuth(),
]));
