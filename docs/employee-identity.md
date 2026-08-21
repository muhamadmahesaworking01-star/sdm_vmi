# Struktur Identitas Pegawai

## Identitas utama

- `employees.id` adalah primary key internal pegawai.
- `employees.nip` adalah identifier administratif unik, numerik 15 digit, dan dibuat sistem.
- Format NIP: `KK + YYYY + YYMMDD + NNN`.
- Sequence NIP disimpan di `nip_sequences` dan dikunci saat transaksi untuk mencegah duplikasi.

## Relasi

Relasi menuju pegawai menggunakan `employee_id`:

```text
employees.id
├── users.employee_id
├── tabel_dokumen_karyawan.employee_id
├── tabel_komponen_gaji.employee_id
├── tabel_payroll_bulanan.employee_id
├── tabel_riwayat_kontrak.employee_id
├── tabel_spesialisasi_pengajar.employee_id
└── teacher_portfolios.employee_id
```

NIP tetap digunakan untuk login, pencarian, tampilan, route administratif kontrak, serta export. NIP tidak digunakan sebagai foreign key.
