<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title><?= e($pageTitle ?? config('app.name') . ' - ' . config('app.tagline')) ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="<?= e($metaDescription ?? 'Platform donasi pohon, relawan penanaman, dan merchandise untuk lingkungan Indonesia yang lebih hijau.') ?>">
    <meta property="og:title" content="<?= e($pageTitle ?? config('app.name')) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? config('app.tagline')) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= url() ?>">

    <!-- Google Fonts: Bitter (Slab-Serif Poster), Space Mono & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@500;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Header & Navigation -->
    <?php require VIEW_PATH . '/partials/navbar.php'; ?>

    <!-- Main Content Body -->
    <main class="flex-grow-1">
        <!-- Flash Messages Global Container -->
        <div class="container mt-3">
            <?php require VIEW_PATH . '/partials/flash-messages.php'; ?>
        </div>

        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php require VIEW_PATH . '/partials/footer.php'; ?>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <?= $extraJs ?>
    <?php endif; ?>
</body>
</html>

