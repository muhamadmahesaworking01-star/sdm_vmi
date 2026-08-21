# DFD (Data Flow Diagram)

## DFD Level 0

```mermaid
flowchart LR
    A[Super Admin] --> S[System SDM]
    B[Direksi] --> S
    C[Karyawan] --> S
    D[Pengajar] --> S
    E[Karyawan & Pengajar] --> S

    S --> D1[(Database Users)]
    S --> D2[(Database Employees)]
    S --> D3[(Database Specializations)]
```

## DFD Level 1

```mermaid
flowchart TD
    U[User Login] --> P[Proses Autentikasi]
    P --> A[Dashboard Per Role]
    A --> M1[Manajemen User]
    A --> M2[Manajemen Karyawan]
    A --> M3[Manajemen Pengajar]
    A --> M4[Manajemen Spesialisasi]
    A --> M5[Profil & Tim]

    M1 --> DB1[(users)]
    M2 --> DB2[(employees)]
    M3 --> DB2
    M4 --> DB3[(specializations)]
    M5 --> DB2
```

## Penjelasan Alur
- Pengguna melakukan login terlebih dahulu.
- Sistem memvalidasi role pengguna.
- Setelah login, sistem menampilkan dashboard sesuai role.
- Setiap role memiliki aliran data yang berbeda, seperti manajemen user untuk admin, akses read-only untuk direksi, dan pengeditan profil untuk karyawan atau pengajar.
- Request user terautentikasi mengalir ke proses Audit Aktivitas dan disimpan pada `activity_logs`.
- Super Admin menjalankan proses Backup yang membaca snapshot tabel inti dan menghasilkan arsip ZIP.
