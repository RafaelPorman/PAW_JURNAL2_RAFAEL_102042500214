<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function input(array $source, string $key): string
{
    return isset($source[$key]) && is_string($source[$key])
        ? trim($source[$key])
        : '';
}

function entities(): array
{
    return [
        'mata_kuliah' => ['label' => 'Mata Kuliah', 'plural' => 'Mata Kuliah'],
        'materi' => ['label' => 'Materi', 'plural' => 'Materi'],
        'tugas' => ['label' => 'Tugas', 'plural' => 'Tugas'],
    ];
}

function entity_exists(string $entity): bool
{
    return array_key_exists($entity, entities());
}

function valid_id(string $value): ?int
{
    if ($value === '' || !ctype_digit($value) || (int) $value < 1) {
        return null;
    }

    return (int) $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && $token !== ''
        && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect(string $url, string $message = ''): void
{
    if ($message !== '') {
        $_SESSION['flash_message'] = $message;
    }

    header('Location: ' . $url);
    exit;
}

function fetch_record(mysqli $conn, string $entity, int $id): ?array
{
    if ($entity === 'mata_kuliah') {
        $stmt = $conn->prepare('SELECT id, kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?');
    } elseif ($entity === 'materi') {
        $stmt = $conn->prepare(
            'SELECT m.id, m.mata_kuliah_id, m.judul, m.isi, m.tautan, mk.nama AS mata_kuliah_nama
             FROM materi m JOIN mata_kuliah mk ON mk.id = m.mata_kuliah_id WHERE m.id = ?'
        );
    } else {
        $stmt = $conn->prepare(
            'SELECT t.id, t.mata_kuliah_id, t.judul, t.instruksi, t.tenggat, t.status, mk.nama AS mata_kuliah_nama
             FROM tugas t JOIN mata_kuliah mk ON mk.id = t.mata_kuliah_id WHERE t.id = ?'
        );
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $record ?: null;
}

function course_options(mysqli $conn): array
{
    $result = $conn->query('SELECT id, kode, nama FROM mata_kuliah ORDER BY nama');
    return $result->fetch_all(MYSQLI_ASSOC);
}

function course_exists(mysqli $conn, int $id): bool
{
    $stmt = $conn->prepare('SELECT id FROM mata_kuliah WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows === 1;
    $stmt->close();

    return $exists;
}

function validate_record(mysqli $conn, string $entity, array $data, ?int $currentId = null): array
{
    $errors = [];
    $length = static fn (string $value): int => mb_strlen($value, 'UTF-8');

    if ($entity === 'mata_kuliah') {
        if (!preg_match('/^[A-Z0-9-]{3,20}$/', $data['kode'])) {
            $errors['kode'] = 'Kode harus 3-20 karakter, berupa huruf kapital, angka, atau tanda -.';
        } else {
            $stmt = $conn->prepare('SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?');
            $selfId = $currentId ?? 0;
            $stmt->bind_param('si', $data['kode'], $selfId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors['kode'] = 'Kode mata kuliah sudah digunakan.';
            }
            $stmt->close();
        }

        foreach (['nama' => 'Nama', 'dosen' => 'Dosen'] as $field => $label) {
            if ($length($data[$field]) < 3 || $length($data[$field]) > 100) {
                $errors[$field] = $label . ' harus berisi 3-100 karakter.';
            }
        }

        if ($data['semester'] === '' || !ctype_digit($data['semester'])
            || (int) $data['semester'] < 1 || (int) $data['semester'] > 14) {
            $errors['semester'] = 'Semester harus berupa bilangan bulat 1-14.';
        }
    } elseif ($entity === 'materi' || $entity === 'tugas') {
        if ($data['mata_kuliah_id'] === '' || !ctype_digit($data['mata_kuliah_id'])
            || !course_exists($conn, (int) $data['mata_kuliah_id'])) {
            $errors['mata_kuliah_id'] = 'Pilih mata kuliah yang tersedia.';
        }

        if ($length($data['judul']) < 3 || $length($data['judul']) > 150) {
            $errors['judul'] = 'Judul harus berisi 3-150 karakter.';
        }

        $longField = $entity === 'materi' ? 'isi' : 'instruksi';
        $longLabel = $entity === 'materi' ? 'Isi materi' : 'Instruksi';
        if ($length($data[$longField]) < 3 || $length($data[$longField]) > 2000) {
            $errors[$longField] = $longLabel . ' harus berisi 3-2000 karakter.';
        }

        if ($entity === 'materi') {
            if ($length($data['tautan']) > 255
                || !preg_match('~^https?://~i', $data['tautan'])
                || filter_var($data['tautan'], FILTER_VALIDATE_URL) === false) {
                $errors['tautan'] = 'Tautan maksimal 255 karakter dan harus berupa URL valid yang diawali http:// atau https://.';
            }
        } else {
            $date = DateTime::createFromFormat('!Y-m-d', $data['tenggat']);
            $dateErrors = DateTime::getLastErrors();
            if (!$date
                || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
                || $date->format('Y-m-d') !== $data['tenggat']) {
                $errors['tenggat'] = 'Masukkan tanggal tenggat yang valid.';
            }

            if (!in_array($data['status'], ['Draf', 'Aktif', 'Selesai'], true)) {
                $errors['status'] = 'Pilih status Draf, Aktif, atau Selesai.';
            }
        }
    }

    return $errors;
}

function save_record(mysqli $conn, string $entity, array $data, ?int $id): string
{
    if ($id !== null) {
        $existing = fetch_record($conn, $entity, $id);
        if ($existing === null) {
            return 'Data yang akan diubah tidak ditemukan.';
        }

        $fields = $entity === 'mata_kuliah'
            ? ['kode', 'nama', 'dosen', 'semester']
            : ($entity === 'materi'
                ? ['mata_kuliah_id', 'judul', 'isi', 'tautan']
                : ['mata_kuliah_id', 'judul', 'instruksi', 'tenggat', 'status']);
        $unchanged = true;
        foreach ($fields as $field) {
            if ((string) $existing[$field] !== (string) $data[$field]) {
                $unchanged = false;
                break;
            }
        }
        if ($unchanged) {
            return 'Tidak ada perubahan; data tetap tersimpan.';
        }
    }

    try {
        if ($entity === 'mata_kuliah') {
            $semester = (int) $data['semester'];
            if ($id === null) {
                $stmt = $conn->prepare('INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('sssi', $data['kode'], $data['nama'], $data['dosen'], $semester);
            } else {
                $stmt = $conn->prepare('UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?');
                $stmt->bind_param('sssii', $data['kode'], $data['nama'], $data['dosen'], $semester, $id);
            }
        } elseif ($entity === 'materi') {
            $courseId = (int) $data['mata_kuliah_id'];
            if ($id === null) {
                $stmt = $conn->prepare('INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('isss', $courseId, $data['judul'], $data['isi'], $data['tautan']);
            } else {
                $stmt = $conn->prepare('UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?');
                $stmt->bind_param('isssi', $courseId, $data['judul'], $data['isi'], $data['tautan'], $id);
            }
        } else {
            $courseId = (int) $data['mata_kuliah_id'];
            if ($id === null) {
                $stmt = $conn->prepare('INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('issss', $courseId, $data['judul'], $data['instruksi'], $data['tenggat'], $data['status']);
            } else {
                $stmt = $conn->prepare('UPDATE tugas SET mata_kuliah_id = ?, judul = ?, instruksi = ?, tenggat = ?, status = ? WHERE id = ?');
                $stmt->bind_param('issssi', $courseId, $data['judul'], $data['instruksi'], $data['tenggat'], $data['status'], $id);
            }
        }

        $stmt->execute();
        $stmt->close();
    } catch (mysqli_sql_exception $exception) {
        if ($exception->getCode() === 1062 && $entity === 'mata_kuliah') {
            return 'Kode mata kuliah sudah digunakan.';
        }

        throw $exception;
    }

    return $id === null ? 'Data berhasil ditambahkan.' : 'Perubahan berhasil disimpan.';
}

function delete_record(mysqli $conn, string $entity, int $id): bool
{
    if ($entity === 'mata_kuliah') {
        $stmt = $conn->prepare('DELETE FROM mata_kuliah WHERE id = ?');
    } elseif ($entity === 'materi') {
        $stmt = $conn->prepare('DELETE FROM materi WHERE id = ?');
    } else {
        $stmt = $conn->prepare('DELETE FROM tugas WHERE id = ?');
    }

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $deleted = $stmt->affected_rows === 1;
    $stmt->close();

    return $deleted;
}

function list_records(mysqli $conn, string $entity, string $search, int $courseFilter): array
{
    $keyword = '%' . $search . '%';
    if ($entity === 'mata_kuliah') {
        $stmt = $conn->prepare(
            "SELECT id, kode, nama, dosen, semester FROM mata_kuliah
             WHERE ? = '' OR kode LIKE ? OR nama LIKE ? ORDER BY nama"
        );
        $stmt->bind_param('sss', $search, $keyword, $keyword);
    } elseif ($entity === 'materi') {
        $stmt = $conn->prepare(
            "SELECT m.id, m.mata_kuliah_id, m.judul, m.isi, m.tautan, mk.nama AS mata_kuliah_nama
             FROM materi m JOIN mata_kuliah mk ON mk.id = m.mata_kuliah_id
             WHERE (? = '' OR m.judul LIKE ?) AND (? = 0 OR m.mata_kuliah_id = ?)
             ORDER BY m.id DESC"
        );
        $stmt->bind_param('ssii', $search, $keyword, $courseFilter, $courseFilter);
    } else {
        $stmt = $conn->prepare(
            "SELECT t.id, t.mata_kuliah_id, t.judul, t.instruksi, t.tenggat, t.status, mk.nama AS mata_kuliah_nama
             FROM tugas t JOIN mata_kuliah mk ON mk.id = t.mata_kuliah_id
             WHERE (? = '' OR t.judul LIKE ?) AND (? = 0 OR t.mata_kuliah_id = ?)
             ORDER BY t.tenggat, t.id DESC"
        );
        $stmt->bind_param('ssii', $search, $keyword, $courseFilter, $courseFilter);
    }

    $stmt->execute();
    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $records;
}

function dashboard_counts(mysqli $conn): array
{
    $counts = [];
    foreach (['mata_kuliah', 'materi', 'tugas'] as $table) {
        $result = $conn->query('SELECT COUNT(*) AS jumlah FROM ' . $table);
        $counts[$table] = (int) $result->fetch_assoc()['jumlah'];
    }

    return $counts;
}
