<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<?php
ob_start();
?>
<div class="pt-3 border-t border-line space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-[13px] font-bold text-ink">Pilih Template Desain Spanduk (Opsional)</span>
        <span class="text-[12px] text-muted">Atau jelaskan desain di catatan</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php foreach (['Promo Diskon', 'Grand Opening', 'Ucapan Selamat', 'Kegiatan / Seminar'] as $tpl): ?>
            <button type="button" data-template-card onclick="ErikaOrderConfigurator.selectTemplate('<?= esc($tpl) ?>', this)" class="text-left p-3 rounded-xl border border-line bg-surface hover:border-primary/50 transition">
                <div class="h-16 rounded-lg bg-white border border-line flex items-center justify-center text-primary/40 mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-[12px] font-bold text-ink truncate"><?= esc($tpl) ?></p>
            </button>
        <?php endforeach; ?>
    </div>
</div>
<?php
$extraSectionsHtml = ob_get_clean();

echo view('pelanggan/_order_configurator', [
    'pageTitle'         => 'Cetak Spanduk',
    'pageSubtitle'      => 'Ukuran custom dalam cm (otomatis dikonversi ke m²), pilihan bahan flexi, dan finishing mata ayam.',
    'defaultKodePrefix' => 'SPD',
    'initialIdLayanan'  => $idLayanan ?? 0,
    'defaultPanjang'    => 300,
    'defaultLebar'      => 100,
    'dimensiPresets'    => [
        [100, 50, '100 × 50 cm'],
        [200, 80, '200 × 80 cm'],
        [300, 100, '300 × 100 cm'],
        [400, 100, '400 × 100 cm'],
    ],
    'placeholderJudul'  => 'Contoh: Spanduk Grand Opening',
    'extraSectionsHtml' => $extraSectionsHtml,
]);
?>

<?= $this->endSection() ?>
