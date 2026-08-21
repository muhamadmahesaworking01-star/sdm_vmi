# Diagram Sistem Informasi SDM

Dokumen ini memuat activity diagram, sequence diagram, dan flowchart utama Sistem Informasi SDM. Diagram menggunakan Mermaid.

## 1. Activity Diagram (Swimlane)

```mermaid
flowchart LR
    subgraph U[Pengguna]
        A((Mulai)) --> B[Buka halaman login]
        B --> C[Masukkan login ID dan password]
        C --> D{Kirim kredensial}
        M[Pilih menu sesuai hak akses]
        N[Isi atau ubah data]
        O[Logout]
        P((Selesai))
    end

    subgraph S[Sistem Informasi SDM]
        E[Validasi format dan kredensial]
        F{Akun valid dan aktif?}
        G[Ambil role pengguna]
        H[Arahkan ke dashboard sesuai role]
        I[Tampilkan dashboard dan menu]
        J[Validasi input dan hak akses]
        K{Data valid?}
        L[Simpan perubahan / proses permintaan]
        Q[Tampilkan pesan berhasil atau gagal]
        R[Hapus sesi login]
    end

    subgraph DBASE[(Database / Audit)]
        T[(Users, Employees, Documents, Payroll, Competencies)]
        V[(Activity Logs)]
    end

    D --> E --> F
    F -- "Tidak" --> Q --> B
    F -- "Ya" --> G --> H --> I --> M
    M --> N --> J --> K
    K -- "Tidak" --> Q --> N
    K -- "Ya" --> L --> T
    L --> V --> Q --> M
    M --> O --> R --> V --> P
```

## 2. Sequence Diagram (Swimlane)

```mermaid
sequenceDiagram
    autonumber
    participant U as Pengguna
    participant UI as Antarmuka SI SDM
    participant Auth as Auth & Role Middleware
    participant C as Controller/Service
    participant DB as Database
    participant Log as Activity Log

    U->>UI: Buka halaman login
    U->>UI: Kirim login ID dan password
    UI->>Auth: POST /login
    Auth->>DB: Cari akun dan verifikasi password
    DB-->>Auth: Data akun/status/role
    alt Akun tidak valid atau ditangguhkan
        Auth-->>UI: Tolak login
        UI-->>U: Tampilkan pesan kesalahan
    else Akun valid dan aktif
        Auth->>Log: Catat login berhasil
        Auth-->>UI: Buat session
        UI->>C: Minta dashboard berdasarkan role
        C->>DB: Ambil data dashboard
        DB-->>C: Data SDM sesuai hak akses
        C-->>UI: Dashboard dan menu role
        UI-->>U: Tampilkan dashboard
        U->>UI: Pilih modul dan kirim aksi
        UI->>Auth: Periksa autentikasi dan role
        Auth->>C: Teruskan permintaan yang diizinkan
        C->>DB: Validasi dan simpan/ambil data
        DB-->>C: Hasil transaksi
        C->>Log: Catat aksi pengguna
        C-->>UI: Respons berhasil/gagal
        UI-->>U: Tampilkan hasil
        U->>UI: Logout
        UI->>Auth: POST /logout
        Auth->>Log: Catat logout
        Auth-->>UI: Hapus session
        UI-->>U: Kembali ke halaman login
    end
```

## 3. Flowchart Sistem Informasi SDM

```mermaid
flowchart TD
    A([Mulai]) --> B[Login]
    B --> C{Kredensial benar?}
    C -- Tidak --> D[Tampilkan pesan login gagal]
    D --> B
    C -- Ya --> E{Role pengguna}

    E -- Super Admin --> F[Kelola user, pegawai, pengajar, spesialisasi, pengumuman]
    F --> G[Import/export data, dokumen, backup, activity log]
    E -- Direksi --> H[Lihat dashboard, organisasi, direktori pegawai/pengajar, laporan]
    E -- Karyawan --> I[Lihat dashboard, ubah profil, dokumen, informasi tim]
    E -- Pengajar --> J[Lihat dashboard, profil akademik, kompetensi, portofolio, dokumen]
    E -- Karyawan & Pengajar --> K[Kelola profil administratif dan akademik, kompetensi, struktur tim]

    G --> L[Validasi hak akses dan input]
    H --> L
    I --> L
    J --> L
    K --> L
    L --> M{Permintaan valid?}
    M -- Tidak --> N[Tampilkan kesalahan]
    N --> O{Coba lagi?}
    O -- Ya --> L
    O -- Tidak --> Q[Logout]
    M -- Ya --> P[(Simpan / baca database SDM)]
    P --> R[Catat aktivitas]
    R --> S[Tampilkan hasil]
    S --> Q
    Q --> T([Selesai])
```

### Keterangan

- Swimlane activity diagram memisahkan tanggung jawab pengguna, aplikasi, dan penyimpanan data/audit.
- Sequence diagram menunjukkan proses autentikasi, pengarahan berdasarkan role, transaksi modul, pencatatan log, dan logout.
- Flowchart menunjukkan percabangan utama berdasarkan role yang tersedia pada aplikasi.
