<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pembayaran</h4>
        <p class="text-muted small mb-0">Pantau dan verifikasi status pembayaran pesanan pelanggan.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Total Pembayaran Terkonfirmasi</div>
            <h3 class="fw-bold text-success mb-0">Rp <?= number_format($totalDiterima, 0, ',', '.') ?></h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Menunggu Verifikasi</div>
            <h3 class="fw-bold text-warning mb-0"><?= $menungguCount ?> transaksi</h3>
        </div>
    </div>
</div>

<div class="card-clean p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID / Ref</th>
                    <th>Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Metode / Channel</th>
                    <th>Tagihan</th>
                    <th>Dibayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembayaranList as $pb): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($pb['order_id_gateway'] ?? ('PAY-' . $pb['id_pembayaran'])) ?></td>
                        <td><?= esc($pb['kode_pesanan']) ?></td>
                        <td><?= esc($pb['nama_pelanggan'] ?? 'Walk-in') ?></td>
                        <td><?= esc($pb['nama_channel'] ?? $pb['metode_pembayaran']) ?></td>
                        <td>Rp <?= number_format($pb['jumlah_tagihan'], 0, ',', '.') ?></td>
                        <td class="fw-semibold">Rp <?= number_format($pb['jumlah_dibayar'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-primary-subtle text-primary"><?= esc($pb['status_pembayaran']) ?></span></td>
                        <td>
                            <?php if ($pb['status_pembayaran'] === 'MENUNGGU_VERIFIKASI'): ?>
                                <form action="<?= base_url('admin/pembayaran/konfirmasi') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_pembayaran" value="<?= $pb['id_pembayaran'] ?>">
                                    <button type="submit" name="aksi" value="TERKONFIRMASI" class="btn btn-sm btn-success">Konfirmasi</button>
                                    <button type="submit" name="aksi" value="DIBATALKAN" class="btn btn-sm btn-outline-danger">Tolak</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>