<?php // views/admin/verifikasi.php ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Verifikasi Anggota Baru</h4>
    <p class="text-muted small mb-0">Setujui atau tolak pendaftaran anggota baru.</p>
  </div>
  <span class="badge bg-warning fs-6"><?= count($pending) ?> Menunggu</span>
</div>

<?php if (empty($pending)): ?>
  <div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
      <i class="bi bi-person-check display-4 text-success"></i>
      <p class="mt-3 fw-semibold">Semua anggota sudah terverifikasi!</p>
      <p class="small">Tidak ada pendaftaran baru yang perlu ditinjau.</p>
    </div>
  </div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($pending as $u): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold"
               style="width:48px;height:48px;font-size:1.2rem">
            <?= strtoupper(substr($u['nama'],0,1)) ?>
          </div>
          <div>
            <div class="fw-bold"><?= htmlspecialchars($u['nama']) ?></div>
            <div class="text-muted small"><?= htmlspecialchars($u['email']) ?></div>
          </div>
        </div>
        <div class="mb-3 small">
          <div class="d-flex gap-2 mb-1 text-muted"><i class="bi bi-telephone"></i><?= htmlspecialchars($u['telp'] ?: '-') ?></div>
          <div class="d-flex gap-2 mb-1 text-muted"><i class="bi bi-geo-alt"></i><?= htmlspecialchars($u['alamat'] ?: '-') ?></div>
          <div class="d-flex gap-2 text-muted"><i class="bi bi-calendar3"></i>Daftar: <?= date('d M Y', strtotime($u['created'])) ?></div>
        </div>
        <div class="d-flex gap-2">
          <form action="index.php?action=verifikasi-user" method="POST" class="flex-grow-1">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button type="submit" class="btn btn-success w-100 btn-sm fw-semibold">
              <i class="bi bi-check-lg me-1"></i>Setujui
            </button>
          </form>
          <form action="index.php?action=verifikasi-user" method="POST" class="flex-grow-1">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
              <i class="bi bi-x-lg me-1"></i>Tolak
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
