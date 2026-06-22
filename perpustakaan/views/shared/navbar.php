<?php
// views/shared/navbar.php
use App\Models\Notifikasi;
$notifCount = $auth ? Notifikasi::countUnread($auth['id']) : 0;
$notifs     = $auth ? Notifikasi::getByUser($auth['id']) : [];
$notifs     = array_slice($notifs, 0, 5);
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm px-3"
     style="background-color:#4338ca !important">
  <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php?page=<?= $auth['role'] ?>/dashboard">
    <i class="bi bi-book-half fs-4"></i>
    <span><?= APP_NAME ?></span>
  </a>
  <div class="ms-auto d-flex align-items-center gap-3">

    <?php if ($notifCount > 0 || count($notifs) > 0): ?>
    <div class="dropdown">
      <button class="btn btn-link text-white position-relative p-0" data-bs-toggle="dropdown">
        <i class="bi bi-bell-fill fs-5"></i>
        <?php if ($notifCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
            <?= $notifCount ?>
          </span>
        <?php endif; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:320px;max-height:360px;overflow-y:auto">
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
          <strong>Notifikasi</strong>
          <?php if ($notifCount > 0): ?>
            <a href="index.php?action=tandai-notif" class="text-primary small">Tandai dibaca</a>
          <?php endif; ?>
        </li>
        <li><hr class="dropdown-divider m-0"></li>
        <?php if (empty($notifs)): ?>
          <li class="px-3 py-2 text-muted small">Tidak ada notifikasi.</li>
        <?php else: ?>
          <?php foreach ($notifs as $n):
            $icon  = match($n['tipe']) { 'tenggat' => 'bi-clock text-warning', 'peringatan' => 'bi-exclamation-triangle text-danger', default => 'bi-info-circle text-primary' };
            $bg    = $n['dibaca'] ? '' : 'bg-light';
          ?>
          <li>
            <div class="dropdown-item <?= $bg ?> d-flex gap-2 py-2">
              <i class="bi <?= $icon ?> mt-1 flex-shrink-0"></i>
              <div>
                <div class="small"><?= htmlspecialchars($n['pesan']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= $n['created'] ?></div>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
    <?php endif; ?>

    <div class="dropdown">
      <button class="btn btn-link text-white p-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
        <?php if ($auth['foto']): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($auth['foto']) ?>" class="rounded-circle" width="32" height="32" style="object-fit:cover">
        <?php else: ?>
          <i class="bi bi-person-circle fs-4"></i>
        <?php endif; ?>
        <span class="d-none d-md-inline small"><?= htmlspecialchars($auth['nama']) ?></span>
        <i class="bi bi-chevron-down small"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li><h6 class="dropdown-header"><?= ucfirst($auth['role']) ?></h6></li>
        <li>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger" href="index.php?action=logout">
            <i class="bi bi-box-arrow-right me-2"></i>Keluar
          </a>
        </li>
      </ul>
    </div>

  </div>
</nav>
