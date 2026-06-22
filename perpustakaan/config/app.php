<?php
// config/app.php — konfigurasi global aplikasi

define('APP_NAME',    'Perpustakaan Digital');
define('APP_VERSION', '1.0.0');
define('APP_URL',     '');          // kosongkan untuk path relatif
define('UPLOAD_DIR',  __DIR__ . '/../public/uploads/covers/');
define('UPLOAD_URL',  'public/uploads/covers/');
define('MAX_UPLOAD',  2 * 1024 * 1024);  // 2 MB
define('ITEMS_PER_PAGE', 4);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (matikan di produksi)
error_reporting(E_ALL);
ini_set('display_errors', 1);
