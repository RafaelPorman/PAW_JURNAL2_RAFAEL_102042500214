<section class="page-heading"><div><p class="eyebrow">DETAIL DATA</p><h1><?= h($entities[$entity]['label']) ?></h1><p class="muted">Informasi lengkap data yang dipilih.</p></div></section>
<article class="detail-card">
    <?php if ($entity === 'mata_kuliah'): ?>
        <dl><dt>Kode</dt><dd><?= h($record['kode']) ?></dd><dt>Nama</dt><dd><?= h($record['nama']) ?></dd><dt>Dosen</dt><dd><?= h($record['dosen']) ?></dd><dt>Semester</dt><dd><?= (int) $record['semester'] ?></dd></dl>
    <?php elseif ($entity === 'materi'): ?>
        <dl><dt>Mata Kuliah</dt><dd><?= h($record['mata_kuliah_nama']) ?></dd><dt>Judul</dt><dd><?= h($record['judul']) ?></dd><dt>Isi</dt><dd class="preserve-lines"><?= h($record['isi']) ?></dd><dt>Tautan</dt><dd><a class="text-link" href="<?= h($record['tautan']) ?>" target="_blank" rel="noopener noreferrer"><?= h($record['tautan']) ?> ↗</a></dd></dl>
    <?php else: ?>
        <dl><dt>Mata Kuliah</dt><dd><?= h($record['mata_kuliah_nama']) ?></dd><dt>Judul</dt><dd><?= h($record['judul']) ?></dd><dt>Instruksi</dt><dd class="preserve-lines"><?= h($record['instruksi']) ?></dd><dt>Tenggat</dt><dd><?= h(date('d M Y', strtotime($record['tenggat']))) ?></dd><dt>Status</dt><dd><span class="status status-<?= h(strtolower($record['status'])) ?>"><?= h($record['status']) ?></span></dd></dl>
    <?php endif ?>
    <div class="form-actions"><a class="btn" href="index.php?halaman=form&amp;entitas=<?= h($entity) ?>&amp;id=<?= (int) $record['id'] ?>">Edit data</a><a class="btn btn-light" href="index.php?halaman=daftar&amp;entitas=<?= h($entity) ?>">Kembali ke daftar</a></div>
</article>
