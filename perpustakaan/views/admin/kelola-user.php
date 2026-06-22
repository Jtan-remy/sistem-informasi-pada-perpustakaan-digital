<?php // views/admin/kelola-user.php
$baseUrl = "index.php?page=admin/kelola-user";
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-0">Kelola User</h4>
    <p class="text-muted small mb-0">Tambah, edit, dan hapus akun pengguna sistem.</p>
  </div>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
    <i class="bi bi-person-plus me-1"></i>Tambah User
  </button>
</div>

<?php if (empty($items)): ?>
  <div class="text-center py-5 text-muted"><p>Tidak ada user.</p></div>
<?php else: ?>
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="px-4">Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Bergabung</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $u):
          $roleBadge = match($u['role']) { 'admin'=>'danger', 'staff'=>'success', default=>'primary' };
          $stBadge   = $u['status'] === 'aktif' ? 'success' : ($u['status'] === 'pending' ? 'warning' : 'secondary');
        ?>
        <tr>
          <td class="px-4">
            <div class="d-flex align-items-center gap-2">
              <div class="rounded-circle bg-<?= $roleBadge ?> bg-opacity-10 text-<?= $roleBadge ?> d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                   style="width:36px;height:36px;font-size:.9rem">
                <?= strtoupper(substr($u['nama'],0,1)) ?>
              </div>
              <div class="fw-semibold small"><?= htmlspecialchars($u['nama']) ?></div>
            </div>
          </td>
          <td class="small text-muted"><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge bg-<?= $roleBadge ?>"><?= ucfirst($u['role']) ?></span></td>
          <td><span class="badge bg-<?= $stBadge ?>"><?= ucfirst($u['status']) ?></span></td>
          <td class="small text-muted"><?= date('d M Y', strtotime($u['created'])) ?></td>
          <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
              <button type="button" class="btn btn-warning btn-sm"
                      onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                <i class="bi bi-pencil"></i>
              </button>
              <?php if ($u['id'] !== $auth['id']): ?>
              <a href="index.php?action=hapus-user&id=<?= $u['id'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Hapus user ini?')">
                <i class="bi bi-trash"></i>
              </a>
              <?php endif; ?>
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

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0">
        <h6 class="modal-title fw-bold" id="modalUserTitle"><i class="bi bi-person-plus me-2"></i>Tambah User</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formUser" action="index.php?action=simpan-user" method="POST">
          <input type="hidden" name="id" id="userId">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="nama" id="userNama" class="form-control" required minlength="3">
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" id="userEmail" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Role</label>
              <select name="role" id="userRole" class="form-select">
                <option value="client">Client</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Status</label>
              <select name="status" id="userStatus" class="form-select">
                <option value="aktif">Aktif</option>
                <option value="pending">Pending</option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">No. Telepon</label>
              <input type="text" name="telp" id="userTelp" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">Password <span id="pwHint" class="text-muted small">(wajib untuk user baru)</span></label>
              <input type="password" name="password" id="userPassword" class="form-control" placeholder="Kosongkan jika tidak diubah">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" form="formUser" class="btn btn-primary fw-semibold">
          <i class="bi bi-save me-1"></i>Simpan
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function editUser(u) {
  document.getElementById('modalUserTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit User';
  document.getElementById('userId').value    = u.id;
  document.getElementById('userNama').value  = u.nama;
  document.getElementById('userEmail').value = u.email;
  document.getElementById('userRole').value  = u.role;
  document.getElementById('userStatus').value= u.status;
  document.getElementById('userTelp').value  = u.telp || '';
  document.getElementById('userPassword').value = '';
  document.getElementById('pwHint').textContent = '(kosongkan jika tidak diubah)';
  new bootstrap.Modal(document.getElementById('modalTambahUser')).show();
}
</script>
