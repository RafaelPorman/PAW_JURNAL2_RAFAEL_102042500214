<?php
$entityLabel = $entities[$entity]['plural'];
$newUrl = 'index.php?halaman=form&entitas=' . rawurlencode($entity);
?>
<section class="page-heading">
    <div><p class="eyebrow">PENGELOLAAN LMS</p><h1><?= h($entityLabel) ?></h1><p class="muted">Temukan dan kelola data <?= h(strtolower($entityLabel)) ?>.</p></div>
    <a class="btn" href="<?= h($newUrl) ?>">+ Tambah <?= h($entities[$entity]['label']) ?></a>
</section>
<form class="toolbar" method="get" action="index.php">
    <input type="hidden" name="halaman" value="daftar">
    <input type="hidden" name="entitas" value="<?= h($entity) ?>">
    <label class="search-field"><span class="sr-only">Kata kunci pencarian</span><input type="search" name="cari" placeholder="<?= $entity === 'mata_kuliah' ? 'Cari kode atau nama mata kuliah' : 'Cari judul ' . strtolower($entityLabel) ?>" value="<?= h($search) ?>"></label>
    <?php if ($entity === 'materi' || $entity === 'tugas'): ?>
        <label><span class="sr-only">Filter mata kuliah</span>
            <select name="mata_kuliah_id">
                <option value="">Semua mata kuliah</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= (int) $course['id'] ?>" <?= (int) $course['id'] === ($courseFilter ?? 0) ? 'selected' : '' ?>><?= h($course['kode'] . ' — ' . $course['nama']) ?></option>
                <?php endforeach ?>
            </select>
        </label>
    <?php endif ?>
    <button class="btn btn-small" type="submit">Cari</button>
    <a class="btn btn-light btn-small" href="index.php?halaman=daftar&amp;entitas=<?= h($entity) ?>">Reset</a>
</form>
<p class="result-count"><?= count($records) ?> data ditemukan</p>
<?php if ($records === []): ?>
    <div class="empty-state"><span class="empty-icon">⌕</span><h2>Belum ada hasil yang cocok</h2><p>Coba kata kunci atau filter yang berbeda.</p><a class="text-link" href="index.php?halaman=daftar&amp;entitas=<?= h($entity) ?>">Reset pencarian <span aria-hidden="true">→</span></a></div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
            <?php if ($entity === 'mata_kuliah'): ?>
                <tr><th>Kode</th><th>Nama Mata Kuliah</th><th>Dosen</th><th>Semester</th><th>Aksi</th></tr>
            <?php elseif ($entity === 'materi'): ?>
                <tr><th>Judul Materi</th><th>Mata Kuliah</th><th>Tautan</th><th>Aksi</th></tr>
            <?php else: ?>
                <tr><th>Judul Tugas</th><th>Mata Kuliah</th><th>Tenggat</th><th>Status</th><th>Aksi</th></tr>
            <?php endif ?>
            </thead>
            <tbody>
            <?php foreach ($records as $row): ?>
                <tr>
                    <?php if ($entity === 'mata_kuliah'): ?>
                        <td><span class="code-pill"><?= h($row['kode']) ?></span></td><td class="primary-cell"><?= h($row['nama']) ?></td><td><?= h($row['dosen']) ?></td><td>Semester <?= (int) $row['semester'] ?></td>
                    <?php elseif ($entity === 'materi'): ?>
                        <td class="primary-cell"><?= h($row['judul']) ?></td><td><?= h($row['mata_kuliah_nama']) ?></td><td><a class="text-link" href="<?= h($row['tautan']) ?>" target="_blank" rel="noopener noreferrer">Buka tautan ↗</a></td>
                    <?php else: ?>
                        <td class="primary-cell"><?= h($row['judul']) ?></td><td><?= h($row['mata_kuliah_nama']) ?></td><td><?= h(date('d M Y', strtotime($row['tenggat']))) ?></td><td><span class="status status-<?= h(strtolower($row['status'])) ?>"><?= h($row['status']) ?></span></td>
                    <?php endif ?>
                    <td><div class="row-actions">
                        <a class="icon-link" href="index.php?halaman=detail&amp;entitas=<?= h($entity) ?>&amp;id=<?= (int) $row['id'] ?>">Detail</a>
                        <a class="icon-link" href="index.php?halaman=form&amp;entitas=<?= h($entity) ?>&amp;id=<?= (int) $row['id'] ?>">Edit</a>
                        <a class="icon-link danger-link" href="index.php?halaman=konfirmasi-hapus&amp;entitas=<?= h($entity) ?>&amp;id=<?= (int) $row['id'] ?>">Hapus</a>
                    </div></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>
