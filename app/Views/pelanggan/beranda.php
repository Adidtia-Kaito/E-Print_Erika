<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<!-- 01 · Hero Section (Figma 2:115) -->
<section class="relative overflow-hidden bg-gradient-to-b from-surface to-white border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-14 lg:py-[74px] grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Hero Text (Kiri) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 text-primary text-[12px] font-bold">
                <span class="w-2 h-2 rounded-full bg-magenta"></span>
                <span>Cetak cepat &bull; Hasil rapi &bull; Harga transparan</span>
            </div>

            <h1 class="text-[36px] sm:text-[48px] lg:text-[54px] font-extrabold text-ink leading-[1.12] tracking-tight">
                Cetak Dokumen, Spanduk, Undangan &amp; Banner <span class="text-primary">Online.</span>
            </h1>

            <p class="text-[16px] sm:text-[18px] text-muted leading-relaxed max-w-[600px]">
                Pilih layanan, atur ukuran dan bahan, unggah file siap cetak, hitung estimasi harga real-time, lalu pantau antrean langsung dari rumah.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <button type="button" onclick="handleMulaiPesan()" class="inline-flex items-center gap-2.5 bg-primary hover:bg-[#212675] text-white font-semibold text-[15px] px-6 h-[48px] rounded-[12px] shadow-md shadow-primary/20 transition">
                    <span>Mulai Pesan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
                <a href="<?= site_url('layanan') ?>" class="inline-flex items-center justify-center bg-white hover:bg-surface border border-line text-ink font-semibold text-[15px] px-6 h-[48px] rounded-[12px] transition">
                    Lihat Layanan
                </a>
            </div>

            <!-- Info Bar -->
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2.5 pt-2 text-[13px] font-medium text-muted">
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Lihat layanan tanpa login
                </span>
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-magenta" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Login saat memesan
                </span>
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lacak antrean online
                </span>
            </div>
        </div>

        <!-- Hero Visual (Kanan — Figma 2:144) -->
        <div class="lg:col-span-5 flex justify-center">
            <div class="relative w-full max-w-[520px] h-[390px] sm:h-[420px] rounded-[28px] bg-surface border border-line overflow-hidden flex items-center justify-center p-6">
                <!-- Decorative CMYK Ellipses -->
                <div class="absolute -top-16 -left-12 w-[240px] h-[240px] rounded-full bg-cyan/20"></div>
                <div class="absolute -bottom-16 -right-10 w-[220px] h-[220px] rounded-full bg-magenta/20"></div>
                <div class="absolute top-6 right-8 w-[120px] h-[120px] rounded-full bg-yellow/35"></div>

                <!-- Contoh Kertas Card -->
                <div class="relative z-10 w-[270px] sm:w-[290px] bg-white rounded-[20px] shadow-xl border border-line p-6 -translate-y-3">
                    <span class="inline-block text-[10px] font-extrabold tracking-[0.14em] text-magenta uppercase">UNDANGAN</span>
                    <h3 class="mt-2 font-extrabold text-[22px] leading-snug text-ink">
                        Pernikahan<br>Anisa &amp; Rizki
                    </h3>
                    <div class="mt-5 space-y-2.5">
                        <div class="h-2 rounded-full bg-surface w-full"></div>
                        <div class="h-2 rounded-full bg-surface w-5/6"></div>
                        <div class="h-2 rounded-full bg-surface w-4/6"></div>
                        <div class="h-2 rounded-full bg-primary/15 w-3/6"></div>
                    </div>
                </div>

                <!-- Contoh Banner Pill -->
                <div class="absolute bottom-7 z-20 px-6 py-3 rounded-full bg-ink text-white text-[12px] font-bold tracking-[0.14em] shadow-lg">
                    SPANDUK &bull; BANNER &bull; BUKU
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Kami Section (Figma 2:157) -->
<section class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-16">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <h2 class="text-[28px] sm:text-[32px] font-extrabold text-ink tracking-tight">Layanan Kami</h2>
            <p class="text-[15px] text-muted mt-1">Pilih layanan, lihat harga dasar, dan hitung estimasi biaya sebelum memesan.</p>
        </div>
        <a href="<?= site_url('layanan') ?>" class="inline-flex items-center gap-1.5 text-[14px] font-bold text-primary hover:underline shrink-0">
            <span>Lihat semua layanan</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </div>

    <!-- Container Dinamis dari GET /api/pelanggan/katalog -->
    <div id="beranda-kategori-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="h-[270px] rounded-[20px] border border-line bg-surface animate-pulse p-6"></div>
        <?php endfor; ?>
    </div>
</section>

<!-- Pilihan Bahan Section (Figma 2:233) -->
<section class="bg-surface border-y border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-11">
        <h3 class="text-[18px] font-bold text-ink mb-4">Pilihan Bahan — tersedia di toko kami</h3>
        <div class="flex flex-wrap gap-2.5">
            <?php
            $bahanChips = ['HVS 70 gr', 'HVS 80 gr', 'Art Paper 120 gr', 'Art Carton 260 gr', 'Flexi 280', 'Flexi 340', 'Albatros', 'Sticker Vinyl'];
            foreach ($bahanChips as $chip):
            ?>
                <span class="px-4 py-2 rounded-full bg-white border border-line text-[13px] font-semibold text-ink shadow-2xs">
                    <?= esc($chip) ?>
                </span>
            <?php endforeach; ?>
        </div>
        <p class="text-[12px] text-muted mt-3">* Ketersediaan bahan dapat dipilih langsung saat mengonfigurasi pesanan.</p>
    </div>
</section>

<!-- Promo Berjalan Section (Figma 2:253) -->
<section id="promo" class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-16">
    <div class="flex items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="text-[28px] sm:text-[32px] font-extrabold text-ink tracking-tight">Promo Berjalan</h2>
            <p class="text-[15px] text-muted mt-1">Gunakan kode promo aktif di bawah ini saat checkout untuk potongan harga.</p>
        </div>
    </div>

    <div id="beranda-promo-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="h-[132px] rounded-[20px] bg-surface border border-line animate-pulse"></div>
        <div class="h-[132px] rounded-[20px] bg-surface border border-line animate-pulse"></div>
    </div>
</section>

<!-- Cara Memesan Section (Figma 2:272) -->
<section id="cara-pesan" class="bg-surface border-t border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-16">
        <h2 class="text-[28px] sm:text-[32px] font-extrabold text-ink tracking-tight mb-10">Cara Memesan</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $langkah = [
                ['1', 'bg-cyan text-white', 'Pilih layanan', 'Lihat layanan, harga dasar, dan estimasi waktu pengerjaan tanpa perlu login.'],
                ['2', 'bg-magenta text-white', 'Login / Daftar', 'Masuk atau buat akun singkat saat siap mengirim pesanan dan file cetak.'],
                ['3', 'bg-yellow text-ink', 'Isi spesifikasi & upload', 'Atur ukuran, bahan, opsi finishing, dan unggah dokumen PDF/Word Anda.'],
                ['4', 'bg-primary text-white', 'Bayar & pantau', 'Selesaikan pembayaran online lalu pantau nomor antrean secara real-time.'],
            ];
            foreach ($langkah as [$no, $badgeCls, $judul, $ket]):
            ?>
                <div class="bg-white rounded-[18px] border border-line p-6">
                    <div class="w-10 h-10 rounded-xl <?= $badgeCls ?> font-extrabold text-[16px] flex items-center justify-center mb-4">
                        <?= $no ?>
                    </div>
                    <h3 class="font-bold text-[16px] text-ink mb-1.5"><?= esc($judul) ?></h3>
                    <p class="text-[13px] text-muted leading-relaxed"><?= esc($ket) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Band (Figma 2:295) -->
<section class="bg-primary text-white">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-12 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <h2 class="text-[28px] font-extrabold tracking-tight">Siap mencetak?</h2>
            <p class="text-[15px] text-indigo-100 mt-1">Login untuk mulai memesan dan pantau antrean percetakan Anda.</p>
        </div>
        <button type="button" onclick="handleMulaiPesan()" class="inline-flex items-center gap-2.5 bg-yellow hover:bg-amber-300 text-ink font-bold text-[15px] px-7 h-[48px] rounded-[12px] shadow-md transition shrink-0">
            <span data-guest-only>Login &amp; Pesan</span>
            <span data-auth-only class="hidden">Mulai Pesan Sekarang</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </button>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function handleMulaiPesan() {
    if (ErikaAuth.isLoggedIn()) {
        window.location.href = `${window.ERIKA_CONFIG.siteUrl}/layanan`;
    } else {
        ErikaAuth.openLoginModal(() => {
            window.location.href = `${window.ERIKA_CONFIG.siteUrl}/layanan`;
        });
    }
}

function copyPromoCode(kode) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(kode).then(() => {
            ErikaUI.toast(`Kode promo ${kode} disalin ke clipboard!`, 'success');
        });
    } else {
        ErikaUI.toast(`Kode promo: ${kode}`, 'info');
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const katGrid = document.getElementById('beranda-kategori-grid');
    const promoGrid = document.getElementById('beranda-promo-grid');

    const cardThemes = [
        { bg: 'bg-primary/10 text-primary', badge: 'bg-primary/10 text-primary', icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>' },
        { bg: 'bg-magenta/10 text-magenta', badge: 'bg-magenta/10 text-magenta', icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>' },
        { bg: 'bg-yellow/25 text-ink', badge: 'bg-yellow/30 text-ink', icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>' },
        { bg: 'bg-cyan/15 text-cyan', badge: 'bg-cyan/15 text-cyan', icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>' },
    ];

    try {
        const data = await ErikaKatalog.load();
        const categories = data.kategori_layanan || [];
        const promos = data.promo || [];

        if (categories.length === 0) {
            katGrid.innerHTML = `<div class="col-span-full p-8 text-center rounded-2xl border border-line bg-surface text-muted text-[14px]">Belum ada kategori layanan aktif.</div>`;
        } else {
            katGrid.innerHTML = categories.map((kat, idx) => {
                const theme = cardThemes[idx % cardThemes.length];
                const layananList = kat.layanan || [];
                const targetUrl = layananList.length > 0
                    ? `${window.ERIKA_CONFIG.siteUrl}/layanan?kategori=${kat.id_kategori_layanan}`
                    : `${window.ERIKA_CONFIG.siteUrl}/layanan`;
                const tags = layananList.slice(0, 3).map((l) =>
                    `<span class="px-2.5 py-1 rounded-md bg-surface border border-line text-[11px] font-semibold text-muted">${erikaEscapeHtml(l.nama_layanan)}</span>`
                ).join('');

                return `
                    <a href="${targetUrl}" class="group bg-white rounded-[20px] border border-line hover:border-primary/40 hover:shadow-lg transition p-6 flex flex-col justify-between">
                        <div>
                            <div class="w-[52px] h-[52px] rounded-2xl ${theme.bg} flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">${theme.icon}</svg>
                            </div>
                            <h3 class="font-bold text-[18px] text-ink group-hover:text-primary transition">${erikaEscapeHtml(kat.nama_kategori)}</h3>
                            <p class="text-[13px] text-muted mt-2 leading-relaxed line-clamp-2">${erikaEscapeHtml(kat.deskripsi || 'Layanan cetak profesional dengan hasil presisi.')}</p>
                            <div class="flex flex-wrap gap-1.5 mt-4">${tags}</div>
                        </div>
                        <div class="mt-5 pt-4 border-t border-line flex items-center justify-between text-[13px] font-bold text-primary">
                            <span>Lihat layanan</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </div>
                    </a>`;
            }).join('');
        }

        if (promos.length === 0) {
            promoGrid.innerHTML = `<div class="col-span-full p-8 text-center rounded-2xl border border-line bg-surface text-muted text-[14px]">Saat ini belum ada promo aktif.</div>`;
        } else {
            promoGrid.innerHTML = promos.map((pr, idx) => {
                const isMagenta = idx % 2 === 0;
                const cardBg = isMagenta
                    ? 'bg-gradient-to-r from-magenta to-[#C7116B]'
                    : 'bg-gradient-to-r from-primary to-[#1F2373]';
                const diskonLabel = pr.jenis_diskon === 'PERSEN'
                    ? `Diskon ${Number(pr.nilai_diskon)}%${pr.maks_diskon ? ` (Maks. ${erikaFormatRp(pr.maks_diskon)})` : ''}`
                    : `Potongan ${erikaFormatRp(pr.nilai_diskon)}`;
                const tglAkhir = pr.berlaku_sampai ? new Date(pr.berlaku_sampai.replace(' ', 'T')).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';

                return `
                    <div class="${cardBg} text-white rounded-[20px] p-6 sm:p-7 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 shadow-md">
                        <div class="space-y-1.5">
                            <h3 class="font-extrabold text-[20px] leading-snug">${erikaEscapeHtml(pr.nama_promo)}</h3>
                            <p class="text-[13px] text-white/90">${diskonLabel} &bull; Min. belanja ${erikaFormatRp(pr.minimal_belanja)}</p>
                            <p class="text-[12px] text-white/75">Berlaku s/d ${tglAkhir} &bull; Sisa kuota ${pr.sisa_kuota ?? (pr.kuota - pr.jumlah_terpakai)}</p>
                        </div>
                        <button type="button" onclick="copyPromoCode('${erikaEscapeHtml(pr.kode_promo)}')" class="shrink-0 bg-white/15 hover:bg-white/25 border border-dashed border-white/60 rounded-xl px-5 py-3 text-center transition">
                            <span class="block text-[10px] font-bold tracking-widest uppercase text-white/80">KODE</span>
                            <span class="block text-[16px] font-extrabold tracking-wider text-white mt-0.5">${erikaEscapeHtml(pr.kode_promo)}</span>
                        </button>
                    </div>`;
            }).join('');
        }
    } catch (err) {
        const msg = `<div class="col-span-full p-6 rounded-2xl border border-amber-200 bg-amber-50 text-amber-900 text-[13px]">
            <strong>Info:</strong> Data katalog belum dapat dimuat dari <code>GET /api/pelanggan/katalog</code> (${erikaEscapeHtml(err.message)}). Pastikan backend API Pelanggan sudah aktif.
        </div>`;
        katGrid.innerHTML = msg;
        promoGrid.innerHTML = '';
    }
});
</script>
<?= $this->endSection() ?>
