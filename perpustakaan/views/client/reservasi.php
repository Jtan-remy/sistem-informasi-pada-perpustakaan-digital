<?php // views/client/reservasi.php ?>
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="index.php?page=client/katalog" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i>
  </a>
  <div>
    <h4 class="fw-bold mb-0">Form Reservasi Buku</h4>
    <p class="text-muted small mb-0">Isi detail peminjaman buku</p>
  </div>
</div>

<?php if (!$buku): ?>
  <div class="alert alert-warning">Buku tidak ditemukan. <a href="index.php?page=client/katalog">Kembali ke katalog</a></div>
<?php elseif ((int)$buku['stok'] <= 0): ?>
  <div class="alert alert-danger">
    <i class="bi bi-x-circle me-2"></i>Maaf, stok buku ini sudah habis.
    <a href="index.php?page=client/katalog" class="ms-2">Cari buku lain</a>
  </div>
<?php else: ?>
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="book-cover bg-primary bg-opacity-10 d-flex align-items-center justify-content-center rounded-top"
           style="height:220px;overflow:hidden">
        <?php if (!empty($buku['cover'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($buku['cover']) ?>" class="w-100 h-100" style="object-fit:cover">
        <?php else: ?>
          <i class="bi bi-book text-primary" style="font-size:4rem"></i>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <h5 class="fw-bold"><?= htmlspecialchars($buku['judul']) ?></h5>
        <p class="text-muted mb-2"><?= htmlspecialchars($buku['penulis']) ?></p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="badge bg-secondary bg-opacity-10 text-secondary"><?= htmlspecialchars($buku['kategori']) ?></span>
          <span class="badge bg-success-subtle text-success border border-success-subtle">Stok: <?= $buku['stok'] ?></span>
        </div>
        <table class="table table-sm table-borderless small mb-0">
          <tr><td class="text-muted">Penerbit</td><td><?= htmlspecialchars($buku['penerbit']) ?></td></tr>
          <tr><td class="text-muted">Tahun</td><td><?= htmlspecialchars($buku['tahun']) ?></td></tr>
          <tr><td class="text-muted">ISBN</td><td><?= htmlspecialchars($buku['isbn']) ?></td></tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-bookmark-plus me-2 text-primary"></i>Detail Peminjaman</h6>
      </div>
      <div class="card-body">
        <form action="index.php?action=ajukan-pinjam" method="POST">
          <input type="hidden" name="buku_id" value="<?= htmlspecialchars($buku['id']) ?>">

          <!-- Info peminjam -->
          <div class="alert alert-info d-flex gap-2 mb-4">
            <i class="bi bi-person-circle fs-5"></i>
            <div>
              <div class="fw-semibold small">Peminjam</div>
              <div class="small"><?= htmlspecialchars($auth['nama']) ?> — <?= htmlspecialchars($auth['email']) ?></div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-medium">Tanggal Pinjam</label>
              <input type="text" class="form-control bg-light" value="<?= date('d M Y') ?>" readonly>
              <div class="form-text">Otomatis hari ini</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Batas Pengembalian</label>
              <input type="text" class="form-control bg-light"
                     value="<?= date('d M Y', strtotime('+14 days')) ?>" readonly>
              <div class="form-text">14 hari dari tanggal pinjam</div>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium">Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="3"
                      placeholder="Tujuan peminjaman, catatan khusus, dll."></textarea>
          </div>

          <div class="alert alert-warning d-flex gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div class="small">
              Pastikan kamu mengambil buku dalam <strong>1x24 jam</strong> setelah disetujui.
              Keterlambatan pengembalian akan dicatat sebagai pelanggaran.
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
              <i class="bi bi-send me-2"></i>Ajukan Reservasi
            </button>
            <a href="index.php?page=client/katalog" class="btn btn-outline-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
