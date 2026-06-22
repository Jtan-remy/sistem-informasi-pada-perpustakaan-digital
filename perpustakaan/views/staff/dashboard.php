<?php // views/staff/dashboard.php
// Peminjaman menunggu konfirmasi (5 terbaru)
$menungguList = [];
foreach ($_SESSION['peminjaman'] ?? [] as $p) {
    if ($p['status'] === 'menunggu') {
        $p['_buku'] = $_SESSION['buku'][$p['buku_id']] ?? [];
        $p['_user'] = $_SESSION['users'][$p['user_id']] ?? [];
        $menungguList[] = $p;
    }
}
usort($menungguList, fn($a,$b) => strcmp($b['created'],$a['created']));
$menungguList = array_slice($menungguList, 0, 5);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Dashboard Staff</h4>
    <p class="text-muted small mb-0">Pantau dan kelola aktivitas perpustakaan.</p>
  </div>
  <a href="index.php?page=staff/tambah-buku" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Tambah Buku
  </a>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'Total Buku',         'val'=>$stats['total_buku'], 'icon'=>'bi-journals',           'color'=>'primary'],
    ['label'=>'Sedang Dipinjam',    'val'=>$stats['dipinjam'],   'icon'=>'bi-bookmark-check-fill', 'color'=>'warning'],
    ['label'=>'Menunggu Konfirmasi','val'=>$stats['menunggu'],   'icon'=>'bi-hourglass-split',     'color'=>'info'],
    ['label'=>'Stok Habis',         'val'=>$stats['stok_habis'], 'icon'=>'bi-exclamation-triangle-fill','color'=>'danger'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm h-100 stat-card">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="stat-icon bg-<?= $c['color'] ?> bg-opacity-10 text-<?= $c['color'] ?> rounded-3 p-3">
          <i class="bi <?= $c['icon'] ?> fs-4"></i>
        </div>
        <div>
          <div class="fs-3 fw-bold lh-1"><?= $c['val'] ?></div>
          <div class="text-muted small"><?= $c['label'] ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Tabel menunggu konfirmasi -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between">
    <h6 class="fw-bold mb-0"><i class="bi bi-hourglass-split me-2 text-info"></i>Reservasi Menunggu Konfirmasi</h6>
    <a href="index.php?page=staff/peminjaman&status=menunggu" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
  </div>
  <div class="card-body p-0">
    <?php if (empty($menungguList)): ?>
      <div class="text-center py-4 text-muted">
        <i class="bi bi-check-circle-fill text-success fs-2"></i>
        <p class="mt-2 small">Tidak ada reservasi yang perlu dikonfirmasi.</p>
      </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="px-4">Anggota</th>
            <th>Buku</th>
            <th>Tgl Ajukan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($menungguList as $p): ?>
          <tr>
            <td class="px-4">
              <div class="fw-semibold small"><?= htmlspecialchars($p['_user']['nama'] ?? '-') ?></div>
              <div class="text-muted small"><?= htmlspecialchars($p['_user']['email'] ?? '') ?></div>
            </td>
            <td>
              <div class="fw-semibold small"><?= htmlspecialchars($p['_buku']['judul'] ?? '-') ?></div>
              <div class="text-muted small"><?= htmlspecialchars($p['_buku']['penulis'] ?? '') ?></div>
            </td>
            <td class="small"><?= date('d M Y', strtotime($p['created'])) ?></td>
            <td>
              <div class="d-flex gap-2">
                <form action="index.php?action=update-status" method="POST" class="d-inline">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="status" value="dipinjam">
                  <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Setujui
                  </button>
                </form>
                <form action="index.php?action=update-status" method="POST" class="d-inline">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="status" value="ditolak">
                  <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Tolak
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
