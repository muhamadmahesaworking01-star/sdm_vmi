# Panduan Penggunaan Proyek SI SDM (contoh1)

Dokumen ini menjelaskan cara menyiapkan, menjalankan, dan menggunakan proyek SI SDM pada workspace ini.

---

## 1. Prasyarat
- PHP 8.1+ (direkomendasikan sesuai `composer.json`)
- Composer
- Node.js + npm / pnpm
- MySQL 5.7+ / MariaDB atau MySQL 8.x
- Git
- (Opsional) Laragon/XAMPP untuk lingkungan lokal di Windows

## 2. Struktur singkat proyek
- `app/` — kode aplikasi (Models, Controllers, Middleware)
- `database/migrations/` — migration skema DB
- `database/seeders/` — seeder (jika ada)
- `routes/web.php` — definisi rute
- `resources/views/` — blade templates
- `public/` — entry point web
- `docs/` — dokumentasi proyek (ERD, DFD, user guide, dll.)

Lihat juga: [docs/README.md](docs/README.md).

## 3. Instalasi (lokal)
Jalankan perintah di folder proyek (`c:\laragon\www\contoh1`):

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies (untuk asset)
npm install
# atau
# pnpm install

# 3. Copy file env
cp .env.example .env
# Windows PowerShell
# copy .env.example .env

# 4. Generate app key
php artisan key:generate
```

## 4. Konfigurasi database
Buka file `.env` dan sesuaikan koneksi database. Contoh pada workspace ini:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cthdatabase
DB_USERNAME=root
DB_PASSWORD=
```

Ada dua opsi untuk mengisi skema dan data:

A. Import SQL dump yang sudah disediakan (`cthdatabase.sql`):

```bash
# buat database kosong lalu import (PowerShell)
# gunakan mysql client yang ada di PATH
mysql -u root -p -e "CREATE DATABASE cthdatabase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Get-Content "C:/Users/vilme/Downloads/cthdatabase.sql" | mysql -u root -p cthdatabase
```

B. Jalankan migration (jika ingin membangun dari migration Laravel):

```bash
php artisan migrate
# jika ada seeder
php artisan db:seed
```

> Catatan: Proyek saat ini menggunakan `cthdatabase` di `.env` dan repository berisi dump SQL yang telah diimpor pada lingkungan pengembangan (lihat file `cthdatabase.sql`).

## 5. Menjalankan aplikasi (development)

```bash
# jalankan server dev Laravel
php artisan serve
# akses: http://127.0.0.1:8000

# jalankan Vite (assets)
npm run dev
```

## 6. Akun & Role
Proyek memiliki role: `super_admin`, `direksi`, `karyawan`, `pengajar`, `karyawan_pengajar`.
Beberapa akun contoh sudah ada di dump DB (`users`):
- superadmin@example.com (role: super_admin)
- karyawan@example.com (role: karyawan)
- pengajar@example.com (role: pengajar)
- direksi@example.com (role: direksi)
- fizi0123@gmail.com (role: karyawan_pengajar)

Reset password dapat dilakukan melalui fitur reset atau admin.

## 7. Alur penggunaan fungsi utama
- Login: `/login` (route didefinisikan di `routes/web.php`)
- Admin (super_admin): `/admin/dashboard` + manajemen users, employees, specializations
- Direksi: `/direksi/dashboard` (akses read-only)
- Karyawan: `/employee/dashboard` + edit profil di `/employee/profile/edit`
- Pengajar: `/teacher/dashboard` + edit profil di `/teacher/profile/edit`
- Double Role: `/double-role/dashboard` dan halaman profil admin/academic

## 8. Database: tabel penting dan fungsinya
Lihat [docs/database-report.md](docs/database-report.md) untuk daftar tabel, relasi logis, dan rekomendasi.

## 9. Migrasi skema (menambahkan foreign keys — rekomendasi)
Jika Anda ingin menegakkan relasi di DB, buat migration baru yang menambahkan FK setelah memeriksa data existing. Contoh skeleton migration:

```php
// contoh file migration: add_foreign_keys.php
public function up()
{
    Schema::table('sessions', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    });

    Schema::table('employees', function (Blueprint $table) {
        $table->foreign('id_atasan')->references('id')->on('employees')->nullOnDelete();
    });
}
```

Jalankan:

```bash
php artisan make:migration add_foreign_keys --table=sessions
php artisan migrate
```

> HATI-HATI: Pastikan nilai kolom yang menjadi referensi konsisten sebelum menambahkan FK.

## 10. Testing
Proyek berisi beberapa test di folder `tests/`. Jalankan:

```bash
php artisan test
# atau
vendor/bin/phpunit
```

## 11. Debug & troubleshooting cepat
- Periksa log: `storage/logs/laravel.log`
- Periksa environment: `php artisan env`
- Permasalahan migrasi: rollback lalu cek data

## 12. Deploy singkat
- Set `APP_ENV=production` dan `APP_DEBUG=false`
- Jalankan `composer install --no-dev --optimize-autoloader`
- Build assets: `npm run build`
- Set file permission pada `storage` dan `bootstrap/cache`

## 13. Skrip otomatisasi setup
Untuk mempercepat setup di Windows, gunakan skrip berikut:

- [scripts/setup-project.ps1](scripts/setup-project.ps1)
- [scripts/setup-project.bat](scripts/setup-project.bat)

Cara pakai:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup-project.ps1
```

Atau:

```bat
scripts\setup-project.bat
```

## 14. Placeholder screenshot
Berikut gambar referensi yang dapat dipakai saat membuat dokumentasi Word atau presentasi:

- [docs/screenshots/login-page.svg](docs/screenshots/login-page.svg)
- [docs/screenshots/dashboard-overview.svg](docs/screenshots/dashboard-overview.svg)
- [docs/screenshots/admin-panel.svg](docs/screenshots/admin-panel.svg)

## 15. Rekomendasi export ke Word
Untuk membuat versi Word, Anda bisa:
1. Buka dokumen ini di editor yang mendukung export markdown ke DOCX.
2. Lampirkan screenshot placeholder dari folder [docs/screenshots](docs/screenshots).
3. Simpan hasilnya dengan nama `Panduan-Penggunaan-SI-SDM.docx`.

---

File panduan ini telah diperluas dengan tutorial langkah demi langkah, placeholder screenshot, dan skrip otomatisasi setup.

