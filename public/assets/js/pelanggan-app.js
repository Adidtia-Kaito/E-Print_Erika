/**
 * Percetakan Erika - Client-side Auth, Katalog, Cart (+File), & Dynamic Order Configurator
 */
(function (window) {
    'use strict';

    const BASE = (window.ERIKA_CONFIG && window.ERIKA_CONFIG.siteUrl) || '';
    const apiUrl = (path) => `${BASE}/${path.replace(/^\/+/, '')}`;

    // =========================================================================
    // Formatting & UI Helpers
    // =========================================================================
    const formatRp = (num) => {
        const val = Math.round(Number(num) || 0);
        return 'Rp ' + val.toLocaleString('id-ID');
    };

    const escapeHtml = (str) => {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };

    const ErikaUI = {
        toast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const colors = {
                success: 'bg-ink text-white border-l-4 border-cyan',
                error: 'bg-red-600 text-white border-l-4 border-yellow',
                info: 'bg-primary text-white border-l-4 border-yellow',
                warning: 'bg-amber-500 text-ink border-l-4 border-ink',
            };

            const el = document.createElement('div');
            el.className = `px-4 py-3 rounded-xl shadow-lg text-[13px] font-semibold flex items-center justify-between gap-3 transition-all ${colors[type] || colors.info}`;
            el.innerHTML = `<span>${escapeHtml(message)}</span>`;
            container.appendChild(el);

            setTimeout(() => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 250);
            }, 3500);
        },

        togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            if (btn) btn.textContent = isPass ? 'Sembunyikan' : 'Tampilkan';
        },

        toggleUserMenu() {
            const dd = document.getElementById('nav-user-dropdown');
            if (dd) dd.classList.toggle('hidden');
        },

        handleCekPesanan() {
            window.location.href = apiUrl('pesanan-saya');
        },

        openProfilPreview() {
            window.location.href = apiUrl('profil');
        }
    };

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', (e) => {
        const dd = document.getElementById('nav-user-dropdown');
        const btn = document.getElementById('nav-user-menu-btn');
        if (dd && btn && !dd.contains(e.target) && !btn.contains(e.target)) {
            dd.classList.add('hidden');
        }
    });

    // =========================================================================
    // Auth State (JWT di localStorage) + Modal 02 & 03
    // =========================================================================
    let pendingAuthCallback = null;

    const ErikaAuth = {
        TOKEN_KEY: 'erika_jwt_token',
        USER_KEY: 'erika_pelanggan',

        getToken() {
            return localStorage.getItem(this.TOKEN_KEY);
        },

        getUser() {
            try {
                const raw = localStorage.getItem(this.USER_KEY);
                return raw ? JSON.parse(raw) : null;
            } catch {
                return null;
            }
        },

        isLoggedIn() {
            return Boolean(this.getToken());
        },

        setSession(token, user) {
            localStorage.setItem(this.TOKEN_KEY, token);
            localStorage.setItem(this.USER_KEY, JSON.stringify(user || {}));
            this.syncNavbar();
        },

        logout() {
            localStorage.removeItem(this.TOKEN_KEY);
            localStorage.removeItem(this.USER_KEY);
            this.syncNavbar();
            const dd = document.getElementById('nav-user-dropdown');
            if (dd) dd.classList.add('hidden');
            ErikaUI.toast('Anda telah keluar dari akun.', 'info');
        },

        syncNavbar() {
            const guestEl = document.getElementById('nav-guest-actions');
            const userEl = document.getElementById('nav-user-actions');
            const loggedIn = this.isLoggedIn();
            const user = this.getUser();

            if (guestEl && userEl) {
                if (loggedIn) {
                    guestEl.classList.add('hidden');
                    userEl.classList.remove('hidden');
                    const name = user?.nama_lengkap || 'Pelanggan';
                    const email = user?.email || user?.no_wa || '';
                    const avatar = document.getElementById('nav-user-avatar');
                    const nameEl = document.getElementById('nav-user-name');
                    const ddName = document.getElementById('dropdown-user-name');
                    const ddEmail = document.getElementById('dropdown-user-email');

                    if (avatar) avatar.textContent = name.charAt(0).toUpperCase();
                    if (nameEl) nameEl.textContent = name;
                    if (ddName) ddName.textContent = name;
                    if (ddEmail) ddEmail.textContent = email;
                } else {
                    guestEl.classList.remove('hidden');
                    userEl.classList.add('hidden');
                }
            }

            // Update elemen kondisional di halaman jika ada
            document.querySelectorAll('[data-auth-only]').forEach((el) => {
                el.classList.toggle('hidden', !loggedIn);
            });
            document.querySelectorAll('[data-guest-only]').forEach((el) => {
                el.classList.toggle('hidden', loggedIn);
            });
        },

        openLoginModal(onSuccess) {
            if (typeof onSuccess === 'function') pendingAuthCallback = onSuccess;
            document.getElementById('modal-daftar')?.classList.add('hidden');
            document.getElementById('login-alert')?.classList.add('hidden');
            document.getElementById('modal-login')?.classList.remove('hidden');
        },

        openRegisterModal(onSuccess) {
            if (typeof onSuccess === 'function') pendingAuthCallback = onSuccess;
            document.getElementById('modal-login')?.classList.add('hidden');
            document.getElementById('register-alert')?.classList.add('hidden');
            document.getElementById('modal-daftar')?.classList.remove('hidden');
        },

        closeModals() {
            document.getElementById('modal-login')?.classList.add('hidden');
            document.getElementById('modal-daftar')?.classList.add('hidden');
            pendingAuthCallback = null;
        },

        showAlert(elId, msg, isError = true) {
            const box = document.getElementById(elId);
            if (!box) return;
            box.className = `mb-4 px-3.5 py-2.5 rounded-xl text-[13px] font-medium ${
                isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'
            }`;
            box.textContent = msg;
            box.classList.remove('hidden');
        },

        async submitLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-login');
            const identifier = document.getElementById('login-identifier').value.trim();
            const kata_sandi = document.getElementById('login-password').value;

            btn.disabled = true;
            btn.innerHTML = '<span>Memproses...</span>';

            try {
                const res = await fetch(apiUrl('api/pelanggan/login'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ identifier, email: identifier, kata_sandi })
                });
                const json = await res.json().catch(() => null);

                if (!res.ok || !json || !json.status) {
                    const msg = json?.message || `Login gagal (HTTP ${res.status}). Pastikan endpoint /api/pelanggan/login aktif.`;
                    this.showAlert('login-alert', msg, true);
                    return;
                }

                const token = json.data?.token;
                const profil = json.data?.profil || json.data?.pelanggan || json.data;
                if (!token) {
                    this.showAlert('login-alert', 'Token JWT tidak ditemukan pada respons server.', true);
                    return;
                }

                this.setSession(token, profil);
                ErikaUI.toast(`Selamat datang, ${profil?.nama_lengkap || 'Pelanggan'}!`, 'success');
                const cb = pendingAuthCallback;
                this.closeModals();
                if (cb) cb();
            } catch (err) {
                this.showAlert('login-alert', 'Tidak dapat terhubung ke server (/api/pelanggan/login).', true);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Masuk &amp; Lanjut Memesan</span>';
            }
        },

        async submitRegister(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-register');
            const payload = {
                nama_lengkap: document.getElementById('reg-nama').value.trim(),
                email: document.getElementById('reg-email').value.trim(),
                no_wa: document.getElementById('reg-wa').value.trim(),
                kata_sandi: document.getElementById('reg-password').value
            };

            btn.disabled = true;
            btn.innerHTML = '<span>Mendaftar...</span>';

            try {
                const res = await fetch(apiUrl('api/pelanggan/register'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json().catch(() => null);

                if (!res.ok || !json || !json.status) {
                    const msg = json?.message || `Pendaftaran gagal (HTTP ${res.status}).`;
                    this.showAlert('register-alert', msg, true);
                    return;
                }

                // Jika API register langsung mengembalikan token, simpan; jika tidak, auto-login
                if (json.data?.token) {
                    this.setSession(json.data.token, json.data.profil || payload);
                    ErikaUI.toast('Akun berhasil dibuat!', 'success');
                    const cb = pendingAuthCallback;
                    this.closeModals();
                    if (cb) cb();
                } else {
                    const loginRes = await fetch(apiUrl('api/pelanggan/login'), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ identifier: payload.email, kata_sandi: payload.kata_sandi })
                    });
                    const loginJson = await loginRes.json().catch(() => null);
                    if (loginRes.ok && loginJson?.status && loginJson.data?.token) {
                        this.setSession(loginJson.data.token, loginJson.data.profil || payload);
                        ErikaUI.toast('Akun berhasil dibuat dan Anda sudah masuk!', 'success');
                        const cb = pendingAuthCallback;
                        this.closeModals();
                        if (cb) cb();
                    } else {
                        this.showAlert('register-alert', 'Registrasi berhasil! Silakan masuk dengan akun baru Anda.', false);
                        setTimeout(() => this.openLoginModal(), 1200);
                    }
                }
            } catch (err) {
                this.showAlert('register-alert', 'Tidak dapat terhubung ke server (/api/pelanggan/register).', true);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Daftar &amp; Lanjut Memesan</span>';
            }
        }
    };

    // =========================================================================
    // Katalog API Client (GET /api/pelanggan/katalog)
    // =========================================================================
    const ErikaKatalog = {
        _promise: null,
        _data: null,

        async load(force = false) {
            if (this._data && !force) return this._data;
            if (this._promise && !force) return this._promise;

            this._promise = fetch(apiUrl('api/pelanggan/katalog'), {
                headers: { 'Accept': 'application/json' }
            })
                .then(async (res) => {
                    const json = await res.json().catch(() => null);
                    if (!res.ok || !json || !json.status) {
                        throw new Error(json?.message || `HTTP ${res.status}`);
                    }
                    this._data = json.data;
                    return this._data;
                })
                .catch((err) => {
                    this._promise = null;
                    throw err;
                });

            return this._promise;
        },

        findLayananById(data, idLayanan) {
            if (!data || !Array.isArray(data.kategori_layanan)) return null;
            for (const kat of data.kategori_layanan) {
                for (const lay of (kat.layanan || [])) {
                    if (Number(lay.id_layanan) === Number(idLayanan)) {
                        return { ...lay, kategori: kat };
                    }
                }
            }
            return null;
        },

        routeForLayanan(layanan) {
            const id = Number(layanan?.id_layanan || 0);
            const kode = String(layanan?.kode_layanan || '').toUpperCase();
            if (kode.startsWith('SPD')) return apiUrl(`pesan/spanduk?id_layanan=${id}`);
            if (kode.startsWith('UND')) return apiUrl(`pesan/undangan?id_layanan=${id}`);
            if (kode.startsWith('BNR')) return apiUrl(`pesan/banner?id_layanan=${id}`);
            if (kode.startsWith('PRT')) return apiUrl(`pesan/dokumen?id_layanan=${id}`);
            return apiUrl(`pesan/${id}`);
        }
    };

    // =========================================================================
    // Cart State + IndexedDB File Storage (Menyimpan item beserta objek File-nya)
    // =========================================================================
    window.ErikaCartFiles = window.ErikaCartFiles || new Map();

    const FileDB = {
        dbPromise: null,
        open() {
            if (this.dbPromise) return this.dbPromise;
            this.dbPromise = new Promise((resolve) => {
                if (!window.indexedDB) return resolve(null);
                const req = indexedDB.open('erika_cart_db', 1);
                req.onupgradeneeded = () => {
                    const db = req.result;
                    if (!db.objectStoreNames.contains('files')) {
                        db.createObjectStore('files');
                    }
                };
                req.onsuccess = () => resolve(req.result);
                req.onerror = () => resolve(null);
            });
            return this.dbPromise;
        },
        async put(key, files) {
            const db = await this.open();
            if (!db) return;
            return new Promise((resolve) => {
                const tx = db.transaction('files', 'readwrite');
                tx.objectStore('files').put(files, key);
                tx.oncomplete = () => resolve();
                tx.onerror = () => resolve();
            });
        },
        async get(key) {
            const db = await this.open();
            if (!db) return [];
            return new Promise((resolve) => {
                const tx = db.transaction('files', 'readonly');
                const req = tx.objectStore('files').get(key);
                req.onsuccess = () => resolve(req.result || []);
                req.onerror = () => resolve([]);
            });
        },
        async del(key) {
            const db = await this.open();
            if (!db) return;
            return new Promise((resolve) => {
                const tx = db.transaction('files', 'readwrite');
                tx.objectStore('files').delete(key);
                tx.oncomplete = () => resolve();
                tx.onerror = () => resolve();
            });
        },
        async clear() {
            const db = await this.open();
            if (!db) return;
            return new Promise((resolve) => {
                const tx = db.transaction('files', 'readwrite');
                tx.objectStore('files').clear();
                tx.oncomplete = () => resolve();
                tx.onerror = () => resolve();
            });
        }
    };

    const ErikaCart = {
        KEY: 'erika_cart_items',

        getItems() {
            try {
                const raw = localStorage.getItem(this.KEY);
                return raw ? JSON.parse(raw) : [];
            } catch {
                return [];
            }
        },

        saveItems(items) {
            localStorage.setItem(this.KEY, JSON.stringify(items));
            this.syncBadge();
            this.renderDrawer();
        },

        async restoreFiles() {
            const items = this.getItems();
            for (const item of items) {
                if (item.type === 'layanan' && !window.ErikaCartFiles.has(item.cart_id)) {
                    const files = await FileDB.get(item.cart_id);
                    if (files && files.length) {
                        window.ErikaCartFiles.set(item.cart_id, files);
                    }
                }
            }
        },

        async addLayananItem(itemPayload, fileObjects = []) {
            const cartId = 'item_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7);
            const filesMeta = fileObjects.map((f) => ({
                name: f.name,
                size: f.size,
                type: f.type
            }));

            // Simpan objek File asli di Memory Map dan IndexedDB
            if (fileObjects.length > 0) {
                window.ErikaCartFiles.set(cartId, fileObjects);
                await FileDB.put(cartId, fileObjects);
            }

            const items = this.getItems();
            items.push({
                cart_id: cartId,
                type: 'layanan',
                ...itemPayload,
                files_meta: filesMeta,
                has_file_objects: fileObjects.length > 0,
                added_at: new Date().toISOString()
            });
            this.saveItems(items);
            return cartId;
        },

        addProdukItem(produk, qty = 1) {
            if (produk.stok_habis) {
                ErikaUI.toast('Maaf, stok produk ini sedang habis.', 'warning');
                return;
            }
            const items = this.getItems();
            const existing = items.find((i) => i.type === 'produk' && Number(i.id_produk) === Number(produk.id_produk));
            if (existing) {
                existing.jumlah += qty;
                existing.sub_total = existing.jumlah * Number(produk.harga_jual);
            } else {
                items.push({
                    cart_id: 'atk_' + Date.now() + '_' + Math.random().toString(36).slice(2, 6),
                    type: 'produk',
                    id_produk: Number(produk.id_produk),
                    nama_produk: produk.nama_produk,
                    sku: produk.sku,
                    satuan: produk.satuan,
                    harga_jual: Number(produk.harga_jual),
                    jumlah: qty,
                    sub_total: qty * Number(produk.harga_jual),
                    added_at: new Date().toISOString()
                });
            }
            this.saveItems(items);
            ErikaUI.toast(`${produk.nama_produk} ditambahkan ke keranjang!`, 'success');
        },

        async removeItem(cartId) {
            const items = this.getItems().filter((i) => i.cart_id !== cartId);
            window.ErikaCartFiles.delete(cartId);
            await FileDB.del(cartId);
            this.saveItems(items);
        },

        async clearAll() {
            window.ErikaCartFiles.clear();
            await FileDB.clear();
            this.saveItems([]);
        },

        getFilesForItem(cartId) {
            return window.ErikaCartFiles.get(cartId) || [];
        },

        syncBadge() {
            const badge = document.getElementById('nav-cart-badge');
            if (!badge) return;
            const count = this.getItems().length;
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        },

        openDrawer() {
            this.renderDrawer();
            document.getElementById('cart-drawer-backdrop')?.classList.remove('hidden');
        },

        closeDrawer() {
            document.getElementById('cart-drawer-backdrop')?.classList.add('hidden');
        },

        renderDrawer() {
            const container = document.getElementById('cart-drawer-items');
            const totalEl = document.getElementById('cart-drawer-total');
            if (!container || !totalEl) return;

            const items = this.getItems();
            if (items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-14 px-4">
                        <div class="w-14 h-14 rounded-2xl bg-surface text-muted mx-auto flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <p class="font-bold text-ink text-[15px]">Keranjang masih kosong</p>
                        <p class="text-[13px] text-muted mt-1">Pilih layanan cetak atau ATK untuk memulai pesanan.</p>
                    </div>`;
                totalEl.textContent = 'Rp 0';
                return;
            }

            let total = 0;
            container.innerHTML = items.map((item) => {
                total += Number(item.sub_total || 0);
                if (item.type === 'produk') {
                    return `
                        <div class="p-4 rounded-xl border border-line bg-white flex items-start justify-between gap-3">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded bg-yellow/20 text-ink text-[10px] font-bold uppercase mb-1">ATK</span>
                                <h4 class="font-bold text-[14px] text-ink">${escapeHtml(item.nama_produk)}</h4>
                                <p class="text-[12px] text-muted mt-0.5">${item.jumlah} ${escapeHtml(item.satuan)} × ${formatRp(item.harga_jual)}</p>
                                <p class="text-[14px] font-bold text-primary mt-1.5">${formatRp(item.sub_total)}</p>
                            </div>
                            <button type="button" onclick="ErikaCart.removeItem('${item.cart_id}')" class="p-1.5 text-muted hover:text-red-600 rounded-lg" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>`;
                }
                const filesText = (item.files_meta || []).map((f) => `${escapeHtml(f.name)} (${(f.size / 1024 / 1024).toFixed(2)} MB)`).join(', ');
                return `
                    <div class="p-4 rounded-xl border border-line bg-white flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="inline-block px-2 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold uppercase mb-1">${escapeHtml(item.kode_layanan || 'LAYANAN')}</span>
                            <h4 class="font-bold text-[14px] text-ink truncate">${escapeHtml(item.nama_layanan)}</h4>
                            ${item.judul_pekerjaan ? `<p class="text-[12px] text-ink font-medium mt-0.5">${escapeHtml(item.judul_pekerjaan)}</p>` : ''}
                            <p class="text-[12px] text-muted mt-0.5">
                                ${item.luas_m2 ? `${item.panjang_cm}×${item.lebar_cm} cm (${Number(item.luas_m2).toFixed(2)} m²) • ` : `${item.jumlah_halaman} hlm × `}
                                ${item.jumlah_salinan} salinan
                            </p>
                            ${filesText ? `<p class="text-[11px] text-cyan font-semibold mt-1 truncate">📎 ${filesText}</p>` : ''}
                            <p class="text-[14px] font-bold text-primary mt-1.5">${formatRp(item.sub_total)}</p>
                        </div>
                        <button type="button" onclick="ErikaCart.removeItem('${item.cart_id}')" class="p-1.5 text-muted hover:text-red-600 rounded-lg shrink-0" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>`;
            }).join('');

            totalEl.textContent = formatRp(total);
        },

        proceedToCheckout() {
            if (this.getItems().length === 0) {
                ErikaUI.toast('Keranjang masih kosong.', 'warning');
                return;
            }
            window.location.href = apiUrl('keranjang');
        }
    };

    // =========================================================================
    // Dynamic Order Configurator Engine (Halaman 05 - 08)
    // =========================================================================
    const ErikaOrderConfigurator = {
        state: {
            layanan: null,
            kategoriLayananList: [],
            opsiIds: new Set(),
            selectedFiles: [],
            selectedTemplate: null,
            estimasi: null,
            debounceTimer: null
        },

        async init(config) {
            const root = document.getElementById('order-configurator-root');
            if (!root) return;

            try {
                const katalog = await ErikaKatalog.load();
                const allLayanan = [];
                (katalog.kategori_layanan || []).forEach((kat) => {
                    (kat.layanan || []).forEach((l) => allLayanan.push({ ...l, kategori: kat }));
                });

                // Pilih layanan target berdasarkan URL param ?id_layanan=..., prop initialIdLayanan, atau kode prefix
                const urlParams = new URLSearchParams(window.location.search);
                const queryId = Number(urlParams.get('id_layanan') || config.initialIdLayanan || 0);
                let target = queryId ? allLayanan.find((l) => Number(l.id_layanan) === queryId) : null;

                if (!target && config.defaultKodePrefix) {
                    target = allLayanan.find((l) => String(l.kode_layanan).toUpperCase().startsWith(config.defaultKodePrefix));
                }
                if (!target) target = allLayanan[0];

                if (!target) {
                    this.renderError('Belum ada layanan aktif yang tersedia di katalog.');
                    return;
                }

                // Tampilkan switcher jika ada beberapa layanan dalam kategori yang sama (misal Print Dokumen: PRT-DOK, PRT-JLD, PRT-UPL)
                const siblings = allLayanan.filter((l) => Number(l.id_kategori_layanan) === Number(target.id_kategori_layanan));
                this.state.kategoriLayananList = siblings;
                this.selectLayanan(target, config);
            } catch (err) {
                this.renderError(`Gagal memuat data layanan dari GET /api/pelanggan/katalog (${err.message}).`);
            }
        },

        renderError(msg) {
            const loading = document.getElementById('cfg-loading-state');
            const content = document.getElementById('cfg-main-content');
            if (loading) {
                loading.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center">
                        <p class="font-bold text-red-700 text-[15px]">Gagal Memuat Konfigurator Layanan</p>
                        <p class="text-[13px] text-red-600 mt-1">${escapeHtml(msg)}</p>
                    </div>`;
            }
            if (content) content.classList.add('hidden');
        },

        selectLayanan(layanan, config = {}) {
            this.state.layanan = layanan;
            this.state.opsiIds = new Set();

            // Auto-pilih opsi pertama pada grup yang wajib_dipilih == 1
            const opsiList = layanan.opsi_layanan || [];
            const groups = {};
            opsiList.forEach((o) => {
                const g = o.grup_opsi || 'Lainnya';
                if (!groups[g]) groups[g] = [];
                groups[g].push(o);
            });
            Object.values(groups).forEach((opts) => {
                const wajib = opts.some((o) => Number(o.wajib_dipilih) === 1);
                if (wajib && opts.length > 0) {
                    this.state.opsiIds.add(Number(opts[0].id_opsi));
                }
            });

            // Header & Breadcrumb Update
            const bcEl = document.getElementById('cfg-breadcrumb');
            const titleEl = document.getElementById('cfg-page-title');
            const descEl = document.getElementById('cfg-page-desc');
            const etaEl = document.getElementById('cfg-estimasi-waktu');
            if (bcEl) bcEl.textContent = `Layanan / ${layanan.nama_layanan}`;
            if (titleEl) titleEl.textContent = layanan.nama_layanan;
            if (descEl) descEl.textContent = layanan.deskripsi || 'Atur spesifikasi pesanan sesuai kebutuhan Anda.';
            if (etaEl) {
                const mins = Number(layanan.estimasi_durasi_menit || 30);
                const text = mins >= 1440 ? `${Math.round(mins / 1440)} hari kerja` : (mins >= 60 ? `± ${Math.round(mins / 60)} jam` : `± ${mins} menit`);
                etaEl.textContent = `Estimasi selesai ${text} setelah pembayaran terkonfirmasi`;
            }

            // Switcher layanan dalam kategori
            const switcherWrap = document.getElementById('cfg-layanan-switcher');
            if (switcherWrap) {
                if (this.state.kategoriLayananList.length > 1) {
                    switcherWrap.classList.remove('hidden');
                    switcherWrap.innerHTML = this.state.kategoriLayananList.map((item) => {
                        const active = Number(item.id_layanan) === Number(layanan.id_layanan);
                        return `
                            <button type="button" data-switch-layanan="${item.id_layanan}" class="px-4 py-2 rounded-[10px] text-[13px] font-semibold border transition ${
                                active
                                    ? 'bg-primary text-white border-primary shadow-sm'
                                    : 'bg-white text-ink border-line hover:border-primary/50'
                            }">
                                ${escapeHtml(item.nama_layanan)} • ${formatRp(item.harga_dasar)}/${escapeHtml(item.satuan_hitung)}
                            </button>`;
                    }).join('');
                    switcherWrap.querySelectorAll('[data-switch-layanan]').forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const id = Number(btn.getAttribute('data-switch-layanan'));
                            const next = this.state.kategoriLayananList.find((x) => Number(x.id_layanan) === id);
                            if (next) this.selectLayanan(next, config);
                        });
                    });
                } else {
                    switcherWrap.classList.add('hidden');
                }
            }

            // Tampilkan/Sembunyikan Upload Dokumen jika butuh_file == 1
            const fileSection = document.getElementById('cfg-section-file');
            if (fileSection) {
                const needFile = Number(layanan.butuh_file) === 1;
                fileSection.classList.toggle('hidden', !needFile);
                if (needFile) this.bindDropzone();
            }

            // Tampilkan/Sembunyikan Dimensi jika butuh_dimensi == 1
            const dimSection = document.getElementById('cfg-section-dimensi');
            const pagesFieldWrap = document.getElementById('cfg-field-halaman-wrap');
            const needDim = Number(layanan.butuh_dimensi) === 1;
            if (dimSection) {
                dimSection.classList.toggle('hidden', !needDim);
                if (needDim) {
                    const pInput = document.getElementById('cfg-panjang-cm');
                    const lInput = document.getElementById('cfg-lebar-cm');
                    if (pInput && !pInput.value) pInput.value = config.defaultPanjang || 300;
                    if (lInput && !lInput.value) lInput.value = config.defaultLebar || 100;
                    this.updateLuasDisplay();
                }
            }
            if (pagesFieldWrap) {
                pagesFieldWrap.classList.toggle('hidden', needDim);
            }

            // Render Opsi Layanan Dinamis
            this.renderOpsiGroups(groups);

            // Bind input listeners
            this.bindInputs();

            // Tampilkan form
            document.getElementById('cfg-loading-state')?.classList.add('hidden');
            document.getElementById('cfg-main-content')?.classList.remove('hidden');

            // Hitung estimasi awal
            this.triggerEstimasi();
        },

        renderOpsiGroups(groups) {
            const container = document.getElementById('cfg-opsi-container');
            const section = document.getElementById('cfg-section-opsi');
            if (!container || !section) return;

            const entries = Object.entries(groups);
            if (entries.length === 0) {
                section.classList.add('hidden');
                container.innerHTML = '';
                return;
            }

            section.classList.remove('hidden');
            container.innerHTML = entries.map(([grupNama, opts]) => {
                const isSingle = opts.some((o) => Number(o.wajib_dipilih) === 1) || opts.length > 1 && ['Rangka', 'Bahan', 'Jilid'].includes(grupNama);
                return `
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-bold text-ink">${escapeHtml(grupNama)}</span>
                            ${opts.some((o) => Number(o.wajib_dipilih) === 1)
                                ? '<span class="text-[11px] font-semibold text-magenta bg-magenta/10 px-2 py-0.5 rounded-full">Wajib dipilih</span>'
                                : '<span class="text-[11px] text-muted">Opsional</span>'}
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            ${opts.map((o) => {
                                const selected = this.state.opsiIds.has(Number(o.id_opsi));
                                const unitSuffix = o.tipe_harga === 'PER_UNIT'
                                    ? `/${escapeHtml(this.state.layanan?.satuan_hitung || 'unit')}`
                                    : '';
                                return `
                                    <button type="button"
                                        data-opsi-id="${o.id_opsi}"
                                        data-opsi-group="${escapeHtml(grupNama)}"
                                        data-opsi-single="${isSingle ? '1' : '0'}"
                                        class="text-left p-4 rounded-xl border transition flex items-start gap-3 ${
                                            selected
                                                ? 'border-primary bg-primary/[0.04] ring-1 ring-primary'
                                                : 'border-line bg-white hover:border-primary/40'
                                        }">
                                        <span class="mt-0.5 w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 ${
                                            selected ? 'border-primary' : 'border-slate-300'
                                        }">
                                            ${selected ? '<span class="w-2.5 h-2.5 rounded-full bg-primary"></span>' : ''}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[14px] text-ink leading-snug">${escapeHtml(o.nama_opsi)}</p>
                                            <p class="text-[12px] font-semibold text-primary mt-1">+${formatRp(o.harga_tambahan)} ${unitSuffix}</p>
                                            <p class="text-[11px] text-muted mt-0.5">${o.tipe_harga === 'PER_UNIT' ? 'Dihitung per kuantitas' : 'Biaya tetap'}</p>
                                        </div>
                                    </button>`;
                            }).join('')}
                        </div>
                    </div>`;
            }).join('');

            container.querySelectorAll('[data-opsi-id]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = Number(btn.getAttribute('data-opsi-id'));
                    const group = btn.getAttribute('data-opsi-group');
                    const isSingle = btn.getAttribute('data-opsi-single') === '1';

                    if (isSingle) {
                        const groupOpsiIds = (this.state.layanan.opsi_layanan || [])
                            .filter((x) => (x.grup_opsi || 'Lainnya') === group)
                            .map((x) => Number(x.id_opsi));
                        const wasSelected = this.state.opsiIds.has(id);
                        const isRequired = (this.state.layanan.opsi_layanan || [])
                            .some((x) => (x.grup_opsi || 'Lainnya') === group && Number(x.wajib_dipilih) === 1);

                        groupOpsiIds.forEach((gid) => this.state.opsiIds.delete(gid));
                        if (!wasSelected || isRequired) {
                            this.state.opsiIds.add(id);
                        }
                    } else {
                        if (this.state.opsiIds.has(id)) {
                            this.state.opsiIds.delete(id);
                        } else {
                            this.state.opsiIds.add(id);
                        }
                    }
                    // Re-render opsi & hitung ulang estimasi
                    const groups = {};
                    (this.state.layanan.opsi_layanan || []).forEach((o) => {
                        const g = o.grup_opsi || 'Lainnya';
                        if (!groups[g]) groups[g] = [];
                        groups[g].push(o);
                    });
                    this.renderOpsiGroups(groups);
                    this.triggerEstimasi();
                });
            });
        },

        bindDropzone() {
            const zone = document.getElementById('cfg-dropzone');
            const input = document.getElementById('cfg-file-input');
            if (!zone || !input || zone.dataset.bound) return;
            zone.dataset.bound = '1';

            zone.addEventListener('click', () => input.click());
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('border-primary', 'bg-primary/5');
            });
            zone.addEventListener('dragleave', () => {
                zone.classList.remove('border-primary', 'bg-primary/5');
            });
            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('border-primary', 'bg-primary/5');
                if (e.dataTransfer?.files?.length) {
                    this.handleFiles(e.dataTransfer.files);
                }
            });
            input.addEventListener('change', () => {
                if (input.files?.length) {
                    this.handleFiles(input.files);
                    input.value = '';
                }
            });
        },

        handleFiles(fileList) {
            const allowedExt = ['pdf', 'doc', 'docx'];
            const maxBytes = 50 * 1024 * 1024; // 50 MB

            for (const file of Array.from(fileList)) {
                const ext = (file.name.split('.').pop() || '').toLowerCase();
                if (!allowedExt.includes(ext)) {
                    ErikaUI.toast(`Format "${file.name}" ditolak. Hanya .pdf, .doc, .docx yang diizinkan.`, 'error');
                    continue;
                }
                if (file.size > maxBytes) {
                    ErikaUI.toast(`Ukuran "${file.name}" melebihi batas maksimal 50 MB.`, 'error');
                    continue;
                }
                this.state.selectedFiles.push(file);

                // Deteksi jumlah halaman ringan di client untuk PDF (perkiraan cepat sebelum upload server)
                if (ext === 'pdf') {
                    this.estimatePdfPagesInBrowser(file);
                }
            }
            this.renderFileList();
        },

        async estimatePdfPagesInBrowser(file) {
            try {
                const buf = await file.slice(0, 2 * 1024 * 1024).text();
                const matches = buf.match(/\/Type\s*\/Page[^s]/g);
                if (matches && matches.length > 0) {
                    const hlmInput = document.getElementById('cfg-jumlah-halaman');
                    if (hlmInput) {
                        hlmInput.value = matches.length;
                        this.triggerEstimasi();
                        ErikaUI.toast(`Terdeteksi ± ${matches.length} halaman pada ${file.name}.`, 'info');
                    }
                }
            } catch {
                // Abaikan jika gagal parse ringan
            }
        },

        renderFileList() {
            const listEl = document.getElementById('cfg-file-list');
            if (!listEl) return;

            if (this.state.selectedFiles.length === 0) {
                listEl.innerHTML = '';
                return;
            }

            listEl.innerHTML = this.state.selectedFiles.map((file, idx) => {
                const mb = (file.size / 1024 / 1024).toFixed(2);
                const ext = (file.name.split('.').pop() || '').toUpperCase();
                return `
                    <div class="flex items-center justify-between gap-3 p-3.5 rounded-xl border border-line bg-surface">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-[11px] shrink-0">
                                ${escapeHtml(ext)}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-bold text-ink truncate">${escapeHtml(file.name)}</p>
                                <p class="text-[12px] text-muted">${mb} MB • Siap diunggah saat checkout</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">Terlampir</span>
                            <button type="button" onclick="ErikaOrderConfigurator.removeFile(${idx})" class="p-1.5 text-muted hover:text-red-600 rounded-lg" title="Hapus file">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>`;
            }).join('');
        },

        removeFile(idx) {
            this.state.selectedFiles.splice(idx, 1);
            this.renderFileList();
        },

        bindInputs() {
            const ids = ['cfg-jumlah-halaman', 'cfg-jumlah-salinan', 'cfg-panjang-cm', 'cfg-lebar-cm'];
            ids.forEach((id) => {
                const el = document.getElementById(id);
                if (!el || el.dataset.bound) return;
                el.dataset.bound = '1';
                el.addEventListener('input', () => {
                    if (id === 'cfg-panjang-cm' || id === 'cfg-lebar-cm') {
                        this.updateLuasDisplay();
                    }
                    this.triggerEstimasi();
                });
            });

            // Quick dimension preset chips
            document.querySelectorAll('[data-preset-dim]').forEach((chip) => {
                if (chip.dataset.bound) return;
                chip.dataset.bound = '1';
                chip.addEventListener('click', () => {
                    const [p, l] = chip.getAttribute('data-preset-dim').split('x').map(Number);
                    const pEl = document.getElementById('cfg-panjang-cm');
                    const lEl = document.getElementById('cfg-lebar-cm');
                    if (pEl) pEl.value = p;
                    if (lEl) lEl.value = l;
                    this.updateLuasDisplay();
                    this.triggerEstimasi();
                });
            });
        },

        updateLuasDisplay() {
            const p = Number(document.getElementById('cfg-panjang-cm')?.value || 0);
            const l = Number(document.getElementById('cfg-lebar-cm')?.value || 0);
            const luas = (p / 100) * (l / 100);
            const out = document.getElementById('cfg-luas-otomatis');
            if (out) out.value = `${luas.toFixed(2).replace('.', ',')} m²`;
        },

        getFormPayload() {
            const lay = this.state.layanan;
            const butuhDimensi = Number(lay?.butuh_dimensi) === 1;
            const jumlah_halaman = butuhDimensi ? 1 : Math.max(1, Number(document.getElementById('cfg-jumlah-halaman')?.value || 1));
            const jumlah_salinan = Math.max(1, Number(document.getElementById('cfg-jumlah-salinan')?.value || 1));
            const panjang_cm = butuhDimensi ? Math.max(1, Number(document.getElementById('cfg-panjang-cm')?.value || 100)) : null;
            const lebar_cm = butuhDimensi ? Math.max(1, Number(document.getElementById('cfg-lebar-cm')?.value || 100)) : null;

            return {
                id_layanan: Number(lay.id_layanan),
                jumlah_halaman,
                jumlah_salinan,
                panjang_cm,
                lebar_cm,
                opsi_ids: Array.from(this.state.opsiIds)
            };
        },

        triggerEstimasi() {
            clearTimeout(this.state.debounceTimer);
            this.state.debounceTimer = setTimeout(() => this.fetchEstimasi(), 150);
        },

        async fetchEstimasi() {
            const lay = this.state.layanan;
            if (!lay) return;

            const payload = this.getFormPayload();
            const statusEl = document.getElementById('sum-api-status');
            if (statusEl) statusEl.textContent = 'Menghitung...';

            try {
                const res = await fetch(apiUrl('api/pelanggan/hitung-estimasi'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json().catch(() => null);
                if (!res.ok || !json || !json.status) {
                    throw new Error(json?.message || `HTTP ${res.status}`);
                }
                this.state.estimasi = json.data;
                this.renderSummary(json.data, payload, true);
            } catch (err) {
                // Fallback perhitungan lokal jika endpoint belum aktif, tetap ditandai
                const fallback = this.calculateLocalFallback(lay, payload);
                this.state.estimasi = fallback;
                this.renderSummary(fallback, payload, false);
            }
        },

        calculateLocalFallback(lay, payload) {
            const butuhDim = Number(lay.butuh_dimensi) === 1;
            const luas_m2 = butuhDim ? (payload.panjang_cm / 100) * (payload.lebar_cm / 100) : null;
            const kuantitas_hitung = butuhDim ? (luas_m2 * payload.jumlah_salinan) : (payload.jumlah_halaman * payload.jumlah_salinan);
            const biaya_dasar = Number(lay.harga_dasar) * kuantitas_hitung;

            const detail_opsi = [];
            let biaya_opsi_total = 0;
            (lay.opsi_layanan || []).forEach((o) => {
                if (payload.opsi_ids.includes(Number(o.id_opsi))) {
                    const biaya = o.tipe_harga === 'PER_UNIT'
                        ? Number(o.harga_tambahan) * kuantitas_hitung
                        : Number(o.harga_tambahan);
                    biaya_opsi_total += biaya;
                    detail_opsi.push({ nama_opsi: o.nama_opsi, tipe_harga: o.tipe_harga, biaya });
                }
            });

            return {
                layanan: lay.nama_layanan,
                kuantitas_hitung,
                luas_m2,
                biaya_dasar,
                detail_opsi,
                biaya_opsi_total,
                sub_total: biaya_dasar + biaya_opsi_total
            };
        },

        renderSummary(data, payload, fromApi) {
            const rowsEl = document.getElementById('sum-breakdown-rows');
            const totalEl = document.getElementById('sum-total-harga');
            const subEl = document.getElementById('sum-subtotal-harga');
            const statusEl = document.getElementById('sum-api-status');
            if (!rowsEl || !totalEl || !subEl) return;

            const lay = this.state.layanan;
            const labelUtama = Number(lay.butuh_dimensi) === 1
                ? `${lay.nama_layanan} (${payload.panjang_cm}×${payload.lebar_cm} cm, ${Number(data.luas_m2).toFixed(2)} m² × ${payload.jumlah_salinan})`
                : `${lay.nama_layanan} (${payload.jumlah_halaman} hlm × ${payload.jumlah_salinan} salinan)`;

            let html = `
                <div class="flex items-start justify-between gap-3 text-[13px]">
                    <span class="text-muted">${escapeHtml(labelUtama)}</span>
                    <span class="font-semibold text-ink shrink-0">${formatRp(data.biaya_dasar)}</span>
                </div>`;

            (data.detail_opsi || []).forEach((op) => {
                html += `
                    <div class="flex items-start justify-between gap-3 text-[13px]">
                        <span class="text-muted">${escapeHtml(op.nama_opsi)}</span>
                        <span class="font-semibold text-ink shrink-0">${formatRp(op.biaya)}</span>
                    </div>`;
            });

            rowsEl.innerHTML = html;
            subEl.textContent = formatRp(data.sub_total);
            totalEl.textContent = formatRp(data.sub_total);

            if (statusEl) {
                statusEl.textContent = fromApi ? 'Real-time API' : 'Estimasi Lokal';
                statusEl.className = `text-[11px] font-semibold px-2 py-0.5 rounded-full ${
                    fromApi ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800'
                }`;
            }
        },

        async saveToCart(openDrawerAfter = false) {
            const lay = this.state.layanan;
            if (!lay) return;

            if (Number(lay.butuh_file) === 1 && this.state.selectedFiles.length === 0) {
                ErikaUI.toast('Silakan unggah minimal 1 file dokumen (.pdf, .doc, .docx) terlebih dahulu.', 'warning');
                return;
            }

            const payload = this.getFormPayload();
            const est = this.state.estimasi || this.calculateLocalFallback(lay, payload);
            const judul = document.getElementById('cfg-judul-pekerjaan')?.value.trim() || lay.nama_layanan;
            const catatan = document.getElementById('cfg-catatan')?.value.trim() || '';
            const spek = this.collectExtraSpecs();

            await ErikaCart.addLayananItem({
                id_layanan: Number(lay.id_layanan),
                kode_layanan: lay.kode_layanan,
                nama_layanan: lay.nama_layanan,
                judul_pekerjaan: judul,
                jumlah_halaman: payload.jumlah_halaman,
                jumlah_salinan: payload.jumlah_salinan,
                panjang_cm: payload.panjang_cm,
                lebar_cm: payload.lebar_cm,
                luas_m2: est.luas_m2,
                opsi_ids: payload.opsi_ids,
                detail_opsi: est.detail_opsi,
                spesifikasi_tambahan: spek,
                catatan_pekerjaan: catatan,
                sub_total: est.sub_total
            }, [...this.state.selectedFiles]);

            ErikaUI.toast(`${lay.nama_layanan} + ${this.state.selectedFiles.length} file disimpan ke keranjang!`, 'success');
            if (openDrawerAfter) {
                if (!ErikaAuth.isLoggedIn()) {
                    ErikaAuth.openLoginModal(() => ErikaCart.openDrawer());
                } else {
                    ErikaCart.openDrawer();
                }
            }
        },

        collectExtraSpecs() {
            const specs = {};
            document.querySelectorAll('[data-spec-field]').forEach((el) => {
                const key = el.getAttribute('data-spec-field');
                if (el.value) specs[key] = el.value;
            });
            if (this.state.selectedTemplate) {
                specs.template = this.state.selectedTemplate;
            }
            return Object.keys(specs).length ? JSON.stringify(specs) : null;
        },

        selectTemplate(name, btn) {
            this.state.selectedTemplate = name;
            document.querySelectorAll('[data-template-card]').forEach((el) => {
                el.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
                el.classList.add('border-line');
            });
            if (btn) {
                btn.classList.remove('border-line');
                btn.classList.add('border-primary', 'ring-2', 'ring-primary/20');
            }
        }
    };

    // Expose globals
    window.ErikaUI = ErikaUI;
    window.ErikaAuth = ErikaAuth;
    window.ErikaKatalog = ErikaKatalog;
    window.ErikaCart = ErikaCart;
    window.ErikaOrderConfigurator = ErikaOrderConfigurator;
    window.erikaFormatRp = formatRp;
    window.erikaEscapeHtml = escapeHtml;

    // Boot on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        ErikaAuth.syncNavbar();
        ErikaCart.syncBadge();
        ErikaCart.restoreFiles();
    });
})(window);
