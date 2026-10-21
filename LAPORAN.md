# Laporan Jurnal Praktikum PAW — Modul 2

## Identitas

- Nama: Rafael Porman
- NIM: 102042500214
- Mata kuliah: Pengembangan Aplikasi Website

## Struktur project

| Berkas | Keterangan |
| --- | --- |
| `index.php` | Beranda, daftar, detail, form tambah/edit, konfirmasi dan pemrosesan hapus. |
| `functions.php` | Validasi, CSRF, query prepared statement, dan fungsi database yang digunakan halaman. |
| `koneksi.php` | Koneksi MySQL/MariaDB; sesuaikan host, port, user, dan password jika berbeda. |
| `skema_lms.sql` | Pembuatan database/tabel dan dua data awal untuk setiap jenis data. |
| `styles.css` | Tampilan responsif aplikasi. |
| `README.md` | Petunjuk instalasi dan ringkasan fitur. |

## Database dan hubungan data

Database `lms_paw` memiliki tabel `mata_kuliah`, `materi`, dan `tugas`. Satu mata kuliah dapat memiliki banyak materi dan tugas. Kolom `materi.mata_kuliah_id` dan `tugas.mata_kuliah_id` menjadi foreign key ke `mata_kuliah.id` dengan aturan `ON DELETE RESTRICT`, sehingga mata kuliah yang masih digunakan tidak bisa dihapus.

## Alur aplikasi dan CRUD

Beranda mengambil ringkasan menggunakan `COUNT(*)`. Menu navigasi membuka daftar mata kuliah, materi, atau tugas. Setiap daftar menyediakan pencarian; daftar materi dan tugas juga menyediakan filter mata kuliah. Detail materi dan tugas menampilkan nama mata kuliah terkait.

Form tambah dan edit memvalidasi seluruh nilai di server sebelum menyimpan dengan prepared statement. Edit memperbarui baris berdasarkan ID yang sudah divalidasi dan memberi pesan khusus jika tidak ada perubahan. Penghapusan menampilkan halaman konfirmasi terlebih dahulu dan hanya memproses permintaan POST dengan token CSRF yang valid. Foreign key tetap melindungi relasi ketika mata kuliah masih memiliki data terkait.

## Hasil pengujian lokal

Pengujian dilakukan melalui Apache dan MySQL lokal XAMPP.

| Pengujian | Hasil |
| --- | --- |
| Membuka beranda dan membaca jumlah data awal | Lulus; jumlah mata kuliah, materi, dan tugas masing-masing 2. |
| Mencari materi dengan kata kunci sekaligus filter mata kuliah | Lulus; hasil menampilkan materi dan nama mata kuliah yang sesuai. |
| Menambah mata kuliah, materi, dan tugas | Lulus; ketiganya tersimpan di database. |
| Menyimpan materi dengan URL `javascript:` | Ditolak; materi tidak tersimpan. |
| Menghapus mata kuliah yang masih digunakan | Ditolak oleh foreign key dan aplikasi menampilkan pesan yang jelas. |
| Menghapus materi/tugas lalu mata kuliah uji | Lulus; penghapusan dilakukan melalui POST setelah konfirmasi. |
| Mengirim kode mata kuliah duplikat | Ditolak. |
| Menyimpan edit tanpa perubahan | Lulus; aplikasi menampilkan pesan bahwa tidak ada perubahan. |
| Mengirim ID nonangka pada form edit/detail | Ditolak dengan HTTP 400. |
| Mengirim POST tanpa token CSRF | Ditolak dengan HTTP 403; data tidak berubah. |
| Pemeriksaan sintaks berkas PHP | Lulus; semua berkas tidak memiliki syntax error. |
| Kondisi database setelah pengujian | Data uji dibersihkan; dua data awal pada tiap tabel tetap tersedia. |

## Bukti screenshot

Tambahkan screenshot fullscreen dari project yang dijalankan sendiri. Pastikan tanggal dan waktu terlihat, dan jangan mengubah isi screenshot.

- [ ] Beranda dan ringkasan database.
- [ ] Daftar serta pencarian/filter.
- [ ] Form tambah/edit dan hasil validasi.
- [ ] Konfirmasi penghapusan dan pesan hasil pengujian.

## Kendala dan penyelesaian

Database perlu dibuat dan diisi sebelum aplikasi dapat digunakan. Selesaikan dengan menyalakan MySQL di XAMPP, mengimpor `skema_lms.sql` melalui phpMyAdmin, lalu memastikan pengaturan koneksi di `koneksi.php` sesuai.

## Repository GitHub

 URL repository project : 
