# API dan Endpoint Internal

Aplikasi ini belum memiliki `routes/api.php` atau API publik berbasis token. Endpoint berikut terdaftar pada `routes/web.php`, menggunakan session Laravel dan CSRF. Endpoint yang mengembalikan JSON ditandai **JSON**; endpoint lain umumnya mengembalikan halaman, redirect, file, atau download.

## Autentikasi

| Method | Endpoint | Akses | Fungsi |
|---|---|---|---|
| GET | `/login` | guest | Menampilkan form login |
| POST | `/login` | guest | Memproses login |
| POST | `/logout` | authenticated | Mengakhiri sesi |
| POST | `/impersonation/stop` | authenticated | Menghentikan impersonasi pengguna |

## Super Admin

Semua endpoint di bagian ini memerlukan `auth` dan `role:super_admin`.

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/admin/dashboard` | Dashboard admin |
| GET/PUT | `/admin/profile` | Melihat dan memperbarui profil admin |
| GET | `/admin/activity-logs` | Melihat log aktivitas |
| GET | `/admin/backup` | Mengunduh backup ZIP berisi JSON |
| GET/POST | `/admin/users`, `/admin/users/create` | Daftar, form, dan tambah pengguna |
| PATCH | `/admin/users/{user}` | Memperbarui pengguna |
| PATCH | `/admin/users/{user}/role` | Mengubah role pengguna |
| PATCH | `/admin/users/{user}/password` | Mereset password pengguna |
| PATCH | `/admin/users/{user}/suspend` | Mengaktifkan atau menangguhkan pengguna |
| POST | `/admin/users/{user}/impersonate` | Masuk sebagai pengguna lain |
| DELETE | `/admin/users/{user}` | Menghapus pengguna |
| GET/POST | `/admin/employees`, `/admin/employees/create` | Daftar, form, dan tambah karyawan |
| DELETE | `/admin/employees/{employee}` | Menghapus karyawan |
| PATCH | `/admin/employees/{employee}/status` | Mengubah status karyawan |
| GET | `/admin/employees/template` | Mengunduh template impor karyawan |
| GET | `/admin/employees/export` | Mengekspor data karyawan |
| POST | `/admin/employees/import` | Mengimpor data karyawan |
| GET/POST | `/admin/teachers`, `/admin/teachers/create` | Daftar, form, dan tambah pengajar |
| DELETE | `/admin/teachers/{teacher}` | Menghapus pengajar |
| GET | `/admin/teachers/template` | Mengunduh template impor pengajar |
| GET | `/admin/teachers/export` | Mengekspor data pengajar |
| POST | `/admin/teachers/import` | Mengimpor data pengajar |
| GET/POST/DELETE | `/admin/specializations[/{specialization}]` | Mengelola spesialisasi |
| GET/POST/DELETE | `/admin/announcements[/{announcement}]` | Mengelola pengumuman |
| POST | `/admin/employees/{employee}/documents` | Mengunggah dokumen pegawai |
| GET | `/admin/documents/{document}/file` | Mengunduh atau menampilkan dokumen |

### Monitoring kontrak

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/admin/contracts` | Halaman ringkasan kontrak |
| GET | `/admin/contracts/data` | Halaman data kontrak |
| GET | `/admin/contracts/monitoring` | Halaman monitoring kontrak |
| GET | `/admin/contracts/expiring` | Daftar kontrak yang segera berakhir |
| GET | `/admin/contracts/history` | Riwayat kontrak |
| GET | `/admin/contracts/{nip}` | Detail kontrak pegawai |
| POST | `/admin/contracts/{nip}/extend` | Menambah perpanjangan kontrak |
| PUT | `/admin/contracts/history/{contract}` | Memperbarui perpanjangan kontrak |
| DELETE | `/admin/contracts/history/{contract}` | Membatalkan perpanjangan kontrak |
| GET | `/admin/contracts/export` | Mengekspor seluruh kontrak |
| GET | `/admin/contracts/{nip}/export` | Mengekspor kontrak satu pegawai |
| GET | `/admin/contracts/api/admin` | **JSON** data kontrak lengkap untuk admin |
| GET | `/admin/contracts/api/direksi` | **JSON** ringkasan indikator kontrak untuk direksi |
| GET | `/admin/contracts/api/me` | **JSON** kontrak pengguna yang sedang login |

### Laporan direksi

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/admin/director/dashboard` | Dashboard monitoring direksi |
| GET/POST | `/admin/director/report-request` | Form dan pengajuan permintaan laporan |
| GET | `/admin/director/report-history` | Riwayat permintaan laporan |
| GET | `/admin/director/report/{report}/download` | Mengunduh laporan |
| DELETE | `/admin/director/report/{report}` | Menghapus laporan |

## Direksi

Semua endpoint memerlukan `auth` dan `role:direksi`. Endpoint ini bersifat read-only kecuali pembaruan profil.

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/direksi/dashboard` | Dashboard direksi |
| GET/PUT | `/direksi/profile`, `/direksi/profile/view` | Melihat dan memperbarui profil |
| GET | `/direksi/contracts` | Monitoring kontrak dan kelengkapan dokumen |
| GET | `/direksi/employees` | Melihat daftar karyawan |
| GET | `/direksi/teachers` | Melihat daftar pengajar |

## Karyawan

Semua endpoint memerlukan `auth` dan `role:karyawan`.

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/employee/dashboard` | Dashboard karyawan |
| GET/PUT | `/employee/profile`, `/employee/profile/edit` | Melihat dan memperbarui profil |
| GET | `/employee/contracts` | Melihat kontrak sendiri |
| GET/POST | `/employee/documents` | Melihat dan mengunggah dokumen sendiri |
| GET | `/employee/documents/{document}/file` | Mengunduh atau menampilkan dokumen sendiri |

## Pengajar

Semua endpoint memerlukan `auth` dan `role:pengajar`.

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/teacher/dashboard` | Dashboard pengajar |
| GET/PUT | `/teacher/profile`, `/teacher/profile/edit` | Melihat dan memperbarui profil |
| GET/PUT | `/teacher/profile/academic` | Mengelola data akademik |
| GET | `/teacher/contracts` | Melihat kontrak sendiri |
| GET/POST | `/teacher/documents` | Melihat dan mengunggah dokumen sendiri |
| GET | `/teacher/documents/{document}/file` | Mengunduh atau menampilkan dokumen sendiri |
| GET/POST/DELETE | `/teacher/competencies[/{competency}]` | Mengelola kompetensi |
| POST/DELETE | `/teacher/portfolios[/{portfolio}]` | Mengelola portofolio |
| GET | `/teacher/portfolios/{portfolio}/file` | Mengunduh atau menampilkan file portofolio |

## Karyawan dan Pengajar

Semua endpoint memerlukan `auth` dan `role:karyawan_pengajar`.

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/double-role/dashboard` | Dashboard pengguna dengan dua peran |
| GET | `/double-role/profile` | Melihat profil |
| GET/PUT | `/double-role/profile/admin` | Mengelola data administrasi |
| GET/PUT | `/double-role/profile/academic` | Mengelola data akademik |
| GET | `/double-role/contracts` | Melihat kontrak sendiri |
| GET/POST | `/double-role/competencies` | Melihat dan menambah kompetensi |
| POST | `/double-role/portfolios` | Menambah portofolio |

## Endpoint umum

| Method | Endpoint | Akses | Fungsi |
|---|---|---|---|
| GET | `/` | authenticated | Mengarahkan pengguna ke dashboard sesuai role |
| GET | `/calendar` | authenticated | Menampilkan kalender |
| GET | `/storage/{path}` | publik Laravel | Mengakses file storage yang dipublikasikan |
| PUT | `/storage/{path}` | publik Laravel | Upload file storage melalui handler Laravel |

## Catatan integrasi

- Tidak ada endpoint `/api/*` yang terdaftar saat ini.
- Request POST, PUT, PATCH, dan DELETE dari browser harus menyertakan CSRF token.
- Autentikasi API eksternal seperti Bearer token belum dikonfigurasi.
- Mutasi form umumnya mengembalikan redirect dengan flash message, sedangkan tiga endpoint `/admin/contracts/api/*` mengembalikan JSON.
