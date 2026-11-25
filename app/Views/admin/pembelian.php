<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<style>
    .pill-tab {
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.38rem 1rem;
        border: 1px solid #E2E8F0;
        background: #fff;
        color: #334155;
        text-decoration: none;
    }
    .pill-tab.active {
        background: #312E81;
        border-color: #312E81;
        color: #fff;
    }
    .faktur-row {
        cursor: pointer;
    }
    .faktur-row.selected {
        background-color: #F8FAFC;
    }
    .clean-tbl th {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748B;
        border-bottom: 1px solid #E2E8F0;
        padding: 0.7rem 0.75rem;
    }
    .clean-tbl td {
        font-size: 0.84rem;
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
    }
</style>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h4 class="fw-bold mb-1">Pembelian &amp; Pemasok</h4>
        <p class="text-muted small mb-0">Catat pembelian bahan/produk dari pemasok &mdash; stok bertambah otomatis (mutasi stok).</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm bg-white px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalPemasok">
            Pemasok Baru <i class="bi bi-plus-lg ms-1"></i>
        </button>
        <button type="button" class="btn btn-primary-erika btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalPembelian">
            Pembelian Baru <i class="bi bi-plus-lg ms-1"></i>
        </button>
    </div>
</div>

<!-- Filter Pill Tab -->
<div class="d-flex gap-2 mb-4">
    <a href="<?= base_url('admin/pembelian?tab=pembelian') ?>" class="pill-tab <?= $tab === 'pembelian' ? 'active' : '' ?>">Pembelian</a>
    <a href="<?= base_url('admin/pembelian?tab=pemasok') ?>" class="pill-tab <?= $tab === 'pemasok' ? 'active' : '' ?>">Pemasok</a>
</div>

<?php if ($tab === 'pembelian'): ?>
    <!-- Tabel Daftar Pembelian -->
    <div class="card-clean p-3 p-md-4 mb-4">
        <div class="table-responsive">
            <table class="table clean-tbl mb-0">
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Pemasok</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Dicatat oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pembelianRows)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data pembelian.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pembelianRows as $pb): ?>
                            <?php $isSelected = $fakturAktif && (int) $fakturAktif['id_pembelian'] === (int) $pb['id_pembelian']; ?>
                            <tr class="faktur-row <?= $isSelected ? 'selected' : '' ?>" onclick="window.location='<?= base_url('admin/pembelian?faktur=' . $pb['id_pembelian']) ?>'">
                                <td class="fw-bold text-dark"><?= esc($pb['nomor_faktur']) ?></td>
                                <td class="text-secondary"><?= date('d M Y', strtotime($pb['tanggal_pembelian'])) ?></td>
                                <td class="text-dark"><?= esc($pb['nama_pemasok'] ?? '-') ?></td>
                                <td class="text-secondary"><?= (int) $pb['jumlah_item'] ?> item</td>
                                <td class="fw-medium text-dark">Rp <?= number_format((float) $pb['total_pembelian'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($pb['status_pembayaran'] === 'LUNAS'): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Lunas</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1">Belum Lunas</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary"><?= esc($pb['nama_admin'] ?? 'Admin') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Pembelian -->
    <div class="card-clean p-3 p-md-4 mb-4">
        <h6 class="fw-bold text-dark mb-3">
            Detail Pembelian &mdash; <?= esc($fakturAktif['nomor_faktur'] ?? '-') ?>
        </h6>
        <div class="table-responsive">
            <table class="table clean-tbl mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Harga Beli Satuan</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detailFaktur)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada item detail pada faktur ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($detailFaktur as $df): ?>
                            <?php
                                $isProduk  = ! empty($df['id_produk']);
                                $satuanItem = $isProduk ? ($df['satuan_produk'] ?? 'pcs') : ($df['satuan_bahan'] ?? 'rim');
                                $namaItem  = $isProduk
                                    ? (($df['nama_produk'] ?? 'Produk') . ' (produk)')
                                    : ($df['nama_bahan'] ?? 'Bahan');
                                $jenisItem = $isProduk ? 'Produk' : 'Bahan';
                            ?>
                            <tr>
                                <td class="text-dark"><?= esc($namaItem) ?></td>
                                <td class="text-secondary"><?= esc($jenisItem) ?></td>
                                <td class="text-secondary"><?= esc($df['jumlah']) ?> <?= esc($satuanItem) ?></td>
                                <td class="text-secondary">Rp <?= number_format((float) $df['harga_beli_satuan'], 0, ',', '.') ?></td>
                                <td class="text-dark fw-medium">Rp <?= number_format((float) $df['sub_total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Kartu Pemasok -->
<div class="card-clean p-3 p-md-4" id="sectionPemasok">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-dark mb-0">Pemasok</h6>
        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold" style="color:#312E81;" data-bs-toggle="modal" data-bs-target="#modalPemasok">
            Kelola
        </button>
    </div>
    <div class="table-responsive">
        <table class="table clean-tbl mb-0">
            <thead>
                <tr>
                    <th>Nama Pemasok</th>
                    <th>Jenis Pasokan</th>
                    <th>Kontak</th>
                    <th>No. Telp</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pemasokList)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Belum ada data pemasok.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pemasokList as $pm): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= esc($pm['nama_pemasok']) ?></td>
                            <td class="text-secondary"><?= esc($pm['jenis_pasokan']) ?></td>
                            <td class="text-secondary"><?= esc($pm['kontak_pemasok']) ?></td>
                            <td class="text-secondary"><?= esc($pm['no_telp']) ?></td>
                            <td class="text-secondary"><?= esc($pm['email']) ?></td>
                            <td>
                                <?php if ((int) $pm['status_aktif'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pemasok Baru -->
<div class="modal fade" id="modalPemasok" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/pembelian/store-pemasok') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Pemasok Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <div class="mb-2">
                    <label class="form-label fw-semibold">Nama Pemasok</label>
                    <input type="text" name="nama_pemasok" class="form-control form-control-sm" placeholder="mis. CV Kertas Jaya" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jenis Pasokan</label>
                        <input type="text" name="jenis_pasokan" class="form-control form-control-sm" placeholder="mis. Kertas" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kontak</label>
                        <input type="text" name="nama_kontak" class="form-control form-control-sm" placeholder="mis. Pak Budi">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">No. Telp</label>
                        <input type="text" name="no_telp" class="form-control form-control-sm" placeholder="0812-xxxx-xxxx">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="sales@pemasok.id">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="alamat" rows="2" class="form-control form-control-sm"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-erika btn-sm">Simpan Pemasok</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pembelian Baru -->
<div class="modal fade" id="modalPembelian" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/pembelian/store') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Pembelian Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">No. Faktur</label>
                        <input type="text" name="no_faktur" class="form-control form-control-sm" value="FKT-<?= date('ym') ?>-<?= rand(100, 999) ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal_pembelian" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-7">
                        <label class="form-label fw-semibold">Pemasok</label>
                        <select name="id_pemasok" class="form-select form-select-sm" required>
                            <?php foreach ($pemasokList as $pm): ?>
                                <option value="<?= $pm['id_pemasok'] ?>"><?= esc($pm['nama_pemasok']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="form-label fw-semibold">Pembayaran</label>
                        <select name="status_pembayaran" class="form-select form-select-sm">
                            <option value="LUNAS">Lunas</option>
                            <option value="BELUM_LUNAS">Belum Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Jenis Item</label>
                    <select name="tipe_item" id="selectTipeItem" class="form-select form-select-sm" onchange="togglePilihanItem()">
                        <option value="BAHAN">Bahan Baku</option>
                        <option value="PRODUK">Produk</option>
                    </select>
                </div>
                <div class="mb-2" id="boxPilihBahan">
                    <label class="form-label fw-semibold">Pilih Bahan Baku</label>
                    <select name="id_bahan" class="form-select form-select-sm">
                        <?php foreach ($bahanList as $b): ?>
                            <option value="<?= $b['id_bahan'] ?>"><?= esc($b['nama_bahan']) ?> (<?= esc($b['satuan']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2 d-none" id="boxPilihProduk">
                    <label class="form-label fw-semibold">Pilih Produk</label>
                    <select name="id_produk" class="form-select form-select-sm">
                        <?php foreach ($produkList as $pr): ?>
                            <option value="<?= $pr['id_produk'] ?>"><?= esc($pr['nama_produk']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control form-control-sm" value="10" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Harga Beli Satuan (Rp)</label>
                        <input type="number" name="harga_beli_satuan" class="form-control form-control-sm" value="50000" min="0" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">Catatan</label>
                    <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Opsional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-erika btn-sm">Simpan Pembelian</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function togglePilihanItem() {
    const tipe = document.getElementById('selectTipeItem').value;
    document.getElementById('boxPilihBahan').classList.toggle('d-none', tipe !== 'BAHAN');
    document.getElementById('boxPilihProduk').classList.toggle('d-none', tipe !== 'PRODUK');
}
</script>
<?= $this->endSection() ?>