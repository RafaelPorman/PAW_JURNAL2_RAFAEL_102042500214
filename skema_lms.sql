CREATE DATABASE IF NOT EXISTS lms_paw
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE lms_paw;

CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL,
    CHECK (semester BETWEEN 1 AND 14)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) NOT NULL,
    CONSTRAINT fk_materi_mata_kuliah
        FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    CONSTRAINT fk_tugas_mata_kuliah
        FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CHECK (status IN ('Draf', 'Aktif', 'Selesai'))
) ENGINE=InnoDB;

INSERT INTO mata_kuliah (kode, nama, dosen, semester)
SELECT 'PAW', 'Pengembangan Aplikasi Website', 'Dosen PAW', 3
WHERE NOT EXISTS (SELECT 1 FROM mata_kuliah WHERE kode = 'PAW');

INSERT INTO mata_kuliah (kode, nama, dosen, semester)
SELECT 'BASDAT', 'Basis Data', 'Dosen Basis Data', 3
WHERE NOT EXISTS (SELECT 1 FROM mata_kuliah WHERE kode = 'BASDAT');

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan)
SELECT id, 'Dasar PHP', 'Pengenalan sintaks PHP, variabel, dan pemrosesan form.', 'https://www.php.net/manual/'
FROM mata_kuliah WHERE kode = 'PAW'
AND NOT EXISTS (SELECT 1 FROM materi WHERE judul = 'Dasar PHP');

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan)
SELECT id, 'Pengantar Basis Data', 'Konsep tabel, relasi, dan query SQL dasar.', 'https://dev.mysql.com/doc/'
FROM mata_kuliah WHERE kode = 'BASDAT'
AND NOT EXISTS (SELECT 1 FROM materi WHERE judul = 'Pengantar Basis Data');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status)
SELECT id, 'Latihan CRUD', 'Buat aplikasi sederhana untuk menambah, melihat, mengubah, dan menghapus data.', '2026-12-31', 'Aktif'
FROM mata_kuliah WHERE kode = 'PAW'
AND NOT EXISTS (SELECT 1 FROM tugas WHERE judul = 'Latihan CRUD');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status)
SELECT id, 'Latihan Query SQL', 'Buat query untuk membaca dan mengolah data dari beberapa tabel.', '2026-12-31', 'Draf'
FROM mata_kuliah WHERE kode = 'BASDAT'
AND NOT EXISTS (SELECT 1 FROM tugas WHERE judul = 'Latihan Query SQL');
