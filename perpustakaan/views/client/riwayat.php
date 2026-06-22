<?php // views/client/riwayat.php
$statusAktif = $status ?? '';
$baseUrl     = "index.php?page=client/riwayat&status={$statusAktif}";
$statusList  = [''=>'Semua','menunggu'=>'Menunggu','dipinjam'=>'Dipinjam','dikembalikan'=>'Dikembalikan','ditolak'=>'Ditolak'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Riwayat Peminjaman</h4>
    <p class="text-muted small mb-0">Histori semua buku yang pernah kamu pinjam.</p>
  </div>
</div>

<!-- Filter tab -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-2">
    <div class="d-flex gap-2 flex-wrap">
      <?php foreach ($statusList as $val => $label): ?>
      <a href="index.php?page=client/riwayat&status=<?= $val ?>"
         class="btn btn-sm <?= $statusAktif === $val ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <?= $label ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php if (empty($items)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-inbox display-5"></i>
    <p class="mt-3">Belum ada riwayat peminjaman.</p>
    <a href="index.php?page=client/katalog" class="btn btn-primary">Pinjam Buku Sekarang</a>
  </div>
<?php else: ?>
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="px-4">Buku</th>
          <th>Tgl Pinjam</th>
          <th>Batas Kembali</th>
          <th>Status</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $p):
          $buku    = $p['_buku'];
          $late    = $p['_terlambat'];
          $selisih = $p['_selisih'];
          $stMap   = \App\Models\Peminjaman::$STATUS_LABEL;
          $st      = $stMap[$p['status']] ?? ['label'=>$p['status'],'badge'=>'secondary'];
        ?>
        <tr>
          <td class="px-4">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center flex-shrink-0"
                   style="width:40px;height:54px">
                <?php if (!empty($buku['cover'])): ?>
                  <img src="<?= UPLOAD_URL . htmlspecialchars($buku['cover']) ?>" style="width:40px;height:54px;object-fit:cover" class="rounded">
                <?php else: ?>
                  <i class="bi bi-book text-primary"></i>
                <?php endif; ?>
              </div>
              <div>
                <div class="fw-semibold small"><?= htmlspecialchars($buku['judul'] ?? '-') ?></div>
                <div class="text-muted small"><?= htmlspecialchars($buku['penulis'] ?? '') ?></div>
              </div>
            </div>
          </td>
          <td class="small"><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
          <td class="small">
            <?= date('d M Y', strtotime($p['tgl_kembali'])) ?>
            <?php if ($p['status'] === 'dipinjam' && $late): ?>
              <br><span class="badge bg-danger">Terlambat <?= abs($selisih) ?> hari</span>
            <?php elseif ($p['status'] === 'dipinjam' && $selisih <= 3): ?>
              <br><span class="badge bg-warning text-dark"><?= $selisih ?> hari lagi</span>
            <?php endif; ?>
          </td>
          <td><span class="badge bg-<?= $st['badge'] ?>"><?= $st['label'] ?></span></td>
          <td class="small text-muted">
            <?php if ($p['tgl_aktual']): ?>
              Dikembalikan: <?= date('d M Y', strtotime($p['tgl_aktual'])) ?>
            <?php elseif (!empty($p['catatan'])): ?>
              <?= htmlspecialchars($p['catatan']) ?>
            <?php else: ?> — <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../shared/pagination.php'; ?>
<?php endif; ?>
