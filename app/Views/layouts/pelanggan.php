<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Beranda') ?> — Percetakan Erika</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2A2F8F',
                        magenta: '#E5197E',
                        cyan: '#12A9E0',
                        yellow: '#FFC727',
                        ink: '#1B1F3B',
                        muted: '#6B7088',
                        surface: '#F7F8FC',
                        line: '#E4E6EF',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1B1F3B;
            background-color: #FFFFFF;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <script>
        window.ERIKA_CONFIG = {
            baseUrl: <?= json_encode(rtrim(base_url('/'), '/')) ?>,
            siteUrl: <?= json_encode(rtrim(site_url('/'), '/')) ?>
        };
    </script>
</head>
<body class="min-h-screen flex flex-col bg-white text-ink antialiased">

<?php
$activeNav = $activeNav ?? 'beranda';
$navClass = static function (string $key) use ($activeNav): string {
    return $activeNav === $key
        ? 'font-semibold text-primary'
        : 'font-medium text-muted hover:text-ink transition-colors';
};
?>

<!-- Navbar Utama (Tamu & Login State) -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-line">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-[16px] flex items-center justify-between gap-4">
        <!-- Logo CMYK Erika -->
        <a href="<?= site_url('/') ?>" class="flex items-center gap-[10px] shrink-0 group">
            <div class="flex flex-col gap-[2px] shrink-0">
                <div class="flex gap-[2px]">
                    <span class="w-[13px] h-[13px] rounded-[3px] bg-cyan block"></span>
                    <span class="w-[13px] h-[13px] rounded-[3px] bg-magenta block"></span>
                </div>
                <div class="flex gap-[2px]">
                    <span class="w-[13px] h-[13px] rounded-[3px] bg-yellow block"></span>
                    <span class="w-[13px] h-[13px] rounded-[3px] bg-ink block"></span>
                </div>
            </div>
            <div class="flex flex-col leading-none">
                <span class="font-extrabold text-[20px] leading-[22px] text-ink tracking-tight">Erika</span>
                <span class="font-semibold text-[9px] tracking-[0.12em] text-muted uppercase">PERCETAKAN</span>
            </div>
        </a>

        <!-- Links Navigasi Tengah -->
        <nav class="hidden md:flex items-center gap-[32px] text-[14px]">
            <a href="<?= site_url('/') ?>" class="<?= $navClass('beranda') ?>">Beranda</a>
            <a href="<?= site_url('layanan') ?>" class="<?= $navClass('layanan') ?>">Layanan</a>
            <a href="<?= site_url('pesan/undangan') ?>" class="<?= $navClass('template') ?>">Template</a>
            <a href="<?= site_url('pesanan-saya') ?>" class="<?= $navClass('pesanan') ?>">Pesanan Saya</a>
        </nav>

        <!-- Actions Kanan -->
        <div class="flex items-center gap-3 shrink-0">
            <!-- Tombol Keranjang -->
            <button type="button" onclick="ErikaCart.openDrawer()" class="relative inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-line bg-white hover:bg-surface text-ink transition" aria-label="Buka Keranjang">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span id="nav-cart-badge" class="hidden absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] px-1 rounded-full bg-magenta text-white text-[11px] font-bold flex items-center justify-center">0</span>
            </button>

            <!-- Guest Actions (Navbar/Tamu) -->
            <div id="nav-guest-actions" class="flex items-center gap-[12px]">
                <button type="button" onclick="ErikaAuth.openLoginModal()" class="bg-white border border-line hover:bg-surface text-ink font-semibold text-[14px] px-[20px] py-[9px] rounded-[10px] transition">
                    Masuk
                </button>
                <button type="button" onclick="ErikaAuth.openRegisterModal()" class="bg-primary hover:bg-[#212675] text-white font-semibold text-[14px] px-[20px] py-[9px] rounded-[10px] transition shadow-sm">
                    Daftar
                </button>
            </div>

            <!-- Logged-in Actions (Navbar/Login) -->
            <div id="nav-user-actions" class="hidden flex items-center gap-2.5">
                <a href="<?= site_url('pesanan-saya') ?>" class="hidden sm:inline-flex items-center gap-2 bg-surface hover:bg-line/60 border border-line text-ink font-semibold text-[13px] px-3.5 py-2 rounded-[10px] transition">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Pesanan Saya</span>
                </a>
                <div class="relative">
                    <button type="button" id="nav-user-menu-btn" onclick="ErikaUI.toggleUserMenu()" class="inline-flex items-center gap-2.5 bg-white border border-line hover:bg-surface pl-2 pr-3 py-1.5 rounded-[10px] transition">
                        <span id="nav-user-avatar" class="w-7 h-7 rounded-lg bg-primary text-white text-xs font-bold flex items-center justify-center overflow-hidden">P</span>
                        <span id="nav-user-name" class="text-[13px] font-semibold text-ink max-w-[120px] truncate">Pelanggan</span>
                        <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="nav-user-dropdown" class="hidden absolute right-0 mt-2 w-52 bg-white border border-line rounded-xl shadow-xl py-2 z-50">
                        <div class="px-4 py-2 border-b border-line">
                            <p id="dropdown-user-name" class="text-xs font-bold text-ink truncate">Pelanggan</p>
                            <p id="dropdown-user-email" class="text-[11px] text-muted truncate">-</p>
                        </div>
                        <a href="<?= site_url('profil') ?>" class="block px-4 py-2 text-[13px] font-medium text-ink hover:bg-surface">Profil Saya</a>
                        <a href="<?= site_url('pesanan-saya') ?>" class="block px-4 py-2 text-[13px] font-medium text-ink hover:bg-surface">Pesanan Saya</a>
                        <a href="<?= site_url('keranjang') ?>" class="block px-4 py-2 text-[13px] font-medium text-ink hover:bg-surface">Keranjang</a>
                        <button type="button" onclick="ErikaAuth.logout()" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-magenta hover:bg-pink-50">Keluar</button>
                    </div>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <button type="button" onclick="document.getElementById('mobile-nav-menu').classList.toggle('hidden')" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-line text-ink">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div id="mobile-nav-menu" class="hidden md:hidden border-t border-line bg-white px-4 py-3 space-y-2 text-[14px]">
        <a href="<?= site_url('/') ?>" class="block py-1.5 <?= $navClass('beranda') ?>">Beranda</a>
        <a href="<?= site_url('layanan') ?>" class="block py-1.5 <?= $navClass('layanan') ?>">Layanan</a>
        <a href="<?= site_url('pesan/undangan') ?>" class="block py-1.5 <?= $navClass('template') ?>">Template</a>
        <a href="<?= site_url('pesanan-saya') ?>" class="block py-1.5 <?= $navClass('pesanan') ?>">Pesanan Saya</a>
        <a href="<?= site_url('profil') ?>" class="block py-1.5 <?= $navClass('profil') ?>">Profil Saya</a>
    </div>
</header>

<!-- Konten Utama -->
<main class="flex-1">
    <?= $this->renderSection('content') ?>
</main>

<!-- Footer Utama (Figma 2:91) -->
<footer class="bg-ink text-white mt-auto">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-10 border-b border-white/10">
            <div class="md:col-span-1 space-y-4">
                <div class="flex items-center gap-[10px]">
                    <div class="flex flex-col gap-[2px]">
                        <div class="flex gap-[2px]">
                            <span class="w-[13px] h-[13px] rounded-[3px] bg-cyan block"></span>
                            <span class="w-[13px] h-[13px] rounded-[3px] bg-magenta block"></span>
                        </div>
                        <div class="flex gap-[2px]">
                            <span class="w-[13px] h-[13px] rounded-[3px] bg-yellow block"></span>
                            <span class="w-[13px] h-[13px] rounded-[3px] bg-white block"></span>
                        </div>
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-extrabold text-[20px] leading-[22px] text-white">Erika</span>
                        <span class="font-semibold text-[9px] tracking-[0.12em] text-slate-400 uppercase">PERCETAKAN</span>
                    </div>
                </div>
                <p class="text-[13px] leading-relaxed text-slate-300">
                    Solusi cetak dokumen, spanduk, undangan, banner, dan kebutuhan ATK berkualitas dengan estimasi harga transparan dan pantauan antrean online.
                </p>
            </div>

            <div>
                <h4 class="font-bold text-[14px] text-white mb-4">Layanan Cetak</h4>
                <ul class="space-y-2.5 text-[13px] text-slate-300">
                    <li><a href="<?= site_url('pesan/dokumen') ?>" class="hover:text-yellow transition">Print Dokumen &amp; Jilid</a></li>
                    <li><a href="<?= site_url('pesan/spanduk') ?>" class="hover:text-yellow transition">Cetak Spanduk Custom</a></li>
                    <li><a href="<?= site_url('pesan/undangan') ?>" class="hover:text-yellow transition">Cetak Undangan</a></li>
                    <li><a href="<?= site_url('pesan/banner') ?>" class="hover:text-yellow transition">Cetak X-Banner &amp; Roll</a></li>
                    <li><a href="<?= site_url('layanan?tab=atk') ?>" class="hover:text-yellow transition">Katalog Produk ATK</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-[14px] text-white mb-4">Informasi</h4>
                <ul class="space-y-2.5 text-[13px] text-slate-300">
                    <li><a href="<?= site_url('/#cara-pesan') ?>" class="hover:text-cyan transition">Cara Memesan</a></li>
                    <li><a href="<?= site_url('/#promo') ?>" class="hover:text-cyan transition">Promo Berjalan</a></li>
                    <li><a href="<?= site_url('layanan') ?>" class="hover:text-cyan transition">Daftar Harga Dasar</a></li>
                    <li><a href="<?= site_url('admin/login') ?>" class="hover:text-cyan transition">Panel Admin</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-[14px] text-white mb-4">Jam Operasional</h4>
                <ul class="space-y-2.5 text-[13px] text-slate-300">
                    <li>Senin – Sabtu: 08.00 – 20.00 WIB</li>
                    <li>Minggu: 09.00 – 16.00 WIB</li>
                    <li class="pt-1 text-yellow font-semibold">WhatsApp: 0812-0000-0001</li>
                </ul>
            </div>
        </div>

        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-[12px] text-slate-400">
            <p>&copy; <?= date('Y') ?> Percetakan Erika (E-Print Erika). Seluruh hak cipta dilindungi.</p>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-sm bg-cyan inline-block"></span>
                <span class="w-2.5 h-2.5 rounded-sm bg-magenta inline-block"></span>
                <span class="w-2.5 h-2.5 rounded-sm bg-yellow inline-block"></span>
                <span class="w-2.5 h-2.5 rounded-sm bg-white inline-block"></span>
            </div>
        </div>
    </div>
</footer>

<!-- Drawer Keranjang Sementara (Menampilkan item & File yang tersimpan di state keranjang) -->
<div id="cart-drawer-backdrop" class="hidden fixed inset-0 z-50 bg-ink/50 backdrop-blur-sm flex justify-end" onclick="if(event.target===this) ErikaCart.closeDrawer()">
    <div class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col">
        <div class="px-6 py-5 border-b border-line flex items-center justify-between">
            <div>
                <h3 class="font-bold text-[18px] text-ink">Keranjang Pesanan</h3>
                <p class="text-[12px] text-muted">Item &amp; file siap cetak tersimpan otomatis</p>
            </div>
            <button type="button" onclick="ErikaCart.closeDrawer()" class="p-2 rounded-lg hover:bg-surface text-muted hover:text-ink">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="cart-drawer-items" class="flex-1 overflow-y-auto p-6 space-y-4"></div>
        <div class="p-6 border-t border-line bg-surface space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-[14px] font-medium text-muted">Estimasi Total</span>
                <span id="cart-drawer-total" class="text-[20px] font-extrabold text-primary">Rp 0</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="ErikaCart.clearAll()" class="border border-line bg-white hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-ink font-semibold text-[13px] py-3 rounded-[10px] transition">
                    Kosongkan
                </button>
                <button type="button" onclick="ErikaCart.proceedToCheckout()" class="bg-primary hover:bg-[#212675] text-white font-semibold text-[13px] py-3 rounded-[10px] transition">
                    Checkout →
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed bottom-5 right-5 z-[60] flex flex-col gap-2 max-w-sm"></div>

<!-- Modal 02 (Login) & Modal 03 (Daftar) -->
<?= $this->include('pelanggan/modal_login') ?>
<?= $this->include('pelanggan/modal_daftar') ?>

<!-- Shared Client App Script -->
<script src="<?= base_url('assets/js/pelanggan-app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
