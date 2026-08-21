@extends('layouts.app')

@section('title', 'Profil Administrasi - SDM Villa Merah')
@section('page_title', 'Profil Administrasi')

@section('content')
@if (! $employee)
    <div class="alert alert-warning border-0 shadow-sm">Akun Anda belum terhubung dengan data pegawai. Hubungi Super Admin agar email, NIP, atau nama akun disamakan.</div>
@else
<form id="profile-form" action="{{ route('double-role.profile.admin.update') }}" method="POST" class="profile-form-card">
    @csrf
    @method('PUT')
    <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3 mb-4">
        <div>
            <p class="text-muted small mb-1">Karyawan & Pengajar</p>
            <h4 class="mb-1">Data Diri dan Administrasi</h4>
            <p class="text-muted mb-0 small">Data yang disimpan akan langsung tampil kembali di profil Anda.</p>
        </div>
        <span class="edit-indicator d-none" data-edit-indicator><i class="bi bi-pencil-square"></i> Mode edit aktif</span>
    </div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">NIP</label><input class="form-control" value="{{ $employee->nip }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" value="{{ $employee->nama }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="{{ $employee->email }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Jabatan / Divisi</label><input class="form-control" value="{{ $employee->jabatan_divisi ?? 'Belum diatur' }}" readonly></div>
        <div class="col-md-6"><label class="form-label">ID Atasan</label><input class="form-control" value="{{ $employee->id_atasan ?? 'Belum diatur' }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Telepon / WhatsApp</label><input name="telepon" class="form-control" value="{{ old('telepon', $employee->telepon) }}" required></div>
        <div class="col-md-6"><label class="form-label">NIK / KTP</label><input name="ktp" class="form-control" value="{{ old('ktp', $employee->ktp) }}" inputmode="numeric" maxlength="16"></div>
        <div class="col-md-6"><label class="form-label">Nomor KK</label><input name="kk" class="form-control" value="{{ old('kk', $employee->kk) }}" inputmode="numeric" maxlength="16"></div>
        <div class="col-md-6"><label class="form-label">NPWP</label><input name="npwp" class="form-control" value="{{ old('npwp', $employee->npwp) }}"></div>
        <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}"></div>
        <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control" value="{{ $employee->tanggal_lahir?->format('Y-m-d') }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Agama</label><select name="agama" class="form-select"><option value="">Pilih agama</option>@foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $agama)<option value="{{ $agama }}" @selected(old('agama', $employee->agama) === $agama)>{{ $agama }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select"><option value="">Pilih jenis kelamin</option>@foreach(['Laki-laki','Perempuan'] as $value)<option value="{{ $value }}" @selected(old('jenis_kelamin', $employee->jenis_kelamin) === $value)>{{ $value }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Berat Badan (kg)</label><input type="number" name="berat_badan" class="form-control" value="{{ old('berat_badan', $employee->berat_badan) }}"></div>
        <div class="col-md-4"><label class="form-label">Tinggi Badan (cm)</label><input type="number" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $employee->tinggi_badan) }}"></div>
        <div class="col-md-4"><label class="form-label">Ukuran Baju</label><select name="ukuran_baju" class="form-select"><option value="">Pilih ukuran</option>@foreach(['S','M','L','XL','XXL','XXXL'] as $value)<option value="{{ $value }}" @selected(old('ukuran_baju', $employee->ukuran_baju) === $value)>{{ $value }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Golongan Darah</label><select name="gol_darah" class="form-select"><option value="">Pilih golongan darah</option>@foreach(['A','B','AB','O'] as $value)<option value="{{ $value }}" @selected(old('gol_darah', $employee->gol_darah) === $value)>{{ $value }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">Status Pernikahan</label><select name="status_pernikahan" class="form-select" required>@foreach(['Belum Menikah','Menikah'] as $value)<option value="{{ $value }}" @selected(old('status_pernikahan', $employee->status_pernikahan ?? 'Belum Menikah') === $value)>{{ $value }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Alamat Lengkap</label><textarea name="alamat" rows="6" class="form-control" required placeholder="Masukkan jalan, dusun/kelurahan, kecamatan, kota, provinsi, RT/RW, dan kode pos">{{ old('alamat', $employee->alamat) }}</textarea></div>
    </div>

    <div class="text-end border-top mt-4 pt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('double-role.profile') }}" class="btn btn-light">Kembali</a>
        <button id="edit-profile-button" class="btn btn-outline-primary px-4" type="button"><i class="bi bi-pencil-square me-1"></i> Edit Profil</button>
        <button id="cancel-profile-button" class="btn btn-outline-secondary px-4 d-none" type="button">Batal</button>
        <button id="save-profile-button" class="btn btn-primary px-4 d-none" type="submit">Simpan Data</button>
    </div>
</form>
@endif
@endsection

@push('styles')
<style>.profile-form-card{background:#fff;border:1px solid #e5e7eb;border-radius:.9rem;box-shadow:0 8px 24px rgba(15,23,42,.06);padding:1.5rem}.profile-form-card .form-label{font-weight:600;color:#34455d;font-size:.88rem}.profile-form-card .form-control,.profile-form-card .form-select{min-height:42px;border-color:#d6dee8}</style>
@endpush
@include('shared.profile.edit-mode')
