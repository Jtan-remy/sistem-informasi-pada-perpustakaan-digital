<?php // views/admin/statistik.php
$chartKatLabel = json_encode(array_keys($katStat));
$chartKatVal   = json_encode(array_values($katStat));
$chartBlLabel  = json_encode(array_keys($bulanStat));
$chartBlVal    = json_encode(array_values($bulanStat));

$users     = $_SESSION['users'] ?? [];
$pinjam    = $_SESSION['peminjaman'] ?? [];
$buku      = $_SESSION['buku'] ?? [];

$totalClient = count(array_filter($users, fn($u) => $u['role'] === 'client'));
$totalStaff  = count(array_filter($users, fn($u) => $u['role'] === 'staff'));
$stokTotal   = array_sum(array_column($buku, 'stok'));
$stokHabis   = count(array_filter($buku, fn($b) => (int)$b['stok'] === 0));
?>
<div class="mb-4">
  <h4 class="fw-bold mb-0">Statistik Sistem</h4>
  <p class="text-muted small mb-0">Analisis data perpustakaan secara menyeluruh.</p>
</div>

<!-- Ringkasan top -->
<div class="row g-3 mb-4">
  <?php
  $items2 = [
    ['l'=>'Total Client',    'v'=>$totalClient, 'i'=>'bi-people',          'c'=>'primary'],
    ['l'=>'Total Staff',     'v'=>$totalStaff,  'i'=>'bi-person-badge',    'c'=>'success'],
    ['l'=>'Total Stok Buku', 'v'=>$stokTotal,   'i'=>'bi-journals',        'c'=>'info'],
    ['l'=>'Buku Stok Habis', 'v'=>$stokHabis,   'i'=>'bi-exclamation-circle','c'=>'danger'],
  ];
  foreach ($items2 as $x): ?>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm text-center p-3">
      <i class="bi <?= $x['i'] ?> fs-2 text-<?= $x['c'] ?> mb-1"></i>
      <div class="fs-3 fw-bold"><?= $x['v'] ?></div>
      <div class="text-muted small"><?= $x['l'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Distribusi Koleksi per Kategori</h6>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <?php if (empty($katStat)): ?>
          <p class="text-muted py-4">Belum ada data.</p>
        <?php else: ?>
          <canvas id="chartKat" style="max-height:300px"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-success"></i>Tren Peminjaman per Bulan</h6>
      </div>
      <div class="card-body">
        <?php if (empty($bulanStat)): ?>
          <p class="text-muted py-4 text-center">Belum ada data.</p>
        <?php else: ?>
          <canvas id="chartBulan" style="max-height:300px"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Status user & stok buku -->
<div class="row g-4">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-info"></i>Status Anggota</h6>
      </div>
      <div class="card-body">
        <?php
        $aktifC   = count(array_filter($users, fn($u) => $u['role']==='client' && $u['status']==='aktif'));
        $pendingC = count(array_filter($users, fn($u) => $u['role']==='client' && $u['status']==='pending'));
        $tolakC   = count(array_filter($users, fn($u) => $u['role']==='client' && $u['status']==='ditolak'));
        $total    = max($totalClient, 1);
        $bars = [['label'=>'Aktif','val'=>$aktifC,'color'=>'success'],['label'=>'Pending','val'=>$pendingC,'color'=>'warning'],['label'=>'Ditolak','val'=>$tolakC,'color'=>'danger']];
        foreach ($bars as $bar): ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between small mb-1">
            <span><?= $bar['label'] ?></span>
            <span class="fw-semibold"><?= $bar['val'] ?></span>
          </div>
          <div class="progress" style="height:8px">
            <div class="progress-bar bg-<?= $bar['color'] ?>" style="width:<?= round($bar['val']/$total*100) ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-stack me-2 text-warning"></i>Stok Buku Kritis (Stok ≤ 1)</h6>
      </div>
      <div class="card-body p-0" style="max-height:220px;overflow-y:auto">
        <div class="list-group list-group-flush">
          <?php
          $kritis = array_filter($buku, fn($b) => (int)$b['stok'] <= 1);
          if (empty($kritis)): ?>
            <div class="text-center py-4 text-success small"><i class="bi bi-check-circle me-1"></i>Semua stok mencukupi.</div>
          <?php else:
            foreach ($kritis as $b): ?>
            <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-4 py-2">
              <div>
                <div class="small fw-semibold"><?= htmlspecialchars($b['judul']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($b['kategori']) ?></div>
              </div>
              <span class="badge <?= (int)$b['stok'] === 0 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                Stok: <?= $b['stok'] ?>
              </span>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx1 = document.getElementById('chartKat');
  if (ctx1) {
    new Chart(ctx1, {
      type: 'pie',
      data: {
        labels: <?= $chartKatLabel ?>,
        datasets: [{ data: <?= $chartKatVal ?>, backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1','#fd7e14','#20c997'], borderWidth: 2, borderColor: '#fff' }]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } } } }
    });
  }
  const ctx2 = document.getElementById('chartBulan');
  if (ctx2) {
    new Chart(ctx2, {
      type: 'line',
      data: {
        labels: <?= $chartBlLabel ?>,
        datasets: [{ label: 'Peminjaman', data: <?= $chartBlVal ?>, fill: true, tension: 0.4, backgroundColor: 'rgba(13,110,253,0.12)', borderColor: '#0d6efd', pointBackgroundColor: '#0d6efd', pointRadius: 5 }]
      },
      options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
  }
});
</script>
