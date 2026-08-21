# Tabel Database

## 1. users
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| login_id | string | Login ID unik, opsional |
| name | string | Nama pengguna |
| email | string | Email unik |
| password | string | Password terenkripsi |
| role | string | Role pengguna: super_admin, direksi, karyawan, pengajar, karyawan_pengajar |
| status_akun | string | Status akun, default aktif |
| remember_token | string | Token login |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

## 2. employees
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| nama | string | Nama karyawan |
| nip | string | Nomor induk pegawai unik |
| peran | enum | Nilai: pengajar / karyawan |
| status_aktif | enum | Nilai: aktif / nonaktif |
| jabatan_divisi | string | Jabatan atau divisi |
| id_atasan | string | Identitas atasan langsung |
| divisi_akademik | string | Divisi akademik |
| kampus_asal | string | Kampus asal |
| email | string | Email unik |
| telepon | string | Nomor telepon |
| alamat | text | Alamat |
| ktp | string | Nomor KTP |
| kk | string | Nomor KK |
| tanggal_masuk | date | Tanggal masuk kerja |
| dokumen_pelatihan | string | Nama dokumen pelatihan |
| nomor_sertifikat | string | Nomor sertifikat |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

## 3. specializations
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | string | Nama spesialisasi unik |
| description | text | Deskripsi spesialisasi |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diperbarui |

## 4. password_reset_tokens
| Kolom | Tipe | Keterangan |
|---|---|---|
| email | string | Primary key |
| token | string | Token reset password |
| created_at | timestamp | Waktu dibuat |

## 5. sessions
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | string | Primary key |
| user_id | bigint | Foreign key ke users |
| ip_address | string | Alamat IP |
| user_agent | text | Informasi browser |
| payload | longtext | Data session |
| last_activity | int | Waktu aktivitas terakhir |

## Catatan Implementasi
- Struktur tabel saat ini sudah mencakup autentikasi, data karyawan, dan master data spesialisasi.
- Peran pengguna dikontrol melalui kolom `role` pada tabel `users`.
- Akses dashboard dan menu sidebar dipetakan berdasarkan nilai role tersebut.
