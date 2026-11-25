<?php
/**
 * Shared Dynamic Order Configurator Shell (Halaman 05 - 08)
 * Menghubungkan form dinamis berdasarkan id_layanan, upload file, dimensi, opsi_layanan,
 * dan kalkulasi real-time ke POST /api/pelanggan/hitung-estimasi.
 */
$pageTitle         = $pageTitle ?? 'Konfigurasi Pesanan';
$pageSubtitle      = $pageSubtitle ?? 'Atur spesifikasi layanan cetak sesuai kebutuhan Anda.';
$defaultKodePrefix = $defaultKodePrefix ?? 'PRT';
$initialIdLayanan  = $initialIdLayanan ?? 0;
$defaultPanjang    = $defaultPanjang ?? 300;
$defaultLebar      = $defaultLebar ?? 100;
$dimensiPresets    = $dimensiPresets ?? [];
$extraSectionsHtml = $extraSectionsHtml ?? '';
?>

<!-- Judul Halaman (Figma Header Band) -->
<section class="bg-surface border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-8 sm:py-10">
        <p id="cfg-breadcrumb" class="text-[13px] font-semibold text-muted">Layanan / <?= esc($pageTitle) ?></p>
        <h1 id="cfg-page-title" class="text-[30px] sm:text-[38px] font-extrabold text-ink tracking-tight mt-1.5"><?= esc($pageTitle) ?></h1>
        <p id="cfg-page-desc" class="text-[15px] text-muted mt-1 max-w-2xl"><?= esc($pageSubtitle) ?></p>

        <!-- Sub-layanan switcher dalam kategori yang sama -->
        <div id="cfg-layanan-switcher" class="hidden flex flex-wrap gap-2.5 mt-5"></div>
    </div>
</section>

<!-- Body Konfigurator (852px Form Kiri + 400px Ringkasan Kanan) -->
<section id="order-configurator-root" class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-10">
    <!-- Loading State -->
    <div id="cfg-loading-state" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-5">
            <div class="h-52 rounded-[20px] bg-surface border border-line animate-pulse"></div>
            <div class="h-52 rounded-[20px] bg-surface border border-line animate-pulse"></div>
        </div>
        <div class="lg:col-span-4">
            <div class="h-96 rounded-[20px] bg-surface border border-line animate-pulse"></div>
        </div>
    </div>

    <!-- Main Form Grid -->
    <div id="cfg-main-content" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kolom Kiri: Form Konfigurasi -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Section Upload Dokumen (Ditampilkan otomatis jika layanan.butuh_file == 1) -->
            <div id="cfg-section-file" class="hidden bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-5">
                <div class="flex items-start gap-3">
                    <span class="w-7 h-7 rounded-lg bg-primary text-white text-[13px] font-bold flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </span>
                    <div>
                        <h2 class="font-bold text-[18px] text-ink">Upload Dokumen Siap Cetak</h2>
                        <p class="text-[13px] text-muted mt-0.5">Format yang diizinkan: <strong>.pdf, .doc, .docx</strong> &bull; Maksimal 50 MB per berkas.</p>
                    </div>
                </div>

                <!-- Drag-and-drop Dropzone -->
                <div id="cfg-dropzone" class="cursor-pointer rounded-2xl border-2 border-dashed border-line hover:border-primary/60 bg-surface/60 transition px-6 py-8 text-center">
                    <input id="cfg-file-input" type="file" accept=".pdf,.doc,.docx" multiple class="hidden">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <p class="text-[14px] font-bold text-ink">Tarik &amp; lepas file di sini, atau klik untuk memilih</p>
                    <p class="text-[12px] text-muted mt-1">Objek berkas akan disimpan aman ke state keranjang sebelum checkout</p>
                </div>

                <!-- File List -->
                <div id="cfg-file-list" class="space-y-2.5"></div>
            </div>

            <!-- Section Dimensi Custom (Ditampilkan otomatis jika layanan.butuh_dimensi == 1) -->
            <div id="cfg-section-dimensi" class="hidden bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-5">
                <div class="flex items-start gap-3">
                    <span class="w-7 h-7 rounded-lg bg-cyan text-white text-[13px] font-bold flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </span>
                    <div>
                        <h2 class="font-bold text-[18px] text-ink">Ukuran &amp; Dimensi (cm)</h2>
                        <p class="text-[13px] text-muted mt-0.5">Isi panjang dan lebar dalam sentimeter. Luas dalam m² dihitung otomatis.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cfg-panjang-cm" class="block text-[13px] font-semibold text-ink mb-1.5">Panjang (cm)</label>
                        <input id="cfg-panjang-cm" type="number" min="1" value="<?= (int) $defaultPanjang ?>" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                    <div>
                        <label for="cfg-lebar-cm" class="block text-[13px] font-semibold text-ink mb-1.5">Lebar (cm)</label>
                        <input id="cfg-lebar-cm" type="number" min="1" value="<?= (int) $defaultLebar ?>" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                    <div>
                        <label for="cfg-luas-otomatis" class="block text-[13px] font-semibold text-ink mb-1.5">Luas (otomatis)</label>
                        <input id="cfg-luas-otomatis" type="text" readonly value="3,00 m²" class="w-full h-[44px] px-4 rounded-[10px] border border-line bg-surface font-bold text-[14px] text-primary">
                    </div>
                </div>

                <?php if (! empty($dimensiPresets)): ?>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-[12px] font-semibold text-muted mr-1">Ukuran populer:</span>
                        <?php foreach ($dimensiPresets as [$p, $l, $label]): ?>
                            <button type="button" data-preset-dim="<?= (int) $p ?>x<?= (int) $l ?>" class="px-3.5 py-1.5 rounded-lg border border-line bg-surface hover:border-primary hover:text-primary text-[12px] font-semibold text-ink transition">
                                <?= esc($label) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section Kuantitas & Spesifikasi Khusus Halaman (05/06/07/08) -->
            <div class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-5">
                <div class="flex items-start gap-3">
                    <span class="w-7 h-7 rounded-lg bg-magenta text-white text-[13px] font-bold flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </span>
                    <div>
                        <h2 class="font-bold text-[18px] text-ink">Jumlah &amp; Identitas Pekerjaan</h2>
                        <p class="text-[13px] text-muted mt-0.5">Setiap perubahan jumlah otomatis memperbarui estimasi biaya.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div id="cfg-field-halaman-wrap">
                        <label for="cfg-jumlah-halaman" class="block text-[13px] font-semibold text-ink mb-1.5">Jumlah Halaman / Lembar</label>
                        <input id="cfg-jumlah-halaman" type="number" min="1" value="<?= (int) ($defaultHalaman ?? 1) ?>" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                    <div>
                        <label for="cfg-jumlah-salinan" class="block text-[13px] font-semibold text-ink mb-1.5">Jumlah Salinan / Rangkap</label>
                        <input id="cfg-jumlah-salinan" type="number" min="1" value="<?= (int) ($defaultSalinan ?? 1) ?>" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                    <div>
                        <label for="cfg-judul-pekerjaan" class="block text-[13px] font-semibold text-ink mb-1.5">Judul Pekerjaan</label>
                        <input id="cfg-judul-pekerjaan" type="text" placeholder="<?= esc($placeholderJudul ?? 'Contoh: Cetak Dokumen / Acara') ?>" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                </div>

                <?= $extraSectionsHtml ?>
            </div>

            <!-- Section Opsi Layanan Dinamis (dari opsi_layanan) -->
            <div id="cfg-section-opsi" class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-5">
                <div class="flex items-start gap-3">
                    <span class="w-7 h-7 rounded-lg bg-yellow text-ink text-[13px] font-bold flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <h2 class="font-bold text-[18px] text-ink">Pilihan Bahan, Rangka &amp; Finishing</h2>
                        <p class="text-[13px] text-muted mt-0.5">Opsi layanan dimuat dinamis sesuai layanan yang dipilih.</p>
                    </div>
                </div>

                <div id="cfg-opsi-container" class="space-y-5"></div>
            </div>

            <!-- Section Catatan Pekerjaan -->
            <div class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-4">
                <h2 class="font-bold text-[18px] text-ink">Catatan untuk Operator</h2>
                <textarea id="cfg-catatan" rows="3" placeholder="Contoh: sampul depan warna biru, potong tepi rapi, atau instruksi khusus lainnya..." class="w-full p-4 rounded-[12px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15"></textarea>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Pesanan Sticky (Figma 6:364) -->
        <aside class="lg:col-span-4 lg:sticky lg:top-24">
            <div class="bg-white rounded-[20px] border border-line shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between gap-2 pb-3 border-b border-line">
                    <h3 class="font-extrabold text-[18px] text-ink">Ringkasan Pesanan</h3>
                    <span id="sum-api-status" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-surface text-muted">Menghitung...</span>
                </div>

                <!-- Breakdown Items -->
                <div id="sum-breakdown-rows" class="space-y-3"></div>

                <div class="pt-3 border-t border-line space-y-2 text-[13px]">
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Subtotal</span>
                        <span id="sum-subtotal-harga" class="font-semibold text-ink">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Diskon promo</span>
                        <span class="text-muted">&mdash;</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-line flex items-baseline justify-between">
                    <span class="font-bold text-[15px] text-ink">Total Estimasi</span>
                    <span id="sum-total-harga" class="text-[26px] font-extrabold text-primary">Rp 0</span>
                </div>

                <div class="space-y-2.5 pt-2">
                    <button type="button" onclick="ErikaOrderConfigurator.saveToCart(true)" class="w-full h-[46px] bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] rounded-[12px] flex items-center justify-center gap-2 transition shadow-sm">
                        <span>Lanjut ke Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                    <button type="button" onclick="ErikaOrderConfigurator.saveToCart(false)" class="w-full h-[46px] bg-white hover:bg-surface border border-line text-ink font-semibold text-[14px] rounded-[12px] transition">
                        Tambah ke Keranjang
                    </button>
                </div>

                <div class="flex items-start gap-2 pt-1 text-[12px] text-muted">
                    <svg class="w-4 h-4 text-cyan shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="cfg-estimasi-waktu">Estimasi selesai dihitung otomatis</span>
                </div>
            </div>
        </aside>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    ErikaOrderConfigurator.init({
        initialIdLayanan: <?= (int) $initialIdLayanan ?>,
        defaultKodePrefix: <?= json_encode($defaultKodePrefix) ?>,
        defaultPanjang: <?= (int) $defaultPanjang ?>,
        defaultLebar: <?= (int) $defaultLebar ?>
    });
});
</script>
