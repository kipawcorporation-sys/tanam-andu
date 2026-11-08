<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($pageTitle ?? 'Autentikasi - ' . config('app.name')) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(25, 135, 84, 0.12);
            overflow: hidden;
            background: #ffffff;
        }
        .auth-card-top-bar {
            height: 6px;
            background: linear-gradient(90deg, #198754, #20c997);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5 col-xl-5">

                <!-- Brand Logo Top -->
                <div class="text-center mb-4">
                    <a href="<?= url('/') ?>" class="text-decoration-none d-inline-flex align-items-center gap-2">
                        <span class="fs-1">🌱</span>
                        <span class="fs-3 fw-bold text-success">Tanam<span class="text-dark">Pohon</span>.id</span>
                    </a>
                    <p class="text-muted small mt-1">Platform Donasi & Relawan Penanaman Pohon</p>
                </div>

                <!-- Flash Messages -->
                <?php require VIEW_PATH . '/partials/flash-messages.php'; ?>

                <!-- Auth Card Container -->
                <div class="card auth-card">
                    <div class="auth-card-top-bar"></div>
                    <div class="card-body p-4 p-sm-5">
                        <?= $content ?>
                    </div>
                </div>

                <!-- Back to Home Link -->
                <div class="text-center mt-4">
                    <a href="<?= url('/') ?>" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

