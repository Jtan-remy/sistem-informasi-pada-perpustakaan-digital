<?php // views/shared/sidebar.php ?>
<nav class="sidebar border-end shadow-sm d-flex flex-column" style="width:230px;min-height:calc(100vh - 56px);background-color:#3730a3;">
  <ul class="nav flex-column pt-3 flex-grow-1">
    <?php foreach ($menu as $item): ?>
    <li class="nav-item">
      <a href="<?= $item['url'] ?>"
        class="nav-link d-flex align-items-center gap-2 px-4 py-2 <?= ($page === explode('?page=', $item['url'])[1]) ? 'active fw-semibold' : '' ?>"
style="color:#c7d2fe"
        <i class="bi <?= $item['icon'] ?> fs-5"></i>
        <span><?= $item['label'] ?></span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <div class="p-3 border-top">
    <small style="color:#a5b4fc"><?= APP_NAME ?> v<?= APP_VERSION ?></small>
  </div>
</nav>
