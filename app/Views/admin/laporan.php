<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Laporan Penjualan</h4>
        <p class="text-muted small mb-0">Khusus Owner &mdash; ringkasan pendapatan, pesanan, dan pengeluaran pembelian.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/laporan/export/pdf') ?>" class="btn btn-outline-secondary btn-sm bg-white">
            <i class="bi bi-file-earmark-pdf me-1"></i> Ekspor PDF
        </a>
        <a href="<?= base_url('admin/laporan/export/excel') ?>" class="btn btn-primary-erika btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Total Pendapatan</div>
            <h4 class="fw-bold mb-1">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h4>
            <span class="badge bg-success-subtle text-success small">&#9650; 14% vs Okt</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Total Pesanan</div>
            <h4 class="fw-bold mb-1"><?= number_format($totalPesanan) ?></h4>
            <span class="badge bg-success-subtle text-success small">&#9650; 9% vs Okt</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Pengeluaran Pembelian</div>
            <h4 class="fw-bold mb-1">Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h4>
            <span class="badge bg-danger-subtle text-danger small">&#9660; 3% vs Okt</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Estimasi Laba Kotor</div>
            <h4 class="fw-bold mb-1">Rp <?= number_format($labaKotor, 0, ',', '.') ?></h4>
            <span class="text-muted small">Pendapatan &minus; pembelian</span>
        </div>
    </div>
</div>

<div class="card-clean p-3">
    <h6 class="fw-bold mb-3">Rekapitulasi Per Layanan</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small mb-0">
            <thead class="table-light">
                <tr>
                    <th>Layanan</th>
                    <th>Pesanan</th>
                    <th>Pendapatan</th>
                    <th>Porsi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekapLayanan as $rl): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($rl['nama']) ?></td>
                        <td><?= esc($rl['pesanan']) ?></td>
                        <td>Rp <?= number_format($rl['pendapatan'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-primary-subtle text-primary"><?= esc($rl['porsi']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>