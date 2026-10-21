<?php
$isEdit = $id !== null;
$title = ($isEdit ? 'Edit ' : 'Tambah ') . $entities[$entity]['label'];
?>
<section class="page-heading"><div><p class="eyebrow">FORMULIR DATA</p><h1><?= h($title) ?></h1><p class="muted">Lengkapi informasi dengan benar sebelum menyimpan.</p></div></section>
<?php if ($errors !== []): ?>
    <div class="notice notice-error" role="alert">
        <?php if (isset($errors['save'])): ?>
            <strong><?= h($errors['save']) ?></strong>
        <?php else: ?>
            <strong><?= $isEdit ? 'Perubahan gagal disimpan. Periksa kembali isian berikut:' : 'Data gagal ditambahkan. Periksa kembali isian berikut:' ?></strong>
            <ul><?php foreach ($errors as $message): ?><li><?= h($message) ?></li><?php endforeach ?></ul>
        <?php endif ?>
    </div>
<?php endif ?>
<?php if (($entity === 'materi' || $entity === 'tugas') && $courses === []): ?>
    <div class="empty-state compact"><h2>Belum ada mata kuliah</h2><p>Buat mata kuliah terlebih dahulu agar dapat menghubungkan <?= h(strtolower($entities[$entity]['label'])) ?>.</p><a class="btn" href="index.php?halaman=form&amp;entitas=mata_kuliah">Tambah mata kuliah</a></div>
<?php else: ?>
    <form class="form-card" method="post" action="index.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="entitas" value="<?= h($entity) ?>">
        <input type="hidden" name="id" value="<?= $id === null ? '' : (int) $id ?>">
        <?php if ($entity === 'mata_kuliah'): ?>
            <div class="form-grid">
                <label>Kode Mata Kuliah<input name="kode" maxlength="20" required value="<?= h($formData['kode']) ?>" placeholder="Contoh: PAW-01"><small>3-20 karakter; huruf kapital, angka, atau tanda -.</small></label>
                <label>Nama Mata Kuliah<input name="nama" maxlength="100" required value="<?= h($formData['nama']) ?>" placeholder="Nama mata kuliah"></label>
                <label>Dosen Pengampu<input name="dosen" maxlength="100" required value="<?= h($formData['dosen']) ?>" placeholder="Nama dosen"></label>
                <label>Semester<input type="number" name="semester" min="1" max="14" required value="<?= h($formData['semester']) ?>" placeholder="1-14"></label>
            </div>
        <?php elseif ($entity === 'materi'): ?>
            <label>Mata Kuliah<select name="mata_kuliah_id" required><option value="">Pilih mata kuliah</option><?php foreach ($courses as $course): ?><option value="<?= (int) $course['id'] ?>" <?= (string) $course['id'] === (string) $formData['mata_kuliah_id'] ? 'selected' : '' ?>><?= h($course['kode'] . ' — ' . $course['nama']) ?></option><?php endforeach ?></select></label>
            <label>Judul Materi<input name="judul" maxlength="150" required value="<?= h($formData['judul']) ?>" placeholder="Judul materi"></label>
            <label>Isi Materi<textarea name="isi" rows="6" maxlength="2000" required placeholder="Tuliskan materi pembelajaran..."><?= h($formData['isi']) ?></textarea><small>3-2000 karakter.</small></label>
            <label>Tautan Materi<input type="url" name="tautan" maxlength="255" required value="<?= h($formData['tautan']) ?>" placeholder="https://contoh.com/materi"><small>Gunakan tautan yang diawali http:// atau https://.</small></label>
        <?php else: ?>
            <label>Mata Kuliah<select name="mata_kuliah_id" required><option value="">Pilih mata kuliah</option><?php foreach ($courses as $course): ?><option value="<?= (int) $course['id'] ?>" <?= (string) $course['id'] === (string) $formData['mata_kuliah_id'] ? 'selected' : '' ?>><?= h($course['kode'] . ' — ' . $course['nama']) ?></option><?php endforeach ?></select></label>
            <label>Judul Tugas<input name="judul" maxlength="150" required value="<?= h($formData['judul']) ?>" placeholder="Judul tugas"></label>
            <label>Instruksi<textarea name="instruksi" rows="6" maxlength="2000" required placeholder="Tuliskan instruksi pengerjaan..."><?= h($formData['instruksi']) ?></textarea><small>3-2000 karakter.</small></label>
            <div class="form-grid">
                <label>Tenggat<input type="date" name="tenggat" required value="<?= h($formData['tenggat']) ?>"></label>
                <label>Status<select name="status" required><?php foreach (['Draf', 'Aktif', 'Selesai'] as $status): ?><option value="<?= h($status) ?>" <?= $formData['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach ?></select></label>
            </div>
        <?php endif ?>
        <div class="form-actions"><button class="btn" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Simpan data' ?></button><a class="btn btn-light" href="index.php?halaman=daftar&amp;entitas=<?= h($entity) ?>">Batal</a></div>
    </form>
<?php endif ?>
