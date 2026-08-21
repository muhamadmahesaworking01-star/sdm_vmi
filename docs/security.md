# Security dan Safeguard

- Role middleware memisahkan `super_admin`, `direksi`, `karyawan`, `pengajar`, dan `karyawan_pengajar`.
- Direksi hanya memiliki route baca; tidak ada route CRUD di grup `role:direksi`.
- Endpoint profil staf tidak menerima ID pegawai dari URL. Pegawai dicari dari email/login ID/nama akun yang sedang login.
- Form mutasi memakai CSRF Laravel dan validasi server-side; impor menggunakan transaksi dan berhenti seluruhnya jika ada baris invalid.
- File backup hanya dibuat di storage private dan dikirim melalui response terautentikasi Super Admin.
- Aktivitas request tercatat untuk audit (user, route, metode, IP, waktu).
