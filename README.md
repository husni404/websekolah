## SMK Madani Websekolah (Gen Z Vibes)

Portal web SMK dengan tampilan neobrutal/glassmorphism, landing estetik untuk siswa/orang tua, plus admin dashboard untuk kelola data dan fitur unggulan **Import Excel siswa**.

### 1. Kebutuhan

- PHP **8.1+** (XAMPP terbaru).
- MySQL / MariaDB (bawaan XAMPP).
- Composer (untuk install PhpSpreadsheet).

### 2. Setup Database

1. Buka `phpMyAdmin`.
2. Import file:
   - `database/schema.sql`
   - (opsional, tapi disarankan) `database/alter_add_sekolah_settings.sql` untuk tabel identitas sekolah.
3. Ini akan membuat database `smk_madani` dengan tabel:
   - `users`, `siswa`, `guru`, `kelas`, `konten`.
   - `sekolah_settings` (identitas sekolah, logo, lokasi).

### 3. Install Dependency (PhpSpreadsheet)

Di terminal pada folder project `websekolah`:

```bash
cd C:\xampp\htdocs\websekolah
composer install
```

Composer akan menginstall:

- `phpoffice/phpspreadsheet` (untuk baca/tulis Excel).

### 4. Seed Akun Admin

Jalankan script seeder (sekali saja) untuk membuat akun admin default:

```bash
cd C:\xampp\htdocs\websekolah
php app/scripts/seed_admin.php
```

Output akan menampilkan:

- Username: `admin`
- Password: `admin123`

Setelah login pertama, **segera ganti password** lewat manajemen user (bisa ditambah nanti).

### 5. Akses Website

- Landing page:
  - `http://localhost/websekolah/`
- Login admin:
  - `http://localhost/websekolah/admin/login`

Setelah login berhasil:

- Dashboard: `http://localhost/websekolah/admin/dashboard`
- Data Siswa: `http://localhost/websekolah/admin/siswa`
- Import Excel Siswa: `http://localhost/websekolah/admin/import/siswa`
- Identitas Sekolah: `http://localhost/websekolah/admin/sekolah`

### 6. Flow Import Excel Siswa

1. Di admin, buka menu **Import Excel**.
2. Klik **Download Template**:
   - Sistem membuat file `template_siswa_YYYYMMDD_HHMMSS.xlsx` berisi kolom:
     - `nisn`, `nama`, `id_kelas`, `alamat`.
3. Isi data siswa pada template:
   - `nisn` dan `nama` **wajib**.
   - `id_kelas` opsional (isi dengan ID dari tabel `kelas`).
   - `alamat` opsional.
4. Upload kembali file Excel di halaman Import.
5. Sistem akan:
   - Menolak baris yang NISN / Nama kosong.
   - Mengecek NISN **duplikat di dalam file**.
   - Mengecek NISN yang **sudah ada di database**.
   - Jika ada NISN duplikat → import dibatalkan, pesan error akan berisi daftar NISN yang bermasalah.
   - Jika semua aman → data siswa di-insert ke tabel `siswa` menggunakan **transaksi** (tidak ada kasus setengah sukses).

### 7. Struktur Folder Singkat

- `index.php` → front controller + router sederhana.
- `views/landing.php` → landing page neobrutal/glass untuk publik.
- `views/admin/...` → halaman login, dashboard, siswa, guru, kelas, konten, identitas sekolah, import.
- `app/init.php` → bootstrap (session, config, helper, PDO).
- `app/auth.php` → autentikasi admin (login/logout, guard).
- `app/import_siswa.php` → logika baca Excel dan import siswa.
- `database/schema.sql` → definisi tabel MySQL.
- `database/alter_add_sekolah_settings.sql` → tabel identitas sekolah (opsional, ada auto-create di admin juga).
- `storage/uploads/` → lokasi sementara file upload Excel.
- `storage/sekolah/` → penyimpanan logo sekolah.
- `storage/konten/` → penyimpanan media konten/mading.

### 8. Catatan Pengembangan

- Style: Tailwind via CDN (bisa diganti ke build lokal kalau diperlukan).
- Security dasar:
  - Prepared statement (PDO) untuk query.
  - CSRF token di semua form penting (login, CRUD, import).
  - Guard `require_admin()` untuk semua route `/admin/*` kecuali login.

### 9. Siap Upload ke Hosting

- File `.htaccess` sudah menggunakan `RewriteBase /` sehingga bisa dipasang di root domain seperti `smkmadanicianjur.sch.id`.
- Pastikan di hosting:
  - PHP versi **8.1+** dan ekstensi `pdo_mysql` aktif.
  - Import `database/schema.sql` dan `database/alter_add_sekolah_settings.sql` ke database MySQL hosting.
  - Sesuaikan kredensial DB di `config/config.php` (host, nama DB, user, password).
  - Folder `storage/*` memiliki permission tulis (untuk upload logo dan konten).

