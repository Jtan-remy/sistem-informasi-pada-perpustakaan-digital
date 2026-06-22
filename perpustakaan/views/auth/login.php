<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-bg d-flex align-items-center justify-content-center" style="min-height:100vh">

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-8">

      <div class="text-center mb-4">
        <div class="auth-logo mb-3">
          <i class="bi bi-book-half text-primary" style="font-size:3rem"></i>
        </div>
        <h4 class="fw-bold"><?= APP_NAME ?></h4>
        <p class="text-muted small">Masuk ke akun Anda</p>
      </div>

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2" role="alert">
        <i class="bi <?= $flash['type']==='success' ? 'bi-check-circle' : 'bi-exclamation-triangle' ?>"></i>
        <span><?= $flash['msg'] ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <form action="index.php?action=login" method="POST" novalidate>
            <div class="mb-3">
              <label class="form-label fw-medium">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control border-start-0 ps-0"
                       placeholder="nama@email.com" required autocomplete="email">
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label fw-medium">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="pwInput" class="form-control border-start-0 ps-0"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePw()">
                  <i class="bi bi-eye" id="pwIcon"></i>
                </button>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
              <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
          </form>
        </div>
      </div>

      <p class="text-center mt-3 small text-muted">
        Belum punya akun?
        <a href="index.php?page=auth/register" class="text-primary fw-medium">Daftar sekarang</a>
      </p>

      <div class="card mt-4 border-0 bg-light rounded-4">
        <div class="card-body p-3">
          <p class="fw-semibold small mb-2 text-muted"><i class="bi bi-info-circle me-1"></i>Akun demo:</p>
          <div class="row g-2 small">
            <div class="col-12">
              <span class="badge bg-danger me-1">Admin</span>
              <code>admin@perpus.id</code> / <code>admin123</code>
            </div>
            <div class="col-12">
              <span class="badge bg-success me-1">Staff</span>
              <code>staff@perpus.id</code> / <code>staff123</code>
            </div>
            <div class="col-12">
              <span class="badge bg-primary me-1">Client</span>
              <code>siti@mail.com</code> / <code>siti123</code>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePw() {
  const i = document.getElementById('pwInput');
  const ic = document.getElementById('pwIcon');
  if (i.type === 'password') { i.type = 'text'; ic.className = 'bi bi-eye-slash'; }
  else { i.type = 'password'; ic.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
