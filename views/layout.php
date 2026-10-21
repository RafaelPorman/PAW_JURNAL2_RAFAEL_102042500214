<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> | LMS EAD</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div class="wrap nav-wrap">
        <a class="brand" href="index.php"><span class="brand-mark">L</span><span>LMS <small>LABORATORIUM EAD</small></span></a>
        <nav aria-label="Navigasi utama">
            <a href="index.php" <?= $page === 'beranda' ? 'aria-current="page"' : '' ?>>Beranda</a>
            <a href="index.php?halaman=daftar&amp;entitas=mata_kuliah">Mata Kuliah</a>
            <a href="index.php?halaman=daftar&amp;entitas=materi">Materi</a>
            <a href="index.php?halaman=daftar&amp;entitas=tugas">Tugas</a>
        </nav>
    </div>
</header>
<main class="wrap">
    <?php if ($flash !== ''): ?>
        <p class="notice" role="status"><?= h($flash) ?></p>
    <?php endif ?>
    <?php if (isset($errors['filter'])): ?>
        <p class="notice notice-error" role="alert"><?= h($errors['filter']) ?></p>
    <?php endif ?>
    <?php
    $pageViews = [
        'beranda' => __DIR__ . '/beranda.php',
        'daftar' => __DIR__ . '/daftar.php',
        'form' => __DIR__ . '/form.php',
        'detail' => __DIR__ . '/detail.php',
        'konfirmasi-hapus' => __DIR__ . '/konfirmasi-hapus.php',
    ];
    require $pageViews[$page];
    ?>
</main>
<footer><div class="wrap"><span>LMS EAD</span><span>Laboratorium EAD · Pengelolaan pembelajaran</span></div></footer>
</body>
</html>
