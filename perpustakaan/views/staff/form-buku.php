<?php // views/staff/form-buku.php
$isEdit = isset($buku) && $buku !== null;
$title  = $isEdit ? 'Edit Buku' : 'Tambah Buku Baru';
?>
<div class="d-flex align-items-center gap-3 mb-4">
  <a href="index.php?page=staff/kelola-buku" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i>
  </a>
  <div>
    <h4 class="fw-bold mb-0"><?= $title ?></h4>
    <p class="text-muted small mb-0"><?= $isEdit ? 'Perbarui informasi buku.' : 'Isi formulir untuk menambah koleksi buku baru.' ?></p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form action="index.php?action=simpan-buku" method="POST" enctype="multipart/form-data" novalidate>
          <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($buku['id']) ?>">
          <?php endif; ?>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-medium">Judul Buku <span class="text-danger">*</span></label>
              <input type="text" name="judul" class="form-control"
                     value="<?= htmlspecialchars($buku['judul'] ?? '') ?>" required maxlength="200">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Penulis <span class="text-danger">*</span></label>
              <input type="text" name="penulis" class="form-control"
                     value="<?= htmlspecialchars($buku['penulis'] ?? '') ?>" required maxlength="100">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Penerbit <span class="text-danger">*</span></label>
              <input type="text" name="penerbit" class="form-control"
                     value="<?= htmlspecialchars($buku['penerbit'] ?? '') ?>" required maxlength="100">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">Tahun Terbit <span class="text-danger">*</span></label>
              <input type="number" name="tahun" class="form-control"
                     value="<?= htmlspecialchars($buku['tahun'] ?? '') ?>"
                     min="1900" max="<?= date('Y') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">ISBN <span class="text-danger">*</span></label>
              <input type="text" name="isbn" class="form-control"
                     value="<?= htmlspecialchars($buku['isbn'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-medium">Stok <span class="text-danger">*</span></label>
              <input type="number" name="stok" class="form-control"
                     value="<?= htmlspecialchars($buku['stok'] ?? '0') ?>" min="0" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
              <select name="kategori" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <?php foreach (\App\Models\Buku::$KATEGORI as $kat): ?>
                  <option value="<?= $kat ?>" <?= ($buku['kategori'] ?? '') === $kat ? 'selected' : '' ?>><?= $kat ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="4"
                        placeholder="Sinopsis atau keterangan singkat tentang buku ini…"><?= htmlspecialchars($buku['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">Cover Buku</label>
              <input type="file" name="cover" class="form-control" accept="image/*" onchange="previewCover(this)">
              <div class="form-text">Format JPG/PNG/WEBP, maks 2MB.</div>
            </div>
          </div>

          <hr class="my-4">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
              <i class="bi bi-save me-2"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Buku' ?>
            </button>
            <a href="index.php?page=staff/kelola-buku" class="btn btn-outline-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Preview cover -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0">Preview Cover</h6>
      </div>
      <div class="card-body text-center">
        <div id="coverPreview" class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center mx-auto mb-3"
             style="width:160px;height:220px;overflow:hidden">
          <?php if ($isEdit && !empty($buku['cover'])): ?>
            <img id="coverImg" src="<?= UPLOAD_URL . htmlspecialchars($buku['cover']) ?>"
                 style="width:160px;height:220px;object-fit:cover" class="rounded">
          <?php else: ?>
            <div id="coverPlaceholder" class="text-center">
              <i class="bi bi-image text-primary fs-1"></i>
              <p class="text-muted small mt-1 mb-0">Belum ada cover</p>
            </div>
          <?php endif; ?>
        </div>
        <p class="text-muted small">Upload gambar untuk melihat preview.</p>
      </div>
    </div>
  </div>
</div>

<script>
function previewCover(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const container = document.getElementById('coverPreview');
      container.innerHTML = `<img src="${e.target.result}" style="width:160px;height:220px;object-fit:cover" class="rounded">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
