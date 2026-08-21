# ERD Sistem Informasi SDM

Diagram berikut menggambarkan struktur tabel utama Sistem Informasi SDM Villa Merah. Relasi dengan garis putus-putus merupakan relasi logis yang belum menggunakan foreign key langsung.

```mermaid
erDiagram
    USERS ||--o{ SESSIONS : "memiliki"
    USERS ||--o{ ANNOUNCEMENTS : "membuat"
    USERS ||--o{ ACTIVITY_LOGS : "mencatat"
    USERS ||--o{ REPORT_REQUESTS : "meminta"
    USERS ||--o| EMPLOYEES : "terhubung via email/NIP"
    USERS ||--o{ PASSWORD_RESET_TOKENS : "reset via email"

    EMPLOYEES ||--o{ TABEL_DOKUMEN_KARYAWAN : "memiliki"
    EMPLOYEES ||--o{ TABEL_RIWAYAT_KONTRAK : "memiliki"
    EMPLOYEES ||--o| TABEL_KOMPONEN_GAJI : "memiliki"
    EMPLOYEES ||--o{ TABEL_PAYROLL_BULANAN : "menerima"
    EMPLOYEES ||--o{ TABEL_SPESIALISASI_PENGAJAR : "memiliki"
    EMPLOYEES ||--o{ TEACHER_PORTFOLIOS : "memiliki"
    EMPLOYEES }o--o| EMPLOYEES : "atasan langsung"

    USERS {
        bigint id PK
        string login_id UK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role
        string status_akun
        json biodata
        timestamp created_at
        timestamp updated_at
    }

    EMPLOYEES {
        bigint id PK
        string nip UK
        string nama
        string peran
        string status_aktif
        string jabatan_divisi
        string id_atasan
        string divisi_akademik
        string kampus_asal
        string email UK
        string telepon
        text alamat
        string ktp
        string kk
        string npwp
        string tempat_lahir
        date tanggal_lahir
        string agama
        string jenis_kelamin
        smallint berat_badan
        smallint tinggi_badan
        string ukuran_baju
        date tanggal_masuk
        date tanggal_keluar
        timestamp created_at
        timestamp updated_at
    }

    SESSIONS {
        string id PK
        bigint user_id FK
        string ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    PASSWORD_RESET_TOKENS {
        string email PK
        string token
        timestamp created_at
    }

    TABEL_DOKUMEN_KARYAWAN {
        int id_dokumen PK
        string nip_pemilik FK
        string jenis_dokumen
        string nama_file_path
        timestamp tanggal_upload
    }

    TABEL_RIWAYAT_KONTRAK {
        int id_kontrak PK
        string nip_pegawai FK
        string tipe_kontrak
        date tanggal_mulai
        date tanggal_selesai
        text keterangan
    }

    TABEL_KOMPONEN_GAJI {
        int id_komponen PK
        string nip_pegawai FK UK
        decimal gaji_pokok
        decimal total_tunjangan_rutin
        timestamp tanggal_update
    }

    TABEL_PAYROLL_BULANAN {
        int id_payroll PK
        string nip_pegawai FK
        string no_slip UK
        string bulan_tahun
        decimal gaji_pokok_history
        decimal tunjangan_history
        decimal bonus_closing
        decimal thr
        decimal bonus_akhir_tahun
        decimal total_gaji_clean
        datetime tanggal_transfer
        string status_pembayaran
    }

    TABEL_SPESIALISASI_PENGAJAR {
        int id_spesialisasi PK
        string nip_pengajar FK
        string nama_keahlian
    }

    TEACHER_PORTFOLIOS {
        bigint id PK
        string nip_pengajar FK
        string judul
        text deskripsi
        string tautan
        string file_path
        timestamp created_at
        timestamp updated_at
    }

    ANNOUNCEMENTS {
        bigint id PK
        bigint created_by FK
        string title
        text content
        string target_role
        timestamp published_at
        timestamp created_at
        timestamp updated_at
    }

    ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string route
        string method
        string ip_address
        text description
        timestamp created_at
        timestamp updated_at
    }

    REPORT_REQUESTS {
        bigint id PK
        bigint user_id FK
        string report_type
        string filter_divisi
        string filter_kampus
        date filter_date_from
        date filter_date_to
        string format
        string status
        string file_path
        text notes
        datetime generated_at
        timestamp created_at
        timestamp updated_at
    }
```

## Catatan Relasi

- `users` terhubung ke `employees` secara logis melalui email atau NIP; foreign key langsung belum diterapkan.
- `employees` menjadi parent untuk data dokumen, kontrak, gaji, payroll, spesialisasi pengajar, dan portofolio.
- `employees.id_atasan` menyimpan hubungan atasan langsung secara logis menggunakan NIP/ID atasan.
- `users` memiliki foreign key langsung ke `sessions`, `announcements`, `activity_logs`, dan `report_requests`.
- `password_reset_tokens.email` menggunakan email sebagai primary key dan relasinya terhadap `users` bersifat logis.
