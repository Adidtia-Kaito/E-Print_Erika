<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pengguna</h4>
        <p class="text-muted small mb-0">Khusus Owner &mdash; kelola akun admin/owner dan data pelanggan.</p>
    </div>
    <button class="btn btn-primary-erika btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdmin">
        Admin Baru <i class="bi bi-plus-lg ms-1"></i>
    </button>
</div>

<div class="card-clean p-3 mb-4">
    <h6 class="fw-bold mb-3">Akun Admin & Owner</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. WhatsApp</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adminList as $ad): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($ad['username']) ?></td>
                        <td><?= esc($ad['nama_lengkap']) ?></td>
                        <td><?= esc($ad['email']) ?></td>
                        <td><?= esc($ad['no_wa']) ?></td>
                        <td><span class="badge bg-primary-subtle text-primary"><?= esc($ad['role']) ?></span></td>
                        <td>
                            <?php if ((int) $ad['status_aktif'] === 1): ?>
                                <span class="badge bg-success-subtle text-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?= base_url('admin/pengguna/toggle-status/' . $ad['id_admin']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Ubah</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card-clean p-3">
    <h6 class="fw-bold mb-3">Pelanggan Terdaftar</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. WhatsApp</th>
                    <th>Total Pesanan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelangganList as $pl): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($pl['nama_lengkap']) ?></td>
                        <td><?= esc($pl['email']) ?></td>
                        <td><?= esc($pl['no_wa']) ?></td>
                        <td><?= esc($pl['total_pesanan']) ?> pesanan</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalAdmin" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/pengguna/store-admin') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Admin / Owner Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <div class="mb-2">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control form-control-sm" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="ADMIN">ADMIN</option>
                            <option value="OWNER">OWNER</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" name="password" class="form-control form-control-sm" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-erika btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>