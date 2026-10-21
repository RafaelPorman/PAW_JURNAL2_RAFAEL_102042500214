<?php
if ($id !== null && fetch_record($conn, $entity, $id) === null) {
    redirect('index.php?halaman=daftar&entitas=' . rawurlencode($entity), 'Data yang akan diubah tidak ditemukan.');
}

if ($entity === 'mata_kuliah') {
    $formData = [
        'kode' => strtoupper(input($_POST, 'kode')),
        'nama' => input($_POST, 'nama'),
        'dosen' => input($_POST, 'dosen'),
        'semester' => input($_POST, 'semester'),
    ];
} elseif ($entity === 'materi') {
    $formData = [
        'mata_kuliah_id' => input($_POST, 'mata_kuliah_id'),
        'judul' => input($_POST, 'judul'),
        'isi' => input($_POST, 'isi'),
        'tautan' => input($_POST, 'tautan'),
    ];
} else {
    $formData = [
        'mata_kuliah_id' => input($_POST, 'mata_kuliah_id'),
        'judul' => input($_POST, 'judul'),
        'instruksi' => input($_POST, 'instruksi'),
        'tenggat' => input($_POST, 'tenggat'),
        'status' => input($_POST, 'status'),
    ];
}

$errors = validate_record($conn, $entity, $formData, $id);
if ($errors === []) {
    try {
        $flash = save_record($conn, $entity, $formData, $id);
        if ($entity === 'mata_kuliah' && $flash === 'Kode mata kuliah sudah digunakan.') {
            $errors['kode'] = $flash;
        } else {
            redirect('index.php?halaman=daftar&entitas=' . rawurlencode($entity), $flash);
        }
    } catch (mysqli_sql_exception $exception) {
        error_log('Penyimpanan LMS gagal: ' . $exception->getMessage());
        $errors['save'] = $id === null
            ? 'Data gagal ditambahkan. Terjadi kesalahan saat menyimpan. Silakan coba lagi.'
            : 'Perubahan gagal disimpan. Terjadi kesalahan saat menyimpan. Silakan coba lagi.';
    }
}

if ($errors !== []) {
    $page = 'form';
}
