@extends('layouts.app')

@section('title', 'Profil Akademik - SDM Villa Merah')
@section('page_title', 'Profil Akademik')

@section('content')
@if (! $employee)
    <div class="alert alert-warning border-0 shadow-sm">Akun Anda belum terhubung dengan data pegawai. Hubungi Super Admin agar email, NIP, atau nama akun disamakan.</div>
@else
<form id="profile-form" action="{{ route('double-role.profile.academic.update') }}" method="POST" class="profile-form-card">
    @csrf
    @method('PUT')
    <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3 mb-4">
        <div>
            <p class="text-muted small mb-1">Karyawan & Pengajar</p>
            <h4 class="mb-1">Data Akademik Pengajar</h4>
            <p class="text-muted mb-0 small">Kampus asal dan data sertifikasi akan tampil kembali setelah disimpan.</p>
        </div>
        <span class="edit-indicator d-none" data-edit-indicator><i class="bi bi-pencil-square"></i> Mode edit aktif</span>
    </div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">NIP</label><input class="form-control" value="{{ $employee->nip }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" value="{{ $employee->nama }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="{{ $employee->email }}" readonly></div>
        <div class="col-md-6"><label class="form-label">Telepon / WhatsApp</label><input name="telepon" class="form-control" value="{{ old('telepon', $employee->telepon) }}" required></div>
        <div class="col-md-6"><label class="form-label">NIK / KTP</label><input name="ktp" class="form-control" value="{{ old('ktp', $employee->ktp) }}" inputmode="numeric" maxlength="16"></div>
        <div class="col-md-6"><label class="form-label">Divisi Akademik</label><input name="divisi_akademik" class="form-control" value="{{ old('divisi_akademik', $employee->divisi_akademik) }}" placeholder="Contoh: Seni Rupa"></div>
        <div class="col-md-6"><label class="form-label">Kampus Asal</label><input name="kampus_asal" class="form-control" value="{{ old('kampus_asal', $employee->kampus_asal) }}" placeholder="Nama perguruan tinggi"></div>
        <div class="col-md-6"><label class="form-label">Dokumen Pelatihan</label><input name="dokumen_pelatihan" class="form-control" value="{{ old('dokumen_pelatihan', $employee->dokumen_pelatihan) }}" placeholder="Nama dokumen/sertifikat pelatihan"></div>
        <div class="col-md-6"><label class="form-label">Nomor Sertifikat</label><input name="nomor_sertifikat" class="form-control" value="{{ old('nomor_sertifikat', $employee->nomor_sertifikat) }}"></div>
        <div class="col-12"><div class="academic-form-note"><i class="bi bi-info-circle me-2"></i>Lengkapi data akademik dan sertifikasi agar profil pengajar dapat dipantau secara akurat.</div></div>
    </div>

    <div class="text-end border-top mt-4 pt-4 d-flex justify-content-end gap-2">
        <a href="{{ route('double-role.profile') }}" class="btn btn-light">Kembali</a>
        <button id="edit-profile-button" class="btn btn-outline-primary px-4" type="button"><i class="bi bi-pencil-square me-1"></i> Edit Profil</button>
        <button id="cancel-profile-button" class="btn btn-outline-secondary px-4 d-none" type="button">Batal</button>
        <button id="save-profile-button" class="btn btn-primary px-4 d-none" type="submit">Simpan Data</button>
    </div>
</form>
@endif

@include('shared.profile.academic-competencies', ['competencies' => $competencies, 'portfolios' => $portfolios, 'competencyPrefix' => 'double-role'])
@endsection

@push('styles')
<style>.profile-form-card{background:#fff;border:1px solid #e5e7eb;border-radius:.9rem;box-shadow:0 8px 24px rgba(15,23,42,.06);padding:1.5rem}.profile-form-card .form-label{font-weight:600;color:#34455d;font-size:.88rem}.profile-form-card .form-control{min-height:42px;border-color:#d6dee8}.academic-form-note{padding:.8rem 1rem;border:1px solid #dbeee7;border-radius:.65rem;background:#f2fbf7;color:#41665b;font-size:.82rem}</style>
@endpush
@include('shared.profile.edit-mode')
