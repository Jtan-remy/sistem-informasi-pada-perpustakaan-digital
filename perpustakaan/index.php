<?php
// index.php — entry point utama aplikasi

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Models/UserSubclasses.php';
require_once __DIR__ . '/app/Models/Buku.php';
require_once __DIR__ . '/app/Models/Peminjaman.php';
require_once __DIR__ . '/app/Models/Helpers.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/BukuController.php';
require_once __DIR__ . '/app/Controllers/PeminjamanController.php';
require_once __DIR__ . '/app/Controllers/UserController.php';

// Boot session & seed data
\App\Config\SessionData::init();

// Dispatch ke router
require_once __DIR__ . '/router.php';
