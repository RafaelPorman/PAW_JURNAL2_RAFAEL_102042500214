<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$database = 'lms_paw';

try {
    $conn = new mysqli($host, $user, $pass, $database, $port);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log('Koneksi LMS gagal: ' . $exception->getMessage());
    http_response_code(503);
    exit('Koneksi database LMS gagal. Pastikan MySQL aktif, skema_lms.sql sudah diimpor, dan pengaturan di koneksi.php sudah benar.');
}
