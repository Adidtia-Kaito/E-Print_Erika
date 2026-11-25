<!-- 02 · Pop-up Login (Figma 4:23 / Modal 4:216) -->
<div id="modal-login" class="hidden fixed inset-0 z-50 bg-ink/55 backdrop-blur-sm flex items-center justify-center p-4" onclick="if(event.target===this) ErikaAuth.closeModals()">
    <div class="w-full max-w-[440px] bg-white rounded-[20px] shadow-2xl border border-line p-7 sm:p-8 relative animate-[fadeIn_0.15s_ease-out]">
        <!-- Header -->
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-[22px] text-ink tracking-tight">Masuk ke Akun Erika</h2>
            <button type="button" onclick="ErikaAuth.closeModals()" class="w-8 h-8 rounded-lg flex items-center justify-center text-muted hover:bg-surface hover:text-ink transition" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Konteks Banner -->
        <div class="bg-surface border border-line rounded-xl px-3.5 py-3 flex items-start gap-2.5 mb-5">
            <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <p class="text-[12px] leading-relaxed text-muted">
                Login diperlukan untuk memesan layanan, menyimpan riwayat pesanan, dan melacak antrean secara online.
            </p>
        </div>

        <!-- Alert Error / Info -->
        <div id="login-alert" class="hidden mb-4 px-3.5 py-2.5 rounded-xl text-[13px] font-medium"></div>

        <!-- Form Login -->
        <form id="form-login-pelanggan" onsubmit="ErikaAuth.submitLogin(event)" class="space-y-4">
            <div>
                <label for="login-identifier" class="block text-[13px] font-semibold text-ink mb-1.5">Email atau No. WhatsApp</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="login-identifier" name="identifier" type="text" required placeholder="nama@email.com atau 0812..." class="w-full h-[44px] pl-10 pr-4 rounded-[10px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                </div>
            </div>

            <div>
                <label for="login-password" class="block text-[13px] font-semibold text-ink mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="login-password" name="kata_sandi" type="password" required placeholder="••••••••" class="w-full h-[44px] pl-10 pr-24 rounded-[10px] border border-line text-[14px] text-ink placeholder:text-muted/60 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15">
                    <button type="button" onclick="ErikaUI.togglePassword('login-password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[12px] font-semibold text-primary hover:underline">
                        Tampilkan
                    </button>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="javascript:void(0)" onclick="ErikaUI.toast('Hubungi WhatsApp Percetakan Erika (0812-0000-0001) untuk reset kata sandi.', 'info')" class="text-[12px] font-semibold text-primary hover:underline">
                    Lupa kata sandi?
                </a>
            </div>

            <button id="btn-submit-login" type="submit" class="w-full h-[46px] bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] rounded-[12px] transition flex items-center justify-center gap-2 shadow-sm">
                <span>Masuk &amp; Lanjut Memesan</span>
            </button>
        </form>

        <p class="mt-5 text-center text-[13px] text-muted">
            Belum punya akun?
            <button type="button" onclick="ErikaAuth.openRegisterModal()" class="font-semibold text-primary hover:underline ml-1">Daftar sekarang</button>
        </p>
    </div>
</div>
