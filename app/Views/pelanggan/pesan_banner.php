<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<?php
ob_start();
?>
<div class="pt-3 border-t border-line space-y-3">
    <span class="block text-[13px] font-bold text-ink">Tema Desain Banner (Opsional)</span>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php foreach (['Promo Produk', 'Menu / Harga', 'Event', 'Profil Usaha'] as $tpl): ?>
            <button type="button" data-template-card onclick="ErikaOrderConfigurator.selectTemplate('<?= esc($tpl) ?>', this)" class="text-left p-3 rounded-xl border border-line bg-surface hover:border-primary/50 transition">
                <div class="h-14 rounded-lg bg-white border border-line flex items-center justify-center text-cyan mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                </div>
                <p class="text-[12px] font-bold text-ink truncate"><?= esc($tpl) ?></p>
            </button>
        <?php endforeach; ?>
    </div>
</div>
<?php
$extraSectionsHtml = ob_get_clean();

echo view('pelanggan/_order_configurator', [
    'pageTitle'         => 'Cetak Banner',
    'pageSubtitle'      => 'Ukuran custom dengan pilihan rangka X-Banner atau Roll-Banner serta pilihan bahan cetak.',
    'defaultKodePrefix' => 'BNR',
    'initialIdLayanan'  => $idLayanan ?? 0,
    'defaultPanjang'    => 160,
    'defaultLebar'      => 60,
    'dimensiPresets'    => [
        [160, 60, '60 × 160 cm'],
        [180, 80, '80 × 180 cm'],
        [200, 85, '85 × 200 cm'],
    ],
    'placeholderJudul'  => 'Contoh: Banner Event Kampus',
    'extraSectionsHtml' => $extraSectionsHtml,
]);
?>

<?= $this->endSection() ?>
