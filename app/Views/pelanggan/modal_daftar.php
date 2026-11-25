<!-- 03 · Pop-up Daftar (Figma 4:249 / Modal 4:442) -->
<div id="modal-daftar" class="hidden fixed inset-0 z-50 bg-ink/55 backdrop-blur-sm flex items-center justify-center p-4" onclick="if(event.target===this) ErikaAuth.closeModals()">
    <div class="w-full max-w-[480px] bg-white rounded-[20px] shadow-2xl border border-line p-7 sm:p-8 relative animate-[fadeIn_0.15s_ease-out]">
        <!-- Header -->
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="font-bold text-[22px] text-ink tracking-tight">Buat Akun Pelanggan</h2>
                <p class="text-[13px] text-muted mt-1">Cukup sekali daftar, pesan kapan saja tanpa antre</p>
            </div>
            <button type="button" onclick="ErikaAuth.closeModals()" class="w-8 h-8 rounded-lg flex items-center justify-center text-muted hover:bg-surface hover:text-ink transition" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Alert Error / Info -->
        <div id="register-alert" class="hidden mb-4 px-3.5 py-2.5 rounded-xl text-[13px] font-medium"></div>

        <!-- Form Register -->
        <form id="form-register-pelanggan" onsubmit="ErikaAuth.submitRegister(event)" class="space-y-3.5">
            <div>
                <label for="reg-nama" class="block text-[13px] font-semibold text-ink mb-1.5">Nama Lengkap</label>
                <input id="reg-nama" name="nama_lengkap" type="text" required minlength="3" placeholder="Siti Aminah" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="reg-email" class="block text-[13px] font-semibold text-ink mb-1.5">Email</label>
                    <input id="reg-email" name="email" type="email" required placeholder="nama@email.com" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                </div>
                <div>
                    <label for="reg-wa" class="block text-[13px] font-semibold text-ink mb-1.5">No. WhatsApp</label>
                    <input id="reg-wa" name="no_wa" type="text" required placeholder="0812-3456-7890" class="w-full h-[44px] px-4 rounded-[10px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                </div>
            </div>

            <div>
                <label for="reg-password" class="block text-[13px] font-semibold text-ink mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input id="reg-password" name="kata_sandi" type="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full h-[44px] pl-4 pr-24 rounded-[10px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    <button type="button" onclick="ErikaUI.togglePassword('reg-password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[12px] font-semibold text-primary hover:underline">
                        Tampilkan
                    </button>
                </div>
            </div>

            <button id="btn-submit-register" type="submit" class="w-full h-[46px] mt-2 bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] rounded-[12px] transition flex items-center justify-center gap-2 shadow-sm">
                <span>Daftar &amp; Lanjut Memesan</span>
            </button>
        </form>

        <p class="mt-5 text-center text-[13px] text-muted">
            Sudah punya akun?
            <button type="button" onclick="ErikaAuth.openLoginModal()" class="font-semibold text-primary hover:underline ml-1">Masuk</button>
        </p>
    </div>
</div>
