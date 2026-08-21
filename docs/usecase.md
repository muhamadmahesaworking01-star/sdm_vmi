# Use Case Diagram

## Daftar Use Case Utama

### 1. Super Admin
- Login ke sistem
- Mengelola akun pengguna
- Mengelola data karyawan
- Mengelola data pengajar
- Mengelola data spesialisasi
- Melihat dashboard admin

### 2. Direksi
- Login ke sistem
- Melihat dashboard eksekutif
- Melihat struktur organisasi
- Melihat direktori karyawan
- Melihat direktori pengajar

### 3. Karyawan
- Login ke sistem
- Melihat dashboard karyawan
- Melihat profil kantor sendiri
- Mengedit biodata pribadi
- Melihat informasi atasan dan tim

### 4. Pengajar
- Login ke sistem
- Melihat dashboard pengajar
- Melihat profil akademik sendiri
- Mengedit biodata akademik
- Melihat kompetensi mengajar

### 5. Karyawan & Pengajar
- Login ke sistem
- Melihat dashboard gabungan
- Menjalankan backup data (Super Admin)
- Melihat log aktivitas seluruh user (Super Admin)
- Mengelola profil administratif
- Mengelola profil akademik
- Melihat kompetensi seni dan struktur tim

## Use Case Ringkas

```mermaid
flowchart LR
    A[Super Admin] --> U1[Login]
    A --> U2[Manage Users]
    A --> U3[Manage Employees]
    A --> U4[Manage Teachers]
    A --> U5[Manage Specializations]

    B[Direksi] --> U6[View Dashboard]
    B --> U7[View Organization]
    B --> U8[View Directory]

    C[Karyawan] --> U9[View Employee Dashboard]
    C --> U10[Edit Profile]
    C --> U11[View Team Info]

    D[Pengajar] --> U12[View Teacher Dashboard]
    D --> U13[Edit Academic Profile]
    D --> U14[View Competencies]

    E[Karyawan & Pengajar] --> U15[View Dual Dashboard]
    E --> U16[Manage Dual Profile]
    E --> U17[View Team Structure]
```
