<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<!-- 09 · Keranjang & Pembayaran (Figma 8:462) -->
<section class="bg-surface border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-8 sm:py-10">
        <p class="text-[13px] font-semibold text-muted">Keranjang / Pembayaran</p>
        <h1 class="text-[30px] sm:text-[38px] font-extrabold text-ink tracking-tight mt-1.5">Keranjang &amp; Pembayaran</h1>
        <p class="text-[15px] text-muted mt-1">Periksa item layanan cetak &amp; ATK, terapkan kode promo, dan konfirmasi pembayaran Anda.</p>
    </div>
</section>

<section class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-10">
    <!-- Empty State -->
    <div id="co-empty-state" class="hidden bg-white rounded-[20px] border border-line p-12 text-center max-w-xl mx-auto">
        <div class="w-16 h-16 rounded-2xl bg-surface text-muted mx-auto flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h2 class="text-[20px] font-extrabold text-ink">Keranjang Belanja Kosong</h2>
        <p class="text-[14px] text-muted mt-1.5 mb-6">Anda belum menambahkan layanan cetak atau produk ATK ke dalam keranjang.</p>
        <a href="<?= site_url('layanan') ?>" class="inline-flex items-center gap-2 bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] px-6 py-3 rounded-[12px] transition">
            <span>Jelajahi Layanan &amp; ATK</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </div>

    <!-- Main Checkout Grid -->
    <form id="co-main-form" onsubmit="submitCheckout(event)" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kolom Kiri: Item, Promo, Kontak, Pembayaran -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Section 1: Daftar Item Layanan & Produk ATK -->
            <div class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-primary text-white text-[13px] font-bold flex items-center justify-center">1</span>
                        <h2 class="font-bold text-[18px] text-ink">Item Pesanan</h2>
                    </div>
                    <a href="<?= site_url('layanan') ?>" class="text-[13px] font-semibold text-primary hover:underline">+ Tambah Item Lain</a>
                </div>

                <div id="co-items-list" class="divide-y divide-line"></div>
            </div>

            <!-- Section 2: Kode Promo -->
            <div class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-magenta text-white text-[13px] font-bold flex items-center justify-center">2</span>
                    <h2 class="font-bold text-[18px] text-ink">Kode Promo</h2>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <input id="co-kode-promo" type="text" placeholder="Masukkan kode promo (misal: UNDANG10, CETAK15)" class="flex-1 h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink uppercase placeholder:normal-case focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    <button id="co-btn-promo" type="button" onclick="applyPromoCode()" class="h-[44px] px-6 rounded-[10px] bg-ink hover:bg-primary text-white font-semibold text-[14px] transition shrink-0">
                        Terapkan
                    </button>
                </div>

                <div id="co-promo-feedback" class="hidden rounded-xl px-4 py-2.5 text-[13px] font-medium"></div>
            </div>

            <!-- Section 3: Informasi Pesanan & Catatan -->
            <div class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-cyan text-white text-[13px] font-bold flex items-center justify-center">3</span>
                    <div>
                        <h2 class="font-bold text-[18px] text-ink">Informasi Pesanan</h2>
                        <p class="text-[12px] text-muted">Pesanan diambil langsung di toko Percetakan Erika setelah selesai.</p>
                    </div>
                </div>

                <div>
                    <label for="co-nama-pesanan" class="block text-[13px] font-semibold text-ink mb-1.5">Nama / Judul Pesanan</label>
                    <input id="co-nama-pesanan" type="text" required placeholder="Contoh: Cetak Skripsi &amp; Map ATK" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                </div>

                <div>
                    <label for="co-catatan" class="block text-[13px] font-semibold text-ink mb-1.5">Catatan untuk Percetakan (Opsional)</label>
                    <textarea id="co-catatan" rows="2" placeholder="Contoh: Tolong kabari lewat WhatsApp jika sudah siap diambil..." class="w-full p-3.5 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15"></textarea>
                </div>
            </div>

            <!-- Section 4: Metode Pembayaran & Upload Bukti -->
            <div class="bg-white rounded-[20px] border border-line p-6 sm:p-7 space-y-5">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-yellow text-ink text-[13px] font-bold flex items-center justify-center">4</span>
                    <div>
                        <h2 class="font-bold text-[18px] text-ink">Metode Pembayaran</h2>
                        <p class="text-[12px] text-muted">Pilih metode pembayaran dan unggah bukti untuk QRIS / Transfer Bank.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="QRIS" class="peer sr-only" checked onchange="onPaymentMethodChange()">
                        <div class="p-4 rounded-xl border border-line peer-checked:border-primary peer-checked:bg-primary/[0.04] peer-checked:ring-1 peer-checked:ring-primary transition">
                            <p class="font-bold text-[14px] text-ink">QRIS</p>
                            <p class="text-[12px] text-muted mt-0.5">GoPay, OVO, DANA, m-Banking</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="TRANSFER_BANK" class="peer sr-only" onchange="onPaymentMethodChange()">
                        <div class="p-4 rounded-xl border border-line peer-checked:border-primary peer-checked:bg-primary/[0.04] peer-checked:ring-1 peer-checked:ring-primary transition">
                            <p class="font-bold text-[14px] text-ink">Transfer Bank</p>
                            <p class="text-[12px] text-muted mt-0.5">BCA / BRI / Mandiri</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="TUNAI" class="peer sr-only" onchange="onPaymentMethodChange()">
                        <div class="p-4 rounded-xl border border-line peer-checked:border-primary peer-checked:bg-primary/[0.04] peer-checked:ring-1 peer-checked:ring-primary transition">
                            <p class="font-bold text-[14px] text-ink">Tunai di Kasir</p>
                            <p class="text-[12px] text-muted mt-0.5">Bayar saat di toko</p>
                        </div>
                    </label>
                </div>

                <!-- Info Rekening / QRIS Toko -->
                <div id="co-payment-info-qris" class="rounded-2xl bg-surface border border-line p-5 flex flex-col sm:flex-row items-center gap-5">
                    <div class="w-28 h-28 rounded-xl bg-white border border-line flex flex-col items-center justify-center shrink-0 p-2">
                        <svg class="w-16 h-16 text-ink" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm11-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-4 0h2v4h-2v-4zm4 4h2v2h-2v-2zm-2-2h2v2h-2v-2zm-4 2h2v2h-2v-2z"/>
                        </svg>
                        <span class="text-[9px] font-bold text-magenta tracking-wider mt-1">QRIS ERIKA</span>
                    </div>
                    <div class="space-y-1 text-center sm:text-left">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-magenta/10 text-magenta text-[11px] font-bold">Scan QRIS Toko</span>
                        <h3 class="font-bold text-[15px] text-ink">Percetakan Erika Official (NMID: ID1026001928374)</h3>
                        <p class="text-[13px] text-muted">Scan menggunakan aplikasi e-wallet atau mobile banking apa saja sesuai nominal Total Bayar, lalu unggah bukti transfer di bawah ini.</p>
                    </div>
                </div>

                <div id="co-payment-info-bank" class="hidden rounded-2xl bg-surface border border-line p-5 space-y-3">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-[11px] font-bold">Rekening Resmi Toko</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-[13px]">
                        <div class="p-3 rounded-xl bg-white border border-line">
                            <p class="font-bold text-primary">Bank BCA</p>
                            <p class="font-extrabold text-ink text-[15px] mt-0.5">8077-0812-3456</p>
                            <p class="text-[11px] text-muted">a.n. Erika Siregar</p>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-line">
                            <p class="font-bold text-primary">Bank BRI</p>
                            <p class="font-extrabold text-ink text-[15px] mt-0.5">8830-0812-9981</p>
                            <p class="text-[11px] text-muted">a.n. Erika Siregar</p>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-line">
                            <p class="font-bold text-primary">Bank Mandiri</p>
                            <p class="font-extrabold text-ink text-[15px] mt-0.5">8908-1234-5566</p>
                            <p class="text-[11px] text-muted">a.n. Percetakan Erika</p>
                        </div>
                    </div>
                </div>

                <div id="co-payment-info-tunai" class="hidden rounded-2xl bg-amber-50 border border-amber-200 p-4 text-[13px] text-amber-900">
                    <strong>Pembayaran Tunai:</strong> Tidak perlu unggah bukti pembayaran. Silakan bayar langsung di kasir Percetakan Erika dengan menyebutkan kode pesanan Anda.
                </div>

                <!-- Upload Bukti Pembayaran (Wajib jika QRIS / TRANSFER_BANK) -->
                <div id="co-bukti-wrapper" class="space-y-2">
                    <label for="co-bukti-file" class="block text-[13px] font-semibold text-ink">
                        Upload Bukti Pembayaran <span class="text-magenta">*</span>
                        <span class="font-normal text-muted">(JPG, PNG, atau PDF &bull; maks. 5 MB)</span>
                    </label>
                    <input id="co-bukti-file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-[13px] text-muted file:mr-4 file:py-2.5 file:px-4 file:rounded-[10px] file:border-0 file:text-[13px] file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 border border-line rounded-[12px] p-2 bg-surface">
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Pembayaran Sticky -->
        <aside class="lg:col-span-4 lg:sticky lg:top-24">
            <div class="bg-white rounded-[20px] border border-line shadow-sm p-6 space-y-4">
                <h3 class="font-extrabold text-[18px] text-ink pb-3 border-b border-line">Ringkasan Pembayaran</h3>

                <div class="space-y-2.5 text-[13px]">
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Subtotal layanan cetak</span>
                        <span id="co-sub-layanan" class="font-semibold text-ink">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Subtotal produk ATK</span>
                        <span id="co-sub-produk" class="font-semibold text-ink">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Diskon promo</span>
                        <span id="co-diskon-val" class="font-semibold text-magenta">&mdash;</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-line flex items-baseline justify-between">
                    <span class="font-bold text-[15px] text-ink">Total Bayar</span>
                    <span id="co-total-bayar" class="text-[26px] font-extrabold text-primary">Rp 0</span>
                </div>

                <button id="co-btn-submit" type="submit" class="w-full h-[48px] bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] rounded-[12px] flex items-center justify-center gap-2 transition shadow-md shadow-primary/20">
                    <span>Konfirmasi &amp; Kirim Pesanan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>

                <p class="text-[11px] text-muted leading-relaxed text-center">
                    Pesanan akan langsung masuk ke antrean produksi Percetakan Erika dan dapat dipantau di halaman Pesanan Saya.
                </p>
            </div>
        </aside>
    </form>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const apiBase = (path) => `${window.ERIKA_CONFIG.siteUrl}/${path.replace(/^\/+/, '')}`;
let appliedPromo = null;

function computeCartTotals() {
    const items = ErikaCart.getItems();
    let subLayanan = 0;
    let subProduk = 0;
    items.forEach((it) => {
        if (it.type === 'produk') subProduk += Number(it.sub_total || 0);
        else subLayanan += Number(it.sub_total || 0);
    });
    const subtotal = subLayanan + subProduk;
    const diskon = appliedPromo ? Number(appliedPromo.nominal_diskon || 0) : 0;
    const total = Math.max(0, subtotal - diskon);
    return { items, subLayanan, subProduk, subtotal, diskon, total };
}

function renderCheckoutView() {
    const { items, subLayanan, subProduk, diskon, total } = computeCartTotals();
    const emptyEl = document.getElementById('co-empty-state');
    const formEl = document.getElementById('co-main-form');

    if (items.length === 0) {
        emptyEl.classList.remove('hidden');
        formEl.classList.add('hidden');
        return;
    }

    emptyEl.classList.add('hidden');
    formEl.classList.remove('hidden');

    // Auto-isi nama pesanan jika masih kosong
    const namaInput = document.getElementById('co-nama-pesanan');
    if (namaInput && !namaInput.value.trim()) {
        const first = items[0];
        namaInput.value = first.judul_pekerjaan || first.nama_layanan || first.nama_produk || 'Pesanan Cetak';
    }

    const listEl = document.getElementById('co-items-list');
    listEl.innerHTML = items.map((item) => {
        if (item.type === 'produk') {
            return `
                <div class="py-4 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-yellow/20 text-ink flex items-center justify-center font-extrabold text-[11px] shrink-0">ATK</div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-[15px] text-ink">${erikaEscapeHtml(item.nama_produk)}</h4>
                            <p class="text-[12px] text-muted mt-0.5">${item.jumlah} ${erikaEscapeHtml(item.satuan)} &times; ${erikaFormatRp(item.harga_jual)}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="font-extrabold text-[15px] text-ink">${erikaFormatRp(item.sub_total)}</span>
                        <button type="button" onclick="removeCheckoutItem('${item.cart_id}')" class="p-1.5 text-muted hover:text-red-600 rounded-lg" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>`;
        }

        const memFiles = ErikaCart.getFilesForItem(item.cart_id);
        const filesBadge = (item.files_meta || []).length > 0
            ? `<p class="text-[12px] text-cyan font-semibold mt-1">📎 ${(item.files_meta || []).map((f) => erikaEscapeHtml(f.name)).join(', ')} (${memFiles.length} file siap kirim)</p>`
            : '';

        return `
            <div class="py-4 flex items-start justify-between gap-4">
                <div class="flex items-start gap-3.5 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-extrabold text-[11px] shrink-0">${erikaEscapeHtml(item.kode_layanan || 'CETAK')}</div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-[15px] text-ink">${erikaEscapeHtml(item.nama_layanan)}${item.judul_pekerjaan ? ` — ${erikaEscapeHtml(item.judul_pekerjaan)}` : ''}</h4>
                        <p class="text-[12px] text-muted mt-0.5">
                            ${item.luas_m2 ? `${item.panjang_cm}×${item.lebar_cm} cm (${Number(item.luas_m2).toFixed(2)} m²) • ` : `${item.jumlah_halaman} hlm × `}
                            ${item.jumlah_salinan} salinan
                        </p>
                        ${filesBadge}
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="font-extrabold text-[15px] text-ink">${erikaFormatRp(item.sub_total)}</span>
                    <button type="button" onclick="removeCheckoutItem('${item.cart_id}')" class="p-1.5 text-muted hover:text-red-600 rounded-lg" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>`;
    }).join('');

    document.getElementById('co-sub-layanan').textContent = erikaFormatRp(subLayanan);
    document.getElementById('co-sub-produk').textContent = erikaFormatRp(subProduk);
    document.getElementById('co-diskon-val').textContent = diskon > 0 ? `- ${erikaFormatRp(diskon)}` : '—';
    document.getElementById('co-total-bayar').textContent = erikaFormatRp(total);
}

async function removeCheckoutItem(cartId) {
    await ErikaCart.removeItem(cartId);
    appliedPromo = null;
    document.getElementById('co-promo-feedback')?.classList.add('hidden');
    renderCheckoutView();
}

function onPaymentMethodChange() {
    const val = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'QRIS';
    document.getElementById('co-payment-info-qris').classList.toggle('hidden', val !== 'QRIS');
    document.getElementById('co-payment-info-bank').classList.toggle('hidden', val !== 'TRANSFER_BANK');
    document.getElementById('co-payment-info-tunai').classList.toggle('hidden', val !== 'TUNAI');
    document.getElementById('co-bukti-wrapper').classList.toggle('hidden', val === 'TUNAI');
}

async function applyPromoCode() {
    const kode = document.getElementById('co-kode-promo').value.trim().toUpperCase();
    const fb = document.getElementById('co-promo-feedback');
    if (!kode) {
        ErikaUI.toast('Masukkan kode promo terlebih dahulu.', 'warning');
        return;
    }

    if (!ErikaAuth.isLoggedIn()) {
        ErikaAuth.openLoginModal(() => applyPromoCode());
        return;
    }

    const { items, subtotal } = computeCartTotals();
    const kategoriSet = new Set();
    try {
        const katalog = await ErikaKatalog.load();
        items.filter((i) => i.type === 'layanan').forEach((i) => {
            const lay = ErikaKatalog.findLayananById(katalog, i.id_layanan);
            if (lay?.id_kategori_layanan) kategoriSet.add(Number(lay.id_kategori_layanan));
        });
    } catch {
        // Biarkan kosong jika katalog gagal dimuat
    }

    const btn = document.getElementById('co-btn-promo');
    btn.disabled = true;
    btn.textContent = 'Memeriksa...';

    try {
        const res = await fetch(apiBase('api/pelanggan/cek-promo'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            },
            body: JSON.stringify({
                kode_promo: kode,
                subtotal,
                kategori_ids: Array.from(kategoriSet)
            })
        });
        const json = await res.json().catch(() => null);

        if (!res.ok || !json || !json.status) {
            appliedPromo = null;
            fb.className = 'rounded-xl px-4 py-2.5 text-[13px] font-medium bg-red-50 text-red-700 border border-red-200';
            fb.textContent = json?.message || `Promo tidak dapat digunakan (HTTP ${res.status}).`;
            fb.classList.remove('hidden');
            renderCheckoutView();
            return;
        }

        appliedPromo = json.data;
        fb.className = 'rounded-xl px-4 py-2.5 text-[13px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200';
        fb.textContent = `Promo ${appliedPromo.kode_promo} diterapkan! Hemat ${erikaFormatRp(appliedPromo.nominal_diskon)}.`;
        fb.classList.remove('hidden');
        renderCheckoutView();
    } catch {
        fb.className = 'rounded-xl px-4 py-2.5 text-[13px] font-medium bg-red-50 text-red-700 border border-red-200';
        fb.textContent = 'Gagal menghubungi server untuk cek promo.';
        fb.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Terapkan';
    }
}

async function submitCheckout(e) {
    e.preventDefault();

    if (!ErikaAuth.isLoggedIn()) {
        ErikaAuth.openLoginModal(() => ErikaUI.toast('Silakan klik Konfirmasi & Kirim Pesanan kembali.', 'info'));
        return;
    }

    await ErikaCart.restoreFiles();
    const { items } = computeCartTotals();
    if (items.length === 0) return;

    const metode = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'QRIS';
    const buktiInput = document.getElementById('co-bukti-file');
    const buktiFile = buktiInput?.files?.[0] || null;

    if (metode !== 'TUNAI' && !buktiFile) {
        ErikaUI.toast('Untuk metode QRIS atau Transfer Bank, wajib mengunggah bukti pembayaran.', 'warning');
        buktiInput?.focus();
        return;
    }

    const formData = new FormData();
    formData.append('nama_pesanan', document.getElementById('co-nama-pesanan').value.trim());
    formData.append('catatan_pelanggan', document.getElementById('co-catatan').value.trim());
    formData.append('metode_pembayaran', metode);

    if (appliedPromo?.kode_promo) {
        formData.append('kode_promo', appliedPromo.kode_promo);
    }

    const itemsLayanan = [];
    const itemsProduk = [];
    let fileCounter = 0;

    items.forEach((it) => {
        if (it.type === 'produk') {
            itemsProduk.push({
                id_produk: Number(it.id_produk),
                jumlah: Number(it.jumlah)
            });
        } else {
            const attachedFiles = ErikaCart.getFilesForItem(it.cart_id);
            const fileIndexes = [];
            attachedFiles.forEach((fileObj) => {
                formData.append('files_layanan[]', fileObj, fileObj.name);
                fileIndexes.push(fileCounter++);
            });

            itemsLayanan.push({
                id_layanan: Number(it.id_layanan),
                judul_pekerjaan: it.judul_pekerjaan || it.nama_layanan,
                jumlah_halaman: Number(it.jumlah_halaman || 1),
                jumlah_salinan: Number(it.jumlah_salinan || 1),
                panjang_cm: it.panjang_cm ? Number(it.panjang_cm) : null,
                lebar_cm: it.lebar_cm ? Number(it.lebar_cm) : null,
                spesifikasi_tambahan: it.spesifikasi_tambahan || null,
                catatan_pekerjaan: it.catatan_pekerjaan || null,
                opsi_ids: it.opsi_ids || [],
                file_index: fileIndexes
            });
        }
    });

    formData.append('items_layanan', JSON.stringify(itemsLayanan));
    formData.append('items_produk', JSON.stringify(itemsProduk));
    if (buktiFile) {
        formData.append('bukti_pembayaran', buktiFile, buktiFile.name);
    }

    const btn = document.getElementById('co-btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<span>Mengirim Pesanan...</span>';

    try {
        const res = await fetch(apiBase('api/pelanggan/checkout'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            },
            body: formData
        });
        const json = await res.json().catch(() => null);

        if (!res.ok || !json || !json.status) {
            if (res.status === 401) {
                ErikaAuth.logout();
                ErikaAuth.openLoginModal();
            }
            ErikaUI.toast(json?.message || `Checkout gagal (HTTP ${res.status}).`, 'error');
            return;
        }

        await ErikaCart.clearAll();
        ErikaUI.toast('Pesanan berhasil dibuat! Mengalihkan ke Pesanan Saya...', 'success');
        setTimeout(() => {
            window.location.href = apiBase('pesanan-saya');
        }, 800);
    } catch {
        ErikaUI.toast('Tidak dapat terhubung ke server (/api/pelanggan/checkout).', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Konfirmasi &amp; Kirim Pesanan</span><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>';
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    await ErikaCart.restoreFiles();
    onPaymentMethodChange();
    renderCheckoutView();
});
</script>
<?= $this->endSection() ?>
