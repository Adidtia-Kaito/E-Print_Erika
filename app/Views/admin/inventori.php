<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Bahan & Produk</h4>
        <p class="text-muted small mb-0">Pantau stok bahan baku dan produk ATK serta riwayat mutasi stok.</p>
    </div>
    <button class="btn btn-primary-erika btn-sm" data-bs-toggle="modal" data-bs-target="#modalBahan">
        Bahan Baru <i class="bi bi-plus-lg ms-1"></i>
    </button>
</div>

<div class="card-clean p-3 mb-4">
    <h6 class="fw-bold mb-3">Stok Bahan Baku</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Bahan</th>
                    <th>Jenis</th>
                    <th>Stok</th>
                    <th>Stok Min.</th>
                    <th>Harga Beli Terakhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bahanList as $b): ?>
                    <?php $menipis = (int) $b['stok'] <= (int) $b['stok_minimum']; ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($b['kode_bahan']) ?></td>
                        <td class="fw-semibold"><?= esc($b['nama_bahan']) ?></td>
                        <td><?= esc($b['jenis_bahan']) ?></td>
                        <td class="fw-semibold"><?= esc($b['stok']) ?> <?= esc($b['satuan']) ?></td>
                        <td><?= esc($b['stok_minimum']) ?> <?= esc($b['satuan']) ?></td>
                        <td>Rp <?= number_format($b['harga_beli_terakhir'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($menipis): ?>
                                <span class="badge bg-danger-subtle text-danger">Menipis</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success">Aman</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card-clean p-3">
            <h6 class="fw-bold mb-3">Stok Opname / Penyesuaian</h6>
            <form action="<?= base_url('admin/inventori/penyesuaian') ?>" method="post" class="small">
                <?= csrf_field() ?>
                <div class="mb-2">
                    <label class="form-label">Pilih Bahan Baku</label>
                    <select name="id_bahan" class="form-select form-select-sm" required>
                        <?php foreach ($bahanList as $b): ?>
                            <option value="<?= $b['id_bahan'] ?>"><?= esc($b['nama_bahan']) ?> (Saat ini: <?= $b['stok'] ?> <?= $b['satuan'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Jumlah Stok Aktual Terbaru</label>
                    <input type="number" name="stok_baru" class="form-control form-control-sm" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="mis. Stok opname bulanan">
                </div>
                <button type="submit" class="btn btn-primary-erika w-100">Simpan Penyesuaian</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-clean p-3">
            <h6 class="fw-bold mb-3">Mutasi Stok Terbaru</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Bahan</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Sebelum &rarr; Sesudah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mutasiList as $m): ?>
                            <tr>
                                <td><?= date('d M H:i', strtotime($m['waktu_mutasi'])) ?></td>
                                <td class="fw-semibold"><?= esc($m['nama_bahan']) ?></td>
                                <td><span class="badge bg-info-subtle text-info"><?= esc($m['jenis_mutasi']) ?></span></td>
                                <td><?= esc($m['jumlah']) ?></td>
                                <td><?= esc($m['stok_sebelum']) ?> &rarr; <strong><?= esc($m['stok_sesudah']) ?></strong></td>
                                <td><?= esc($m['keterangan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Bahan Baku -->
<div class="modal fade" id="modalBahan" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/inventori/store-bahan') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Bahan Baku</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <div class="mb-2">
                    <label class="form-label">Kode Bahan</label>
                    <input type="text" name="kode_bahan" class="form-control form-control-sm" placeholder="mis. BHN-020" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Nama Bahan</label>
                    <input type="text" name="nama_bahan" class="form-control form-control-sm" placeholder="mis. Kertas A4 70 gr" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label">Jenis Bahan</label>
                        <input type="text" name="jenis_bahan" class="form-control form-control-sm" value="Kertas" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control form-control-sm" value="rim" required>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stok" class="form-control form-control-sm" value="0" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="stok_minimum" class="form-control form-control-sm" value="5" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="harga_beli_terakhir" class="form-control form-control-sm" value="0" required>
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