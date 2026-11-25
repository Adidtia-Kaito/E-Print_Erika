<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pesanan</h4>
        <p class="text-muted small mb-0">Kelola semua pesanan pelanggan &mdash; ubah status dan pantau pembayaran.</p>
    </div>
    <a href="<?= base_url('admin/laporan/export/excel') ?>" class="btn btn-outline-secondary btn-sm bg-white">
        <i class="bi bi-download me-1"></i> Ekspor
    </a>
</div>

<div class="card-clean p-3 mb-4">
    <form method="get" class="row g-2">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari kode pesanan, nama pelanggan, atau nama pesanan..." value="<?= esc($keyword) ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="SEMUA" <?= $statusFilter === 'SEMUA' ? 'selected' : '' ?>>Semua Status</option>
                <option value="MENUNGGU_KONFIRMASI" <?= $statusFilter === 'MENUNGGU_KONFIRMASI' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                <option value="DIPROSES_DICETAK" <?= $statusFilter === 'DIPROSES_DICETAK' ? 'selected' : '' ?>>Diproses / Dicetak</option>
                <option value="SIAP_DIAMBIL" <?= $statusFilter === 'SIAP_DIAMBIL' ? 'selected' : '' ?>>Siap Diambil</option>
                <option value="SELESAI" <?= $statusFilter === 'SELESAI' ? 'selected' : '' ?>>Selesai</option>
                <option value="DIBATALKAN" <?= $statusFilter === 'DIBATALKAN' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="tipe" class="form-select form-select-sm">
                <option value="SEMUA" <?= $tipeFilter === 'SEMUA' ? 'selected' : '' ?>>Semua Tipe</option>
                <option value="ONLINE" <?= $tipeFilter === 'ONLINE' ? 'selected' : '' ?>>Online</option>
                <option value="WALKIN" <?= $tipeFilter === 'WALKIN' ? 'selected' : '' ?>>Walk-in</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-erika w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card-clean p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Pesanan</th>
                    <th>Tipe</th>
                    <th>Total</th>
                    <th>Status Pesanan</th>
                    <th>Pembayaran</th>
                    <th>Waktu Pesan</th>
                    <th>Ubah Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesananList as $p): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($p['kode_pesanan']) ?></td>
                        <td><?= esc($p['nama_pelanggan'] ?? 'Walk-in') ?></td>
                        <td><?= esc($p['nama_pesanan']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= esc($p['tipe_pesanan']) ?></span></td>
                        <td class="fw-semibold">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-primary-subtle text-primary"><?= esc($p['status_pesanan']) ?></span></td>
                        <td><span class="badge bg-success-subtle text-success"><?= esc($p['status_pembayaran']) ?></span></td>
                        <td><?= date('d M H:i', strtotime($p['waktu_pesan'])) ?></td>
                        <td>
                            <form action="<?= base_url('admin/pesanan/update-status') ?>" method="post" class="d-flex gap-1">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_pesanan" value="<?= $p['id_pesanan'] ?>">
                                <select name="status_pesanan" class="form-select form-select-sm" style="width:145px;">
                                    <?php foreach (['MENUNGGU_KONFIRMASI','DIPROSES_DICETAK','PERLU_REVISI','SIAP_DIAMBIL','SELESAI','DIBATALKAN'] as $st): ?>
                                        <option value="<?= $st ?>" <?= $p['status_pesanan'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary-erika">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>