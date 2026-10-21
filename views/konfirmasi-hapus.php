<section class="page-heading"><div><p class="eyebrow">KONFIRMASI</p><h1>Hapus <?= h($entities[$entity]['label']) ?>?</h1><p class="muted">Tindakan ini tidak dapat dibatalkan.</p></div></section>
<section class="confirm-card">
    <p>Anda akan menghapus <strong><?= h($entity === 'mata_kuliah' ? $record['nama'] : $record['judul']) ?></strong>.</p>
    <?php if ($entity === 'mata_kuliah'): ?><p class="muted">Mata kuliah yang masih memiliki materi atau tugas tidak dapat dihapus.</p><?php endif ?>
    <form method="post" action="index.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="aksi" value="hapus">
        <input type="hidden" name="entitas" value="<?= h($entity) ?>">
        <input type="hidden" name="id" value="<?= (int) $record['id'] ?>">
        <div class="form-actions"><button class="btn btn-danger-fill" type="submit">Ya, hapus data</button><a class="btn btn-light" href="index.php?halaman=daftar&amp;entitas=<?= h($entity) ?>">Batal</a></div>
    </form>
</section>
