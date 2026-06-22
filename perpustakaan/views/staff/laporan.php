<?php // views/staff/laporan.php
$totalBuku  = count($buku);
$totalPinjam= count($peminjaman);
$aktif = $selesai = $menunggu = $ditolak = 0;
foreach ($peminjaman as $p) {
    match($p['status']) {
        'dipinjam'     => $aktif++,
        'dikembalikan' => $selesai++,
        'menunggu'     => $menunggu++,
        'ditolak'      => $ditolak++,
        default        => null,
    };
}
$totalMember = count(array_filter($users, fn($u) => $u['role'] === 'client' && $u['status'] === 'aktif'));

// Top 5 buku paling sering dipinjam
$bukuCount = [];
foreach ($peminjaman as $p) {
    $bukuCount[$p['buku_id']] = ($bukuCount[$p['buku_id']] ?? 0) + 1;
}
arsort($bukuCount);
$topBuku = array_slice($bukuCount, 0, 5, true);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Laporan Pencatatan Buku</h4>
    <p class="text-muted small mb-0">Ringkasan aktivitas perpustakaan.</p>
  </div>
  <span class="badge bg-secondary">Per <?= date('d M Y') ?></span>
</div>

<!-- Summary cards -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'Total Koleksi Buku', 'val'=>$totalBuku,   'icon'=>'bi-journals',       'color'=>'primary'],
    ['label'=>'Total Anggota Aktif','val'=>$totalMember,  'icon'=>'bi-people-fill',    'color'=>'info'],
    ['label'=>'Total Transaksi',    'val'=>$totalPinjam,  'icon'=>'bi-arrow-left-right','color'=>'secondary'],
    ['label'=>'Sedang Dipinjam',    'val'=>$aktif,        'icon'=>'bi-bookmark-fill',  'color'=>'warning'],
    ['label'=>'Dikembalikan',       'val'=>$selesai,      'icon'=>'bi-check-circle',   'color'=>'success'],
    ['label'=>'Menunggu',           'val'=>$menunggu,     'icon'=>'bi-hourglass',      'color'=>'danger'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-6 col-md-2">
    <div class="card border-0 shadow-sm text-center p-3">
      <i class="bi <?= $c['icon'] ?> fs-3 text-<?= $c['color'] ?> mb-1"></i>
      <div class="fs-4 fw-bold"><?= $c['val'] ?></div>
      <div class="text-muted" style="font-size:.75rem"><?= $c['label'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <!-- Top buku -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Top 5 Buku Paling Sering Dipinjam</h6>
      </div>
      <div class="card-body">
        <?php if (empty($topBuku)): ?>
          <p class="text-muted text-center py-3">Belum ada data.</p>
        <?php else: ?>
          <?php $rank = 1; foreach ($topBuku as $bid => $cnt):
            $b = $buku[$bid] ?? null;
            if (!$b) continue;
            $pct = $totalPinjam > 0 ? round($cnt / $totalPinjam * 100) : 0;
          ?>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark">#<?= $rank++ ?></span>
                <span class="small fw-semibold"><?= htmlspecialchars($b['judul']) ?></span>
              </div>
              <span class="badge bg-primary"><?= $cnt ?>x</span>
            </div>
            <div class="progress" style="height:6px">
              <div class="progress-bar" style="width:<?= $pct ?>%"></div>
            </div>
            <div class="text-muted" style="font-size:.72rem"><?= htmlspecialchars($b['penulis']) ?></div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Daftar semua peminjaman aktif -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between">
        <h6 class="fw-bold mb-0"><i class="bi bi-bookmark-check me-2 text-primary"></i>Peminjaman Aktif</h6>
        <a href="index.php?page=staff/peminjaman&status=dipinjam" class="btn btn-sm btn-outline-primary">Semua</a>
      </div>
      <div class="card-body p-0" style="max-height:340px;overflow-y:auto">
        <div class="list-group list-group-flush">
          <?php
          $aktifList = array_filter($peminjaman, fn($p) => $p['status'] === 'dipinjam');
          if (empty($aktifList)): ?>
            <div class="text-center py-4 text-muted small">Tidak ada peminjaman aktif.</div>
          <?php else:
            foreach ($aktifList as $p):
              $b  = $buku[$p['buku_id']] ?? null;
              $u  = $users[$p['user_id']] ?? null;
              $late = strtotime($p['tgl_kembali']) < strtotime(date('Y-m-d'));
          ?>
          <div class="list-group-item border-0 px-4 py-2 <?= $late ? 'bg-danger bg-opacity-10' : '' ?>">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold small"><?= htmlspecialchars($b['judul'] ?? '-') ?></div>
                <div class="text-muted small"><?= htmlspecialchars($u['nama'] ?? '-') ?></div>
              </div>
              <div class="text-end">
                <?php if ($late): ?>
                  <span class="badge bg-danger">Terlambat</span>
                <?php else: ?>
                  <span class="small text-muted"><?= date('d M', strtotime($p['tgl_kembali'])) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
