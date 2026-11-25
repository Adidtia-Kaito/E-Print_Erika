<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<style>
    .promo-table th {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748B;
        border-bottom: 1px solid #E2E8F0;
        padding: 0.75rem 0.65rem;
        white-space: nowrap;
    }
    .promo-table td {
        padding: 0.9rem 0.65rem;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.85rem;
    }
    .promo-form-card .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.3rem;
    }
    .promo-form-card .form-control,
    .promo-form-card .form-select {
        font-size: 0.84rem;
        border-color: #E2E8F0;
        border-radius: 8px;
        padding: 0.48rem 0.7rem;
    }
</style>

<!-- Header Halaman -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Promo</h4>
        <p class="text-muted small mb-0">Atur kode promo, kuota, dan masa berlaku per kategori layanan.</p>
    </div>
    <button type="button" class="btn btn-primary-erika btn-sm px-3 py-2" onclick="fokusFormPromo()">
        Promo Baru <i class="bi bi-plus-lg ms-1"></i>
    </button>
</div>

<div class="row g-4 align-items-start">
    <!-- Kolom Kiri: Tabel Daftar Promo -->
    <div class="col-lg-8">
        <div class="card-clean p-3 p-md-4">
            <div class="table-responsive">
                <table class="table promo-table mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Promo</th>
                            <th>Kategori</th>
                            <th>Diskon</th>
                            <th>Periode</th>
                            <th>Kuota</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($promoList)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data promo.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promoList as $pr): ?>
                                <?php
                                    $sekarang    = date('Y-m-d H:i:s');
                                    $sudahHabis  = (int) $pr['jumlah_terpakai'] >= (int) $pr['kuota'];
                                    $sudahLewat  = $sekarang > $pr['berlaku_sampai'];
                                    $aktif       = (int) $pr['status_aktif'] === 1 && ! $sudahHabis && ! $sudahLewat;

                                    if ($pr['jenis_diskon'] === 'PERSEN') {
                                        $teksDiskon = rtrim(rtrim(number_format((float) $pr['nilai_diskon'], 2, '.', ''), '0'), '.') . '%';
                                        if (! empty($pr['maks_diskon']) && (float) $pr['maks_diskon'] > 0) {
                                            $rb = round((float) $pr['maks_diskon'] / 1000);
                                            $teksDiskon .= ' (maks ' . $rb . ' rb)';
                                        }
                                    } else {
                                        $teksDiskon = 'Rp ' . number_format((float) $pr['nilai_diskon'], 0, ',', '.');
                                    }

                                    $periode = 's/d ' . date('d M Y', strtotime($pr['berlaku_sampai']));
                                ?>
                                <tr>
                                    <td class="fw-bold text-dark text-nowrap"><?= esc($pr['kode_promo']) ?></td>
                                    <td class="fw-medium text-dark" style="min-width: 120px;"><?= esc($pr['nama_promo']) ?></td>
                                    <td class="text-secondary"><?= esc($pr['nama_kategori'] ?? 'Semua') ?></td>
                                    <td class="text-dark text-nowrap"><?= esc($teksDiskon) ?></td>
                                    <td class="text-secondary text-nowrap"><?= esc($periode) ?></td>
                                    <td class="text-dark text-nowrap"><?= (int) $pr['jumlah_terpakai'] ?> / <?= (int) $pr['kuota'] ?></td>
                                    <td>
                                        <form action="<?= base_url('admin/promo/toggle/' . $pr['id_promo']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <?php if ($aktif): ?>
                                                <button type="submit" class="badge border-0 bg-success-subtle text-success px-2 py-1 rounded-pill" title="Klik untuk menonaktifkan">
                                                    Aktif
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="badge border-0 bg-secondary-subtle text-secondary px-2 py-1 rounded-pill" title="Klik untuk mengaktifkan">
                                                    Berakhir
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Promo Baru -->
    <div class="col-lg-4">
        <div class="card-clean p-3 p-md-4 promo-form-card">
            <h6 class="fw-bold text-dark mb-3">Promo Baru</h6>

            <form action="<?= base_url('admin/promo/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Kode Promo</label>
                    <input type="text" id="inputKodePromo" name="kode_promo" class="form-control" placeholder="mis. NATAL20" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Promo</label>
                    <input type="text" name="nama_promo" class="form-control" placeholder="Nama promo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori Layanan</label>
                    <select name="id_kategori_layanan" class="form-select">
                        <option value="">Semua kategori</option>
                        <?php foreach ($kategoriList as $kl): ?>
                            <option value="<?= $kl['id_kategori_layanan'] ?>"><?= esc($kl['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Jenis Diskon</label>
                        <select name="jenis_diskon" class="form-select">
                            <option value="PERSEN">Persen</option>
                            <option value="NOMINAL">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Nilai Diskon</label>
                        <input type="number" step="any" name="nilai_diskon" class="form-control" placeholder="10" required>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Maks. Diskon</label>
                        <input type="text" name="maks_diskon" class="form-control" placeholder="Rp 50.000">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Min. Belanja</label>
                        <input type="text" name="minimal_belanja" class="form-control" placeholder="Rp 150.000">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Berlaku Dari</label>
                        <input type="date" name="berlaku_dari" class="form-control" value="2026-12-01" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Berlaku Sampai</label>
                        <input type="date" name="berlaku_sampai" class="form-control" value="2026-12-31" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Kuota</label>
                    <input type="number" name="kuota" class="form-control" value="100" min="1" required>
                </div>

                <button type="submit" class="btn btn-primary-erika w-100 py-2">Simpan Promo</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function fokusFormPromo() {
    const el = document.getElementById('inputKodePromo');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.focus();
    }
}
</script>
<?= $this->endSection() ?>