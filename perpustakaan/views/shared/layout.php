<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php
$isAuth   = isset($auth) && $auth;
$isPublic = str_starts_with($viewFile, 'auth/');
?>

<?php if ($isAuth && !$isPublic): ?>
  <?php require __DIR__ . '/navbar.php'; ?>
  <div class="d-flex" style="min-height:100vh">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="flex-grow-1 main-content p-4">
      <?php require __DIR__ . '/flash.php'; ?>
      <?php renderView($viewFile, get_defined_vars()); ?>
    </main>
  </div>
<?php else: ?>
  <main>
    <?php require __DIR__ . '/flash.php'; ?>
    <?php renderView($viewFile, get_defined_vars()); ?>
  </main>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="public/js/app.js"></script>
</body>
</html>
