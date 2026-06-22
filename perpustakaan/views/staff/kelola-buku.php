<?php // views/staff/kelola-buku.php
$q        = htmlspecialchars($query ?? '');
$katAktif = htmlspecialchars($kategori ?? '');
$sortAktif= $sort ?? 'judul';
$baseUrl  = "index.php?page=staff/kelola-buku&q={$q}&kategori={$katAktif}&sort={$sortAktif}";
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Kelola Data Buku</h4>
    <p class="text-muted small mb-0">Tambah, edit, dan hapus koleksi buku perpustakaan.</p>
  </div>
  <a href="index.php?page=staff/tambah-buku" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Tambah Buku
  </a>
</div>

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-3">
    <form method="GET" action="index.php" class="row g-2 align-items-end">
      <input type="hidden" name="page" value="staff/kelola-buku">
      <div class="col-md-4">
        <label class="form-label small fw-medium mb-1">Cari</label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="q" class="form-control border-start-0 ps-0"
                 placeholder="Judul, penulis, ISBN…" value="<?= $q ?>">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-medium mb-1">Kategori</label>
        <select name="kategori" class="form-select">
          <option value="">Semua</option>
          <?php foreach (\App\Models\Buku::$KATEGORI as $kat): ?>
            <option value="<?= $kat ?>" <?= $katAktif===$kat ? 'selected':'' ?>><?= $kat ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-medium mb-1">Urut</label>
        <select name="sort" class="form-select">
          <option value="judul"   <?= $sortAktif==='judul'   ? 'selected':'' ?>>Judul A-Z</option>
          <option value="penulis" <?= $sortAktif==='penulis' ? 'selected':'' ?>>Penulis</option>
          <option value="tahun"   <?= $sortAktif==='tahun'   ? 'selected':'' ?>>Terbaru</option>
          <option value="stok"    <?= $sortAktif==='stok'    ? 'selected':'' ?>>Stok</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="index.php?page=staff/kelola-buku" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>
</div>

<p class="text-muted small mb-3">Menampilkan <?= count($items) ?> dari <?= $total ?> buku</p>

<?php if (empty($items)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-search display-5"></i>
    <p class="mt-3">Tidak ada buku ditemukan.</p>
  </div>
<?php else: ?>
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="px-4" style="width:40%">Buku</th>
          <th>Kategori</th>
          <th>ISBN</th>
          <th class="text-center">Stok</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $b): ?>
        <tr>
          <td class="px-4">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center flex-shrink-0"
                   style="width:42px;height:56px;overflow:hidden">
                <?php if (!empty($b['cover'])): ?>
                  <img src="<?= UPLOAD_URL . htmlspecialchars($b['cover']) ?>" style="width:42px;height:56px;object-fit:cover" class="rounded">
                <?php else: ?>
                  <i class="bi bi-book text-primary"></i>
                <?php endif; ?>
              </div>
              <div>
                <div class="fw-semibold small"><?= htmlspecialchars($b['judul']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($b['penulis']) ?> · <?= htmlspecialchars($b['tahun']) ?></div>
              </div>
            </div>
          </td>
          <td><span class="badge bg-secondary bg-opacity-10 text-secondary small"><?= htmlspecialchars($b['kategori']) ?></span></td>
          <td class="small text-muted"><?= htmlspecialchars($b['isbn']) ?></td>
          <td class="text-center">
            <?php if ((int)$b['stok'] > 0): ?>
              <span class="badge bg-success"><?= $b['stok'] ?></span>
            <?php else: ?>
              <span class="badge bg-danger">Habis</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
              <a href="index.php?page=staff/edit-buku&id=<?= $b['id'] ?>" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i>
              </a>
              <button type="button" class="btn btn-danger btn-sm"
                      onclick="konfirmasiHapus('<?= $b['id'] ?>','<?= htmlspecialchars(addslashes($b['judul'])) ?>')">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../shared/pagination.php'; ?>
<?php endif; ?>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold"><i class="bi bi-trash text-danger me-2"></i>Hapus Buku</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Apakah kamu yakin ingin menghapus buku <strong id="judulHapus"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
      </div>
      <div class="modal-footer border-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <a id="btnHapusConfirm" href="#" class="btn btn-danger">
          <i class="bi bi-trash me-1"></i>Ya, Hapus
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function konfirmasiHapus(id, judul) {
  document.getElementById('judulHapus').textContent = judul;
  document.getElementById('btnHapusConfirm').href = 'index.php?action=hapus-buku&id=' + id;
  new bootstrap.Modal(document.getElementById('modalHapus')).show();
}
</script>
