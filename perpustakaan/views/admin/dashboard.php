<?php // views/admin/dashboard.php
$chartKat   = json_encode(array_keys($stats['chart_data']));
$chartVal   = json_encode(array_values($stats['chart_data']));
$bulanStat  = \App\Models\Peminjaman::getStatistikBulanan();
$chartBulanLabel = json_encode(array_keys($bulanStat));
$chartBulanVal   = json_encode(array_values($bulanStat));
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Dashboard Admin</h4>
    <p class="text-muted small mb-0">Ringkasan statistik keseluruhan sistem perpustakaan.</p>
  </div>
  <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['label'=>'Total Anggota',  'val'=>$stats['total_user'],   'icon'=>'bi-people-fill',    'color'=>'primary', 'sub'=>'client aktif'],
    ['label'=>'Perlu Verifikasi','val'=>$stats['user_pending'],'icon'=>'bi-person-exclamation','color'=>'warning','sub'=>'akun pending'],
    ['label'=>'Total Buku',     'val'=>$stats['total_buku'],   'icon'=>'bi-journals',       'color'=>'success',  'sub'=>'koleksi'],
    ['label'=>'Sedang Dipinjam','val'=>$stats['dipinjam'],     'icon'=>'bi-bookmark-check-fill','color'=>'info','sub'=>'transaksi aktif'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm h-100 stat-card">
      <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3 mb-2">
          <div class="stat-icon bg-<?= $c['color'] ?> bg-opacity-10 text-<?= $c['color'] ?> rounded-3 p-2">
            <i class="bi <?= $c['icon'] ?> fs-4"></i>
          </div>
          <div class="fs-2 fw-bold lh-1"><?= $c['val'] ?></div>
        </div>
        <div class="fw-semibold small"><?= $c['label'] ?></div>
        <div class="text-muted" style="font-size:.75rem"><?= $c['sub'] ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Peminjaman per Kategori</h6>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <?php if (empty($stats['chart_data'])): ?>
          <p class="text-muted">Belum ada data.</p>
        <?php else: ?>
          <canvas id="chartKategori" style="max-height:280px"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-success"></i>Tren Peminjaman Bulanan</h6>
      </div>
      <div class="card-body">
        <?php if (empty($bulanStat)): ?>
          <p class="text-muted text-center py-4">Belum ada data.</p>
        <?php else: ?>
          <canvas id="chartBulanan" style="max-height:280px"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Aksi cepat -->
<div class="row g-3">
  <div class="col-md-4">
    <a href="index.php?page=admin/verifikasi" class="card border-0 shadow-sm text-decoration-none h-100 quick-action">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3"><i class="bi bi-person-check fs-3"></i></div>
        <div>
          <div class="fw-bold">Verifikasi Anggota</div>
          <div class="text-muted small"><?= $stats['user_pending'] ?> akun menunggu</div>
        </div>
        <i class="bi bi-chevron-right ms-auto text-muted"></i>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="index.php?page=admin/kelola-user" class="card border-0 shadow-sm text-decoration-none h-100 quick-action">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3"><i class="bi bi-people fs-3"></i></div>
        <div>
          <div class="fw-bold">Kelola User</div>
          <div class="text-muted small"><?= $stats['total_user'] ?> anggota aktif</div>
        </div>
        <i class="bi bi-chevron-right ms-auto text-muted"></i>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="index.php?page=admin/statistik" class="card border-0 shadow-sm text-decoration-none h-100 quick-action">
      <div class="card-body d-flex align-items-center gap-3 p-3">
        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3"><i class="bi bi-graph-up fs-3"></i></div>
        <div>
          <div class="fw-bold">Statistik Lengkap</div>
          <div class="text-muted small">Analisis data sistem</div>
        </div>
        <i class="bi bi-chevron-right ms-auto text-muted"></i>
      </div>
    </a>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Pie chart kategori
  const ctxK = document.getElementById('chartKategori');
  if (ctxK) {
    new Chart(ctxK, {
      type: 'doughnut',
      data: {
        labels: <?= $chartKat ?>,
        datasets: [{
          data: <?= $chartVal ?>,
          backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1','#fd7e14','#20c997'],
          borderWidth: 2,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } } }
      }
    });
  }

  // Bar chart bulanan
  const ctxB = document.getElementById('chartBulanan');
  if (ctxB) {
    new Chart(ctxB, {
      type: 'bar',
      data: {
        labels: <?= $chartBulanLabel ?>,
        datasets: [{
          label: 'Peminjaman',
          data: <?= $chartBulanVal ?>,
          backgroundColor: 'rgba(13,110,253,0.75)',
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
    });
  }
});
</script>
