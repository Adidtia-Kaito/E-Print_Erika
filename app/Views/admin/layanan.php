<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Layanan & Opsi</h4>
        <p class="text-muted small mb-0">Kelola kategori layanan, tarif dasar, opsi tambahan, dan bahan baku dipakai.</p>
    </div>
    <button class="btn btn-primary-erika btn-sm" data-bs-toggle="modal" data-bs-target="#modalLayanan">
        Layanan Baru <i class="bi bi-plus-lg ms-1"></i>
    </button>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($kategoriList as $kl): ?>
        <div class="col-md-3">
            <div class="card-clean p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small"><?= esc($kl['nama_kategori']) ?></span>
                    <span class="badge bg-success-subtle text-success">Aktif</span>
                </div>
                <div class="text-muted small">/<?= esc($kl['slug']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card-clean p-3 mb-4">
    <h6 class="fw-bold mb-3">Daftar Master Layanan</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Layanan</th>
                    <th>Kategori</th>
                    <th>Harga Dasar</th>
                    <th>Satuan</th>
                    <th>Estimasi</th>
                    <th>File</th>
                    <th>Dimensi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($layananList as $l): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($l['kode_layanan']) ?></td>
                        <td class="fw-semibold"><?= esc($l['nama_layanan']) ?></td>
                        <td><?= esc($l['nama_kategori']) ?></td>
                        <td>Rp <?= number_format($l['harga_dasar'], 0, ',', '.') ?></td>
                        <td><?= esc($l['satuan_hitung']) ?></td>
                        <td><?= esc($l['estimasi_durasi_menit']) ?> mnt</td>
                        <td><?= (int) $l['butuh_file'] === 1 ? 'Ya' : 'Tidak' ?></td>
                        <td><?= (int) $l['butuh_dimensi'] === 1 ? 'Ya' : 'Tidak' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-clean p-3">
            <h6 class="fw-bold mb-3">Opsi Layanan</h6>
            <table class="table table-sm align-middle small mb-0">
                <thead class="table-light">
                    <tr><th>Opsi</th><th>Grup</th><th>Tipe</th><th>Tambahan</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($opsiList as $o): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($o['nama_opsi']) ?> <small class="text-muted">(<?= esc($o['nama_layanan']) ?>)</small></td>
                            <td><?= esc($o['grup_opsi']) ?></td>
                            <td><?= esc($o['tipe_harga']) ?></td>
                            <td>Rp <?= number_format($o['harga_tambahan'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-clean p-3">
            <h6 class="fw-bold mb-3">Pemakaian Bahan per Layanan</h6>
            <table class="table table-sm align-middle small mb-0">
                <thead class="table-light">
                    <tr><th>Layanan</th><th>Bahan</th><th>Pemakaian</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($bahanResep as $br): ?>
                        <tr>
                            <td><?= esc($br['nama_layanan']) ?></td>
                            <td class="fw-semibold"><?= esc($br['nama_bahan']) ?></td>
                            <td><?= esc($br['jumlah_pemakaian']) ?> <?= esc($br['satuan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Layanan -->
<div class="modal fade" id="modalLayanan" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/layanan/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Layanan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <div class="mb-2">
                    <label class="form-label">Kode Layanan</label>
                    <input type="text" name="kode_layanan" class="form-control form-control-sm" placeholder="mis. PRT-A3" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Nama Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control form-control-sm" placeholder="mis. Cetak Poster A3" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Kategori</label>
                    <select name="id_kategori_layanan" class="form-select form-select-sm" required>
                        <?php foreach ($kategoriList as $kl): ?>
                            <option value="<?= $kl['id_kategori_layanan'] ?>"><?= esc($kl['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Harga Dasar (Rp)</label>
                        <input type="number" name="harga_dasar" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Satuan Hitung</label>
                        <input type="text" name="satuan_hitung" class="form-control form-control-sm" value="lembar" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Estimasi Durasi (Menit)</label>
                    <input type="number" name="estimasi_durasi_menit" class="form-control form-control-sm" value="30" required>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="butuh_file" value="1" id="bf">
                    <label class="form-check-label" for="bf">Memerlukan unggah file</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="butuh_dimensi" value="1" id="bd">
                    <label class="form-check-label" for="bd">Memerlukan input dimensi (panjang/lebar)</label>
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