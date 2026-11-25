<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Panel - Percetakan Erika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F4F5F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 920px;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(27, 26, 51, 0.12);
            background: #fff;
        }
        .brand-side {
            background: linear-gradient(145deg, #1B1A33 0%, #312E81 100%);
            color: #fff;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .logo-grid {
            display: inline-grid;
            grid-template-columns: repeat(2, 12px);
            gap: 4px;
        }
        .logo-grid span { width: 12px; height: 12px; border-radius: 3px; }
        .btn-erika {
            background-color: #312E81;
            border-color: #312E81;
            color: #fff;
            font-weight: 600;
            padding: 0.7rem;
            border-radius: 10px;
        }
        .btn-erika:hover { background-color: #252269; color: #fff; }
    </style>
</head>
<body>
<div class="container p-3">
    <div class="login-card mx-auto row g-0">
        <div class="col-lg-5 brand-side">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="logo-grid">
                        <span style="background:#EC4899"></span>
                        <span style="background:#06B6D4"></span>
                        <span style="background:#FACC15"></span>
                        <span style="background:#FFFFFF"></span>
                    </div>
                    <span class="fs-4 fw-bold">Erika</span>
                </div>
                <h2 class="fw-bold lh-base mt-4">Kelola pesanan, antrean, dan stok dalam satu tempat.</h2>
                <p class="text-white-50 mt-3 small">Panel khusus Admin & Owner Percetakan Erika.</p>
            </div>
            <div class="small text-white-50">&copy; 2026 Percetakan Erika</div>
        </div>

        <div class="col-lg-7 p-4 p-md-5">
            <h4 class="fw-bold text-dark mb-1">Masuk ke Panel</h4>
            <p class="text-muted small mb-4">Gunakan akun admin yang terdaftar.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 small"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success py-2 small"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <form action="<?= base_url('admin/login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username (mis. erika)" value="<?= old('username') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="kataSandi" name="kata_sandi" class="form-control" placeholder="Masukkan kata sandi" required>
                        <button type="button" class="btn btn-outline-secondary small" onclick="togglePass()">Tampilkan</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ingatSaya">
                        <label class="form-check-label small text-muted" for="ingatSaya">Ingat saya</label>
                    </div>
                    <span class="small text-muted">Lupa kata sandi? Hubungi Owner</span>
                </div>

                <button type="submit" class="btn btn-erika w-100 mb-3">Masuk</button>
                <p class="text-center text-muted small mb-0">Hak akses mengikuti role akun: <strong>Owner</strong> atau <strong>Admin</strong>.</p>
            </form>
        </div>
    </div>
</div>
<script>
function togglePass() {
    const input = document.getElementById('kataSandi');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>