<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<?php
ob_start();
?>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-line">
    <div>
        <label class="block text-[13px] font-semibold text-ink mb-1.5">Ukuran Kertas</label>
        <select data-spec-field="ukuran_kertas" class="w-full h-[44px] px-3.5 rounded-[10px] border border-line text-[14px] text-ink bg-white focus:outline-none focus:border-primary">
            <option value="A4 (210 x 297 mm)">A4 (210 &times; 297 mm)</option>
            <option value="F4 / Folio (215 x 330 mm)">F4 / Folio (215 &times; 330 mm)</option>
            <option value="A3 (297 x 420 mm)">A3 (297 &times; 420 mm)</option>
        </select>
    </div>
    <div>
        <label class="block text-[13px] font-semibold text-ink mb-1.5">Mode Warna</label>
        <select data-spec-field="warna_cetak" class="w-full h-[44px] px-3.5 rounded-[10px] border border-line text-[14px] text-ink bg-white focus:outline-none focus:border-primary">
            <option value="Hitam Putih">Hitam Putih</option>
            <option value="Full Color">Full Color</option>
        </select>
    </div>
    <div>
        <label class="block text-[13px] font-semibold text-ink mb-1.5">Sisi Cetak</label>
        <select data-spec-field="sisi_cetak" class="w-full h-[44px] px-3.5 rounded-[10px] border border-line text-[14px] text-ink bg-white focus:outline-none focus:border-primary">
            <option value="1 Sisi">1 Sisi</option>
            <option value="Bolak-balik">Bolak-balik</option>
        </select>
    </div>
</div>
<?php
$extraSectionsHtml = ob_get_clean();

echo view('pelanggan/_order_configurator', [
    'pageTitle'         => 'Print Dokumen & Jilid',
    'pageSubtitle'      => 'Print biasa, upload dokumen PDF/Word, serta pilihan jilid lakban, spiral kawat, atau buku.',
    'defaultKodePrefix' => 'PRT',
    'initialIdLayanan'  => $idLayanan ?? 0,
    'defaultHalaman'    => 1,
    'defaultSalinan'    => 1,
    'placeholderJudul'  => 'Contoh: Skripsi Bab 1-3',
    'extraSectionsHtml' => $extraSectionsHtml,
]);
?>

<?= $this->endSection() ?>
