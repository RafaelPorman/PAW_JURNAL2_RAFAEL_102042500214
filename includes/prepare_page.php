<?php
if (!in_array($page, ['beranda', 'daftar', 'form', 'detail', 'konfirmasi-hapus'], true)) {
    http_response_code(404);
    exit('Halaman tidak ditemukan.');
}

if ($page !== 'beranda' && !entity_exists($entity)) {
    http_response_code(404);
    exit('Halaman atau jenis data tidak ditemukan.');
}

if ($page === 'form' && $idRaw !== '' && $id === null) {
    http_response_code(400);
    exit('ID harus berupa bilangan bulat positif.');
}

$title = $page === 'beranda' ? 'Beranda' : $entities[$entity]['plural'];
$counts = [];
$records = [];
$courses = [];
$record = null;
$search = input($_GET, 'cari');
$courseFilterRaw = input($_GET, 'mata_kuliah_id');
$courseFilter = $courseFilterRaw === '' ? 0 : valid_id($courseFilterRaw);

if ($page === 'beranda') {
    $counts = dashboard_counts($conn);
} elseif ($page === 'daftar') {
    $invalidCourseFilter = false;
    if ($entity === 'materi' || $entity === 'tugas') {
        $courses = course_options($conn);
        if ($courseFilterRaw !== '' && $courseFilter === null) {
            $errors['filter'] = 'Filter mata kuliah tidak valid.';
            $invalidCourseFilter = true;
        }
    }
    if (!$invalidCourseFilter) {
        $records = list_records($conn, $entity, $search, $courseFilter ?? 0);
    }
} else {
    if (($page === 'detail' || $page === 'konfirmasi-hapus' || ($page === 'form' && $id !== null)) && $id === null) {
        http_response_code(400);
        exit('ID harus berupa bilangan bulat positif.');
    }

    if ($id !== null) {
        $record = fetch_record($conn, $entity, $id);
        if ($record === null) {
            http_response_code(404);
            exit('Data tidak ditemukan.');
        }
    }

    if ($page === 'form') {
        $title = ($id === null ? 'Tambah ' : 'Edit ') . $entities[$entity]['label'];
        if ($entity === 'materi' || $entity === 'tugas') {
            $courses = course_options($conn);
        }
        if ($formData === []) {
            if ($entity === 'mata_kuliah') {
                $formData = $record ?? ['kode' => '', 'nama' => '', 'dosen' => '', 'semester' => ''];
            } elseif ($entity === 'materi') {
                $formData = $record ?? ['mata_kuliah_id' => '', 'judul' => '', 'isi' => '', 'tautan' => ''];
            } else {
                $formData = $record ?? ['mata_kuliah_id' => '', 'judul' => '', 'instruksi' => '', 'tenggat' => '', 'status' => 'Draf'];
            }
        }
    }
}
