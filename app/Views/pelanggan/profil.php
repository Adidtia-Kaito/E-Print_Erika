<?= $this->extend('layouts/pelanggan') ?>

<?= $this->section('content') ?>

<!-- 11 · Profil Saya (Figma 8:890) -->
<section class="bg-surface border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-8 sm:py-10">
        <p class="text-[13px] font-semibold text-muted">Akun / Profil Saya</p>
        <h1 class="text-[30px] sm:text-[38px] font-extrabold text-ink tracking-tight mt-1.5">Profil Saya</h1>
        <p class="text-[15px] text-muted mt-1">Kelola data akun pelanggan, foto profil, dan keamanan kata sandi Anda.</p>
    </div>
</section>

<section class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-10">
    <!-- Guest Gate -->
    <div id="prof-guest-gate" class="hidden bg-white rounded-[20px] border border-line p-10 text-center max-w-lg mx-auto">
        <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary mx-auto flex items-center justify-center mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <h2 class="text-[20px] font-extrabold text-ink">Masuk untuk Mengelola Profil</h2>
        <p class="text-[14px] text-muted mt-1.5 mb-6">Silakan masuk terlebih dahulu untuk melihat dan mengubah profil akun Anda.</p>
        <button type="button" onclick="ErikaAuth.openLoginModal(() => loadProfil())" class="bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] px-6 py-3 rounded-[12px] transition">
            Masuk Sekarang
        </button>
    </div>

    <!-- Main Profil Grid -->
    <div id="prof-main-layout" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
        <!-- Menu Akun Kiri (3 cols, Figma 8:925) -->
        <aside class="lg:col-span-3 bg-white rounded-[20px] border border-line p-3 space-y-1">
            <a href="<?= site_url('profil') ?>" class="block px-4 py-2.5 rounded-xl bg-primary/10 text-primary font-bold text-[14px]">Profil Saya</a>
            <a href="<?= site_url('pesanan-saya') ?>" class="block px-4 py-2.5 rounded-xl text-ink hover:bg-surface font-medium text-[14px] transition">Pesanan Saya</a>
            <a href="#seksi-sandi" class="block px-4 py-2.5 rounded-xl text-ink hover:bg-surface font-medium text-[14px] transition">Ubah Kata Sandi</a>
            <button type="button" onclick="logoutAndRedirect()" class="w-full text-left px-4 py-2.5 rounded-xl text-magenta hover:bg-pink-50 font-semibold text-[14px] transition">Keluar</button>
        </aside>

        <!-- Form Profil Kanan (9 cols, Figma 8:934) -->
        <div class="lg:col-span-9 bg-white rounded-[20px] border border-line p-6 sm:p-8">
            <form id="form-profil-pelanggan" onsubmit="submitProfil(event)" class="space-y-6">
                <!-- Foto Profil -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-5 pb-6 border-b border-line">
                    <div id="prof-avatar-box" class="w-[88px] h-[88px] rounded-2xl bg-primary text-white text-[28px] font-extrabold flex items-center justify-center overflow-hidden shrink-0">
                        <span id="prof-avatar-initials">P</span>
                        <img id="prof-avatar-img" src="" alt="Foto Profil" class="hidden w-full h-full object-cover">
                    </div>
                    <div class="space-y-2.5">
                        <h2 class="font-bold text-[16px] text-ink">Foto Profil</h2>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <label class="cursor-pointer inline-flex items-center gap-2 h-[38px] px-4 rounded-[10px] bg-primary hover:bg-[#212675] text-white text-[13px] font-semibold transition">
                                <span>Unggah Foto</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <input id="prof-foto-input" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" onchange="previewFotoProfil(this)">
                            </label>
                            <span class="text-[12px] text-muted">JPG, PNG, WebP (maks. 2 MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Alert -->
                <div id="prof-alert" class="hidden rounded-xl px-4 py-3 text-[13px] font-medium"></div>

                <!-- Biodata -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="prof-nama" class="block text-[13px] font-semibold text-ink mb-1.5">Nama Lengkap</label>
                        <input id="prof-nama" name="nama_lengkap" type="text" required class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                    <div>
                        <label for="prof-email" class="block text-[13px] font-semibold text-ink mb-1.5">Email</label>
                        <input id="prof-email" name="email" type="email" required class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="prof-wa" class="block text-[13px] font-semibold text-ink mb-1.5">No. WhatsApp</label>
                        <input id="prof-wa" name="no_wa" type="text" required class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    </div>
                </div>

                <!-- Ubah Kata Sandi (Opsional) -->
                <div id="seksi-sandi" class="pt-5 border-t border-line space-y-4">
                    <div>
                        <h3 class="font-bold text-[16px] text-ink">Ubah Kata Sandi <span class="text-[12px] font-normal text-muted">(Opsional)</span></h3>
                        <p class="text-[12px] text-muted mt-0.5">Kosongkan kolom di bawah ini jika tidak ingin mengganti kata sandi.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="prof-pass-lama" class="block text-[13px] font-semibold text-ink mb-1.5">Kata Sandi Lama</label>
                            <input id="prof-pass-lama" name="kata_sandi_lama" type="password" placeholder="••••••••" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                        </div>
                        <div>
                            <label for="prof-pass-baru" class="block text-[13px] font-semibold text-ink mb-1.5">Kata Sandi Baru</label>
                            <input id="prof-pass-baru" name="kata_sandi_baru" type="password" minlength="8" placeholder="Minimal 8 karakter" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button id="prof-btn-save" type="submit" class="h-[44px] px-6 rounded-[12px] bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] transition shadow-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="loadProfil()" class="h-[44px] px-5 rounded-[12px] border border-line bg-white hover:bg-surface text-ink font-semibold text-[14px] transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const apiBase = (path) => `${window.ERIKA_CONFIG.siteUrl}/${path.replace(/^\/+/, '')}`;

function showProfilAlert(msg, isError = false) {
    const box = document.getElementById('prof-alert');
    box.className = `rounded-xl px-4 py-3 text-[13px] font-medium ${
        isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'
    }`;
    box.textContent = msg;
    box.classList.remove('hidden');
}

function setAvatarView(nama, fotoUrl) {
    const initEl = document.getElementById('prof-avatar-initials');
    const imgEl = document.getElementById('prof-avatar-img');
    initEl.textContent = (nama || 'P').charAt(0).toUpperCase();
    if (fotoUrl) {
        imgEl.src = fotoUrl.startsWith('http') ? fotoUrl : `${window.ERIKA_CONFIG.baseUrl}/${fotoUrl.replace(/^\/+/, '')}`;
        imgEl.classList.remove('hidden');
        initEl.classList.add('hidden');
    } else {
        imgEl.classList.add('hidden');
        initEl.classList.remove('hidden');
    }
}

function previewFotoProfil(input) {
    const file = input.files?.[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        ErikaUI.toast('Ukuran foto profil maksimal 2 MB.', 'warning');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = (ev) => {
        const imgEl = document.getElementById('prof-avatar-img');
        const initEl = document.getElementById('prof-avatar-initials');
        imgEl.src = ev.target.result;
        imgEl.classList.remove('hidden');
        initEl.classList.add('hidden');
    };
    reader.readAsDataURL(file);
}

async function loadProfil() {
    const gate = document.getElementById('prof-guest-gate');
    const main = document.getElementById('prof-main-layout');

    if (!ErikaAuth.isLoggedIn()) {
        gate.classList.remove('hidden');
        main.classList.add('hidden');
        return;
    }

    gate.classList.add('hidden');
    main.classList.remove('hidden');
    document.getElementById('prof-alert').classList.add('hidden');

    // Isi awal dari cache localStorage sambil menunggu API
    const cached = ErikaAuth.getUser();
    if (cached) {
        document.getElementById('prof-nama').value = cached.nama_lengkap || '';
        document.getElementById('prof-email').value = cached.email || '';
        document.getElementById('prof-wa').value = cached.no_wa || '';
        setAvatarView(cached.nama_lengkap, cached.foto_profile);
    }

    try {
        const res = await fetch(apiBase('api/pelanggan/profil'), {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            }
        });
        const json = await res.json().catch(() => null);

        if (res.status === 401) {
            ErikaAuth.logout();
            loadProfil();
            return;
        }
        if (!res.ok || !json || !json.status) {
            showProfilAlert(json?.message || `Gagal memuat profil terbaru (HTTP ${res.status}).`, true);
            return;
        }

        const p = json.data || {};
        document.getElementById('prof-nama').value = p.nama_lengkap || '';
        document.getElementById('prof-email').value = p.email || '';
        document.getElementById('prof-wa').value = p.no_wa || '';
        setAvatarView(p.nama_lengkap, p.foto_profile);
        ErikaAuth.setSession(ErikaAuth.getToken(), p);
    } catch {
        showProfilAlert('Belum dapat terhubung ke GET /api/pelanggan/profil.', true);
    }
}

async function submitProfil(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('nama_lengkap', document.getElementById('prof-nama').value.trim());
    formData.append('email', document.getElementById('prof-email').value.trim());
    formData.append('no_wa', document.getElementById('prof-wa').value.trim());

    const passLama = document.getElementById('prof-pass-lama').value;
    const passBaru = document.getElementById('prof-pass-baru').value;
    if (passBaru) {
        if (!passLama) {
            showProfilAlert('Masukkan kata sandi lama untuk mengubah kata sandi.', true);
            return;
        }
        formData.append('kata_sandi_lama', passLama);
        formData.append('kata_sandi_baru', passBaru);
    }

    const fotoFile = document.getElementById('prof-foto-input')?.files?.[0];
    if (fotoFile) {
        formData.append('foto_profile', fotoFile, fotoFile.name);
    }

    const btn = document.getElementById('prof-btn-save');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
        const res = await fetch(apiBase('api/pelanggan/profil/update'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${ErikaAuth.getToken()}`
            },
            body: formData
        });
        const json = await res.json().catch(() => null);

        if (!res.ok || !json || !json.status) {
            showProfilAlert(json?.message || `Gagal menyimpan perubahan (HTTP ${res.status}).`, true);
            return;
        }

        const updated = json.data || {};
        ErikaAuth.setSession(ErikaAuth.getToken(), updated);
        setAvatarView(updated.nama_lengkap, updated.foto_profile);
        document.getElementById('prof-pass-lama').value = '';
        document.getElementById('prof-pass-baru').value = '';
        document.getElementById('prof-foto-input').value = '';
        showProfilAlert('Profil berhasil diperbarui!', false);
        ErikaUI.toast('Profil berhasil diperbarui!', 'success');
    } catch {
        showProfilAlert('Tidak dapat terhubung ke POST /api/pelanggan/profil/update.', true);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Simpan Perubahan';
    }
}

function logoutAndRedirect() {
    ErikaAuth.logout();
    window.location.href = apiBase('/');
}

document.addEventListener('DOMContentLoaded', loadProfil);
</script>
<?= $this->endSection() ?>
