<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar — <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-bg d-flex align-items-center py-5">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

      <div class="text-center mb-4">
        <i class="bi bi-book-half text-primary" style="font-size:2.5rem"></i>
        <h4 class="fw-bold mt-2"><?= APP_NAME ?></h4>
        <p class="text-muted small">Buat akun baru sebagai anggota</p>
      </div>

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
        <?= $flash['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <form action="index.php?action=register" method="POST" novalidate>

            <div class="mb-3">
              <label class="form-label fw-medium">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required minlength="3">
            </div>
            <div class="mb-3">
              <label class="form-label fw-medium">Email</label>
              <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-medium">No. Telepon</label>
              <input type="text" name="telp" class="form-control" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-medium">Alamat</label>
              <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-medium">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="mb-4">
              <label class="form-label fw-medium">Konfirmasi Password</label>
              <input type="password" name="konfirm" class="form-control" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
              <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
            </button>
          </form>
        </div>
      </div>

      <p class="text-center mt-3 small text-muted">
        Sudah punya akun? <a href="index.php?page=auth/login" class="text-primary fw-medium">Masuk di sini</a>
      </p>
      <div class="alert alert-info small mt-2 rounded-3">
        <i class="bi bi-info-circle me-1"></i>Akun baru perlu diverifikasi admin sebelum bisa login.
      </div>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
