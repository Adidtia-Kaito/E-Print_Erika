<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Antrean Produksi (FIFO)</h4>
        <p class="text-muted small mb-0">Papan antrean berurutan berdasarkan waktu masuk paling awal.</p>
    </div>
</div>

<?php
$kolom = [
    'MENUNGGU'     => ['label' => 'Menunggu', 'badge' => 'secondary'],
    'DIKERJAKAN'   => ['label' => 'Dikerjakan', 'badge' => 'primary'],
    'SIAP_DIAMBIL' => ['label' => 'Siap Diambil', 'badge' => 'warning'],
    'SELESAI'      => ['label' => 'Selesai', 'badge' => 'success'],
];
?>

<div class="row g-3">
    <?php foreach ($kolom as $key => $info): ?>
        <div class="col-md-6 col-xl-3">
            <div class="card-clean p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold"><?= $info['label'] ?></span>
                    <span class="badge bg-<?= $info['badge'] ?>-subtle text-<?= $info['badge'] ?>"><?= count($grouped[$key] ?? []) ?></span>
                </div>

                <?php if (empty($grouped[$key])): ?>
                    <p class="text-muted small text-center my-4">Tidak ada antrean</p>
                <?php else: ?>
                    <?php foreach ($grouped[$key] as $item): ?>
                        <div class="border rounded-3 p-3 mb-2 bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-primary"><?= esc($item['nomor_antrean']) ?></span>
                                <span class="badge <?= $item['prioritas'] === 'TINGGI' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' ?>">
                                    <?= esc(ucfirst(strtolower($item['prioritas']))) ?>
                                </span>
                            </div>
                            <div class="fw-semibold small mb-1"><?= esc($item['nama_pesanan'] ?? $item['kode_pesanan']) ?></div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                Masuk: <?= date('H:i', strtotime($item['waktu_masuk'])) ?> &bull; <?= esc($item['nama_pelanggan'] ?? 'Walk-in') ?>
                            </div>

                            <form action="<?= base_url('admin/antrean/update-status') ?>" method="post" class="mt-2 d-flex gap-1">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_antrean" value="<?= $item['id_antrean'] ?>">
                                <select name="status_antrean" class="form-select form-select-sm">
                                    <?php foreach ($kolom as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= $key === $k ? 'selected' : '' ?>><?= $v['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary-erika">Pindah</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>