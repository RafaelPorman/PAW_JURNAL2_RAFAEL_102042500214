<?php
if ($id === null) {
    http_response_code(400);
    exit('ID data yang akan dihapus tidak valid.');
}

try {
    $deleted = delete_record($conn, $entity, $id);
} catch (mysqli_sql_exception $exception) {
    if ($exception->getCode() === 1451 && $entity === 'mata_kuliah') {
        redirect('index.php?halaman=daftar&entitas=mata_kuliah', 'Mata kuliah tidak dapat dihapus karena masih memiliki materi atau tugas.');
    }

    throw $exception;
}

redirect(
    'index.php?halaman=daftar&entitas=' . rawurlencode($entity),
    $deleted ? 'Data berhasil dihapus.' : 'Data tidak ditemukan; tidak ada yang dihapus.'
);
