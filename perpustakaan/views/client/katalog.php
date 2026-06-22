<?php // views/client/katalog.php
$q        = htmlspecialchars($query ?? '');
$katAktif = htmlspecialchars($kategori ?? '');
$sortAktif= $sort ?? 'judul';
$baseUrl  = "index.php?page=client/katalog&q={$q}&kategori={$katAktif}&sort={$sortAktif}";
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Katalog Buku</h4>
    <p class="text-muted small mb-0">Temukan dan pinjam buku favoritmu.</p>
  </div>
</div>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-3">
    <form method="GET" action="index.php" class="row g-2 align-items-end">
      <input type="hidden" name="page" value="client/katalog">
      <div class="col-md-5">
        <label class="form-label small fw-medium mb-1">Cari Buku</label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="q" class="form-control border-start-0 ps-0"
                 placeholder="Judul, penulis, atau ISBN…" value="<?= $q ?>">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-medium mb-1">Kategori</label>
        <select name="kategori" class="form-select">
          <option value="">Semua Kategori</option>
          <?php foreach (\App\Models\Buku::$KATEGORI as $kat): ?>
            <option value="<?= $kat ?>" <?= $katAktif === $kat ? 'selected' : '' ?>><?= $kat ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-medium mb-1">Urutkan</label>
        <select name="sort" class="form-select">
          <option value="judul"   <?= $sortAktif==='judul'   ? 'selected':'' ?>>Judul A-Z</option>
          <option value="penulis" <?= $sortAktif==='penulis' ? 'selected':'' ?>>Penulis</option>
          <option value="tahun"   <?= $sortAktif==='tahun'   ? 'selected':'' ?>>Terbaru</option>
          <option value="stok"    <?= $sortAktif==='stok'    ? 'selected':'' ?>>Stok Terbanyak</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="index.php?page=client/katalog" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>
</div>

<p class="text-muted small mb-3">Menampilkan <?= count($items) ?> dari <?= $total ?> buku</p>

<?php if (empty($items)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-search display-5"></i>
    <p class="mt-3">Tidak ada buku ditemukan.</p>
    <a href="index.php?page=client/katalog" class="btn btn-outline-primary">Reset Pencarian</a>
  </div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($items as $b): ?>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="card h-100 border-0 shadow-sm buku-card">
      <!-- Cover -->
      <div class="book-cover bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
           style="height:180px;overflow:hidden">
        <?php if (!empty($b['cover'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($b['cover']) ?>"
               class="w-100 h-100" style="object-fit:cover">
        <?php else: ?>
          <i class="bi bi-book text-primary" style="font-size:3rem"></i>
        <?php endif; ?>
      </div>
      <div class="card-body p-3 d-flex flex-column">
        <span class="badge bg-secondary bg-opacity-10 text-secondary mb-1 align-self-start small">
          <?= htmlspecialchars($b['kategori']) ?>
        </span>
        <h6 class="fw-semibold mb-1 lh-sm" style="font-size:.9rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
          <?= htmlspecialchars($b['judul']) ?>
        </h6>
        <p class="text-muted small mb-2"><?= htmlspecialchars($b['penulis']) ?></p>
        <div class="mt-auto d-flex align-items-center justify-content-between">
          <?php if ((int)$b['stok'] > 0): ?>
            <span class="badge bg-success-subtle text-success border border-success-subtle">
              <i class="bi bi-check-circle me-1"></i>Stok: <?= $b['stok'] ?>
            </span>
          <?php else: ?>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
              <i class="bi bi-x-circle me-1"></i>Habis
            </span>
          <?php endif; ?>
          <?php if ((int)$b['stok'] > 0): ?>
          <a href="index.php?page=client/reservasi&buku_id=<?= $b['id'] ?>"
             class="btn btn-primary btn-sm">Pinjam</a>
          <?php else: ?>
          <button class="btn btn-secondary btn-sm" disabled>Pinjam</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php
$baseUrl = "index.php?page=client/katalog&q={$q}&kategori={$katAktif}&sort={$sortAktif}";
require __DIR__ . '/../shared/pagination.php';
?>
<?php endif; ?>
