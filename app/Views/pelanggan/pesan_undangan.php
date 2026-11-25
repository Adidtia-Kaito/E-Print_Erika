<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<?php
ob_start();
?>
<div class="pt-3 border-t border-line space-y-5">
    <div>
        <span class="block text-[13px] font-bold text-ink mb-2.5">Pilih Template Undangan</span>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php
            $templates = [
                ['Elegan Gold', 'bg-amber-50 text-amber-700'],
                ['Floral Pink', 'bg-pink-50 text-magenta'],
                ['Minimalis', 'bg-slate-100 text-ink'],
                ['Rustic', 'bg-orange-50 text-orange-800'],
                ['Modern Biru', 'bg-indigo-50 text-primary'],
                ['Klasik', 'bg-stone-100 text-stone-700'],
                ['Islami', 'bg-emerald-50 text-emerald-700'],
                ['Garden', 'bg-teal-50 text-teal-700'],
            ];
            foreach ($templates as [$tpl, $tint]):
            ?>
                <button type="button" data-template-card onclick="ErikaOrderConfigurator.selectTemplate('<?= esc($tpl) ?>', this)" class="text-left p-3 rounded-xl border border-line bg-white hover:border-primary/50 transition">
                    <div class="h-20 rounded-lg <?= $tint ?> flex items-center justify-center font-bold text-[12px] mb-2">
                        <?= esc($tpl) ?>
                    </div>
                    <p class="text-[12px] font-semibold text-ink truncate"><?= esc($tpl) ?></p>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-[13px] font-semibold text-ink mb-1.5">Nama Mempelai / Acara</label>
            <input type="text" data-spec-field="nama_acara" placeholder="Contoh: Anisa &amp; Rizki" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="block text-[13px] font-semibold text-ink mb-1.5">Tanggal &amp; Waktu Acara</label>
            <input type="text" data-spec-field="tanggal_waktu" placeholder="Sabtu, 12 Des 2026 • 10.00 WIB" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-[13px] font-semibold text-ink mb-1.5">Lokasi Acara</label>
            <input type="text" data-spec-field="lokasi_acara" placeholder="Gedung Serbaguna Bunga Melati, Medan" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary">
        </div>
    </div>
</div>
<?php
$extraSectionsHtml = ob_get_clean();

echo view('pelanggan/_order_configurator', [
    'pageTitle'         => 'Cetak Undangan',
    'pageSubtitle'      => 'Pilih template undangan pernikahan, khitanan, atau acara, lalu atur jumlah lembar cetak.',
    'defaultKodePrefix' => 'UND',
    'initialIdLayanan'  => $idLayanan ?? 0,
    'defaultHalaman'    => 100,
    'defaultSalinan'    => 1,
    'placeholderJudul'  => 'Contoh: Undangan Anisa & Rizki',
    'extraSectionsHtml' => $extraSectionsHtml,
]);
?>

<?= $this->endSection() ?>
