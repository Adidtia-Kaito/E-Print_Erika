<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <p class="text-muted small mb-0">Ringkasan operasional Percetakan Erika &mdash; <?= date('d M Y') ?></p>
    </div>
    <div class="d-flex gap-2">
        <?php if (session()->get('admin_role') === 'OWNER'): ?>
            <a href="<?= base_url('admin/laporan') ?>" class="btn btn-outline-secondary btn-sm bg-white">
                <i class="bi bi-download me-1"></i> Unduh Laporan
            </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/pesanan') ?>" class="btn btn-primary-erika btn-sm">
            Pesanan Baru <i class="bi bi-plus-lg ms-1"></i>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Total Pesanan</div>
            <h3 class="fw-bold mb-1"><?= number_format($totalPesanan) ?></h3>
            <span class="badge bg-success-subtle text-success small">&#9650; 12% vs kemarin</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Pendapatan Lunas</div>
            <h3 class="fw-bold mb-1">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h3>
            <span class="badge bg-success-subtle text-success small">&#9650; 8% vs kemarin</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Antrean Aktif</div>
            <h3 class="fw-bold mb-1"><?= number_format($antreanAktif) ?></h3>
            <span class="text-muted small">Estimasi ~35 mnt/antrean</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="text-muted small mb-1">Stok Menipis</div>
            <h3 class="fw-bold text-danger mb-1"><?= count($stokMenipis) ?> bahan</h3>
            <span class="text-danger small">Perlu dibeli segera</span>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card-clean p-3 h-100">
            <h6 class="fw-bold mb-3">Pendapatan 7 Hari Terakhir (Juta Rp)</h6>
            <canvas id="chartPendapatan" height="140"></canvas>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-clean p-3 h-100">
            <h6 class="fw-bold mb-3">Pesanan per Layanan</h6>
            <div class="row align-items-center">
                <div class="col-6">
                    <canvas id="chartLayanan"></canvas>
                </div>
                <div class="col-6">
                    <?php foreach ($layananStats as $ls): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span>
                                <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:<?= $ls['warna'] ?>"></span>
                                <?= esc($ls['nama']) ?>
                            </span>
                            <strong><?= $ls['persen'] ?>%</strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-clean p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Pesanan Terbaru</h6>
                <a href="<?= base_url('admin/pesanan') ?>" class="small text-decoration-none">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Pesanan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesananTerbaru as $p): ?>
                            <tr>
                                <td class="fw-semibold"><?= esc($p['kode_pesanan']) ?></td>
                                <td><?= esc($p['nama_pelanggan'] ?? 'Walk-in') ?></td>
                                <td><?= esc($p['nama_pesanan']) ?></td>
                                <td class="fw-semibold">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                                <td><span class="badge bg-primary-subtle text-primary"><?= esc($p['status_pesanan']) ?></span></td>
                                <td>
                                    <?php if ($p['status_pembayaran'] === 'LUNAS'): ?>
                                        <span class="badge bg-success-subtle text-success">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning"><?= esc($p['status_pembayaran']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-clean p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Stok Menipis</h6>
                <a href="<?= base_url('admin/inventori') ?>" class="small text-decoration-none">Lihat stok</a>
            </div>
            <?php if (empty($stokMenipis)): ?>
                <p class="text-muted small mb-0">Semua stok bahan dalam batas aman.</p>
            <?php else: ?>
                <?php foreach ($stokMenipis as $b): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <div class="fw-semibold small"><?= esc($b['nama_bahan']) ?></div>
                            <small class="text-muted">Min. <?= esc($b['stok_minimum']) ?> <?= esc($b['satuan']) ?></small>
                        </div>
                        <span class="badge bg-danger-subtle text-danger">Sisa <?= esc($b['stok']) ?> <?= esc($b['satuan']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartPendapatan'), {
    type: 'bar',
    data: {
        labels: <?= $chart7HariLabel ?>,
        datasets: [{
            label: 'Pendapatan (Juta Rp)',
            data: <?= $chart7HariData ?>,
            backgroundColor: ['#312E81','#312E81','#EC4899','#312E81','#312E81','#312E81','#312E81'],
            borderRadius: 8
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartLayanan'), {
    type: 'doughnut',
    data: {
        labels: ['Print Dokumen', 'Spanduk', 'Undangan', 'Banner'],
        datasets: [{
            data: [42, 24, 19, 15],
            backgroundColor: ['#312E81', '#EC4899', '#06B6D4', '#F59E0B'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { display: false } }, cutout: '68%' }
});
</script>
<?= $this->endSection() ?>