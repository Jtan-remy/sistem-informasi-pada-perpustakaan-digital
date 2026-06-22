<?php // views/client/profil.php
$u = $user;
?>
<div class="mb-4">
  <h4 class="fw-bold mb-0">Profil Saya</h4>
  <p class="text-muted small mb-0">Kelola informasi akun kamu.</p>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm text-center p-4">
      <div class="mb-3">
        <?php if (!empty($u['foto'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($u['foto']) ?>"
               class="rounded-circle border" style="width:96px;height:96px;object-fit:cover">
        <?php else: ?>
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
               style="width:96px;height:96px;font-size:2.5rem">
            <?= strtoupper(substr($u['nama'],0,1)) ?>
          </div>
        <?php endif; ?>
      </div>
      <h5 class="fw-bold mb-0"><?= htmlspecialchars($u['nama']) ?></h5>
      <p class="text-muted small mb-2"><?= htmlspecialchars($u['email']) ?></p>
      <span class="badge bg-primary">Member Aktif</span>
      <hr>
      <div class="text-start small text-muted">
        <div class="d-flex gap-2 mb-1"><i class="bi bi-telephone"></i><?= htmlspecialchars($u['telp'] ?: '-') ?></div>
        <div class="d-flex gap-2 mb-1"><i class="bi bi-geo-alt"></i><?= htmlspecialchars($u['alamat'] ?: '-') ?></div>
        <div class="d-flex gap-2"><i class="bi bi-calendar3"></i>Bergabung: <?= date('d M Y', strtotime($u['created'])) ?></div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil</h6>
      </div>
      <div class="card-body">
        <form action="index.php?action=update-profil" method="POST" enctype="multipart/form-data" novalidate>

          <div class="mb-3">
            <label class="form-label fw-medium">Foto Profil</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
            <div class="form-text">Format JPG/PNG/WEBP, maks 2MB.</div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control"
                     value="<?= htmlspecialchars($u['nama']) ?>" required minlength="3">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Email</label>
              <input type="email" class="form-control bg-light"
                     value="<?= htmlspecialchars($u['email']) ?>" disabled>
              <div class="form-text">Email tidak bisa diubah.</div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">No. Telepon <span class="text-danger">*</span></label>
            <input type="text" name="telp" class="form-control"
                   value="<?= htmlspecialchars($u['telp']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($u['alamat']) ?></textarea>
          </div>

          <hr>
          <h6 class="fw-semibold mb-3 text-muted">Ganti Password (opsional)</h6>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-medium">Password Baru</label>
              <input type="password" name="password" class="form-control"
                     placeholder="Kosongkan jika tidak diganti" minlength="6">
            </div>
          </div>

          <button type="submit" class="btn btn-primary px-4 fw-semibold">
            <i class="bi bi-save me-2"></i>Simpan Perubahan
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
