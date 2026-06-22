<?php
// views/shared/pagination.php
// Variabel yang dibutuhkan: $paginator (objek Pagination), $baseUrl (string URL tanpa param p)
if (!isset($paginator) || $paginator->getTotalPage() <= 1) return;
?>
<nav aria-label="Navigasi halaman" class="mt-4">
  <ul class="pagination justify-content-center mb-0">
    <li class="page-item <?= !$paginator->hasPrev() ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $baseUrl ?>&p=<?= $paginator->getCurrentPage() - 1 ?>">
        <i class="bi bi-chevron-left"></i>
      </a>
    </li>
    <?php foreach ($paginator->getPageRange() as $p): ?>
    <li class="page-item <?= $p === $paginator->getCurrentPage() ? 'active' : '' ?>">
      <a class="page-link" href="<?= $baseUrl ?>&p=<?= $p ?>"><?= $p ?></a>
    </li>
    <?php endforeach; ?>
    <li class="page-item <?= !$paginator->hasNext() ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $baseUrl ?>&p=<?= $paginator->getCurrentPage() + 1 ?>">
        <i class="bi bi-chevron-right"></i>
      </a>
    </li>
  </ul>
  <p class="text-center text-muted small mt-2">
    Halaman <?= $paginator->getCurrentPage() ?> dari <?= $paginator->getTotalPage() ?>
    (<?= $paginator->getTotalItem() ?> data)
  </p>
</nav>
