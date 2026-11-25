<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> - E-Print Erika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1B1A33;
            --sidebar-active: #312E81;
            --body-bg: #F5F6FA;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #1E293B;
        }
        .sidebar {
            width: 255px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: #CBD5E1;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        .logo-grid {
            display: inline-grid;
            grid-template-columns: repeat(2, 10px);
            gap: 3px;
        }
        .logo-grid span { width: 10px; height: 10px; border-radius: 2px; }
        .sidebar-section {
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            color: #64748B;
            font-weight: 700;
            padding: 1rem 1.25rem 0.35rem;
            text-transform: uppercase;
        }
        .sidebar .nav-link {
            color: #CBD5E1;
            padding: 0.6rem 1rem;
            margin: 0.15rem 0.75rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.06);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: var(--sidebar-active);
            color: #fff;
            font-weight: 600;
        }
        .main-content {
            margin-left: 255px;
            padding: 1.75rem 2rem;
        }
        .card-clean {
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        }
        .btn-primary-erika {
            background-color: #312E81;
            border-color: #312E81;
            color: #fff;
            border-radius: 9px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .btn-primary-erika:hover { background-color: #252269; color: #fff; }
        @media (max-width: 991px) {
            .sidebar { position: static; width: 100%; min-height: auto; }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="p-3 d-flex align-items-center gap-2 border-bottom border-secondary border-opacity-25">
        <div class="logo-grid">
            <span style="background:#EC4899"></span>
            <span style="background:#06B6D4"></span>
            <span style="background:#FACC15"></span>
            <span style="background:#FFFFFF"></span>
        </div>
        <div>
            <div class="fw-bold text-white lh-1">Erika</div>
            <small class="text-white-50" style="font-size: 0.65rem; letter-spacing: 0.08em;">ADMIN PANEL</small>
        </div>
    </div>

    <div class="flex-grow-1 overflow-auto pb-3">
        <div class="sidebar-section">Operasional</div>
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="<?= base_url('admin/pesanan') ?>" class="nav-link <?= ($activeMenu ?? '') === 'pesanan' ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i> Pesanan
        </a>
        <a href="<?= base_url('admin/antrean') ?>" class="nav-link <?= ($activeMenu ?? '') === 'antrean' ? 'active' : '' ?>">
            <i class="bi bi-list-task"></i> Antrean
        </a>

        <div class="sidebar-section">Katalog</div>
        <a href="<?= base_url('admin/layanan') ?>" class="nav-link <?= ($activeMenu ?? '') === 'layanan' ? 'active' : '' ?>">
            <i class="bi bi-layers"></i> Layanan & Opsi
        </a>
        <a href="<?= base_url('admin/promo') ?>" class="nav-link <?= ($activeMenu ?? '') === 'promo' ? 'active' : '' ?>">
            <i class="bi bi-tag"></i> Promo
        </a>

        <div class="sidebar-section">Inventori</div>
        <a href="<?= base_url('admin/inventori') ?>" class="nav-link <?= ($activeMenu ?? '') === 'inventori' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Bahan & Produk
        </a>
        <a href="<?= base_url('admin/pembelian') ?>" class="nav-link <?= ($activeMenu ?? '') === 'pembelian' ? 'active' : '' ?>">
            <i class="bi bi-truck"></i> Pembelian & Pemasok
        </a>

        <div class="sidebar-section">Keuangan</div>
        <a href="<?= base_url('admin/pembayaran') ?>" class="nav-link <?= ($activeMenu ?? '') === 'pembayaran' ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i> Pembayaran
        </a>

        <?php if (session()->get('admin_role') === 'OWNER'): ?>
            <div class="sidebar-section">Owner</div>
            <a href="<?= base_url('admin/laporan') ?>" class="nav-link <?= ($activeMenu ?? '') === 'laporan' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Laporan
            </a>
            <a href="<?= base_url('admin/pengguna') ?>" class="nav-link <?= ($activeMenu ?? '') === 'pengguna' ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Pengguna
            </a>
        <?php endif; ?>
    </div>

    <div class="p-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <?= strtoupper(substr(session()->get('admin_nama') ?? 'ER', 0, 2)) ?>
            </div>
            <div class="lh-sm">
                <div class="text-white fw-semibold small"><?= esc(session()->get('admin_nama')) ?></div>
                <small class="text-white-50" style="font-size:0.72rem;"><?= esc(ucfirst(strtolower(session()->get('admin_role')))) ?></small>
            </div>
        </div>
        <a href="<?= base_url('admin/logout') ?>" class="text-white-50" title="Keluar"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</aside>

<main class="main-content">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>