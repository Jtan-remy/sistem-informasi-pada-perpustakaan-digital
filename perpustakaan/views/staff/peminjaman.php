<?php // views/staff/peminjaman.php
$statusAktif = $status ?? '';
$baseUrl     = "index.php?page=staff/peminjaman&status={$statusAktif}";
$stMap       = \App\Models\Peminjaman::$STATUS_LABEL;
$statusList  = array_merge(['' => ['label'=>'Semua','badge'=>'secondary']], $stMap);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Manajemen Peminjaman</h4>
    <p class="text-muted small mb-0">Konfirmasi, update, dan pantau semua transaksi peminjaman.</p>
  </div>
</div>

<!-- Filter tab -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-2 d-flex gap-2 flex-wrap">
    <?php foreach ($statusList as $val => $info): ?>
    <a href="index.php?page=staff/peminjaman&status=<?= $val ?>"
       class="btn btn-sm <?= $statusAktif === $val ? 'btn-primary' : 'btn-outline-secondary' ?>">
      <?= $info['label'] ?>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (empty($items)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-inbox display-5"></i>
    <p class="mt-3">Tidak ada data peminjaman.</p>
  </div>
<?php else: ?>
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="px-4">Anggota</th>
          <th>Buku</th>
          <th>Tgl Pinjam</th>
          <th>Batas Kembali</th>
          <th>Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $p):
          $st      = $stMap[$p['status']] ?? ['label'=>$p['status'],'badge'=>'secondary'];
          $buku    = $p['_buku'];
          $user    = $p['_user'];
          $late    = $p['status']==='dipinjam' && strtotime($p['tgl_kembali']) < strtotime(date('Y-m-d'));
        ?>
        <tr class="<?= $late ? 'table-danger' : '' ?>">
          <td class="px-4">
            <div class="fw-semibold small"><?= htmlspecialchars($user['nama'] ?? '-') ?></div>
            <div class="text-muted small"><?= htmlspecialchars($user['email'] ?? '') ?></div>
          </td>
          <td>
            <div class="fw-semibold small"><?= htmlspecialchars($buku['judul'] ?? '-') ?></div>
            <div class="text-muted small"><?= htmlspecialchars($buku['kategori'] ?? '') ?></div>
          </td>
          <td class="small"><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
          <td class="small">
            <?= date('d M Y', strtotime($p['tgl_kembali'])) ?>
            <?php if ($late): ?>
              <br><span class="badge bg-danger">Terlambat!</span>
            <?php endif; ?>
          </td>
          <td><span class="badge bg-<?= $st['badge'] ?>"><?= $st['label'] ?></span></td>
          <td class="text-center">
            <?php if ($p['status'] === 'menunggu'): ?>
              <div class="d-flex gap-1 justify-content-center">
                <form action="index.php?action=update-status" method="POST" class="d-inline">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="status" value="dipinjam">
                  <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Setujui</button>
                </form>
                <form action="index.php?action=update-status" method="POST" class="d-inline">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="status" value="ditolak">
                  <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i> Tolak</button>
                </form>
              </div>
            <?php elseif ($p['status'] === 'dipinjam'): ?>
              <form action="index.php?action=update-status" method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <input type="hidden" name="status" value="dikembalikan">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-arrow-return-left me-1"></i>Kembalikan</button>
              </form>
            <?php else: ?>
              <span class="text-muted small">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../shared/pagination.php'; ?>
<?php endif; ?>
