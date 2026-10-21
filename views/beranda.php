<section class="hero">
    <p class="eyebrow">LEARNING MANAGEMENT SYSTEM</p>
    <h1>Semua kegiatan belajar,<br><span>dalam satu ruang.</span></h1>
    <p class="intro">Kelola mata kuliah, materi, dan tugas dengan mudah.</p>
    <a class="btn" href="index.php?halaman=daftar&amp;entitas=mata_kuliah">Jelajahi mata kuliah <span aria-hidden="true">→</span></a>
</section>
<section class="section">
    <div class="section-heading">
        <div><p class="eyebrow">RINGKASAN</p><h2>Aktivitas akademik</h2></div>
        <span class="muted">Data langsung dari database</span>
    </div>
    <div class="stats-grid">
        <?php foreach (['mata_kuliah' => 'Mata Kuliah', 'materi' => 'Materi', 'tugas' => 'Tugas'] as $key => $label): ?>
            <a class="stat-card" href="index.php?halaman=daftar&amp;entitas=<?= h($key) ?>">
                <span><?= h($label) ?></span><strong><?= (int) $counts[$key] ?></strong><small>Lihat data <span aria-hidden="true">↗</span></small>
            </a>
        <?php endforeach ?>
    </div>
</section>
<section class="section quick-links">
    <div><p class="eyebrow">MULAI KELOLA</p><h2>Pilih area kerja</h2></div>
    <?php foreach ($entities as $key => $item): ?>
        <a class="quick-link" href="index.php?halaman=daftar&amp;entitas=<?= h($key) ?>"><span><?= h($item['plural']) ?></span><span aria-hidden="true">→</span></a>
    <?php endforeach ?>
</section>
