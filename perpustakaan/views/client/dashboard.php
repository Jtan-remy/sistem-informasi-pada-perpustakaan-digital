<?php // views/client/dashboard.php
use App\Models\Notifikasi;
$userId = $auth['id'];
$notifs = array_slice(Notifikasi::getByUser($userId), 0, 4);

// Peminjaman aktif milik client
$pinjamAktif = [];
foreach ($_SESSION['peminjaman'] ?? [] as $p) {
    if ($p['user_id'] === $userId && in_array($p['status'], ['dipinjam','menunggu'])) {
        $p['_buku'] = $_SESSION['buku'][$p['buku_id']] ?? [];
        $pinjamAktif[] = $p;
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Selamat datang, <?= htmlspecialchars($auth['nama']) ?> 👋</h4>
    <p class="text-muted small mb-0">Pantau aktivitas peminjaman bukumu di sini.</p>
  </div>
  <a href="index.php?page=client/katalog" class="btn btn-primary">
    <i class="bi bi-search me-1"></i>Cari Buku
  </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'Sedang Dipinjam', 'val'=>$stats['dipinjam'],   'icon'=>'bi-book-fill',       'color'=>'primary'],
    ['label'=>'Menunggu Konfirmasi','val'=>$stats['menunggu'], 'icon'=>'bi-hourglass-split',  'color'=>'warning'],
    ['label'=>'Selesai Dikembalikan','val'=>$stats['selesai'], 'icon'=>'bi-check-circle-fill','color'=>'success'],
    ['label'=>'Notifikasi Baru',  'val'=>$stats['notif_baru'],'icon'=>'bi-bell-fill',        'color'=>'info'],
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

<div class="row g-4">
  <!-- Peminjaman Aktif -->
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between">
        <h6 class="fw-bold mb-0"><i class="bi bi-bookmark-check me-2 text-primary"></i>Peminjaman Aktif</h6>
        <a href="index.php?page=client/riwayat" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <?php if (empty($pinjamAktif)): ?>
          <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-6"></i>
            <p class="mt-2 small">Belum ada peminjaman aktif.</p>
            <a href="index.php?page=client/katalog" class="btn btn-sm btn-primary">Pinjam Sekarang</a>
          </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
          <?php foreach ($pinjamAktif as $p):
            $buku    = $p['_buku'];
            $selisih = (int)((strtotime($p['tgl_kembali']) - strtotime(date('Y-m-d'))) / 86400);
            $late    = $selisih < 0;
            $soon    = $selisih <= 3 && $selisih >= 0;
          ?>
          <div class="list-group-item border-0 px-4 py-3">
            <div class="d-flex align-items-start gap-3">
              <div class="book-cover-sm bg-primary bg-opacity-10 rounded text-center d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:60px">
                <?php if (!empty($buku['cover'])): ?>
                  <img src="<?= UPLOAD_URL . htmlspecialchars($buku['cover']) ?>" class="rounded" style="width:44px;height:60px;object-fit:cover">
                <?php else: ?>
                  <i class="bi bi-book text-primary fs-5"></i>
                <?php endif; ?>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-truncate"><?= htmlspecialchars($buku['judul'] ?? '-') ?></div>
                <div class="text-muted small"><?= htmlspecialchars($buku['penulis'] ?? '') ?></div>
                <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                  <?php if ($p['status'] === 'menunggu'): ?>
                    <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                  <?php elseif ($late): ?>
                    <span class="badge bg-danger">Terlambat <?= abs($selisih) ?> hari</span>
                  <?php elseif ($soon): ?>
                    <span class="badge bg-warning text-dark">Tenggat <?= $selisih ?> hari lagi</span>
                  <?php else: ?>
                    <span class="badge bg-primary">Dipinjam</span>
                  <?php endif; ?>
                  <span class="text-muted small">Kembali: <?= date('d M Y', strtotime($p['tgl_kembali'])) ?></span>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Notifikasi -->
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between">
        <h6 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-warning"></i>Notifikasi Terbaru</h6>
      
      </div>
      <div class="card-body p-0">
        <?php if (empty($notifs)): ?>
          <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash display-6"></i>
            <p class="mt-2 small">Tidak ada notifikasi.</p>
          </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
          <?php foreach ($notifs as $n):
            $icon = match($n['tipe']) {
              'tenggat'     => 'bi-clock-fill text-warning',
              'peringatan'  => 'bi-exclamation-triangle-fill text-danger',
              default       => 'bi-info-circle-fill text-primary',
            };
          ?>
          <div class="list-group-item border-0 px-4 py-3 <?= !$n['dibaca'] ? 'bg-light' : '' ?>">
            <div class="d-flex gap-3">
              <i class="bi <?= $icon ?> fs-5 mt-1 flex-shrink-0"></i>
              <div>
                <div class="small"><?= htmlspecialchars($n['pesan']) ?></div>
                <div class="text-muted" style="font-size:.75rem"><?= $n['created'] ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
