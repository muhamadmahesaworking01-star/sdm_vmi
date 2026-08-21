# API dan Endpoint Internal

Aplikasi saat ini menggunakan endpoint web Laravel dengan middleware session dan CSRF. Endpoint sensitif dibatasi `auth` + `role`.

| Method | Endpoint | Akses | Fungsi |
|---|---|---|---|
| GET | `/admin/backup` | super_admin | Unduh backup ZIP JSON |
| GET | `/admin/activity-logs` | super_admin | Log aktivitas berhalaman |
| GET/POST | `/admin/employees/{template,export,import}` | super_admin | Template, ekspor, dan impor karyawan |
| GET/POST | `/admin/teachers/{template,export,import}` | super_admin | Template, ekspor, dan impor pengajar |
| PUT | `/employee/profile`, `/teacher/profile` | pemilik profil | Self-service biodata |

Semua response mutasi mengembalikan redirect dengan flash message; validasi gagal mengembalikan error ke form.
