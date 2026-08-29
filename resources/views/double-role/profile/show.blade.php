@extends('layouts.app')

@section('title', 'Profil Saya - SDM Villa Merah')
@section('page_title', 'Profil Saya')

@section('content')
<div class="dual-profile">
    @if (! $employee)
        <div class="alert alert-warning border-0 shadow-sm">Akun Anda belum terhubung dengan data pegawai. Hubungi Super Admin agar email, NIP, atau nama akun disamakan.</div>
    @else
        <section class="profile-hero mb-4">
            <div>
                <p class="mb-2">{{ $profileRole }}</p>
                <h2 class="mb-1">Selamat datang, {{ $employee->nama }}.</h2>
                <span>Kelola identitas administrasi dan akademik Anda dari satu profil terpadu.</span>
            </div>
            <div class="completion-box">
                <small>Kelengkapan Profil</small>
                <strong>{{ $profileCompletion }}%</strong>
                <div class="progress mt-2" style="height: 8px;"><div class="progress-bar bg-success" style="width: {{ $profileCompletion }}%"></div></div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="avatar-lg mb-3">{{ strtoupper(substr($employee->nama, 0, 1)) }}</div>
                        <h5 class="mb-1">{{ $employee->nama }}</h5>
                        <p class="text-muted mb-3">{{ $employee->email }}</p>
                        <span class="badge {{ $employee->status_aktif === 'aktif' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($employee->status_aktif ?? 'aktif') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-6"><div class="info-card"><small>NIP</small><strong>{{ $employee->nip }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Email</small><strong>{{ $employee->email ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Jabatan / Divisi</small><strong>{{ $employee->jabatan_divisi ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Atasan</small><strong>{{ $employee->nama_atasan ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Divisi Akademik</small><strong>{{ $employee->divisi_akademik ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Kampus Asal</small><strong>{{ $employee->kampus_asal ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Nomor Sertifikat</small><strong>{{ $employee->nomor_sertifikat ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Dokumen Pelatihan</small><strong>{{ $employee->dokumen_pelatihan ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Telepon / WhatsApp</small><strong>{{ $employee->telepon ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Alamat</small><strong>{{ $employee->alamat ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>NIK / KTP</small><strong>{{ $employee->ktp ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Nomor KK</small><strong>{{ $employee->kk ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Nama Ibu Kandung</small><strong>{{ $employee->nama_gadis_ibu_kandung ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>NPWP</small><strong>{{ $employee->npwp ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Tempat, Tanggal Lahir</small><strong>{{ $employee->tempat_lahir ?? 'Belum diisi' }}{{ $employee->tanggal_lahir ? ', '.$employee->tanggal_lahir->translatedFormat('d F Y') : '' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Agama</small><strong>{{ $employee->agama ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Jenis Kelamin</small><strong>{{ $employee->jenis_kelamin ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Status Pernikahan</small><strong>{{ $employee->status_pernikahan ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Data Fisik</small><strong>{{ $employee->berat_badan ? $employee->berat_badan.' kg' : '-' }} · {{ $employee->tinggi_badan ? $employee->tinggi_badan.' cm' : '-' }} · {{ $employee->gol_darah ?? '-' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Ukuran Baju</small><strong>{{ $employee->ukuran_baju ?? 'Belum diisi' }}</strong></div></div>
                    <div class="col-md-6"><div class="info-card"><small>Alamat Lengkap</small><strong>{{ $employee->alamat ?: 'Belum diisi' }}</strong></div></div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="{{ route($profileEditRoute, ['edit' => 1]) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i> Edit Profil</a>
                    @if ($academicEditRoute)
                        <a href="{{ route($academicEditRoute, ['edit' => 1]) }}" class="btn btn-outline-success"><i class="bi bi-mortarboard me-1"></i> Edit Profil Akademik</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .profile-hero { display: flex; justify-content: space-between; gap: 1rem; align-items: center; padding: 2rem; border-radius: 1rem; color: #fff; background: linear-gradient(120deg, #1d4ed8, #0f766e); box-shadow: 0 10px 25px rgba(15, 23, 42, .12); }
    .profile-hero p, .profile-hero span { color: rgba(255,255,255,.78); }
    .completion-box { min-width: 210px; padding: 1rem; border-radius: .8rem; background: rgba(255,255,255,.14); }
    .completion-box strong { display: block; font-size: 1.8rem; }
    .avatar-lg { width: 72px; height: 72px; border-radius: 50%; display: grid; place-items: center; color: #fff; background: #1d4ed8; font-weight: 800; font-size: 1.7rem; }
    .info-card { height: 100%; padding: 1.1rem; border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; box-shadow: 0 4px 14px rgba(15, 23, 42, .04); }
    .info-card small { display: block; color: #64748b; margin-bottom: .35rem; }
    .info-card strong { color: #172554; }
    @media (max-width: 768px) { .profile-hero { flex-direction: column; align-items: stretch; } }
</style>
@endpush
