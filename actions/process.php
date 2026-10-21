<?php
if (!verify_csrf_token(input($_POST, 'csrf_token'))) {
    http_response_code(403);
    exit('Permintaan ditolak karena token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
}

$entity = input($_POST, 'entitas');
if (!entity_exists($entity)) {
    http_response_code(400);
    exit('Jenis data tidak valid.');
}

$action = input($_POST, 'aksi');
$postId = input($_POST, 'id');
$id = $postId === '' ? null : valid_id($postId);
if ($postId !== '' && $id === null) {
    http_response_code(400);
    exit('ID data tidak valid.');
}

if ($action === 'hapus') {
    require __DIR__ . '/hapus.php';
} elseif ($action === 'simpan') {
    require __DIR__ . '/simpan.php';
} else {
    http_response_code(400);
    exit('Tindakan tidak valid.');
}
