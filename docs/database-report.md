# Laporan Database untuk Sistem SI SDM

## Database yang dipakai
- Nama database: `cthdatabase` (ditentukan di file `.env` sebagai `DB_DATABASE`)

## Ringkasan tabel
Terdapat tabel berikut di `cthdatabase`:
- `users`
- `employees`
- `specializations`
- `sessions`
- `password_reset_tokens`
- `migrations`
- `cache`
- `cache_locks`
- `jobs`
- `failed_jobs`
- `job_batches`

> Catatan: Tidak ditemukan constraint foreign-key ter-enforced pada skema saat ini; relasi sebagian bersifat logis (di-handle oleh aplikasi).

---

## Tabel dan fungsinya

- **`users`**
  - Kolom penting: `id`, `login_id`, `name`, `email`, `password`, `role`, `status_akun`, `remember_token`, `created_at`, `updated_at`.
  - Fungsi: Menyimpan akun pengguna dan informasi autentikasi + role (super_admin, direksi, karyawan, pengajar, karyawan_pengajar).
  - Catatan relasi: Secara logis tiap `user` menjadi pemilik sesi (`sessions.user_id`) dan dapat terhubung ke `employees` via `email` atau `login_id`/NIP.

- **`employees`**
  - Kolom penting: `id`, `nama`, `nip`, `peran`, `status_aktif`, `jabatan_divisi`, `id_atasan`, `divisi_akademik`, `kampus_asal`, `email`, `telepon`, `alamat`, `ktp`, `kk`, `tanggal_masuk`, `dokumen_pelatihan`, `nomor_sertifikat`, `created_at`, `updated_at`.
  - Fungsi: Menyimpan profil pegawai/pengajar (data HR dan akademik).
  - Catatan relasi: `id_atasan` menyimpan referensi ke atasan (NIP/ID) — relasi hirarki organisasi; aplikasi menghubungkan `employees.nip` ke `users` bila perlu.

- **`specializations`**
  - Kolom penting: `id`, `name`, `description`, `created_at`, `updated_at`.
  - Fungsi: Master data untuk spesialisasi/kompetensi pengajar.

- **`sessions`**
  - Kolom penting: `id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`.
  - Fungsi: Penyimpanan sesi (Laravel session driver `database`) untuk autentikasi aktif dan manajemen sesi.
  - Catatan relasi: `user_id` menunjuk ke `users.id` secara logis.

- **`password_reset_tokens`**
  - Kolom penting: `email` (pk), `token`, `created_at`.
  - Fungsi: Menyimpan token reset password yang dikirim ke email pengguna.

- **`migrations`**
  - Kolom penting: `id`, `migration`, `batch`.
  - Fungsi: Menyimpan daftar migration yang sudah dijalankan (Laravel migration tracking).

- **`cache`** dan **`cache_locks`**
  - `cache`: menyimpan key/value untuk cache menggunakan driver `database`.
  - `cache_locks`: menyimpan locks untuk mekanisme cache-based locking.
  - Fungsi: Mendukung caching aplikasi (mis. cache konfigurasi, view, query jika dikonfigurasi).

- **`jobs`**, **`failed_jobs`**, **`job_batches`**
  - `jobs`: antrean job (payload) yang akan diproses oleh worker.
  - `failed_jobs`: menyimpan job yang gagal untuk analisis/pengulangan.
  - `job_batches`: metadata untuk batch job (jika fitur batch queue Laravel digunakan).
  - Fungsi: Menunjang processing background/queue (email, import, export, dsb.).

---

## Relasi (logis/hubungan data)
- `users.id` 1 — N `sessions.user_id` (satu user memiliki banyak sesi).
- `users.email` 1 — 1/1..N `employees.email` (secara praktis email menghubungkan akun ke profil pegawai bila diisi).
- `employees.id_atasan` -> `employees.id` (hirarki atasan/bawahan internal pada tabel `employees`).
- `employees.nip` biasanya dipetakan ke `users.login_id` atau ke `tabel_users.nip_pemilik` pada skema lain (kaitan antara credential dan profil HR).
- `specializations` independent; dapat dipetakan ke pengajar melalui tabel pivot jika perlu (saat ini tidak ada tabel pivot otomatis di skema).

> Catatan: Karena tidak ada FK yang didefinisikan di DB, aplikasi (Laravel) bertanggung jawab menjaga konsistensi referensial.

---

## Rekomendasi singkat
- Jika diinginkan hubungan ter-enforce oleh DB, tambahkan foreign keys: `sessions.user_id -> users.id`, `employees.nip -> users.login_id` (atau `users.login_id` disesuaikan), `employees.id_atasan -> employees.id`.
- Pertimbangkan tabel pivot `employee_specialization` jika satu pengajar dapat memiliki banyak spesialisasi.
- Pastikan kolom kunci (NIP, login_id, email) memiliki index/unique constraint sesuai kebutuhan untuk konsistensi.

---

## Catatan terakhir
Laporan ini berdasarkan DB aktif yang disebutkan di `.env` (`cthdatabase`) dan hasil inspeksi schema saat ini. Jika Anda ingin saya tambahkan diagram ERD yang menyertakan relasi yang direkomendasikan (dengan FK), saya bisa membuatkan dan menyimpan ke `docs/`.
