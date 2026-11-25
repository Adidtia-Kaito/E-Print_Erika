<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<!-- 04 · Daftar Layanan & Katalog ATK (Figma 4:484 + FR-03) -->
<section class="bg-surface border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] pt-10 pb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <h1 class="text-[32px] sm:text-[38px] font-extrabold text-ink tracking-tight">Semua Layanan &amp; Katalog ATK</h1>
                <p class="text-[15px] text-muted mt-1">Lihat harga dasar, estimasi durasi pengerjaan, dan pilih layanan atau produk ATK.</p>
            </div>

            <!-- Search Input -->
            <div class="relative w-full md:w-[320px]">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input id="katalog-search-input" type="text" placeholder="Cari layanan atau produk ATK…" class="w-full h-[44px] pl-11 pr-4 rounded-[12px] bg-white border border-line text-[14px] text-ink placeholder:text-muted/70 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
            </div>
        </div>

        <!-- Filter Tabs (Kategori Layanan + Katalog ATK) -->
        <div id="katalog-filter-tabs" class="flex items-center gap-2.5 overflow-x-auto no-scrollbar pt-7">
            <button type="button" data-tab="SEMUA" class="tab-btn px-4 py-2 rounded-[10px] text-[13px] font-semibold bg-primary text-white shrink-0 transition">Semua</button>
            <button type="button" data-tab="ATK" class="tab-btn px-4 py-2 rounded-[10px] text-[13px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0 transition">Katalog ATK</button>
        </div>
    </div>
</section>

<section class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-10 space-y-10">
    <!-- Info Banner untuk Tamu -->
    <div data-guest-only class="rounded-xl bg-surface border border-line px-4 py-3 flex items-center gap-3 text-[13px] text-muted">
        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        <span>Kamu bisa menjelajah semua layanan dan menghitung estimasi biaya secara gratis. <button type="button" onclick="ErikaAuth.openLoginModal()" class="font-bold text-primary hover:underline">Masuk</button> diperlukan saat checkout pesanan.</span>
    </div>

    <!-- Section Layanan Cetak -->
    <div id="section-layanan-wrapper">
        <div id="layanan-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="h-[380px] rounded-[20px] border border-line bg-surface animate-pulse"></div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Section Katalog Produk ATK (FR-03) -->
    <div id="section-atk-wrapper" class="space-y-5">
        <div class="flex items-center justify-between border-t border-line pt-8">
            <div>
                <h2 class="text-[22px] font-extrabold text-ink">Katalog Produk ATK</h2>
                <p class="text-[13px] text-muted mt-0.5">Alat tulis dan perlengkapan kantor siap beli bersamaan dengan pesanan cetak.</p>
            </div>
        </div>
        <div id="atk-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"></div>
    </div>

    <p class="text-[12px] text-muted pt-2">* Harga dasar, opsi tambahan, dan estimasi waktu pengerjaan diambil secara real-time dari katalog sistem.</p>
</section>

<!-- Modal Detail Layanan -->
<div id="modal-detail-layanan" class="hidden fixed inset-0 z-50 bg-ink/55 backdrop-blur-sm flex items-center justify-center p-4" onclick="if(event.target===this) closeDetailModal()">
    <div class="w-full max-w-lg bg-white rounded-[20px] shadow-2xl border border-line p-7 space-y-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <span id="det-badge" class="inline-block px-2.5 py-1 rounded-md bg-primary/10 text-primary text-[11px] font-bold"></span>
                <h3 id="det-nama" class="font-extrabold text-[22px] text-ink mt-2"></h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="p-1.5 rounded-lg text-muted hover:bg-surface">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p id="det-desc" class="text-[14px] text-muted leading-relaxed"></p>
        <div id="det-opsi-list" class="space-y-2 max-h-56 overflow-y-auto"></div>
        <div class="pt-3 border-t border-line flex items-center justify-between gap-4">
            <div>
                <span class="block text-[11px] text-muted">Harga mulai dari</span>
                <span id="det-harga" class="text-[20px] font-extrabold text-primary"></span>
            </div>
            <a id="det-pesan-link" href="#" class="bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] px-6 py-3 rounded-[12px] transition">
                Konfigurasi &amp; Pesan &rarr;
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let katalogData = { kategori_layanan: [], produk_atk: [] };
let activeTab = 'SEMUA';
let searchKeyword = '';

function openDetailModal(idLayanan) {
    const lay = ErikaKatalog.findLayananById(katalogData, idLayanan);
    if (!lay) return;

    document.getElementById('det-badge').textContent = `${lay.kategori?.nama_kategori || 'Layanan'} • ${lay.kode_layanan}`;
    document.getElementById('det-nama').textContent = lay.nama_layanan;
    document.getElementById('det-desc').textContent = lay.deskripsi || '-';
    document.getElementById('det-harga').textContent = `${erikaFormatRp(lay.harga_dasar)} / ${lay.satuan_hitung}`;
    document.getElementById('det-pesan-link').href = ErikaKatalog.routeForLayanan(lay);

    const opsiWrap = document.getElementById('det-opsi-list');
    const opsi = lay.opsi_layanan || [];
    if (opsi.length === 0) {
        opsiWrap.innerHTML = `<p class="text-[12px] text-muted italic">Tidak ada opsi tambahan khusus.</p>`;
    } else {
        opsiWrap.innerHTML = `<p class="text-[12px] font-bold text-ink uppercase tracking-wider mb-1">Opsi Tersedia</p>` +
            opsi.map((o) => `
                <div class="flex items-center justify-between text-[13px] px-3 py-2 rounded-lg bg-surface border border-line">
                    <span><strong>${erikaEscapeHtml(o.grup_opsi)}:</strong> ${erikaEscapeHtml(o.nama_opsi)}</span>
                    <span class="font-semibold text-primary">+${erikaFormatRp(o.harga_tambahan)} ${o.tipe_harga === 'PER_UNIT' ? '/unit' : ''}</span>
                </div>`).join('');
    }
    document.getElementById('modal-detail-layanan').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('modal-detail-layanan').classList.add('hidden');
}

function setActiveTab(tabValue) {
    activeTab = tabValue;
    document.querySelectorAll('#katalog-filter-tabs .tab-btn').forEach((btn) => {
        const isActive = btn.getAttribute('data-tab') === String(tabValue);
        btn.className = `tab-btn px-4 py-2 rounded-[10px] text-[13px] font-semibold shrink-0 transition ${
            isActive
                ? 'bg-primary text-white shadow-sm'
                : 'bg-white border border-line text-muted hover:text-ink'
        }`;
    });
    renderCatalogItems();
}

function renderCatalogItems() {
    const layananGrid = document.getElementById('layanan-cards-grid');
    const atkGrid = document.getElementById('atk-cards-grid');
    const layananWrap = document.getElementById('section-layanan-wrapper');
    const atkWrap = document.getElementById('section-atk-wrapper');

    const kw = searchKeyword.trim().toLowerCase();
    const showLayanan = activeTab !== 'ATK';
    const showAtk = activeTab === 'SEMUA' || activeTab === 'ATK';

    layananWrap.classList.toggle('hidden', !showLayanan);
    atkWrap.classList.toggle('hidden', !showAtk);

    if (showLayanan) {
        const items = [];
        (katalogData.kategori_layanan || []).forEach((kat) => {
            if (activeTab !== 'SEMUA' && String(kat.id_kategori_layanan) !== String(activeTab)) return;
            (kat.layanan || []).forEach((l) => {
                const hay = `${l.nama_layanan} ${l.kode_layanan} ${l.deskripsi || ''} ${kat.nama_kategori}`.toLowerCase();
                if (!kw || hay.includes(kw)) {
                    items.push({ ...l, nama_kategori: kat.nama_kategori });
                }
            });
        });

        if (items.length === 0) {
            layananGrid.innerHTML = `<div class="col-span-full p-10 text-center rounded-2xl border border-line bg-surface text-muted text-[14px]">Tidak ada layanan yang sesuai dengan filter pencarian.</div>`;
        } else {
            layananGrid.innerHTML = items.map((l) => {
                const mins = Number(l.estimasi_durasi_menit || 30);
                const durasi = mins >= 1440 ? `${Math.round(mins / 1440)} hari` : (mins >= 60 ? `± ${Math.round(mins / 60)} jam` : `± ${mins} menit`);
                const targetUrl = ErikaKatalog.routeForLayanan(l);

                return `
                    <div class="bg-white rounded-[20px] border border-line hover:border-primary/40 hover:shadow-lg transition p-5 flex flex-col justify-between">
                        <div>
                            <div class="h-[150px] rounded-[14px] bg-surface border border-line/70 flex flex-col items-center justify-center overflow-hidden mb-4">
                                ${l.gambar_contoh
                                    ? `<img src="${window.ERIKA_CONFIG.baseUrl}/${erikaEscapeHtml(l.gambar_contoh)}" alt="${erikaEscapeHtml(l.nama_layanan)}" class="w-full h-full object-cover">`
                                    : `<svg class="w-10 h-10 text-primary/40" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                       <span class="text-[11px] font-semibold text-muted mt-1.5">${erikaEscapeHtml(l.kode_layanan)}</span>`
                                }
                            </div>
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <span class="px-2.5 py-1 rounded-md bg-primary/10 text-primary text-[11px] font-bold">${erikaEscapeHtml(l.nama_kategori)}</span>
                                <span class="text-[11px] font-semibold text-muted">${erikaEscapeHtml(l.kode_layanan)}</span>
                            </div>
                            <h3 class="font-bold text-[19px] text-ink">${erikaEscapeHtml(l.nama_layanan)}</h3>
                            <p class="text-[13px] text-muted mt-1.5 line-clamp-2 leading-relaxed">${erikaEscapeHtml(l.deskripsi || '')}</p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-line space-y-4">
                            <div class="flex items-end justify-between">
                                <div>
                                    <span class="block text-[11px] text-muted">Mulai dari</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-[20px] font-extrabold text-primary">${erikaFormatRp(l.harga_dasar)}</span>
                                        <span class="text-[12px] text-muted">/ ${erikaEscapeHtml(l.satuan_hitung)}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 text-[12px] font-medium text-muted">
                                    <svg class="w-4 h-4 text-cyan" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    ${durasi}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                <button type="button" onclick="openDetailModal(${l.id_layanan})" class="h-[42px] rounded-[10px] border border-line bg-white hover:bg-surface text-ink font-semibold text-[13px] transition">
                                    Detail
                                </button>
                                <a href="${targetUrl}" class="h-[42px] rounded-[10px] bg-primary hover:bg-[#212675] text-white font-semibold text-[13px] flex items-center justify-center gap-1.5 transition">
                                    <span>Pesan</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>`;
            }).join('');
        }
    }

    if (showAtk) {
        const produkList = (katalogData.produk_atk || []).filter((p) => {
            if (!kw) return true;
            return `${p.nama_produk} ${p.sku} ${p.merek || ''}`.toLowerCase().includes(kw);
        });

        if (produkList.length === 0) {
            atkGrid.innerHTML = `<div class="col-span-full p-8 text-center rounded-2xl border border-line bg-surface text-muted text-[14px]">Tidak ada produk ATK yang ditemukan.</div>`;
        } else {
            atkGrid.innerHTML = produkList.map((p, idx) => {
                const stokHabis = Boolean(p.stok_habis) || Number(p.stok || 0) <= 0;
                return `
                    <div class="bg-white rounded-[18px] border border-line p-5 flex flex-col justify-between ${stokHabis ? 'opacity-85' : 'hover:border-primary/40 hover:shadow-md'} transition">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded bg-yellow/25 text-ink text-[11px] font-bold">${erikaEscapeHtml(p.sku)}</span>
                                ${stokHabis
                                    ? `<span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-[11px] font-bold">Stok Habis</span>`
                                    : `<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">Tersedia</span>`
                                }
                            </div>
                            <h3 class="font-bold text-[16px] text-ink">${erikaEscapeHtml(p.nama_produk)}</h3>
                            <p class="text-[12px] text-muted mt-1">${erikaEscapeHtml(p.merek || 'Perlengkapan ATK')} &bull; Satuan ${erikaEscapeHtml(p.satuan)}</p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-line space-y-3">
                            <div class="flex items-baseline justify-between">
                                <span class="text-[18px] font-extrabold text-primary">${erikaFormatRp(p.harga_jual)}</span>
                                <span class="text-[11px] text-muted">/ ${erikaEscapeHtml(p.satuan)}</span>
                            </div>
                            <button type="button"
                                ${stokHabis ? 'disabled' : `onclick="addAtkByIndex(${idx})"`}
                                data-atk-id="${p.id_produk}"
                                class="w-full h-[40px] rounded-[10px] font-semibold text-[13px] transition flex items-center justify-center gap-1.5 ${
                                    stokHabis
                                        ? 'bg-slate-200 text-slate-500 cursor-not-allowed'
                                        : 'bg-primary hover:bg-[#212675] text-white'
                                }">
                                ${stokHabis ? 'Stok Habis' : '+ Tambah ke Keranjang'}
                            </button>
                        </div>
                    </div>`;
            }).join('');
        }
    }
}

function addAtkByIndex(filteredIdx) {
    const kw = searchKeyword.trim().toLowerCase();
    const list = (katalogData.produk_atk || []).filter((p) => {
        if (!kw) return true;
        return `${p.nama_produk} ${p.sku} ${p.merek || ''}`.toLowerCase().includes(kw);
    });
    const produk = list[filteredIdx];
    if (produk) ErikaCart.addProdukItem(produk, 1);
}

document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);
    const initialKat = params.get('kategori');
    const initialTabParam = params.get('tab');

    const searchInput = document.getElementById('katalog-search-input');
    searchInput?.addEventListener('input', (e) => {
        searchKeyword = e.target.value || '';
        renderCatalogItems();
    });

    try {
        katalogData = await ErikaKatalog.load();
        const tabsEl = document.getElementById('katalog-filter-tabs');
        const katBtns = (katalogData.kategori_layanan || []).map((k) =>
            `<button type="button" data-tab="${k.id_kategori_layanan}" class="tab-btn px-4 py-2 rounded-[10px] text-[13px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0 transition">${erikaEscapeHtml(k.nama_kategori)}</button>`
        ).join('');

        tabsEl.innerHTML = `
            <button type="button" data-tab="SEMUA" class="tab-btn px-4 py-2 rounded-[10px] text-[13px] font-semibold bg-primary text-white shrink-0 transition">Semua</button>
            ${katBtns}
            <button type="button" data-tab="ATK" class="tab-btn px-4 py-2 rounded-[10px] text-[13px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0 transition">Katalog ATK</button>
        `;

        tabsEl.querySelectorAll('.tab-btn').forEach((btn) => {
            btn.addEventListener('click', () => setActiveTab(btn.getAttribute('data-tab')));
        });

        if (initialTabParam === 'atk') {
            setActiveTab('ATK');
        } else if (initialKat) {
            setActiveTab(initialKat);
        } else {
            renderCatalogItems();
        }
    } catch (err) {
        document.getElementById('layanan-cards-grid').innerHTML = `
            <div class="col-span-full p-6 rounded-2xl border border-amber-200 bg-amber-50 text-amber-900 text-[13px]">
                <strong>Gagal memuat katalog:</strong> <code>GET /api/pelanggan/katalog</code> (${erikaEscapeHtml(err.message)}).
            </div>`;
    }
});
</script>
<?= $this->endSection() ?>
