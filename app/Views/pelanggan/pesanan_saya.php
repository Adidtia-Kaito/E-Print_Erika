<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<!-- 10 · Pesanan Saya (Figma 8:666) -->
<section class="bg-surface border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-8 sm:py-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="text-[13px] font-semibold text-muted">Akun / Pesanan Saya</p>
            <h1 class="text-[30px] sm:text-[38px] font-extrabold text-ink tracking-tight mt-1.5">Pesanan Saya</h1>
            <p class="text-[15px] text-muted mt-1">Pantau status pengerjaan, nomor antrean FIFO, unggah revisi berkas, atau ajukan komplain.</p>
        </div>
        <button type="button" onclick="loadOrders()" class="inline-flex items-center gap-2 bg-white hover:bg-surface border border-line text-ink font-semibold text-[13px] px-4 py-2.5 rounded-[10px] transition shrink-0">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Muat Ulang</span>
        </button>
    </div>
</section>

<section class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-10">
    <!-- Guest Gate -->
    <div id="ps-guest-gate" class="hidden bg-white rounded-[20px] border border-line p-10 text-center max-w-lg mx-auto">
        <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary mx-auto flex items-center justify-center mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h2 class="text-[20px] font-extrabold text-ink">Masuk untuk Melihat Pesanan</h2>
        <p class="text-[14px] text-muted mt-1.5 mb-6">Riwayat pesanan dan nomor antrean terhubung dengan akun pelanggan Anda.</p>
        <button type="button" onclick="ErikaAuth.openLoginModal(() => loadOrders())" class="bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] px-6 py-3 rounded-[12px] transition">
            Masuk Sekarang
        </button>
    </div>

    <!-- Main Master-Detail Layout -->
    <div id="ps-main-layout" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
        <!-- Kolom Kiri: Filter & Daftar Pesanan (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Status Filter Tabs -->
            <div id="ps-status-filters" class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <button type="button" data-status="SEMUA" class="ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold bg-primary text-white shrink-0">Semua</button>
                <button type="button" data-status="MENUNGGU_KONFIRMASI" class="ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0">Menunggu</button>
                <button type="button" data-status="DIPROSES_DICETAK" class="ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0">Diproses</button>
                <button type="button" data-status="PERLU_REVISI" class="ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0">Perlu Revisi</button>
                <button type="button" data-status="SIAP_DIAMBIL" class="ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0">Siap Diambil</button>
                <button type="button" data-status="SELESAI" class="ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold bg-white border border-line text-muted hover:text-ink shrink-0">Selesai</button>
            </div>

            <div id="ps-orders-list" class="space-y-3"></div>
        </div>

        <!-- Kolom Kanan: Detail Pesanan (7 cols) -->
        <div class="lg:col-span-7">
            <div id="ps-detail-panel" class="bg-white rounded-[20px] border border-line p-6 sm:p-7">
                <div class="text-center py-12 text-muted text-[14px]">Pilih salah satu pesanan di sebelah kiri untuk melihat detail lengkap.</div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Ajukan Komplain / Revisi -->
<div id="modal-komplain" class="hidden fixed inset-0 z-50 bg-ink/55 backdrop-blur-sm flex items-center justify-center p-4" onclick="if(event.target===this) closeKomplainModal()">
    <div class="w-full max-w-md bg-white rounded-[20px] shadow-2xl border border-line p-7 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-[20px] text-ink">Ajukan Komplain / Revisi</h3>
            <button type="button" onclick="closeKomplainModal()" class="p-1.5 rounded-lg text-muted hover:bg-surface">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form onsubmit="submitKomplain(event)" class="space-y-4">
            <input type="hidden" id="komp-id-pesanan">
            <div>
                <label class="block text-[13px] font-semibold text-ink mb-1.5">Jenis Pengajuan</label>
                <select id="komp-jenis" class="w-full h-[44px] px-3.5 rounded-[10px] border border-line text-[14px] text-ink bg-white focus:outline-none focus:border-primary">
                    <option value="KOMPLAIN">Komplain Hasil / Layanan</option>
                    <option value="REVISI">Permintaan Revisi Desain / File</option>
                </select>
            </div>
            <div>
                <label for="komp-pesan" class="block text-[13px] font-semibold text-ink mb-1.5">Detail Pesan</label>
                <textarea id="komp-pesan" rows="4" required placeholder="Jelaskan kendala atau revisi yang dibutuhkan..." class="w-full p-3.5 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeKomplainModal()" class="h-[42px] px-5 rounded-[10px] border border-line text-ink font-semibold text-[13px]">Batal</button>
                <button id="komp-btn-submit" type="submit" class="h-[42px] px-6 rounded-[10px] bg-magenta hover:bg-[#c5126b] text-white font-semibold text-[13px] transition">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const apiBase = (path) => `${window.ERIKA_CONFIG.siteUrl}/${path.replace(/^\/+/, '')}`;

const STATUS_BADGES = {
    MENUNGGU_KONFIRMASI: { label: 'Menunggu Konfirmasi', cls: 'bg-yellow/25 text-ink border border-yellow/40' },
    DIPROSES_DICETAK:    { label: 'Diproses / Dicetak',  cls: 'bg-cyan/15 text-cyan border border-cyan/30' },
    PERLU_REVISI:        { label: 'Perlu Revisi',        cls: 'bg-magenta/15 text-magenta border border-magenta/30' },
    SIAP_DIAMBIL:        { label: 'Siap Diambil',        cls: 'bg-primary/15 text-primary border border-primary/30' },
    SELESAI:             { label: 'Selesai',             cls: 'bg-emerald-100 text-emerald-700 border border-emerald-200' },
    DIBATALKAN:          { label: 'Dibatalkan',          cls: 'bg-red-100 text-red-700 border border-red-200' }
};

const PAY_BADGES = {
    BELUM_BAYAR:         { label: 'Belum Bayar',         cls: 'bg-red-50 text-red-600' },
    MENUNGGU_VERIFIKASI: { label: 'Menunggu Verifikasi', cls: 'bg-amber-100 text-amber-800' },
    LUNAS:               { label: 'Lunas',               cls: 'bg-emerald-100 text-emerald-700' },
    REFUND:              { label: 'Refund',              cls: 'bg-slate-100 text-slate-700' },
    DIBATALKAN:          { label: 'Dibatalkan',          cls: 'bg-red-100 text-red-700' }
};

let allOrders = [];
let selectedOrderId = null;
let currentFilter = 'SEMUA';

function statusBadgeHtml(status) {
    const cfg = STATUS_BADGES[status] || { label: status || '-', cls: 'bg-slate-100 text-slate-700' };
    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ${cfg.cls}">${erikaEscapeHtml(cfg.label)}</span>`;
}

function payBadgeHtml(status) {
    const cfg = PAY_BADGES[status] || { label: status || '-', cls: 'bg-slate-100 text-slate-700' };
    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ${cfg.cls}">${erikaEscapeHtml(cfg.label)}</span>`;
}

function fmtDate(dt) {
    if (!dt) return '—';
    const d = new Date(String(dt).replace(' ', 'T'));
    if (isNaN(d.getTime())) return dt;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' • ' +
        d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

async function loadOrders() {
    const gate = document.getElementById('ps-guest-gate');
    const main = document.getElementById('ps-main-layout');

    if (!ErikaAuth.isLoggedIn()) {
        gate.classList.remove('hidden');
        main.classList.add('hidden');
        return;
    }

    gate.classList.add('hidden');
    main.classList.remove('hidden');

    const listEl = document.getElementById('ps-orders-list');
    listEl.innerHTML = `<div class="h-32 rounded-2xl bg-surface border border-line animate-pulse"></div>`;

    try {
        const res = await fetch(apiBase('api/pelanggan/pesanan'), {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            }
        });
        const json = await res.json().catch(() => null);

        if (res.status === 401) {
            ErikaAuth.logout();
            loadOrders();
            return;
        }
        if (!res.ok || !json || !json.status) {
            throw new Error(json?.message || `HTTP ${res.status}`);
        }

        allOrders = Array.isArray(json.data) ? json.data : [];
        renderOrdersList();

        if (allOrders.length > 0) {
            const targetId = selectedOrderId && allOrders.some((o) => Number(o.id_pesanan) === Number(selectedOrderId))
                ? selectedOrderId
                : allOrders[0].id_pesanan;
            selectOrder(targetId);
        } else {
            document.getElementById('ps-detail-panel').innerHTML = `
                <div class="text-center py-12 text-muted text-[14px]">Belum ada pesanan. <a href="${apiBase('layanan')}" class="text-primary font-bold hover:underline">Mulai pesan layanan</a>.</div>`;
        }
    } catch (err) {
        listEl.innerHTML = `<div class="p-5 rounded-2xl border border-amber-200 bg-amber-50 text-amber-900 text-[13px]">
            Gagal memuat daftar pesanan dari <code>GET /api/pelanggan/pesanan</code> (${erikaEscapeHtml(err.message)}).
        </div>`;
    }
}

function renderOrdersList() {
    const listEl = document.getElementById('ps-orders-list');
    const filtered = currentFilter === 'SEMUA'
        ? allOrders
        : allOrders.filter((o) => o.status_pesanan === currentFilter);

    if (filtered.length === 0) {
        listEl.innerHTML = `<div class="p-8 text-center rounded-2xl border border-line bg-surface text-muted text-[13px]">Tidak ada pesanan pada filter status ini.</div>`;
        return;
    }

    listEl.innerHTML = filtered.map((o) => {
        const active = Number(o.id_pesanan) === Number(selectedOrderId);
        return `
            <button type="button" onclick="selectOrder(${o.id_pesanan})" class="w-full text-left p-5 rounded-[18px] border transition ${
                active ? 'border-primary bg-primary/[0.03] ring-1 ring-primary' : 'border-line bg-white hover:border-primary/40'
            }">
                <div class="flex items-center justify-between gap-2 text-[12px] text-muted">
                    <span class="font-bold text-ink">${erikaEscapeHtml(o.kode_pesanan)}</span>
                    <span>${fmtDate(o.waktu_pesan)}</span>
                </div>
                <h3 class="font-bold text-[16px] text-ink mt-1.5 truncate">${erikaEscapeHtml(o.nama_pesanan)}</h3>
                <div class="flex items-center justify-between gap-2 mt-3">
                    <div class="flex flex-wrap gap-1.5">
                        ${statusBadgeHtml(o.status_pesanan)}
                        ${payBadgeHtml(o.status_pembayaran)}
                    </div>
                    <span class="font-extrabold text-[15px] text-primary shrink-0">${erikaFormatRp(o.total_bayar)}</span>
                </div>
                ${o.nomor_antrean ? `<p class="text-[11px] font-semibold text-muted mt-2">Antrean: <span class="text-ink">${erikaEscapeHtml(o.nomor_antrean)}</span> (${erikaEscapeHtml(o.status_antrean || '-')})</p>` : ''}
            </button>`;
    }).join('');
}

async function selectOrder(idPesanan) {
    selectedOrderId = idPesanan;
    renderOrdersList();

    const panel = document.getElementById('ps-detail-panel');
    panel.innerHTML = `<div class="py-16 text-center text-muted text-[14px]">Memuat detail pesanan...</div>`;

    try {
        const res = await fetch(apiBase(`api/pelanggan/pesanan/${idPesanan}`), {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            }
        });
        const json = await res.json().catch(() => null);
        if (!res.ok || !json || !json.status) {
            throw new Error(json?.message || `HTTP ${res.status}`);
        }
        renderOrderDetail(json.data);
    } catch (err) {
        panel.innerHTML = `<div class="p-5 rounded-xl bg-red-50 text-red-700 text-[13px]">Gagal memuat detail pesanan (${erikaEscapeHtml(err.message)}).</div>`;
    }
}

function renderOrderDetail(detail) {
    const p = detail.pesanan || detail;
    const antrean = detail.antrean || null;
    const layananItems = detail.detail_layanan || detail.items_layanan || [];
    const produkItems = detail.detail_produk || detail.items_produk || [];
    const files = detail.files || [];
    const pembayaran = detail.pembayaran || null;
    const revisiList = detail.revisi_komplain || detail.riwayat_revisi || [];

    const steps = ['MENUNGGU_KONFIRMASI', 'DIPROSES_DICETAK', 'SIAP_DIAMBIL', 'SELESAI'];
    const currentIdx = steps.indexOf(p.status_pesanan);
    const stepLabels = ['Dibuat', 'Diproses', 'Siap Diambil', 'Selesai'];

    const isPerluRevisi = p.status_pesanan === 'PERLU_REVISI';
    const detailLayananOptions = layananItems.map((dl) =>
        `<option value="${dl.id_detail_layanan}">${erikaEscapeHtml(dl.nama_layanan || 'Layanan')} — ${erikaEscapeHtml(dl.judul_pekerjaan || '')}</option>`
    ).join('');

    const panel = document.getElementById('ps-detail-panel');
    panel.innerHTML = `
        <div class="space-y-6">
            <!-- Header Detail + Nomor Antrean -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-line">
                <div class="space-y-2">
                    <div class="text-[22px] font-extrabold text-ink">${erikaEscapeHtml(p.kode_pesanan)}</div>
                    <p class="text-[14px] font-semibold text-muted">${erikaEscapeHtml(p.nama_pesanan)}</p>
                    <div class="flex flex-wrap gap-2">
                        ${statusBadgeHtml(p.status_pesanan)}
                        ${payBadgeHtml(p.status_pembayaran)}
                    </div>
                </div>
                <div class="bg-surface border border-line rounded-2xl px-5 py-3.5 text-center shrink-0">
                    <span class="block text-[10px] font-bold tracking-widest uppercase text-muted">NOMOR ANTREAN</span>
                    <span class="block text-[26px] font-extrabold text-primary leading-tight mt-0.5">${erikaEscapeHtml(antrean?.nomor_antrean || p.nomor_antrean || '—')}</span>
                    <span class="block text-[11px] font-semibold text-muted">${erikaEscapeHtml(antrean?.status_antrean || p.status_antrean || '-')}</span>
                </div>
            </div>

            <!-- Timeline Status -->
            ${p.status_pesanan !== 'DIBATALKAN' ? `
                <div class="grid grid-cols-4 gap-2">
                    ${stepLabels.map((lbl, idx) => {
                        const done = currentIdx >= idx || (isPerluRevisi && idx === 0);
                        return `
                            <div class="text-center">
                                <div class="h-1.5 rounded-full mb-2 ${done ? 'bg-primary' : 'bg-line'}"></div>
                                <span class="text-[11px] font-bold ${done ? 'text-primary' : 'text-muted'}">${lbl}</span>
                            </div>`;
                    }).join('')}
                </div>
            ` : ''}

            <!-- Alert & Form Revisi File jika status PERLU_REVISI -->
            ${isPerluRevisi ? `
                <div class="rounded-2xl border-2 border-magenta/40 bg-magenta/[0.04] p-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-magenta shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h4 class="font-extrabold text-[15px] text-magenta">Pesanan Memerlukan Revisi Berkas</h4>
                            <p class="text-[13px] text-ink mt-1">
                                <strong>Catatan kendala:</strong>
                                ${erikaEscapeHtml(p.catatan_revisi || p.catatan_admin || revisiList[0]?.tanggapan_admin || revisiList[0]?.pesan || 'Silakan unggah berkas perbaikan (.pdf/.doc/.docx) agar proses cetak dapat dilanjutkan.')}
                            </p>
                        </div>
                    </div>

                    <form onsubmit="submitRevisiFile(event, ${p.id_pesanan})" class="space-y-3 pt-2 border-t border-magenta/20">
                        ${layananItems.length > 0 ? `
                            <div>
                                <label class="block text-[12px] font-bold text-ink mb-1">Pilih Item Layanan</label>
                                <select id="rev-id-detail" required class="w-full h-[40px] px-3 rounded-[10px] border border-line bg-white text-[13px]">
                                    ${detailLayananOptions}
                                </select>
                            </div>
                        ` : ''}
                        <div>
                            <label class="block text-[12px] font-bold text-ink mb-1">Unggah Berkas Perbaikan (.pdf, .doc, .docx • maks 50 MB)</label>
                            <input id="rev-file" type="file" accept=".pdf,.doc,.docx" required class="block w-full text-[12px] text-muted file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-magenta file:text-white border border-line rounded-xl p-1.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-[12px] font-bold text-ink mb-1">Catatan Perbaikan (Opsional)</label>
                            <input id="rev-catatan" type="text" placeholder="Contoh: Margin halaman sudah diperbaiki" class="w-full h-[40px] px-3 rounded-[10px] border border-line bg-white text-[13px]">
                        </div>
                        <button id="rev-btn-submit" type="submit" class="h-[42px] px-5 rounded-[10px] bg-magenta hover:bg-[#c5126b] text-white font-semibold text-[13px] transition">
                            Kirim Berkas Revisi
                        </button>
                    </form>
                </div>
            ` : ''}

            <!-- Info Waktu -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-[12px]">
                <div class="p-3 rounded-xl bg-surface border border-line">
                    <span class="block text-muted">Waktu Pesan</span>
                    <span class="block font-bold text-ink mt-0.5">${fmtDate(p.waktu_pesan)}</span>
                </div>
                <div class="p-3 rounded-xl bg-surface border border-line">
                    <span class="block text-muted">Estimasi Selesai</span>
                    <span class="block font-bold text-ink mt-0.5">${fmtDate(p.estimasi_selesai)}</span>
                </div>
                <div class="p-3 rounded-xl bg-surface border border-line">
                    <span class="block text-muted">Waktu Siap</span>
                    <span class="block font-bold text-ink mt-0.5">${fmtDate(p.waktu_siap)}</span>
                </div>
                <div class="p-3 rounded-xl bg-surface border border-line">
                    <span class="block text-muted">Total Bayar</span>
                    <span class="block font-extrabold text-primary mt-0.5">${erikaFormatRp(p.total_bayar)}</span>
                </div>
            </div>

            <!-- Rincian Item & File -->
            <div class="space-y-3">
                <h4 class="font-bold text-[14px] text-ink">Rincian Pekerjaan &amp; File</h4>
                <div class="rounded-xl border border-line overflow-hidden divide-y divide-line">
                    ${layananItems.map((dl) => `
                        <div class="p-3.5 flex items-center justify-between gap-3 text-[13px]">
                            <div>
                                <p class="font-bold text-ink">${erikaEscapeHtml(dl.nama_layanan || 'Layanan')} ${dl.judul_pekerjaan ? `— ${erikaEscapeHtml(dl.judul_pekerjaan)}` : ''}</p>
                                <p class="text-[12px] text-muted">${dl.jumlah_halaman} hlm &times; ${dl.jumlah_salinan} salinan</p>
                            </div>
                            <span class="font-bold text-ink">${erikaFormatRp(dl.sub_total)}</span>
                        </div>
                    `).join('')}
                    ${produkItems.map((dp) => `
                        <div class="p-3.5 flex items-center justify-between gap-3 text-[13px]">
                            <div>
                                <p class="font-bold text-ink">${erikaEscapeHtml(dp.nama_produk || 'Produk ATK')}</p>
                                <p class="text-[12px] text-muted">${dp.jumlah} &times; ${erikaFormatRp(dp.harga_satuan)}</p>
                            </div>
                            <span class="font-bold text-ink">${erikaFormatRp(dp.sub_total)}</span>
                        </div>
                    `).join('')}
                </div>

                ${files.length > 0 ? `
                    <div class="space-y-2 pt-1">
                        <p class="text-[12px] font-bold text-muted uppercase tracking-wider">Berkas Terunggah</p>
                        ${files.map((f) => `
                            <div class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl bg-surface border border-line text-[13px]">
                                <span class="truncate font-medium text-ink">📄 ${erikaEscapeHtml(f.nama_file_asli)} (${f.jumlah_halaman_terdeteksi || 1} hlm)</span>
                                <button type="button" onclick="downloadProtectedFile(${f.id_file}, '${erikaEscapeHtml(f.nama_file_asli)}')" class="text-[12px] font-bold text-primary hover:underline shrink-0">Unduh</button>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>

            <!-- Riwayat Komplain / Revisi -->
            ${revisiList.length > 0 ? `
                <div class="space-y-2">
                    <h4 class="font-bold text-[14px] text-ink">Riwayat Revisi &amp; Komplain</h4>
                    <div class="space-y-2">
                        ${revisiList.map((rk) => `
                            <div class="p-3.5 rounded-xl bg-surface border border-line text-[13px]">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-ink">${erikaEscapeHtml(rk.jenis)}</span>
                                    <span class="text-[11px] font-semibold text-primary">${erikaEscapeHtml(rk.status)}</span>
                                </div>
                                <p class="text-muted mt-1">${erikaEscapeHtml(rk.pesan)}</p>
                                ${rk.tanggapan_admin ? `<p class="text-[12px] text-ink mt-1.5 pt-1.5 border-t border-line"><strong>Admin:</strong> ${erikaEscapeHtml(rk.tanggapan_admin)}</p>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            <!-- Footer Actions -->
            <div class="pt-3 border-t border-line flex flex-wrap items-center justify-between gap-3">
                <span class="text-[12px] text-muted">
                    Metode: <strong>${erikaEscapeHtml(pembayaran?.metode_pembayaran || '-')}</strong>
                </span>
                <button type="button" onclick="openKomplainModal(${p.id_pesanan})" class="h-[42px] px-5 rounded-[10px] border border-magenta/40 bg-magenta/5 hover:bg-magenta hover:text-white text-magenta font-semibold text-[13px] transition">
                    Ajukan Komplain / Revisi
                </button>
            </div>
        </div>`;
}

async function downloadProtectedFile(idFile, fileName) {
    try {
        const res = await fetch(apiBase(`api/pelanggan/file/${idFile}`), {
            headers: { 'Authorization': `Bearer ${ErikaAuth.getToken()}` }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName || 'dokumen';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    } catch (err) {
        ErikaUI.toast(`Gagal mengunduh berkas (${err.message}).`, 'error');
    }
}

async function submitRevisiFile(e, idPesanan) {
    e.preventDefault();
    const fileInput = document.getElementById('rev-file');
    const file = fileInput?.files?.[0];
    if (!file) return;

    const formData = new FormData();
    const idDetail = document.getElementById('rev-id-detail')?.value;
    if (idDetail) formData.append('id_detail_layanan', idDetail);
    formData.append('file_revisi', file, file.name);
    formData.append('catatan_revisi', document.getElementById('rev-catatan')?.value.trim() || '');

    const btn = document.getElementById('rev-btn-submit');
    btn.disabled = true;
    btn.textContent = 'Mengunggah...';

    try {
        const res = await fetch(apiBase(`api/pelanggan/pesanan/${idPesanan}/revisi-file`), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            },
            body: formData
        });
        const json = await res.json().catch(() => null);
        if (!res.ok || !json || !json.status) {
            ErikaUI.toast(json?.message || `Gagal mengunggah revisi (HTTP ${res.status}).`, 'error');
            return;
        }
        ErikaUI.toast('Berkas revisi berhasil diunggah! Status kembali ke Menunggu Konfirmasi.', 'success');
        loadOrders();
    } catch {
        ErikaUI.toast('Gagal menghubungi server saat unggah revisi.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Kirim Berkas Revisi';
    }
}

function openKomplainModal(idPesanan) {
    document.getElementById('komp-id-pesanan').value = idPesanan;
    document.getElementById('komp-pesan').value = '';
    document.getElementById('modal-komplain').classList.remove('hidden');
}

function closeKomplainModal() {
    document.getElementById('modal-komplain').classList.add('hidden');
}

async function submitKomplain(e) {
    e.preventDefault();
    const idPesanan = document.getElementById('komp-id-pesanan').value;
    const jenis = document.getElementById('komp-jenis').value;
    const pesan = document.getElementById('komp-pesan').value.trim();

    const btn = document.getElementById('komp-btn-submit');
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    try {
        const res = await fetch(apiBase(`api/pelanggan/pesanan/${idPesanan}/komplain`), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            },
            body: JSON.stringify({ jenis, pesan })
        });
        const json = await res.json().catch(() => null);
        if (!res.ok || !json || !json.status) {
            ErikaUI.toast(json?.message || `Gagal mengirim pengajuan (HTTP ${res.status}).`, 'error');
            return;
        }
        ErikaUI.toast('Pengajuan komplain/revisi berhasil dikirim!', 'success');
        closeKomplainModal();
        selectOrder(idPesanan);
    } catch {
        ErikaUI.toast('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Kirim Pengajuan';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.ps-filter-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            currentFilter = btn.getAttribute('data-status');
            document.querySelectorAll('.ps-filter-btn').forEach((b) => {
                const active = b.getAttribute('data-status') === currentFilter;
                b.className = `ps-filter-btn px-3.5 py-2 rounded-[10px] text-[12px] font-semibold shrink-0 ${
                    active ? 'bg-primary text-white' : 'bg-white border border-line text-muted hover:text-ink'
                }`;
            });
            renderOrdersList();
        });
    });

    loadOrders();
});
</script>
<?= $this->endSection() ?>
