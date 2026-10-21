# LMS EAD

Aplikasi LMS sederhana untuk mengelola mata kuliah, materi, dan tugas menggunakan PHP, MySQL/MariaDB, HTML, dan CSS tanpa framework.

## Menjalankan aplikasi

1. Nyalakan Apache dan MySQL dari XAMPP.
2. Buka phpMyAdmin, lalu impor `skema_lms.sql`. Skrip membuat database `lms_paw`, tiga tabel, serta dua data awal untuk setiap jenis data.
3. Jika kredensial atau port MySQL berbeda, sesuaikan variabel koneksi di `koneksi.php`.
4. Letakkan folder project di dalam `htdocs`, lalu buka `http://localhost/PAW_JURNAL2_RAFAEL_102042500214/`.

## Fitur

- Ringkasan jumlah data dihitung langsung dengan `COUNT(*)`.
- CRUD mata kuliah, materi, dan tugas; materi/tugas terhubung ke satu mata kuliah.
- Tombol tambah membuka formulir sesuai jenis data; aplikasi menampilkan konfirmasi setelah penyimpanan berhasil dan pesan kesalahan jika validasi atau penyimpanan gagal.
- Validasi input di server, prepared statements, escaping HTML, dan token CSRF untuk permintaan perubahan data.
- Penghapusan hanya melalui POST setelah halaman konfirmasi; mata kuliah yang masih digunakan dilindungi foreign key `ON DELETE RESTRICT`.
- Pencarian mata kuliah berdasarkan kode/nama serta pencarian judul dan filter mata kuliah untuk materi/tugas.
- Data awal dan perubahan tersimpan di MySQL/MariaDB.

## Struktur file

- `index.php` menjadi front controller yang menghubungkan proses aplikasi.
- `includes/bootstrap.php` menyiapkan koneksi, session, dan data permintaan; `includes/prepare_page.php` memvalidasi rute serta mengambil data untuk halaman.
- `actions/process.php` mengarahkan permintaan POST ke `actions/simpan.php` atau `actions/hapus.php`.
- `functions.php` berisi fungsi database dan validasi.
- `views/layout.php` berisi kerangka halaman bersama.
- `views/beranda.php`, `views/daftar.php`, `views/form.php`, `views/detail.php`, dan `views/konfirmasi-hapus.php` berisi tampilan untuk setiap halaman.
- `styles.css` mengatur tampilan aplikasi.
